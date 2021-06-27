<?php

require_once './Services/IProductoService.php';
require_once './models/ProductoDelPedido.php';
require_once './models/Producto.php';
require_once './models/Pedido.php';

use App\Models\ProductoDelPedido as ProductoDelPedido;
use App\Models\Producto as Producto;
use App\Models\Pedido as Pedido;

class ProductoDelPedidoController implements IProductoService {

    public function ListarUnProducto($request, $response, $args)
    {
        $prodId = intval($args['id']);
        
        //consigo el producto del pedido.
        $productoDelPedido = ProductoDelPedido::find($prodId);
        //consigo los datos del producto.
        $prod = Producto::find($productoDelPedido->id_prod);
        //asigno al productoDelPedido
        $productoDelPedido->tipo = $prod->tipo;
        $productoDelPedido->precio = $prod->precio;
        $productoDelPedido->descripcion = $prod->descripcion;

        $payload = json_encode($productoDelPedido);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarProductos($request, $response){

        //$array = array();
        $listaProductos = ProductoDelPedido::all();
        
        foreach($listaProductos as $productoDelPedido){
            $prod = Producto::find($productoDelPedido->id_prod);
            if(isset($prod->tipo) && isset($prod->precio) && isset($prod->descripcion)){
                $productoDelPedido->tipo = $prod->tipo;
                $productoDelPedido->precio = $prod->precio;
                $productoDelPedido->descripcion = $prod->descripcion;
            }
        }

        $payload = json_encode(array("listaProductosDelPedido" => $listaProductos));
    
        $response->getBody()->write($payload);

        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnProducto($request, $response){

        $ArrayParam = $request->getParsedBody();
        $prod = new ProductoDelPedido();
        $prod->id_pedido = $ArrayParam['id_pedido'];
        $prod->id_prod = $ArrayParam['id_prod'];
        $prod->sector = $ArrayParam['sector'];
        $prod->estado = $ArrayParam['estado'];
        $prod->duracionEstimada = $ArrayParam['duracionEstimada'];
       
        if(isset($prod)){
            $prod->save();
            $payload = json_encode(array("mensaje" => "Producto del pedido cargado exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUnProducto($request, $response, $args){
        $productoId = $args['id'];
        // Buscamos el producto
        $producto = ProductoDelPedido::find($productoId);
        // Borramos
        $producto->delete();

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnProducto($request, $response, $args){
        $parametros = $request->getParsedBody();
        
        $prodModificado = new ProductoDelPedido();
        $prodModificado->id_pedido = $parametros['id_pedido'];
        $prodModificado->id_prod = $parametros['id_prod'];
        $prodModificado->sector = $parametros['sector'];
        $prodModificado->estado = $parametros['estado'];
        $productoId = $args['id'];

        // Conseguimos el objeto
        $prod = ProductoDelPedido::where('id', '=', $productoId)->first();

        // Si existe
        if ($prod !== null) {
            // Seteamos un nuevo producto
            $prod->id_pedido = $prodModificado->id_pedido;
            $prod->id_prod = $prodModificado->id_prod;
            $prod->sector = $prodModificado->sector;
            $prod->estado = $prodModificado->estado;
            // Guardamos en base de datos
            $prod->save();
            $payload = json_encode(array("mensaje" => "Producto del pedido modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Producto no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TomarProductoParaElaborar($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idProd = $args['id'];
        $sector = $parametros['sector'];
        $duracionEstimada = $parametros['duracionEstimada'];

        if($sector !== null && $idProd !== null){
            $productoDelPedido = ProductoDelPedido::find($idProd);
    
            // Si existe
            if ($productoDelPedido !== null) {
                // Colocamos el codigo de mesa
                $productoDelPedido->estado = "En Preparacion";
                $productoDelPedido->duracionEstimada = $duracionEstimada;

                $pedidoDeEsteProducto = Pedido::find($productoDelPedido->id_pedido);
                if(isset($pedidoDeEsteProducto) && $pedidoDeEsteProducto->estado === "Pendiente"){
                    $pedidoDeEsteProducto->estado = "En Preparacion";
                    $pedidoDeEsteProducto->save();
                }
                // Guardamos en base de datos
                $productoDelPedido->save();
                $payload = json_encode(array("mensaje" => "Producto tomado con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "Producto no encontrado"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function EntregarProducto($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idProd = $args['id'];
        $sector = $parametros['sector'];
        $duracionFinal = $parametros['duracionFinal'];

        if($sector !== null && $idProd !== null){
            $productoDelPedido = ProductoDelPedido::find($idProd);
    
            // Si existe
            if ($productoDelPedido !== null) {
                // Colocamos el producto como terminado y con duración final
                $productoDelPedido->estado = "Terminado";
                $productoDelPedido->duracionFinal = $duracionFinal;
                // Guardamos en base de datos
                $productoDelPedido->save();
                $payload = json_encode(array("mensaje" => "Producto terminado con exito"));
            } else {
                $payload = json_encode(array("mensaje" => "Producto no encontrado"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
}

?>