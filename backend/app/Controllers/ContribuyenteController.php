<?php

namespace App\Controllers;

use App\Repositories\ContribuyenteRepository;
use App\Support\Request;
use App\Support\Response;
use App\Support\Validator;
use InvalidArgumentException;
use PDOException;

class ContribuyenteController
{
    private ContribuyenteRepository $repository;

    public function __construct()
    {
        $this->repository = new ContribuyenteRepository();
    }

    public function index(): void
    {
        $term = Request::input('term');
        $items = $this->repository->search($term);

        Response::json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(): void
    {
        try {
            $cedula = Validator::optionalString(Request::input('cedula_rif'));
            $razon = Validator::optionalString(Request::input('razon_social'));
            $estado = Validator::optionalString(Request::input('estado_cont'));

            if (!$cedula || !$razon) {
                throw new InvalidArgumentException('CEDULA/RIF y RAZON SOCIAL son obligatorios.');
            }

            $id = $this->repository->create([
                'cedula_rif' => $cedula,
                'razon_social' => $razon,
                'estado_cont' => $estado,
            ]);

            Response::json([
                'success' => true,
                'message' => 'Contribuyente registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible registrar el contribuyente.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function update(): void
    {
        try {
            $id = Validator::requireInt(Request::input('id_contribuyente'), 'id_contribuyente');
            $cedula = Validator::optionalString(Request::input('cedula_rif'));
            $razon = Validator::optionalString(Request::input('razon_social'));
            $estado = Validator::optionalString(Request::input('estado_cont'));

            if (!$cedula || !$razon) {
                throw new InvalidArgumentException('CEDULA/RIF y RAZON SOCIAL son obligatorios.');
            }

            $updated = $this->repository->update($id, [
                'cedula_rif' => $cedula,
                'razon_social' => $razon,
                'estado_cont' => $estado,
            ]);

            if (!$updated) {
                Response::error('No se pudo actualizar el contribuyente.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'message' => 'Contribuyente actualizado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible actualizar el contribuyente.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function show(): void
    {
        try {
            $id = Validator::requireInt(Request::input('id_contribuyente'), 'id_contribuyente');
            $contribuyente = $this->repository->find($id);

            if ($contribuyente === null) {
                Response::error('Contribuyente no encontrado.', 404);
                return;
            }

            Response::json([
                'success' => true,
                'data' => $contribuyente,
            ]);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        }
    }
}

