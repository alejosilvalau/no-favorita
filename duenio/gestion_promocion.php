<?php
$page = 'gestion_promocion';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'Dueño de local') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}
?>

<div class="container">
  <h2 class="mt-5 text-center">Gestión de Promociones</h2>
  <div class="row mt-5">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dar de alta una promocion</h5>
          <p class="card-text">Agrega una nueva promoción al sistema.</p>
          <a href="./alta_descuento.php" class="btn btn-primary btn-block">Generar Promoción</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dar de baja una promoción</h5>
          <p class="card-text">Elimina una promoción del sistema.</p>
          <a href="./baja_descuento.php" class="btn btn-primary btn-block">Eliminar Promoción</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Gestión de Solicitudes</h5>
          <p class="card-text">Gestionar solicitudes de descuentos de los clientes.</p>
          <a href="./gestionar_solicitud.php" class="btn btn-primary btn-block">Gestionar
            Solicitudes</a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<?php
include ("../includes/footer.php");
?>