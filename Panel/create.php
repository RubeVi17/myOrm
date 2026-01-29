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

$responseSql = null;
if ($_POST && isset($_POST['model'], $_POST['data'])) {
    $data = $_POST['data'];

    $response = $selectedModel::create($data);
    $response = $response->toArray();

}

require 'layout.php';
?>

<h2>Crear Registro</h2>

<div class="row">
    <div class="col-6">
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
</div>

<?php
if ($selectedModel && !empty($columns)):

    $fks = $selectedModel::foreignKeys();

    $inputType = null;
    foreach ($columns as $column):
        if (strpos($column['Type'], 'int') !== false) {
            $inputType = 'number';
        } elseif (strpos($column['Type'], 'varchar') !== false || strpos($column['Type'], 'text') !== false) {
            $inputType = 'text';
        } elseif (strpos($column['Type'], 'date') !== false) {
            $inputType = 'date';
        } else {
            $inputType = 'text';
        }
    endforeach;

?>

<div class="row">
    <div class="col-8">
        <div class="card">
            <h3>Crear nuevo <?php echo $selectedModel; ?></h3>
            <form method="post">
                <input type="hidden" name="model" value="<?php echo $selectedModel; ?>">
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
                                echo "<option value='{$opt['id']}'>{$opt['id']} : {$opt['name']}</option>";
                            } ?>
                        </select>
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
                        <?= $column['Null'] === 'NO' ? 'required' : '' ?>>
                    </div>
                <?php } endforeach; ?>
                <button type="submit">Crear</button>
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
