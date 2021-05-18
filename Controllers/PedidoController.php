<?php

require_once './Services/IPedidoService.php';
require_once './Domain/Pedido.php';
require_once './Domain/Producto.php';

class PedidoController implements IPedidoService {

    public function ListarUnPedido($request, $response)
    {
    }

    public function ListarPedidos($request, $response){

        $arrayFinal = array();
        $pedidos = Pedido::TraerTodosPedidos();
        
        foreach($pedidos as $pedido){
            //push productos a $pedido y push $pedido a $arrayFinal.
            $pedido->listaProductos = Producto::TraerProductoPorId($pedido->id);
            array_push($arrayFinal, $pedido);
        }

        $pedidosJson = json_encode($arrayFinal);
        $response->getBody()->write($pedidosJson);
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnPedido($request, $response){

        $ArrayParam = $request->getParsedBody();
        $pedido = new Pedido();
        $pedido->CrearPedido($ArrayParam['codigo'], $ArrayParam['id_mesa']);

        if($pedido->validarPedido()){
            if($pedido->InsertarPedido() > 0){
                $response->getBody()->write("Pedido cargado");
            }
            else{
                $response->getBody()->write("Error al cargar el pedido");
            }
        }
        return $response;
    }

    public function BorrarUnPedido($request, $response){

    }

    public function ModificarUnPedido($request, $response){

    }
}