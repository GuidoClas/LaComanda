<?php
require_once __DIR__ . '/../Auth/Authentificator.php';

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Response;

class LoggerMW{

    static function Log(Request $req, RequestHandler $handler) : Response{

        $response = new Response();
        $requestHeader = $req->getHeader('token');
        $elToken = $requestHeader[0];

        try{
            AuthentificatorJWT::VerificarToken($elToken);
            $user = AuthentificatorJWT::ObtenerData($elToken);
            //$user['usuario']->tipo === 'socio' verifico que sea socio y si lo es, le doy acceso al endpoint deseado, sino, devuelvo 401.
            $response = $handler->handle($req);
            return $response;
        }catch(Exception $ex){
            $response->getBody->write($ex->getMessage());
            $response->withStatus(401);
            return $response;
        }
    }
}


?>