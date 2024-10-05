<?php
$page = 'alta_local';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

$message = "";
$message_type = "";
$locales = [];
$local = null;
$busquedaPorNombre = false;

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["buscarLocal"])) {
    $idLocal = trim($_POST["codLocal"]);
    $nombreLocal = trim($_POST["nombreLocal"]);

    if ($idLocal) {
      $qryBuscarLocal = "SELECT * FROM locales WHERE codLocal = '$idLocal'";
      $resultBuscarLocal = mysqli_query($link, $qryBuscarLocal);
      $local = mysqli_fetch_array($resultBuscarLocal);
      if (!$local) {
        $message = 'No existe un local con ese ID.';
        $message_type = 'error';
      }
    } elseif ($nombreLocal) {
      $qryBuscarLocal = "SELECT * FROM locales WHERE nombreLocal LIKE '%$nombreLocal%'";
      $resultBuscarLocal = mysqli_query($link, $qryBuscarLocal);
      while ($row = mysqli_fetch_array($resultBuscarLocal)) {
        $locales[] = $row;
      }

      if (count($locales) == 0) {
        $message = 'No existe un local con ese nombre.';
        $message_type = 'error';
      } else {
        $busquedaPorNombre = true;
      }
    }
  } elseif (isset($_POST["seleccionarLocal"])) {
    $idLocal = trim($_POST["codLocal"]);
    $qryBuscarLocal = "SELECT * FROM locales WHERE codLocal = '$idLocal'";
    $resultBuscarLocal = mysqli_query($link, $qryBuscarLocal);
    $local = mysqli_fetch_array($resultBuscarLocal);
  } elseif (isset($_POST["modificarLocal"])) {
    $idLocal = trim($_POST["codLocal"]);
    $nombreLocal = trim($_POST["nombreLocal"]);
    $ubicacionLocal = trim($_POST["ubicacionLocal"]);
    $rubro = trim($_POST["rubroLocal"]);

    $imagenLocal = $_POST["imagenActual"];
    if ($_FILES["imagenLocal"]["name"]) {
      $imagenResult = subirImagen($_FILES["imagenLocal"]);
      if (isset($imagenResult["error"])) {
        $message = $imagenResult["error"];
        $message_type = 'error';
      } else {
        $imagenLocal = $imagenResult["success"];
      }
    }

    if ($message_type !== 'error') {
      // Actualizar los datos del local
      $qryModificarLocal = "UPDATE locales SET nombreLocal = '$nombreLocal', ubicacionLocal = '$ubicacionLocal', rubroLocal = '$rubro', imagenLocal = '$imagenLocal' WHERE codLocal = '$idLocal'";
      $resultModificarLocal = mysqli_query($link, $qryModificarLocal);

      if ($resultModificarLocal) {
        $message = "Local modificado exitosamente.";
        $message_type = 'success';
      } else {
        $message = "Hubo un error al modificar el local. Por favor, inténtalo de nuevo.";
        $message_type = 'error';
      }
    }
  }

  mysqli_close($link);
}
?>


<div class="container">
  <h2 class="mt-5 text-center">Modificar Local</h2>
  <form action="modificacion_local.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="codLocal">Código del Local:</label>
      <input type="text" class="form-control" id="codLocal" name="codLocal" value="">
    </div>
    <div class="form-group">
      <label for="nombreLocal">Nombre del Local:</label>
      <input type="text" class="form-control" id="nombreLocal" name="nombreLocal" value="">
    </div>
    <button type="submit" name="buscarLocal" class="btn btn-primary btn-block">Buscar Local</button>
  </form>

  <?php if ($busquedaPorNombre && count($locales) > 0): ?>
    <form action="modificacion_local.php" method="POST" class="mt-3">
      <div class="form-group">
        <label for="seleccionarLocal">Seleccione el Local:</label>
        <select class="form-control" id="seleccionarLocal" name="codLocal">
          <?php foreach ($locales as $local): ?>
            <option value="<?php echo $local['codLocal']; ?>">
              <?php echo $local['codLocal'] . ' - ' . $local['nombreLocal']; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" name="seleccionarLocal" class="btn btn-primary btn-block">Seleccionar Local</button>
    </form>
  <?php elseif ($local): ?>
    <!-- Si el local ha sido encontrado por código o se ha seleccionado uno -->
    <form action="modificacion_local.php" method="POST" enctype="multipart/form-data" class="mt-3">
      <input type="hidden" name="codLocal" value="<?php echo $local['codLocal']; ?>">
      <div class="form-group">
        <label for="nombre">Nombre del Local:</label>
        <input type="text" class="form-control" id="nombre" name="nombreLocal"
          value="<?php echo $local['nombreLocal']; ?>" required>
      </div>
      <div class="form-group">
        <label for="ubicacion">Ubicación del Local:</label>
        <textarea class="form-control" id="ubicacion" name="ubicacionLocal"
          required><?php echo $local['ubicacionLocal']; ?></textarea>
      </div>
      <div class="form-group">
        <label for="rubro">Rubro del Local:</label>
        <select class="form-select form-control" id="rubro" name="rubroLocal" required>
          <option value="" disabled selected>Selecciona un rubro</option>
          <option value="Indumentaria" <?php echo $local['rubroLocal'] == 'Indumentaria' ? 'selected' : ''; ?>>
            Indumentaria</option>
          <option value="Perfumeria" <?php echo $local['rubroLocal'] == 'Perfumeria' ? 'selected' : ''; ?>>Perfumería
          </option>
          <option value="Optica" <?php echo $local['rubroLocal'] == 'Optica' ? 'selected' : ''; ?>>Óptica</option>
          <option value="Comida" <?php echo $local['rubroLocal'] == 'Comida' ? 'selected' : ''; ?>>Comida</option>
          <option value="Computacion" <?php echo $local['rubroLocal'] == 'Computacion' ? 'selected' : ''; ?>>Computación
          </option>
          <option value="Entretenimiento" <?php echo $local['rubroLocal'] == 'Entretenimiento' ? 'selected' : ''; ?>>
            Entretenimiento
          </option>
          <option value="Cine" <?php echo $local['rubroLocal'] == 'Cine' ? 'selected' : ''; ?>>Cine
          </option>
          <option value="Otros" <?php echo $local['rubroLocal'] == 'Otros' ? 'selected' : ''; ?>>Otros
          </option>
        </select>
      </div>
      <div class="form-group">
        <label for="imagen">Imagen (no habrá cambios en el cuadro luego de su selección): </label>

        <div class="custom-file">
          <input type="file" class="custom-file-input" id="imagen" name="imagenLocal">
          <label class="custom-file-label" for="imagen">Elige una imagen...</label>
        </div>
        <input type="hidden" name="imagenActual" value="<?php echo $local['imagenLocal']; ?>">
      </div>
      <button type="submit" name="modificarLocal" class="btn btn-primary btn-block">Modificar Local</button>
    </form>
  <?php endif; ?>

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
              onclick="window.location.href='modificacion_local.php'">Cerrar</button>
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
              onclick="window.location.href='modificacion_local.php'">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <a href="../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Inicio</a>
</div>

<?php
include ("../includes/footer.php");
?>