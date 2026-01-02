<?php
if (function_exists('pg_query_params')) {
    echo "¡Éxito! PostgreSQL ya está activo.";
} else {
    echo "Sigue desactivado. Revisa el archivo php.ini.";
}
?>