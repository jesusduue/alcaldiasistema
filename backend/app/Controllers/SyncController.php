<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\LogActividadModel;
use App\Models\SyncModel;
use App\Support\Auth;
use InvalidArgumentException;
use mysqli_sql_exception;

class SyncController extends Controller
{
    private SyncModel $sync;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->sync = new SyncModel();
        $this->logs = new LogActividadModel();
    }

    /** Genera un paquete de sincronizacion. */
    public function export(): void
    {
        try {
            if (!$this->canExport()) {
                $this->error('Acceso restringido.', 403);
                return;
            }
            $includeLogs = $this->resolveIncludeLogs();
            $package = $this->sync->exportPackage($includeLogs);

            $this->recordLog('EXPORTAR', 'Generacion de paquete de sincronizacion', [
                'include_logs' => $includeLogs,
                'total_registros' => $package['metadata']['total_registros'] ?? 0,
                'total_por_tabla' => $package['metadata']['total_por_tabla'] ?? [],
            ]);

            $filename = 'sync_' . date('Ymd_His') . '.json';
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo json_encode($package, JSON_UNESCAPED_UNICODE);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible generar el paquete.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Importa un paquete de sincronizacion. */
    public function import(): void
    {
        try {
            if (!Auth::isAdmin()) {
                $this->error('Acceso restringido.', 403);
                return;
            }
            $replace = $this->resolveReplaceMode();
            $file = $this->resolveUpload();
            $payload = $this->decodePackage($file);

            $validation = $this->sync->validatePackage($payload);
            if (!$validation['valid']) {
                $this->error('Paquete de sincronizacion invalido.', 422, $validation['errors']);
                return;
            }

            $result = $this->sync->importPackage($payload, $replace);

            $this->recordLog('IMPORTAR', 'Importacion de paquete de sincronizacion', [
                'replace' => $replace,
                'total_registros' => $payload['metadata']['total_registros'] ?? 0,
            ]);

            $this->json([
                'success' => true,
                'message' => 'Paquete importado correctamente.',
                'data' => $result,
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        } catch (mysqli_sql_exception $exception) {
            $this->error('No fue posible importar el paquete.', 500, ['error' => $exception->getMessage()]);
        }
    }

    /** Retorna el estado actual de registros y ultima sincronizacion. */
    public function status(): void
    {
        if (!Auth::isAdmin()) {
            $this->error('Acceso restringido.', 403);
            return;
        }
        $status = $this->sync->status();

        $this->json([
            'success' => true,
            'data' => $status,
        ]);
    }

    private function resolveIncludeLogs(): bool
    {
        $raw = Validator::optionalString($this->input('include_logs') ?? $this->input('includeLogs'));
        if ($raw === null) {
            return true;
        }

        $value = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            throw new InvalidArgumentException('El valor de include_logs es invalido.');
        }

        return $value;
    }

    private function resolveReplaceMode(): bool
    {
        $raw = Validator::optionalString($this->input('replace') ?? $this->input('replace_all'));
        if ($raw === null) {
            return false;
        }

        $value = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            throw new InvalidArgumentException('El valor de reemplazo es invalido.');
        }

        return $value;
    }

    private function resolveUpload(): array
    {
        $file = $_FILES['package'] ?? $_FILES['paquete'] ?? null;

        if (!$file) {
            throw new InvalidArgumentException('Debes adjuntar un archivo de sincronizacion.');
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('No fue posible cargar el archivo.');
        }

        if (empty($file['tmp_name'])) {
            throw new InvalidArgumentException('El archivo temporal no esta disponible.');
        }

        return $file;
    }

    private function decodePackage(array $file): array
    {
        $content = file_get_contents($file['tmp_name']);
        if ($content === false || trim($content) === '') {
            throw new InvalidArgumentException('El archivo esta vacio.');
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('El archivo no contiene JSON valido.');
        }

        return $decoded;
    }

    private function recordLog(string $accion, string $detalle, array $metadata = []): void
    {
        $current = Auth::user();

        if (!$current) {
            return;
        }

        $this->logs->record([
            'fky_usu' => (int) $current['id'],
            'nom_usu' => (string) $current['nombre'],
            'modulo' => 'Sincronizacion',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'sincronizacion',
            'entidad_id' => 0,
            'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }

    private function canExport(): bool
    {
        if (Auth::isAdmin()) {
            return true;
        }

        $current = Auth::user();
        if (!$current) {
            return false;
        }

        $role = strtoupper(trim((string) ($current['rol_nombre'] ?? $current['rol'] ?? '')));

        return in_array($role, ['TESORERO', 'TESORERA', 'TESORERIA'], true);
    }
}
