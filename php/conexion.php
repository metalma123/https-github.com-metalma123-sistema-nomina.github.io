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
    // Mostramos el error real para que el usuario pueda corregirlo (ej: contraseña mal, BD no existe)
    die("ERROR DE CONEXIÓN: " . $e->getMessage() . "<br><br><b>Sugerencias:</b><br>1. Verifica que PostgreSQL esté encendido.<br>2. Verifica que el usuario 'postgres' y la contraseña coincidan.<br>3. Verifica que la base de datos '$dbname' exista.");
}