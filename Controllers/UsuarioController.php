<?php
require_once './Services/IUsuarioService.php';
require_once './Domain/Usuario.php';

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
        $usuario->CrearUsuario($ArrayParam['nombre'], $ArrayParam['apellido'], $ArrayParam['fechaAlta'], $ArrayParam['fechaBaja'], $ArrayParam['tipo']);

        if($usuario->validarUsuario()){
            if($usuario->InsertarUsuario() > 0){
                $response->getBody()->write("Usuario cargado");
            }
            else{
                $response->getBody()->write("Error al cargar");
            }
        }
        return $response;
    }

    public function BorrarUnUsuario($request, $response, $args){

    }

    public function ModificarUnUsuario($request, $response, $args){

    }
}

?>