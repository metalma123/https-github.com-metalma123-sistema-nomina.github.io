<?php
include 'auth.php';
include 'utils.php';
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar datos del formulario
    $nombres = sanitize($_POST['nombres']);
    $apellidos = sanitize($_POST['apellidos']);
    $cedula = sanitize($_POST['cedula']);
    $cargo = sanitize($_POST['cargo']);
    $fecha_ingreso = sanitize($_POST['fecha_ingreso']);
    $direccion = sanitize($_POST['direccion']);
    $telefono = sanitize($_POST['telefono']);
    $observaciones = sanitize($_POST['observaciones']);
    $salario_diario = sanitize($_POST['salario_diario'] ?? 0);
    $valor_hora_extra = sanitize($_POST['valor_hora_extra'] ?? 0);

    // Preparar la consulta SQL (Usamos parámetros para evitar SQL Injection)
    $sql = "INSERT INTO registro (nombres, apellidos, cedula, cargo, fecha_ingreso, direccion, telefono, observaciones, salario_diario, valor_hora_extra) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
            $observaciones,
            $_POST['salario_diario'],
            $_POST['valor_hora_extra']
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