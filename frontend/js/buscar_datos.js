$(buscar_datos());
//funcion ajax para buscar datos en tiempo real

	function buscar_datos (consulta){

		$.ajax({
			url:'../vista/busqueda_TR.php',
			type: 'POST',
			dataType: 'html',
			data:{consulta:consulta},
		})
		.done(function(respuesta){

			$("#datos").html(respuesta);
		})
		.fail(function(){
			console.log("error");
		}) 
	}

	$(document).on('keyup','#caja_busqueda', function(){

			var valor = $(this).val();
			if(valor != ""){
				buscar_datos(valor);
			}
			else{
				buscar_datos();


			}
	});