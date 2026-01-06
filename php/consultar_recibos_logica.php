<?php
include 'auth.php';
include 'utils.php';
include 'conexion.php';
header('Content-Type: application/json');

$cedula = sanitize($_GET['cedula'] ?? '');
$id_recibo = sanitize($_GET['id_recibo'] ?? '');

try {
    if (!empty($cedula)) {
        // Buscar todos los recibos de una cédula
        $sql = "SELECT n.*, r.nombres, r.apellidos, r.cargo 
                FROM nomina n 
                JOIN registro r ON n.cedula_trabajador = r.cedula 
                WHERE n.cedula_trabajador = :cedula 
                ORDER BY n.id_recibo DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } elseif (!empty($id_recibo)) {
        // Buscar un recibo específico por su ID
        $sql = "SELECT n.*, r.nombres, r.apellidos, r.cargo 
                FROM nomina n 
                JOIN registro r ON n.cedula_trabajador = r.cedula 
                WHERE n.id_recibo = :id_recibo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id_recibo' => $id_recibo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Recibo no encontrado']);
        }
    } else {
        echo json_encode(['error' => 'Debe proporcionar una cédula o un número de recibo']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>
