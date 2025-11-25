<?php

namespace App\Core;

/**
 * Normaliza el acceso a los datos recibidos por GET, POST o JSON.
 */
class Request
{
    private static ?array $jsonPayload = null;

    /**
     * Obtiene un parámetro sin importar su origen.
     */
    public static function input(string $key, mixed $default = null): mixed
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        $json = self::json();

        if (array_key_exists($key, $json)) {
            return $json[$key];
        }

        return $default;
    }

    /**
     * Retorna el cuerpo JSON decodificado una única vez.
     */
    public static function json(): array
    {
        if (self::$jsonPayload !== null) {
            return self::$jsonPayload;
        }

        $body = file_get_contents('php://input');
        $decoded = json_decode($body ?: '', true);

        self::$jsonPayload = is_array($decoded) ? $decoded : [];

        return self::$jsonPayload;
    }
}
