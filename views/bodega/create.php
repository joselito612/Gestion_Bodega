<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Bodega</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container container-forms">
        <h1>Nueva Bodega</h1>
        
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_duplicate'])): ?>
            <div id="modalError" class="modal show">
                <div class="modal-content">
                    <h3>Código de Bodega ya existe</h3>
                    <p>Por favor agregar otro código</p>
                    <button onclick="document.getElementById('modalError').classList.remove('show')">Aceptar</button>
                </div>
            </div>
            <?php unset($_SESSION['error_duplicate']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?action=create">
            <div class="form-group">
                <label for="codigo">Código *</label>
                <input type="text" id="codigo" name="codigo" maxlength="5" required>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección *</label>
                <select id="direccion" name="direccion" required>
                    <option value="">Seleccione...</option>
                    <option value="Estación Central">Estación Central</option>
                    <option value="Pudahuel">Pudahuel</option>
                    <option value="Las Condes">Las Condes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dotacion">Dotación *</label>
                <input type="number" id="dotacion" name="dotacion" min="1" required>
            </div>

            <div class="form-group">
                <label>Encargados</label>
                <div class="checkbox-group">
                    <?php foreach ($encargados as $encargado): ?>
                        <label>
                            <input type="checkbox" name="encargados[]" value="<?= $encargado['id'] ?>">
                            <?= htmlspecialchars($encargado['nombre'] . ' ' . $encargado['apellido1']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const codigo = document.getElementById('codigo').value.trim();
                const nombre = document.getElementById('nombre').value.trim();
                const direccion = document.getElementById('direccion').value;
                const dotacion = document.getElementById('dotacion').value;

                if (!codigo || codigo.length < 2 || codigo.length > 5 || !/^B[a-zA-Z0-9]+$/.test(codigo)) {
                    alert('Código debe comenzar con "B" mayúscula seguido de 1 a 4 caracteres alfanuméricos (ej: B1, B123, B001)');
                    e.preventDefault();
                    return;
                }
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