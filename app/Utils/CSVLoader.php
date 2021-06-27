<?php

namespace Utils\CSV;

class CSVLoader{

    public static function ObtenerDatos($pathArchivo){
        if ($pathArchivo === NULL) return array();
        
        $arrayColumnas = array("usuario","clave", "tipo", "nombre", "apellido", "estado");
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