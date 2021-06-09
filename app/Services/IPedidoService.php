<?php

interface IPedidoService
{ 
   	public function ListarUnPedido($request, $response, $args); 
   	public function ListarPedidos($request, $response); 
   	public function CargarUnPedido($request, $response);
   	public function BorrarUnPedido($request, $response, $args);
   	public function ModificarUnPedido($request, $response, $args);
}

?>