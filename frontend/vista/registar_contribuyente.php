<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>REGISTAR CONTRIBUYENTE</title>
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
	<style type="text/css">
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    height: 100vh;
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0px 0px 5px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 500px;
}



input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background-color: #43a1a1;
    color: black;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    width: 100%;
    text-transform: uppercase;
}

button:hover {
    background-color: #004647;
	color:white ;}
.dib {

			max-width: 400px;

			margin: 100px auto;

			padding: 20px;

			border: 1px solid #cccccc;

			border-radius: 5px;

			background-color: #ffffff;

		}
/*.....................................*/
#navbar {
  overflow: hidden;
  background-color:#6e131c;


}

#navbar a {
  float: left;
  display: flex;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
  font-weight: bold;

}

#navbar a:hover {
  background: #6e272e;
  color: white;

 

 
}
 .busqueda {
    
    width: 250px; 
    display: inline-flex;
    justify-content: space-between;
   margin-left: 600px;
   margin-top: 10px;


 }

h1{
    text-align: center;
}

	</style>
</head>
<body>
	
<div id="navbar">
  <a class="navbar-brand" href="#">
    <img src="../logo.png" alt="Logo" style="width:40px;">
  </a>

  <a href="index.html">Inicio</a>
  <a href="relacion_diaria.php">Diarios</a>
  <a href="registar_contribuyente.php">Registrar Contribuyente</a> 
  <a href="registar_clasificador.php">Registrar Clasificador</a>
</div>

			<div class="dib">
			<form action="../../backend/controlador/contribuyente.php">
				<div>
					<span>CEDULA/RIF</span>
					<input type="text" name="cedula_rif" required>
				</div>
				<div>
					<span>RAZON SOCIAL</span>
					<input type="text" name="razon_social" required>
				</div>
				<div>
					<span>ESTADO</span>
					<Select name="estado_cont">
						<option value="x">---</option>
						<option value="Activo">Activo</option>
						<option value="Inactivo">Inactivo</option>
					</Select>
				</div>

				<div>
					<button type="submit" value="insertar" name="accion"> guardar</button>
				</div>

		    </form></div>
</body>
</html>