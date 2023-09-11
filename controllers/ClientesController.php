<?php

require_once 'models/Cliente.php';

class ClientesController extends ControllerBase{


    function __construct()
    {
        parent::__construct();
        $this->isPublic = false;
    }

    function render()
    {
        if($this->isPublic)
        {
            $this->view->render('Clientes/index');
        }else{
            if($this->validarAcceso()){
                $this->view->render('Clientes/index');
            }else{
                $this->redirect('Login/');
            }
        }
    }

    function validarAcceso(){
        session_name("LOGIN");
        session_start();
        $trabajadorId = $_SESSION['TrabajadorId'];
        $rol = $_SESSION['Rol'];

        
        if($rol == 1){
            return true;
        }

        return false;
        
    }

    function LogOut(){
        session_name("LOGIN");
        session_start();
        unset($_SESSION['TrabajadorId']);
        unset($_SESSION['Rol']);
        session_destroy();
        $this->redirect('Login/');
    }

    function Crear(){
        $mensaje = "";
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $cliente = new Cliente();
            $cliente->nombre = $_POST['nombreCrear'];
            $cliente->apellido = $_POST['apellidoCrear'];
            $cliente->sexo = $_POST['generoCrear'];
            $cliente->nit = $_POST['nitCrear'];
            $cliente->direccion = $_POST['direccionCrear'];
            $cliente->telefono = $_POST['telefonoCrear'];
            $cliente->email = $_POST['emailCrear'];

            $res = $this->model->insert($cliente);
            $id = $this->model->getLastId();
            
            if($res){
                $mensaje = "Cliente Insertado con Exito";
            }
            else{
                $mensaje = "Hubo un erro al insertar el cliente";
            }
        }
        

        $respuesta = array(
            'Respuesta' => $res,
            'Mensaje' => $mensaje,
            'Valor' => $id
        );

        header('Content-Type: application/json');
        echo json_encode($respuesta);
    }

    function Listar(){
        $res = $this->model->read();
            
        if(isset($res)){
            $this->view->model = $res;
        }
    }

    function Actualizar(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $cliente = new Cliente();
            $cliente->id = intval($_POST['idActualizar']);
            $cliente->nombre = $_POST['nombreActualizar'];
            $cliente->apellido = $_POST['apellidoActualizar'];
            $cliente->sexo = $_POST['generoActualizar'];
            $cliente->nit = $_POST['nitActualizar'];
            $cliente->direccion = $_POST['direccionActualizar'];
            $cliente->telefono = $_POST['telefonoActualizar'];
            $cliente->email = $_POST['emailActualizar'];
            $res = $this->model->update($cliente);
            
            if($res){
                $mensaje = "Cliente Actualizada con Exito";
            }
            else{
                $mensaje = "Hubo un error al Actualizar el Cliente";
            }
        }

        $respuesta = array(
            'Respuesta' => $res,
            'Mensaje' => $mensaje,
            'Valor' => $cliente
        );

        header('Content-Type: application/json');
        echo json_encode($respuesta);
    
    }

    function Eliminar(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id = intval($_POST['idEliminar']);

            $res = $this->model->delete($id);
            
            if($res){
                $mensaje = "Cliente Eliminado con Exito";
            }
            else{
                $mensaje = "Hubo un error al Eliminar el Cliente";
            }
        }

        $respuesta = array(
            'Respuesta' => $res,
            'Mensaje' => $mensaje,
            'Valor' => $id
        );

        header('Content-Type: application/json');
        echo json_encode($respuesta);
    
    }
}

?>