<?php

$page = 'sign-up';
include("../includes/navbar.php");

include('../includes/sesiones.php');
include('../includes/conexion.inc');


require_once '../vendor/autoload.php';

require '../libs/PHPMailer-master/src/Exception.php';
require '../libs/PHPMailer-master/src/PHPMailer.php';
require '../libs/PHPMailer-master/src/SMTP.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['HTTP_HOST'] == 'localhost') {
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();
}
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["nombreUsuario"]);
  $clave = trim($_POST["claveUsuario"]);
  $clave_confirmada = trim($_POST["confirm-password"]);
  $tipoUsuario = isset($_POST["aplica-dueño"]) ? "Dueño de local" : "Cliente";

  if ($clave != $clave_confirmada) {
    $message = 'Las contraseñas no coinciden';
    $alertClass = 'alert danger';
  } else {
    $password_hash = password_hash($clave, PASSWORD_DEFAULT);

    $qry = "SELECT * FROM usuarios WHERE \"nombreUsuario\" = '$email'";
    $result = pg_query($link, $qry) or die(pg_last_error($link));
    $vResult = pg_fetch_array($result);

    if ($vResult) {
      $message = 'El correo ya está registrado';
      $alertClass = 'alert danger';
    } else {
      $token = bin2hex(random_bytes(50));
      $insert_qry = "INSERT INTO usuarios (\"nombreUsuario\", \"claveUsuario\", \"tipoUsuario\", \"categoriaCliente\", validation_token, is_validated) VALUES ('$email', '$password_hash', '$tipoUsuario', 'Inicial', '$token', 0)";
      if (pg_query($link, $insert_qry)) {
        $message = 'Registro exitoso. Revisa tu correo para confirmar tu registro';
        $alertClass = 'alert success';
        $mail = new PHPMailer(true);
        try {
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'nofavorita2@gmail.com';
          $mail->Password = $_ENV['MAIL_PASS'];
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = 587;

          $mail->setFrom('nofavorita2@gmail.com', 'No Favorita');
          $mail->addAddress($email);

          $mail->isHTML(true);
          $mail->Subject = 'Confirmacion de Registro';
          $mail->Body = "Haz clic en el siguiente enlace para confirmar tu registro: <a href='https://www.nofavorita.social/public/confirmar.php?token=$token'>Confirmar Registro</a>";

          $mail->send();
        } catch (Exception $e) {
          $message = 'Hubo un error al enviar el correo de confirmación';
          $alertClass = 'alert danger';
        }
      } else {
        $message = 'Hubo un error al registrarse. Por favor, inténtalo de nuevo';
        $alertClass = 'alert danger';
      }
    }
    pg_close($link);
  }
}
?>


<div class="container">
  <h2 class="mt-5 text-center">REGISTRARSE</h2>
  <form action="sign-up.php" method="post">
    <div class="form-group">
      <label for="nombre">Email:</label>
      <input type="text" class="form-control" id="nombre" name="nombreUsuario" required>
    </div>
    <div class="form-group">
      <label for="password">Contraseña:</label>
      <input type="password" class="form-control" id="clave" name="claveUsuario" required>
    </div>
    <div class="form-group">
      <label for="confirm-password">Confirmar Contraseña:</label>
      <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
    </div>
    <div class="form-group checkbox-container">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="aplica-dueño" name="aplica-dueño">
        <label class="form-check-label" for="aplica-dueño">Aplicar para dueño</label>
      </div>
      <button type="submit" class="btn btn-primary btn-block submit">Registrarse</button>
  </form>
  <?php if (!empty($message)): ?>
    <div class="<?php echo $alertClass; ?> text-center">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
</div>
</div>

<?php include("../includes/footer.php"); ?>