<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\LogActividadModel;
use App\Models\RolModel;
use App\Support\Auth;
use App\Views\RolView;
use InvalidArgumentException;
use mysqli_sql_exception;

class RolController extends Controller
{
    private RolModel $roles;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->roles = new RolModel();
        $this->logs = new LogActividadModel();
    }

    /** Lista todos los roles activos. */
    public function index(): void
    {
        $includeInactive = filter_var(
            $this->input('include_inactive') ?? $this->input('include_inactivos'),
            FILTER_VALIDATE_BOOLEAN
        );
        $items = $this->roles->all($includeInactive);

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

            $this->recordLog('CREAR', 'Registro de rol', $id, [
                'nombre' => $nombre,
                'estado' => 'A',
            ]);

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

            $accion = strtoupper($estado) === 'I' ? 'DESHABILITAR' : 'ACTUALIZAR';
            $this->recordLog($accion, 'Actualizacion de rol', $idRol, [
                'nombre' => $nombre,
                'estado' => $estado,
            ]);

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

    private function recordLog(string $accion, string $detalle, int $rolId, array $metadata = []): void
    {
        $current = Auth::user();

        if (!$current) {
            return;
        }

        $this->logs->record([
            'fky_usu' => (int) $current['id'],
            'nom_usu' => (string) $current['nombre'],
            'modulo' => 'Roles',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'rol',
            'entidad_id' => $rolId,
            'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }
}
