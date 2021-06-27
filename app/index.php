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
require_once './Controllers/ProductoDelPedidoController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/MesaController.php';
require_once './Controllers/AuthController.php';
require_once './Controllers/ClienteController.php';
require_once './Middlewares/LoggerMW.php';

$app = AppFactory::create();
$app->setBasePath('/LaComanda/app');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

//ELOQUENT
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

$app->post('/log', \AuthController::class . ':IniciarSesion');

$app->group('/usuarios', function (RouteCollectorProxy $group){
    $group->post('/agregar', \UsuarioController::class . ':CargarUnUsuario');
    $group->get('/listar', \UsuarioController::class . ':ListarUsuarios');
    $group->get('/listarUno/{id}', \UsuarioController::class . ':ListarUnUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUnUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUnUsuario');
    $group->put('/suspender/{id}',\UsuarioController::class . ':SuspenderUnUsuario');
    $group->put('/reasignar/{id}',\UsuarioController::class . ':ReasignarUnUsuario');

    $group->post('/cargarCSV', \UsuarioController::class . ':CargarPorCSV');

})->add(\LoggerMW::class . ':LogSocio');

$app->group('/clientes', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ClienteController::class . ':CargarUnCliente');
    $group->get('/listarUno/{id}', \ClienteController::class . ':ListarUnCliente');
    $group->delete('/{id}', \ClienteController::class . ':BorrarUnCliente');
})->add(\LoggerMW::class . ':LogMozoYSocio');

$app->group('/productosDelPedido', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ProductoDelPedidoController::class . ':CargarUnProducto');
    $group->get('/listar', \ProductoDelPedidoController::class . ':ListarProductos');
    $group->get('/listarUno/{id}', \ProductoDelPedidoController::class . ':ListarUnProducto');
    $group->put('/{id}', \ProductoDelPedidoController::class . ':ModificarUnProducto');
    $group->delete('/{id}', \ProductoDelPedidoController::class . ':BorrarUnProducto');
    $group->put('/elaborarProducto/{id}', \ProductoDelPedidoController::class . ':TomarProductoParaElaborar')->add(\LoggerMW::class . ':LogSectorCorrecto');
    $group->put('/entregarProducto/{id}', \ProductoDelPedidoController::class . ':EntregarProducto')->add(\LoggerMW::class . ':LogSectorCorrecto');
})->add(\LoggerMW::class . ':LogEmpleado');

$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \PedidoController::class . ':CargarUnPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listar', \PedidoController::class . ':ListarPedidos')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listarUno/{id}', \PedidoController::class . ':ListarUnPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/{id}', \PedidoController::class . ':ModificarUnPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->delete('/{id}', \PedidoController::class . ':BorrarUnPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/estado/{id}', \PedidoController::class . ':ActualizarEstadoPedido')->add(\LoggerMW::class . ':LogEmpleado');
    $group->get('/listarPendientes',\PedidoController::class . ':ListarPedidosPendientes')->add(\LoggerMW::class . ':LogEmpleado');
    $group->put('/listarParaServir/{codigo}',\PedidoController::class . ':ListarParaServirPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/entregar/{codigo}',\PedidoController::class . ':EntregarPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/cancelar/{codigo}',\PedidoController::class . ':CancelarPedido')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/estadisticas30Dias',\PedidoController::class . ':ObtenerPedidos30DiasAtras')->add(\LoggerMW::class . ':LogSocio');
    //El cliente puede ver su pedido sin ninguna validacion
    $group->get('/{codigo}', \PedidoController::class . ':ListarUnPedidoPorCodigo');
});

$app->group('/mesas', function (RouteCollectorProxy $group){
    $group->post('/agregar', \MesaController::class . ':CargarUnaMesa')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listar', \MesaController::class . ':ListarMesas')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listarUna/{id}', \MesaController::class . ':ListarUnaMesa')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/{id}', \MesaController::class . ':ModificarUnaMesa')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->delete('/{id}', \MesaController::class . ':BorrarUnaMesa')->add(\LoggerMW::class . ':LogMozoYSocio');
    //Solo el socio puede cerrar una mesa
    $group->put('/cerrarMesa/{id}', \MesaController::class . ':CerrarMesa')->add(\LoggerMW::class . ':LogSocio');

    $group->put('/{id}/{estado}', \MesaController::class . ':ModificarEstadoDeMesa')->add(\LoggerMW::class . ':LogMozoYSocio');

    //El cliente puede ver su mesa y pedido sin ninguna validacion
    $group->get('/listarUnaParaCliente/{codigoMesa}/{codigoPedido}', \MesaController::class . ':DevolverDuracion');
});

$app->run();

?>