<?php
require_once './models/Usuario.php';
require_once './Auth/Authentificator.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario as Usuario;

class AuthController{

    public function IniciarSesion(Request $req, Response $response, $args){

        $arrayParam = $req->getParsedBody();
        $user = $arrayParam['username'];
        $pass = $arrayParam['pass'];

        $user = Usuario::where('usuario', $user)->where('clave', $pass)->get();
        
        if(!isset($user)){
            $payload = json_encode(array("mensaje" => "Usuario no existente"));
            $response->getBody()->write($payload);
            return $response;
        }
        else{
            $datos = json_encode(array("usuario" => $user));
            $token = AuthentificatorJWT::GenerarToken($datos);

            $response->getBody()->write($token);
            return $response;
        }
    }

    public function ProbarDatos(Request $req, Response $response){
        $requestHeader = $req->getHeader('token');
        $elToken = $requestHeader[0];

        $response->getBody()->write(AuthentificatorJWT::ObtenerData($elToken));
            return $response;
    }
}

?>