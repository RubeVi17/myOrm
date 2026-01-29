<?php





include 'layout.php';
?>
<h2>Modelos</h2>
<div class="row">
    <div class="col-12">
        <div class="card">
            <h3>Crear modelo</h3>
            <form method="post">
                <div class="form-row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="required">Nombre</label>
                            <input type="text" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-group-icon"></i>
                                <input type="email">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit">Crear Modelo</button>
            </form>
        </div>
    </div>
</div>
