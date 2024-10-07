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
      $query = "UPDATE usuarios SET aprobado = true WHERE codUsuario = ?";
      $accion = 'validada';
      $alertClass = 'alert success'; // Clase de alerta para éxito
    } elseif ($_POST['action'] == 'denegar') {
      $query = "UPDATE usuarios SET aprobado = 2 WHERE codUsuario = ?";
      $accion = 'denegada';
      $alertClass = 'alert danger'; // Clase de alerta para error
    }

    if ($stmt = mysqli_prepare($link, $query)) {
      mysqli_stmt_bind_param($stmt, "i", $idUsuario);

      if (mysqli_stmt_execute($stmt)) {
        $message = "La solicitud del usuario con ID $idUsuario ha sido $accion correctamente.";
      } else {
        $message = "Error al realizar la acción para el usuario con ID $idUsuario.";
        $alertClass = 'alert danger'; // Asegurar que la clase de alerta sea de error en caso de fallo
      }
      mysqli_stmt_close($stmt);
    } else {
      $message = 'Error al preparar la consulta.';
      $alertClass = 'alert danger'; // Asegurar que la clase de alerta sea de error en caso de fallo
    }
  }
}

// Consulta actualizada para asegurarse de que se seleccionen solo usuarios no aprobados
$query = "SELECT * FROM usuarios WHERE tipoUsuario = 'Dueño de local' AND aprobado = false";
$resultado = mysqli_query($link, $query);
?>

<div class="container">
  <h1>Usuarios Pendientes de Aprobación</h1>
  <?php if (!empty($message)): ?>
    <div class="<?php echo $alertClass; ?> text-center">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>
  <?php
  if (mysqli_num_rows($resultado) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
      echo "<div class='usuario-container'>";
      echo "<p><strong>Codigo de Usuario:</strong> " . $fila["codUsuario"] . "</p>";
      echo "<p><strong>Email:</strong> " . $fila["nombreUsuario"] . "</p>";
      echo "<p><strong>Tipo de Usuario:</strong> " . $fila["tipoUsuario"] . "</p>";
      echo "<form action='validar_dueño.php' method='POST'>";
      echo "<input type='hidden' name='codUsuario' value='" . $fila['codUsuario'] . "'>";
      echo "<button type='submit' name='action' value='validar' class='btn-validar'>Validar</button>";
      echo "<button type='submit' name='action' value='denegar' class='btn-denegar'>Denegar</button>";
      echo "</form>";
      echo "</div>";
    }
  } else {
    echo "No se encontraron usuarios que cumplan los criterios.";
  }
  mysqli_close($link);
  ?>
</div>
<div class="text-center">
  <a href="../public/home.php" class="btn btn-secondary mt-3">Volver al Home</a>
</div>

<?php
include("../includes/footer.php");
?>