<?php
include_once __DIR__.'/ManejoArchivos.php';

class vehiculos extends ManejoArchivos{ 
    public $_patente;
    public $_marca;
    public $_modelo;
    public $_precio;
    public static $pathAutosJSON = './vehiculos.json';

    public function __construct($patente,$marca,$modelo,$precio) {
        $this->_patente = $patente;
        $this->_marca = $marca;
        $this->_modelo = $modelo;
        $this->_precio = $precio;
    }

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
    public function __toString(){

        $datos = '';
        $datos .= 'DATOS DEL VEHICULO:</br>';
        $datos .= 'PATENTE: ' . $this->_patente  . '</br>';
        $datos .= 'MODELO: ' . $this->_modelo  . '</br>';
        $datos .= 'MARCA: ' . $this->_marca  . '</br>';
        $datos .= 'PRECIO: ' . $this->_precio  . '</br>';

        return $datos;
    }


    public function verificarPatente(array $array = null){
        $loginUser = false;

        if($array !== null){
            foreach ($array as $patente ) {

                if($patente->_patente === $this->_patente){
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
    public static function SaveIngresoJSON(array $arrayObj = null){
        try {

            echo parent::SaveJSON(vehiculos::$pathAutosJSON,$arrayObj);

        } catch (\Throwable $e) {

            throw new Exception($e->getMessage());
            
        }
    }

    public static function ReadIngresoJSON(){
        try {
            //Pasamanos...
            $listaFromArchivoJSON = parent::ReadJSON(vehiculos::$pathAutosJSON);
            $arrayIngreso = [];

            foreach ($listaFromArchivoJSON as $dato) {

                $nuevoIngreso = new vehiculos($dato->_patente,$dato->_marca,$dato->_modelo,$dato->_precio);

                array_push($arrayIngreso,$nuevoIngreso);

            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
        return $arrayIngreso;
    }
}