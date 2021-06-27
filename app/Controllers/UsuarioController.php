<?php
require_once './Services/IUsuarioService.php';
require_once './models/Usuario.php';
require_once './Utils/CSVLoader.php';
use App\Models\Usuario as Usuario;
use Utils\CSV\CSVLoader as CSV;

class UsuarioController implements IUsuarioService {

    public function ListarUnUsuario($request, $response, $args){
        $usuId = intval($args['id']);

        $usuario = Usuario::find($usuId);
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
        $usuario->usuario = $ArrayParam['usuario'];
        $usuario->clave = $ArrayParam['clave'];
        $usuario->nombre = $ArrayParam['nombre'];
        $usuario->apellido = $ArrayParam['apellido'];
        $usuario->estado = $ArrayParam['estado'];
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
        $usrModificado->usuario = $parametros['usuario'];
        $usrModificado->clave = $parametros['clave'];
        $usrModificado->nombre = $parametros['nombre'];
        $usrModificado->apellido = $parametros['apellido'];
        $usrModificado->estado = $parametros['estado'];
        $usrModificado->tipo = $parametros['tipo'];
        $usuarioId = $args['id'];

        // Conseguimos el objeto
        $usr = Usuario::where('id', '=', $usuarioId)->first();

        // Si existe
        if (isset($usr)) {
            // Seteamos un nuevo usuario
            $usr->usuario = $usrModificado->usuario;
            $usr->clave = $usrModificado->clave;
            $usr->nombre = $usrModificado->nombre;
            $usr->apellido = $usrModificado->apellido;
            $usr->estado = $usrModificado->estado;
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

    public function SuspenderUnUsuario($request, $response, $args){
        $idUser = $args['id'];

        // Conseguimos el objeto
        $user = Usuario::find($idUser);

        // Si existe
        if (isset($user)) {
            // Seteamos un nuevo estado
            $user->estado = "Suspendido";
            // Guardamos en base de datos
            $user->save();
            $payload = json_encode(array("mensaje" => "Usuario suspendido con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ReasignarUnUsuario($request, $response, $args){
        $idUser = $args['id'];

        // Conseguimos el objeto
        $user = Usuario::find($idUser);

        // Si existe
        if (isset($user)) {
            // Seteamos un nuevo estado
            $user->estado = "Activo";
            // Guardamos en base de datos
            $user->save();
            $payload = json_encode(array("mensaje" => "Usuario reasignado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarPorCSV($request, $response){
        $pathArchivo = self::ObtenerArchivo("archivoCSV");

        $csv = CSV::ObtenerDatos($pathArchivo);

        foreach($csv as $user){
            $usuario = new Usuario();
            $usuario->usuario = $user['usuario'];
            $usuario->clave = $user['clave'];
            $usuario->tipo = $user['tipo'];
            $usuario->nombre = $user['nombre'];
            $usuario->apellido =$user['apellido'];
            $usuario->estado = $user['estado'];

            $usuario->save();
        }

        $response->getBody()->write(json_encode(array("mensaje" => "Usuarios cargados por CSV")));
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function ObtenerArchivo ( string $nombreFile ) : ?string {
        return (key_exists($nombreFile, $_FILES)) ? $_FILES[$nombreFile]['tmp_name'] : NULL;
    }
}

?>