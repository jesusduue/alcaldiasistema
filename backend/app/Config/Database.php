<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $settings = require __DIR__ . '/settings.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $settings['db']['host'],
                $settings['db']['database'],
                $settings['db']['charset']
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    $settings['db']['username'],
                    $settings['db']['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $exception) {
                throw new PDOException('Database connection failed: ' . $exception->getMessage(), (int) $exception->getCode());
            }
        }

        return self::$connection;
    }
}

