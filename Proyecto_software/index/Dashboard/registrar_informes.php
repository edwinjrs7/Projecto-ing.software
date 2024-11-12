<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="registrar.css">
    <title></title>
    <?php
    require_once '../bdd/conexion.php';
    require_once '../metodos/ManejoBDD.php';
    
    session_start();
    if(!isset($_SESSION['id'])){
     header('Location: ../Ingresos_usuarios/login.php');
     exit();
    }

    $nombre_usuario =  htmlspecialchars($_SESSION['firstname']);

    $userId = $_SESSION['id'];
    $mensaje = '';
    $tipo_mensaje = '';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
         $operacionesSQL = new operacionesSQL($conn);

         $consumo = filter_input(INPUT_POST, 'consumo', FILTER_VALIDATE_FLOAT);
         $valorPagado = filter_input(INPUT_POST, 'valorpagado', FILTER_VALIDATE_FLOAT);
         $periodo = filter_input(INPUT_POST, 'periodo', FILTER_SANITIZE_STRING);

         if($consumo !== false && $valorPagado !== false && !empty($periodo)){
             // Formatear el periodo (asumiendo que se ingresa como "YYYY-MM" o similar)

             $periodo = date('Y-m', strtotime($periodo . '-01'));

             //se guarda el consumo

             $resultado = $operacionesSQL->guardarConsumoMensual($userId, $consumo, $valorPagado, $periodo);
             if($resultado === false){
                 $mensaje = 'Error al guardar el consumo mensual';
             }else{

                 if($resultado['success']){
                     $año = date('Y', strtotime($periodo));
                     $operacionesSQL->actualizarConsumoAnual($userId, $año);
                     
                     $mensaje = "Consumo registrado correctamente";
                     $tipo_mensaje = "success";
 
                 }else{
                     $mensaje = "Error al Registrar el consumo: ".$resultado['error'];
                     $tipo_mensaje = 'error';
                     
                 }
             }
         }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $operacionesSQL = new operacionesSQL($conn);
        $consumo_id = filter_input(INPUT_POST, 'consumo_id', FILTER_VALIDATE_INT);

        if ($consumo_id !== false) {
            $eliminar_resultado = $operacionesSQL->EliminarConsumoMensual($userId,$consumo_id);
            if ($eliminar_resultado) {
                $mensaje = "Consumo eliminado correctamente";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al eliminar el consumo";
                $tipo_mensaje = "error";
            }
        }
    }

    $consumos = new operacionesSQL($conn);
    $resultados = $consumos->obtenerConsumosMensuales($userId);
 ?>
</head>

<body>
    <aside id="sidebar" class="navigation">
        <ul class="side-menu top">
            <li>

                <a href="" class="brand">
                    <span class="icon">
                        <img src="../images/logo.png" alt="" class="logo" width="90" height="90">
                    </span>

                    <span class="title">
                        <?php echo $nombre_usuario?>
                    </span>


                </a>
            </li>


            <li class="">
                <a href="Dashboard.php">
                    <span class="icon">
                        <i class='bx bxs-dashboard'></i>
                    </span>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="">
                <a href="buscador.php">
                    <span class="icon">
                        <i class='bx bx-search'></i>
                    </span>
                    <span class="text">Buscador</span>
                </a>
            </li>
            <li class="">
                <a href="estadisticas.php">
                    <span class="icon">
                        <i class='bx bxs-doughnut-chart'></i>
                    </span>
                    <span class="text">Analytics</span>
                </a>
            </li>
            <li class="">
                <a href="">
                    <span class="icon">
                        <i class='bx bxs-cog'></i>
                    </span>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li class="active">
                <a href="">
                    <span class="icon">
                        <i class='bx bxs-file-plus'></i>
                    </span>
                    <span class="text">Ingresar Consumos</span>
                </a>
            </li>
            <li>
                <a href="">
                    <span class="icon">
                        <i class='bx bxs-home'></i>
                    </span>
                    <span class="text">Pagina de Inicio</span>
                </a>
            </li>
            <li>
                <a href="../Ingresos_usuarios/logout.php" class="logout">
                    <span class="icon">
                        <i class='bx bxs-log-out'></i>
                    </span>
                    <span class="text">cerrar Sesión</span>
                </a>
            </li>


        </ul>
    </aside>
    <section id="content">
        <nav>
            <div class="toggle"><i class="bx bx-menu"></i></div>
        </nav>
        <main class="main">
            <div class="head-title">
                <div class="left">
                    <h1>Ingresa los consumos</h1>
                </div>
            </div>
            <div class="flex-container">
                <div class="card-register">
                    <div class="card-header">
                        <h2 class="card-title">Registro de consumos</h2>
                        <p class="card-description">Ingresa el ultimo valor de consumo registrado en tu factura</p>
                    </div>
                    <div class="card-content">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="consumo">Consumo:</label>
                                    <input type="number" name="consumo" id="consumo" placeholder="consumo">
                                </div>
                                <div class="form-group">
                                    <label for="valorpagado">Valor a pagar:</label>
                                    <input type="number" name="valorpagado" placeholder="Valor de tu factura">
                                </div>
                                <div class="form-group">
                                    <label for="periodo">Periodo:</label>
                                    <input type="text" name="periodo" id="periodo" placeholder="Mes del consumo">
                                    <span>Por favor ingresar el periodo en formato "YYYY-MM"</span>
                                </div>
                                <div class="form-group"><label for=""></label></div>
                                <div class="form-group"><label for=""></label></div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Ingresar Informe</button>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
    
                <div class="" style="flex: 1;">
                    <h3>Registros de Consumos</h3>
                    <table class="interactive-table">
                        <thead>
                            <tr>
                                <th>Consumo</th>
                                <th>Periodo</th>
                                <th>Costo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="consumoTableBody">
                            
                                        
                                        
                                <?php
                                    if(!$resultados['error']){
                                        foreach ($resultados['resultados'] as $resultado){
                                       ?>
                                          <tr>
                                              <td><?php echo $resultado['consumo'] ?>Kwh</td>
                                              <td><?php echo $resultado['periodo'] ?></td>
                                              <td>$<?php echo $resultado['costo'] ?></td>
                                              <td>
                                                   
                                                    <button class="edit-btn"><i class='bx bx-edit-alt'></i></button>
                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="display:inline;">
                                                        <input type="hidden" name="accion" value="eliminar">
                                                        <input type="hidden" name="consumo_id" value="<?php echo $resultado['id']; ?>">
                                                        <button type="submit" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar este registro?')"><i class='bx bx-trash'></i></button>
                                                    </form>
                                                
                                              </td>
                                          </tr>
                                          <?php
                                        }
                                    } else{
                                        echo "<p>Aun No hay registros de consumo.</p>";
                                    }
                                ?>
                             
                        </tbody>
                    </table>
                </div>  
            </div>
        </main>
    </section>
    <script src="scripts.js"></script>


</body>

</html>