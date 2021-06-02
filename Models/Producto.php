<?php
require_once './Dao/accessDAO.php';

class Producto{

    public $id;
    public $id_pedido;
    public $tipo;
    public $descripcion;

    const TIPOS = array("Bebida", "Comida", "Postre");

    public function __construct(){

    }

    
    public function CrearProducto($id_pedido, $tipo, $descripcion){
        $this->tipo = $this->validarTipo($tipo) ? $tipo : null;
        $this->id_pedido = $id_pedido;
        $this->descripcion = $this->validarTexto($descripcion) ? $descripcion : null;
    }
    
    private function validarTexto($texto){

        if(isset($texto) && is_string($texto) && preg_match("/^[a-zA-Z0-9]+$/", $texto)){
            return true;
        }

        return false;
    }

    private function validarTipo($tipo){

        if(isset($tipo) && in_array($tipo, self::TIPOS)){
            return true;
        }
        return false;
    }

    public function validarProducto(){
        if(!isset($this->id_pedido) || !isset($this->descripcion) || !isset($this->tipo)){
            return false;
        }
        return true;
    }


    public function InsertarProducto(){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO productos (id_pedido,tipo,descripcion)VALUES(:id_pedido,:tipo,:descripcion)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
		$consulta->execute();

		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}

    public static function TraerTodosProductos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from productos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function TraerProductoPorId($id){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM productos WHERE id_pedido LIKE :idPedido");
        $consulta->bindValue(':idPedido', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }
}

?>