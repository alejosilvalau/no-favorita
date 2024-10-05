<?php
$page = 'modificacion_perfil';
include ("../includes/navbar.php");

$codUsuario = $_SESSION['codUsuario'];
$busca_usuario = "SELECT * FROM usuarios WHERE codUsuario = '$codUsuario'";
$resultado = mysqli_query($link, $busca_usuario);
$usuario = mysqli_fetch_array($resultado);
$message = "";
$message_type = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if ($_POST["claveUsuario"] == $_POST["claveUsuario2"]) {
    if (isset($_POST["modificarUsuario"])) {
      $qryModificarLocal = "";
      if (!empty($_POST["nombreUsuario"]) && !empty($_POST["claveUsuario"])) {
        $nombreUsuario = $_POST["nombreUsuario"];
        $clave = $_POST["claveUsuario"];
        $password_hash = password_hash($clave, PASSWORD_DEFAULT);
        $qryModificarLocal = "UPDATE usuarios SET nombreUsuario = '$nombreUsuario', claveUsuario = '$password_hash' WHERE codUsuario = '$codUsuario'";
      } elseif (!empty($_POST["claveUsuario"])) {

        $clave = $_POST["claveUsuario"];
        $password_hash = password_hash($clave, PASSWORD_DEFAULT);
        $qryModificarLocal = "UPDATE usuarios SET claveUsuario = '$password_hash' WHERE codUsuario = '$codUsuario'";
      } elseif (!empty($_POST["nombreUsuario"])) {

        $nombreUsuario = $_POST["nombreUsuario"];
        $qryModificarLocal = "UPDATE usuarios SET nombreUsuario = '$nombreUsuario' WHERE codUsuario = '$codUsuario'";
      }
      if (!empty($qryModificarLocal)) {
        $resultModificarLocal = mysqli_query($link, $qryModificarLocal);
        if ($resultModificarLocal) {
          $message = "Datos modificados exitosamente.";
          $message_type = 'success';
        } else {
          $message = "Hubo un error al modificar los datos. Por favor, inténtalo de nuevo.";
          $message_type = 'error';
        }
      }
    }
  } else {
    $message = "Las contraseñas no coinciden.";
    $message_type = 'error';
  }
}
?>


<div class="container">
  <form action="modificacion_perfil.php" method="POST" enctype="multipart/form-data" class="mt-3">
    <div class="form-group">
      <label for="nombre">Nombre de usuario:</label>
      <input type="email" class="form-control" id="nombre" name="nombreUsuario">
    </div>
    <div class="form-group">
      <label for="clave">Contraseña:</label>
      <textarea class="form-control" id="clave" name="claveUsuario"></textarea>
    </div>
    <div class="form-group">
      <label for="clave2">Repite la constraseña:</label>
      <textarea class="form-control" id="clave2" name="claveUsuario2"></textarea>
    </div>
    <button type="submit" name="modificarUsuario" class="btn btn-primary btn-block">Modificar datos Usuario</button>
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
              onclick="window.location.href='modificacion_perfil.php'">Cerrar</button>
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
              onclick="window.location.href='modificacion_perfil.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <a href="../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
</div>
</div>

<?php
include ("../includes/footer.php");
?>