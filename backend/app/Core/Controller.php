<?php

namespace App\Core;

/**
 * Punto en común para todos los controladores HTTP.
 */
abstract class Controller
{
    protected View $view;

    public function __construct(?View $view = null)
    {
        $this->view = $view ?? new View();
    }

    /**
     * Atajo para leer parámetros de entrada.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return Request::input($key, $default);
    }

    /**
     * Atajo para responder con JSON.
     */
    protected function json(array $payload, int $status = 200): void
    {
        $this->view->json($payload, $status);
    }

    /**
     * Atajo para responder errores.
     */
    protected function error(string $message, int $status = 400, array $details = []): void
    {
        $this->view->error($message, $status, $details);
    }
}

