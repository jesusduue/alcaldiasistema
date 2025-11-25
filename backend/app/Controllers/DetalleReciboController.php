<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\FacturaDetalleModel;
use App\Models\FacturaModel;
use InvalidArgumentException;
use mysqli_sql_exception;

class DetalleReciboController extends Controller
{
    private FacturaDetalleModel $detalles;
    private FacturaModel $facturas;

    public function __construct()
    {
        parent::__construct();
        $this->detalles = new FacturaDetalleModel();
        $this->facturas = new FacturaModel();
    }

    /** Lista los montos por impuesto en una fecha. */
    public function listByFecha(): void
    {
        try {
            $fecha = Validator::requireDate($this->input('fecha_det_recibo'), 'fecha_det_recibo');
            $items = $this->detalles->listByFecha($fecha);

            $this->json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    /** Elimina lógicamente los detalles de una factura. */
    public function deleteByFactura(): void
    {
        try {
            $facturaId = $this->resolveFacturaId();
            $this->detalles->deleteByFactura($facturaId);

            $this->json([
                'success' => true,
                'message' => 'Detalle de factura anulado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible eliminar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Elimina lógicamente un detalle individual. */
    public function deleteById(): void
    {
        try {
            $detalleId = Validator::requireInt($this->input('id_detalle_recibo'), 'id_detalle_recibo');
            $this->detalles->deleteById($detalleId);

            $this->json([
                'success' => true,
                'message' => 'Detalle anulado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible eliminar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }

    private function resolveFacturaId(): int
    {
        $idFactura = $this->input('id_factura');

        if ($idFactura !== null && $idFactura !== '') {
            return Validator::requireInt($idFactura, 'id_factura');
        }

        $numero = Validator::requireString($this->input('num_factura'), 'num_factura');
        $facturaId = $this->facturas->findIdByNumero($numero, false);

        if ($facturaId === null) {
            throw new InvalidArgumentException('Factura no encontrada.');
        }

        return $facturaId;
    }
}

