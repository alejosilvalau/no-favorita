<?php
$page = 'eliminacion_local';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es administrador
  exit();
}

$message = "";
$message_type = "";
$locales = [];
$local = null;
$busquedaPorNombre = false;

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
  } elseif (isset($_POST["confirmar_eliminar"])) {
    $idLocal = trim($_POST["codLocal"]);

    $qryEliminarUsoPromociones = "DELETE uso FROM uso_promociones uso INNER JOIN promociones promo ON uso.codPromo = promo.codPromo WHERE promo.codLocal = '$idLocal'";
    $resultEliminarUsoPromociones = mysqli_query($link, $qryEliminarUsoPromociones);

    $qryEliminarPromociones = "DELETE FROM promociones WHERE codLocal = '$idLocal'";
    $resultEliminarPromociones = mysqli_query($link, $qryEliminarPromociones);

    if ($resultEliminarPromociones && $resultEliminarUsoPromociones) {

      $qryEliminarLocal = "DELETE FROM locales WHERE codLocal = '$idLocal'";
      $resultEliminarLocal = mysqli_query($link, $qryEliminarLocal);

      if ($resultEliminarLocal) {
        $message = "Local y todas las promociones asociadas eliminadas correctamente.";
        $message_type = 'success';
        $local = null;
      } else {
        $message = "Hubo un error al eliminar el local. Por favor, inténtalo de nuevo.";
        $message_type = 'error';
      }
    } else {
      $message = "Hubo un error al eliminar las promociones y uso de promociones asociados al local. Por favor, inténtalo de nuevo.";
      $message_type = 'error';
    }
  }

  mysqli_close($link);
}
?>

<div class="container">
  <h2 class="mt-5 text-center">Eliminar Local</h2>
  <form action="eliminacion_local.php" method="POST">
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
    <form action="eliminacion_local.php" method="POST" class="mt-3">
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
    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">Información del Local</h5>
        <p><strong>Código:</strong> <?php echo $local['codLocal']; ?></p>
        <p><strong>Nombre:</strong> <?php echo $local['nombreLocal']; ?></p>
        <p><strong>Ubicación:</strong> <?php echo $local['ubicacionLocal']; ?></p>
        <p><strong>Rubro:</strong> <?php echo $local['rubroLocal']; ?></p>

        <h5 class="card-title mt-4">Confirmación de Eliminación</h5>
        <p>¿Está seguro que desea eliminar el local "<?php echo $local['nombreLocal']; ?>" y todas las promociones
          asociadas?</p>
        <form action="eliminacion_local.php" method="POST">
          <input type="hidden" name="codLocal" value="<?php echo $local['codLocal']; ?>">
          <button type="submit" name="confirmar_eliminar" class="btn btn-danger">Confirmar Eliminación</button>
          <button type="submit" name="cancelar" class="btn btn-secondary ml-2">Cancelar</button>
        </form>
      </div>
    </div>
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
              onclick="window.location.href='eliminacion_local.php'">Cerrar</button>
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
              onclick="window.location.href='eliminacion_local.php'">Cerrar</button>
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