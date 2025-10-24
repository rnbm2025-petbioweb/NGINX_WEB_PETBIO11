<?php
// require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables del .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

$host = $_ENV['MYSQL_HOST'] ?? '127.0.0.1';
$user = $_ENV['MYSQL_USER'] ?? 'root';
$pass = $_ENV['MYSQL_PASSWORD'] ?? '';
$db   = $_ENV['MYSQL_DATABASE'] ?? '';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("❌ Error de conexión: " . $mysqli->connect_error);
}

echo "✅ Conexión exitosa a la base de datos '{$db}' en {$host}\n";

$result = $mysqli->query("SHOW TABLES;");
if ($result) {
    echo "📋 Tablas encontradas:\n";
    while ($row = $result->fetch_array()) {
        echo " - " . $row[0] . "\n";
    }
} else {
    echo "⚠️ No se pudieron listar las tablas.\n";
}

$mysqli->close();
?>
