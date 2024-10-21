<?php
include("../includes/header.php");
?>

<nav class="navbar navbar-expand-lg navbar-white">

  <a href="../public/home.php"><img class="navbar-brand" src="../assets/no-favorita-logo-v3.png" alt="no-favorita-logo"></a>


  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="../public/home.php">INICIO</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="../public/locales.php">LOCALES</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../public/promociones.php">PROMOCIONES</a>
      </li>
      <li class="nav-item">
        <a href="../public/mapa_sitio.php" class="nav-link">MAPA DEL SITIO</a>
      </li>
      <?php if ($_SESSION['tipoUsuario'] == 'No registrado'): ?>
        <li class="nav-item"><a href="login.php" class="nav-link">INICIAR SESIÓN</a></li>
      <?php elseif (isset($_SESSION['tipoUsuario'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-person-circle text-white"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right dropdown" aria-labelledby="userDropdown">
            <div class="dropdown-header">
              <strong><?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?></strong>
            </div>
            <div class="dropdown-item">
              Tipo de Usuario: <strong><?php echo ucfirst(htmlspecialchars($_SESSION['tipoUsuario'])); ?></strong>
            </div>

            <?php if ($_SESSION['categoriaCliente'] && (($_SESSION['tipoUsuario'] == "Cliente") || ($_SESSION['tipoUsuario'] == "Dueño de local"))): ?>
              <div class="dropdown-item">
                Categoría: <strong><?php echo htmlspecialchars($_SESSION['categoriaCliente']); ?></strong>
              </div>
              <div class="dropdown-divider"></div>
              <div>
                <a class="dropdown-item" href="../public/modificacion_perfil.php">Modificación Perfil</a>
              </div>
              <div>
                <a class="dropdown-item" href="../public/historial.php">Historial de Promociones</a>
              </div>
            <?php elseif ($_SESSION['tipoUsuario'] == 'administrador'): ?>
              <div>
                <a class="dropdown-item" href="../private/seccion_administrador.php">Panel de Administrador</a>
              </div>
              <div>
                <a class="dropdown-item" href="../private/gestionar_descuentos.php">Gestión de descuentos</a>
              </div>
              <div>
                <a class="dropdown-item" href="../private/validar_duenio.php">Validación de dueños</a>
              </div>
              <div>
                <a class="dropdown-item" href="../private/reportes.php">Reportes</a>
              </div>
            <?php endif; ?>
            <?php if ($_SESSION['categoriaCliente'] && (($_SESSION['tipoUsuario'] == "Dueño de local"))): ?>
              <div>
                <a class="dropdown-item" href="../private/gestion_promocion.php">Gestionar Promociones</a>
              </div>
              <div>
                <a class="dropdown-item" href="../private/reportes.php">Reportes</a>
              </div>
            <?php endif; ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../public/logout.php">Cerrar Sesión</a>
          </div>
        </li>
      <?php endif; ?>

    </ul>
  </div>

</nav>