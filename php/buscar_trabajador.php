<?php
include 'conexion.php'; // Tu archivo de conexión con PDO

// Evita que errores de PHP se mezclen con el JSON
error_reporting(0); 
header('Content-Type: application/json');

$cedula = $_GET['cedula'] ?? '';

if (empty($cedula)) {
    echo json_encode(['error' => 'Por favor ingrese una cédula']);
    exit;
}

try {
    $sql = "SELECT * FROM registro WHERE cedula = :cedula LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':cedula' => $cedula]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Trabajador no encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos']);
}
?>