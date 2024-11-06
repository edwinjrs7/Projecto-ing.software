<?php
    include_once '../bdd/conexion.php';
     
    
    class Empresas{
        private $conexion;
        public function __construct($conn){
           $this->conexion = $conn;
        }

        public function ObtenerEmpresa(){
            $sql = 'SELECT id, nombre,ubicacion,descripcion,sector,logo,calificacion_promedio FROM empresa';
            $stmt = $this->conexion->prepare($sql);
            
            
            if($stmt === false){
                return ['error' => true, 'resultados' => []];
            }
            $stmt->execute();
            $resultado = $stmt->get_result();

            $empresas = [];

            while($row = $resultado->fetch_assoc()){
                $empresas[] = $row;
            }

            return['error'=> false, 'resultados'=> $empresas];
        }

        public function ObtenerSelecciones($userId){
            $sql = 'SELECT em.logo, em.nombre, em.tipo, em.calificacion_promedio FROM empresa em JOIN seleccionarEmpresa se on em.id = se.id_empresa where se.id_usuario = ?;';
            $stmt = $this->conexion->prepare($sql);

            if ($stmt === false){
                return['error'=> true, 'resultados'=> []];
            }
            $stmt->bind_param('i',$userId);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $selecciones = [];

            while($row= $resultado->fetch_assoc()){
                $selecciones[]=$row;
            }

            return['error'=>false, 'resultados'=>$selecciones];

        }
    } 

 
?>