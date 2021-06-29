<?php
require_once './models/Operacion.php';
use App\Models\Operacion as Op;

class LogOperaciones{

    public static function Loguear($usuario, $tipo, $descripcion){
        if(isset($usuario) && isset($tipo) && isset($descripcion)){
            
            $op = new Op();
            $op->usuario = $usuario;
            $op->tipo = $tipo;
            $op->descripcion = $descripcion;
            $op->save();
            
        }
    }
}

?>