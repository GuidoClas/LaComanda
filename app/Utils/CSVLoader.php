<?php

namespace Utils\CSV;

class CSVLoader{

    public static function ObtenerDatosUsuarios($pathArchivo){
        if ($pathArchivo === NULL) return array();

        $arrayColumnas = array("usuario","clave", "nombre", "apellido", "estado", "tipo");
        $retorno = array();
    
        if (($archivo = fopen($pathArchivo, "r")) !== false) {
            while (($data = fgetcsv($archivo, 0, ',')) !== false) {
                $count = count($data);
                $datosArchivo = array();

                if ($count != count($arrayColumnas)) {
                    fclose($archivo);
                    return array();
                }

                for ( $index = 0; $index < $count; $index++ ) {
                    $datosArchivo[$arrayColumnas[$index]] = $data[$index];
                }
                array_push($retorno, $datosArchivo);
            }
            fclose($archivo);
        }
        return $retorno;
    }

    public static function ObtenerDatosProductos($pathArchivo){
        if ($pathArchivo === NULL) return array();

        $arrayColumnas = array("tipo","precio", "descripcion");
        $retorno = array();
    
        if (($archivo = fopen($pathArchivo, "r")) !== false) {
            while (($data = fgetcsv($archivo, 0, ',')) !== false) {
                $count = count($data);
                $datosArchivo = array();

                if ($count != count($arrayColumnas)) {
                    fclose($archivo);
                    return array();
                }

                for ( $index = 0; $index < $count; $index++ ) {
                    $datosArchivo[$arrayColumnas[$index]] = $data[$index];
                }
                array_push($retorno, $datosArchivo);
            }
            fclose($archivo);
        }
        return $retorno;
    }

}

?>