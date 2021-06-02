<?php

require_once './Services/IMesaService.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';

class MesaController implements IMesaService {

    public function ListarUnaMesa($request, $response)
    {
    }

    public function ListarMesas($request, $response){

        $arrayFinal = array();
        $mesas = Mesa::TraerTodasMesas();
        
        foreach($mesas as $mesa){
            
            $mesa->pedido = Pedido::TraerPedidoPorId($mesa->id);
            array_push($arrayFinal, $mesa);
        }

        $mesasJson = json_encode($arrayFinal);
        $response->getBody()->write($mesasJson);
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUnaMesa($request, $response){

        $ArrayParam = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->CrearMesa($ArrayParam['codigo']);

        if($mesa->validarMesa()){
            if($mesa->InsertarMesa() > 0){
                $response->getBody()->write("Mesa cargada");
            }
            else{
                $response->getBody()->write("Error al cargar la mesa");
            }
        }
        return $response;
    }

    public function BorrarUnaMesa($request, $response){

    }

    public function ModificarUnaMesa($request, $response){

    }
}