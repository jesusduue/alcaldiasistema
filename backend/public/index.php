<?php

require_once __DIR__ . '/../autoload.php';

use App\Controllers\ActividadController;
use App\Controllers\ClasificadorController;
use App\Controllers\ContribuyenteController;
use App\Controllers\DetalleReciboController;
use App\Controllers\FacturaController;
use App\Controllers\RolController;
use App\Controllers\SystemController;
use App\Controllers\UsuarioController;
use App\Core\Response;

$entity = $_GET['entity'] ?? '';
$action = $_GET['action'] ?? '';
$method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');

$routes = [
    'contribuyentes' => [
        'list' => ['controller' => ContribuyenteController::class, 'method' => 'index', 'http' => ['get']],
        'store' => ['controller' => ContribuyenteController::class, 'method' => 'store', 'http' => ['post']],
        'update' => ['controller' => ContribuyenteController::class, 'method' => 'update', 'http' => ['post', 'put']],
        'show' => ['controller' => ContribuyenteController::class, 'method' => 'show', 'http' => ['get']],
    ],
    'system' => [
        'license_status' => ['controller' => SystemController::class, 'method' => 'licenseStatus', 'http' => ['get']],
    ],
    'clasificadores' => [
        'list' => ['controller' => ClasificadorController::class, 'method' => 'index', 'http' => ['get']],
        'store' => ['controller' => ClasificadorController::class, 'method' => 'store', 'http' => ['post']],
    ],
    'facturas' => [
        'list' => ['controller' => FacturaController::class, 'method' => 'index', 'http' => ['get']],
        'by_contribuyente' => ['controller' => FacturaController::class, 'method' => 'listByContribuyente', 'http' => ['get']],
        'by_fecha' => ['controller' => FacturaController::class, 'method' => 'listByFecha', 'http' => ['get']],
        'show' => ['controller' => FacturaController::class, 'method' => 'show', 'http' => ['get']],
        'store' => ['controller' => FacturaController::class, 'method' => 'store', 'http' => ['post']],
        'update' => ['controller' => FacturaController::class, 'method' => 'update', 'http' => ['post', 'put']],
        'update_detalle' => ['controller' => FacturaController::class, 'method' => 'updateDetalle', 'http' => ['post', 'put']],
        'delete' => ['controller' => FacturaController::class, 'method' => 'delete', 'http' => ['post', 'delete']],
        'verify' => ['controller' => FacturaController::class, 'method' => 'verify', 'http' => ['get', 'post']],
        'next_number' => ['controller' => FacturaController::class, 'method' => 'nextNumber', 'http' => ['get']],
    ],
    'detalles' => [
        'by_fecha' => ['controller' => DetalleReciboController::class, 'method' => 'listByFecha', 'http' => ['get']],
        'delete_by_factura' => ['controller' => DetalleReciboController::class, 'method' => 'deleteByFactura', 'http' => ['post', 'delete']],
        'delete' => ['controller' => DetalleReciboController::class, 'method' => 'deleteById', 'http' => ['post', 'delete']],
    ],
    'roles' => [
        'list' => ['controller' => RolController::class, 'method' => 'index', 'http' => ['get']],
        'store' => ['controller' => RolController::class, 'method' => 'store', 'http' => ['post']],
        'update' => ['controller' => RolController::class, 'method' => 'update', 'http' => ['post', 'put']],
    ],
    'usuarios' => [
        'list' => ['controller' => UsuarioController::class, 'method' => 'index', 'http' => ['get']],
        'store' => ['controller' => UsuarioController::class, 'method' => 'store', 'http' => ['post']],
        'update' => ['controller' => UsuarioController::class, 'method' => 'update', 'http' => ['post', 'put']],
    ],
    'actividades' => [
        'list' => ['controller' => ActividadController::class, 'method' => 'index', 'http' => ['get']],
    ],
];

if (!isset($routes[$entity][$action])) {
    Response::error('Ruta no encontrada.', 404);
    exit;
}

$route = $routes[$entity][$action];

if (!in_array($method, $route['http'], true)) {
    Response::error('Método HTTP no permitido.', 405);
    exit;
}

$controllerClass = $route['controller'];

/** @var object $controller */
$controller = new $controllerClass();
$methodName = $route['method'];

if (!method_exists($controller, $methodName)) {
    Response::error('Acción no disponible.', 500);
    exit;
}

$controller->{$methodName}();
