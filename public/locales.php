<?php
$page = 'locales';
include ("../includes/navbar.php");

// Configuración de paginación
$registrosPorPagina = 5;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina - 1) * $registrosPorPagina;

// Obtener parámetros de búsqueda y filtros
$search = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';
$rubro = isset($_GET['rubro']) ? mysqli_real_escape_string($link, $_GET['rubro']) : '';

$busca_locales = "SELECT * FROM locales WHERE 1=1";

if ($search) {
  $busca_locales .= " AND (nombreLocal LIKE '%$search%' OR codLocal = '$search')";
}

if ($rubro) {
  $busca_locales .= " AND rubroLocal = '$rubro'";
}

$queryTotal = $busca_locales; // Query sin límite ni offset para contar total de registros
$resultTotal = mysqli_query($link, $queryTotal);
$total_locales = mysqli_num_rows($resultTotal);

// Agregar paginación a la consulta principal
$busca_locales .= " LIMIT $offset, $registrosPorPagina";

$resultado = mysqli_query($link, $busca_locales);
?>


<div class="container">
  <div class="row">
    <div class="col-md-3">
      <form method="get" action="locales.php">
        <div class="form-group">
          <label for="rubro">Rubro:</label>
          <select class="form-control" name="rubro" id="rubro">
            <option value="">Todos</option>
            <option value="Indumentaria" <?php if ($rubro == 'Indumentaria')
              echo 'selected'; ?>>Indumentaria</option>
            <option value="Comida" <?php if ($rubro == 'Comida')
              echo 'selected'; ?>>Comida</option>
            <option value="Óptica" <?php if ($rubro == 'Óptica')
              echo 'selected'; ?>>Óptica</option>
            <option value="Perfumería" <?php if ($rubro == 'Perfumería')
              echo 'selected'; ?>>Perfumería</option>
            <option value="Cine" <?php if ($rubro == 'Cine')
              echo 'selected'; ?>>Cine</option>
            <option value="Entretenimiento" <?php if ($rubro == 'Entretenimiento')
              echo 'selected'; ?>>Entretenimiento
            </option>
            <option value="Computacion" <?php if ($rubro == 'Computación')
              echo 'selected'; ?>>Computación
            </option>
            <option value="Otros" <?php if ($rubro == 'Otros')
              echo 'selected'; ?>>Otros
            </option>

          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-filtrar">Filtrar</button>
      </form>
    </div>
    <div class="col-md-9">
      <?php
      if ($total_locales > 0) {
        while ($row = mysqli_fetch_assoc($resultado)) {
          echo "<div class='local-container'>";
          echo "<div class='local-image'>";
          echo "<img src='" . $row["imagenLocal"] . "' alt='Local Image'>";
          echo "</div>";
          echo "<div class='local-info'>";
          echo "<p><strong>Código del Local:</strong> " . $row["codLocal"] . "</p>";
          echo "<p><strong>Nombre del Local:</strong> " . $row["nombreLocal"] . "</p>";
          echo "<p><strong>Ubicación:</strong> " . $row["ubicacionLocal"] . "</p>";
          echo "<p><strong>Rubro:</strong> " . $row["rubroLocal"] . "</p>";
          echo "</div>";
          echo "<button type='button' class='btn-seleccionar' onclick=\"window.location.href='promocion_local.php?codLocal=" . $row['codLocal'] . "'\">Seleccionar</button>";
          echo "</div>";
        }
        // Mostrar paginación si hay más de una página
        if ($total_locales > $registrosPorPagina) {
          echo "<nav aria-label='Page navigation example'>";
          echo "<ul class='pagination justify-content-center'>";
          $totalPaginas = ceil($total_locales / $registrosPorPagina);
          $pagina_actual = $pagina;


          if ($pagina_actual > 1) {
            echo "<li class='page-item'><a class='page-link' href='?pagina=" . ($pagina_actual - 1) . "'>&laquo; Anterior</a></li>";
          }


          for ($i = 1; $i <= $totalPaginas; $i++) {
            echo "<li class='page-item " . ($pagina_actual == $i ? 'active' : '') . "'><a class='page-link' href='?pagina=$i'>$i</a></li>";
          }


          if ($pagina_actual < $totalPaginas) {
            echo "<li class='page-item'><a class='page-link' href='?pagina=" . ($pagina_actual + 1) . "'>Siguiente &raquo;</a></li>";
          }
          echo "</ul></nav>";
        }
      } else {
        echo "No hay locales disponibles.";
      }
      mysqli_close($link);
      ?>
    </div>
  </div>
</div>

<?php
include ("../includes/footer.php"); ?>