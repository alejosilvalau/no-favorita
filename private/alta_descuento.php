<?php
$page = 'alta_descuento';
include ("../includes/navbar.php");

// Verificar que el usuario sea dueño de local
if ($_SESSION['tipoUsuario'] !== 'Dueño de local') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $textoPromo = trim($_POST["textoPromo"]);
  $fechaDesdePromo = $_POST["fechaDesdePromo"];
  $fechaHastaPromo = $_POST["fechaHastaPromo"];
  $categoriaCliente = $_POST["categoriaCliente"];
  $diasSemana = isset($_POST["diasSemana"]) ? implode(",", $_POST["diasSemana"]) : "";
  $estadoPromo = "pendiente";

  $codLocal = $_POST["codLocal"];

  // Validaciones de fechas y texto de promoción
  if ($fechaDesdePromo > $fechaHastaPromo || $fechaDesdePromo < date("Y-m-d")) {
    $message = "La fecha desde debe ser menor a la fecha hasta y mayor o igual a hoy.";
    $message_type = "error";
  } else {
    // Verificar si la promoción ya existe con esas fechas y texto
    $buscarPromo = "SELECT * FROM promociones WHERE textoPromo = '$textoPromo' AND fechaDesdePromo = '$fechaDesdePromo' AND fechaHastaPromo = '$fechaHastaPromo'";
    $result = mysqli_query($link, $buscarPromo);
    if (mysqli_num_rows($result) > 0) {
      $message = "Ya existe una promoción con ese texto y esas fechas.";
      $message_type = "error";
    } else {
      // Insertar la promoción en la base de datos
      $query = "INSERT INTO promociones (textoPromo, fechaDesdePromo, fechaHastaPromo, categoriaCliente, diasSemana, estadoPromo, codLocal, cantUsadas) 
                VALUES ('$textoPromo', '$fechaDesdePromo', '$fechaHastaPromo', '$categoriaCliente', '$diasSemana', '$estadoPromo', '$codLocal', 0)";

      if (mysqli_query($link, $query)) {
        $message = "Promoción agregada exitosamente.";
        $message_type = "success";
      } else {
        $message = "Hubo un error al agregar la promoción. Por favor, inténtalo de nuevo.";
        $message_type = "error";
      }
    }
  }

  mysqli_close($link);
}
?>

<div class="container">
  <h2 class="mt-5 text-center">Alta de Promoción</h2>
  <?php if ($message): ?>
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
              <button type="button" class="btn btn-success"
                onclick="window.location.href='alta_descuento.php'">Cerrar</button>
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
                onclick="window.location.href='alta_descuento.php'">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <form action="alta_descuento.php" method="POST">
      <div class="form-group">
        <label for="textoPromo">Texto de la Promoción:</label>
        <input type="text" class="form-control" id="textoPromo" name="textoPromo" required>
      </div>
      <div class="form-group">
        <label for="fechaDesdePromo">Fecha Desde:</label>
        <input type="date" class="form-control" id="fechaDesdePromo" name="fechaDesdePromo" required>
      </div>
      <div class="form-group">
        <label for="fechaHastaPromo">Fecha Hasta:</label>
        <input type="date" class="form-control" id="fechaHastaPromo" name="fechaHastaPromo" required>
      </div>
      <div class="form-group">
        <label for="categoriaCliente">Categoría de Cliente:</label>
        <select class="form-control" id="categoriaCliente" name="categoriaCliente" required>
          <option value="Inicial">Inicial</option>
          <option value="Medium">Medium</option>
          <option value="Premium">Premium</option>
        </select>
      </div>
      <div class="form-group">
        <label for="diasSemana">Días de la Semana:</label><br>
        <input type="checkbox" id="lunes" name="diasSemana[]" value="0">
        <label for="lunes"> Lunes</label><br>
        <input type="checkbox" id="martes" name="diasSemana[]" value="1">
        <label for="martes"> Martes</label><br>
        <input type="checkbox" id="miercoles" name="diasSemana[]" value="2">
        <label for="miercoles"> Miércoles</label><br>
        <input type="checkbox" id="jueves" name="diasSemana[]" value="3">
        <label for="jueves"> Jueves</label><br>
        <input type="checkbox" id="viernes" name="diasSemana[]" value="4">
        <label for="viernes"> Viernes</label><br>
        <input type="checkbox" id="sabado" name="diasSemana[]" value="5">
        <label for="sabado"> Sábado</label><br>
        <input type="checkbox" id="domingo" name="diasSemana[]" value="6">
        <label for="domingo"> Domingo</label><br>
      </div>
      <div class="form-group">
        <label for="codLocal">Código de Local y Nombre de Local:</label>
        <select class="form-control" id="codLocal" name="codLocal" required>
          <?php
          // Obtener los locales del usuario actual
          $codUsuario = $_SESSION['codUsuario'];
          $queryLocales = "SELECT * FROM locales WHERE codUsuario = '$codUsuario'";
          $resultLocales = mysqli_query($link, $queryLocales);
          while ($row = mysqli_fetch_assoc($resultLocales)) {
            echo "<option value='" . $row['codLocal'] . "'>" . $row['codLocal'] . " - " . $row['nombreLocal'] . "</option>";
          }
          ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Agregar Promoción</button>
      <a href="../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Home</a>
    </form>
  <?php endif; ?>
</div>


<?php
include ("../includes/footer.php");
?>