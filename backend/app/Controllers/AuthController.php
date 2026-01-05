<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\LogActividadModel;
use App\Models\UsuarioModel;
use App\Support\Auth;
use InvalidArgumentException;

class AuthController extends Controller
{
    private UsuarioModel $usuarios;
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->usuarios = new UsuarioModel();
        $this->logs = new LogActividadModel();
    }

    public function login(): void
    {
        try {
            $usuario = Validator::requireString($this->input('usuario') ?? $this->input('nombre'), 'usuario');
            $clave = Validator::requireString($this->input('clave') ?? $this->input('password'), 'clave');

            $registro = $this->usuarios->findByNombreWithRol($usuario);
            if (!$registro || strtoupper((string) $registro['est_registro']) !== 'A') {
                $this->error('Usuario o clave incorrecta.', 401);
                return;
            }

            if (!$this->verifyPassword($clave, $registro)) {
                $this->error('Usuario o clave incorrecta.', 401);
                return;
            }

            Auth::login($registro);
            $this->recordLog('LOGIN', 'Inicio de sesion exitoso', $registro);

            $this->json([
                'success' => true,
                'message' => 'Acceso permitido.',
                'data' => Auth::user(),
            ]);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage(), 422);
        }
    }

    public function logout(): void
    {
        $user = Auth::user();

        if ($user) {
            $this->recordLog('LOGOUT', 'Cierre de sesion', [
                'id_usu' => $user['id'],
                'nom_usu' => $user['nombre'],
                'fky_rol' => $user['rol_id'],
                'nom_rol' => $user['rol_nombre'] ?? '',
            ]);
        }

        Auth::logout();

        $this->json([
            'success' => true,
            'message' => 'Sesion finalizada.',
        ]);
    }

    public function me(): void
    {
        $this->json([
            'success' => true,
            'data' => Auth::user(),
        ]);
    }

    private function verifyPassword(string $plain, array $registro): bool
    {
        $hash = (string) ($registro['cla_usu'] ?? '');
        $info = password_get_info($hash);
        $idUsuario = (int) ($registro['id_usu'] ?? 0);

        if ($info['algo'] !== 0) {
            if (!password_verify($plain, $hash)) {
                return false;
            }

            if ($idUsuario > 0 && password_needs_rehash($hash, PASSWORD_BCRYPT)) {
                $this->usuarios->updatePassword($idUsuario, $plain);
            }

            return true;
        }

        if (!hash_equals($hash, $plain)) {
            return false;
        }

        if ($idUsuario > 0) {
            $this->usuarios->updatePassword($idUsuario, $plain);
        }

        return true;
    }

    private function recordLog(string $accion, string $detalle, array $usuario): void
    {
        $idUsuario = (int) ($usuario['id_usu'] ?? $usuario['id'] ?? 0);
        $nombre = (string) ($usuario['nom_usu'] ?? $usuario['nombre'] ?? 'sistema');

        if ($idUsuario <= 0) {
            return;
        }

        $this->logs->record([
            'fky_usu' => $idUsuario,
            'nom_usu' => $nombre,
            'modulo' => 'Sistema',
            'accion' => $accion,
            'detalle' => $detalle,
            'entidad_tipo' => 'auth',
            'entidad_id' => $idUsuario,
            'metadata' => json_encode([], JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
        ]);
    }
}
