<?php

interface IMesaService
{ 
   	public function ListarUnaMesa($request, $response, $args); 
   	public function ListarMesas($request, $response); 
   	public function CargarUnaMesa($request, $response);
   	public function BorrarUnaMesa($request, $response, $args);
   	public function ModificarUnaMesa($request, $response, $args);
}

?>