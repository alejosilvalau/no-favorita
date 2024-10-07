<?php
$page = 'gestion_promocion';
include("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'Dueño de local') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}
?>

<div class="container ">
  <h2 class="mt-5 text-center">GESTIÓN DE PROMOCIONES</h2>
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">ALTA PROMOCIÓN</h5>
          <p class="card-text">Agrega una nueva promoción al sistema.</p>
          <a href="../private/alta_descuento.php" class="btn btn-primary btn-block submit">Generar Promoción</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">BAJA PROMOCIÓN</h5>
          <p class="card-text">Elimina una promoción del sistema.</p>
          <a href="../private/baja_descuento.php" class="btn btn-primary btn-block submit">Eliminar Promoción</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">GESTIÓN SOLICITUD</h5>
          <p class="card-text">Gestionar solicitudes de descuentos.</p>
          <a href="../private/gestionar_solicitud.php" class="btn btn-primary btn-block submit">Gestionar
            Solicitudes</a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<?php
include("../includes/footer.php");
?>