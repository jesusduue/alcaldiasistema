<?php

namespace App\Controllers;

use App\Repositories\ClasificadorRepository;
use App\Support\Request;
use App\Support\Response;
use App\Support\Validator;
use InvalidArgumentException;
use PDOException;

class ClasificadorController
{
    private ClasificadorRepository $repository;

    public function __construct()
    {
        $this->repository = new ClasificadorRepository();
    }

    public function index(): void
    {
        $items = $this->repository->all();

        Response::json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(): void
    {
        try {
            $nombre = Validator::optionalString(Request::input('nombre'));

            if (!$nombre) {
                throw new InvalidArgumentException('El nombre del rubro es obligatorio.');
            }

            $id = $this->repository->create($nombre);

            Response::json([
                'success' => true,
                'message' => 'Clasificador registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            Response::error($exception->getMessage(), 422);
        } catch (PDOException $exception) {
            Response::error('No fue posible registrar el clasificador.', 500, ['error' => $exception->getMessage()]);
        }
    }
}

