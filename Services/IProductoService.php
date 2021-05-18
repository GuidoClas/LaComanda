<?php

interface IProductoService
{ 
   	public function ListarUnProducto($request, $response); 
   	public function ListarProductos($request, $response); 
   	public function CargarUnProducto($request, $response);
   	public function BorrarUnProducto($request, $response);
   	public function ModificarUnProducto($request, $response);
}

?>