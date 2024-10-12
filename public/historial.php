<?php
$page = 'historial';
include("../includes/navbar.php");

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

if (isset($_SESSION['tipoUsuario'])) {
  $codUsuario = $_SESSION['codUsuario'];
  $busca_promociones = "SELECT * FROM uso_promociones up INNER JOIN promociones p ON p.\"codPromo\" = up.\"codPromo\" AND \"codCliente\" = '$codUsuario'";
}

$resultado = pg_query($link, $busca_promociones);
$total_promociones = pg_num_rows($resultado);
?>

<div class="container">
  <?php
  if ($total_promociones > 0) {
    while ($row = pg_fetch_assoc($resultado)) {
      echo "<div class='promo-container'>";
      echo "<h3><strong>ID DE PROMOCIÓN: " . $row["codPromo"] . "</strong></h3>";
      echo "<p>Texto de la Promoción: " . $row["textoPromo"] . "</p>";
      echo "<p>Fecha Desde: " . $row["fechaDesdePromo"] . "</p>";
      echo "<p>Fecha Hasta: " . $row["fechaHastaPromo"] . "</p>";
      echo "<p>Categoría del Cliente: " . $row["categoriaCliente"] . "</p>";
      echo "<p>Días de la Semana: " . numerosADias($row["diasSemana"]) . "</p>";
      echo "<p>Estado de la Promoción: " . $row["estadoPromo"] . "</p>";
      //echo "<button type='button' class='btn-seleccionar' onclick=\"window.location.href='promociones.php?codLocal=" . $row['codLocal'] . "'\">Seleccionar</button>";
      echo "</div>";
    }
  } else {
    echo "No adquiriste ninguna promoción.";
  }
pg_close($link);
  ?>
</div>
<?php
if ($total_promociones <= 0) {
  echo "<div class='filler'></div>";
}
include("../includes/footer.php");
?>