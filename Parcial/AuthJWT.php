<?php

require './vendor/autoload.php';
use \Firebase\JWT\JWT; //namespace

class AuthJWT {

    private static $secretKey = 'primerparcial';
    
    /**
     * Crea un token con los claims especificados en el payload por los datos.
     *
     * @param   object $datos
     * @return  string Json Web Token (JWT).
     */
    public static function Login( $datos ) {

        //$payload = [ 'data' => $datos ];

        return JWT::encode( $datos, self::$secretKey );

    }

    public static function ValidarToken( $jwt ) {
        
        try {
            
            $jwtDecodificado = JWT::decode( $jwt, self::$secretKey, ['HS256'] );

            // print_r( $jwtDecodificado );
            
        } catch (\Throwable $e) {
            
            // throw new Exception( '</br>Token Incorrecto!</br>' . $e . '</br>');
            throw new Exception( $e->getMessage() );
            
        }
        
        return $jwtDecodificado;

    }

    public static function GetDatos ( $jwt ) {

        try {
            
            return JWT::decode( $jwt, self::$secretKey, ['HS256'] );

        } catch (\Throwable $e) {
            
            // throw new Exception( '</br>Token Incorrecto!</br>' . $e . '</br>');

            throw new Exception( $e->getMessage() );

        }

    }

}