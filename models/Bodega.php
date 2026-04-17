<?php

require_once __DIR__ . '/../config/db.php';

class Bodega {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll($filtroEstado = null) {
        $sql = "SELECT b.*, 
                COALESCE(string_agg(e.nombre || ' ' || e.apellido1, ', ')) as encargados
                FROM bodega b
                LEFT JOIN bodega_encargado be ON b.id = be.bodega_id AND be.estado = 'Activo'
                LEFT JOIN encargado e ON be.encargado_id = e.id";

        $params = [];
        if ($filtroEstado && $filtroEstado !== 'todas') {
            $sql .= " WHERE b.estado = ?";
            $params[] = $filtroEstado;
        }

        $sql .= " GROUP BY b.id ORDER BY b.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT b.*, 
                array_agg(e.id) as encargado_ids
                FROM bodega b
                LEFT JOIN bodega_encargado be ON b.id = be.bodega_id AND be.estado = 'Activo'
                LEFT JOIN encargado e ON be.encargado_id = e.id
                WHERE b.id = ?
                GROUP BY b.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result && isset($result['encargado_ids'])) {
            $result['encargado_ids'] = $result['encargado_ids'] 
                ? array_map('intval', explode(',', trim($result['encargado_ids'], '{}'))) 
                : [];
        }
        
        return $result;
    }

    public function create($data) {
        $checkSql = "SELECT id FROM bodega WHERE codigo = ?";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([$data['codigo']]);
        if ($checkStmt->fetch()) {
            throw new Exception('DUPLICATE_CODE');
        }

        $sql = "INSERT INTO bodega (codigo, nombre, direccion, dotacion, estado)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['codigo'],
            $data['nombre'],
            $data['direccion'],
            $data['dotacion'],
            $data['estado'] ?? 'Activada'
        ]);

        $bodegaId = $this->db->lastInsertId();

        if (!empty($data['encargados'])) {
            $this->asignarEncargados($bodegaId, $data['encargados']);
        }

        return $bodegaId;
    }

    public function update($id, $data) {
        $sql = "UPDATE bodega 
                SET nombre = ?, direccion = ?, dotacion = ?, estado = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['direccion'],
            $data['dotacion'],
            $data['estado'],
            $id
        ]);

        if (isset($data['encargados'])) {
            $this->actualizarEncargados($id, $data['encargados']);
        }

        return $id;
    }

    public function delete($id) {
        $sql = "UPDATE bodega SET estado = 'Desactivada' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function hardDelete($id) {
        $this->db->beginTransaction();
        try {
            $sql1 = "DELETE FROM bodega_encargado WHERE bodega_id = ?";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute([$id]);

            $sql2 = "DELETE FROM bodega WHERE id = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function asignarEncargados($bodegaId, $encargadoIds) {
        foreach ($encargadoIds as $encargadoId) {
            $sql = "INSERT INTO bodega_encargado (bodega_id, encargado_id, estado)
                    VALUES (?, ?, 'Activo')
                    ON CONFLICT (bodega_id, encargado_id) 
                    DO UPDATE SET estado = 'Activo'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$bodegaId, $encargadoId]);
        }
    }

    public function actualizarEncargados($bodegaId, $encargadoIds) {
        $sql = "UPDATE bodega_encargado SET estado = 'Inactivo' WHERE bodega_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bodegaId]);

        $this->asignarEncargados($bodegaId, $encargadoIds);
    }

    public function getEncargadosDisponibles() {
        $sql = "SELECT id, run, nombre, apellido1 
                FROM encargado 
                ORDER BY nombre";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}