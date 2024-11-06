<?php
   
   require_once '../bdd/conexion.php';
   session_start();

   if(!isset($_SESSION['id'])){
      header("Location: ../Ingreso_usuarios/login.php");
      exit();
   }

   $usuario_id = $_SESSION['id'];
   $empresa_id = isset($_POST['empresa_id']) ? $_POST['empresa_id'] : null;

   if($empresa_id){
      $sql = "INSERT into seleccionarEmpresa(id_usuario, id_empresa) values (?,?);";
      $stmt = $conn->prepare($sql);

      if($stmt){
        $stmt->bind_param("ii", $usuario_id, $empresa_id);
        if($stmt->execute()){
            header("Location: Dashboard.php");
            exit();
        }else{
            echo "Error al seleccionar la empresa" .$stmt->error;
        }
      }else{
        echo "Error al preparar la consulta" .$conn->error;
      }
   }


?>