<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\FacturaModel;
use App\Models\LogActividadModel;
use App\Support\LicenseGuard;
use App\Views\FacturaView;
use InvalidArgumentException;
use mysqli_sql_exception;
use RuntimeException;

class FacturaController extends Controller
{
    private const DETALLE_SLOTS = ['A', 'B', 'C', 'D', 'E', 'F'];

    private FacturaModel $facturas;
    private LicenseGuard $license;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->facturas = new FacturaModel();
        $this->license = new LicenseGuard();
        $this->logs = new LogActividadModel();
    }

    /** Lista todas las facturas o filtra por término. */
    public function index(): void
    {
        $term = Validator::optionalString($this->input('term'));
        $items = $this->facturas->listAll($term);

        $this->json([
            'success' => true,
            'data' => FacturaView::collection($items),
        ]);
    }

    /** Lista facturas de un contribuyente específico. */
    public function listByContribuyente(): void
    {
        try {
            $id = $this->requireNumericId($this->input('id_contribuyente'), 'id_contribuyente');
            $items = $this->facturas->listByContribuyente($id);

            $this->json([
                'success' => true,
                'data' => FacturaView::collection($items),
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    /** Lista facturas por fecha exacta. */
    public function listByFecha(): void
    {
        try {
            $fecha = Validator::requireDate($this->input('fecha'), 'fecha');
            $items = $this->facturas->listByFecha($fecha);

            $this->json([
                'success' => true,
                'data' => FacturaView::collection($items),
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    /** Muestra una factura con su detalle. */
    public function show(): void
    {
        try {
            $idFactura = $this->resolveFacturaIdFromRequest(true);
            $resultado = $this->facturas->findWithDetalle($idFactura);

            if ($resultado === null) {
                $this->error('Factura no encontrada.', 404);
                return;
            }

            $this->json([
                'success' => true,
                'data' => FacturaView::detail($resultado['factura'], $resultado['detalles']),
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    /** Registra una nueva factura junto a su detalle. */
    public function store(): void
    {
        try {
            $this->license->ensureActive();

            $payload = $this->payload();
            $fecha = Validator::requireDate($payload['fecha'] ?? null, 'fecha');
            $codContribuyente = $this->requireNumericId($payload['cod_contribuyente'] ?? null, 'cod_contribuyente');
            $concepto = Validator::optionalString($payload['concepto'] ?? null) ?? '';
            $numeroControl = Validator::optionalString($payload['num_fac'] ?? $payload['num_factura'] ?? null);
            $detalleSlots = $this->extractDetalle($payload, true);
            $detalleItems = $this->buildDetalleItems($detalleSlots);

            $total = Validator::optionalDecimal($payload['total_factura'] ?? null);
            if ($total === null) {
                $total = $this->sumMontos($detalleSlots);
            }

            $facturaId = $this->facturas->create(
                [
                    'num_fac' => $numeroControl,
                    'fec_fac' => $fecha,
                    'fky_usu' => $this->resolveUsuarioId($payload),
                    'fky_con' => $codContribuyente,
                    'des_fac' => $concepto,
                    'tot_fac' => $total,
                    'est_pago' => 'A',
                    'est_registro' => 'A',
                ],
                $detalleItems
            );

            $this->recordLog('CREAR', 'Registro de factura', $facturaId, [
                'numero' => $numeroControl,
                'total' => $total,
            ], $payload);

            $this->json([
                'success' => true,
                'message' => 'Factura creada correctamente.',
                'id_factura' => $facturaId,
            ], 201);
        } catch (RuntimeException $exception) {
            $this->licenseError($exception);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible registrar la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Actualiza los datos generales de la factura. */
    public function update(): void
    {
        try {
            $payload = $this->payload();
            $this->license->ensureActive();

            $idFactura = $this->resolveFacturaIdFromPayload($payload);
            $fecha = Validator::requireDate($payload['fecha'] ?? null, 'fecha');
            $codContribuyente = $this->requireNumericId($payload['cod_contribuyente'] ?? null, 'cod_contribuyente');
            $concepto = Validator::optionalString($payload['concepto'] ?? null) ?? '';
            $estado = strtoupper((string) ($payload['ESTADO_FACT'] ?? 'A'));
            $detalleSlots = $this->extractDetalle($payload, false);
            $total = Validator::optionalDecimal($payload['total_factura'] ?? null) ?? $this->sumMontos($detalleSlots);
            $estadoPago = in_array($estado, ['N', 'NULO', 'ANULADO'], true) ? 'N' : 'A';

            $updated = $this->facturas->update($idFactura, [
                'fec_fac' => $fecha,
                'fky_usu' => $this->resolveUsuarioId($payload),
                'fky_con' => $codContribuyente,
                'des_fac' => $concepto,
                'tot_fac' => $total,
                'est_pago' => $estadoPago,
            ]);

            if (!$updated) {
                $this->error('No se pudo actualizar la factura.', 404);
                return;
            }

            $this->recordLog('ACTUALIZAR', 'Actualización de factura', $idFactura, [
                'estado' => $estadoPago,
                'total' => $total,
            ], $payload);

            $this->json([
                'success' => true,
                'message' => 'Factura actualizada correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            $this->licenseError($exception);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible actualizar la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Actualiza únicamente el detalle. */
    public function updateDetalle(): void
    {
        try {
            $payload = $this->payload();
            $this->license->ensureActive();

            $idFactura = $this->resolveFacturaIdFromPayload($payload);
            $detalleItems = $this->buildDetalleItems($this->extractDetalle($payload, true));

            $this->facturas->updateDetalle($idFactura, $detalleItems);

            $this->recordLog('DETALLE', 'Actualización de detalle', $idFactura, [
                'lineas' => count($detalleItems),
            ], $payload);

            $this->json([
                'success' => true,
                'message' => 'Detalle actualizado correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            $this->licenseError($exception);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible actualizar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Anula (borrado lógico) una factura. */
    public function delete(): void
    {
        try {
            $idFactura = $this->resolveFacturaIdFromRequest();
            $this->license->ensureActive();

            $this->facturas->delete($idFactura);

            $this->recordLog('ANULAR', 'Anulación de factura', $idFactura, []);

            $this->json([
                'success' => true,
                'message' => 'Factura anulada correctamente.',
            ]);
        } catch (RuntimeException $exception) {
            $this->licenseError($exception);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible anular la factura.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Verifica si existe una factura por ID o número. */
    public function verify(): void
    {
        try {
            $idFactura = $this->input('id_factura');
            $numeroFactura = $this->input('num_factura');

            if ($idFactura !== null && $idFactura !== '') {
                $exists = $this->facturas->exists($this->requireNumericId($idFactura, 'id_factura'));
            } elseif ($numeroFactura !== null && $numeroFactura !== '') {
                $numero = Validator::requireString($numeroFactura, 'num_factura');
                $exists = $this->facturas->existsByNumero($numero);
            } else {
                throw new InvalidArgumentException('Debe indicar num_factura o id_factura.');
            }

            $this->json([
                'success' => true,
                'existe' => $exists,
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    /** Retorna el siguiente número correlativo sugerido. */
    public function nextNumber(): void
    {
        $next = $this->facturas->nextNumber();

        $this->json([
            'success' => true,
            'data' => ['next' => $next],
        ]);
    }

    private function payload(): array
    {
        $json = Request::json();

        return !empty($json) ? $json : $_POST;
    }

    private function resolveFacturaIdFromRequest(bool $includeInactive = false): int
    {
        return $this->resolveFacturaId(
            $this->input('id_factura'),
            $this->input('num_factura'),
            $includeInactive
        );
    }

    private function resolveFacturaIdFromPayload(array $payload, bool $includeInactive = false): int
    {
        return $this->resolveFacturaId(
            $payload['id_factura'] ?? null,
            $payload['num_factura'] ?? null,
            $includeInactive
        );
    }

    private function resolveFacturaId(mixed $idValue, mixed $numeroValue, bool $includeInactive): int
    {
        if ($idValue !== null && $idValue !== '') {
            return $this->requireNumericId($idValue, 'id_factura');
        }

        if ($numeroValue === null || $numeroValue === '') {
            throw new InvalidArgumentException('Debe indicar el numero de factura.');
        }

        $numero = Validator::requireString($numeroValue, 'num_factura');
        $facturaId = $this->facturas->findIdByNumero($numero, $includeInactive);

        if ($facturaId === null) {
            throw new InvalidArgumentException('Factura no encontrada.');
        }

        return $facturaId;
    }

    private function extractDetalle(array $payload, bool $requireLine = true): array
    {
        $detalle = [];
        $lineasValidas = 0;

        // Soporte para array dinámico 'detalles'
        if (isset($payload['detalles']) && is_array($payload['detalles'])) {
            foreach ($payload['detalles'] as $index => $item) {
                $impuesto = Validator::optionalInt($item['id_clasificador'] ?? null);
                $monto = Validator::optionalDecimal($item['monto_impuesto'] ?? null);

                if ($impuesto !== null && $monto !== null) {
                    // Usamos claves numéricas para el nuevo formato, pero mantenemos compatibilidad interna
                    // si buildDetalleItems espera claves específicas, adaptaremos buildDetalleItems también.
                    // Por ahora, simplemente acumulamos en un array indexado.
                    $detalle[] = [
                        'impuesto' => $impuesto,
                        'monto' => $monto
                    ];
                    $lineasValidas++;
                }
            }
        } else {
            // Soporte Legacy (A-F)
            foreach (self::DETALLE_SLOTS as $slot) {
                $impuestoKey = 'impuesto_' . $slot;
                $montoKey = 'monto_impuesto_' . $slot;
                $fallback = 'id_clasificador' . $slot;

                $impuesto = Validator::optionalInt($payload[$impuestoKey] ?? $payload[$fallback] ?? null);
                $monto = Validator::optionalDecimal($payload[$montoKey] ?? null);

                if ($impuesto !== null && $monto !== null) {
                    $detalle[] = [
                        'impuesto' => $impuesto,
                        'monto' => $monto
                    ];
                    $lineasValidas++;
                }
            }
        }

        if ($lineasValidas === 0 && $requireLine) {
            throw new InvalidArgumentException('Debe registrar al menos un impuesto en la factura.');
        }

        return $detalle;
    }

    private function buildDetalleItems(array $detalle): array
    {
        $items = [];

        foreach ($detalle as $item) {
            $items[] = [
                'fky_tip' => (int) $item['impuesto'],
                'monto' => (float) $item['monto'],
                'estado' => 'A',
            ];
        }

        return $items;
    }

    private function sumMontos(array $detalle): float
    {
        $total = 0.0;

        foreach ($detalle as $item) {
            $total += (float) ($item['monto'] ?? 0);
        }

        return $total;
    }

    private function requireNumericId(mixed $value, string $field): int
    {
        $normalized = null;
        if ($value !== null && $value !== '') {
            $normalized = ltrim((string) $value, '0');
            if ($normalized === '') {
                $normalized = '0';
            }
        }

        return Validator::requireInt($normalized, $field);
    }

    private function resolveUsuarioId(array $payload = []): int
    {
        return (int) ($payload['id_usuario'] ?? $payload['usuario_registro'] ?? $this->input('id_usuario') ?? 1);
    }

    private function resolveUsuarioNombre(array $payload = []): string
    {
        return Validator::optionalString($payload['usuario_nombre'] ?? $this->input('usuario_nombre')) ?? 'sistema';
    }

    private function recordLog(string $accion, string $detalle, int $facturaId, array $metadata = [], array $payload = []): void
    {
        $this->logs->record([
            'fky_usu' => $this->resolveUsuarioId($payload),
            'nom_usu' => $this->resolveUsuarioNombre($payload),
            'modulo' => 'Facturas',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'factura',
            'entidad_id' => $facturaId,
            'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }

    private function licenseError(RuntimeException $exception): void
    {
        $this->error(
            $exception->getMessage(),
            423,
            [
                'code' => 'LICENSE_EXPIRED',
                'expires_at' => $this->license->getExpirationDate(),
                'support_contact' => $this->license->getSupportContact(),
            ]
        );
    }
}

