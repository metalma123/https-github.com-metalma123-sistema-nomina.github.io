<?php
// Incluimos tu archivo de conexión existente
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $cedula = $_POST['cedula'];
    $cargo = $_POST['cargo'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $observaciones = $_POST['observaciones'];

    // Preparar la consulta SQL (Usamos parámetros para evitar SQL Injection)
    $sql = "INSERT INTO registro (nombres, apellidos, cedula, cargo, fecha_ingreso, direccion, telefono, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Ejecutar la consulta usando PDO
    // Ejecutar la consulta usando PDO
    try {
        $stmt = $conexion->prepare($sql);
        $result = $stmt->execute(array(
            $nombres, 
            $apellidos, 
            $cedula, 
            $cargo, 
            $fecha_ingreso, 
            $direccion, 
            $telefono, 
            $observaciones
        ));

        // Si llega aquí es porque no hubo excepción
        echo "<script>alert('Registro guardado exitosamente'); window.location='../html/registro.php';</script>";
        
    } catch (PDOException $e) {
        // 23505 es el código SQLSTATE para violaciones de unicidad (duplicate key) en PostgreSQL
        if ($e->getCode() == '23505') {
            echo "<script>alert('Error: La cédula " . htmlspecialchars($cedula) . " ya se encuentra registrada.'); window.history.back();</script>";
        } else {
            echo "Error en el registro: " . $e->getMessage();
        }
    }
}
?>