<?php
require_once './models/Usuario.php';
require_once './models/Operacion.php';
require_once './models/ProductoDelPedido.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/Factura.php';


use App\Models\Usuario as Usuario;
use App\Models\Operacion as Op;
use App\Models\Pedido as Pedido;
use App\Models\Mesa as Mesa;
use App\Models\Factura as Factura;


use App\Models\ProductoDelPedido as ProductoDelPedido;
use Carbon\Factory;

class ConsultasController{

    public function IngresosDeUsuarios($request, $response, $args){
        $f1 = $args['desde'];
        $f2 = $args['hasta'];

        $lista = Usuario::whereBetween('fechaAlta', [$f1, $f2])->get();
        
        $payload = json_encode(array("listaUsuario" => $lista));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CantidadDeOpPorSector($request, $response, $args){
        $sector = $args['sector'];
        $sector = $this->GetSector($sector);

        $cantidad = Op::where('tipo', $sector)->count();
        
        $payload = json_encode(array("Cantidad de operaciones del sector" => $cantidad));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CantidadDeOpPorSectorPorEmpleado($request, $response, $args){
        $sector = $args['sector'];
        $sector = $this->GetSector($sector);

        $collection = Op::groupBy('usuario')
        ->where('tipo', $sector)
        ->selectRaw('count(*) as Operaciones, usuario')
        ->get();

        $payload = json_encode(array("Cantidad de operaciones del sector por empleado" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CantidadDeOpPorPorEmpleado($request, $response, $args){
        $collection = Op::groupBy('usuario')
        ->selectRaw('count(*) as Operaciones, usuario')
        ->get();

        $payload = json_encode(array("Cantidad de operaciones por empleado separados" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ProductoMasVendido($request, $response, $args){

        $collection = ProductoDelPedido::groupBy('id_prod')
        ->selectRaw('count(id_prod) as Cantidad, productos.descripcion')
        ->join('productos', 'productos_del_pedido.id_prod', '=', 'productos.id')
        ->get();

        $payload = json_encode(array("Producto más vendido" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ProductoMenosVendido($request, $response, $args){

        $collection = ProductoDelPedido::groupBy('id_prod')
        ->selectRaw('count(id_prod) as Cantidad, productos.descripcion')
        ->join('productos', 'productos_del_pedido.id_prod', '=', 'productos.id')
        ->orderBy('Cantidad')
        ->get();

        $payload = json_encode(array("Producto menos vendido" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    //SELECT pedidos.codigo as Codigo FROM `productos_del_pedido` INNER JOIN pedidos ON productos_del_pedido.id_pedido = pedidos.id WHERE duracionFinal > duracionEstimada

    public function PedidosDemorados($request, $response, $args){

        $collection = ProductoDelPedido::groupBy('id_prod')
        ->join('pedidos', 'productos_del_pedido.id_pedido', '=', 'pedidos.id')
        ->where('duracionFinal', '>', 'duracionEstimada')
        ->where('pedidos.estado', '=', 'Entregado')
        ->get();

        $payload = json_encode(array("Pedidos Demorados" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function PedidosCancelados($request, $response, $args){

        $collection = Pedido::where('estado', '=', 'Cancelado')
        ->get();

        $payload = json_encode(array("Pedidos Cancelados" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MesaMasUsada($request, $response, $args){

        $collection = Mesa::groupBy('codigo')
        ->selectRaw('count(codigo) as Cantidad, codigo as Codigo')
        ->orderBy('Cantidad', 'DESC')
        ->get();

        $payload = json_encode(array("Mesa mas usada" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MesaMenosUsada($request, $response, $args){

        $collection = Mesa::groupBy('codigo')
        ->selectRaw('count(codigo) as Cantidad, codigo as Codigo')
        ->orderBy('Cantidad', 'ASC')
        ->get();

        $payload = json_encode(array("Mesa menos usada" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function MesaQueMasFacturo($request, $response, $args){

        $collection = Factura::groupBy('id_mesa')
        ->selectRaw('count(id_mesa) as Utilizaciones, sum(total) as Total, mesas.codigo as Codigo')
        ->join('mesas', 'facturas.id_mesa', '=', 'mesas.id')
        ->get();

        $payload = json_encode(array("Mesa que mas facturo" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MesaQueMenosFacturo($request, $response, $args){

        $collection = Factura::groupBy('id_mesa')
        ->selectRaw('count(id_mesa) as Utilizaciones, sum(total) as Total, mesas.codigo as Codigo')
        ->join('mesas', 'facturas.id_mesa', '=', 'mesas.id')
        ->orderBy('Total', 'ASC')
        ->get();

        $payload = json_encode(array("Mesa que menos facturo" => $collection[0]));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MesaConMayorImporte($request, $response, $args){

        $collection = Factura::selectRaw('max(total) as Importe, mesas.codigo as Codigo')
        ->join('mesas', 'facturas.id_mesa', '=', 'mesas.id')
        ->get();

        $payload = json_encode(array("Mesa con mayor importe" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function MesaConMenorImporte($request, $response, $args){

        $collection = Factura::selectRaw('min(total) as Importe, mesas.codigo as Codigo')
        ->join('mesas', 'facturas.id_mesa', '=', 'mesas.id')
        ->get();

        $payload = json_encode(array("Mesa con menor importe" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function FacturacionDeMesaEntreFechas($request, $response, $args){
        $f1 = $args['desde'];
        $f2 = $args['hasta'];

        $collection = Factura::selectRaw('sum(total) as Importe')
        ->whereBetween('fechaAlta', [$f1, $f2])
        ->get();
        
        $payload = json_encode(array("Facturacion de mesas entre fechas" => $collection));
    
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    private function GetSector($tipo){
        $sector = "";
        switch($tipo){
            case "Mozo":
                $sector = "Mozo";
                break;
            case "Barra":
                $sector = "Bartender";
                break;
            case "Cervecero":
                $sector = "Cervecero";
                break;
            case "Cocina":
                $sector = "Cocinero";
                break;
            default:
                $sector = "Socio";
                break;
        }
        return $sector;
    }

    
}

?>