<?php
$page = 'gestionar_solicitud';
include ("../includes/navbar.php");

// Verificar que el usuario sea dueño de local
if ($_SESSION['tipoUsuario'] !== 'Dueño de local') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}

$message = '';
$message_type = '';

// Aprobar o rechazar uso de promoción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $codUsuario = $_POST["codCliente"];
  $codPromo = $_POST["codPromo"];
  $accion = $_POST["accion"];

  // Query para actualizar el estado del uso de la promoción
  $nuevoEstado = ($accion == 'aprobar') ? 'aceptada' : 'rechazada';
  $queryActualizar = "UPDATE uso_promociones SET estado = '$nuevoEstado' WHERE codCliente = '$codUsuario' AND codPromo = '$codPromo'";

  if (mysqli_query($link, $queryActualizar)) {
    if ($accion == 'aprobar') {
      // Sumar 1 al atributo cantUsadas en la tabla promociones
      $querySumar = "UPDATE promociones SET cantUsadas = cantUsadas + 1 WHERE codPromo = '$codPromo'";
      mysqli_query($link, $querySumar);

      // Obtener el contadorCategoria actual del cliente
      $queryContador = "SELECT contadorCategoria FROM usuarios WHERE codUsuario = '$codUsuario'";
      $resultContador = mysqli_query($link, $queryContador);
      $rowContador = mysqli_fetch_assoc($resultContador);
      $contadorActual = $rowContador['contadorCategoria'];

      // Incrementar el contador
      $contadorNuevo = $contadorActual + 1;

      // Actualizar el contadorCategoria del cliente
      $queryActualizarContador = "UPDATE usuarios SET contadorCategoria = '$contadorNuevo' WHERE codUsuario = '$codUsuario'";
      mysqli_query($link, $queryActualizarContador);

      // Verificar si el contador alcanza los umbrales para actualizar la categoría del cliente
      if ($contadorNuevo == 10) {
        $queryActualizarCategoria = "UPDATE usuarios SET categoriaCliente = 'Medium' WHERE codUsuario = '$codUsuario'";
        mysqli_query($link, $queryActualizarCategoria);
      } elseif ($contadorNuevo == 20) {
        $queryActualizarCategoria = "UPDATE usuarios SET categoriaCliente = 'Premium' WHERE codUsuario = '$codUsuario'";
        mysqli_query($link, $queryActualizarCategoria);
      }
    }
    $message = "La promoción ha sido " . (($accion == 'aprobar') ? 'aceptada' : 'rechazada') . " correctamente.";
    $message_type = 'success';
  } else {
    $message = "Hubo un error al actualizar la promoción. Por favor, inténtalo de nuevo.";
    $message_type = 'error';
  }
}

// Obtener todos los uso_promociones en estado "Enviada" para el local del dueño
$codUsuario = $_SESSION['codUsuario'];
$queryPromociones = "SELECT u.codCliente, u.codPromo, u.fechaUsoPromo, u.estado, p.textoPromo
                     FROM uso_promociones u
                     INNER JOIN promociones p ON u.codPromo = p.codPromo
                     WHERE u.estado = 'Enviada' AND p.codLocal IN (SELECT codLocal FROM locales WHERE codUsuario = '$codUsuario')";
$resultPromociones = mysqli_query($link, $queryPromociones);
?>

<div class="container">
  <h2 class="mt-5 text-center">Gestión de Uso de Promociones</h2>
  <div class="table-container mt-50">
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
      <table class="table table-striped table-hover">
        <thead class="thead-dark">
          <tr>
            <th>Código Cliente</th>
            <th>Código Promoción</th>
            <th>Texto Promoción</th>
            <th>Fecha de Uso</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($resultPromociones)): ?>
            <tr>
              <td><?php echo $row['codCliente']; ?></td>
              <td><?php echo $row['codPromo']; ?></td>
              <td><?php echo $row['textoPromo']; ?></td>
              <td><?php echo $row['fechaUsoPromo']; ?></td>
              <td class="btn-acciones d-flex" style="justify-content: space-between;">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                  <input type="hidden" name="codCliente" value="<?php echo $row['codCliente']; ?>">
                  <input type="hidden" name="codPromo" value="<?php echo $row['codPromo']; ?>">
                  <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-sm">Aprobar</button>
                </form>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                  <input type="hidden" name="codCliente" value="<?php echo $row['codCliente']; ?>">
                  <input type="hidden" name="codPromo" value="<?php echo $row['codPromo']; ?>">
                  <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <a href="../public/home.php" class="btn btn-secondary btn-block mt-3">Volver al Home</a>
</div>

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
            onclick="window.location.href='gestionar_solicitud.php'">Cerrar</button>
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
            onclick="window.location.href='gestionar_solicitud.php'">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php
mysqli_free_result($resultPromociones);
unset($message);
mysqli_close($link);
include ("../includes/footer.php");
?>