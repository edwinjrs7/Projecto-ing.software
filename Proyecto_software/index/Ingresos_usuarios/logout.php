<?php
    
    session_start();
    $_SESSION = [];
    session_unset();
    session_destroy();

// Redirige al usuario a la página de inicio de sesión
   header("Location: ../index.html");
   exit();
   
?>
