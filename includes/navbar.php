<?php
include("../includes/header.php");
?>

<nav class="navbar navbar-expand-lg navbar-white">

  <a href="../public/home.php"><img class="navbar-brand" src="../assets/no-favorita-logo-v3.webp" alt="no-favorita-logo"></a>


  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
    aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="../public/home.php">INICIO</a>
      </li>
      <?php if ($_SESSION['tipoUsuario'] == 'administrador'): ?>
        <li class="nav-item">
          <a class="nav-link" href="../private/seccion_administrador.php">Panel de Administrador</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../private/gestionar_descuentos.php">Gestión de descuentos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../private/validar_dueño.php">Validación de dueños</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../private/reportes.php">Reportes</a>
        </li>
      <?php elseif ($_SESSION['tipoUsuario'] == 'Dueño de local'): ?>
        <li class="nav-item">
          <a class="nav-link" href="../duenio/gestion_promocion.php">Gestionar Promociones</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../private/reportes.php">Reportes</a>
        </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="../public/locales.php">LOCALES</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../public/promociones.php">PROMOCIONES</a>
      </li>
      <li class="nav-item">
        <a href="../public/mapa_sitio.php" class="nav-link">MAPA DEL SITIO</a>
      </li>
      <?php if (basename($_SERVER['PHP_SELF']) == 'promociones.php' || basename($_SERVER['PHP_SELF']) == 'locales.php'): ?>
        <li class="nav-item">
          <form class="form-inline my-2 my-lg-0" method="get"
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Buscar" aria-label="Buscar">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
          </form>
        </li>
      <?php endif; ?>
      <?php if ($_SESSION['tipoUsuario'] == 'No registrado'): ?>
        <li class="nav-item"><a href="login.php" class="nav-link">INICIAR SESIÓN</a></li>
      <?php elseif (isset($_SESSION['tipoUsuario'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <div class="dropdown-header">
              <strong><?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?></strong>
            </div>
            <div class="dropdown-item">
              Tipo de Usuario: <?php echo htmlspecialchars($_SESSION['tipoUsuario']); ?>
            </div>
            <?php if ($_SESSION['categoriaCliente'] && (($_SESSION['tipoUsuario'] == "Cliente") || ($_SESSION['tipoUsuario'] == "Dueño de local"))): ?>
              <div class="dropdown-item">
                Categoría: <?php echo htmlspecialchars($_SESSION['categoriaCliente']); ?>
              </div>
              <div>
                <a class="dropdown-item" href="../public/modificacion_perfil.php">Modificación Perfil</a>
              </div>
              <div>
                <a class="dropdown-item" href="../public/historial.php">Historial de Promociones</a>
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