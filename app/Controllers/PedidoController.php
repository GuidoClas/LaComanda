<?php

require_once './Services/ICrudEntity.php';
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoDelPedido.php';

use App\Models\ProductoDelPedido as ProductoDelPedido;
use App\Models\Producto as Producto;
use App\Models\Pedido as Pedido;

class PedidoController implements ICrudEntity {

    public function ListarUno($request, $response, $args)
    {
        $pedidoId = intval($args['id']);

        $pedido = Pedido::find($pedidoId);
        if(isset($pedido)){
            $productosDelPedido = ProductoDelPedido::where('id_pedido', $pedido->id)->get();
            foreach($productosDelPedido as $p){
                $prod = Producto::find($p->id_prod);
                if(isset($prod->tipo) && isset($prod->precio) && isset($prod->descripcion)){
                    $p->tipo = $prod->tipo;
                    $p->precio = $prod->precio;
                    $p->descripcion = $prod->descripcion;
                }
            }
            $pedido->listaProductos = $productosDelPedido;
        
            $payload = json_encode($pedido);
        }else{
            $payload = json_encode(array("mensaje" => "error"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarUnPedidoPorCodigo($request, $response, $args){
        $pedidoCod = $args['codigo'];
        $pedido = Pedido::where('codigo', $pedidoCod)->first();
       
        if(isset($pedido)){
            $productosDelPedido = ProductoDelPedido::where('id_pedido', $pedido->id)->get();
            foreach($productosDelPedido as $p){
                $prod = Producto::find($p->id_prod);
                if(isset($prod->tipo) && isset($prod->precio) && isset($prod->descripcion)){
                    $p->tipo = $prod->tipo;
                    $p->precio = $prod->precio;
                    $p->descripcion = $prod->descripcion;
                }
            }
            $pedido->listaProductos = $productosDelPedido;
            
            $payload = json_encode($pedido);
        } else{
            $payload = json_encode(array("mensaje" => "error"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarTodos($request, $response){

        $pedidos = Pedido::all();
        
        foreach($pedidos as $pedido){
            //push productos a $pedido y push $pedido a $arrayFinal.
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
        }

        $pedidosJson = json_encode($pedidos);
        $response->getBody()->write($pedidosJson);
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPedidosPendientes($request, $response){

        $pedidos = Pedido::all();
        $arrayFinal = array();

        foreach($pedidos as $pedido){
            if($pedido->estado === 'Pendiente'){
                //push productos a $pedido y push $pedido a $arrayFinal.
                $productosDelPedido = ProductoDelPedido::where('id_pedido', $pedido->id)->where('estado', 'Pendiente')->get();
                foreach($productosDelPedido as $p){
                    $prod = Producto::find($p->id_prod);
                    if(isset($prod->tipo) && isset($prod->precio) && isset($prod->descripcion)){
                        $p->tipo = $prod->tipo;
                        $p->precio = $prod->precio;
                        $p->descripcion = $prod->descripcion;
                    }
                    $pedido->listaProductos = $productosDelPedido;
                    array_push($arrayFinal, $pedido);
                }
            }
        }

        if(empty($arrayFinal) || $arrayFinal === null){
            $payload = json_encode(array("mensaje" => "No hay pedidos pendientes"));
            $response->getBody()->write($payload);
        }else{
            $pedidosJson = json_encode($arrayFinal);
            $response->getBody()->write($pedidosJson);
        }
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response){

        $ArrayParam = $request->getParsedBody();
        $pedido = new Pedido();
        $pedido->id_mesa = $ArrayParam['id_mesa'];
        $pedido->codigo = $ArrayParam['codigo'];
        $pedido->estado = "Pendiente";

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

    public function BorrarUno($request, $response, $args){
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

    public function ModificarUno($request, $response, $args){
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

    public function ListarParaServirPedido($request, $response, $args){
        $codigo = $args['codigo'];

        if($codigo !== null && strlen($codigo) == 5){
            $pedido = Pedido::where('codigo', $codigo)->first();
            // Si existe
            if ($pedido !== null) {
                // Colocamos el estado en listo
                $pedido->estado = "Listo para servir";
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

    public function EntregarPedido($request, $response, $args){
        $codigo = $args['codigo'];

        if($codigo !== null && strlen($codigo) == 5){
            $pedido = Pedido::where('codigo', $codigo)->first();
            // Si existe
            if ($pedido !== null) {
                // Colocamos el estado en entregado
                $pedido->estado = "Entregado";
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

    public function CancelarPedido($request, $response, $args){
        $codigo = $args['codigo'];

        if($codigo !== null && strlen($codigo) == 5){
            $pedido = Pedido::where('codigo', $codigo)->first();
            // Si existe
            if ($pedido !== null) {
                // Colocamos el estado en entregado
                $pedido->estado = "Cancelado";
                // Guardamos en base de datos
                $pedido->save();
                $payload = json_encode(array("mensaje" => "Pedido cancelado con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerPedidos30DiasAtras($request, $response){
        $d1 = date('y-m-d');
        $d2 = date('y-m-d', strtotime('-30 days'));
    
        $pedidos = Pedido::where('estado', 'Entregado')->whereBetween('fechaAlta', [$d2, $d1])->get();
        foreach($pedidos as $pedido){
            //push productos a $pedido y push $pedido a $arrayFinal.
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
        }

        $pedidosJson = json_encode($pedidos);
        $response->getBody()->write($pedidosJson);
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

?>