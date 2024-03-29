<?php

class VentaController extends ControllerBase{


    function __construct()
    {
        parent::__construct();
        $this->isPublic = false;
    }

    function render()
    {
        if($this->isPublic)
        {
            $this->view->render('Venta/index');
        }else{
            if($this->validarAcceso()){
                $this->view->render('Venta/index');
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

    function busqueda(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $busqueda = $_POST['busqueda'];

            $res = $this->model->search($busqueda);
            
            if($res){
                $mensaje = "Categoria Actualizada con Exito";
            }
            else{
                $mensaje = "Hubo un error al Actualizar la Categoria";
            }
            
            $respuesta = array(
                'Respuesta' => isset($res),
                'Mensaje' => $mensaje,
                'Valor' => $res
            );
            
            header('Content-Type: application/json');
            echo json_encode($respuesta);
        }
    }
}

?>