<?php
require_once '../Core/Database.php';

$modelsPath = __DIR__ . '/../Models';
$modelFiles = glob($modelsPath . '/*.php');

$models = array_map(function($file) {
    return basename($file, '.php');
}, $modelFiles);
$_GET['model'] ??= null;
$selectedModel = $_GET['model'] ? $_GET['model'] : null;

$columns = [];
$relations = [];

if($selectedModel != null){
    require_once "../Models/{$selectedModel}.php";
    $table = $selectedModel::getTableName();

    $pdo = Database::connection();
    $stmt = $pdo->prepare("DESCRIBE {$table}");
    $stmt->execute();

    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener relaciones
    if (method_exists($selectedModel, 'relations')) {
        $relations = $selectedModel::relations();
    }
}

$builder = null;
$records = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if($_POST["form_id"] == "search"){
        if (
            isset($_POST['column'], $_POST['value']) &&
            $_POST['column'] !== '' &&
            $_POST['value'] !== ''
        ) {
            $column = $_POST['column'];
            $value = $_POST['value'];
            $operator = $_POST['operator'] ?? '=';

            // 👉 Builder
            $builder = $selectedModel::where($column, $operator, $value);

            // 👉 Ejecutar después
            $records = $builder->get();

            // Incluir relaciones
            if(isset($_POST['relation'])){
                $relation = $_POST['relation'];
                $model = $records[0];
                
                $model->load($relation);

                $relationsData = $model->{$relation};
                $builderRelationSql = null;
            }

        } elseif (isset($_POST['model'])) {
            // 👉 all() NO sirve para debug SQL
            // Simulamos "all" con Builder
            $builder = $selectedModel::query();
            $records = $builder->get();
        }
    }

    if($_POST["form_id"] == "relation"){
        if (isset($_POST['relation'], $_POST['model'])) {
            $relation = $_POST['relation'];
            $model = $selectedModel::find(4); // Ejemplo: buscar el modelo con ID 4

            if ($model && method_exists($model, $relation)) {
                $builder = $model->$relation();

                $sql = $builder->sqlDebug();
                $records = $builder->get();
            }
        }
    }
    
}

require 'layout.php';
?>
<h2>Obtener Registros</h2>

<div class="row">
    <div class="col-4">
        <form method="get" action="">
            <div class="form-group">
                <label>Seleccionar Modelo:</label>
                <select name="model" onchange="this.form.submit()">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($models as $model): ?>
                        <option value="<?php echo $model; ?>" <?php if ($model === $selectedModel) echo 'selected'; ?>>
                            <?php echo $model; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <?php
    if ($selectedModel && !empty($columns)):
    ?>
    <div class="col-8">
        <form method="post">
            <div class="form-group">
                <label>Buscar por:</label>
                <select name="column">
                    <?php foreach ($columns as $column): ?>
                        <option value="<?php echo $column['Field']; ?>"><?php echo $column['Field']; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="operator">
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                    <option value="<"><</option>
                    <option value=">">></option>
                    <option value="<="><=</option>
                    <option value=">=">>=</option>
                </select>
                <input type="text" name="value" placeholder="Valor a buscar">
                <input type="hidden" name="model" value="<?php echo $selectedModel; ?>">
                <input type="hidden" name="form_id" value="search">
                <button type="submit">Buscar</button>
            </div>
            <div class="form-group">
                <label>Relaciones:</label>
                <select name="relation">
                    <option value="">-- Ninguna --</option>
                    <?php foreach ($relations as $relation => $type): ?>
                        <option value="<?php echo $relation; ?>"><?php echo $relation; ?> => <?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <small>Consulta SQL Generada:</small>
                <code>
                    <?php
                    if ($builder) {
                        echo htmlspecialchars($builder->sqlDebug());
                    } else {
                        echo 'SELECT * FROM ' . $selectedModel . 's WHERE id > 1';
                    }
                    ?>
                </code>
            </div>
            <?php
            if (isset($relationsData) && !empty($relationsData)):
            ?>
            <div class="form-group">
                <small>Consulta SQL de la Relación:</small>
                <code>
                    <?php
                        echo htmlspecialchars($builderRelationSql);
                    ?>
                </code>
            </div>
            <?php
            endif;
            ?>
        </form>
    </div>
    <?php
    endif;
    ?>
</div>

<?php if ($builder ?? false): ?>
<section>
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <h3 class="table-title">Registros Obtenidos:</h3>
                <?php if (!empty($records)): ?>
                    <table>
                        <thead>
                            <tr>
                                <?php foreach (array_keys($records[0]->toArray()) as $field): ?>
                                    <th><?php echo $field;?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record):
                                // obtener los datos de la relación del modelo
                                ?>
                                <tr>
                                    <?php 
                                    foreach ($record->toArray() as $value):
                                    ?>
                                        <td>
                                            <?php 
                                            if (strlen($value) > 50) {
                                                echo htmlspecialchars(substr($value, 0, 50)) . '...';
                                            } else {
                                                echo htmlspecialchars($value);
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #666;">No se encontraron registros.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
    if (isset($relationsData) && !empty($relationsData)):
        if($relationsData instanceof Model):
            $relationsData = [$relationsData];
        endif;
?>
<div class="row">
    <div class="col-12">
        <div class="table-container">
            <h3 class="table-title">Registros de la Relación: <?php echo htmlspecialchars(get_class($relationsData[0])); ?></h3>
            <?php if (!empty($relationsData)): ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($relationsData[0]->toArray()) as $field): ?>
                                <th><?php echo $field; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relationsData as $relationRecord): ?>
                            <tr>
                                <?php foreach ($relationRecord->toArray() as $value): ?>
                                    <td>
                                        <?php 
                                        if (strlen($value) > 50) {
                                            echo htmlspecialchars(substr($value, 0, 50)) . '...';
                                        } else {
                                            echo htmlspecialchars($value);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #666;">No se encontraron registros relacionados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    endif;
endif;
?>

<?php if (isset($columns) && !empty($columns)): ?>
<div class="row">
    <div class="col-7">
        <div class="table-container">
            <h3>Detalles del Modelo:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Columna</th>
                        <th>Tipo</th>
                        <th>Nulo</th>
                        <th>Default</th>
                        <th>Llave</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($columns as $column): ?>
                        <tr>
                            <td><?php echo $column['Field']; ?></td>
                            <td><?php echo $column['Type']; ?></td>
                            <td><?php echo $column['Null']; ?></td>
                            <td><?php echo $column['Default']; ?></td>
                            <td><?php echo $column['Key']; ?></td>
                            <td><?php echo $column['Extra']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
</body>
</html>
