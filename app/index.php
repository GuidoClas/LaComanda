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
require_once './Controllers/ProductoController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/MesaController.php';
require_once './Controllers/AuthController.php';
require_once './Controllers/ClienteController.php';
require_once './Controllers/ConsultasController.php';
require_once './Middlewares/LoggerMW.php';

$app = AppFactory::create();
$app->setBasePath('/app');
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
    $group->post('/agregar', \UsuarioController::class . ':CargarUno');
    $group->get('/listar', \UsuarioController::class . ':ListarTodos');
    $group->get('/listarUno/{id}', \UsuarioController::class . ':ListarUno');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
    $group->put('/suspender/{id}',\UsuarioController::class . ':SuspenderUnUsuario');
    $group->put('/reasignar/{id}',\UsuarioController::class . ':ReasignarUnUsuario');

    $group->post('/cargarCSV', \UsuarioController::class . ':CargarPorCSV');
    $group->get('/descargarCSV', \UsuarioController::class . ':DescargarPorCSV');

})->add(\LoggerMW::class . ':LogSocio');

$app->group('/clientes', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ClienteController::class . ':CargarUnCliente');
    $group->get('/listarUno/{id}', \ClienteController::class . ':ListarUnCliente');
    $group->delete('/{id}', \ClienteController::class . ':BorrarUnCliente');
    $group->get('/descargarPDF', \ClienteController::class . ':DescargarPorPDF');
})->add(\LoggerMW::class . ':LogMozoYSocio');

$app->group('/productos', function (RouteCollectorProxy $group){
    $group->post('/cargarCSV', \ProductoController::class . ':CargarPorCSV');
    $group->get('/descargarCSV', \ProductoController::class . ':DescargarPorCSV');
    $group->get('/descargarPDF', \ProductoController::class . ':DescargarPorPDF');
})->add(\LoggerMW::class . ':LogEmpleado');
 
$app->group('/productosDelPedido', function (RouteCollectorProxy $group){
    $group->post('/agregar', \ProductoDelPedidoController::class . ':CargarUno');
    $group->get('/listar', \ProductoDelPedidoController::class . ':ListarTodos');
    $group->get('/listarUno/{id}', \ProductoDelPedidoController::class . ':ListarUno');
    $group->put('/{id}', \ProductoDelPedidoController::class . ':ModificarUno');
    $group->delete('/{id}', \ProductoDelPedidoController::class . ':BorrarUno');
    $group->put('/elaborarProducto/{id}', \ProductoDelPedidoController::class . ':TomarProductoParaElaborar')->add(\LoggerMW::class . ':LogSectorCorrecto');
    $group->put('/entregarProducto/{id}', \ProductoDelPedidoController::class . ':EntregarProducto')->add(\LoggerMW::class . ':LogSectorCorrecto');
})->add(\LoggerMW::class . ':LogEmpleado');

$app->group('/pedidos', function (RouteCollectorProxy $group){
    $group->post('/agregar', \PedidoController::class . ':CargarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listar', \PedidoController::class . ':ListarTodos')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listarUno/{id}', \PedidoController::class . ':ListarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/{id}', \PedidoController::class . ':ModificarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->delete('/{id}', \PedidoController::class . ':BorrarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
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
    $group->post('/agregar', \MesaController::class . ':CargarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listar', \MesaController::class . ':ListarTodos')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->get('/listarUna/{id}', \MesaController::class . ':ListarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->put('/{id}', \MesaController::class . ':ModificarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    $group->delete('/{id}', \MesaController::class . ':BorrarUno')->add(\LoggerMW::class . ':LogMozoYSocio');
    //Solo el socio puede cerrar una mesa
    $group->put('/cerrarMesa/{id}', \MesaController::class . ':CerrarMesa')->add(\LoggerMW::class . ':LogSocio');
    $group->put('/{id}/{estado}', \MesaController::class . ':ModificarEstadoDeMesa')->add(\LoggerMW::class . ':LogMozoYSocio');

    //El cliente puede ver su mesa y pedido sin ninguna validacion
    $group->get('/listarUnaParaCliente/{codigoMesa}/{codigoPedido}', \MesaController::class . ':DevolverDuracion');
});

$app->group('/consultas', function (RouteCollectorProxy $group){
    //formato fechas ej:2021-06-27.
    $group->get('/ingresos/{desde}/{hasta}', \ConsultasController::class . ':IngresosDeUsuarios');
    $group->get('/cantidadOperacionesSector/{sector}', \ConsultasController::class . ':CantidadDeOpPorSector');
    $group->get('/cantidadOperacionesSectorPorEmpleado/{sector}', \ConsultasController::class . ':CantidadDeOpPorSectorPorEmpleado');
    $group->get('/cantidadOperacionesPorEmpleado', \ConsultasController::class . ':CantidadDeOpPorPorEmpleado');
    /////////////////////////////
    $group->get('/productoMasVendidoEnPedidos', \ConsultasController::class . ':ProductoMasVendido');
    $group->get('/productoMenosVendidoEnPedidos', \ConsultasController::class . ':ProductoMenosVendido');
    $group->get('/pedidosDemorados', \ConsultasController::class . ':PedidosDemorados');
    $group->get('/pedidosCancelados', \ConsultasController::class . ':PedidosCancelados');
    ////////////////////////////
    $group->get('/mesaMasUsada', \ConsultasController::class . ':MesaMasUsada');
    $group->get('/mesaMenosUsada', \ConsultasController::class . ':MesaMenosUsada');
    $group->get('/mesaFacturoMas', \ConsultasController::class . ':MesaQueMasFacturo');
    $group->get('/mesaFacturoMenos', \ConsultasController::class . ':MesaQueMenosFacturo');
    $group->get('/mesaMayorImporte', \ConsultasController::class . ':MesaConMayorImporte');
    $group->get('/mesaMenorImporte', \ConsultasController::class . ':MesaConMenorImporte');
    $group->get('/facturacionEntreFechas/{desde}/{hasta}', \ConsultasController::class . ':FacturacionDeMesaEntreFechas');

})->add(\LoggerMW::class . ':LogSocio');

$app->run();

?>