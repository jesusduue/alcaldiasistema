<?php
	
	require_once('utilidad.php');
	class contribuyente extends utilidad
	{
		public $id_contribuyente;
		public $cedula_rif;
		public $razon_social;
		public $estado_cont;

	public function insertar()
		{
			$this->que_bda="INSERT INTO contribuyente(
				
				cedula_rif,
				razon_social,
				estado_cont)
				VALUES('$this->cedula_rif','$this->razon_social','$this->estado_cont');";
				return $this->ejecutar();
		}	

		public function actualizar()
		{
			$this->que_bda="UPDATE	contribuyente SET 
			id_contribuyente='$this->id_contribuyente',
			cedula_rif='$this->cedula_rif',
			razon_social='$this->razon_social',
			estado_cont='$this->estado_cont' WHERE id_contribuyente='$this->id_contribuyente';";
			return $this->ejecutar();
			}

	public function listar()
		{
			$this->que_bda="SELECT * FROM contribuyente WHERE 1=1";
			return $this->ejecutar();
		}		

		 public function filtrar()
		 {
				$filtro1=($this->id_contribuyente!="")?" and id_contribuyente='$this->id_contribuyente'":"";
				//Los % son para que se busque todo con las iniciales de los nombres.
				$filtro2=($this->cedula_rif!="")?" and cedula_rif like '%$this->cedula_rif%'":"";

				$filtro3=($this->razon_social!="")?" and razon_social like '%$this->razon_social%'":"";

				$filtro4=($this->estado_cont!="")?" and estado_cont like '%$this->estado_cont%'":"";

				 $this->que_bda="SELECT * from contribuyente WHERE 1=1 $filtro1 $filtro2 $filtro3 $filtro4;";

				return $this->ejecutar();
		 	}
	}

/*  */

 ?>