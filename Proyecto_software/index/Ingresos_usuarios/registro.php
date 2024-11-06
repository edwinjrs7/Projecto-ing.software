<?php

require '../bdd/conexion.php';

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"]=='POST'){
    $name = $_POST['name'];
    $lastname= $_POST['lastname'];
    $email= $_POST['email'];
    $username=$_POST['username'];
    $password=$_POST['password'];
    $password2=$_POST['password2'];
    $user_type=$_POST['usertype'];

    if($password !== $password2){
        echo "Las contraseñas no coinciden";
    }else{
        $hashed_password = password_hash($password,PASSWORD_DEFAULT);

        $sql= "INSERT INTO usuario (username,firstname,lastname, email, contraseña, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $name, $lastname, $email,$hashed_password,$user_type);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Registro exitoso. Puedes iniciar sesión ahora.";
            // Redirigir a la página de inicio de sesión
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Cerrar la conexión
    $stmt->close();
    

}

?>