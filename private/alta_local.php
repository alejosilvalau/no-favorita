<?php
$page = 'alta_local';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php");
  exit();
}

function subirImagen($file)
{
  $target_dir = '/uploads/';
  $target_file = $target_dir . basename($file["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  $check = getimagesize($file["tmp_name"]);
  if ($check === false) {
    return ["error" => "El archivo no es una imagen válida."];
  }

  if ($file["size"] > 5000000) {
    return ["error" => "El archivo es demasiado grande. Máximo 5MB."];
  }

  $allowed_formats = ["jpg", "jpeg", "png", "gif"];
  if (!in_array($imageFileType, $allowed_formats)) {
    return ["error" => "Sólo se permiten archivos JPG, JPEG, PNG y GIF."];
  }

  if (move_uploaded_file($file["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $target_file)) {
    return ["success" => $target_file];
  } else {
    return ["error" => "Hubo un error al subir tu archivo."];
  }
}

$message = "";
$message_type = "";
$usuarios = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscarUsuario"])) {
  $query = mysqli_real_escape_string($link, trim($_POST["buscarUsuario"]));
  $sql = "SELECT codUsuario, nombreUsuario FROM usuarios WHERE codUsuario = '$query' OR nombreUsuario LIKE '%$query%' AND tipoUsuario = 'Dueño de local'";
  $result = mysqli_query($link, $sql);

  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $usuarios[] = $row;
    }
    if (empty($usuarios)) {
      $message = "No se encontró ningún usuario con ese nombre o código.";
      $message_type = 'error';
    }
  } else {
    $message = "Error al ejecutar la búsqueda: " . mysqli_error($link);
    $message_type = 'error';
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["buscarUsuario"])) {
  $nombreLocal = trim($_POST["nombreLocal"]);
  $ubicacionLocal = trim($_POST["ubicacionLocal"]);
  $rubro = trim($_POST["rubroLocal"]);
  $usuarioLocal = trim($_POST["codUsuario"]);

  $imagenResult = subirImagen($_FILES["imagenLocal"]);
  if (isset($imagenResult["error"])) {
    $message = $imagenResult["error"];
    $message_type = 'error';
  } else {
    $imagenLocal = $imagenResult["success"];

    $buscarUsuario = "SELECT * FROM usuarios WHERE codUsuario = '$usuarioLocal'";
    $result = mysqli_query($link, $buscarUsuario);
    $vResult = mysqli_fetch_array($result);
    if (!$vResult) {
      $message = 'No existe un usuario con ese código';
      $message_type = 'error';
    } else {
      $busquedaLocal = "SELECT * FROM locales WHERE nombreLocal = '$nombreLocal' AND ubicacionLocal = '$ubicacionLocal'";
      $result2 = mysqli_query($link, $busquedaLocal);
      $vResult2 = mysqli_fetch_array($result2);
      if ($vResult2) {
        $message = 'El local ya existe';
        $message_type = 'error';
      } else {
        $query = "INSERT INTO locales (nombreLocal, ubicacionLocal, rubroLocal, codUsuario, imagenLocal) VALUES ('$nombreLocal', '$ubicacionLocal', '$rubro', '$usuarioLocal', '$imagenLocal')";
        if (mysqli_query($link, $query)) {
          $message = "Local agregado exitosamente.";
          $message_type = 'success';
        } else {
          $message = "Hubo un error al agregar el local. Por favor, inténtalo de nuevo.";
          $message_type = 'error';
        }
      }
    }
    mysqli_close($link);
  }
}
?>

<div class="container">
  <h2 class="mt-5 text-center">Dar de alta un local</h2>
  <!-- Formulario de búsqueda de usuario -->
  <form method="POST" action="alta_local.php">
    <div class="form-group">
      <label for="buscarUsuario">Buscar Usuario por nombre o correo:</label>
      <input type="text" class="form-control" id="buscarUsuario" name="buscarUsuario"
        placeholder="Introduce nombre o correo del usuario (Vacío para ver todos)">
      <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </div>
  </form>
  <?php if (!empty($usuarios)): ?>
    <form action="alta_local.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="codUsuario">Seleccionar Usuario:</label>
        <select class="form-control" id="codUsuario" name="codUsuario" required>
          <option value="" disabled selected>Selecciona un usuario</option>
          <?php foreach ($usuarios as $usuario): ?>
            <option value="<?php echo htmlspecialchars($usuario['codUsuario']); ?>">
              <?php echo htmlspecialchars($usuario['nombreUsuario']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Otros campos del formulario principal -->
      <div class="form-group">
        <label for="nombreLocal">Nombre del Local:</label>
        <input type="text" class="form-control" id="nombreLocal" name="nombreLocal" required>
      </div>
      <div class="form-group">
        <label for="ubicacionLocal">Ubicación del Local:</label>
        <textarea class="form-control" id="ubicacionLocal" name="ubicacionLocal" required></textarea>
      </div>
      <div class="form-group">
        <select class="form-select form-control" id="rubro" name="rubroLocal" required>
          <option value="" disabled selected>Selecciona un rubro</option>
          <option value="Indumentaria">Indumentaria</option>
          <option value="Perfumeria">Perfumería</option>
          <option value="Optica">Óptica</option>
          <option value="Comida">Comida</option>
          <option value="Computacion">Computación</option>
          <option value="Entretenimiento">Entretenimiento</option>
          <option value="Cine">Cine</option>
          <option value="Otros">Otros</option>
        </select>
      </div>
      <div class="form-group">
        <label for="imagenLocal">Imagen del Local (no habrá cambios en el cuadro luego de su selección):</label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="imagenLocal" name="imagenLocal">
          <label class="custom-file-label" for="imagenLocal">Elige una imagen...</label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Agregar Local</button>
      <a href="../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
    </form>
  <?php endif; ?>


  <!-- Modal de éxito -->
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
            <button type="button" class="btn btn-success" onclick="window.location.href='alta_local.php'">Cerrar</button>
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
            <button type="button" class="btn btn-danger" onclick="window.location.href='alta_local.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>


<?php
include ("../includes/footer.php");
?>