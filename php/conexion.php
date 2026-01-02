<?php
// Configuración de la base de datos
// Intenta leer de variable de entorno DATABASE_URL (Render/Neon standard)
$database_url = getenv("DATABASE_URL");

if ($database_url) {
    $url = parse_url($database_url);
}

if (isset($url) && is_array($url) && isset($url["host"])) {
    $host = $url["host"];
    $port = $url["port"] ?? 5432;
    $user = $url["user"] ?? "postgres"; // Fallback por si acaso
    $password = $url["pass"] ?? "";     // Fallback por si acaso
    $dbname = ltrim($url["path"] ?? "/sistema_nomina", "/");
} else {
    // Si no hay DATABASE_URL, busca variables individuales
    $host = getenv('DB_HOST') ?: "localhost";
    $port = getenv('DB_PORT') ?: "5432";
    $dbname = getenv('DB_NAME') ?: "sistema_nomina";
    $user = getenv('DB_USER') ?: "postgres";
    $password = getenv('DB_PASSWORD') ?: "123456789";
}

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conexion = new PDO($dsn, $user, $password);
    // Configurar para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En producción, no mostrar detalles del error ($e->getMessage())
    // die("Error crítico de conexión: " . $e->getMessage()); 
    die("Lo sentimos, hay un problema de conexión con el sistema. Intente más tarde.");
}
?>