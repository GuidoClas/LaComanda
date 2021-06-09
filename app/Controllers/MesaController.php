<?php // CLASE CLIENTE DEBERIA EXISTIR Y TENER UNA MESA

require_once './Services/IMesaService.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';

use App\Models\Mesa as Mesa;
use App\Models\Pedido as Pedido;

class MesaController implements IMesaService {

    public function ListarUnaMesa($request, $response, $args)
    {
        $arrayFinal = array();
        $mesaId = intval($args['id']);

        $mesa = Mesa::find($mesaId);

        $mesa->listaPedidos = Pedido::where('id_mesa', $mesaId)->get();
        array_push($arrayFinal, $mesa);
        
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarMesas($request, $response){

        $arrayFinal = array();
        $mesas = Mesa::all();
        
        foreach($mesas as $mesa){
            $mesa->listaPedidos = Pedido::where('id_mesa', $mesa->id)->get();
            array_push($arrayFinal, $mesa);
        }

        $mesasJson = json_encode($arrayFinal);
        $response->getBody()->write($mesasJson);
        
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnaMesa($request, $response){

        $ArrayParam = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id_cliente = $ArrayParam['id_cliente'];
        $mesa->codigo = $ArrayParam['codigo'];
        $mesa->estado = $ArrayParam['estado'];

        if($mesa !== null){
            $mesa->save();
            $payload = json_encode(array("mensaje" => "Mesa cargada exitosamente"));
            $response->getBody()->write($payload);
        }
        else{
            $payload = json_encode(array("mensaje" => "error"));
            $response->getBody()->write($payload);
        }

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUnaMesa($request, $response, $args){
        $mesaId = $args['id'];
        // Buscamos la mesa
        $mesa = Mesa::find($mesaId);

        if($mesa !== null){
            // Borramos
            $mesa->delete();
            $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUnaMesa($request, $response, $args){
        $parametros = $request->getParsedBody();
        
        $mesaModificada = new Mesa();
        $mesaModificada->id_cliente = $parametros['id_cliente'];
        $mesaModificada->codigo = $parametros['codigo'];
        $mesaModificada->estado = $parametros['estado'];
        
        $mesaId = $args['id'];

        // Conseguimos el objeto
        $mesa = Mesa::where('id', '=', $mesaId)->first();

        // Si existe
        if ($mesa !== null) {
            // Seteamos una nueva mesa
            $mesa->id_cliente = $mesaModificada->id_cliente;
            $mesa->codigo = $mesaModificada->codigo;
            $mesa->estado = $mesaModificada->estado;
            // Guardamos en base de datos
            $mesa->save();
            $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

?>