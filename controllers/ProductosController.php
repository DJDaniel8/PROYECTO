<?php

require_once 'models/Producto.php';

class ProductosController extends ControllerBase{


    function __construct()
    {
        parent::__construct();
        $this->isPublic = false;
    }

    function loadModel($model){
        parent::loadModel($model);
        //$this->Listar();
    }

    function render()
    {
        if($this->isPublic)
        {
            $this->view->render('Productos/index');
        }else{
            if($this->validarAcceso()){
                $this->view->render('Productos/index');
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
            $producto = new Producto();
            $producto->codigo = $_POST['codigo'];
            $producto->nombre = $_POST['nombre'];
            $producto->descripcion = $_POST['descripcion'];

            if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
                $nombreTemporal = $_FILES["imagen"]["tmp_name"];
                $nombreArchivo = $_FILES["imagen"]["name"];

                $directorioDestino = constant('URL').'resources/imagenes/'. $nombreArchivo;

                if (move_uploaded_file($nombreTemporal, $directorioDestino)) {
                    echo "La imagen se ha cargado correctamente.";
                } else {
                    echo "Error al mover la imagen al servidor.";
                }

                $accountKey = 'tu_clave_de_acceso';
                $blobName = $nombreArchivo;
                $localFilePath = constant('URL').'resources/imagenes/'. $nombreArchivo;
                $blobUrl = constant('blobServiceUrl').'/'.$blobName;
                $accountName = constant('accountName');
                $accountKey = constant('accountKey');

                $extension = pathinfo($localFilePath, PATHINFO_EXTENSION);

                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $contentType = 'image/jpeg';
                } elseif ($extension === 'png') {
                    $contentType = 'image/png';
                }

                $ch = curl_init($blobUrl);
                $fp = fopen($localFilePath, 'rb');
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_INFILE, $fp);
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFilePath));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "x-ms-blob-type: BlockBlob",
                    "x-ms-blob-content-type: $contentType" // Cambia el tipo de contenido según corresponda
                ));
                curl_setopt($ch, CURLOPT_USERPWD, "$accountName:$accountKey");

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);
                fclose($fp);

                if ($httpCode === 201) {
                    echo "La imagen se cargó correctamente en Azure Blob Storage.";
                } else {
                    echo "Error al cargar la imagen en Azure Blob Storage. Código HTTP: $httpCode";
                }
            }

            
            $producto->imagen = $blobUrl;

            $res = $this->model->insert($producto);
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
            'Valor' => $id
        );

        header('Content-Type: application/json');
        echo json_encode($respuesta);
    }
}

?>