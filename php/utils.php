<?php
/**
 * utils.php
 * Funciones de utilidad para el sistema.
 */

/**
 * Escapa contenido HTML para prevenir XSS.
 * @param string $text
 * @return string
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitiza entradas de usuario para evitar caracteres maliciosos.
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return trim($data ?? '');
}
?>
