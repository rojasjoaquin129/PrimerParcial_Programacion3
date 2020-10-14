<?php
include_once __DIR__.'/ManejoArchivos.php';

class Usuario extends ManejoArchivos{
    public $_email;
    public $_tipoUsuario;
    public $_password;
    public $_namefoto;
    public static $pathJSON = './archivos/usuarios.json';

    public function __construct($email, $tipo , $password,$foto) {
        $this->_email = $email;
        $this->_tipoUsuario = $tipo;
        $this->_password = $password;
        $this->$_namefoto=$foto;
    }

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
    public function __toString(){
        return $this->_email . '*' .$this->_tipoUsuario . '*' . $this->_password;
    }


    //  ----------------------------------------------------------------
    //----------------------------------------------------------------
    //!! VERIFICACION USUARIO
    public function verificarUsuario(array $array = null){
        $loginUser = false;

        if($array !== null){
            foreach ($array as $user ) {

                if($user->_password === $this->_password && $user->_email === $this->_email){
                    $loginUser = true;
                }
            }
        }else{
            throw new Exception('<br/>Array null.<br/>');
        }
        return $loginUser;
    }


    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //JSON
    public static function SaveUsuarioJSON(array $arrayObj = null){
        try {
            echo parent::SaveJSON(Usuario::$pathJSON,$arrayObj);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function ReadUsuarioJSON(){
        try {
            //Pasamanos...
            $listaFromArchivoJSON = parent::ReadJSON(Usuario::$pathJSON);
            $arrayUsuario = [];

            foreach ($listaFromArchivoJSON as $dato) {
                $nuevoUsuario = new Usuario($dato->_email,$dato->_tipoUsuario,$dato->_password);
                array_push($arrayUsuario,$nuevoUsuario);
            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
        return $arrayUsuario;
    }

    public function EmailUnico(array $array = null){
        if($array !== null){
            $emailRepetido = false;
            foreach ($array as $item) {
                if($item->_email === $this->_email){
                    $emailRepetido = true;
                }
            }
        }
        return $emailRepetido;
    }

    /**
     * POR SI LAS MOSCAS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
     */
    public static function autoID(array $array = null){
        if($array !== null){
            $id = 0;
            foreach ($array as $item) {
                if($item->_id > $id){
                    $id = $item->_id;
                }
            }
        }
        return $id + 1;
    }
}
