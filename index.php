<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Exception\NotFoundException;

require __DIR__ . '/vendor/autoload.php';
require_once './Controllers/UsuarioController.php';
require_once './Controllers/ProductoController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/MesaController.php';

$app = AppFactory::create();
$app->setBasePath('/LaComanda');
$app->addRoutingMiddleware();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello a casa!");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->post('/agregar', \UsuarioController::class . ':CargarUnUsuario');
    $group->get('/listar', \UsuarioController::class . ':ListarUsuarios');
    $group->get('/listar/{id}', \UsuarioController::class . ':ListarUnUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ProductoController::class . ':CargarUnProducto');
    $group->get('/listar', \ProductoController::class . ':ListarProductos');
    $group->get('/listar/{id}', \ProductoController::class . ':ListarUnProducto');
});

$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \PedidoController::class . ':CargarUnPedido');
    $group->get('/listar', \PedidoController::class . ':ListarPedidos');
    $group->get('/listar/{id}', \PedidoController::class . ':ListarUnPedido');
});

$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->post('/agregar', \MesaController::class . ':CargarUnaMesa');
    $group->get('/listar', \MesaController::class . ':ListarMesas');
    $group->get('/listar/{id}', \MesaController::class . ':ListarUnaMesa');
});

$app->run();

?>