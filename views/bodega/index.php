<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Bodegas</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Bodegas</h1>
        
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="toolbar">
            <a href="index.php?action=create" class="btn btn-primary">Nueva Bodega</a>
            <form class="filter-form" method="GET">
                <input type="hidden" name="action" value="index">
                <select name="filtro">
                    <option value="todas">Todas</option>
                    <option value="Activada" <?= ($filtro === 'Activada') ? 'selected' : '' ?>>Activadas</option>
                    <option value="Desactivada" <?= ($filtro === 'Desactivada') ? 'selected' : '' ?>>Desactivadas</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filtrar</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Dotación</th>
                    <th>Encargados</th>
                    <th>Estado</th>
                    <th>Fecha/Hora Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bodegas as $bodega): ?>
                    <tr>
                        <td><?= htmlspecialchars($bodega['codigo']) ?></td>
                        <td><?= htmlspecialchars($bodega['nombre']) ?></td>
                        <td><?= htmlspecialchars($bodega['direccion']) ?></td>
                        <td><?= htmlspecialchars($bodega['dotacion']) ?></td>
                        <td><?= htmlspecialchars($bodega['encargados'] ?? 'Sin asignar') ?></td>
                        <td>
                            <span class="estado estado-<?= htmlspecialchars($bodega['estado']) ?>">
                                <?= htmlspecialchars($bodega['estado']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($bodega['fecha_creacion']))) ?></td>
                        <td class="actions">
                            <a href="index.php?action=edit&id=<?= $bodega['id'] ?>" class="btn btn-secondary">Editar</a>
                            <?php if ($bodega['estado'] === 'Activada'): ?>
                                <a href="index.php?action=deactivate&id=<?= $bodega['id'] ?>" class="btn btn-warning" onclick="return confirm('¿Desactivar bodega?')">Desactivar</a>
                            <?php else: ?>
                                <a href="index.php?action=harddelete&id=<?= $bodega['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar permanentemente? Esta acción no se puede deshacer.')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>