<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexion.inc");
include("sesiones.php");

require_once '../vendor/autoload.php';

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['HTTP_HOST'] == 'localhost') {
  $dotenv = Dotenv::createImmutable('../');
  $dotenv->load();
}

$message_footer = '';
$message_type_footer = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['mailUsuario'];
  $nom = $_POST['name'];
  $ape = $_POST['apellido'];
  $con = $_POST['consulta'];
  $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '/';

  $mail = new PHPMailer(true);

  try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de tu dominio
    $mail->SMTPAuth = true;
    $mail->Username = 'nofavorita2@gmail.com';  // Cuenta válida para autenticación
    $mail->Password = $_ENV['MAIL_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinatarios
    $mail->setFrom($email, $nom . ' ' . $ape);  // Dirección del remitente (puede no existir)
    $mail->addAddress('nofavorita2@gmail.com', 'No Favorita');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Consulta No Favorita';
    $mail->Body = "<p><strong>Nombre:</strong> $nom $ape</p>
                       <p><strong>Email:</strong> $email</p>
                       <p><strong>Consulta:</strong></p>
                       <p>$con</p>";
    $mail->AltBody = "Nombre: $nom $ape\nEmail: $email\nConsulta:\n$con";

    $mail->send();
    $_SESSION['message_footer'] = 'Correo enviado con éxito';
    $_SESSION['message_type_footer'] = 'success';
  } catch (Exception $e) {
    $_SESSION['message_footer'] = "Error al enviar el correo: {$mail->ErrorInfo}";
    $_SESSION['message_type_footer'] = 'error';
  }

  header('Location: ' . htmlspecialchars($redirect_to));
  exit;
}
