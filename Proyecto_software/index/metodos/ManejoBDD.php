<?php

    include_once '../bdd/conexion.php';

    class operacionesSQL{
        public $conexion;

        public function __construct($conn){
            $this->conexion = $conn;
        }

        public function guardarConsumoMensual($userId, $consumo, $costo, $periodo){
            //Primero se Insertan los datos de consumo

            $sql1 = "INSERT INTO consumo (user_id, consumo) VALUES (?,?)";
            $stmt1 = $this->conexion->prepare($sql1);

            if($stmt1 === false){
                return['success' => false, 'error' => $this->conexion->error];
            }

            $stmt1->bind_param("id", $userId, $consumo);
            $stmt1->execute();

            $consumoId = $this->conexion->insert_id;

            $sql2 = "INSERT INTO consumoMensual (id_consumo, costo, periodo) VALUES (?,?,?)";
            $stmt2 = $this->conexion->prepare($sql2);

            if($stmt2 === false){
                return['success' => false, 'error' => $this->conexion->error];
            }

            $stmt2->bind_param("ids", $consumoId, $costo, $periodo);
            $ejecucion_final = $stmt2->execute();

            if(!$ejecucion_final){
                return['success' => false, 'error' => $this->conexion->error];
            }

            return['success' => true];
        }
        
        public function obtenerConsumosMensuales($userId){
            $sql = "SELECT cm.periodo, c.consumo, cm.costo FROM consumoMensual cm JOIN consumo c ON cm.id_consumo = c.id WHERE c.user_id = ? ORDER BY cm.periodo";
            $stmt = $this->conexion->prepare($sql);
             
            if($stmt === false){
                return['error'=> true, 'mensaje' => $this->conexion->error, 'resultado'=>[]];
            }
            

            $stmt->bind_param("i",$userId);
            $stmt->execute();

            if(!$stmt->execute()){
                return['error'=> true, 'mensaje'=> $this->conexion->error, 'resultado'=>[]];
            }

            $result = $stmt->get_result();
            $resultado = $result->fetch_all(MYSQLI_ASSOC);

            return ['error' => false, 'resultados' => $resultado];
        }

        public function actualizarConsumoAnual($userId,$año){
            $sql = "SELECT SUM(c.consumo) as total_consumido, SUM(cm.costo) as total_pagado FROM consumo c JOIN consumoMensual cm ON c.id = cm.id_consumo WHERE c.user_id = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $totales = $stmt->get_result()->fetch_assoc();

            $sql2 = "INSERT INTO consumoAnual (id_consumo, total_pagado, total_consumido, año)
            SELECT id, ?, ?, ?
            FROM consumo
            WHERE user_id = ?
            ORDER BY id DESC LIMIT 1
            ON DUPLICATE KEY UPDATE
            total_pagado = VALUES(total_pagado),
            total_consumido = VALUES(total_consumido)";
            
            $stmt2 = $this->conexion->prepare($sql2);
            $stmt2->bind_param("ddii", 
            $totales['total_pagado'], 
            $totales['total_consumido'],
            $año,
            $userId
            );

            return $stmt2->execute();
        }

        public function __destruct(){
            if($this->conexion){
                $this->conexion->close();
            }
        }
    }
    
    class AnalizadorConsumo{
        private $factory, $db;

        public function __construct(CalcularConsumosFactory $factory, operacionesSQL $db){
            $this->factory = $factory;
            $this->db = $db;
            
        }

        public function AnalizarConsumo($userId){
            $resultados = [];
            $consumos = $this->db->obtenerConsumosMensuales($userId);
            if($consumos['error']){
                return ['error'=> true, 'resultados'=> $resultados];
            }

            $datosConsumo = $consumos['resultados'];

            if(count($datosConsumo) < 2){
                return ['error'=> true, 'resultados'=> $resultados];
            }
            
            for ($i=0;$i < count($datosConsumo)-1;$i++){
                $consumoActual = $datosConsumo[$i]['consumo'];
                $consumoAnterior = $datosConsumo[$i+1]['consumo'];
                $periodo = $datosConsumo[$i]['periodo'];

                $diferencia = $this->factory->crearCalculo('diferencia');
                $porcentaje = $this->factory->crearCalculo('porcentaje');

                $diferencia = $diferencia->calcular($consumoActual,$consumoAnterior);
                $porcentaje = $porcentaje->calcular($diferencia, $consumoAnterior);

                $resultados[] = [
                    'periodo' => $periodo,
                    'consumo'=> $consumoActual,
                    'costo'=> $datosConsumo[$i]['costo'],
                    'diferencia' => $diferencia,
                    'porcentaje' => $porcentaje
                ];
            }

            return ['error'=> false, 'resultados'=>$resultados];

        }
        public function CalcularPromedio($userId){
            $totalConsumo = 0;
            $totalCosto = 0;
            $resultado = [];

            $consumos = $this->db->obtenerConsumosMensuales($userId);

            if($consumos['error']){
                return ['error'=> true, 'resultados'=> $resultado];
            }

            $datosConsumo = $consumos['resultados'];
            if (empty($datosConsumo)) {
                return ['error' => true, 'resultados' => $resultado];
            }
            

            foreach($datosConsumo as $datos){
                $totalConsumo += $datos['consumo'];
                $totalCosto += $datos['costo'];
            }

            $promedioConsumo = $totalConsumo / count($datosConsumo);
            $promedioCosto = $totalCosto / count($datosConsumo);

            $resultados = [
                'promedioConsumo' => $promedioConsumo,
                'promedioCosto' => $promedioCosto
            ];

            return['error' => false, 'resultados' => $resultados];
        }
    }
   
    
?>