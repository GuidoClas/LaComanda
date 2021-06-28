<?php // CLASE CLIENTE DEBERIA EXISTIR Y TENER UNA MESA

require_once './Services/ICrudEntity.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/Cliente.php';
require_once './models/ProductoDelPedido.php';
require_once './models/Producto.php';
require_once './Controllers/ClienteController.php';

use App\Models\Mesa as Mesa;
use App\Models\Pedido as Pedido;
use App\Models\Cliente as Cliente;
use App\Models\ProductoDelPedido as ProductoDelPedido;
use App\Models\Producto as Producto;

class MesaController implements ICrudEntity {

    public function ListarUno($request, $response, $args)
    {
        $arrayFinal = array();
        $mesaId = intval($args['id']);

        $mesa = Mesa::find($mesaId);

        $mesa->listaPedidos = Pedido::where('id_mesa', $mesaId)->get();
        array_push($arrayFinal, $mesa);
        
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function DevolverDuracion($request, $response, $args)
    {
        $mesaCod = $args['codigoMesa'];
        $pedidoCod = $args['codigoPedido'];

        $mesa = Mesa::where('codigo', $mesaCod)->first();
        /////
        $pedido = Pedido::where('codigo', $pedidoCod)->first();
        /////
        $minutosRestantes = ProductoDelPedido::where('id_pedido', $pedido->id)->where('estado', 'En Preparacion')->sum('duracionEstimada');
        $horasRestantes = round($minutosRestantes / 60, 1); 

        $payload = json_encode(array("minutos" => $minutosRestantes, "horas" => $horasRestantes));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarTodos($request, $response){

        $arrayPedidos = array();
        $arrayFinal = array();
        $mesas = Mesa::all();
        
        foreach($mesas as $mesa){
            $pedidos = Pedido::where('id_mesa', $mesa->id)->get(); 

            foreach($pedidos as $pedido){
              
                $productoDelPedidos = ProductoDelPedido::where('id_pedido', $pedido->id)->get();
                foreach($productoDelPedidos as $p){
                    $prod = Producto::find($p->id_prod);
        
                    if(isset($prod->tipo) && isset($prod->precio) && isset($prod->descripcion)){
                        $p->tipo = $prod->tipo;
                        $p->precio = $prod->precio;
                        $p->descripcion = $prod->descripcion;
                    }
                }
                $pedido->listaProductos = $productoDelPedidos;
                array_push($arrayPedidos, $pedido);
            }
            
            $mesa->listaPedidos = $arrayPedidos;
            array_push($arrayFinal, $mesa);
        }

        $mesasJson = json_encode($arrayFinal);
        $response->getBody()->write($mesasJson);
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response){

        $ArrayParam = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id_cliente = $ArrayParam['id_cliente'];
        $mesa->codigo = $ArrayParam['codigo'];
        $mesa->estado = $ArrayParam['estado'];

        if($mesa !== null){
            $cliente = Cliente::find($mesa->id_cliente);
            $mesa->save();
            $payload = json_encode(array("mensaje" => "Mesa cargada exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args){
        $mesaId = $args['id'];
        // Buscamos la mesa
        $mesa = Mesa::find($mesaId);

        if($mesa !== null){
            // Borramos
            $mesa->delete();
            $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        
        $mesaModificada = new Mesa();
        $mesaModificada->id_cliente = $parametros['id_cliente'];
        $mesaModificada->codigo = $parametros['codigo'];
        $mesaModificada->estado = $parametros['estado'];
        
        $mesaId = $args['id'];

        // Conseguimos el objeto
        $mesa = Mesa::where('id', '=', $mesaId)->first();

        // Si existe
        if ($mesa !== null) {
            // Seteamos una nueva mesa
            $mesa->id_cliente = $mesaModificada->id_cliente;
            $mesa->codigo = $mesaModificada->codigo;
            $mesa->estado = $mesaModificada->estado;
            // Guardamos en base de datos
            $mesa->save();

            // Le setteamos el codigo de la mesa generada al cliente correspondiente.
            if(ClienteController::ModificarUnCliente($mesa->id_cliente, $mesa->codigo)){
                $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
            } else{
                $payload = json_encode(array("mensaje" => "No se pudo asignar la mesa al cliente"));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstadoDeMesa($request, $response, $args){
        $idMesa = $args['id'];
        $estadoNum = $args['estado'];
        $nuevoEstado = self::AsignarEstadoMesa($estadoNum);

        // Conseguimos el objeto
        $mesa = Mesa::find($idMesa);

        // Si existe
        if ($mesa !== null) {
            // Colocamos el estado de mesa
            $mesa->estado = $nuevoEstado;
            // Guardamos en base de datos
            $mesa->save();
            $payload = json_encode(array("mensaje" => "Estado de mesa actualizado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args){
        $idMesa = $args['id'];
        $nuevoEstado = "Cerrada";

        // Conseguimos el objeto
        $mesa = Mesa::find($idMesa);

        // Si existe
        if (isset($mesa)) {
            // Colocamos el estado de mesa
            $mesa->estado = $nuevoEstado;
            // Guardamos en base de datos
            $mesa->save();
            $payload = json_encode(array("mensaje" => "Estado de mesa actualizado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    private static function AsignarEstadoMesa($estadoNum){
        if($estadoNum > 0 && $estadoNum < 4){
            switch($estadoNum){
                case 1:
                    $nuevoEstado = "Con cliente esperando pedido";
                    break;
                case 2:
                    $nuevoEstado = "Con cliente comiendo";
                    break;
                case 3:
                    $nuevoEstado = "Con cliente pagando";
                    break;
                default:
                    $nuevoEstado = "Abierta";
            }
        }
        return $nuevoEstado;
    }
}

?>