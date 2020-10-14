<?php
include_once __DIR__.'/ManejoArchivos.php';

class Servicios extends ManejoArchivos{ 
    public $_nombre;
    public $_tipo;
    public $_id;
    public $_precio;
    public $_demora;
    public static $pathAutosJSON = './tiposServicio.json';

    public function __construct($nombre,$tipo,$id,$precio,$demora) {
        $this->_nombre = $nombre;
        $this->_tipo = $tipo;
        $this->_id = $id;
        $this->_precio = $precio;
        $this->_demora=$demora;
    }
    

    public function __get($name){ return $this->$name; }
    public function __set($name, $value){ $this->$name = $value; }
    public function __toString(){

        $datos = '';
        $datos .= 'DATOS DEL Servicio:</br>';
        $datos .= 'Nombre: ' . $this->_nombre  . '</br>';
        $datos .= 'Tipo: ' . $this->_tipo  . '</br>';
        $datos .= 'Id: ' . $this->_id  . '</br>';
        $datos .= 'PRECIO: ' . $this->_precio  . '</br>';
        $datos .= 'Demora: '. $this->_demora  .'</br>';

        return $datos;
    }


    

    //----------------------------------------------------------------
    //----------------------------------------------------------------
    //JSON
    public static function SaveIngresoJSON(array $arrayObj = null){
        try {

            echo parent::SaveJSON(Servicios::$pathAutosJSON,$arrayObj);

        } catch (\Throwable $e) {

            throw new Exception($e->getMessage());
            
        }
    }

    public static function ReadIngresoJSON(){
        try {
            //Pasamanos...
            $listaFromArchivoJSON = parent::ReadJSON(Servicios::$pathAutosJSON);
            $arrayIngreso = [];

            foreach ($listaFromArchivoJSON as $dato) {

                $nuevoServicio = new Servicios($dato->_nombre,$dato->_tipo,$dato->_id,$dato->_precio,$dato->_demora);

                array_push($arrayIngreso,$nuevoServicio);

            }

        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
        return $arrayIngreso;
    }
}