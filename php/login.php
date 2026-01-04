<?php
/**
 * ARCHIVO DE DIAGNÓSTICO Y LOGIN
 * Si ves este texto en el navegador, tu servidor Apache no está procesando PHP correctamente.
 * Asegúrate de abrir: http://localhost/SistemaNomina/html/index.html
 */

// 1. Mostrar errores para depuración (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 2. Verificar extensión PDO PostgreSQL
if (!extension_loaded('pdo_pgsql')) {
    die("Error: La extensión 'pdo_pgsql' no está habilitada en tu PHP (XAMPP). <br> 
         Por favor, edita tu archivo php.ini y quita el punto y coma (;) de la línea: <b>extension=pdo_pgsql</b> y reinicia Apache.");
}

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarioForm = trim($_POST['usuarios'] ?? '');
    $claveForm = trim($_POST['clave'] ?? '');

    if (empty($usuarioForm) || empty($claveForm)) {
        echo "<script>alert('Por favor llene todos los campos'); window.location='../html/index.html';</script>";
        exit();
    }

    try {
        // Usamos la variable $conexion definida en conexion.php
        if (!isset($conexion)) {
            die("Error: La variable de conexión no está definida. Revisa php/conexion.php");
        }

        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $usuarioForm]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificación simple (texto plano o encriptada)
            if ($claveForm === $user['password'] || password_verify($claveForm, $user['password'])) {
                
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_usuario'] = $user['username'];

                header("Location: ../html/menu.php");
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta'); window.location='../html/index.html';</script>";
            }
        } else {
            echo "<script>alert('El usuario no existe'); window.location='../html/index.html';</script>";
        }
    } catch (PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }
} else {
    header("Location: ../html/index.html");
    exit();
}