<?php

class Utils{

    public static function validarUsuario($nombre, $apellido, $tipo){
        if((!isset($nombre) || !self::validarTexto($nombre)) || (!isset($apellido) || !self::validarTexto($apellido)) || !isset($tipo)){
            return false;
        }
        return true;
    }

    private static function validarTexto($texto){

        if(isset($texto) && is_string($texto) && preg_match("/^[A-Za-z]{3,20}\ ?+[A-Za-z]{0,20}$/", $texto)){
            return true;
        }

        return false;
    }

}

?>