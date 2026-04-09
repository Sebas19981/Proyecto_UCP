<?php
// Models/ProductoModel.php
require_once 'Config/Conexion.php';

class ProductoModel {
    private $db;

    public function __construct() {
        $this->db = Conexion::Conectar();
    }

    public function listarTodos() {
        $stmt = $this->db->prepare("SELECT `Item` as item, descripcion, pesoProm as kg FROM producto ORDER BY descripcion ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorItem($item) {
        $stmt = $this->db->prepare("SELECT `Item` as item, descripcion, pesoProm as kg FROM producto WHERE `Item` = ?");
        $stmt->execute([intval($item)]);
        return $stmt->fetch();
    }

    public function crear($item, $descripcion, $minimo, $maximo, $pesoProm) {
        $stmt = $this->db->prepare("INSERT INTO producto (`Item`, descripcion, minimo, maximo, pesoProm) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([intval($item), $descripcion, floatval($minimo), floatval($maximo), floatval($pesoProm)]);
    }

    public function actualizar($itemActual, $descripcion, $minimo, $maximo, $pesoProm) {
        $stmt = $this->db->prepare("UPDATE producto SET descripcion = ?, minimo = ?, maximo = ?, pesoProm = ? WHERE `Item` = ?");
        return $stmt->execute([$descripcion, floatval($minimo), floatval($maximo), floatval($pesoProm), intval($itemActual)]);
    }

    public function eliminar($item) {
        $stmt = $this->db->prepare("DELETE FROM producto WHERE `Item` = ?");
        return $stmt->execute([intval($item)]);
    }

    public function buscar($texto) {
        $stmt = $this->db->prepare("SELECT `Item` as item, descripcion, pesoProm as kg FROM producto WHERE descripcion LIKE ? OR `Item` LIKE ? ORDER BY descripcion ASC LIMIT 20");
        $busqueda = '%' . $texto . '%';
        $stmt->execute([$busqueda, $busqueda]);
        return $stmt->fetchAll();
    }
}
