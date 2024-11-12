<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="logo.png" type="image/ x-icon">
    <title>Green Energy Solutions</title>
    <style>
          .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        /* Estilos para el contenido del modal */
        .notification-box {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            position: relative;
        }
        .notification-icon {
            font-size: 48px;
            margin-bottom: 20px;
            color: #f44336;
        }
        .notification-message h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .close-button {
            background-color: #f44336;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-size: 16px;
            margin-top: 20px;
        }
        .close-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<?php

   require '../bdd/conexion.php';
   
   $mensaje = '';
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
           $mensaje = "La contraseña es incorrecta";
        }
    } else {

        $mensaje="<h3 class='mensaje'>Este usuario no existe en la base de datos</h3>";
    }

    $stmt->close();
}?>
<body>
    <div class="login-container">
        <header class="login-header">
            <h1>Bienvenido</h1>
            
        </header>
        <form action="" class="login-form" method="post">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" name="username" id="username">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="form-group">
                <label for="usertype">Escoje el tipo de Usuario</label>
                <select name="usertype" id="usertype">
                    <option value="usuario">Usuario</option>
                    <option value="empresario">Empresario</option>
                </select>
            </div>
            <button type="submit" class="submit-button">Ingresar</button>
        </form>
        <p class="register-link">No tienes cuenta ?<a href="registro.html">Registrate</a></p>
        <?php if ($mensaje): ?>
            <div class="modal-overlay" id="modal">
                <div class='notification-box'>
                    <div class='notification-icon' role='img' aria-label='Icono de error'>&#x26A0;</div>
                        <div class='notification-message'>
                        <h2><?php echo $mensaje; ?></h2>
                        </div>
                        <button class='close-button' onclick='closeModal()'>Cerrar</button>
                    </div>
                </div> 
            </div>
               
        <?php endif ?>    
    </div>
    <script>
        function closeNotification() {
            document.querySelector('.notification-box').style.display = 'none';
        }

        function tryAgain() {
            document.querySelector('#password').value = '';
            document.querySelector('#password').focus();
        }
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
        </script>
</body>

</html>