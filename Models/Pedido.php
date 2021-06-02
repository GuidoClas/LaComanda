<?php
require_once './Dao/accessDAO.php';

class Pedido{

    public $id;
    public $id_mesa;
    public $codigo;
    public $estado;

    public function __construct(){

    }

    
    public function CrearPedido($codigo, $id_mesa){
        $this->codigo = $this->validarCodigo($codigo) ? $codigo : null;
        $this->id_mesa = $id_mesa;
        $this->estado = "En preparación";
    }
    
    private function validarCodigo($codigo){

        if(isset($codigo) && preg_match("/^[a-zA-Z0-9]{5,5}+$/", $codigo)){
            return true;
        }
        return false;
    }

    public function validarPedido(){
        if(!isset($this->codigo) || !isset($this->estado) || !isset($this->id_mesa)){
            return false;
        }
        return true;
    }


    public function InsertarPedido(){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO pedidos (codigo,estado,id_mesa)VALUES(:codigo,:estado,:id_mesa)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
		$consulta->execute();

		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}

    public static function TraerTodosPedidos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function TraerPedidoPorId($id){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos WHERE id_mesa LIKE :idMesa");
        $consulta->bindValue(':idMesa', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}

?>