<?php

require_once 'models/Ingreso.php';
require_once 'views/Ingresos/IngresosView.php';

class IngresosController extends ControllerBase{


    function __construct()
    {
        $this->view = new IngresosView();
        $this->isPublic = false;
    }

    function loadModel($model){
        parent::loadModel($model);
        $this->view->proveedores = $this->cargarProveedores();
        $this->Listar();
    }

    function render()
    {
        if($this->isPublic)
        {
            $this->view->render('Ingresos/index');
        }else{
            if($this->validarAcceso()){
                $this->view->render('Ingresos/index');
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
        date_default_timezone_set('America/Guatemala');
        $mensaje = "";
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $ingreso = new Ingreso();
            $ingreso->fecha = date('d/m/Y');
            $ingreso->personal->id = $_POST['personalId'];
            $ingreso->proveedor = $_POST['proveedorId'];

            $res = $this->model->insert($ingreso);
            $id = $this->model->getLastId();
            
            if($res){
                $mensaje = "Producto Insertado con Exito";
            }
            else{
                $mensaje = "Hubo un erro al insertar el producto";
            }
        }
        

        $respuesta = array(
            'Respuesta' => $res,
            'Mensaje' => $mensaje,
            'Valor' => $ingreso
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

    function Eliminar(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id = intval($_POST['id']);

            $res = $this->model->delete($id);
            
            if($res){
                $mensaje = "Ingreso Eliminado con Exito";
            }
            else{
                $mensaje = "Hubo un error al Eliminar el Ingreso";
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

    function cargarProveedores(){
        $res = $this->model->getProvedores();
        if(isset($res)){
            return $res;
        }
        else{
            return array('No definido');
        }
    }
}

?>