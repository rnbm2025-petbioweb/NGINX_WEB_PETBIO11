<?php
/**
 * 🌐 Conexión híbrida PETBIO: MySQL → fallback Supabase
 * Compatible con Render, Termux y Docker.
 */

// --- Variables MySQL ---
$MYSQL_HOST = getenv('MYSQL_HOST') ?: '127.0.0.1';
$MYSQL_PORT = getenv('MYSQL_PORT') ?: '3306';
$MYSQL_USER = getenv('MYSQL_USER') ?: 'root';
$MYSQL_PASS = getenv('MYSQL_PASSWORD') ?: 'R00t_Segura_2025!';
$MYSQL_DB   = getenv('MYSQL_DATABASE') ?: 'db__produccion_petbio_segura_2025';

// --- Variables Supabase (PostgreSQL) ---
$SUPABASE_HOST = getenv('SUPABASE_HOST') ?: 'db.jbsxvonnrahhfffeacdy.supabase.co';
$SUPABASE_PORT = getenv('SUPABASE_PORT') ?: '5432';
$SUPABASE_USER = getenv('SUPABASE_USER') ?: 'postgres';
$SUPABASE_PASS = getenv('SUPABASE_PASS') ?: 'R00t_Segura_2025!';
$SUPABASE_DB   = getenv('SUPABASE_DB')   ?: 'postgres';

try {
    // 🔹 Intentar conexión a MySQL
    $pdo = new PDO("mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8mb4", $MYSQL_USER, $MYSQL_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    define('DB_ENGINE', 'MySQL');
    error_log("✅ Conectado a MySQL PETBIO ($MYSQL_HOST)");
} catch (PDOException $e) {
    error_log("⚠️ MySQL no disponible: " . $e->getMessage());
    try {
        // 🔸 Intentar Supabase (PostgreSQL) con IPv4 y SSL
        $dsn = "pgsql:host=$SUPABASE_HOST;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require;options='-4'";
        $pdo = new PDO($dsn, $SUPABASE_USER, $SUPABASE_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        define('DB_ENGINE', 'Supabase');
        error_log("✅ Conectado a Supabase ($SUPABASE_HOST) [IPv4]");
    } catch (PDOException $e2) {
        error_log("❌ Falla total: " . $e2->getMessage());
        die("❌ Error fatal: no se pudo conectar ni a MySQL ni a Supabase → " . $e2->getMessage());
    }
}
?>

