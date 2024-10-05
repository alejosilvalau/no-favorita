<?php
$page = 'promocion_local';
include ("../includes/navbar.php");

function numerosADias($numeros)
{
  $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  $numerosArray = explode(',', $numeros); // Asume que los días están separados por comas
  $nombresDias = [];

  foreach ($numerosArray as $numero) {
    if (isset($diasSemana[$numero])) {
      $nombresDias[] = $diasSemana[$numero];
    }
  }

  return implode(', ', $nombresDias); // Devuelve los nombres de los días separados por comas
}

$hoy = date('Y-m-d'); // Fecha actual en formato yyyy-mm-dd
$diaSemana = date('w'); // Día de la semana actual (0 para domingo, 1 para lunes, etc.)
$codLocal = isset($_GET['codLocal']) ? $_GET['codLocal'] : null;

$codUsuario = isset($_SESSION['codUsuario']) ? $_SESSION['codUsuario'] : null;
$tipoUsuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : 'No registrado';

$mensaje = '';
$tipoMensaje = '';

$esDuenio = false;
if ($codUsuario && $tipoUsuario == 'Dueño de local') {
  $query_duenio = "SELECT * FROM locales WHERE codLocal = $codLocal AND codUsuario = $codUsuario";
  $resultado_duenio = mysqli_query($link, $query_duenio);
  $esDuenio = mysqli_num_rows($resultado_duenio) > 0;
}

// Procesar solicitud de uso de la promoción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_promo']) && $codUsuario) {
  $codPromo = mysqli_real_escape_string($link, $_POST['codPromo']);
  $insert_query = "INSERT INTO uso_promociones (codCliente, codPromo, fechaUsoPromo, estado) VALUES ('$codUsuario', '$codPromo', '$hoy', 'enviada')";
  if (mysqli_query($link, $insert_query)) {
    $mensaje = 'Solicitud de promoción exitosa.';
    $tipoMensaje = 'success';
  } else {
    $mensaje = 'Error al solicitar la promoción. Por favor, inténtelo de nuevo.';
    $tipoMensaje = 'error';
  }
}

// Obtener parámetros de búsqueda y filtros
$search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';
$fecha_desde = isset($_GET['fecha_desde']) ? mysqli_real_escape_string($link, $_GET['fecha_desde']) : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? mysqli_real_escape_string($link, $_GET['fecha_hasta']) : '';

// Construir consulta base para las promociones
$busca_promociones = "SELECT * FROM promociones
                          WHERE codLocal = $codLocal
                          AND fechaHastaPromo >= '$hoy'
                          AND FIND_IN_SET('$diaSemana', diasSemana) > 0";

// Aplicar filtro de categoría de cliente si es un cliente registrado
if ($codUsuario && $tipoUsuario == 'Cliente') {
  $categoriaCliente = mysqli_real_escape_string($link, $_SESSION['categoriaCliente']);
  if ($categoriaCliente == 'Inicial') {
    $busca_promociones .= " AND estadoPromo = 'aprobada' AND categoriaCliente = 'Inicial'";
  } elseif ($categoriaCliente == 'Medium') {
    $busca_promociones .= " AND estadoPromo = 'aprobada' AND (categoriaCliente = 'Inicial' OR categoriaCliente = 'Medium')";
  } elseif ($categoriaCliente == 'Premium') {
    $busca_promociones .= " AND estadoPromo = 'aprobada'";
  }
}


// Aplicar filtros adicionales
if ($search) {
  $busca_promociones .= " AND (textoPromo LIKE '%$search%' OR codPromo LIKE '%$search%')";
}

if ($fecha_desde) {
  $busca_promociones .= " AND fechaDesdePromo >= '$fecha_desde'";
}

if ($fecha_hasta) {
  $busca_promociones .= " AND fechaHastaPromo <= '$fecha_hasta'";
}

// Implementación de paginación
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 5; // Número de registros por página
$offset = ($page - 1) * $records_per_page;
$count_query = "SELECT COUNT(*) AS total_records FROM ($busca_promociones) AS count_promos";
$count_result = mysqli_query($link, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total_records'];
$total_pages = ceil($total_records / $records_per_page);

$busca_promociones .= " LIMIT $offset, $records_per_page";

$resultado = mysqli_query($link, $busca_promociones);
?>

<div class="container">
  <div class="row">
    <div class="col-md-3">
      <form method="get" action="promocion_local.php">
        <input type="hidden" name="codLocal" value="<?php echo $codLocal; ?>">
        <div class="form-group">
          <label for="fecha_desde">Fecha desde:</label>
          <input type="date" class="form-control" name="fecha_desde" id="fecha_desde"
            value="<?php echo $fecha_desde; ?>">
        </div>
        <div class="form-group">
          <label for="fecha_hasta">Fecha hasta:</label>
          <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
            value="<?php echo $fecha_hasta; ?>">
        </div>
        <button type="submit" class="btn-filtrar">Filtrar</button>
      </form>
    </div>
    <div class="col-md-9">
      <?php
      if (mysqli_num_rows($resultado) > 0) {
        while ($row = mysqli_fetch_assoc($resultado)) {
          echo "<div class='promo-container'>";
          echo "<h3>ID de la Promoción: " . $row["codPromo"] . "</h3>";
          echo "<p><strong>Texto de la Promoción:</strong> " . $row["textoPromo"] . "</p>";
          echo "<p><strong>Fecha Desde:</strong> " . $row["fechaDesdePromo"] . "</p>";
          echo "<p><strong>Fecha Hasta:</strong> " . $row["fechaHastaPromo"] . "</p>";
          echo "<p><strong>Categoría del Cliente:</strong> " . $row["categoriaCliente"] . "</p>";
          echo "<p><strong>Días de la Semana:</strong> " . numerosADias($row["diasSemana"]) . "</p>";
          echo "<p><strong>Estado de la Promoción:</strong> " . $row["estadoPromo"] . "</p>";

          if ($codUsuario && $row["fechaDesdePromo"] <= $hoy) {
            // Verificar si el usuario ya utilizó esta promoción
            $consulta_uso = "SELECT * FROM uso_promociones WHERE codCliente = $codUsuario AND codPromo = " . $row['codPromo'];
            $resultado_uso = mysqli_query($link, $consulta_uso);

            if (mysqli_num_rows($resultado_uso) > 0) {
              echo "<div class='alert warning'>No puedes volver a utilizar esta promoción.</div>";
            } else {
              if ($tipoUsuario == 'No registrado') {
                echo "<div class='alert info'>Inicia sesión o regístrese para solicitar el uso de esta promoción.</div>";
              } elseif ($tipoUsuario == 'Dueño de local' && !$esDuenio && $categoriaCliente == 'Inicial' && ($row['categoriaCliente'] == 'Premium' || $row['categoriaCliente'] == 'Medium')) {
                echo "<div class='alert danger'>No puede solicitar esta promoción debido a que su categoría es muy baja.</div>";
              } elseif ($tipoUsuario == 'Dueño de local' && !$esDuenio && $categoriaCliente == 'Medium' && $row['categoriaCliente'] == 'Premium') {
                echo "<div class='alert danger'>No puede solicitar esta promoción debido a que su categoría es muy baja.</div>";
              } elseif ($tipoUsuario == 'Dueño de local' && !$esDuenio && $row['estadoPromo'] != 'aprobada') {
                echo "<div class='alert danger'>No puede solicitar esta promoción debido a que no ha sido aprobada.</div>";
              } elseif ($tipoUsuario != 'administrador' && !$esDuenio) {
                // Formulario para solicitar el uso de la promoción
                echo '<form method="post">';
                echo '<input type="hidden" name="codPromo" value="' . $row['codPromo'] . '">';
                echo '<button type="submit" name="solicitar_promo" class="btn-seleccionar">Solicitar uso de la promoción</button>';
                echo '</form>';
              } elseif ($esDuenio) {
                echo "<div class='alert info'>Esta promoción le pertenece a tu local.</div>";
              }
            }
          } elseif (!$codUsuario) {
            echo "<div class='alert danger'>Debe registrarse o iniciar sesión para acceder a este tipo de promociones</div>";
          } else {
            echo "<div class='alert info'>Esta promoción estará disponible desde " . $row["fechaDesdePromo"] . ".</div>";
          }

          echo "</div>";
        }

        // Mostrar paginación si hay más de una página
        if ($total_pages > 1) {
          echo '<nav aria-label="Page navigation example">';
          echo '<ul class="pagination justify-content-center">';
          for ($i = 1; $i <= $total_pages; $i++) {
            echo '<li class="page-item' . ($i === $page ? ' active' : '') . '">';
            echo '<a class="page-link" href="promocion_local.php?codLocal=' . $codLocal . '&page=' . $i . '">' . $i . '</a>';
            echo '</li>';
          }
          echo '</ul></nav>';
        }
      } else {
        echo "<div class='alert info'>No hay promociones disponibles para este local.</div>";
      }
      mysqli_close($link);
      ?>
    </div>
  </div>
</div>

<!-- Modal -->
<?php if ($tipoMensaje == 'success'): ?>
  <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
    style="display: block; background: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Éxito</h5>
        </div>
        <div class="modal-body">
          <?php echo $mensaje; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="window.location.href='locales.php'">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($tipoMensaje == 'error'): ?>
  <div class="modal fade show" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel"
    style="display: block; background: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
        </div>
        <div class="modal-body">
          <?php echo $mensaje; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="window.location.href='locales.php'">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include ("../includes/footer.php"); ?>