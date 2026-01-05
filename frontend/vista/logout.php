<?php

require_once __DIR__ . '/../../backend/autoload.php';

use App\Models\LogActividadModel;
use App\Support\Auth;

Auth::start();

$user = Auth::user();
if ($user) {
    $logs = new LogActividadModel();
    $logs->record([
        'fky_usu' => (int) $user['id'],
        'nom_usu' => (string) $user['nombre'],
        'modulo' => 'Sistema',
        'accion' => 'LOGOUT',
        'detalle' => 'Cierre de sesion',
        'entidad_tipo' => 'auth',
        'entidad_id' => (int) $user['id'],
        'metadata' => json_encode([], JSON_UNESCAPED_UNICODE),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
    ]);
}

Auth::logout();

header('Location: login.php');
exit;
