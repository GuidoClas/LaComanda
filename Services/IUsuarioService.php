<?php

interface IUsuarioService
{ 
   	public function ListarUnUsuario($request, $response, $args); 
   	public function ListarUsuarios($request, $response, $args); 
   	public function CargarUnUsuario($request, $response);
   	public function BorrarUnUsuario($request, $response, $args);
   	public function ModificarUnUsuario($request, $response, $args);
}

?>