<?php

namespace App\Core;

use RuntimeException;

/**
 * Renderiza respuestas HTML/JSON en función del contexto.
 */
class View
{
    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? __DIR__ . '/../Views';
    }

    /**
     * Renderiza una plantilla PHP ubicada en app/Views.
     */
    public function render(string $template, array $data = []): void
    {
        $path = $this->basePath . '/' . ltrim($template, '/');
        if (substr($path, -4) !== '.php') {
            $path .= '.php';
        }

        if (!is_file($path)) {
            throw new RuntimeException("La vista {$template} no existe.");
        }

        extract($data, EXTR_SKIP);
        require $path;
    }

    /**
     * Envía una respuesta JSON.
     */
    public function json(array $payload, int $status = 200): void
    {
        Response::json($payload, $status);
    }

    /**
     * Envía un error con formato consistente.
     */
    public function error(string $message, int $status = 400, array $details = []): void
    {
        Response::error($message, $status, $details);
    }
}
