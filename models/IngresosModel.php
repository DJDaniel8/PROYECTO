<?php

require_once 'models/Ingreso.php';
require_once 'models/Proveedor.php';
require_once 'models/Producto.php';
require_once 'models/Stock.php';

class IngresosModel extends ModelBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function insert(Ingreso $ingreso){
        $query = "INSERT INTO Ingresos 
                    (fecha, trabajadorId, proveedorId) VALUES 
                    (CONVERT(datetime, :fecha ,103), :trabajadorId, :proveedorId )";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);
        $resultadoQuery->bindParam(':fecha', $ingreso->fecha, PDO::PARAM_STR);
        $resultadoQuery->bindParam(':trabajadorId', $ingreso->personal->id, PDO::PARAM_INT);
        $resultadoQuery->bindParam(':proveedorId', $ingreso->proveedor->id, PDO::PARAM_INT);
        
        $resultadoQuery->execute();
        
        if($resultadoQuery->rowCount() == 1)
        {
            return true;
        }
        else{
            return false;
        }
    }

    public function insertIntoStocks(Stock $stock){
        $query = "INSERT INTO [dbo].[Stocks]
                    (stock, precio_compra, precio_venta_sugerido, precio_minimo, productoId) VALUES
                    (:stock, :precio_compra, :precio_venta_sugerido, :precio_minimo, {$stock->producto->id})";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);
        $resultadoQuery->bindParam(':stock', $stock->stock, PDO::PARAM_INT);
        $resultadoQuery->bindParam(':precio_compra', $stock->precioCompra, PDO::PARAM_STR);
        $resultadoQuery->bindParam(':precio_venta_sugerido', $stock->precioVenta, PDO::PARAM_STR);
        $resultadoQuery->bindParam(':precio_minimo', $stock->precioVentaMinimo, PDO::PARAM_STR);

        $resultadoQuery->execute();
        
        if($resultadoQuery->rowCount() == 1)
        {
            $query = "SELECT TOP(1) * From Stocks ORDER BY stockId desc";
            $conexion = $this->db->connect();
            $resultadoQuery = $conexion->prepare($query);

            
            $resultadoQuery->execute();
                
            if($row = $resultadoQuery->fetch())
            {
                return $row['stockId'];
            }
            else{
                return 0;
            }
        }
        else{
            return 0;
        }
    }

    public function insertIntoProductosIngresos(int $ingresoId, int $productoId, int $cantidad, float $precioCompra, int $stockId){
        $query = "INSERT INTO [dbo].[Productos_Ingresos]
                    (ingresoId, productoId, cantidad, preciocompra, stockId) VALUES
                    (:ingresoId, :productoId, :cantidad, :preciocompra, :stockId)";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);
        $resultadoQuery->bindParam(':ingresoId', $ingresoId, PDO::PARAM_INT);
        $resultadoQuery->bindParam(':productoId', $productoId, PDO::PARAM_INT);
        $resultadoQuery->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $resultadoQuery->bindParam(':preciocompra', $precioCompra, PDO::PARAM_STR);
        $resultadoQuery->bindParam(':stockId', $stockId, PDO::PARAM_INT);

        $resultadoQuery->execute();
        
        if($resultadoQuery->rowCount() == 1)
        {
            return true;
        }
        else{
            return false;
        }
    }    

    public function read(){
        $ingresos = array();
        $query = "SELECT i.ingresoId, i.fecha, t.trabajadorId, t.nombre, t.apellido, p.proveedorId, p.razonSocial FROM Ingresos as i
                        INNER JOIN [dbo].[Trabajadores] as t ON i.trabajadorId = t.trabajadorId
                        INNER JOIN [dbo].[Proveedores] as p ON i.proveedorId = p.proveedorId
                        ORDER BY i.ingresoId desc";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);

        
        $resultadoQuery->execute();
        
        while ($row = $resultadoQuery->fetch()) {
            $ingreso = new Ingreso();

            $ingreso->id=$row['ingresoId'];
            $ingreso->fecha=$row['fecha'];
            $ingreso->personal->id=$row['trabajadorId'];
            $ingreso->personal->nombre=$row['nombre'];
            $ingreso->personal->apellido=$row['apellido'];
            $ingreso->proveedor->id=$row['proveedorId'];
            $ingreso->proveedor->razonSocial = $row['razonSocial'];
            
            array_push($ingresos, $ingreso);
        }

        return $ingresos;
    }


    public function delete($id){
        $query = "DELETE FROM Ingresos WHERE ingresoId = :id";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);
        $resultadoQuery->bindParam(':id', $id, PDO::PARAM_INT);

        $resultadoQuery->execute();
        
        if($resultadoQuery->rowCount() == 1)
        {
            return true;
        }
        else{
            return false;
        }

    }

    public function getLastId(){
        $query = "SELECT TOP(1) * From Ingresos ORDER BY ingresoId desc";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);

        
        $resultadoQuery->execute();
            
        if($row = $resultadoQuery->fetch())
        {
            return $row['ingresoId'];
        }
        else{
            return 0;
        }
        
    }

    public function getProvedores(){
        $proveedores = array();
        $query = "SELECT * FROM Proveedores";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);

        
        $resultadoQuery->execute();
        
        while ($row = $resultadoQuery->fetch()) {
            $proveedor = new Proveedor();

            $proveedor->id=$row['proveedorId'];
            $proveedor->razonSocial=$row['razonSocial'];
            
            array_push($proveedores, $proveedor);
        }

        return $proveedores;
    }

    public function getProductosByProveedorId($id){
        $productos = array();
        $query = "SELECT * FROM Productos WHERE proveedorId = :id";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);   
        $resultadoQuery->bindParam(':id', $id, PDO::PARAM_INT);

        
        $resultadoQuery->execute();
        
        while ($row = $resultadoQuery->fetch()) {
            $producto = new Producto();

            $producto->id=$row['productoId'];
            $producto->codigo=$row['codigo'];
            $producto->nombre=$row['nombre'];
            $producto->descripcion=$row['descripcion'];
            
            array_push($productos, $producto);
        }

        return $productos;
    }
    

}

?>