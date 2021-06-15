<?php
require_once __DIR__ . '/../Auth/Authentificator.php';

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Response;

class LoggerMW{

    static function LogSocio(Request $req, RequestHandler $handler) : Response{

        $response = new Response();
        $requestHeader = $req->getHeader('token');
        if(isset($requestHeader)){
            $elToken = $requestHeader[0];
            try{
                AuthentificatorJWT::VerificarToken($elToken);
                $user = AuthentificatorJWT::ObtenerData($elToken);
                $user = json_decode ($user,true);
    
                if($user['usuario'][0]['tipo'] === 'Socio'){
                    $response = $handler->handle($req);
                    return $response;
                }
                else{
                    throw new Exception("Este usuario no tiene permitido este acceso");
                }
            }catch(Exception $ex){
                $response->getBody()->write($ex->getMessage());
                return $response->withStatus(401);
            }
        }
    }

    static function LogMozoYSocio(Request $req, RequestHandler $handler) : Response{

        $response = new Response();
        $requestHeader = $req->getHeader('token');
        $elToken = $requestHeader[0];
        try{
            AuthentificatorJWT::VerificarToken($elToken);
            $user = AuthentificatorJWT::ObtenerData($elToken);
            $user = json_decode ($user,true);

            if($user['usuario'][0]['tipo'] === 'Socio' || $user['usuario'][0]['tipo'] === 'Mozo'){
                $response = $handler->handle($req);
                return $response;
            }
            else{
                throw new Exception("Este usuario no tiene permitido esta acción");
            }
        }catch(Exception $ex){
            $response->getBody()->write($ex->getMessage());
            return $response->withStatus(401);
        }
    }

    static function LogEmpleado(Request $req, RequestHandler $handler) : Response{

        $response = new Response();
        $requestHeader = $req->getHeader('token');
        $elToken = $requestHeader[0];

        try{
            AuthentificatorJWT::VerificarToken($elToken);
            $user = AuthentificatorJWT::ObtenerData($elToken);
            $user = json_decode ($user,true);

            if($user['usuario'][0]['tipo'] === 'Socio' || 
            $user['usuario'][0]['tipo'] === 'Mozo' || 
            $user['usuario'][0]['tipo'] === 'Bartender' || 
            $user['usuario'][0]['tipo'] === 'Cervecero' || 
            $user['usuario'][0]['tipo'] === 'Cocinero'){
                $response = $handler->handle($req);
                return $response;
            }
            else{
                throw new Exception("Este usuario no tiene permitido esta acción");
            }
        }catch(Exception $ex){
            $response->getBody()->write($ex->getMessage());
            return $response->withStatus(401);
        }
    }

    static function LogSectorCorrecto(Request $req, RequestHandler $handler) : Response{

        $response = new Response();
        $requestHeader = $req->getHeader('token');
        $elToken = $requestHeader[0];

        $params =$req->getParsedBody();
        $sector = $params['sector'];

        try{
            AuthentificatorJWT::VerificarToken($elToken);
            $user = AuthentificatorJWT::ObtenerData($elToken);
            $user = json_decode ($user,true);

            if($user['usuario'][0]['tipo'] === $sector){
                $response = $handler->handle($req);
                return $response;
            }
            else{
                throw new Exception("Este usuario no tiene permitido tomar productos de este sector");
            }
        }catch(Exception $ex){
            $response->getBody()->write($ex->getMessage());
            return $response->withStatus(401);
        }
    }
    
}


?>