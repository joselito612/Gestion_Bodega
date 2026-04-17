<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Bodegas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .toolbar { display: flex; gap: 10px; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; text-decoration: none; border-radius: 4px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-secondary { background: #6c757d; color: white; }
        .filter-form { display: flex; gap: 10px; align-items: center; }
        select, input { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .estado { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .estado-ACTIVADA { background: #d4edda; color: #155724; }
        .estado-DESACTIVADA { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 8px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; }
        .modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #dc3545; color: white; padding: 30px; border-radius: 8px; text-align: center; max-width: 400px; }
        .modal-content h3 { margin: 0 0 15px 0; }
        .modal-content button { background: white; color: #dc3545; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    </style>
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