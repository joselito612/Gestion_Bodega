<?php

require_once __DIR__ . '/../models/Bodega.php';

class BodegaController {
    private $model;

    public function __construct() {
        $this->model = new Bodega();
    }

    public function index() {
        $filtroEstado = $_GET['filtro'] ?? null;
        $bodegas = $this->model->getAll($filtroEstado);
        return $bodegas;
    }

    public function show($id) {
        return $this->model->getById($id);
    }

    public function create() {
        $data = $this->validar($_POST);
        if ($data === false) {
            return false;
        }
        try {
            return $this->model->create($data);
        } catch (Exception $e) {
            if ($e->getMessage() === 'DUPLICATE_CODE') {
                $_SESSION['error_duplicate'] = true;
                return false;
            }
            throw $e;
        }
    }

    public function edit($id) {
        $data = $this->validar($_POST);
        if ($data === false) {
            return false;
        }
        return $this->model->update($id, $data);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }

    public function hardDelete($id) {
        return $this->model->hardDelete($id);
    }

    public function getEncargados() {
        return $this->model->getEncargadosDisponibles();
    }

    private function validar($data) {
        $errors = [];

        if (empty($data['codigo']) || strlen($data['codigo']) < 2 || strlen($data['codigo']) > 5 || !preg_match('/^B[a-zA-Z0-9]+$/', $data['codigo'])) {
            $errors[] = 'Código debe comenzar con "B" mayúscula seguido de 1 a 4 caracteres alfanuméricos (ej: B1, B123, B001)';
        }

        if (empty($data['nombre']) || strlen($data['nombre']) > 100) {
            $errors[] = 'Nombre debe tener máximo 100 caracteres';
        }

        $direcciones = ['Estación Central', 'Pudahuel', 'Las Condes'];
        if (empty($data['direccion']) || !in_array($data['direccion'], $direcciones)) {
            $errors[] = 'Seleccione una dirección válida';
        }

        if (empty($data['dotacion']) || !is_numeric($data['dotacion']) || (int)$data['dotacion'] < 1) {
            $errors[] = 'Dotación debe ser un número positivo';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            return false;
        }

        $estadosValidos = ['Activada', 'Desactivada'];
        $estado = isset($data['estado']) && in_array($data['estado'], $estadosValidos) 
            ? $data['estado'] 
            : 'Activada';

        return [
            'codigo' => strtoupper(trim($data['codigo'])),
            'nombre' => trim($data['nombre']),
            'direccion' => $data['direccion'],
            'dotacion' => (int)$data['dotacion'],
            'estado' => $estado,
            'encargados' => $data['encargados'] ?? []
        ];
    }
}