<?php

   require '../bdd/conexion.php';
   
   
   session_start();

   if ($_POST) {
    $usuario = $_POST['username'];
    $contrasena = $_POST['password'];

    // Consulta para obtener los datos de usuario
    $sql = "SELECT id, firstname, tipo_usuario, contraseña FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Verificamos que la consulta se haya preparado correctamente
    if ($stmt === false) {
        die('Error en la consulta: ' . htmlspecialchars($conn->error));
    }

    // Enlaza el parámetro
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $tipo_usuario, $hashed_password);

    // Verificar si se encontraron resultados
    if ($stmt->fetch()) {
        // Verifica la contraseña
        if (password_verify($contrasena, $hashed_password)) {
            // Asigna los datos de sesión si el login es exitoso
            $_SESSION['id'] = $id;
            $_SESSION['firstname'] = $nombre;
            $_SESSION['tipo_usuario'] = $tipo_usuario;

            header("Location: ../Dashboard/Dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "El usuario no existe";
    }

    $stmt->close();
}



?>