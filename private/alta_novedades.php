<?php
$page = 'alta_novedades';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $descripcion = trim($_POST["textoNovedad"]);
  $fechaD = trim($_POST["fechaDesdeNovedad"]);
  $fechaH = trim($_POST["fechaHastaNovedad"]);
  $tipoUsuario = trim($_POST["categoriaCliente"]);

  $today = date("Y-m-d");

  if ($fechaD < $today) {
    $message = "La fecha desde debe ser mayor a hoy.";
    $message_type = 'error';
  } elseif ($fechaH <= $fechaD) {
    $message = "La fecha hasta debe ser mayor a la fecha desde.";
    $message_type = 'error';
  } else {

    $buscarNovedad = "select * from novedades where textoNovedad = '$descripcion' and fechaDesdeNovedad = '$fechaD' and fechaHastaNovedad = '$fechaH' and categoriaCliente = '$tipoUsuario'";
    $result = mysqli_query($link, $buscarNovedad);
    $vResult = mysqli_fetch_array($result);
    if ($vResult) {
      $message = 'Ya existe esa novedad';
      $message_type = 'error';
    } else {

      $altaNovedad = "insert into novedades (textoNovedad, fechaDesdeNovedad, fechaHastaNovedad, categoriaCliente) values('$descripcion', '$fechaD', '$fechaH', '$tipoUsuario')";
      $result2 = mysqli_query($link, $altaNovedad);
      if ($result2) {
        $message = "Novedad generada exitosamente.";
        $message_type = 'success';
      } else {
        $message = "Hubo un error al generar la novedad. Por favor, inténtalo de nuevo.";
        $message_type = 'error';
      }
    }
  }
  mysqli_close($link);
}
?>

<div class="container">
  <h2 class="mt-5 text-center">Generar una novedad</h2>
  <form action="alta_novedades.php" method="POST">
    <div class="form-group">
      <label for="textoNovedad">Descripción de la Novedad:</label>
      <textarea class="form-control" name="textoNovedad" id="textoNovedad" cols="30" rows="10" required></textarea>
    </div>
    <div class="form-group">
      <label for="fechaDesdeNovedad">Fecha Desde Novedad</label>
      <input type="date" class="form-control" id="fechaDesdeNovedad" name="fechaDesdeNovedad" required>
    </div>
    <div class="form-group">
      <label for="fechaHastaNovedad">Fecha Hasta Novedad</label>
      <input type="date" class="form-control" id="fechaHastaNovedad" name="fechaHastaNovedad" required>
    </div>
    <div class="form-group">
      <label for="categoriaCliente">Categoría del cliente:</label>
      <select class="form-select form-control" id="categoriaCliente" name="categoriaCliente" required>
        <option value="" disabled selected>Seleccione la categoría del cliente</option>
        <option value="Inicial">Inicial</option>
        <option value="Medium">Medium</option>
        <option value="Premium">Premium</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Generar Novedad</button>

    <a href="../../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
  </form>
</div>
</div>

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
            onclick="window.location.href='alta_novedades.php'">Cerrar</button>
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
          <button type="button" class="btn btn-danger" onclick="window.location.href='alta_novedades.php'">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>


<?php
include ("../includes/footer.php"); ?>