<?php
$page = 'home';
include("../includes/navbar.php");

$consulta = "";

if (isset($_SESSION['tipoUsuario'])) {
  $categoriaCliente = $_SESSION['categoriaCliente'];
  $tipoUsuario = $_SESSION['tipoUsuario'];

  if ($categoriaCliente == 'Premium' || $tipoUsuario == 'Dueño de local' || $tipoUsuario == 'administrador' || $tipoUsuario == 'No registrado') {
    $consulta = "SELECT * FROM novedades";
  } elseif ($categoriaCliente == 'Medium') {
    $consulta = "SELECT * FROM novedades WHERE \"categoriaCliente\" IN ('Medium', 'Inicial')";
  } elseif ($categoriaCliente == 'Inicial') {
    $consulta = "SELECT * FROM novedades WHERE \"categoriaCliente\" = 'Inicial'";
  }
}

if (!empty($consulta)) {
  $resultado = pg_query($link, $consulta);
} else {
  $resultado = false;
}

$hoy = date('Y-m-d');
$eliminarNovedadesQuery = "DELETE FROM novedades WHERE \"fechaHastaNovedad\" < '$hoy'";
$resultadoEliminar = pg_query($link, $eliminarNovedadesQuery);

if ($resultadoEliminar) {
  $message = "Novedades vencidas han sido eliminadas automáticamente.";
} else {
  $message = "Error al intentar eliminar novedades vencidas.";
}
?>

<div class="video">
  <iframe class="video-embed" src="https://player.vimeo.com/video/1017660487?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;autoplay=1&amp;loop=1&amp;autopause=0&amp;muted=1&amp;controls=0&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; clipboard-write" title="no-favorita-video"></iframe>
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
<div class="d-flex justify-content-center novedades" id="novedades">
  <div class="container">
    <h2 class="services-title text-center"><strong>NOVEDADES</strong></h2>
    <div class="row justify-content-center">
      <?php
      if ($resultado) {
        while ($novedad = pg_fetch_assoc($resultado)) {

      ?>
          <div class="col-md-4">
            <div class='card my-2'>
              <h5 class='card-title'><?php echo strtoupper($novedad['textoNovedad']); ?></h5>
              <p class='card-text'>Desde: <?php echo $novedad['fechaDesdeNovedad']; ?><br>Hasta: <?php echo $novedad['fechaHastaNovedad']; ?></p>
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
    <div class="d-flex flex-wrap justify-content-center align-items-center">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_b16d8e7e694342f9b2904b14bf439790~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_b16d8e7e694342f9b2904b14bf439790~mv2.png" alt="logo webfav1_Mesa de trabajo 1" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_ce9e8f05a667485fba0b36a0794bfe94~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_ce9e8f05a667485fba0b36a0794bfe94~mv2.png" alt="logo webfav1-28" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_dd5cf6254ffc4689aa1371f9719dc8db~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_dd5cf6254ffc4689aa1371f9719dc8db~mv2.png" alt="logo webfav1-29" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_67367b2ec9d2499ea07db1421ce925cd~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_67367b2ec9d2499ea07db1421ce925cd~mv2.png" alt="logo webfav1-27" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_268f2a905b494d5aaff2638b4174fe1e~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_268f2a905b494d5aaff2638b4174fe1e~mv2.png" alt="logo webfav1-32" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_08e80c7a97764ccf86409b20788dd288~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_08e80c7a97764ccf86409b20788dd288~mv2.png" alt="logo webfav1-03" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_beecfd73b3dc4731a61597eb8fa77207~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_beecfd73b3dc4731a61597eb8fa77207~mv2.png" alt="logo webfav1-18" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_af5ae3bc2f8e4f8892ca846ff789aaf1~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_af5ae3bc2f8e4f8892ca846ff789aaf1~mv2.png" alt="logo webfav1-19" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_f82054ef56c740b580a71ab8833812ea~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_f82054ef56c740b580a71ab8833812ea~mv2.png" alt="logofav web 2" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_a19443d019ef43a6a48a424cd8dfeba4~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_a19443d019ef43a6a48a424cd8dfeba4~mv2.png" alt="logo webfav1-22" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_9f2f96844ca54f9bae60b03d002477a4~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_9f2f96844ca54f9bae60b03d002477a4~mv2.png" alt="logo webfav1-30" data-ssr-src-done="true" fetchpriority="high">
      <img class="marcas-logos" src="https://static.wixstatic.com/media/290684_db011b37896a4ce5874245b1d575d98e~mv2.png/v1/fill/w_186,h_191,al_c,q_85,usm_0.66_1.00_0.01,enc_auto/290684_db011b37896a4ce5874245b1d575d98e~mv2.png" alt="logo webfav1-26" data-ssr-src-done="true" fetchpriority="high">
    </div>
  </div>

</div>
<div>
  <div class="parallax">
    <div class="overlay">
      <h2>Disfrutá el Patio De Comidas</h2>
      <p>Desayunos, Almuerzos, Meriendas, Happy Hours y Cena.</p>
      <p class="mt-5">Domingos a Jueves de 10:00 a 00:00hs <br>
        Viernes, Sábados y vísperas de feriado de 10 a 01hs</p>
    </div>
  </div>
</div>
<div class="container container-horarios">
  <div class="row d-flex flex-column horarios">
    <h2><strong>HORARIOS</strong></h2>
    <p>ABIERTO DE LUNES A DOMINGO</p>
  </div>
  <div class="row d-flex mt-1 locales">
    <div class="col-md-4">
      <h3><strong>COMERCIOS</strong></h3>
      <p>10 a 21hs</p>
    </div>
    <div class="col-md-4">
      <h3><strong>CINES</strong></h3>
      <p>13:30 a 23hs</p>
    </div>
    <div class="col-md-4">
      <h3><strong>FARMACIA</strong></h3>
      <p>L-V. 8:30 a 20:30hs | S. 9 A 20:30hs</p>
    </div>
  </div>
</div>
<div class="container-estacionamiento d-flex flex-column">
  <div class="texto-estacionamiento">
    <h2><strong>SARMIENTO 942</strong></h2>
    <h3><strong>1 hora de estacionamiento gratis <br>
        con tu ticket de compra</strong></h3>
    <p>No te olvides de sellar tu ticket de estacionamiento en el puesto de atención al cliente <br> ubicado en el 2do piso al lado de los ascensores.</p>
    <p class="my-5">Horarios estacionamiento: <br>
      Lunes a Miércoles de 8 a 20hs - Jueves a Sábado de 8 a 21hs</p>
  </div>
</div>
<div class="formulario">
  <div class="container container-formulario">
    <div class="col-md-5">
      <h2>CONTACTO</h2>
      <p>Para consultas comerciales, contactarse a: <br><a href="mailto: nofavorita2@gmail.com" target="_self">nofavorita2@gmail.com</a></p>
      <p>Oportunidades Laborales: <br>No hay vacantes disponibles</p>
      <p>Por otras consultas, por favor completar <br>el formulario.</p>
    </div>
    <div class="col-md-7 formulario-input">
      <form method="POST" action="../includes/footer_contact.php" name="contactar">
        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <div class="form-group">
          <input title="Nombre" type="text" class="form-control" placeholder="Nombre" name="name"
            autocomplete="given-name" value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>"
            required>
        </div>
        <div class="form-group">
          <input title="Apellido" type="text" class="form-control" placeholder="Apellido" name="apellido"
            autocomplete="family-name"
            value="<?php echo isset($_SESSION['apellido']) ? $_SESSION['apellido'] : ''; ?>" required>
        </div>
        <div class="form-group">
          <input title="E-mail" type="email" class="form-control" placeholder="E-mail" name="mailUsuario"
            autocomplete="email"
            value="<?php echo isset($_SESSION['nombreUsuario']) ? $_SESSION['nombreUsuario'] : ''; ?>" required>
        </div>
        <div class="form-group">
          <textarea class="form-control" title="Consulta" placeholder="Consulta" rows="3" name="consulta"
            required></textarea>
        </div>
        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-primary formulario-button">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
if (isset($_SESSION['message_footer'])) {
  $message_footer = $_SESSION['message_footer'];
  $message_type_footer = $_SESSION['message_type_footer'];
  unset($_SESSION['message_footer']);
  unset($_SESSION['message_type_footer']);
}
?>
<!-- Modal de éxito -->
<?php if (isset($message_type_footer) && $message_type_footer == 'success'): ?>
  <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
    style="display: block; background: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Éxito</h5>
        </div>
        <div class="modal-body">
          <?php echo $message_footer; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" onclick="window.location.reload();">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Modal de error -->
<?php if (isset($message_type_footer) && $message_type_footer == 'error'): ?>
  <div class="modal fade show" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel"
    style="display: block; background: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
        </div>
        <div class="modal-body">
          <?php echo $message_footer; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="window.location.reload();">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php
include("../includes/footer.php");
?>