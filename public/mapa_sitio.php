<?php
$page = 'mapa_sitio';
include ("../includes/navbar.php");
?>

<div class="container mt-4">
  <h1>Mapa del Sitio</h1>
  <ul>
    <li><a href="../public/home.php">Inicio</a></li>
    <?php if ($_SESSION['tipoUsuario'] == 'administrador'): ?>
      <li><a href="../private/seccion_administrador.php">Panel de Administrador</a></li>
      <li><a href="../private/gestionar_descuentos.php">Gestión de descuentos</a></li>
      <li><a href="../private/validar_dueño.php">Validación de dueños</a></li>
      <li><a href="../private/reportes.php">Reportes</a></li>
    <?php endif; ?>
    <?php if ($_SESSION['tipoUsuario'] == 'Dueño de local'): ?>
      <li><a href="../duenio/gestion_promocion.php">Gestionar Promociones</a></li>
      <li><a href="../private/reportes.php">Reportes</a></li>
    <?php endif; ?>
    <li><a href="../public/locales.php">Locales</a></li>
    <li><a href="../public/promociones.php">Promociones</a></li>
    <li><a href="../public/login.php">Registrarse / Iniciar Sesión / Cerrar Sesión</a></li>
  </ul>
</div>


<?php
include ("../includes/footer.php");
?>