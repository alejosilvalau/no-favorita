<?php
$page = 'historial';
include("../includes/navbar.php");

function numerosADias($numeros)
{
  $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
  $numerosArray = explode(',', $numeros); // Asume que los días están separados por comas
  $nombresDias = [];

  foreach ($numerosArray as $numero) {
    if (isset($diasSemana[$numero])) {
      $nombresDias[] = $diasSemana[$numero];
    }
  }

  return implode(', ', $nombresDias); // Devuelve los nombres de los días separados por comas
}

if (isset($_SESSION['tipoUsuario'])) {
  $codUsuario = $_SESSION['codUsuario'];
  $busca_promociones = "SELECT * FROM uso_promociones up INNER JOIN promociones p where p.codPromo = up.codPromo AND codCliente = '$codUsuario'";
}

$resultado = mysqli_query($link, $busca_promociones);
$total_promociones = mysqli_num_rows($resultado);
?>

<div class="container">
  <?php
  if ($total_promociones > 0) {
    while ($row = mysqli_fetch_assoc($resultado)) {
      echo "<div class='promo-container'>";
      echo "<h3>ID de la Promoción: " . $row["codPromo"] . "</h3>";
      echo "<p><strong>Texto de la Promoción:</strong> " . $row["textoPromo"] . "</p>";
      echo "<p><strong>Fecha Desde:</strong> " . $row["fechaDesdePromo"] . "</p>";
      echo "<p><strong>Fecha Hasta:</strong> " . $row["fechaHastaPromo"] . "</p>";
      echo "<p><strong>Categoría del Cliente:</strong> " . $row["categoriaCliente"] . "</p>";
      echo "<p><strong>Días de la Semana:</strong> " . numerosADias($row["diasSemana"]) . "</p>";
      echo "<p><strong>Estado de la Promoción:</strong> " . $row["estadoPromo"] . "</p>";
      //echo "<button type='button' class='btn-seleccionar' onclick=\"window.location.href='promociones.php?codLocal=" . $row['codLocal'] . "'\">Seleccionar</button>";
      echo "</div>";
    }
  } else {
    echo "No adquiriste ninguna promoción.";
  }
  mysqli_close($link);
  ?>
</div>

<?php
include("../includes/footer.php"); ?>