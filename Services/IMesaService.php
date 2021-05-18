<?php

interface IMesaService
{ 
   	public function ListarUnaMesa($request, $response); 
   	public function ListarMesas($request, $response); 
   	public function CargarUnaMesa($request, $response);
   	public function BorrarUnaMesa($request, $response);
   	public function ModificarUnaMesa($request, $response);
}

?>