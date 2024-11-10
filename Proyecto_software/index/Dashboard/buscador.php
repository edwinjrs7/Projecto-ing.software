<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="buscador.css">
    <title>Green Energy Solutions - Buscador</title>
    <?php
       require_once '../bdd/conexion.php';
       require_once '../metodos/CrudEmpresas.php';
       session_start();
       if(!isset($_SESSION['id'])){
           header("Location: ../Ingresos_usuarios/login.php");
           exit();
       }

       $nombre_usuario =  htmlspecialchars($_SESSION['firstname']);
       $userId = $_SESSION['id'];

       $empresa = new Empresas($conn);
       $empresas = $empresa->obtenerEmpresa();

       
    ?>
</head>
<body>
    <aside id="sidebar">
        <ul class="side-menu top">
            <li>

                <a href="" class="brand">
                    <span class="icon">
                        <img src="../images/logo.png" alt="" class="logo" width="90" height="90">
                    </span>

                    <span class="title"><?php echo $nombre_usuario?></span><br>
                    


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
            <li class="active">
                <a href="">
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
            <li class="">
                <a href="registrar_informes.php">
                    <span class="icon">
                        <i class='bx bxs-file-plus'></i>
                    </span>
                    <span class="text">Ingresar Consumos</span>
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
        <div class="toggle">
            <i class="bx bx-menu"></i>
        </div>
        <form method="GET">
            <div class="form-input">
                <input type="search" id="search" placeholder="Search..." name="buscar" />
                <button type="submit">Search</button>
            </div>
        </form>
    </nav>
    <main class="main">
        <div class="head-title">
            <div class="left">
                <h1>Selecciona Una Empresa.</h1>
            </div>
        </div>
        <div class="empresa-list">
            <div class="empresa-cards">
                <?php if(!$empresas['error']){
                    foreach($empresas['resultados'] as $empresa){
                ?>
                <div class="empresa-card">
                    <div class="header">
                        <img src="<?php echo htmlspecialchars($empresa['logo'])?>" alt="" class="logo">
                        <div>
                            <h3><?php echo htmlspecialchars($empresa['nombre'])?></h3>
                        </div>
                    </div>
                    <p class="description"><?php echo htmlspecialchars($empresa['descripcion'])?></p>
                    <p class="location"><i class='bx bx-map'></i><?php echo htmlspecialchars($empresa['ubicacion'])?></p>
                    <div class="rating">
                            <div class="stars" aria-label="Rating: 4.6 out of 5 stars">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star-half'></i>
                            </div>
                            <span class="score">4.6</span>
                    </div>
                    <div class="buttons">
                        <button class="button button-outline">Comparar</button>
                        <form action="seleccion.php" method="post">
                            <input type="hidden" name="empresa_id" value="<?php echo $empresa['id']; ?>">
                            <button class="button button-filled">Seleccionar</button>
                        </form>
                    </div>
                </div>
                <?php }
                }else{
                   echo ' <p>No hay empresas registradas.</p>';
                } 
                ?>
            </div>
        </div>

    </main>
    </section>
    <script src="scripts.js"></script>
</body>
</html>