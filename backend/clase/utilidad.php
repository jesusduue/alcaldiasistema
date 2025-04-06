<?php

  class utilidad
  {

        private $nom_ser;  //-Nombre del Servidor.
        private $usu_ser;  //-Usuario del Servidor
        private $cla_ser;  // -clave del servidor.
        private $nom_bda;  //-Nombre de la base de datos.
        private $con_bda;  //-Conexion de la base de Datos.
        public  $que_bda;   //-El query que quiero ejecutar
        public  $resultado; //-Mensaje del usuario en el controlador
        public  $puntero;   //-Para apuntar a una fila luego de un select

        function __construct() 
        {
           $this->nom_ser="localhost";
           $this->usu_ser="root";
           $this->cla_ser="";
           $this->nom_bda="sist_alcaldia";
           $this->conectar();
         }

         public function conectar()
         {
          $this->con_bda=new mysqli($this->nom_ser,$this->usu_ser, $this->cla_ser, $this->nom_bda);
         }

         public function ejecutar()
         {
          
         $this->que_bda;
             return $this->con_bda->query($this->que_bda);

         }

         public function mensaje() 
         {
          
          if ($this->resultado==true)
          {
            echo "<script languaje='javascript'>
            alert('PROCESADO CORRECTAMENTE');
            document.location='../../frontend/vista/registar_contribuyente.php';
            </script>";

          }

          else
          {

            echo "Hubo un error. Favor intentelo nuevamente";
                    }
         }

       public function mensajeCLAS() 
         {
          
          if ($this->resultado==true)
          {
            echo "<script languaje='javascript'>
            alert('PROCESADO CORRECTAMENTE');
            document.location='../../frontend/vista/registar_clasificador.php';
            </script>";

          }

          else
          {

            echo "Hubo un error. Favor intentelo nuevamente";
                    }
         }

          public function mensajeliminar() 
         {
          
          if ($this->resultado==true)
          {
            echo "<script languaje='javascript'>
            alert('PROCESADO CORRECTAMENTE');
            document.location='../../frontend/vista/';
            </script>";

          }

          else
          {

            echo "Hubo un error. Favor intentelo nuevamente";
                    }
         }

#-------------------------------------------------#
#FUNCION PARA RECORRER EN LA BD Y EXTRAER LOS DATOS PARA MOSTRAR EN PANTALLA
      public function extraer_dato()
    {
      return $this->puntero->fetch_assoc(); //FETCH_ASSOC ES PARA RECORRER EN LA TABLA DE LA BD
    }
#----------------------------------------#


#-------------FUNCION POLIMORFICA-----------#
          public function asignar_valor()
          {

            foreach ($_REQUEST as $atributo => $valor)
            {
     @         $this->$atributo=$valor;
            }
          
         }#fin de funcion






  }


?>