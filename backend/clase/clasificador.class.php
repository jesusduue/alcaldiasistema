<?php
	require_once('utilidad.php');
	class clasificador extends utilidad
	{
		public $id_clasificador;
		public $nombre;

	public function insertar()
		{
			$this->que_bda="INSERT INTO clasificador(
				nombre) VALUES ('$this->nombre');";
			return $this->ejecutar();
		}	

	public function listar()
		{
			$this->que_bda="SELECT * FROM clasificador WHERE 1=1";
			return $this->ejecutar();
		}	
	}

  ?>