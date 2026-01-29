<?php

require_once "../Models/Topic.php";

$topic = Topic::find(1);

$topic->loadMany([
    'user',
    'categorie',
    'comments',
    'comments.user',
    'likes.user',    
]);

require 'layout.php';
?>

<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-comments"></i> Detalles del Topic</h2>
            <div class="btn-group">
                <a href="/panel/update.php?model=Topic&id=<?= $topic->id ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- COLUMNA PRINCIPAL: TOPIC -->
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-newspaper"></i> <?= htmlspecialchars($topic->title) ?></h3>
            </div>
            <div class="card-body">
                <!-- INFO DEL AUTOR Y CATEGORÍA -->
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Autor</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                <input type="text" value="<?= htmlspecialchars($topic->user->name) ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label><i class="fas fa-folder"></i> Categoría</label>
                            <span class="badge badge-primary" style="display: block; padding: 12px; font-size: 14px;">
                                <?= htmlspecialchars($topic->categorie->name) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Fecha</label>
                            <input type="text" value="<?= htmlspecialchars($topic->date) ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Descripción</label>
                    <textarea disabled rows="6"><?= htmlspecialchars($topic->description) ?></textarea>
                </div>

                <!-- ESTADÍSTICAS -->
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="alert alert-info">
                            <i class="fas fa-comments"></i>
                            <strong><?= count($topic->comments) ?></strong> comentarios en este topic
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="alert alert-success">
                            <i class="fas fa-thumbs-up"></i>
                            <strong><?= count($topic->likes) ?></strong> personas dieron like
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE COMENTARIOS -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-comment-dots"></i> Comentarios (<?= count($topic->comments) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($topic->comments())): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        No hay comentarios todavía. ¡Sé el primero en comentar!
                    </div>
                <?php else: ?>
                    <?php foreach ($topic->comments as $comment):
                        ?>
                        <div class="card mb-2" style="background: <?= $comment->pinned === 1 ? '#fff3cd' : '#f8f9fa' ?>;">
                            <div class="card-body" style="padding: 16px;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-user-circle" style="font-size: 24px; color: var(--primary);"></i>
                                        <div>
                                            <strong style="color: var(--text-dark);">
                                                <?= htmlspecialchars($comment->user->name) ?>
                                            </strong>
                                            <?php if($comment->pinned === 1): ?>
                                                <span class="badge badge-warning" style="margin-left: 8px;">
                                                    <i class="fas fa-thumbtack"></i> Fijado
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <small style="color: var(--text-muted);">
                                        <i class="fas fa-clock"></i> <?= date('d/m/Y h:i A', strtotime($comment->created_at)) ?>
                                    </small>
                                </div>
                                <div class="comment-content"><?= nl2br(htmlspecialchars($comment->comment)) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- COLUMNA LATERAL: LIKES Y ACCIONES -->
    <div class="col-4">
        <!-- LIKES -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-heart"></i> Likes (<?= count($topic->likes) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($topic->likes)): ?>
                    <p class="text-center" style="color: var(--text-muted); padding: 20px 0;">
                        <i class="fas fa-heart-broken" style="font-size: 48px; opacity: 0.3;"></i>
                        <br><br>
                        Aún no hay likes
                    </p>
                <?php else: ?>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($topic->likes as $like): ?>
                            <div class="d-flex align-items-center gap-2 mb-2" style="padding: 8px; background: #f8f9fa; border-radius: 8px;">
                                <i class="fas fa-user-circle" style="font-size: 20px; color: var(--primary);"></i>
                                <span><?= htmlspecialchars($like->user->name) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-block">
                    <i class="fas fa-thumbs-up"></i> Dar Like
                </button>
            </div>
        </div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Acciones</h3>
            </div>
            <div class="card-body">
                <button class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-share"></i> Compartir
                </button>
                <button class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-bookmark"></i> Guardar
                </button>
                <button class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-flag"></i> Reportar
                </button>
                <hr>
                <button class="btn btn-danger btn-block">
                    <i class="fas fa-trash-alt"></i> Eliminar Topic
                </button>
            </div>
        </div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Información</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>ID del Topic</label>
                    <input type="text" value="<?= $topic->id ?>" disabled>
                </div>
                <div class="form-group mb-0">
                    <label>Estado</label>
                    <br>
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Activo
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</body>
</html>