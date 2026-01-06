<?php
/**
 * auth.php
 * Verifica si el usuario ha iniciado sesión. 
 * Si no, redirige al login o devuelve error si es una petición AJAX.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    // Verificar si es una petición AJAX/JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || 
        strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Sesión expirada o no iniciada', 'error' => 'No autorizado']);
        exit;
    } else {
        // Redirección normal según la profundidad del archivo
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_dir === 'php' || $current_dir === 'html') {
            header("Location: ../html/index.html");
        } else {
            header("Location: html/index.html");
        }
        exit();
    }
}
?>
