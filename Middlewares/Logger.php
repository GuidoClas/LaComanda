<?php

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Response;

class Logger{

    private static function log(Request $req, RequestHandler $handler){

        $response = new Response();
        $requestHeaders = $req->getHeaders();

        if ( !array_key_exists( 'Authorization', $requestHeaders ) ) return $response->withStatus( StatusCodeInterface::STATUS_FORBIDDEN );

        $jwt = $requestHeaders['Authorization'][0];
        
        if ( Auth::Verificar($jwt) === false ) return $response->withStatus( StatusCodeInterface::STATUS_FORBIDDEN );
        
        if ( $func(Auth::ObtenerDatos($jwt)) === false ) return $response->withStatus( StatusCodeInterface::STATUS_FORBIDDEN );

        $response = $handler->handle($req);

        return $response;
    }
}


?>