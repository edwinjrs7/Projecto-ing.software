<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Green Energy Solutions - Dashboard</title>
    <?php 
        require_once '../bdd/conexion.php';
        include '../metodos/ManejoBDD.php';
        include '../metodos/factory/CalcularConsumos.php';
        include '../metodos/CrudEmpresas.php';
        session_start();
        if(!isset($_SESSION['id'])){
            header("Location: ../Ingresos_usuarios/login.php");
            exit();
        }

        $nombre_usuario =  htmlspecialchars($_SESSION['firstname']);
        $userId = $_SESSION['id'];
        

        //instancia de otras clases.
        $factory = new CalcularConsumosFactory();
        $operacionesSQL = new operacionesSQL($conn);
        $analizador = new AnalizadorConsumo($factory,$operacionesSQL);
        $empresas = new Empresas($conn);

        $resultados = $operacionesSQL->obtenerConsumosMensuales($userId);
        $analisis = $analizador->analizarConsumo($userId);
        $promedios = $analizador->CalcularPromedio($userId);
        $selecciones = $empresas->ObtenerSelecciones($userId);

        $labels = [];
        $consumos = [];
        $costos = [];
        $diferencia=[];
        $porcentual=[];
        $promedio=[];

        if (!$resultados['error'] && !empty($resultados['resultados'])) {
            foreach ($resultados['resultados'] as $resultado) {
                $labels[] = $resultado['periodo'];
                $consumos[] = $resultado['consumo'];
                $costos[] = $resultado['costo'];
            }
        } else {
            // Asigna valores predeterminados
            $labels = $consumos = $costos = [];
        }
        
        if (isset($analisis['error']) && !$analisis['error'] && isset($analisis['resultados'])) {
            foreach ($analisis['resultados'] as $porcentaje) {
                $diferencia[] = $porcentaje['diferencia'];
                $porcentual[] = $porcentaje['porcentaje'];
            }
        } else {
            // Valores por defecto si no hay datos
            $diferencia = [];
            $porcentual = [];
        }
        if (!$promedios['error'] && isset($promedios['resultados'])) {
            // Asegúrate de que 'resultados' es un array antes de acceder a sus elementos.
            $promedioCosto = isset($promedios['resultados']['promedioCosto']) ? $promedios['resultados']['promedioCosto'] : 0;
            $promedioConsumo = isset($promedios['resultados']['promedioConsumo']) ? $promedios['resultados']['promedioConsumo'] : 0;
        } else {
            // Si hay un error o 'resultados' no está definido
            $promedioCosto = 0;
            $promedioConsumo = 0;
        }
        $resumen = $resultados['resumen'] ?? null;
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

                    <span class="title">
                        <?php echo $nombre_usuario?>
                    </span><br>



                </a>
            </li>


            <li class="active">
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
                <a href="">
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
                <a href="">
                    <span class="icon">
                        <i class='bx bxs-home'></i>
                    </span>
                    <span class="text">Pagina de Inicio</span>
                </a>
            </li>
            <li>
                <a href="" class="logout">
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
            <div class="head-tittle">
                <div class="left">
                    <h1>Dashboard</h1>
                </div>
            </div>
            <div class="card-grid">
                <div class="card">
                    <h3 class="card-title"><i class='bx bxs-bolt'></i>Ultimo Consumo:</h3>
                    <?php if (!empty($consumos)): ?>
                    <p class="card-value">
                        <?php echo htmlspecialchars($resultado['consumo']);?>kWh
                    </p>
                    <?php else: ?>
                    <p class="card-value">0kWh</p>
                    <?php endif; ?>
                    <?php
                 
                 if(count($porcentual) >= 1){
                    $porcentajeActual = $porcentual[count($porcentual) - 1];
                    $porcentajeAnterior = count($porcentual) > 1 ? $porcentual[count($porcentual) - 2] : $porcentajeActual;
                
                    $flecha = '';
                    if($porcentajeActual > $porcentajeAnterior){
                        $flecha = '<i class="bx bxs-up-arrow" style="color: red;"></i>';
                    } elseif ($porcentajeActual < $porcentajeAnterior){
                        $flecha = '<i class="bx bxs-down-arrow" style="color: green;"></i>';
                    } else {
                        $flecha = '<i class="bx bxs-right-arrow"></i>';
                    }
                
                    echo '<p class="card-description">' . $flecha . htmlspecialchars($porcentajeActual) . '% ' . '</p>';
                } 

             ?>
                </div>
                <div class="card">
                    <h3 class="card-title"><i class='bx bxs-pie-chart-alt-2' ></i>Consumo Promedio:</h3>
                    <p class="card-value"><?php echo $promedioCosto ?>Kwh</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><i class='bx bx-dollar'></i>Tu Ultimo Pago:</h3>
                    <p class="card-value">$<?php echo htmlspecialchars($resultado['costo']); ?> pesos.</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><i class='bx bxs-briefcase-alt-2'></i>Proveedores:</h3>
                    <p class="card-value">2</p>
                </div>
            </div>

            <div class="chart-placeholder">
                <h2>Consumo Energerico Mensual
                    <p class = "card-description">
                        <?php echo $flecha. htmlspecialchars($porcentaje['porcentaje'])?>%
                         Desde el ultimo registro.
                    </p>
                    <?php if (!empty($consumos)):?>
                        <div class="chart-area"></div>
                    <?php else: ?>
                        <p>No Hay datos suficientes para mostrar en la grafica.</p>
                    <?php endif ?>
                </h2>
            </div>
            <div class="companies-table">
                <h2>Tus Empresas seleccionandas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!$selecciones['error'] && !empty($selecciones)){
                            foreach($selecciones['resultados'] as $seleccion){
                                ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($seleccion['logo']);?>" alt="Logo de empresa" width="50" height="50"></td>
                                    <td><?php echo htmlspecialchars($seleccion['nombre'])?></td>
                                    <td><?php echo htmlspecialchars($seleccion['tipo'])?></td>
                                    <td></td>
                                </tr>
                                <?php
                            }
                        }else{
                            echo '<p>No has seleccionando ninguna empresa aun</p>';
                        } 
                        ?>
                        
                    </tbody>
                </table>
            </div>
        </main>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const options = {
            series: [{
                name: 'Consumo',
                data: <?php echo json_encode($consumos); ?>
            }],
            chart: {
                type: 'area',
                height: 350
            },
            xaxis: {
                categories: <?php echo json_encode($labels); ?>
            },
        yaxis: {
            title: {
                text: 'Consumo (kWh)'
            }
        }
        };

        const chart = new ApexCharts(document.querySelector('.chart-area'), options);
        chart.render();
    </script>
    <script src="scripts.js"></script>
</body>

</html>