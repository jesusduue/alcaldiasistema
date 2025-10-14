<?php

namespace App\Controllers;

use App\Repositories\DetalleReciboRepository;
use App\Repositories\FacturaRepository;
use App\Support\Request;
use App\Support\Response;
use App\Support\Validator;
use App\Support\LicenseGuard;
use InvalidArgumentException;
use PDOException;
use RuntimeException;

class FacturaController
{
    private FacturaRepository $facturas;
    private DetalleReciboRepository $detalles;
    private LicenseGuard $license;

    public function __construct()
    {
        $this->facturas = new FacturaRepository();
        $this->detalles = new DetalleReciboRepository();
        $this->license = new LicenseGuard();
    }

    public function index(): void
    {
        $term = Request::input('term');
        $items = $this->facturas->listAll($term);

        Response::json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function listByContribuyente(): void
    {
        try {
            $id = Validator::requireInt(Request::input('id_contribuyente'), 'id_contribuyente');
            $items = $this->facturas->listByContribuyente($id);

            Response::json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }

    public function listByFecha(): void
    {
        try {
            $fecha = Validator::requireDate(Request::input('fecha'), 'fecha');
            $items = $this->facturas->listByFecha($fecha);

            Response::json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }

    public function show(): void
    {
        try {
            $num = Validator::requireInt(Request::input('num_factura'), 'num_factura');
            $factura = $this->facturas->findWithDetalle($num);

            if ($factura === null) {
                Response::error('Factura no encontrada.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'data' => $factura,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }

    public function store(): void
    {
        try {
            // Bloquear la creación de facturas si la licencia ha expirado.
            $this->license->ensureActive();

            $payload = $this->payload();

            $fecha = Validator::requireDate($payload['fecha'] ?? null, 'fecha');
            $codContribuyente = Validator::requireInt($payload['cod_contribuyente'] ?? null, 'cod_contribuyente');
            $concepto = Validator::optionalString($payload['concepto'] ?? null) ?? '';

            $detalle = $this->extractDetalle($payload);

            $total = Validator::optionalDecimal($payload['total_factura'] ?? null);
            if ($total === null) {
                $total = $this->sumMontos($detalle);
            }

            $facturaId = $this->facturas->create(
                [
                    'fecha' => $fecha,
                    'id_usuario' => (int) ($payload['id_usuario'] ?? 1),
                    'cod_contribuyente' => $codContribuyente,
                    'concepto' => $concepto,
                    'total_factura' => $total,
                    'estado' => $payload['ESTADO_FACT'] ?? 'activo',
                ],
                [
                    'fecha_det_recibo' => $payload['fecha_det_recibo'] ?? $fecha,
                    'impuesto_A' => $detalle['impuesto_A'],
                    'monto_impuesto_A' => $detalle['monto_impuesto_A'],
                    'impuesto_B' => $detalle['impuesto_B'],
                    'monto_impuesto_B' => $detalle['monto_impuesto_B'],
                    'impuesto_C' => $detalle['impuesto_C'],
                    'monto_impuesto_C' => $detalle['monto_impuesto_C'],
                    'impuesto_D' => $detalle['impuesto_D'],
                    'monto_impuesto_D' => $detalle['monto_impuesto_D'],
                    'impuesto_E' => $detalle['impuesto_E'],
                    'monto_impuesto_E' => $detalle['monto_impuesto_E'],
                    'impuesto_F' => $detalle['impuesto_F'],
                    'monto_impuesto_F' => $detalle['monto_impuesto_F'],
                    'est_registro' => 'activo',
                ]
            );

            Response::json([
                'success' => true,
                'message' => 'Factura creada correctamente.',
                'num_factura' => $facturaId,
            ], 201);
        } catch (RuntimeException $exception) {
            Response::error(
                $exception->getMessage(),
                423,
                [
                    'code' => 'LICENSE_EXPIRED',
                    'expires_at' => $this->license->getExpirationDate(),
                    'support_contact' => $this->license->getSupportContact(),
                ]
            );
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible registrar la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function update(): void
    {
        try {
            $payload = $this->payload();

            $this->license->ensureActive();

            $numFactura = Validator::requireInt($payload['num_factura'] ?? null, 'num_factura');
            $fecha = Validator::requireDate($payload['fecha'] ?? null, 'fecha');
            $codContribuyente = Validator::requireInt($payload['cod_contribuyente'] ?? null, 'cod_contribuyente');
            $concepto = Validator::optionalString($payload['concepto'] ?? null) ?? '';
            $estado = Validator::optionalString($payload['ESTADO_FACT'] ?? null) ?? 'activo';
            $total = Validator::optionalDecimal($payload['total_factura'] ?? null);

            if ($total === null) {
                $factura = $this->facturas->find($numFactura);
                if ($factura === null) {
                    Response::error('Factura no encontrada.', 404);
                    return;
                }
                $total = (float) $factura['total_factura'];
            }

            $updated = $this->facturas->update($numFactura, [
                'fecha' => $fecha,
                'id_usuario' => (int) ($payload['id_usuario'] ?? 1),
                'cod_contribuyente' => $codContribuyente,
                'concepto' => $concepto,
                'total_factura' => $total,
                'estado' => $estado,
            ]);

            if (!$updated) {
                Response::error('No se pudo actualizar la factura.', 404);
                return;
            }

            if ($estado === 'nulo') {
                $this->detalles->markByFactura($numFactura, 'inactivo');
            } elseif ($estado !== 'eliminado') {
                $this->detalles->markByFactura($numFactura, 'activo');
            }

            Response::json([
                'success' => true,
                'message' => 'Factura actualizada correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            Response::error(
                $exception->getMessage(),
                423,
                [
                    'code' => 'LICENSE_EXPIRED',
                    'expires_at' => $this->license->getExpirationDate(),
                    'support_contact' => $this->license->getSupportContact(),
                ]
            );
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible actualizar la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function updateDetalle(): void
    {
        try {
            $payload = $this->payload();

            $this->license->ensureActive();

            $numFactura = Validator::requireInt($payload['num_factura'] ?? null, 'num_factura');
            $detalle = $this->extractDetalle($payload);

            $updated = $this->facturas->updateDetalle($numFactura, [
                'impuesto_A' => $detalle['impuesto_A'],
                'monto_impuesto_A' => $detalle['monto_impuesto_A'],
                'impuesto_B' => $detalle['impuesto_B'],
                'monto_impuesto_B' => $detalle['monto_impuesto_B'],
                'impuesto_C' => $detalle['impuesto_C'],
                'monto_impuesto_C' => $detalle['monto_impuesto_C'],
                'impuesto_D' => $detalle['impuesto_D'],
                'monto_impuesto_D' => $detalle['monto_impuesto_D'],
                'impuesto_E' => $detalle['impuesto_E'],
                'monto_impuesto_E' => $detalle['monto_impuesto_E'],
                'impuesto_F' => $detalle['impuesto_F'],
                'monto_impuesto_F' => $detalle['monto_impuesto_F'],
                'est_registro' => 'activo',
            ]);

            if (!$updated) {
                Response::error('No se pudo actualizar el detalle de la factura.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'message' => 'Detalle actualizado correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            Response::error(
                $exception->getMessage(),
                423,
                [
                    'code' => 'LICENSE_EXPIRED',
                    'expires_at' => $this->license->getExpirationDate(),
                    'support_contact' => $this->license->getSupportContact(),
                ]
            );
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible actualizar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function delete(): void
    {
        try {
            $numFactura = Validator::requireInt(Request::input('num_factura'), 'num_factura');

            $this->license->ensureActive();

            $this->facturas->delete($numFactura);

            Response::json([
                'success' => true,
                'message' => 'Factura eliminada correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            Response::error(
                $exception->getMessage(),
                423,
                [
                    'code' => 'LICENSE_EXPIRED',
                    'expires_at' => $this->license->getExpirationDate(),
                    'support_contact' => $this->license->getSupportContact(),
                ]
            );
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible eliminar la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function verify(): void
    {
        try {
            $numFactura = Validator::requireInt(Request::input('num_factura'), 'num_factura');
            $exists = $this->facturas->exists($numFactura);

            Response::json([
                'success' => true,
                'existe' => $exists,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }

    public function nextNumber(): void
    {
        $next = $this->facturas->nextNumber();

        Response::json([
            'success' => true,
            'data' => [
                'next' => $next,
            ],
        ]);
    }

    private function payload(): array
    {
        $json = Request::json();
        if (!empty($json)) {
            return $json;
        }

        return $_POST;
    }

    private function extractDetalle(array $payload): array
    {
        $detalle = [
            'impuesto_A' => Validator::optionalInt($payload['impuesto_A'] ?? $payload['id_clasificadorA'] ?? null),
            'monto_impuesto_A' => Validator::optionalDecimal($payload['monto_impuesto_A'] ?? null),
            'impuesto_B' => Validator::optionalInt($payload['impuesto_B'] ?? $payload['id_clasificadorB'] ?? null),
            'monto_impuesto_B' => Validator::optionalDecimal($payload['monto_impuesto_B'] ?? null),
            'impuesto_C' => Validator::optionalInt($payload['impuesto_C'] ?? $payload['id_clasificadorC'] ?? null),
            'monto_impuesto_C' => Validator::optionalDecimal($payload['monto_impuesto_C'] ?? null),
            'impuesto_D' => Validator::optionalInt($payload['impuesto_D'] ?? $payload['id_clasificadorD'] ?? null),
            'monto_impuesto_D' => Validator::optionalDecimal($payload['monto_impuesto_D'] ?? null),
            'impuesto_E' => Validator::optionalInt($payload['impuesto_E'] ?? $payload['id_clasificadorE'] ?? null),
            'monto_impuesto_E' => Validator::optionalDecimal($payload['monto_impuesto_E'] ?? null),
            'impuesto_F' => Validator::optionalInt($payload['impuesto_F'] ?? $payload['id_clasificadorF'] ?? null),
            'monto_impuesto_F' => Validator::optionalDecimal($payload['monto_impuesto_F'] ?? null),
        ];

        if ($detalle['monto_impuesto_A'] === null) {
            $detalle['monto_impuesto_A'] = 0.0;
        }

        return $detalle;
    }

    private function sumMontos(array $detalle): float
    {
        $total = 0.0;
        foreach (['monto_impuesto_A', 'monto_impuesto_B', 'monto_impuesto_C', 'monto_impuesto_D', 'monto_impuesto_E', 'monto_impuesto_F'] as $field) {
            if ($detalle[$field] !== null) {
                $total += (float) $detalle[$field];
            }
        }

        return $total;
    }
}
