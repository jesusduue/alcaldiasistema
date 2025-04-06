<?php 
	require_once('utilidad.php');

	class detalle_recibo extends utilidad
	{
		public $id_detalle_recibo;
		public $fecha_det_recibo;
		public $cod_factura;
		public $impuesto_A;
		public $monto_impuesto_A;
		public $impuesto_B;
		public $monto_impuesto_B;
		public $impuesto_C;
		public $monto_impuesto_C;
		public $impuesto_D;
		public $monto_impuesto_D;
		public $impuesto_E;
		public $monto_impuesto_E;
		public $impuesto_F;
		public $monto_impuesto_F;

		public function eliminar($id_detalle_recibo)
		{
			// Consulta SQL para eliminar el registro
		echo $this->que_bda = "DELETE FROM detalle_recibo WHERE id_detalle_recibo = '$id_detalle_recibo';";
			
			// Ejecutar la consulta
			return $this->ejecutar(); // Retorna true si la eliminación fue exitosa
		}
	

	public function listar(){		
	//$this->que_bda="SELECT * FROM detalle_recibo WHERE 1=1";
		$this->que_bda="SELECT 
    			d.id_detalle_recibo, 
    			d.fecha_det_recibo,
    			d.cod_factura,
    			cA.nombre AS nombre_impuesto_A,
    			monto_impuesto_A,
    			cB.nombre AS nombre_impuesto_B,
    			monto_impuesto_B,
    			cC.nombre AS nombre_impuesto_C,
    			monto_impuesto_C,
    			cD.nombre AS nombre_impuesto_D,
    			monto_impuesto_D,
    			cE.nombre AS nombre_impuesto_E,
    			monto_impuesto_E,
   			 	cF.nombre AS nombre_impuesto_F,
   			 	monto_impuesto_F
				FROM 
    					detalle_recibo d
				INNER JOIN 
    				clasificador cA ON d.impuesto_A = cA.id_clasificador
				LEFT JOIN 
    				clasificador cB ON d.impuesto_B = cB.id_clasificador
				LEFT JOIN 
    				clasificador cC ON d.impuesto_C = cC.id_clasificador
				LEFT JOIN 
    				clasificador cD ON d.impuesto_D = cD.id_clasificador
				LEFT JOIN 
    				clasificador cE ON d.impuesto_E = cE.id_clasificador
				lEFT JOIN 
    				clasificador cF ON d.impuesto_F = cF.id_clasificador

					ORDER BY 
    							d.cod_factura ASC;";


	return $this->ejecutar();
	}



	public function listar_fecha_rubro(){
			
	//$this->que_bda="SELECT * FROM detalle_recibo WHERE 1=1";
		 $this->que_bda="SELECT 
    			d.id_detalle_recibo, 
    			d.fecha_det_recibo,
    			d.cod_factura,
    			cA.nombre AS nombre_impuesto_A,
    			monto_impuesto_A,
    			cB.nombre AS nombre_impuesto_B,
    			monto_impuesto_B,
    			cC.nombre AS nombre_impuesto_C,
    			monto_impuesto_C,
    			cD.nombre AS nombre_impuesto_D,
    			monto_impuesto_D,
    			cE.nombre AS nombre_impuesto_E,
    			monto_impuesto_E,
   			 	cF.nombre AS nombre_impuesto_F,
   			 	monto_impuesto_F
				FROM 
    					detalle_recibo d
				INNER JOIN 
    				clasificador cA ON d.impuesto_A = cA.id_clasificador
				LEFT JOIN 
    				clasificador cB ON d.impuesto_B = cB.id_clasificador
				LEFT JOIN 
    				clasificador cC ON d.impuesto_C = cC.id_clasificador
				LEFT JOIN 
    				clasificador cD ON d.impuesto_D = cD.id_clasificador
				LEFT JOIN 
    				clasificador cE ON d.impuesto_E = cE.id_clasificador
				lEFT JOIN 
    				clasificador cF ON d.impuesto_F = cF.id_clasificador

					 where fecha_det_recibo='$this->fecha_det_recibo';";


	return $this->ejecutar();
	}

	}

/* 
*/