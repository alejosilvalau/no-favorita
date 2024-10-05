<?php
$page = 'seccion_administrador';
include ("../includes/navbar.php");

if ($_SESSION['tipoUsuario'] !== 'administrador') {
  header("Location: ../public/home.php"); // Redirigir si no es dueño de local
  exit();
}
?>


<div class="container">
  <h2 class="mt-5 text-center">Gestión de Locales</h2>
  <div class="row mt-5">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dar de alta un local</h5>
          <p class="card-text">Agrega un nuevo local al sistema.</p>
          <a href="alta_local.php" class="btn btn-primary btn-block">Agregar Local</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Modificar un local</h5>
          <p class="card-text">Edita la información de un local existente.</p>
          <a href="modificacion_local.php" class="btn btn-primary btn-block">Modificar Local</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dar de baja un local</h5>
          <p class="card-text">Elimina un local del sistema.</p>
          <a href="eliminacion_local.php" class="btn btn-primary btn-block">Eliminar Local</a>
        </div>
      </div>
    </div>
  </div>
  <h2 class="mt-5 text-center">Gestión de Novedades</h2>
  <div class="row mt-5">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Crear una novedad</h5>
          <p class="card-text">Genera una novedad para el shopping.</p>
          <a href="alta_novedades.php" class="btn btn-primary btn-block">Agregar Novedad</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Modificar una Novedad</h5>
          <p class="card-text">Edita la información de una novedad existente.</p>
          <a href="modificacion_novedades.php" class="btn btn-primary btn-block">Modificar Novedad</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dar de baja una novedad</h5>
          <p class="card-text">Elimina una novedad del shopping.</p>
          <a href="eliminacion_novedades.php" class="btn btn-primary btn-block">Eliminar Novedad</a>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
include ("../includes/footer.php"); ?>