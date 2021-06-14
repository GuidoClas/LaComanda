<?php

require_once './Services/IPedidoService.php';
require_once './models/Pedido.php';
require_once './models/ProductoDelPedido.php';

use App\Models\ProductoDelPedido as ProductoDelPedido;
use App\Models\Pedido as Pedido;

class PedidoController implements IPedidoService {

    public function ListarUnPedido($request, $response, $args)
    {
        $arrayFinal = array();
        $pedidoId = intval($args['id']);

        $pedido = Pedido::find($pedidoId);

        $pedido->listaProductos = ProductoDelPedido::where('id_pedido', $pedidoId)->get();
        array_push($arrayFinal, $pedido);
        
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarUnPedidoPorCodigo($request, $response, $args)
    {
        $arrayFinal = array();
        $pedidoCod = $args['codigo'];

        $pedido = Pedido::where('codigo', $pedidoCod)->get();

        $pedido->listaProductos = ProductoDelPedido::where('id_pedido', $pedido->id)->get();
        array_push($arrayFinal, $pedido);
        
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPedidos($request, $response){

        $arrayFinal = array();
        $pedidos = Pedido::all();
        
        foreach($pedidos as $pedido){
            //push productos a $pedido y push $pedido a $arrayFinal.
            $pedido->listaProductos = ProductoDelPedido::where('id_pedido',$pedido->id)->get();
            array_push($arrayFinal, $pedido);
        }

        $pedidosJson = json_encode($arrayFinal);
        $response->getBody()->write($pedidosJson);
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnPedido($request, $response){

        $ArrayParam = $request->getParsedBody();
        $pedido = new Pedido();
        $pedido->id_mesa = $ArrayParam['id_mesa'];
        $pedido->codigo = $ArrayParam['codigo'];
        $pedido->estado = $ArrayParam['estado'];

        if($pedido){
            $pedido->save();
            $payload = json_encode(array("mensaje" => "Pedido cargado exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUnPedido($request, $response, $args){
        $pedidoId = $args['id'];
        // Buscamos el pedido
        $pedido = Pedido::find($pedidoId);

        if($pedido !== null){
            // Borramos
            $pedido->delete();
            $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        
        $pedModificado = new Pedido();
        $pedModificado->id_mesa = $parametros['id_mesa'];
        $pedModificado->codigo = $parametros['codigo'];
        $pedModificado->estado = $parametros['estado'];
        
        $pedidoId = $args['id'];

        // Conseguimos el objeto
        $pedido = Pedido::where('id', '=', $pedidoId)->first();

        // Si existe
        if ($pedido !== null) {
            // Seteamos un nuevo pedido
            $pedido->id_mesa = $pedModificado->id_mesa;
            $pedido->codigo = $pedModificado->codigo;
            $pedido->estado = $pedModificado->estado;
            // Guardamos en base de datos
            $pedido->save();
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ActualizarUnPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['id'];
        $nuevoEstado = $parametros['estado'];

        if($nuevoEstado !== null && $idPedido !== null){
            $pedido = Pedido::find($idPedido);
    
            // Si existe
            if ($pedido !== null) {
                // Colocamos el codigo de mesa
                $pedido->estado = $nuevoEstado;
                // Guardamos en base de datos
                $pedido->save();
                $payload = json_encode(array("mensaje" => "Estado del Pedido actualizado con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

?>