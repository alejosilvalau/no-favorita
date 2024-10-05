<?php
$page = 'modificacion_novedades';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

$message = "";
$message_type = "";
$novedades = [];
$selectedNovedad = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["buscar"])) {
    // Buscar novedades por los campos
    $fechaD = trim($_POST["fechaDesdeNovedad"]);
    $fechaH = trim($_POST["fechaHastaNovedad"]);
    $tipoUsuario = trim($_POST["categoriaCliente"]);

    $buscarNovedades = "SELECT * FROM novedades WHERE fechaDesdeNovedad >= '$fechaD' AND fechaHastaNovedad <= '$fechaH' AND categoriaCliente = '$tipoUsuario'";
    $result = mysqli_query($link, $buscarNovedades);
    while ($row = mysqli_fetch_assoc($result)) {
      $novedades[] = $row;
    }

    if (empty($novedades)) {
      $message = 'No existen novedades con esos datos.';
      $message_type = 'error';
    }
  } elseif (isset($_POST["seleccionar"])) {

    $codNovedad = $_POST["codNovedad"];
    $buscarNovedad = "SELECT * FROM novedades WHERE codNovedad = '$codNovedad'";
    $result = mysqli_query($link, $buscarNovedad);
    $selectedNovedad = mysqli_fetch_assoc($result);
  } elseif (isset($_POST["modificar"])) {

    $codNovedad = $_POST["codNovedad"];
    $descripcion = trim($_POST["textoNovedad"]);
    $fechaD = trim($_POST["fechaDesdeNovedad"]);
    $fechaH = trim($_POST["fechaHastaNovedad"]);
    $tipoUsuario = trim($_POST["categoriaCliente"]);


    if (empty($descripcion)) {
      $buscarDescripcionOriginal = "SELECT textoNovedad FROM novedades WHERE codNovedad = '$codNovedad'";
      $result = mysqli_query($link, $buscarDescripcionOriginal);
      $row = mysqli_fetch_assoc($result);
      $descripcion = $row['textoNovedad'];
    }

    $modificarNovedad = "UPDATE novedades SET textoNovedad = '$descripcion', fechaDesdeNovedad = '$fechaD', fechaHastaNovedad = '$fechaH', categoriaCliente = '$tipoUsuario' WHERE codNovedad = '$codNovedad'";
    $result2 = mysqli_query($link, $modificarNovedad);
    if ($result2) {
      $message = "Novedad modificada exitosamente.";
      $message_type = 'success';
      $novedades = [];
      $selectedNovedad = [];
    } else {
      $message = "Hubo un error al modificar la novedad. Por favor, inténtalo de nuevo.";
      $message_type = 'error';
    }
  }
  mysqli_close($link);
}
?>


<div class="container">
  <h2 class="mt-5 text-center">Modificar una Novedad</h2>
  <form action="modificacion_novedades.php" method="POST">
    <div class="form-group">
      <label for="fechaDesdeNovedad">Fecha Desde Novedad</label>
      <input type="date" class="form-control" id="fechaDesdeNovedad" name="fechaDesdeNovedad"
        value="<?php echo isset($selectedNovedad['fechaDesdeNovedad']) ? $selectedNovedad['fechaDesdeNovedad'] : ''; ?>"
        required>
    </div>
    <div class="form-group">
      <label for="fechaHastaNovedad">Fecha Hasta Novedad</label>
      <input type="date" class="form-control" id="fechaHastaNovedad" name="fechaHastaNovedad"
        value="<?php echo isset($selectedNovedad['fechaHastaNovedad']) ? $selectedNovedad['fechaHastaNovedad'] : ''; ?>"
        required>
    </div>
    <div class="form-group">
      <label for="categoriaCliente">Categoría del Cliente</label>
      <select class="form-select form-control" id="categoriaCliente" name="categoriaCliente" required>
        <option value="" disabled selected>Seleccione la categoría del cliente</option>
        <option value="Inicial" <?php echo (isset($selectedNovedad['categoriaCliente']) && $selectedNovedad['categoriaCliente'] == 'Inicial') ? 'selected' : ''; ?>>Inicial</option>
        <option value="Medium" <?php echo (isset($selectedNovedad['categoriaCliente']) && $selectedNovedad['categoriaCliente'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
        <option value="Premium" <?php echo (isset($selectedNovedad['categoriaCliente']) && $selectedNovedad['categoriaCliente'] == 'Premium') ? 'selected' : ''; ?>>Premium</option>
      </select>
    </div>
    <button type="submit" name="buscar" class="btn btn-primary btn-block">Buscar Novedad</button>
  </form>

  <?php if (!empty($novedades)): ?>
    <div class="mt-4">
      <h5>Seleccione la novedad que desea modificar:</h5>
      <form action="modificacion_novedades.php" method="POST">
        <ul class="list-group">
          <?php foreach ($novedades as $novedad): ?>
            <li class="list-group-item">
              <input type="radio" name="codNovedad" value="<?php echo $novedad['codNovedad']; ?>" required>
              <?php echo "Descripción: " . $novedad['textoNovedad'] . " - Desde: " . $novedad['fechaDesdeNovedad'] . " - Hasta: " . $novedad['fechaHastaNovedad'] . " - Categoría Cliente: " . $novedad['categoriaCliente']; ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <button type="submit" name="seleccionar" class="btn btn-success btn-block mt-2">Seleccionar Novedad</button>
      </form>
    </div>
  <?php endif; ?>

  <?php if (!empty($selectedNovedad)): ?>
    <form action="modificacion_novedades.php" method="POST" class="mt-4">
      <input type="hidden" name="codNovedad" value="<?php echo $selectedNovedad['codNovedad']; ?>">
      <div class="form-group">
        <label for="textoNovedad">Descripción de la Novedad:</label>
        <textarea class="form-control" name="textoNovedad" id="textoNovedad" cols="30" rows="10"
          required><?php echo $selectedNovedad['textoNovedad']; ?></textarea>
      </div>
      <div class="form-group">
        <label for="fechaDesdeNovedad">Fecha Desde Novedad</label>
        <input type="date" class="form-control" id="fechaDesdeNovedad" name="fechaDesdeNovedad"
          value="<?php echo $selectedNovedad['fechaDesdeNovedad']; ?>" required>
      </div>
      <div class="form-group">
        <label for="fechaHastaNovedad">Fecha Hasta Novedad</label>
        <input type="date" class="form-control" id="fechaHastaNovedad" name="fechaHastaNovedad"
          value="<?php echo $selectedNovedad['fechaHastaNovedad']; ?>" required>
      </div>
      <div class="form-group">
        <label for="categoriaCliente">Categoría del Cliente:</label>
        <select class="form-select form-control" id="categoriaCliente" name="categoriaCliente" required>
          <option value="" disabled selected>Seleccione la categoría del cliente</option>
          <option value="Inicial" <?php echo ($selectedNovedad['categoriaCliente'] == 'Inicial') ? 'selected' : ''; ?>>
            Inicial</option>
          <option value="Medium" <?php echo ($selectedNovedad['categoriaCliente'] == 'Medium') ? 'selected' : ''; ?>>
            Medium</option>
          <option value="Premium" <?php echo ($selectedNovedad['categoriaCliente'] == 'Premium') ? 'selected' : ''; ?>>
            Premium</option>
        </select>
      </div>
      <button type="submit" name="modificar" class="btn btn-primary btn-block">Modificar Novedad</button>
    </form>
  <?php endif; ?>

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
            <button type="button" class="btn btn-success"
              onclick="window.location.href='modificacion_novedades.php'">Cerrar</button>
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
            <button type="button" class="btn btn-danger"
              onclick="window.location.href='modificacion_novedades.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <a href="../../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
</div>


<?php
include ("../includes/footer.php"); ?>