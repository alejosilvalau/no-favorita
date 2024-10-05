<?php
$page = 'reportes';
include ("../includes/navbar.php");
if ($_SESSION['tipoUsuario'] !== 'administrador' && $_SESSION['tipoUsuario'] !== 'Dueño de local') {
  header("Location: ../../public/home.php"); // Redirigir si no es administrador o dueño de local
  exit();
}

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

$limit = 5; // Límite de promociones por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Verifica si el usuario es un dueño
if ($_SESSION['tipoUsuario'] == 'Dueño de local') {
  $idDueño = $_SESSION['codUsuario'];
  $total_query = "SELECT COUNT(*) FROM promociones WHERE estadoPromo = 'aprobada' AND codLocal IN (SELECT codLocal FROM locales WHERE codUsuario = '$idDueño')";
  $query = "SELECT * FROM promociones WHERE estadoPromo = 'aprobada' AND codLocal IN (SELECT codLocal FROM locales WHERE codUsuario = '$idDueño') LIMIT $limit OFFSET $offset";
} else {
  $total_query = "SELECT COUNT(*) FROM promociones WHERE estadoPromo = 'aprobada'";
  $query = "SELECT * FROM promociones WHERE estadoPromo = 'aprobada' LIMIT $limit OFFSET $offset";
}

$total_result = mysqli_query($link, $total_query);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit);
$result = mysqli_query($link, $query);
?>

<div class="container">
  <h1>Reporte de Promociones Aprobadas</h1>
  <?php
  if ($result) {
    echo "<div class='gestion_descuentos'>";
    while ($row = mysqli_fetch_assoc($result)) {
      echo "<div class='promo-container'>";
      //echo "<h3>" . $row['titulo'] . "</h3>";
      echo "<p>Descripción de la promoción: " . $row['textoPromo'] . "</p>";
      echo "<p>Fecha de inicio: " . $row['fechaDesdePromo'] . "</p>";
      echo "<p>Fecha de fin: " . $row['fechaHastaPromo'] . "</p>";
      echo "<p>Categoria Destinada: " . $row['categoriaCliente'] . "</p>";
      echo "<p>Dias de la semana disponibles: " . numerosADias($row['diasSemana']) . "</p>";
      $codLocal = $row['codLocal'];
      $query = "SELECT * FROM locales WHERE codLocal = $codLocal";
      $resultado = mysqli_query($link, $query);
      $local = mysqli_fetch_array($resultado);
      echo "<p>Nombre del local al cual pertenece: " . $local['nombreLocal'] . "</p>";
      echo "<p>Cantidad de veces que se utilizó la promoción: " . $row['cantUsadas'] . "</p>";
      echo "</div>";
    }
    echo "</div>";

    // Pagination
    if ($total_pages > 1) {
      echo "<nav aria-label='Page navigation example'>";
      echo "<ul class='pagination' style='justify-content: center;'>";
      for ($i = 1; $i <= $total_pages; $i++) {
        echo "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='reportes.php?page=$i'>$i</a></li>";
      }
      echo "</ul></nav>";
    }
  } else {
    echo "Failed to fetch promotions: " . mysqli_error($conexion);
  }
  mysqli_close($link);
  ?>
</div>

<?php
include ("../includes/footer.php"); ?>