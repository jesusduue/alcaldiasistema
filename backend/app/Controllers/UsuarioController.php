<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\UsuarioModel;
use App\Views\UsuarioView;
use InvalidArgumentException;
use mysqli_sql_exception;

class UsuarioController extends Controller
{
    private UsuarioModel $usuarios;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new UsuarioModel();
    }

    /** Lista los usuarios registrados. */
    public function index(): void
    {
        $items = $this->usuarios->list();

        $this->json([
            'success' => true,
            'data' => UsuarioView::collection($items),
        ]);
    }

    /** Crea un nuevo usuario del sistema. */
    public function store(): void
    {
        try {
            $payload = $this->validatePayload();

            if ($this->usuarios->findByNombre($payload['nom_usu'])) {
                throw new InvalidArgumentException('El nombre de usuario ya existe.');
            }

            $id = $this->usuarios->create($payload);

            $this->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible registrar el usuario.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Actualiza datos básicos del usuario. */
    public function update(): void
    {
        try {
            $idUsuario = Validator::requireInt($this->input('id_usuario'), 'id_usuario');
            $payload = $this->validatePayload(true);

            if ($this->usuarios->findByNombre($payload['nom_usu'], $idUsuario)) {
                throw new InvalidArgumentException('Otro usuario ya utiliza ese nombre.');
            }

            $updated = $this->usuarios->update($idUsuario, $payload);

            if (!$updated) {
                $this->error('No se pudo actualizar el usuario.', 404);
                return;
            }

            $this->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible actualizar el usuario.', 500, ['error' => $exception->getMessage()]);
        }
    }

    private function validatePayload(bool $isUpdate = false): array
    {
        $nombre = $this->input('nombre') ?? $this->input('nom_usu');
        $rol = $this->input('id_rol') ?? $this->input('fky_rol');
        $estado = $this->input('estado') ?? $this->input('est_registro') ?? 'A';

        $payload = [
            'nom_usu' => Validator::requireString($nombre, 'nombre'),
            'fky_rol' => Validator::requireInt($rol, 'id_rol'),
            'est_registro' => Validator::requireIn($estado, 'estado', ['A', 'I']),
        ];

        $password = $this->input('clave') ?? $this->input('password');
        if (!$isUpdate || Validator::optionalString($password) !== null) {
            $payload['cla_usu'] = Validator::requireString($password, 'clave');
        }

        return $payload;
    }
}
