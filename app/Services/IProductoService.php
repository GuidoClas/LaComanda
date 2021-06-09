<?php

interface IProductoService
{ 
   	public function ListarUnProducto($request, $response, $args); 
   	public function ListarProductos($request, $response); 
   	public function CargarUnProducto($request, $response);
   	public function BorrarUnProducto($request, $response, $args);
   	public function ModificarUnProducto($request, $response, $args);
}

?>