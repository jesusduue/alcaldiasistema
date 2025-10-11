<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>REGISTRAR CLASIFICADOR</title>
	<link rel="stylesheet" href="../bootstrap-5.3.1-dist/css/bootstrap.css">
	<style type="text/css">

		body {

			font-family: Arial, sans-serif;

			margin: 0;
			padding: 0;
			background-color: #f0f0f0;

		}


		.dib {

			max-width: 400px;

			margin: 100px auto;

			padding: 20px;

			border: 1px solid #cccccc;

			border-radius: 5px;

			background-color: #ffffff;

		}


		span {

			display: block;

			font-size: 14px;

			font-weight: bold;

			margin-bottom: 5px;

		}


		input[type="text"] {

			width: 100%;

			padding: 8px;

			margin-bottom: 15px;

			border: 1px solid #cccccc;

			border-radius: 3px;

		}


		button[type="submit"] {

			width: 100%;

			padding: 10px;

			background-color: #43a1a1;

			border: none;

			border-radius: 5px;

			color:black;

			font-size: 16px;

			cursor: pointer;

		}


		button[type="submit"]:hover {

			background-color: #004647;
			color: white;

		}

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
<div class="d-flex align-items-center"> 
    <form action="lista_facturas_fecha.php" class="busqueda"  method="get">
        <input class="form-control me-2" name="fecha" type="date">
        <input class="btn btn-primary " type="submit" value="buscar">
    </form>
</div>
</div>

			<form action="listar_rubros.php" class="busqueda"  method="get">
      <input class="form-control me-2" name="fecha_det_recibo" type="date">
      <input class="btn btn-primary " type="submit" value="buscar">
    </form>
    
	<div class="dib">


		<form id="form-clasificador">
			
			<div>
				<span>AGREGAR CLASIFICADOR</span>
				<input type="text" name="nombre" required>
			</div>
			<div id="mensaje-clasificador" class="alert d-none mt-3" role="alert"></div>
			<div>
				<button type="submit" value="insertar" name="accion">AGREGAR</button>
			</div>

		</form>


	</div>

<script src="../js/apiClient.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', () => {
		const formulario = document.getElementById('form-clasificador');
		const mensaje = document.getElementById('mensaje-clasificador');

		function mostrarMensaje(tipo, texto) {
			mensaje.className = `alert alert-${tipo} mt-3`;
			mensaje.textContent = texto;
			mensaje.classList.remove('d-none');
		}

		function limpiarMensaje() {
			mensaje.classList.add('d-none');
			mensaje.textContent = '';
		}

		formulario.addEventListener('submit', async (evento) => {
			evento.preventDefault();
			limpiarMensaje();
			const formData = new FormData(formulario);
			const payload = Object.fromEntries(formData.entries());
			try {
				const respuesta = await apiRequest('clasificadores', 'store', {
					method: 'POST',
					body: payload,
				});
				formulario.reset();
				mostrarMensaje('success', respuesta?.message || 'Clasificador registrado correctamente.');
			} catch (error) {
				mostrarMensaje('danger', error.message || 'No fue posible registrar el clasificador.');
			}
		});
	});
</script>
</body>
</html>
