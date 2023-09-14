<?php

require_once 'models/Ingreso.php';

class IngresosModel extends ModelBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function insert(Ingreso $ingreso){
        $query = "INSERT INTO Ingresos (fecha, trabajadorId, proveedorId) VALUES (:fecha, :trabajadorId, :proveedorId )";
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

}

?>