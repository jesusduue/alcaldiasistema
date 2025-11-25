<?php

namespace App\Core;

use mysqli;
use mysqli_sql_exception;
use RuntimeException;

/**
 * Administra la conexión mysqli reutilizable para toda la capa de modelos.
 */
class Database
{
    private static ?mysqli $connection = null;

    /**
     * Retorna una conexión mysqli lista para usarse.
     */
    public static function connection(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $settings = require __DIR__ . '/../Config/settings.php';
        $config = $settings['db'] ?? [];

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $mysqli = new mysqli(
                $config['host'] ?? 'localhost',
                $config['username'] ?? 'root',
                $config['password'] ?? '',
                $config['database'] ?? '',
                (int) ($config['port'] ?? 3306)
            );
            $mysqli->set_charset($config['charset'] ?? 'utf8mb4');
        } catch (mysqli_sql_exception $exception) {
            throw new RuntimeException('No se pudo establecer la conexión con la base de datos.', 0, $exception);
        }

        self::$connection = $mysqli;

        return self::$connection;
    }

    /**
     * Limpia la conexión única (útil en pruebas).
     */
    public static function disconnect(): void
    {
        if (self::$connection instanceof mysqli) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}

