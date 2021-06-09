<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Exception\NotFoundException;

require __DIR__ . '/../vendor/autoload.php';
require_once './Controllers/UsuarioController.php';
require_once './Controllers/ProductoController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/MesaController.php';

$app = AppFactory::create();
$app->setBasePath('/LaComanda/app');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

//ELOQUENT
// Load ENV

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Bienvenido a la comanda!");
    return $response;
});

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->post('/agregar', \UsuarioController::class . ':CargarUnUsuario');
    $group->get('/listar', \UsuarioController::class . ':ListarUsuarios');
    $group->get('/listarUno/{id}', \UsuarioController::class . ':ListarUnUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUnUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUnUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ProductoController::class . ':CargarUnProducto');
    $group->get('/listar', \ProductoController::class . ':ListarProductos');
    $group->get('/listarUno/{id}', \ProductoController::class . ':ListarUnProducto');
    $group->put('/{id}', \ProductoController::class . ':ModificarUnProducto');
    $group->delete('/{id}', \ProductoController::class . ':BorrarUnProducto');
});

$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \PedidoController::class . ':CargarUnPedido');
    $group->get('/listar', \PedidoController::class . ':ListarPedidos');
    $group->get('/listarUno/{id}', \PedidoController::class . ':ListarUnPedido');
    $group->put('/{id}', \PedidoController::class . ':ModificarUnPedido');
    $group->delete('/{id}', \PedidoController::class . ':BorrarUnPedido');
});

$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->post('/agregar', \MesaController::class . ':CargarUnaMesa');
    $group->get('/listar', \MesaController::class . ':ListarMesas');
    $group->get('/listarUna/{id}', \MesaController::class . ':ListarUnaMesa');
    $group->put('/{id}', \MesaController::class . ':ModificarUnaMesa');
    $group->delete('/{id}', \MesaController::class . ':BorrarUnaMesa');
});

$app->run();

?>