<?php 
  require_once('utilidad.php');
class factura extends utilidad
{
        public $num_factura;
        public $fecha;
        public $id_usuario; 
        public $cod_contribuyente; 
        public $concepto; 
        public $total_factura;
        public $ESTADO_FACT;


  public function listar()
  {
    //$this->que_bda="SELECT * FROM factura WHERE 1=1";
    $this->que_bda="SELECT num_factura, fecha, cedula_rif, razon_social, concepto, total_factura, ESTADO_FACT
      from factura 
      INNER JOIN contribuyente
      ON factura.cod_contribuyente = contribuyente.id_contribuyente ORDER BY num_factura ASC";
    return $this->ejecutar(); // Consulta para mostrar datos con tablas relacionadas entre si
  }

   public function listar_fecha()
  {
    $this->que_bda="SELECT num_factura, fecha, cedula_rif, razon_social, concepto, total_factura, ESTADO_FACT
      from factura 
      INNER JOIN contribuyente
      ON factura.cod_contribuyente = contribuyente.id_contribuyente where fecha='$this->fecha';";
    return $this->ejecutar(); // Consulta para mostrar datos con tablas relacionadas entre si
  }

   public function listar_num_factura()
  {
    $this->que_bda="SELECT num_factura, fecha, cedula_rif, razon_social, concepto, total_factura, ESTADO_FACT
      from factura 
      INNER JOIN contribuyente
      ON factura.cod_contribuyente = contribuyente.id_contribuyente where num_factura='$this->num_factura';";
    return $this->ejecutar(); // Consulta para mostrar datos con tablas relacionadas entre si
  }

   public function listar_reimprimir_factura()
  {
    $this->que_bda="SELECT num_factura, fecha, id_contribuyente, cedula_rif, razon_social, concepto, total_factura, ESTADO_FACT
      from factura 
      INNER JOIN contribuyente
      ON factura.cod_contribuyente = contribuyente.id_contribuyente where num_factura='$this->num_factura';";
    return $this->ejecutar(); // Consulta para mostrar datos con tablas relacionadas entre si
  }

 public function mostrar_factura()
{
  // Consulta SQL 
    $this->que_bda = " SELECT 
            factura.num_factura, 
            factura.fecha, 
            contribuyente.id_contribuyente, 
            contribuyente.cedula_rif, 
            contribuyente.razon_social, 
            factura.concepto,
            d.impuesto_A, cA.nombre AS nombre_impuesto_A, d.monto_impuesto_A,
            d.impuesto_B, cB.nombre AS nombre_impuesto_B, d.monto_impuesto_B, 
            d.impuesto_C, cC.nombre AS nombre_impuesto_C, d.monto_impuesto_C, 
            d.impuesto_D, cD.nombre AS nombre_impuesto_D, d.monto_impuesto_D, 
            d.impuesto_E, cE.nombre AS nombre_impuesto_E, d.monto_impuesto_E,  
            d.impuesto_F, cF.nombre AS nombre_impuesto_F, d.monto_impuesto_F, 
            factura.total_factura, 
            factura.ESTADO_FACT
        FROM 
            factura
        INNER JOIN 
            contribuyente ON factura.cod_contribuyente = contribuyente.id_contribuyente
        INNER JOIN 
            detalle_recibo d ON factura.num_factura = d.cod_factura
        LEFT JOIN 
            clasificador cA ON d.impuesto_A = cA.id_clasificador
        LEFT JOIN 
            clasificador cB ON d.impuesto_B = cB.id_clasificador
        LEFT JOIN 
            clasificador cC ON d.impuesto_C = cC.id_clasificador
        LEFT JOIN 
            clasificador cD ON d.impuesto_D = cD.id_clasificador
        LEFT JOIN 
            clasificador cE ON d.impuesto_E = cE.id_clasificador
        LEFT JOIN 
            clasificador cF ON d.impuesto_F = cF.id_clasificador
        WHERE 
            num_factura = '$this->num_factura';";

    // Ejecutar la consulta y devolver los resultados
    return $this->ejecutar(); 
}

public function anular_factura()
{
     echo $this->que_bda = "UPDATE 
            factura 
        SET 
            fecha = '$this->fecha',
            id_usuario = 1,
            cod_contribuyente = '$this->cod_contribuyente',
            concepto = '$this->concepto',
            total_factura = '$this->total_factura',
            ESTADO_FACT = '$this->ESTADO_FACT'
        WHERE
            num_factura = '$this->num_factura';";

    // Ejecutar la consulta y devolver los resultados
    return $this->ejecutar();
}


  public function filtrar()
{
  $filtro1=($this->num_factura!="")?" and num_factura='$this->'num_factura'":"";
  //Los % son para que se busque todo con las iniciales de los nombres.
 
 $this->que_bda="SELECT * from factura WHERE 1=1 $filtro1 ORDER BY num_factura DESC;";
return $this->ejecutar();
}
}



 ?>