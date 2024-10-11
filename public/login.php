<?php
$page = 'login';
include("../includes/navbar.php");

include("../includes/conexion.inc");
include("../includes/sesiones.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['nombreUsuario']);
  $password = trim($_POST['claveUsuario']);
  if (empty($email) || empty($password)) {
    $_SESSION['message'] = 'Todos los campos son obligatorios.';
    $_SESSION['message_type'] = 'error';
  } else {
    $busca_usuario = "SELECT * FROM usuarios WHERE \"nombreUsuario\" = '$email'";
    $resultado = pg_query($link, $busca_usuario);
    $usuario = pg_fetch_assoc($resultado);

    if ($usuario) {
      if (password_verify($password, $usuario['claveUsuario'])) {
        if (($usuario['aprobado'] || $usuario['tipoUsuario'] == 'Cliente' || $usuario['tipoUsuario'] == 'administrador') && $usuario['is_validated']) {
          $_SESSION['tipoUsuario'] = $usuario['tipoUsuario'];
          $_SESSION['codUsuario'] = $usuario['codUsuario'];
          $_SESSION['categoriaCliente'] = $usuario['categoriaCliente'];
          $_SESSION['nombreUsuario'] = $usuario['nombreUsuario'];
          header('Location: home.php');
          exit();
        } elseif ($usuario['is_validated'] == 0) {
          $_SESSION['message'] = 'El usuario todavía no ha sido validado. Por favor, revisa tu correo para confirmar tu registro.';
          $_SESSION['message_type'] = 'error';
        } else {
          $_SESSION['message'] = 'El usuario todavía no ha sido aprobado por un administrador.';
          $_SESSION['message_type'] = 'error';
        }
      } else {
        $_SESSION['message'] = 'La contraseña ingresada es incorrecta.';
        $_SESSION['message_type'] = 'error';
      }
    } else {
      $_SESSION['message'] = 'No existe un usuario con ese nombre.';
      $_SESSION['message_type'] = 'error';
    }
    pg_free_result($resultado);
  }

  pg_close($link);
}
?>




<div class="container">
  <h2 class="mt-5 text-center">INICIAR SESION</h2>
  <form action="login.php" method="POST">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" name="nombreUsuario" autocomplete="true" required>
    </div>
    <div class="form-group">
      <label for="password">Contraseña:</label>
      <input type="password" class="form-control" id="password" name="claveUsuario" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block submit">Entrar</button>
    <div>No estás registrado? <a class="registar" href="sign-up.php"">Registrarse</a></div>
  </form>
  <?php if (isset($_SESSION['message']) && $_SESSION['message_type'] == 'error'): ?>
    <div class=" alert alert-danger text-center">
        <?php
        echo $_SESSION['message'];
        unset($_SESSION['message']);
        ?>
    </div>
  <?php elseif (isset($_SESSION['message']) && $_SESSION['message_type'] == 'success'): ?>
    <div class="alert alert-success text-center">
      <?php
      echo $_SESSION['message'];
      unset($_SESSION['message']);
      ?>
    </div>
  <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>