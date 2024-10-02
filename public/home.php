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
  <!-- <iframe class="video" width="560" height="315" src="https://www.youtube.com/embed/VnQrNaNDhZE?controls=0&start=54&end=515&loop=1&playlist=VnQrNaNDhZE&mute=1&autoplay=1&modestbranding=1&showinfo=0&iv_load_policy=3" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin"></iframe> -->
  <h1 class="text-center video-title">
    Volvé a vivir <br />
    el centro de Rosario
  </h1>
</div>



<!-- <div id="carouselExampleCaptions" class="carousel slide">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../assets/foto4.jpg" class="d-block w-100" alt="imagen_shopping_1">
      <div class="carousel-caption d-none d-md-block">
        <p></p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../assets/Foto3.jpg" class="d-block w-100" alt="imagen_shopping_2">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
    <div class="carousel-item">
      <img src="../assets/foto6.jpg" class="d-block w-100" alt="imagen_shopping_3">
      <div class="carousel-caption d-none d-md-block">
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden"></span>
  </button>
</div> -->
<div class="d-flex justify-content-center mt-5">
  <div class="container">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php
include("../includes/footer.php");
?>