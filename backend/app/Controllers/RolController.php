<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\RolModel;
use App\Views\RolView;
use InvalidArgumentException;
use mysqli_sql_exception;

class RolController extends Controller
{
    private RolModel $roles;

    public function __construct()
    {
        parent::__construct();
        $this->roles = new RolModel();
    }

    /** Lista todos los roles activos. */
    public function index(): void
    {
        $items = $this->roles->all();

        $this->json([
            'success' => true,
            'data' => RolView::collection($items),
        ]);
    }

    /** Registra un nuevo rol. */
    public function store(): void
    {
        try {
            $nombre = Validator::requireString($this->input('nombre'), 'nombre');
            $id = $this->roles->create($nombre);

            $this->json([
                'success' => true,
                'message' => 'Rol registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible registrar el rol.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Actualiza el nombre o estado del rol. */
    public function update(): void
    {
        try {
            $idRol = Validator::requireInt($this->input('id_rol'), 'id_rol');
            $nombre = Validator::requireString($this->input('nombre'), 'nombre');
            $estado = Validator::requireIn($this->input('estado'), 'estado', ['A', 'I']);

            $updated = $this->roles->update($idRol, $nombre, $estado);

            if (!$updated) {
                $this->error('No se pudo actualizar el rol.', 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible actualizar el rol.', 500, ['error' => $exception->getMessage()]);
        }
    }
}

