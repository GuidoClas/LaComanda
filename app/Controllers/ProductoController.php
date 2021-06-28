<?php
require_once './models/Producto.php';
require_once './Utils/CSVLoader.php';
require_once './Utils/PDF.php';

use App\Models\Producto as Producto;
use Utils\CSV\CSVLoader as CSV;

class ProductoController {

    public function CargarPorCSV($request, $response){
        $pathArchivo = self::ObtenerArchivo("archivoCSV");
        $payload = json_encode(array("mensaje" => "Productos cargados por CSV"));
        
        $csv = CSV::ObtenerDatosProductos($pathArchivo);
        
        foreach($csv as $prod){
            $producto = new Producto();
            $producto->tipo = $prod['tipo'];
            $producto->precio = $prod['precio'];
            $producto->descripcion = $prod['descripcion'];

            if(!$producto->save()){
                $payload =json_encode(array("mensaje" => "error"));
            }
        }

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function DescargarPorCSV($request, $response){
        
        $productos = Producto::all()->toArray();
        $path = dirname(__DIR__,1) . '/ProductsCSV/';
        try{
            $fp = fopen($path . 'stockProductosDescargados.csv', 'w');

            foreach($productos as $prod){
                fputcsv($fp, $prod);
            }
            fclose($fp);

        }catch(Exception $ex){
            $response->getBody()->write(json_encode(array("mensaje" => "error")));
            return $response
            ->withHeader('Content-Type', 'application/json');
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . 'stockProductosDescargados' . '.csv"');
        header('Content-Length: ' . filesize($path . '/stockProductosDescargados.csv'));
        echo readfile($path . 'stockProductosDescargados.csv'); 
    
        return $response;
    }

    public function DescargarPorPDF($request, $response){
        $productos = Producto::all('tipo', 'precio', 'descripcion')->toArray();
        $arrayProd = array();

        foreach($productos as $p){
            $arrP = array();
            array_push($arrP, $p['tipo']);
            array_push($arrP, $p['precio']);
            array_push($arrP, $p['descripcion']);
            array_push($arrayProd, $arrP);
        }

        $payload = json_encode($productos);
        $pdf = self::CrearPDF($arrayProd);
       
        $pdf->Output("productos.pdf", 'D');   

        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function CrearPDF($arrayProd){
        $pdf = new PDF();
        $header = array('Tipo', 'Precio', 'Descripcion');

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->AddPage();
        $pdf->Cell(10, 15, 'LISTA DE PRODUCTOS');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->FancyTable($header,$arrayProd);
       
        return $pdf;
    }
    
    public static function ObtenerArchivo ( string $nombreFile ) : ?string {
        return (key_exists($nombreFile, $_FILES)) ? $_FILES[$nombreFile]['tmp_name'] : NULL;
    }
}

?>
