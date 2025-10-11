<?php

namespace App\Support;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    public static function error(string $message, int $status = 400, array $details = []): void
    {
        self::json(
            [
                'success' => false,
                'message' => $message,
                'details' => $details,
            ],
            $status
        );
    }
}

