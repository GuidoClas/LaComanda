<?php
require_once './Dao/accessDAO.php';

class Usuario{

    public $nombre;
    public $apellido;
    public $id;
    public $fechaAlta;
    public $fechaBaja;
    public $tipo;

    const TIPOS = array("Mozo", "Bartender", "Cervecero", "Cocinero", "Socio");

    public function __construct(){

    }

    
    public function CrearUsuario($nombre, $apellido, $fechaAlta, $fechaBaja, $tipo){
        $this->nombre = $this->validarTexto($nombre) ? $nombre : null;
        $this->apellido = $this->validarTexto($apellido) ? $apellido : null;
        $this->fechaAlta = $fechaAlta;
        $this->fechaBaja = $fechaBaja;
        $this->tipo = $this->validarTipo($tipo) ? $tipo : null;
    }
    
    private function validarTexto($texto){

        if(isset($texto) && is_string($texto) && preg_match("/^[A-Za-z]{3,20}\ ?+[A-Za-z]{0,20}$/", $texto)){
            return true;
        }

        return false;
    }

    public function validarUsuario(){
        if(!isset($this->nombre) || !isset($this->apellido) || !isset($this->tipo)){
            return false;
        }
        return true;
    }

    private function validarTipo($tipo){

        if(isset($tipo) && in_array($tipo, self::TIPOS)){
            return true;
        }
        return false;
    }

    public function InsertarUsuario(){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
	    $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO usuarios (tipo,nombre,apellido,fechaAlta,fechaBaja)VALUES(:tipo,:nombre,:apellido,:fechaAlta,:fechaBaja)");
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
		$consulta->execute();

		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}

    public static function TraerTodosUsuarios()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from usuarios");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
}

?>