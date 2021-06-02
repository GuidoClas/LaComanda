<?php
require_once './Services/IUsuarioService.php';
use \App\Models\Usuario as Usuario;

class UsuarioController implements IUsuarioService {

    public function ListarUnUsuario($request, $response, $args){

    }

    public function ListarUsuarios($request, $response, $args){

        $listaUsuarios = Usuario::TraerTodosUsuarios();

        $usuariosJson = json_encode($listaUsuarios);

        $response->getBody()->write($usuariosJson);
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnUsuario($request, $response){

        $ArrayParam = $request->getParsedBody();
        $usuario = new Usuario();
        $usuario->nombre = $ArrayParam['nombre'];
        $usuario->apellido = $ArrayParam['apellido'];
        $usuario->fechaAlta = $ArrayParam['fechaAlta'];
        $usuario->fechaBaja = $ArrayParam['fechaBaja'];
        $usuario->tipo = $ArrayParam['tipo'];

        
        if($usuario->validarUsuario()){
            $usuario->save();
            $payload = json_encode(array("mensaje" => "Usuario cargado exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUnUsuario($request, $response, $args){

    }

    public function ModificarUnUsuario($request, $response, $args){

    }
}

?>