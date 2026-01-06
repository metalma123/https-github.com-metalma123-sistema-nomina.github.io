<?php
include 'auth.php';
include 'utils.php';
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibimos y sanitizamos los datos del formulario
    $id = sanitize($_POST['id_registro'] ?? '');
    $cedula = sanitize($_POST['cedula'] ?? '');

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
?>