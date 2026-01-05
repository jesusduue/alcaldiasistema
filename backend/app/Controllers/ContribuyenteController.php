<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\ContribuyenteModel;
use App\Models\LogActividadModel;
use App\Support\Auth;
use App\Views\ContribuyenteView;
use InvalidArgumentException;
use mysqli_sql_exception;

class ContribuyenteController extends Controller
{
    private ContribuyenteModel $contribuyentes;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->contribuyentes = new ContribuyenteModel();
        $this->logs = new LogActividadModel();
    }

    /** Lista contribuyentes opcionalmente filtrados. */
    public function index(): void
    {
        $term = Validator::optionalString($this->input('term'));
        $items = $this->contribuyentes->search($term);

        $this->json([
            'success' => true,
            'data' => ContribuyenteView::collection($items),
        ]);
    }

    /** Registra un nuevo contribuyente. */
    public function store(): void
    {
        try {
            $payload = $this->validatePayload();

            if ($this->contribuyentes->findByRif($payload['rif_con'])) {
                throw new InvalidArgumentException('El RIF ya se encuentra registrado.');
            }

            $id = $this->contribuyentes->create($payload);

            $this->recordLog('CREAR', 'Registro de contribuyente', $id, $payload['rif_con']);

            $this->json([
                'success' => true,
                'message' => 'Contribuyente registrado correctamente.',
                'id' => $id,
            ], 201);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible registrar el contribuyente.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Actualiza un contribuyente existente. */
    public function update(): void
    {
        try {
            $id = Validator::requireInt($this->input('id_contribuyente'), 'id_contribuyente');
            $payload = $this->validatePayload();

            if ($this->contribuyentes->findByRif($payload['rif_con'], $id)) {
                throw new InvalidArgumentException('Otro contribuyente utiliza el mismo RIF.');
            }

            $updated = $this->contribuyentes->update($id, $payload);

            if (!$updated) {
                $this->error('No se encontró el contribuyente indicado.', 404);
                return;
            }

            $this->recordLog('ACTUALIZAR', 'Actualización de contribuyente', $id, $payload['rif_con']);

            $this->json([
                'success' => true,
                'message' => 'Contribuyente actualizado correctamente.',
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible actualizar el contribuyente.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Muestra la ficha de un contribuyente. */
    public function show(): void
    {
        try {
            $id = Validator::requireInt($this->input('id_contribuyente'), 'id_contribuyente');
            $contribuyente = $this->contribuyentes->find($id);

            if ($contribuyente === null) {
                $this->error('Contribuyente no encontrado.', 404);
                return;
            }

            $this->json([
                'success' => true,
                'data' => ContribuyenteView::item($contribuyente),
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    private function validatePayload(): array
    {
        $estado = $this->normalizeEstado($this->input('estado_cont'));

        return [
            'nom_con' => Validator::requireString($this->input('razon_social'), 'razon_social'),
            'rif_con' => Validator::requireString($this->input('cedula_rif'), 'cedula_rif'),
            'tel_con' => Validator::requireString($this->input('telefono'), 'telefono'),
            'ema_con' => Validator::requireEmail($this->input('email'), 'email'),
            'dir_con' => Validator::requireString($this->input('direccion'), 'direccion'),
            'fky_usu_registro' => $this->resolveUsuarioId(),
            'est_registro' => $estado,
        ];
    }

    private function normalizeEstado(mixed $estado): string
    {
        $valor = strtoupper(trim((string) ($estado ?? 'A')));

        return $valor === 'I' ? 'I' : 'A';
    }

    private function resolveUsuarioId(): int
    {
        $sessionId = Auth::id();
        if ($sessionId !== null) {
            return $sessionId;
        }

        return (int) ($this->input('usuario_registro') ?? $this->input('id_usuario') ?? 1);
    }

    private function resolveUsuarioNombre(): string
    {
        $sessionName = Auth::name();
        if ($sessionName !== 'sistema') {
            return $sessionName;
        }

        return Validator::optionalString($this->input('usuario_nombre')) ?? 'sistema';
    }

    private function recordLog(string $accion, string $detalle, int $entityId, string $rif): void
    {
        $this->logs->record([
            'fky_usu' => $this->resolveUsuarioId(),
            'nom_usu' => $this->resolveUsuarioNombre(),
            'modulo' => 'Contribuyentes',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'contribuyente',
            'entidad_id' => $entityId,
            'metadata' => json_encode(['rif' => $rif], JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }
}
