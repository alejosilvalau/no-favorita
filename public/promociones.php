<?php
$page = 'promociones';
include ("../includes/navbar.php");

// Función para obtener nombres de días
function numerosADias($numeros)
{
  $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  $numerosArray = explode(',', $numeros);
  $nombresDias = [];

  foreach ($numerosArray as $numero) {
    if (isset($diasSemana[$numero])) {
      $nombresDias[] = $diasSemana[$numero];
    }
  }

  return implode(', ', $nombresDias);
}

// Variables de sesión y mensaje
$hoy = date('Y-m-d');
$codUsuario = isset($_SESSION['codUsuario']) ? $_SESSION['codUsuario'] : null;
$tipoUsuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : 'No registrado';
$categoriaCliente = isset($_SESSION['categoriaCliente']) ? $_SESSION['categoriaCliente'] : null;
$message = "";
$message_type = "";

// Procesar solicitud de promoción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['solicitar_promo'])) {
  $codPromo = mysqli_real_escape_string($link, $_POST['codPromo']);
  $insert_query = "INSERT INTO uso_promociones (codCliente, codPromo, fechaUsoPromo, estado) VALUES ('$codUsuario', '$codPromo', '$hoy', 'enviada')";
  if (mysqli_query($link, $insert_query)) {
    $message = 'Solicitud de promoción enviada correctamente.';
    $message_type = 'success';
  } else {
    $message = 'Hubo un error al enviar la solicitud de promoción.';
    $message_type = 'error';
  }
}

// Obtener filtros
$search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';
$fecha_desde = isset($_GET['fecha_desde']) ? mysqli_real_escape_string($link, $_GET['fecha_desde']) : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? mysqli_real_escape_string($link, $_GET['fecha_hasta']) : '';
$local = isset($_GET['local']) ? mysqli_real_escape_string($link, $_GET['local']) : '';
$rubroLocal = isset($_GET['rubroLocal']) ? mysqli_real_escape_string($link, $_GET['rubroLocal']) : '';
$categoriaPromo = isset($_GET['categoriaPromo']) ? mysqli_real_escape_string($link, $_GET['categoriaPromo']) : '';
$promociones_solicitadas = isset($_GET['promociones_solicitadas']) ? $_GET['promociones_solicitadas'] : '';
$mis_locales = isset($_GET['mis_locales']) ? $_GET['mis_locales'] : '';

// Construir consulta base para las promociones
$busca_promociones = "SELECT p.*, l.nombreLocal, l.rubroLocal, l.codUsuario FROM promociones p INNER JOIN locales l ON l.codLocal = p.codLocal WHERE p.estadoPromo = 'aprobada'";

// Aplicar filtros
if ($search) {
  $busca_promociones .= " AND (p.textoPromo LIKE '%$search%' OR p.codPromo LIKE '%$search%')";
}

if ($fecha_desde) {
  $busca_promociones .= " AND p.fechaDesdePromo >= '$fecha_desde'";
}

if ($fecha_hasta) {
  $busca_promociones .= " AND p.fechaHastaPromo <= '$fecha_hasta'";
}

if ($local) {
  $busca_promociones .= " AND (p.codLocal = '$local' OR l.nombreLocal LIKE '%$local%')";
}

if ($rubroLocal) {
  $busca_promociones .= " AND l.rubroLocal = '$rubroLocal'";
}

if ($categoriaPromo) {
  $busca_promociones .= " AND p.categoriaCliente = '$categoriaPromo'";
}

if ($promociones_solicitadas && $codUsuario) {
  $busca_promociones .= " AND p.codPromo IN (SELECT codPromo FROM uso_promociones WHERE codCliente = '$codUsuario')";
}

if ($mis_locales && $tipoUsuario == 'Dueño de local') {
  $busca_promociones .= " AND l.codUsuario = '$codUsuario'";
}

// Filtrar por categoría de cliente
if ($tipoUsuario == 'Cliente') {
  if ($categoriaCliente == 'Inicial') {
    $busca_promociones .= " AND p.categoriaCliente = 'Inicial'";
  } elseif ($categoriaCliente == 'Medium') {
    $busca_promociones .= " AND (p.categoriaCliente = 'Inicial' OR p.categoriaCliente = 'Medium')";
  }
}

// Implementar paginación
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;
$count_query = "SELECT COUNT(*) AS total_records FROM ($busca_promociones) AS count_promos";
$count_result = mysqli_query($link, $count_query);
$total_records = $count_result ? mysqli_fetch_assoc($count_result)['total_records'] : 0;
$total_pages = ceil($total_records / $records_per_page);

$busca_promociones .= " LIMIT $offset, $records_per_page";
$resultado = mysqli_query($link, $busca_promociones);
?>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-3">
      <form method="get" action="promociones.php">
        <div class="form-group">
          <label for="fecha_desde">Fecha desde:</label>
          <input type="date" class="form-control" name="fecha_desde" id="fecha_desde"
            value="<?php echo htmlspecialchars($fecha_desde); ?>">
        </div>
        <div class="form-group">
          <label for="fecha_hasta">Fecha hasta:</label>
          <input type="date" class="form-control" name="fecha_hasta" id="fecha_hasta"
            value="<?php echo htmlspecialchars($fecha_hasta); ?>">
        </div>
        <div class="form-group">
          <label for="local">Código o nombre del local:</label>
          <input type="text" class="form-control" name="local" id="local"
            value="<?php echo htmlspecialchars($local); ?>">
        </div>
        <div class="form-group">
          <label for="rubroLocal">Rubro del local:</label>
          <select class="form-control" id="rubroLocal" name="rubroLocal">
            <option value="">Todos</option>
            <option value="Indumentaria">Indumentaria</option>
            <option value="Perfumeria">Perfumería</option>
            <option value="Optica">Óptica</option>
            <option value="Comida">Comida</option>
            <option value="Computacion">Computación</option>
            <option value="Entretenimiento">Entretenimiento</option>
            <option value="Cine">Cine</option>
            <option value="Otros">Otros</option>
          </select>
        </div>
        <?php if ($tipoUsuario == 'No registrado' || $tipoUsuario == 'Cliente' && $categoriaCliente != 'Inicial' || $tipoUsuario == 'Dueño de local' || $tipoUsuario == 'administrador'): ?>
          <div class="form-group">
            <label for="categoriaPromo">Categoría de la promoción:</label>
            <select class="form-control" name="categoriaPromo" id="categoriaPromo">
              <option value="">Todas</option>
              <option value="Inicial" <?php echo $categoriaPromo == 'Inicial' ? 'selected' : ''; ?>>Inicial</option>
              <option value="Medium" <?php echo $categoriaPromo == 'Medium' ? 'selected' : ''; ?>>Medium</option>
              <?php if ($tipoUsuario == 'administrador' || $tipoUsuario == 'Dueño de local' || $categoriaCliente == 'Premium'): ?>
                <option value="Premium" <?php echo $categoriaPromo == 'Premium' ? 'selected' : ''; ?>>Premium</option>
              <?php endif; ?>
            </select>
          </div>
        <?php endif; ?>
        <?php if ($tipoUsuario == 'Cliente' || $tipoUsuario == 'Dueño de local'): ?>
          <div class="form-group">
            <label for="promociones_solicitadas">Promociones solicitadas:</label>
            <input type="checkbox" id="promociones_solicitadas" name="promociones_solicitadas" value="1" <?php echo $promociones_solicitadas ? 'checked' : ''; ?>>
          </div>
        <?php endif; ?>
        <?php if ($tipoUsuario == 'Dueño de local'): ?>
          <div class="form-group">
            <label for="mis_locales">Mis locales:</label>
            <input type="checkbox" id="mis_locales" name="mis_locales" value="1" <?php echo $mis_locales ? 'checked' : ''; ?>>
          </div>
        <?php endif; ?>
        <button type="submit" class="btn-filtrar">Filtrar</button>
      </form>
    </div>
    <div class="col-md-9">
      <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
          <div class='promo-container'>
            <h3>ID de la Promoción: <?php echo htmlspecialchars($row["codPromo"]); ?></h3>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($row["textoPromo"]); ?></p>
            <p><strong>Fecha Desde:</strong> <?php echo htmlspecialchars($row["fechaDesdePromo"]); ?></p>
            <p><strong>Fecha Hasta:</strong> <?php echo htmlspecialchars($row["fechaHastaPromo"]); ?></p>
            <p><strong>Categoría del Cliente:</strong> <?php echo htmlspecialchars($row["categoriaCliente"]); ?></p>
            <p><strong>Días de la Semana:</strong> <?php echo numerosADias($row["diasSemana"]); ?></p>
            <p><strong>Estado de la Promoción:</strong> <?php echo htmlspecialchars($row["estadoPromo"]); ?></p>
            <p><strong>Código del Local:</strong> <?php echo htmlspecialchars($row["codLocal"]); ?></p>
            <p><strong>Nombre del Local:</strong> <?php echo htmlspecialchars($row["nombreLocal"]); ?></p>
            <p><strong>Rubro del Local:</strong> <?php echo htmlspecialchars($row["rubroLocal"]); ?></p>

            <?php if ($codUsuario): ?>
              <?php if ($tipoUsuario == 'Dueño de local' && $row['codUsuario'] == $codUsuario): ?>
                <div class='alert info'>Esta promoción pertenece a su local.</div>
              <?php elseif ($tipoUsuario == 'No registrado'): ?>
                <div class='alert info'>Inicie sesión o regístrese para solicitar uso de promociones.</div>
              <?php elseif ($tipoUsuario == 'Dueño de local' && $row['codUsuario'] != $codUsuario && $categoriaCliente == 'Inicial' && ($row['categoriaCliente'] == 'Medium' || $row['categoriaCliente'] == 'Premium')): ?>
                <div class='alert danger'>No puede solicitar esta promoción debido a que su categoría es muy baja.</div>
              <?php elseif ($tipoUsuario == 'Dueño de local' && $row['codUsuario'] != $codUsuario && $categoriaCliente == 'Medium' && $row['categoriaCliente'] == 'Premium'): ?>
                <div class='alert danger'>No puede solicitar esta promoción debido a que su categoría es muy baja.</div>
              <?php else: ?>
                <?php if ($tipoUsuario == 'Cliente'): ?>
                  <?php if ($row['fechaDesdePromo'] <= date('Y-m-d')): ?>
                    <?php
                    $consulta_uso = "SELECT * FROM uso_promociones WHERE codCliente = '$codUsuario' AND codPromo = '" . $row['codPromo'] . "'";
                    $resultado_uso = mysqli_query($link, $consulta_uso);
                    ?>
                    <?php if (mysqli_num_rows($resultado_uso) > 0): ?>
                      <div class='alert warning'>Ya has utilizado esta promoción.</div>
                    <?php elseif (!in_array(date('w'), explode(',', $row['diasSemana']))): ?>
                      <div class='alert info'>Esta promoción no está disponible en este dia.</div>
                    <?php else: ?>
                      <form method="post">
                        <input type="hidden" name="codPromo" value="<?php echo htmlspecialchars($row['codPromo']); ?>">
                        <button type="submit" name="solicitar_promo" class="btn-seleccionar">Solicitar uso de la promoción</button>
                      </form>
                    <?php endif; ?>
                  <?php else: ?>
                    <div class='alert info warning'>Esta promoción aún no está disponible.</div>
                  <?php endif; ?>
                <?php elseif ($tipoUsuario == 'Dueño de local'): ?>
                  <?php if ($row['codUsuario'] != $codUsuario): ?>
                    <?php
                    $consulta_uso = "SELECT * FROM uso_promociones WHERE codCliente = '$codUsuario' AND codPromo = '" . $row['codPromo'] . "'";
                    $resultado_uso = mysqli_query($link, $consulta_uso);
                    ?>
                    <?php if (mysqli_num_rows($resultado_uso) > 0): ?>
                      <div class='alert warning'>Ya has utilizado esta promoción.</div>
                    <?php else: ?>
                      <form method="post" action="promociones.php">
                        <input type="hidden" name="codPromo" value="<?php echo htmlspecialchars($row['codPromo']); ?>">
                        <button type="submit" name="solicitar_promo" class="btn-seleccionar">Solicitar uso de la promoción</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
        <nav aria-label="Page navigation">
          <ul class="pagination" style="justify-content: center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link"
                  href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php else: ?>
        <div class='alert info'>No se encontraron promociones.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal de éxito -->
  <?php if ($message_type == 'success'): ?>
    <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
      style="display: block; background: rgba(0, 0, 0, 0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Éxito</h5>
          </div>
          <div class="modal-body">
            <?php echo $message; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" onclick="window.location.href='promociones.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Modal de error -->
  <?php if ($message_type == 'error'): ?>
    <div class="modal fade show" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel"
      style="display: block; background: rgba(0, 0, 0, 0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error</h5>
          </div>
          <div class="modal-body">
            <?php echo $message; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" onclick="window.location.href='promociones.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>


<?php include ("../includes/footer.php"); ?>