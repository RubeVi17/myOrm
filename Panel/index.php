<?php require 'layout.php'; ?>

<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 style="font-size: 48px; margin-bottom: 10px;">
            <i class="fas fa-database" style="color: var(--primary);"></i> 
            myOrm Lab
        </h1>
        <p style="font-size: 18px; color: var(--text-muted);">
            Laboratorio de experimentación para tu ORM personalizado
        </p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        <div class="card text-center" style="border-top: 4px solid var(--success);">
            <div style="padding: 30px 20px;">
                <i class="fas fa-plus-circle" style="font-size: 48px; color: var(--success); margin-bottom: 16px;"></i>
                <h3 style="margin-bottom: 12px;">Crear Registros</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    Inserta nuevos datos en tus modelos con formularios dinámicos
                </p>
                <a href="/panel/create.php" class="btn btn-success">
                    <i class="fas fa-arrow-right"></i> Ir a Crear
                </a>
            </div>
        </div>
    </div>

    <div class="col-3">
        <div class="card text-center" style="border-top: 4px solid var(--info);">
            <div style="padding: 30px 20px;">
                <i class="fas fa-search" style="font-size: 48px; color: var(--info); margin-bottom: 16px;"></i>
                <h3 style="margin-bottom: 12px;">Query Builder</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    Consulta y filtra registros con el constructor de queries
                </p>
                <a href="/panel/query.php" class="btn btn-info">
                    <i class="fas fa-arrow-right"></i> Ir a Query
                </a>
            </div>
        </div>
    </div>

    <div class="col-3">
        <div class="card text-center" style="border-top: 4px solid var(--warning);">
            <div style="padding: 30px 20px;">
                <i class="fas fa-edit" style="font-size: 48px; color: var(--warning); margin-bottom: 16px;"></i>
                <h3 style="margin-bottom: 12px;">Actualizar</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    Modifica registros existentes de forma sencilla
                </p>
                <a href="/panel/update.php" class="btn btn-warning">
                    <i class="fas fa-arrow-right"></i> Ir a Actualizar
                </a>
            </div>
        </div>
    </div>

    <div class="col-3">
        <div class="card text-center" style="border-top: 4px solid var(--primary);">
            <div style="padding: 30px 20px;">
                <i class="fas fa-database" style="font-size: 48px; color: var(--primary); margin-bottom: 16px;"></i>
                <h3 style="margin-bottom: 12px;">Migraciones</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    Gestiona el esquema de tu base de datos
                </p>
                <a href="/panel/migrate.php" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i> Ir a Migrar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Sobre este proyecto</h3>
            </div>
            <div class="card-body">
                <p>
                    <strong>myOrm</strong> es un ORM educativo construido desde cero para comprender 
                    los fundamentos de la persistencia de datos y el patrón Active Record.
                </p>
                <p style="margin-top: 12px;">
                    Este sandbox te permite experimentar con todas las funcionalidades del ORM:
                </p>
                <div class="row mt-3">
                    <div class="col-6">
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> CRUD completo</strong> - Create, Read, Update, Delete
                        </div>
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> Query Builder</strong> - Construcción fluida de consultas
                        </div>
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> Migraciones</strong> - Control de versiones de BD
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> Relaciones</strong> - HasMany, BelongsTo, etc.
                        </div>
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> Timestamps</strong> - created_at y updated_at automáticos
                        </div>
                        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px;">
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                            <strong> SQL Debug</strong> - Visualiza las queries generadas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Estadísticas</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3" style="padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <div>
                        <i class="fas fa-cube" style="color: var(--primary); font-size: 24px;"></i>
                    </div>
                    <div class="text-right">
                        <h4 style="margin-bottom: 0; color: var(--primary);">
                            <?php
                            $modelsPath = __DIR__ . '/../Models';
                            $modelFiles = glob($modelsPath . '/*.php');
                            echo count($modelFiles);
                            ?>
                        </h4>
                        <small style="color: var(--text-muted);">Modelos</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3" style="padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <div>
                        <i class="fas fa-code-branch" style="color: var(--success); font-size: 24px;"></i>
                    </div>
                    <div class="text-right">
                        <h4 style="margin-bottom: 0; color: var(--success);">
                            <?php
                            $migrationsPath = __DIR__ . '/../Migrations';
                            $migrationFiles = glob($migrationsPath . '/*.php');
                            echo count($migrationFiles);
                            ?>
                        </h4>
                        <small style="color: var(--text-muted);">Migraciones</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="padding: 12px; background: #f8f9fa; border-radius: 8px;">
                    <div>
                        <i class="fas fa-clock" style="color: var(--info); font-size: 24px;"></i>
                    </div>
                    <div class="text-right">
                        <h4 style="margin-bottom: 0; color: var(--info);">
                            <?php echo date('H:i'); ?>
                        </h4>
                        <small style="color: var(--text-muted);">Hora actual</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-rocket"></i> Acceso Rápido</h3>
            </div>
            <div class="card-body">
                <a href="/panel/model.php" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-plus"></i> Crear Modelo
                </a>
                <a href="/panel/view.php" class="btn btn-outline-primary btn-block">
                    <i class="fas fa-eye"></i> Ver Ejemplo
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-lightbulb" style="font-size: 24px;"></i>
                <div>
                    <strong>Tip:</strong> Usa el Query Builder para ver el SQL generado en tiempo real y 
                    aprender cómo funciona tu ORM por dentro.
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</body>
</html>