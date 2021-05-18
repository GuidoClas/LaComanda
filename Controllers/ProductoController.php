<?php

require_once './Services/IProductoService.php';
require_once './Domain/Producto.php';

class ProductoController implements IProductoService {

    public function ListarUnProducto($request, $response)
    {
        
    }

    public function ListarProductos($request, $response){

        $listaProductos = Producto::TraerTodosProductos();

        $productosJson = json_encode($listaProductos);

        $response->getBody()->write($productosJson);
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnProducto($request, $response){

        $ArrayParam = $request->getParsedBody();
        $producto = new Producto();
        $producto->CrearProducto($ArrayParam['id_pedido'], $ArrayParam['tipo'], $ArrayParam['descripcion']);

        if($producto->validarProducto()){
            if($producto->InsertarProducto() > 0){
                $response->getBody()->write("Producto cargado");
            }
            else{
                $response->getBody()->write("Error al cargar producto");
            }
        }
        return $response;
    }

    public function BorrarUnProducto($request, $response){

    }

    public function ModificarUnProducto($request, $response){

    }
}