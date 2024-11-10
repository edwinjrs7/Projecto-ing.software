<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $correo = htmlspecialchars($_POST['correo'] ?? '');
    $asunto = htmlspecialchars($_POST['asunto'] ?? '');
    $mensaje = htmlspecialchars($_POST['mensaje'] ?? '');

//validar si se enviaron los valores
if (!empty($nombre) && !empty($correo) && !empty($asunto) && !empty($mensaje){
    //ruta y nombre del archivo
    $ruta_archivo = "contacto.txt";

    //Verificar si el archivo existe
    $archivo_existe = file_exists($ruta_archivo);

    //Obtener el numero de registros o inicializar en 0 si no existe
    $num_registros = 0;
    if ($archivo_existe){
        $lineas = file($ruta_archivo, FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea){
            if (prog_match('/^\d+\./', $linea)){
                $num_registros++;
            }
        }
    }

      // Incrementar el numero de registros para el nuevo registro
      $num_registros++;

    // Incrementar el número de registros para el nuevo registro
		$num_registros++;

		// Abrir el archivo en modo de escritura
		$file = fopen($ruta_archivo, "a");
		if ($file) {
			// Escribir la cabecera si el archivo no existe
			if (!$archivo_existe) {
				fwrite($file, "Id \tNombre Persona \t\tEmail Persona \t\t\tDirección \t\tPais" . PHP_EOL);
				fwrite($file, "=========================================================================" . PHP_EOL);
			}

			// Escribir el nuevo registro en el archivo con el formato requerido
			fwrite($file, "$num_registros.\t$nombre\t\t\t$correo\t\t\t$asunto\t\t$mensaje" . PHP_EOL);
			fwrite($file, " -------------------------------------------------------------------------" . PHP_EOL);

			// Cerrar el archivo
			fclose($file);

			// Redirigir a una página específica
			header('Location: index.html');
			exit();
		} else {
			echo "Error al abrir el archivo.";
		}
	} else {
		echo "Por favor, completa todos los campos del formulario.";
	}
} else {
	echo "Acceso inválido.";
}
?>