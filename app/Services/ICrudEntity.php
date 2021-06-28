<?php

interface ICrudEntity
{ 
   	public function ListarUno($request, $response, $args); 
   	public function ListarTodos($request, $response); 
   	public function CargarUno($request, $response);
   	public function BorrarUno($request, $response, $args);
   	public function ModificarUno($request, $response, $args);
}

?>