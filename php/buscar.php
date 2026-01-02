<?php
include 'conexion.php'; // Tu archivo de conexión con PDO


// Lógica GET: Buscar datos para llenar el formulario (JSON)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cedula = $_GET['cedula'] ?? '';
    
    // Si no hay cédula, no devolvemos nada o error
    if (empty($cedula)) {
        echo json_encode(['error' => 'Cédula no proporcionada']);
        exit;
    }

    $sql = "SELECT * FROM registro WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$cedula]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Trabajador no encontrado']);
    }
    exit; // Importante detener la ejecución aquí para GET
}

// Lógica POST: Actualizar datos (HTML/JS Response)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_registro'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $cargo = $_POST['cargo'];
    $fecha = $_POST['fecha_ingreso'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE registro SET nombres=?, apellidos=?, cargo=?, fecha_ingreso=?, direccion=?, telefono=? WHERE id_registro=?";
    
    try {
        $stmt = $conexion->prepare($sql);
        $result = $stmt->execute([$nombres, $apellidos, $cargo, $fecha, $direccion, $telefono, $id]);

        if ($result) {
            echo "<script>alert('¡Actualizado con éxito!'); window.location='../html/modificar.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>






?>