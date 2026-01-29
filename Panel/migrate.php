<?php
require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'run_migrations') {
        // Ejecutar las migraciones
        $output = [];
        ob_start();
        include '../migrate.php';
        $output = explode("\n", ob_get_clean());
    }
}

$migrationOutput = $output ?? ['Presiona el botón para ejecutar las migraciones.'];

// Obtener lista de migraciones existentes
$migrationsPath = __DIR__ . '/../Migrations';
$migrationFiles = glob($migrationsPath . '/*.php');
$migrations = array_map(function($file) {
    return [
        'name' => basename($file, '.php'),
        'path' => $file,
        'date' => date('Y-m-d H:i:s', filemtime($file))
    ];
}, $migrationFiles);

// Obtener lista de modelos existentes
$modelsPath = __DIR__ . '/../Models';
$modelFiles = glob($modelsPath . '/*.php');
$models = array_map(function($file) {
    return basename($file, '.php');
}, $modelFiles);

?>

<!-- HEADER CON TABS -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-database"></i> Gestión de Base de Datos</h2>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="showTab('migrations')">
                    <i class="fas fa-database"></i> Migraciones
                </button>
                <button class="btn btn-outline-primary" onclick="showTab('create')">
                    <i class="fas fa-plus-circle"></i> Crear Nuevo
                </button>
                <button class="btn btn-outline-primary" onclick="showTab('models')">
                    <i class="fas fa-cube"></i> Modelos Existentes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TAB: MIGRACIONES -->
<div id="tab-migrations" class="tab-content">
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Migraciones Disponibles (<?= count($migrations) ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($migrations)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay migraciones creadas todavía.
                        </div>
                    <?php else: ?>
                        <div class="table-container" style="padding: 0;">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-file-code"></i> Nombre</th>
                                        <th><i class="fas fa-calendar"></i> Fecha de Creación</th>
                                        <th><i class="fas fa-tools"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($migrations as $migration): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-code-branch" style="color: var(--primary);"></i>
                                                <?= htmlspecialchars($migration['name']) ?>
                                            </td>
                                            <td><?= $migration['date'] ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-terminal"></i> Log de Migraciones</h3>
                </div>
                <div class="card-body">
                    <code><?php
                        foreach ($migrationOutput as $line) {
                            if (trim($line)) {
                                echo htmlspecialchars($line) . "\n";
                            }
                        }
                    ?></code>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-play-circle"></i> Ejecutar Migraciones</h3>
                </div>
                <div class="card-body">
                    <p>Ejecuta todas las migraciones pendientes en la base de datos.</p>
                    <form method="post">
                        <input type="hidden" name="action" value="run_migrations">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-rocket"></i> Ejecutar Todas las Migraciones
                        </button>
                    </form>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Las migraciones se ejecutan en orden y crean/modifican las tablas en tu base de datos.
            </div>
        </div>
    </div>
</div>

<!-- TAB: CREAR NUEVO MODELO/MIGRACIÓN -->
<div id="tab-create" class="tab-content" style="display: none;">
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-magic"></i> Crear Modelo y Migración</h3>
                </div>
                <div class="card-body">
                    <form id="createModelForm" method="post" action="create_migration.php">
                        <!-- INFORMACIÓN BÁSICA -->
                        <div class="card mb-3" style="background: #f8f9fa;">
                            <div style="padding: 20px;">
                                <h4><i class="fas fa-info-circle"></i> Información Básica</h4>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="required"><i class="fas fa-cube"></i> Nombre del Modelo</label>
                                            <input type="text" 
                                                   name="model_name" 
                                                   id="model_name"
                                                   placeholder="Ej: Product, Category, User"
                                                   required
                                                   onkeyup="updatePreview()">
                                            <small class="form-text">
                                                <i class="fas fa-lightbulb"></i> Usa singular en PascalCase
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="required"><i class="fas fa-table"></i> Nombre de la Tabla</label>
                                            <input type="text" 
                                                   name="table_name" 
                                                   id="table_name"
                                                   placeholder="Ej: products, categories, users"
                                                   required>
                                            <small class="form-text">
                                                <i class="fas fa-lightbulb"></i> Usa plural en snake_case
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CAMPOS DEL MODELO -->
                        <div class="card">
                            <div style="padding: 20px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4><i class="fas fa-columns"></i> Campos del Modelo</h4>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addField()">
                                        <i class="fas fa-plus"></i> Agregar Campo
                                    </button>
                                </div>

                                <div id="fields-container">
                                    <!-- Los campos se agregarán aquí dinámicamente -->
                                </div>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    Los campos <strong>id</strong>, <strong>created_at</strong> y <strong>updated_at</strong> se agregan automáticamente.
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Crear Modelo y Migración
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetForm()">
                                <i class="fas fa-redo"></i> Reiniciar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- PREVIEW Y OPCIONES -->
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-eye"></i> Vista Previa</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label><i class="fas fa-file-code"></i> Modelo</label>
                        <code id="preview-model" style="font-size: 11px; padding: 10px;">
                            class YourModel extends Model
                        </code>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-table"></i> Tabla</label>
                        <code id="preview-table" style="font-size: 11px; padding: 10px;">
                            your_table
                        </code>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sliders-h"></i> Opciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" id="with_timestamps" name="with_timestamps" checked>
                        <label for="with_timestamps">
                            <i class="fas fa-clock"></i> Incluir timestamps
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="with_soft_deletes" name="with_soft_deletes">
                        <label for="with_soft_deletes">
                            <i class="fas fa-trash-restore"></i> Soft deletes
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="auto_run" name="auto_run">
                        <label for="auto_run">
                            <i class="fas fa-bolt"></i> Ejecutar migración automáticamente
                        </label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-book"></i> Guía Rápida</h3>
                </div>
                <div class="card-body">
                    <small>
                        <strong>Tipos de datos:</strong><br>
                        • string - Texto corto (VARCHAR)<br>
                        • text - Texto largo<br>
                        • integer - Números enteros<br>
                        • decimal - Números decimales<br>
                        • boolean - Verdadero/Falso<br>
                        • date - Solo fecha<br>
                        • datetime - Fecha y hora<br>
                        • timestamp - Marca de tiempo
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB: MODELOS EXISTENTES -->
<div id="tab-models" class="tab-content" style="display: none;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-cube"></i> Modelos Existentes (<?= count($models) ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($models)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay modelos creados todavía.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($models as $model): ?>
                                <div class="col-4 mb-3">
                                    <div class="card" style="border-left: 4px solid var(--primary);">
                                        <div style="padding: 20px;">
                                            <h4>
                                                <i class="fas fa-cube" style="color: var(--primary);"></i>
                                                <?= htmlspecialchars($model) ?>
                                            </h4>
                                            <div class="btn-group mt-2" style="width: 100%;">
                                                <a href="/panel/query.php?model=<?= $model ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-search"></i> Query
                                                </a>
                                                <a href="/panel/create.php?model=<?= $model ?>" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-plus"></i> Crear
                                                </a>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let fieldCounter = 0;

// Cambiar entre tabs
function showTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Mostrar el tab seleccionado
    document.getElementById('tab-' + tabName).style.display = 'block';
    
    // Actualizar botones activos
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
}

// Agregar campo dinámicamente
function addField() {
    fieldCounter++;
    const container = document.getElementById('fields-container');
    
    const fieldHtml = `
        <div class="card mb-2" id="field-${fieldCounter}" style="background: #f8f9fa;">
            <div style="padding: 15px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 style="margin: 0;"><i class="fas fa-grip-vertical"></i> Campo #${fieldCounter}</h5>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeField(${fieldCounter})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label class="required">Nombre</label>
                            <input type="text" 
                                   name="fields[${fieldCounter}][name]" 
                                   placeholder="nombre_campo"
                                   required>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label class="required">Tipo</label>
                            <select name="fields[${fieldCounter}][type]" required onchange="toggleLength(${fieldCounter})">
                                <option value="">-- Tipo --</option>
                                <option value="string">String</option>
                                <option value="text">Text</option>
                                <option value="integer">Integer</option>
                                <option value="bigInteger">Big Integer</option>
                                <option value="decimal">Decimal</option>
                                <option value="boolean">Boolean</option>
                                <option value="date">Date</option>
                                <option value="datetime">Datetime</option>
                                <option value="timestamp">Timestamp</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2" id="length-container-${fieldCounter}" style="display: none;">
                        <div class="form-group">
                            <label>Longitud</label>
                            <input type="number" 
                                   name="fields[${fieldCounter}][length]" 
                                   placeholder="255">
                        </div>
                    </div>
                    <div class="col-3" id="decimal-length-container-${fieldCounter}" style="display: none;">
                        <div class="form-group">
                            <label>Longitud Decimal</label>
                            <input type="number" 
                                   name="fields[${fieldCounter}][decimal_length]" 
                                   placeholder="2">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Opciones</label>
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="nullable-${fieldCounter}"
                                       name="fields[${fieldCounter}][nullable]">
                                <label for="nullable-${fieldCounter}">Nullable</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="unique-${fieldCounter}"
                                       name="fields[${fieldCounter}][unique]">
                                <label for="unique-${fieldCounter}">Unique</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label>Valor por Defecto</label>
                            <input type="text" 
                                   name="fields[${fieldCounter}][default]" 
                                   placeholder="NULL">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fieldHtml);
}

// Remover campo
function removeField(id) {
    document.getElementById('field-' + id).remove();
}

// Toggle length input based on field type
function toggleLength(id) {
    const typeSelect = document.querySelector(`select[name="fields[${id}][type]"]`);
    const lengthContainer = document.getElementById(`length-container-${id}`);
    const decimalLengthContainer = document.getElementById(`decimal-length-container-${id}`);
    
    if (typeSelect.value === 'string') {
        decimalLengthContainer.style.display = 'none';
        lengthContainer.style.display = 'block';
    } else if (typeSelect.value === 'decimal') {
        lengthContainer.style.display = 'none';
        decimalLengthContainer.style.display = 'block';
    } else {
        decimalLengthContainer.style.display = 'none';
        lengthContainer.style.display = 'none';
    }
}

// Actualizar preview
function updatePreview() {
    const modelName = document.getElementById('model_name').value || 'YourModel';
    const tableName = document.getElementById('table_name').value || 'your_table';
    
    document.getElementById('preview-model').textContent = `class ${modelName} extends Model`;
    document.getElementById('preview-table').textContent = tableName;
}

// Auto-generar nombre de tabla desde modelo
document.getElementById('model_name')?.addEventListener('input', function(e) {
    const modelName = e.target.value;
    if (modelName) {
        // Convertir PascalCase a snake_case y pluralizar
        const tableName = modelName
            .replace(/([A-Z])/g, '_$1')
            .toLowerCase()
            .substring(1) + 's';
        document.getElementById('table_name').value = tableName;
    }
});

// Reiniciar formulario
function resetForm() {
    document.getElementById('createModelForm').reset();
    document.getElementById('fields-container').innerHTML = '';
    fieldCounter = 0;
    updatePreview();
}

// Agregar primer campo al cargar
window.addEventListener('DOMContentLoaded', function() {
    addField();
});
</script>

</div>
</body>
</html>