<?php

namespace App\Controllers;

use App\Repositories\DetalleReciboRepository;
use App\Support\Request;
use App\Support\Response;
use App\Support\Validator;
use InvalidArgumentException;
use PDOException;

class DetalleReciboController
{
    private DetalleReciboRepository $repository;

    public function __construct()
    {
        $this->repository = new DetalleReciboRepository();
    }

    public function listByFecha(): void
    {
        try {
            $fecha = Validator::requireDate(Request::input('fecha_det_recibo'), 'fecha_det_recibo');
            $items = $this->repository->listByFecha($fecha);

            Response::json([
                'success' => true,
                'data' => $items,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }

    public function deleteByFactura(): void
    {
        try {
            $numFactura = Validator::requireInt(Request::input('num_factura'), 'num_factura');

            $this->repository->deleteByFactura($numFactura);

            Response::json([
                'success' => true,
                'message' => 'Detalle de factura anulado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible eliminar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function deleteById(): void
    {
        try {
            $idDetalle = Validator::requireInt(Request::input('id_detalle_recibo'), 'id_detalle_recibo');

            $this->repository->deleteById($idDetalle);

            Response::json([
                'success' => true,
                'message' => 'Detalle anulado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible eliminar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }
}
