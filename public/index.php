<?php

session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Bodega.php';
require_once __DIR__ . '/../controllers/BodegaController.php';

$controller = new BodegaController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $bodegas = $controller->index();
        $filtro = $_GET['filtro'] ?? null;
        require __DIR__ . '/../views/bodega/index.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->create();
            if ($result !== false) {
                $_SESSION['success'] = 'Bodega creada correctamente';
                header('Location: index.php');
                exit;
            }
        }
        $encargados = $controller->getEncargados();
        require __DIR__ . '/../views/bodega/create.php';
        break;

    case 'edit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $controller->edit($id);
            if ($result !== false) {
                $_SESSION['success'] = 'Bodega actualizada correctamente';
                header('Location: index.php');
                exit;
            }
        }
        $bodega = $controller->show($id);
        $encargados = $controller->getEncargados();
        require __DIR__ . '/../views/bodega/edit.php';
        break;

    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->delete($id);
            $_SESSION['success'] = 'Bodega eliminada correctamente';
        }
        header('Location: index.php');
        exit;

    case 'harddelete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->hardDelete($id);
            $_SESSION['success'] = 'Bodega eliminada permanentemente';
        }
        header('Location: index.php');
        exit;

    case 'deactivate':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->delete($id);
            $_SESSION['success'] = 'Bodega desactivada correctamente';
        }
        header('Location: index.php');
        exit;

    default:
        header('Location: index.php?action=index');
        exit;
}