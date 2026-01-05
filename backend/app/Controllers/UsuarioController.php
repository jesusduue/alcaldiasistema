<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\LogActividadModel;
use App\Models\UsuarioModel;
use App\Support\Auth;
use App\Views\UsuarioView;
use InvalidArgumentException;
use mysqli_sql_exception;

class UsuarioController extends Controller
{
    private UsuarioModel $usuarios;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new UsuarioModel();
        $this->logs = new LogActividadModel();
    }

    /** Lista los usuarios registrados. */
    public function index(): void
    {
        $includeInactive = filter_var(
            $this->input('include_inactive') ?? $this->input('include_inactivos'),
            FILTER_VALIDATE_BOOLEAN
        );
        $items = $this->usuarios->list($includeInactive);

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

            $this->recordLog('CREAR', 'Registro de usuario', $id, [
                'usuario' => $payload['nom_usu'],
                'rol_id' => $payload['fky_rol'],
                'estado' => $payload['est_registro'],
            ]);

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

            $accion = $payload['est_registro'] === 'I' ? 'DESHABILITAR' : 'ACTUALIZAR';
            $this->recordLog($accion, 'Actualizacion de usuario', $idUsuario, [
                'usuario' => $payload['nom_usu'],
                'rol_id' => $payload['fky_rol'],
                'estado' => $payload['est_registro'],
            ]);

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

    private function recordLog(string $accion, string $detalle, int $usuarioId, array $metadata = []): void
    {
        $current = Auth::user();

        if (!$current) {
            return;
        }

        $this->logs->record([
            'fky_usu' => (int) $current['id'],
            'nom_usu' => (string) $current['nombre'],
            'modulo' => 'Usuarios',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'usuario',
            'entidad_id' => $usuarioId,
            'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }
}
