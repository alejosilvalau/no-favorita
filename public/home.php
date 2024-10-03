<?php
$page = 'home';
include("../includes/navbar.php");

$consulta = "";

if (isset($_SESSION['tipoUsuario'])) {
  $categoriaCliente = $_SESSION['categoriaCliente'];
  $tipoUsuario = $_SESSION['tipoUsuario'];

  if ($categoriaCliente == 'Premium' || $tipoUsuario == 'Dueño de local' || $tipoUsuario == 'administrador') {
    $consulta = "SELECT * FROM novedades";
  } elseif ($categoriaCliente == 'Medium') {
    $consulta = "SELECT * FROM novedades WHERE categoriaCliente IN ('Medium', 'Inicial')";
  } elseif ($categoriaCliente == 'Inicial') {
    $consulta = "SELECT * FROM novedades WHERE categoriaCliente = 'Inicial'";
  }
}

if (!empty($consulta)) {
  $resultado = mysqli_query($link, $consulta);
} else {
  $resultado = false;
}

$hoy = date('Y-m-d');
$eliminarNovedadesQuery = "DELETE FROM novedades WHERE fechaHastaNovedad < '$hoy'";
$resultadoEliminar = mysqli_query($link, $eliminarNovedadesQuery);

if ($resultadoEliminar) {
  $message = "Novedades vencidas han sido eliminadas automáticamente.";
} else {
  $message = "Error al intentar eliminar novedades vencidas.";
}
?>

<div class="video">
  <iframe class="video-embed" src="https://player.vimeo.com/video/1015628916?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autoplay=1&amp;loop=1&amp;autopause=0&amp;muted=1&amp;controls=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write" title="no-favorita-video"></iframe>
  <script src="https://player.vimeo.com/api/player.js"></script>
  <div class="video-text">
    <h1 class="text-center video-title">
      Volvé a vivir <br />
      el centro de Rosario
    </h1>
    <a href="#novedades" class="video-button" alt="circled-chevron-down">
      <img width="50" height="50" src="../assets/arrow-down.png" alt="circled-chevron-down" />
    </a>
  </div>
</div>
<div class="d-flex justify-content-center mt-5">
  <div class="container" id="novedades">
    <h2 class="services-title text-center">Novedades</h2>
    <div class="row justify-content-center">
      <?php
      if ($resultado) {
        while ($novedad = mysqli_fetch_assoc($resultado)) {
      ?>
          <div class="col-md-4 mb-4">
            <div class='card'>
              <div class='card-body'>
                <h5 class='card-title'><?php echo $novedad['textoNovedad']; ?></h5>
                <p class='card-text'>Desde: <?php echo $novedad['fechaDesdeNovedad']; ?><br>Hasta: <?php echo $novedad['fechaHastaNovedad']; ?></p>
              </div>
            </div>
          </div>
      <?php
        }
      } else {
        echo "<p>No hay novedades disponibles</p>";
      }
      ?>
    </div>
  </div>
</div>
<div class="marcas">
  <div class="container d-flex flex-column justify-content-center align-items-center">
    <h2 class="marcas-title">CONOCÉ NUESTRAS MARCAS</h2>
    <hr>
    <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_dd5cf6254ffc4689aa1371f9719dc8db~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_dd5cf6254ffc4689aa1371f9719dc8db~mv2.png" alt="logo webfav1-29" style="width: 186px; height: 191px; object-fit: cover; object-position: 50% 50%;" data-ssr-src-done="true" fetchpriority="high">
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php
include("../includes/footer.php");
?>