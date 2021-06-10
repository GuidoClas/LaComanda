<?php
use Firebase\JWT\JWT;

class AuthentificatorJWT{

    private static array $encriptacion = ['HS256'];
    private static string $clave = "miClaveIndescifrable157951";

    public static function GenerarToken($datos){
       
        $hora = time();

        $payload = array(
        	'iat'=>$hora,
            'exp' => $hora + (60*60),
            'aud' => self::Aud(),
            'data' => $datos
        );

        return JWT::encode($payload, self::$clave, self::$encriptacion[0]);
    }

    public static function VerificarToken($token)
    {
        if(empty($token)|| !isset($token))
        {
            throw new Exception("El token esta vacio.");
        } 

        try {
            $payload = JWT::decode($token,self::$clave,self::$encriptacion);
        } catch (Exception $e) {
           throw new Exception("Clave expirada");
        }
        
        if($payload->aud !== self::Aud())
        {
            throw new Exception("No es el usuario valido");
        }
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$clave,
            self::$encriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}
?>