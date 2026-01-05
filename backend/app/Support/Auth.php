<?php

namespace App\Support;

use App\Core\Response;

class Auth
{
    private const SESSION_KEY = 'user';

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);

        $roleName = (string) ($user['nom_rol'] ?? $user['rol_nombre'] ?? '');

        $_SESSION[self::SESSION_KEY] = [
            'id' => (int) ($user['id_usu'] ?? $user['id_usuario'] ?? 0),
            'nombre' => (string) ($user['nom_usu'] ?? $user['nombre'] ?? ''),
            'rol_id' => (int) ($user['fky_rol'] ?? $user['rol_id'] ?? 0),
            'rol_nombre' => $roleName,
            'is_admin' => self::isAdminRole($roleName),
        ];
    }

    public static function logout(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function check(): bool
    {
        self::start();

        return !empty($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?array
    {
        self::start();

        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user ? (int) $user['id'] : null;
    }

    public static function name(): string
    {
        $user = self::user();

        return $user['nombre'] ?? 'sistema';
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user ? !empty($user['is_admin']) : false;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Response::error('No autorizado.', 401);
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            Response::error('Acceso restringido.', 403);
            exit;
        }
    }

    private static function isAdminRole(string $roleName): bool
    {
        $normalized = strtoupper(trim($roleName));

        return in_array($normalized, ['ADMINISTRADOR', 'ADMIN'], true);
    }
}
