<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Energy Solutions - Estadisticas</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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

        $factory = new CalcularConsumosFactory();
        $operacionesSQL = new operacionesSQL($conn);
        $analizador = new AnalizadorConsumo($factory,$operacionesSQL); 

        $resultados = $operacionesSQL->obtenerConsumosMensuales($userId);
        $totales = $operacionesSQL->obtenerConsumoAnual($userId);
        $analisis = $analizador->analizarConsumo($userId);
        $promedios = $analizador->CalcularPromedio($userId);

        $labels = [];
        $consumos = [];
        $costos = [];
        $totalconsumido= [];
        $totalpagado = [];

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

        if(!$totales['error'] && !empty($totales['resultados'])){
            $totalconsumido = isset($totales['resultados']['total_consumido']) ? $totales['resultados']['total_consumido'] :0;
            $totalpagado = isset($totales['resultados']['total_pagado']) ? $totales['resultados']['total_pagado'] : 0;
        }else{
            // Asigna valores predeterminados
            $totalconsumido = 0;
            $totalpagado = 0;
        }
       
        $diferencia=[];
        $porcentual=[];
        $promedio=[];

        
        
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
            <li class="active">
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
            <div class="toggle"><i class="bx bx-menu"></i></div>
        </nav>
        <main class="main">
            <div class="head-title">
                <div class="left">
                    <h1>Estadisticas</h1>
                </div>
            </div>
            <div class="card-grid">
                <div class="card">
                    <h3 class="card-title"><i class='bx bx-dollar'></i>Pago acumulado:</h3>
                    <p class="card-value">$<?php echo number_format($totalpagado);?> pesos</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><i class='bx bxs-bolt'></i>Consumo Acumulado:</h3>
                    <p class="card-value"><?php echo number_format($totalconsumido);?>Kwh</p>
                </div>
                <div class="card">
                    <h3 class="card-title"><i class='bx bxs-bolt'></i>Ultimo Consumo:</h3>
                    <?php if (!empty($consumos)): ?>
                    <p class="card-value">
                        <?php echo htmlspecialchars($resultado['consumo']);?>kWh
                    </p>
                    <?php else: ?>
                    <p class="card-value">0kWh</p>
                    <?php endif ?>
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
                } else{
                    echo '<p class="card-description">0%</p>';
                }

                ?>
                </div>
                
            </div>
            <div class="card-grid">
                <div class="chart-placeholder">
                    <h4>Relacion costo consumo</h4>
                    <div class="chart-area"></div>
                </div>
                <div class="chart-placeholder">
                    <h4>Pagos Mensuales</h4>
                    <div class="chart-area" id="chart-area-2"></div>
                </div>
               
            </div>
            <div class="chart-placeholder">
                <h2>Consumo Energetico Mensual </h2>
                <?php if(!empty($porcentual)):?>
                    <p class = "card-description">
                        <?php echo $flecha. htmlspecialchars($porcentaje['porcentaje'])?>%
                         Desde el ultimo registro.
                    </p>
                <?php else: ?>
                    <p class = "card-description"></p>
                <?php endif ?>
                <?php if (!empty($consumos)):?>
                        <div class="chart-area" id="chart-area-3"></div>
                <?php else: ?>
                        <p>No Hay datos suficientes para mostrar en la grafica.</p>
                <?php endif ?>
            </div>
        </main>
    </section>  
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
          const options = {
            series: [
                {
                    name: 'Consumo (kWh)',
                    data: <?php echo json_encode($consumos); ?>,
                },
                {
                    name: 'Costo (USD)',
                    data: <?php echo json_encode($costos); ?>,
                }
            ],
            chart: {
                type: 'line',
                height: 250
            },
            xaxis: {
                categories: <?php echo json_encode($labels); ?>
            },
            yaxis: [
                {
                    title: {
                        text: 'Consumo (kWh)'
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Costo (COP)'
                    }
                }
            ],
            colors: ['#00BFFF', '#FF6347'],
            stroke: {
                width: 2,
                curve: 'smooth'
            },
            
            
            legend: {
                position: 'top'
            }
        };

        const chart = new ApexCharts(document.querySelector('.chart-area'), options);
        chart.render();
        
        
            const options2={
            series: [{
                name: 'Costo (USD)',
                data: <?php echo json_encode($costos); ?>
            }],
            chart: {
                type: 'bar',
                height: 250
            },
            xaxis: {
                categories: <?php echo json_encode($labels); ?>,
                title: {
                    text: 'Meses'
                }
            },
            yaxis: {
                title: {
                    text: 'Costo (COP)'
                }
            },
            
            colors: ['#22c55e'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return "$" + val;
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$" + val;
                    }
                }
            }
        };


        const chart2 = new ApexCharts(document.querySelector('#chart-area-2'), options2);
        chart2.render();
        
        const options3 = {
            series: [{
                name: 'Consumo',
                data: <?php echo json_encode($consumos); ?>
            }],
            chart: {
                type: 'line',
                height: 250
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

        const chart3 = new ApexCharts(document.querySelector('#chart-area-3'), options3);
        chart3.render();
        
    </script>   
    <script src="scripts.js"></script>   
</body>
</html>