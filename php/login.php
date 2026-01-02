<?php
session_start();

/**
 * 1. CONFIGURACIÓN DE CONEXIÓN
 * Usamos el archivo compartido para no repetir código y soportar variables de entorno.
 */
include 'conexion.php';
// $conexion ya está disponible desde el include

// $conexion ya está disponible desde el include

try {
    /**
     * 2. PROCESAMIENTO DEL FORMULARIO
     * Usamos 'usuarios' y 'clave' porque así están en tu index.html
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $usuarioForm = $_POST['usuarios'] ?? '';
        $claveForm = $_POST['clave'] ?? '';

        // 3. BUSCAR EL USUARIO
        // Importante: Verifica si tu columna en Postgres se llama 'username'
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $usuarioForm]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            /**
             * 4. VALIDACIÓN DE CONTRASEÑA
             * Si usas contraseñas encriptadas (recomendado 2025): password_verify($claveForm, $user['password'])
             * Si las guardaste como texto plano (para pruebas): $claveForm === $user['password']
             */
            if ($claveForm === $user['password'] || password_verify($claveForm, $user['password'])) {
                
                // Seguridad: Regenerar ID de sesión para prevenir fijación de sesiones
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_usuario'] = $user['username'];

                // REDIRECCIÓN: Salimos de /php/ y entramos a /html/ para buscar menu.php
                header("Location: ../html/menu.php");
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta'); window.location='../html/index.html';</script>";
            }
        } else {
            echo "<script>alert('El usuario no existe'); window.location='../html/index.html';</script>";
        }
    }

} catch (PDOException $e) {
    // Si hay error de conexión, lo mostramos
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>