<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="logo.png" type="image/ x-icon">
    <title>Green Energy Solutions</title>
</head>

<body>
    <div class="login-container">
        <header class="login-header">
            <h1>Bienvenido</h1>
            <?php include('controlador.php') ?>
        </header>
        <form action="controlador.php" class="login-form" method="post">
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
    </div>
</body>

</html>