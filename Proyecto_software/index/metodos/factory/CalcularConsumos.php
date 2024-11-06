<?php
    
    abstract class calcularConsumo{
        public $consumoActual, $consumoAnterior;

        public abstract function calcular($consumoActual,$consumoAnterior);
    }

    class difernciaConsumo extends calcularConsumo{

        public function calcular($consumoActual, $consumoAnterior){
            return $consumoActual - $consumoAnterior;
        }
    }

    class PorcentajeVariacion extends calcularConsumo{
       

        public function calcular($consumoActual,$consumoAnterior){
            return round((($consumoActual - $consumoAnterior)/$consumoAnterior)*100,2);
        }
    }

    interface calcularFactoryMethod{
        public function crearCalculo($calculo);
    }
    
    class CalcularConsumosFactory implements calcularFactoryMethod{
        public function crearCalculo($calculo){
            switch($calculo) {
                case 'diferencia':
                    return new difernciaConsumo();
                case 'porcentaje':
                    return new  PorcentajeVariacion();
                default:
                   throw new Exception("Tipo de Operación no valida");
            }
        }
    }


?>