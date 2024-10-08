<footer class="footer mt-auto py-3">
  <div class="container-footer">
    <div class="row-footer">
      <div class="col-md-4">
        <h5>ESCRIBINOS</h5>
        <form method="POST" action="/includes/footer_contact.php" name="contactar">
          <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <input title="Nombre" type="text" class="form-control" placeholder="Nombre" name="name"
                autocomplete="given-name" value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>"
                required>
            </div>
            <div class="form-group col-md-6">
              <input title="Apellido" type="text" class="form-control" placeholder="Apellido" name="apellido"
                autocomplete="family-name"
                value="<?php echo isset($_SESSION['apellido']) ? $_SESSION['apellido'] : ''; ?>" required>
            </div>
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
          <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
      </div>
      <div class="col-md-2">
      </div>
      <div class="col-md-2">
        <h5>AUTORIDADES Y CONTACTOS IMPORTANTES DE LA ORGANIZACIÓN</h5>
        <p>
          <strong>Dueño del shopping:</strong> Pedro Rodriguez. <br>
          <strong>Encargado Atención al Cliente:</strong> Cristian Romero. <br>
          <strong>Encargado Soporte Técnico:</strong> Lionel Scaloni. <br>
          <strong>Desarrolladores del sitio:</strong> Cravero, Pablo / Pérez Fontela, Simón; <br>
        </p>
      </div>
      <div class="col-md-2">
        <h5>CORREOS ELECTRÓNICOS</h5>
        <p>
          <strong>Consultas generales:</strong> nofavorita@hotmail.com<br>
          <strong>Dueño del shopping:</strong> estebanquito@gmail.com <br>
          <strong>Atención al cliente:</strong> soporte@nofavorita.social <br>
          <strong>Desarrolladores web:</strong> alejo.lautaro@hotmail.com
        </p>
      </div>
    </div>
  </div>
</footer>


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

</body>

</html>