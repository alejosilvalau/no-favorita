<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
$page = 'gestionar_descuentos';
include ("../includes/navbar.php");
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
$query = "SELECT * FROM promociones WHERE estadoPromo = 'pendiente' AND fechaDesdePromo >= '$hoy'";

$total_result = mysqli_query($link, $query);
if (!$total_result) {
  die('Error en la consulta: ' . mysqli_error($link));
}
$total_promociones = mysqli_num_rows($total_result);

$query .= " LIMIT $limit OFFSET $offset";
$resultado = mysqli_query($link, $query);
if (!$resultado) {
  die('Error en la consulta con límite: ' . mysqli_error($link));
}
$total_pages = ceil($total_promociones / $limit);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $codPromocion = $_POST['codPromo'];
  if (isset($_POST['action'])) {
    if ($_POST['action'] == 'validar') {
      $query_update = "UPDATE promociones SET estadoPromo = 'aprobada' WHERE codPromo = ?";
      $accion = 'aprobada';
      $alertClass = 'alert-success';
    } elseif ($_POST['action'] == 'denegar') {
      $query_update = "UPDATE promociones SET estadoPromo = 'denegada' WHERE codPromo = ?";
      $accion = 'denegada';
      $alertClass = 'alert-danger';
    }

    if (isset($query_update)) {
      if ($stmt = mysqli_prepare($link, $query_update)) {
        mysqli_stmt_bind_param($stmt, "i", $codPromocion);
        if (mysqli_stmt_execute($stmt)) {
          $message = "La solicitud de promoción cuyo código es $codPromocion ha sido $accion correctamente.";
        } else {
          $message = "Error al realizar la acción para la promoción cuyo código es $codPromocion.";
          $alertClass = 'alert-danger';
        }
        mysqli_stmt_close($stmt);
      } else {
        $message = 'Error al preparar la consulta.';
        $alertClass = 'alert-danger';
      }
    }
    // Redirigir a la misma página con los parámetros GET actualizados
    header("Location: gestionar_descuentos.php?page=$page&message=" . urlencode($message) . "&alertClass=$alertClass");
    exit();
  }
}
ob_end_flush();
?>


<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
  }

  .container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  h1 {
    text-align: center;
    margin-bottom: 20px;
  }

  .promo-container {
    padding: 15px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
    transition: box-shadow 0.3s ease;
  }

  .promo-container:hover {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  }

  .promo-container p {
    margin: 5px 0;
  }

  .btn-validar,
  .btn-denegar {
    padding: 8px 15px;
    margin-right: 10px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.3s ease;
  }

  .btn-validar {
    background-color: #6a11cb;
    color: #fff;
  }

  .btn-denegar {
    background-color: #dc3545;
    color: #fff;
  }

  .btn-validar:hover,
  .btn-denegar:hover {
    opacity: 0.8;
  }

  .alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    color: #fff;
  }

  .alert-success {
    background-color: #28a745;
  }

  .alert-danger {
    background-color: #dc3545;
  }

  .text-center a {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #6c757d;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
  }

  .text-center a:hover {
    background-color: #5a6268;
  }

  .pagination {
    justify-content: center;
    margin-top: 20px;
  }

  .pagination .page-item .page-link {
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #ddd;
  }

  .pagination .page-item .page-link:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
  }

  .pagination .page-item.active .page-link {
    z-index: 1;
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
  }
</style>


<div class="container">
  <h1>Promociones Pendientes de Aprobación</h1>
  <?php if (!empty($_GET['message'])): ?>
    <div class="alert <?php echo $_GET['alertClass']; ?> text-center">
      <?php echo urldecode($_GET['message']); ?>
    </div>
  <?php endif; ?>

  <?php
  if (mysqli_num_rows($resultado) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
      echo "<div class='promo-container'>";
      echo "<p><strong>Código de Promoción:</strong> " . $fila["codPromo"] . "</p>";
      echo "<p><strong>Código de Local:</strong> " . $fila["codLocal"] . "</p>";
      echo "<p><strong>Texto de la Promoción:</strong> " . $fila["textoPromo"] . "</p>";
      echo "<p><strong>Fecha Desde Promoción:</strong> " . $fila["fechaDesdePromo"] . "</p>";
      echo "<p><strong>Fecha Hasta Promoción:</strong> " . $fila["fechaHastaPromo"] . "</p>";
      echo "<p><strong>Categoría del Cliente:</strong> " . $fila["categoriaCliente"] . "</p>";
      echo "<p><strong>Días de la Semana:</strong> " . $fila["diasSemana"] . "</p>";
      echo "<p><strong>Estado de la Promoción:</strong> " . $fila["estadoPromo"] . "</p>";
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

  <div class="text-center">
    <a href="../../public/home.php">Volver al Inicio</a>
  </div>
</div>

<?php include ("../includes/footer.php"); ?>