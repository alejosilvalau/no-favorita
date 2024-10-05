<?php
include ("../includes/conexion.inc");
include ("../includes/sesiones.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['nombreUsuario']);
  $password = trim($_POST['claveUsuario']);
  if (empty($email) || empty($password)) {
    $_SESSION['message'] = 'Todos los campos son obligatorios.';
    $_SESSION['message_type'] = 'error';
  } else {
    $busca_usuario = "SELECT * FROM usuarios WHERE nombreUsuario = '$email'";
    $resultado = mysqli_query($link, $busca_usuario);
    $usuario = mysqli_fetch_assoc($resultado);

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
    mysqli_free_result($resultado);
  }

  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: white;
    }

    .container {
      max-width: 500px;
      margin-top: 50px;
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.3);
      box-shadow: none;
    }

    .btn-primary {
      background-color: #6a11cb;
      border: none;
      margin-bottom: 10px;
    }

    .btn-primary:hover {
      background-color: #2575fc;
    }

    label {
      font-weight: bold;
    }

    .alert-danger {
      background-color: #ff4d4d;
      color: white;
      border-radius: 5px;
      padding: 10px;
      margin-top: 20px;
      border: 1px solid #ff0000;
    }

    .alert-success {
      background-color: #00cc00;
      color: white;
      border-radius: 5px;
      padding: 10px;
      margin-top: 20px;
      border: 1px solid #00b300;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2 class="mt-5 text-center">Iniciar Sesion</h2>
    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="nombreUsuario" autocomplete="true" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" class="form-control" id="password" name="claveUsuario" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Entrar</button>
      <div>No estás registrado? <a href="sign-up.php" style="color: yellow">Registrarse</a></div>
    </form>
    <?php if (isset($_SESSION['message']) && $_SESSION['message_type'] == 'error'): ?>
      <div class="alert alert-danger text-center">
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

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>