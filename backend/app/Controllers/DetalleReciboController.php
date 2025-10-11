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

            $deleted = $this->repository->deleteByFactura($numFactura);

            if (!$deleted) {
                Response::error('No se encontró información del detalle para eliminar.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'message' => 'Detalle de factura eliminado correctamente.',
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

            $deleted = $this->repository->deleteById($idDetalle);

            if (!$deleted) {
                Response::error('No se encontró información del detalle.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'message' => 'Detalle eliminado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible eliminar el detalle.', 500, ['error' => $exception->getMessage()]);
        }
    }
}

