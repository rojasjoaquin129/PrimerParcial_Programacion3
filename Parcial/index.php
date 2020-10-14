<?php

include_once __DIR__.'/AuthJWT.php';
include_once __DIR__.'/ManejoArchivos.php';
include_once __DIR__.'/usuario.php';
include_once __DIR__.'/Servicio.php';
include_once __DIR__.'/veiculos.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? 0;
$jwt = $_SERVER['HTTP_TOKEN'] ?? '';

try {

    $jwtDecodificado = AuthJWT::ValidarToken( $jwt );

    // print_r(AuthJWT::GetDatos( $_SERVER['HTTP_TOKEN'] ));

    // print_r($jwtDecodificado);

} catch (\Throwable $e) {

    echo $e;
    
    //var_dump($e->getTrace());

}

switch ($path) {

    case '/registro':

        switch ($method) {

            case 'POST':

                try {
                    
                    $listaUsuariosJSON = Usuario::ReadUsuarioJSON();
                    
                    $email = $_POST['email'] ?? '';
                    $tipo = $_POST['tipo'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $extArray=explode('.',$_FILES["Archivo"]['name']);
                    


                    if(( $tipo === 'admin' || $tipo === 'user' ) &&(guardarImagen($extArray))) {

                        $foto=$extArray[0].".".$extArray[1];
                        $nuevoUsuario = new Usuario($email, $tipo,$password,$foto);

                        $emailRegistrado = $nuevoUsuario->EmailUnico( $listaUsuariosJSON );

                        if( !$emailRegistrado ) {

                            array_push($listaUsuariosJSON,$nuevoUsuario);
                            //var_dump($listaUsuariosJSON);
                            Usuario::SaveUsuarioJSON($listaUsuariosJSON);

                        }else {

                            throw new Exception('</br>El Email ya está registrado!</br>');

                        }
                 

                    }else {

                        throw new Exception( '</br>Tipo incorrecto.(Sólo puede ser admin o user)</br>' );

                    }

                } catch (\Throwable $e) {

                    echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                    var_dump($e->getTrace());

                }

                break;
        }

        break;

    case '/login':

        switch ($method) {

            case 'POST':

                try {

                    $listaUsuariosJSON = Usuario::ReadUsuarioJSON();

                    $email = $_POST['email'] ?? '';
                    $password = $_POST['password'] ?? '';

                    foreach ($listaUsuariosJSON as $key) {
                        if($key->_tipoUsuario === 'admin') {
                            
                            $nuevoUsuario = new Usuario($email, $key->_tipoUsuario,$password,null);
                            
                        }else if($key->_tipoUsuario === 'user') {

                            $nuevoUsuario = new Usuario($email, $key->_tipoUsuario,$password,null);

                        }
                    }

                    $estaLaPatente = $nuevoUsuario->verificarUsuario($listaUsuariosJSON);

                    if($estaLaPatente){

                        $payload = ['email' => $nuevoUsuario->_email,
                                    'tipo' =>$nuevoUsuario->_tipoUsuario ];

                        $token = AuthJWT::Login( $payload );

                        print_r($token);

                        //var_dump(AuthJWT::ValidarToken($token));
                        
                        echo '</br>Login con éxito!</br>';

                    }else{

                        echo '</br>LOGIN SIN ÉXITO :(</br>';

                    }

                } catch (\Throwable $e) {

                    echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                    var_dump($e->getTrace());

                }

                break;
        }

        break;

    

    case '/vehiculo':

        if($jwtDecodificado->tipo === 'user' && $method=='POST')
        { //Verifico JWT por el header

            try 
            {
                //Email y tipo están en el token.
                $listaIngresoJSON = vehiculos::ReadIngresoJSON();

                $patente = $_POST['patente'] ?? '';
                $modelo=$_POST['modelo'] ?? '';
                $marca=$_POST['marca'] ?? '';
                $precio=$_POST['precio'] ?? '';

                $nuevoIngreso = new vehiculos( $patente,$marca, $modelo, $precio );

                if($nuevoIngreso->verificarPatente($listaIngresoJSON))
                {
                array_push( $listaIngresoJSON, $nuevoIngreso);
                }
                else
                {
                    echo "patente repetida se encuentra en la lista";
                }
                vehiculos::SaveIngresoJSON( $listaIngresoJSON );
    
            } catch (\Throwable $e) 
            {
                echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                var_dump($e->getTrace());
            }
        }
        else
        {
            echo '</br>Usted no es del tipo user.</br>';
        }

    break;

    case '/patente':
        if($jwtDecodificado->tipo === 'user' && $method=='GET')
        {
            $flag=false;
            $listaIngresoJSON = vehiculos::ReadIngresoJSON();
            $desconocido = $_GET['algo'] ?? '';
            $arraycoincidencias=array();
            
            if($listaIngresoJSON !== null){
                foreach ($listaIngresoJSON as $patente ) {
    
                    if($patente->_patente === $desconocido ||$patente->_modelo === $desconocido||$patente->_marca === $desconocido){
                        $flag = true;
                        echo $patente ;
                    }
                }
            }
        }
        else
        {
            echo '</br>Usted no es del tipo user.</br>';
        }
    break;

    case '/sevicio':
        if($jwtDecodificado->tipo === 'user' && $method=='POST')
        {
            $listaServicioJSON = Servicios::ReadIngresoJSON();
            $nombre = $_POST['nombre'] ?? '';
            $tipo=$_POST['tipo'] ?? '';
            $id=$_POST['id'] ?? '';
            $precio=$_POST['precio'] ?? ''; 
            $demora=$_POST['demora']??'';
            if($tipo===  '10000' ||$tipo==='20000'||$tipo ==='50000'){

            $nuevoServicio=new Servicios($nombre,$tipo,$id,$precio,$demora);
            array_push( $listaServicioJSON, $nuevoIngreso);
            }
            else
            {
                echo "no es un tipo apropiado";
            }
            Servicios::SaveIngresoJSON( $listaServicioJSON );
            echo "guardado con exito";

        }else
        {
            echo '</br>Usted no es del tipo user.</br>';
        }




    break;

    
}
function guardarImagen($extArray)
{
    //$aleatorio=rand(1000,10000);
    $flag=false;
    if($extArray[1]=="jpg"|| $extArray[1]=="png")
    {
        if($_FILES['Archivo']['size']<= 3500000)
        {
        
        $origen=$_FILES['Archivo']["tmp_name"]; // saco el origen del archivo 
        $destino="img/".$extArray[0].".".$extArray[1]; // cambio el nombre del archivo
        //$destino="MarcaDeAgua/".$extArray[0].".".$extArray[1]; 
        $subido=move_uploaded_file($origen,$destino);//lo mueve al archivo  ;
        //insertarMarcaDeAguaEnMedio($destino);
        $flag=true;
        //echo "Se subio la imagen ";
        }
        else
        {
            echo "La imagen pesa mas de 3,5 mb";
        }
        
    }
    else
    {
        
        echo "EL archivo no es una imagen";
    }
    return $flag;
}



