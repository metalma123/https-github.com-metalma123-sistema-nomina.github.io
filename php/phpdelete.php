<?php
include 'conexion.php'; // Asegúrate de que $conexion esté definida aquí

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_registro'];
    $cedula = $_POST['cedula'];

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibimos los datos del formulario
    $id = $_POST['id_registro'] ?? '';
    $cedula = $_POST['cedula'] ?? '';

    if (!empty($id)) {
        try {
            // En PDO para PostgreSQL usamos "?" o ":id" como marcador de posición
            $sql = "DELETE FROM registro WHERE id_registro = ?";
            $stmt = $conexion->prepare($sql);
            
            // Ejecutamos pasando el ID en un array
            $result = $stmt->execute([$id]);

            if ($result) {
                echo "<script>
                    alert('Registro con Cédula $cedula eliminado correctamente.');
                    window.location='../html/eliminar.php';  
                </script>";
            }
        } catch (PDOException $e) {
            // Manejo de errores específico de PDO
            echo "Error al eliminar en 2025: " . $e->getMessage();
        }
    } else {
        echo "<script>
            alert('No se seleccionó ningún registro para eliminar.'); 
            window.history.back();
        </script>";
    }
}
}
?>