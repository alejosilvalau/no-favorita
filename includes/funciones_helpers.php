<?php
function numerosADias($numeros)
{
  $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
  $numerosArray = explode(',', $numeros);
  $nombresDias = [];

  foreach ($numerosArray as $numero) {
    $numero = intval(trim($numero));
    if ($numero >= 0 && $numero <= 6) {
      $nombresDias[] = $diasSemana[$numero];
    }
  }

  return implode(', ', $nombresDias);
}
