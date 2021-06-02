<?php
require_once './Dao/accessDAO.php';

class Mesa{

    public $id;
    public $codigoMesa;
    public $tiempoRestante;

    public function __construct(){

    }

    
    public function CrearMesa($codigo){
        $this->codigoMesa = $this->validarCodigo($codigo) ? $codigo : null;
        $this->tiempoRestante = 30;
    }
    
    private function validarCodigo($codigo){

        if(isset($codigo) && preg_match("/^[a-zA-Z]{5,5}+$/", $codigo)){
            return true;
        }
        return false;
    }

    public function validarMesa(){
        if(!isset($this->codigoMesa) || !isset($this->tiempoRestante)){
            return false;
        }
        return true;
    }


    public function InsertarMesa(){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO mesas (codigo,tiempoRestante)VALUES(:codigo,:tiempoRestante)");
        $consulta->bindValue(':codigo', $this->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoRestante', $this->tiempoRestante, PDO::PARAM_INT);
		$consulta->execute();

		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}

    public static function TraerTodasMesas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
}

?>