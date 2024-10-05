<?php
$page = 'eliminacion_novedades';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

$message = '';
$message_type = '';
$novedades = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["fechaDesdeNovedad"]) && isset($_POST["fechaHastaNovedad"]) && isset($_POST["categoriaCliente"])) {
    $fechaD = trim($_POST["fechaDesdeNovedad"]);
    $fechaH = trim($_POST["fechaHastaNovedad"]);
    $tipoUsuario = trim($_POST["categoriaCliente"]);

    $buscarNovedades = "SELECT * FROM novedades WHERE fechaDesdeNovedad >= '$fechaD' AND fechaHastaNovedad <= '$fechaH' AND categoriaCliente = '$tipoUsuario'";
    $result = mysqli_query($link, $buscarNovedades);

    if (mysqli_num_rows($result) > 0) {
      $novedades = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
      $message = "No se encontraron novedades con los criterios especificados.";
      $message_type = 'error';
    }

    mysqli_close($link);
  }
}

// Proceso de eliminación si se ha seleccionado una novedad para eliminar
if (isset($_POST['eliminar_novedad'])) {
  $idNovedad = $_POST['eliminar_novedad'];

  // Consulta SQL para eliminar la novedad seleccionada
  $eliminarNovedad = "DELETE FROM novedades WHERE codNovedad = '$idNovedad'";
  $result = mysqli_query($link, $eliminarNovedad);

  if ($result) {
    $message = "Novedad eliminada correctamente.";
    $message_type = 'success';
  } else {
    $message = "Hubo un error al intentar eliminar la novedad. Por favor, inténtalo de nuevo.";
    $message_type = 'error';
  }

  mysqli_close($link);
}
?>


<div class="container">
  <h2 class="mt-5 text-center">Eliminar una novedad</h2>
  <form action="eliminacion_novedades.php" method="POST">
    <div class="form-group">
      <label for="fechaDesdeNovedad">Fecha Desde Novedad</label>
      <input type="date" class="form-control" id="fechaDesdeNovedad" name="fechaDesdeNovedad" required>
    </div>
    <div class="form-group">
      <label for="fechaHastaNovedad">Fecha Hasta Novedad</label>
      <input type="date" class="form-control" id="fechaHastaNovedad" name="fechaHastaNovedad" required>
    </div>
    <div class="form-group">
      <label for="categoriaCliente">Tipo de Usuario:</label>
      <select class="form-select form-control" id="categoriaCliente" name="categoriaCliente" required>
        <option value="" disabled selected>Seleccione la categoría del cliente</option>
        <option value="Inicial">Inicial</option>
        <option value="Medium">Medium</option>
        <option value="Premium">Premium</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Buscar Novedades</button>
    <a href="../../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
  </form>

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
              onclick="window.location.href='eliminacion_novedades.php'">Cerrar</button>
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
              onclick="window.location.href='eliminacion_novedades.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($novedades): ?>
    <h2 class="mt-5 text-center">Resultados encontrados:</h2>
    <form action="eliminacion_novedades.php" method="POST">
      <ul class="list-group mt-3">
        <?php foreach ($novedades as $novedad): ?>
          <li class="list-group-item">
            <input type="radio" name="eliminar_novedad" value="<?php echo $novedad['codNovedad']; ?>">
            Descripción: <?php echo $novedad['textoNovedad']; ?> | Desde: <?php echo $novedad['fechaDesdeNovedad']; ?> |
            Hasta: <?php echo $novedad['fechaHastaNovedad']; ?> | Cliente: <?php echo $novedad['categoriaCliente']; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <button type="submit" class="btn btn-danger btn-block mt-3">Eliminar Novedad Seleccionada</button>
    </form>
  <?php endif; ?>
</div>


<?php
include ("../includes/footer.php"); ?>