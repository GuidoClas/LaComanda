<?php
require_once './Services/IUsuarioService.php';
require_once './models/Usuario.php';
use App\Models\Usuario as Usuario;

class UsuarioController implements IUsuarioService {

    public function ListarUnUsuario($request, $response, $args){
        $usu = intval($args['id']);

        $usuario = Usuario::find($usu);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarUsuarios($request, $response){

        $lista = Usuario::all();
        
        $payload = json_encode(array("listaUsuario" => $lista));
    
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnUsuario($request, $response){

        $ArrayParam = $request->getParsedBody();
        $usuario = new Usuario();
        $usuario->nombre = $ArrayParam['nombre'];
        $usuario->apellido = $ArrayParam['apellido'];
        $usuario->fechaAlta = $ArrayParam['fechaAlta'];
        $usuario->tipo = $ArrayParam['tipo'];

        
        if($usuario){
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
        $usuarioId = $args['id'];
        // Buscamos el usuario
        $usuario = Usuario::find($usuarioId);
        // Borramos
        $usuario->delete();

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnUsuario($request, $response, $args){
        $parametros = $request->getParsedBody();
        
        $usrModificado = new Usuario();
        $usrModificado->nombre = $parametros['nombre'];
        $usrModificado->apellido = $parametros['apellido'];
        $usrModificado->fechaAlta = $parametros['fechaAlta'];
        $usrModificado->tipo = $parametros['tipo'];
        $usuarioId = $args['id'];

        // Conseguimos el objeto
        $usr = Usuario::where('id', '=', $usuarioId)->first();

        // Si existe
        if ($usr !== null) {
            // Seteamos un nuevo usuario
            $usr->nombre = $usrModificado->nombre;
            $usr->apellido = $usrModificado->apellido;
            $usr->fechaAlta = $usrModificado->fechaAlta;
            $usr->tipo = $usrModificado->tipo;
            // Guardamos en base de datos
            $usr->save();
            $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

?>