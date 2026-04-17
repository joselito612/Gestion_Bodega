<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Bodega</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .checkbox-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-group { display: flex; gap: 10px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Bodega</h1>
        
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=edit&id=<?= $bodega['id'] ?>">
            <div class="form-group">
                <label for="codigo">Código</label>
                <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($bodega['codigo']) ?>" readonly style="background: #eee;">
            </div>

            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($bodega['nombre']) ?>" maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección *</label>
                <select id="direccion" name="direccion" required>
                    <option value="Estación Central" <?= ($bodega['direccion'] === 'Estación Central') ? 'selected' : '' ?>>Estación Central</option>
                    <option value="Pudahuel" <?= ($bodega['direccion'] === 'Pudahuel') ? 'selected' : '' ?>>Pudahuel</option>
                    <option value="Las Condes" <?= ($bodega['direccion'] === 'Las Condes') ? 'selected' : '' ?>>Las Condes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dotacion">Dotación *</label>
                <input type="number" id="dotacion" name="dotacion" value="<?= htmlspecialchars($bodega['dotacion']) ?>" min="1" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="Activada" <?= ($bodega['estado'] === 'Activada') ? 'selected' : '' ?>>Activada</option>
                    <option value="Desactivada" <?= ($bodega['estado'] === 'Desactivada') ? 'selected' : '' ?>>Desactivada</option>
                </select>
            </div>

            <div class="form-group">
                <label>Encargados</label>
                <div class="checkbox-group">
                    <?php foreach ($encargados as $encargado): ?>
                        <label>
                            <input type="checkbox" name="encargados[]" value="<?= $encargado['id'] ?>" <?= in_array($encargado['id'], $bodega['encargado_ids'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($encargado['nombre'] . ' ' . $encargado['apellido1']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
        </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const nombre = document.getElementById('nombre').value.trim();
                const direccion = document.getElementById('direccion').value;
                const dotacion = document.getElementById('dotacion').value;

                if (!nombre || nombre.length > 100) {
                    alert('Nombre debe tener máximo 100 caracteres');
                    e.preventDefault();
                    return;
                }
                if (!direccion) {
                    alert('Seleccione una dirección válida');
                    e.preventDefault();
                    return;
                }
                if (!dotacion || parseInt(dotacion) < 1) {
                    alert('Dotación debe ser un número positivo');
                    e.preventDefault();
                }
            });
        </script>
    </div>
</body>
</html>