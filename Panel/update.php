<?php
require_once '../Core/Database.php';

$modelsPath = __DIR__ . '/../Models';
$modelFiles = glob($modelsPath . '/*.php');

$models = array_map(function($file) {
    return basename($file, '.php');
}, $modelFiles);

$selectedModel = $_GET['model'] ?? null;

$columns = [];

if($selectedModel){
    require_once "../Models/{$selectedModel}.php";
    $table = $selectedModel::getTableName();

    $pdo = Database::connection();
    $stmt = $pdo->prepare("DESCRIBE {$table}");
    $stmt->execute();

    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$builder = null;
$responseSql = null;
$record = null;
if ($_SERVER["REQUEST_METHOD"] == "POST"){


    if($_POST["form_id"] == "search"){
        if (
            isset($_POST['column'], $_POST['value']) &&
            $_POST['column'] !== '' &&
            $_POST['value'] !== ''
        ) {
            $column = $_POST['column'];
            $value = $_POST['value'];
            $operator = $_POST['operator'] ?? '=';

            $builder = $selectedModel::where($column, $operator, $value);

            $record = $builder->first();

        }
    }

    if($_POST["form_id"] == "update"){
        if (
            isset($_POST['data']) &&
            !empty($_POST['data'])
        ) {
            $data = $_POST['data'];
            //var_dump($data);
            $record = $selectedModel::find($data['id']);
            $response = $record->update($data);
            if($response){
                $record = $selectedModel::find($data['id']);
                $response = $record->toArray();
            }
        }
    }

}
require 'layout.php';
?>

<h2>Actualizar Registro</h2>

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
            <div class="form-row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Buscar por:</label>
                        <select name="column">
                            <option value="id">ID</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Operador:</label>
                        <select name="operator">
                            <option value="=">=</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Valor:</label>
                        <input type="text" name="value" placeholder="Valor a buscar">
                        <input type="hidden" name="model" value="<?php echo $selectedModel; ?>">
                        <input type="hidden" name="form_id" value="search">
                    </div>
                </div>
            </div>
            <button type="submit">Buscar</button>
            <div class="form-group">
                <small>Consulta SQL Generada:</small>
                <code>
                    <?php
                    if ($builder) {
                        echo htmlspecialchars($builder->sqlDebug());
                    } else {
                        echo 'SELECT * FROM ' . $selectedModel . 's WHERE 1';
                    }
                    ?>
                </code>
            </div>
        </form>
    </div>
    <?php
    endif;
    ?>
</div>

<?php
if ($selectedModel && !empty($columns) && isset($record)):

    $fks = $selectedModel::foreignKeys();

    $inputType = null;
    foreach ($columns as $column):
        if (strpos($column['Type'], 'int') !== false) {
            $inputType = 'number';
        } elseif (strpos($column['Type'], 'varchar') !== false || strpos($column['Type'], 'text') !== false) {
            $inputType = 'text';
        } elseif (strpos($column['Type'], 'date') !== false) {
            $inputType = 'date';
        } elseif (strpos($column['Type'], 'datetime') !== false) {
            $inputType = 'datetime-local';
        } elseif (strpos($column['Type'], 'text') !== false) {
            $inputType = 'textarea';
        }
        else {
            $inputType = 'text';
        }
    endforeach;

?>

<div class="row">
    <div class="col-8">
        <div class="card">
            <h3>Actualizar <?php echo $selectedModel; ?></h3>
            <form method="post">
                <input type="hidden" name="model" value="<?php echo $selectedModel; ?>">
                <input type="hidden" name="form_id" value="update">
                <input type="hidden" name="data[id]" value="<?php echo $record->id; ?>">
                <?php foreach ($columns as $column): 
                    if(isset($fks[$column['Field']])){
                        $refTable = $fks[$column['Field']]['table'];
                        $stmt = Database::connection()->prepare("SELECT * FROM {$refTable}");
                        $stmt->execute();
                        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="form-group">
                        <label><?php echo $column['Field']; ?>:</label>
                        <select name="data[<?= $column['Field'] ?>]" <?= $column['Null'] === 'NO' ? 'required' : '' ?>>
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($options as $opt) {
                            ?>
                                <option value='<?php echo $opt['id']; ?>' <?php if ($record->{$column['Field']} == $opt['id']) echo 'selected'; ?>>
                                    <?php echo $opt['id'] . ' : ' . ($opt['name'] ?? ''); ?>
                                </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <?php
                    }elseif($column['Type'] === 'text'){
                    ?>
                    <?php if ($column['Extra'] === 'auto_increment') continue; ?>
                    <div class="form-group">
                        <label><?php echo $column['Field']; ?>:</label>
                        <textarea
                        name="data[<?= $column['Field'] ?>]"
                        placeholder="<?= $column['Type'] ?>"
                        cols="30" rows="5"
                        <?= $column['Null'] === 'NO' ? 'required' : '' ?>><?php echo htmlspecialchars($record->{$column['Field']}); ?></textarea>
                    </div>
                    <?php
                    }else{
                    ?>
                    <?php if ($column['Extra'] === 'auto_increment') continue; ?>
                    <div class="form-group">
                        <label><?php echo $column['Field']; ?>:</label>
                        <input
                        type="<?= $inputType ?>"
                        name="data[<?= $column['Field'] ?>]"
                        placeholder="<?= $column['Type'] ?>"
                        value="<?php echo htmlspecialchars($record->{$column['Field']}); ?>"
                        <?= $column['Null'] === 'NO' ? 'required' : '' ?>>
                    </div>
                <?php } endforeach; ?>
                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="table-container">
                <h3>Resultados</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Columna</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($response)): ?>
                            <?php foreach ($response as $key => $value): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($key); ?></td>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No se ha creado ningún registro aún.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
endif;
?>

</div>
</body>
</html>
