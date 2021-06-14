<?php
require_once './models/Cliente.php';
use App\Models\Cliente as Cliente;

class ClienteController{

    public function ListarUnCliente($request, $response, $args){
        $clienteId = intval($args['id']);

        $cliente = Cliente::find($clienteId);
        $payload = json_encode($cliente);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnCliente($request, $response){

        $ArrayParam = $request->getParsedBody();
        $cliente = new Cliente();
        $cliente->codigo = $ArrayParam['codigo'];
        $cliente->nombre = $ArrayParam['nombre'];
        $cliente->apellido = $ArrayParam['apellido'];
        
        if(isset($cliente)){
            $cliente->save();
            $payload = json_encode(array("mensaje" => "Cliente cargado exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUnCliente($request, $response, $args){
        $clienteId = $args['id'];
        // Buscamos el cliente
        $cliente = Cliente::find($clienteId);
        // Borramos
        $cliente->delete();

        $payload = json_encode(array("mensaje" => "Cliente borrado con exito"));

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    static function ModificarUnCliente($id, $codigo){
        $cliente = Cliente::find($id);
        
        // Si existe
        if ($cliente !== null) {
            // Colocamos el codigo de mesa
            $cliente->codigo = $codigo;
            // Guardamos en base de datos
            $cliente->save();
            $retorno = true;
        } else {
            $retorno = false;
        }
        return $retorno;
    }

}

?>