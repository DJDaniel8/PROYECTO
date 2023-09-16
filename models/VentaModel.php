<?php

require_once 'models/Stock.php';

class VentaModel extends ModelBase {

    public function __construct()
    {
        parent::__construct();
    }

    public function search($busqueda){
        $stocks = array();
        $query = "SELECT * FROM [dbo].[Productos] as p
                    INNER JOIN [dbo].[Stocks] as s ON p.productoId = s.productoId
                    WHERE p.nombre like '%{$busqueda}%' OR p.codigo like '%{$busqueda}%'";
        $conexion = $this->db->connect();
        $resultadoQuery = $conexion->prepare($query);

        
        $resultadoQuery->execute();
        
        while ($row = $resultadoQuery->fetch()) {
            $stock = new Stock();
            $stock->id = intval($row['stockId']);
            $stock->producto->id = intval($row['productoId']);
            $stock->producto->codigo = $row['codigo'];
            $stock->producto->nombre = $row['nombre'];
            $stock->stock = intval($row['stock']);
            $stock->precioCompra = floatval($row['precio_compra']);
            $stock->precioVenta = floatval($row['precio_venta_sugerido']);
            $stock->precioVentaMinimo = floatval($row['precio_minimo']);
            array_push($stocks, $stock);
        }

        return $stocks;
    }
}

?>