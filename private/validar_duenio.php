<?php
$page = 'validar_duenio';
include("../includes/navbar.php");
if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php");
  exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $idUsuario = $_POST['codUsuario'];
  if (isset($_POST['action'])) {
    if ($_POST['action'] == 'validar') {
      $query = "UPDATE usuarios SET aprobado = 1 WHERE \"codUsuario\" = $1";
      $accion = 'validada';
      $alertClass = 'alert success'; // Clase de alerta para éxito
    } elseif ($_POST['action'] == 'denegar') {
      $query = "UPDATE usuarios SET aprobado = 2 WHERE \"codUsuario\" = $1";
      $accion = 'denegada';
      $alertClass = 'alert danger'; // Clase de alerta para error
    }

    $stmt = pg_prepare($link, "my_query", $query);

    if ($stmt) {
      // Execute the prepared statement with parameters
      $result = pg_execute($link, "my_query", array($idUsuario));

      if ($result) {
        $message = "La solicitud del usuario con ID $idUsuario ha sido $accion correctamente.";
      } else {
        $message = "Error al realizar la acción para el usuario con ID $idUsuario.";
        $alertClass = 'alert-danger';
      }
    } else {
      $message = 'Error al preparar la consulta.';
      $alertClass = 'alert-danger'; 
    }
  }
}

// Consulta actualizada para asegurarse de que se seleccionen solo usuarios no aprobados
$query = "SELECT * FROM usuarios WHERE \"tipoUsuario\" = 'Dueño de local' AND aprobado = 0";
$resultado = pg_query($link, $query);
?>

<div class="container">
  <h1><strong>USUARIOS PENDIENTES DE APROBACIÓN</strong></h1>
  <?php if (!empty($message)): ?>
    <div class="<?php echo $alertClass; ?> text-center">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <?php
if (pg_num_rows($resultado) > 0) {
  while ($fila = pg_fetch_assoc($resultado)) {
      echo "<div class='usuario-container'>";
      echo "<p>Codigo de Usuario: " . $fila["codUsuario"] . "</p>";
      echo "<p>Email: " . $fila["nombreUsuario"] . "</p>";
      echo "<p>Tipo de Usuario: " . $fila["tipoUsuario"] . "</p>";
      echo "<form action='validar_duenio.php' method='POST'>";
      echo "<input type='hidden' name='codUsuario' value='" . $fila['codUsuario'] . "'>";
      echo "<button type='submit' name='action' value='validar' class='btn-validar'>Validar</button>";
      echo "<button type='submit' name='action' value='denegar' class='btn-denegar'>Denegar</button>";
      echo "</form>";
      echo "</div>";
    }
  } else {
    echo "No se encontraron usuarios que cumplan los criterios.";
  }
pg_close($link);
  ?>
</div>
<?php
if (pg_num_rows($resultado) <= 0) {
  echo "<div class='filler'></div>";
}
include("../includes/footer.php");
?>