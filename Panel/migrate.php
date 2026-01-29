<?php
require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ejecutar las migraciones
    $output = [];
    ob_start();
    include '../migrate.php';
    $output = explode("\n", ob_get_clean());
} else {
    $output = ['Presiona el botón para ejecutar las migraciones.'];
}

?>

<h2>Migraciones</h2>
<div class="row">
    <div class="col-2">
        <form method="post">
                <button type="submit">Ejecutar Migraciones</button>
            </form>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <h3>Respuesta de Migraciones</h3>
            <code><?php
foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}
?></code>
        </div>
    </div>
</div>

</body>
</html>
