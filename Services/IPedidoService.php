<?php

interface IPedidoService
{ 
   	public function ListarUnPedido($request, $response); 
   	public function ListarPedidos($request, $response); 
   	public function CargarUnPedido($request, $response);
   	public function BorrarUnPedido($request, $response);
   	public function ModificarUnPedido($request, $response);
}

?>