<?php
include("../includes/conexion.inc");
include("../includes/sesiones.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>No Favorita</title>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.png">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    rel="stylesheet">
  <link href="https://db.onlinewebfonts.com/c/3591a95e9abd510d170b0ba4077e70d3?family=Nimbus+Sans+TW01Con" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css"
    rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/avenir-lt-pro" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/navbar.css">
  <link rel="stylesheet" href="../css/footer.css">

  <?php
  // Incluir hojas de estilo específicas para cada página
  if ($page == 'home') {
    echo '<link rel="stylesheet" href="../css/home.css">';
  } elseif ($page == 'locales' || $page == 'baja_descuento' || $page == 'promocion_local' || $page == 'promociones' || $page == 'reportes' || $page == 'historial') {
    echo '<link rel="stylesheet" href="../css/listados.css">';
    if ($page  == 'historial') {
      echo '<link rel="stylesheet" href="../css/historial.css">';
    }
  } elseif ($page == 'promociones') {
    echo '<link rel="stylesheet" href="../css/promociones.css">';
  } elseif ($page == 'alta_local' || $page == 'alta_novedades' || $page == 'eliminacion_local' || $page == 'eliminacion_novedades' || $page == 'gestionar_descuentos' || $page == 'modificacion_local' || $page == 'modificacion_novedades' || $page == 'seccion_administrador' || $page == 'validar_duenio' || $page == 'alta_descuento' || $page == 'gestion_promocion' || $page == 'gestionar_solicitud' || $page == 'modificacion_perfil') {
    echo '<link rel="stylesheet" href="../css/admin_locales.css">';
    if ($page == 'gestionar_descuentos') {
      echo '<link rel="stylesheet" href="../css/gestionar_descuentos.css">';
    } elseif ($page == 'validar_duenio') {
      echo '<link rel="stylesheet" href="../css/validar_dueño.css">';
    } elseif ($page == 'gestionar_solicitud') {
      echo '<link rel="stylesheet" href="../css/gestionar_solicitud.css">';
    }
  } elseif ($page == 'login') {
    echo '<link rel="stylesheet" href="../css/login.css">';
  } elseif ($page == 'sign-up') {
    echo '<link rel="stylesheet" href="../css/sign-up.css">';
  } elseif ($page == 'mapa_sitio') {
    echo '<link rel="stylesheet" href="../css/mapa_sitio.css">';
  }

  ?>

</head>


<body>


  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>