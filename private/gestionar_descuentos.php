<?php
include_once('../includes/funciones_helpers.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
$page = 'gestionar_descuentos';
include("../includes/navbar.php");
if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php");
  exit();
}

$message = '';
$alertClass = '';
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$hoy = date('Y-m-d');
$query = "SELECT * FROM promociones WHERE \"estadoPromo\" = 'pendiente' AND \"fechaDesdePromo\" >= '$hoy'";

$total_result = pg_query($link, $query);
if (!$total_result) {
  die('Error en la consulta: ' . pg_last_error($link));
}
$total_promociones = pg_num_rows($total_result);

$query .= " LIMIT $limit OFFSET $offset";
$resultado = pg_query($link, $query);
if (!$resultado) {
  die('Error en la consulta con límite: ' . pg_last_error($link));
}
$total_pages = ceil($total_promociones / $limit);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $codPromocion = $_POST['codPromo'];
  if (isset($_POST['action'])) {
    if ($_POST['action'] == 'validar') {
      $query_update = "UPDATE promociones SET \"estadoPromo\" = 'aprobada' WHERE \"codPromo\" = $1";
      $accion = 'aprobada';
      $alertClass = 'alert-success';
    } elseif ($_POST['action'] == 'denegar') {
      $query_update = "UPDATE promociones SET \"estadoPromo\" = 'denegada' WHERE \"codPromo\" = $1";
      $accion = 'denegada';
      $alertClass = 'alert-danger';
    }

    if (isset($query_update)) {
      $stmt = pg_prepare($link, "my_query", $query_update);

      if ($stmt) {
        $result = pg_execute($link, "my_query", array($codPromocion));

        if ($result) {
          $message = "La solicitud de promoción cuyo código es $codPromocion ha sido $accion correctamente.";
        } else {
          $message = "Error al realizar la acción para la promoción cuyo código es $codPromocion.";
          $alertClass = 'alert-danger';
        }
      } else {
        $message = 'Error al preparar la consulta.';
        $alertClass = 'alert-danger';
      }
    }
    pg_close($link);
    // Redirigir a la misma página con los parámetros GET actualizados
    header("Location: gestionar_descuentos.php?page=$page&message=" . urlencode($message) . "&alertClass=$alertClass");
    exit();
  }
}
ob_end_flush();
?>

<div class="container">
  <h1><strong>PROMOCIONES PENDIENTES DE APROBACIÓN</strong></h1>
  <?php if (!empty($_GET['message'])): ?>
    <div class="alert <?php echo $_GET['alertClass']; ?> text-center">
      <?php echo urldecode($_GET['message']); ?>
    </div>
  <?php endif; ?>

  <?php
if (pg_num_rows($resultado) > 0) {
  while ($fila = pg_fetch_assoc($resultado)) {
      echo "<div class='promo-container'>";
      echo "<p>Código de Promoción: " . $fila["codPromo"] . "</p>";
      echo "<p>Código de Local: " . $fila["codLocal"] . "</p>";
      echo "<p>Texto de la Promoción: " . $fila["textoPromo"] . "</p>";
      echo "<p>Fecha Desde Promoción: " . $fila["fechaDesdePromo"] . "</p>";
      echo "<p>Fecha Hasta Promoción: " . $fila["fechaHastaPromo"] . "</p>";
      echo "<p>Categoría del Cliente: " . $fila["categoriaCliente"] . "</p>";
      echo "<p>Días de la Semana: " . numerosADias($fila["diasSemana"]) . "</p>";
      echo "<p>Estado de la Promoción: " . $fila["estadoPromo"] . "</p>";
      echo "<form action='gestionar_descuentos.php?page=$page' method='POST'>";
      echo "<input type='hidden' name='codPromo' value='" . $fila['codPromo'] . "'>";
      echo "<button type='submit' name='action' value='validar' class='btn btn-validar'>Aprobar</button>";
      echo "<button type='submit' name='action' value='denegar' class='btn btn-denegar'>Denegar</button>";
      echo "</form>";
      echo "</div>";
    }

    if ($total_pages > 1) {
      echo "<nav aria-label='Page navigation example'>";
      echo "<ul class='pagination'>";
      $pagina_actual = $page;

      if ($pagina_actual > 1) {
        echo "<li class='page-item'><a class='page-link' href='gestionar_descuentos.php?page=" . ($pagina_actual - 1) . "'>&laquo; Anterior</a></li>";
      }

      for ($i = 1; $i <= $total_pages; $i++) {
        echo "<li class='page-item" . ($pagina_actual == $i ? ' active' : '') . "'><a class='page-link' href='gestionar_descuentos.php?page=" . $i . "'>" . $i . "</a></li>";
      }

      if ($pagina_actual < $total_pages) {
        echo "<li class='page-item'><a class='page-link' href='gestionar_descuentos.php?page=" . ($pagina_actual + 1) . "'>Siguiente &raquo;</a></li>";
      }

      echo "</ul>";
      echo "</nav>";
    }
  } else {
    echo "<p>No hay promociones pendientes de aprobación.</p>";
  }



  ?>

  <!-- <div class="text-center">
    <a href="../../public/home.php">Volver al Inicio</a>
  </div> -->
</div>

<?php
if (pg_num_rows($resultado) <= 0) {
  echo "<div class='filler'></div>";
}
include("../includes/footer.php");
?>