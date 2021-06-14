<?php

require_once './Services/IProductoService.php';
require_once './models/Producto.php';

use App\Models\ProductoDelPedido as ProductoDelPedido;

class ProductoController implements IProductoService {

    public function ListarUnProducto($request, $response, $args)
    {
        $prodId = intval($args['id']);

        $producto = ProductoDelPedido::find($prodId);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarProductos($request, $response){

        $lista = ProductoDelPedido::all();
        
        $payload = json_encode(array("listaUsuario" => $lista));
    
        $response->getBody()->write($payload);

        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnProducto($request, $response){

        $ArrayParam = $request->getParsedBody();
        $prod = new ProductoDelPedido();
        $prod->id_pedido = $ArrayParam['id_pedido'];
        $prod->tipo = $ArrayParam['tipo'];
        $prod->precio = $ArrayParam['precio'];
        $prod->descripcion = $ArrayParam['descripcion'];

        if($prod){
            $prod->save();
            $payload = json_encode(array("mensaje" => "Producto cargado exitosamente"));
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
        $prodModificado->tipo = $parametros['tipo'];
        $prodModificado->precio = $parametros['precio'];
        $prodModificado->descripcion = $parametros['descripcion'];
        $productoId = $args['id'];

        // Conseguimos el objeto
        $prod = ProductoDelPedido::where('id', '=', $productoId)->first();

        // Si existe
        if ($prod !== null) {
            // Seteamos un nuevo producto
            $prod->id_pedido = $prodModificado->id_pedido;
            $prod->tipo = $prodModificado->tipo;
            $prod->precio = $prodModificado->precio;
            $prod->descripcion = $prodModificado->descripcion;
            // Guardamos en base de datos
            $prod->save();
            $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Producto no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

?>