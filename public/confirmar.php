<?php
include ('../includes/sesiones.php');
include('../includes/conexion.php'); // Archivo donde están tus credenciales de conexión a la base de datos

if (isset($_GET['token'])) {
  $token = $_GET['token'];

  // Verificar si la conexión se establece correctamente
  if ($link === false) {
    die("ERROR: No se pudo conectar a la base de datos. " . pg_last_error());
  }

  // Buscar el usuario con el token dado
  $qry = "SELECT * FROM usuarios WHERE validation_token = '$token'";
  $result = pg_query($link, $qry);

  if (!$result) {
    die("ERROR: No se pudo ejecutar la consulta. " . pg_last_error($link));
  }

  $user = pg_fetch_array($result);
  // Si se encuentra el usuario
  if ($user) {
    // Actualizar el estado del token a confirmado
    $update_qry = "UPDATE usuarios SET is_validated = 1 WHERE validation_token = '$token'";
    if (pg_query($link, $update_qry)) {
      $_SESSION['message'] = "Registro confirmado exitosamente. Puedes iniciar sesión ahora.";
      $_SESSION['message_type'] = "success";
      header("Location: login.php");
      exit();
    } else {
      $_SESSION['message'] = "Hubo un error al confirmar el registro. Por favor, inténtalo de nuevo.";
      $_SESSION['message_type'] = "error";
    }
  } else {
    $_SESSION['message'] = "Token inválido o expirado.";
    $_SESSION['message_type'] = "error";
  }
  pg_close($link);
} else {
  $_SESSION['message'] = "No se proporcionó un token.";
  $_SESSION['message_type'] = "error";
}
header("Location: sign-up.php");
exit();
?>