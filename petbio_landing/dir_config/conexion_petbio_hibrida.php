<?php
/**
 * 🌐 Conexión híbrida PETBIO: MySQL local (Termux) → fallback Supabase remoto
 * Versión optimizada para entorno híbrido NGINX + PHP-FPM + Termux
 */

$MYSQL_HOST = getenv('MYSQL_HOST') ?: '127.0.0.1';
$MYSQL_PORT = getenv('MYSQL_PORT') ?: '3306';
$MYSQL_USER = getenv('MYSQL_USER') ?: 'root';
$MYSQL_PASS = getenv('MYSQL_PASSWORD') ?: 'R00t_Segura_2025!';
$MYSQL_DB   = getenv('MYSQL_DATABASE') ?: 'db__produccion_petbio_segura_2025';

$SUPABASE_HOST = getenv('SUPABASE_HOST') ?: 'db.jbsxvonnrahhfffeacdy.supabase.co';
$SUPABASE_PORT = getenv('SUPABASE_PORT') ?: '5432';
$SUPABASE_USER = getenv('SUPABASE_USER') ?: 'postgres';
$SUPABASE_PASS = getenv('SUPABASE_PASS') ?: 'R00t_Segura_2025!';
$SUPABASE_DB   = getenv('SUPABASE_DB')   ?: 'postgres';

try {
    // 🔹 Intentar conexión a MariaDB local
    $dsn = "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8mb4;unix_socket=/data/data/com.termux/files/usr/var/run/mysqld.sock";
    $pdo = new PDO($dsn, $MYSQL_USER, $MYSQL_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    define('DB_ENGINE', 'MySQL');
    error_log("✅ Conectado a MySQL local en Termux ($MYSQL_HOST:$MYSQL_PORT)");
} catch (PDOException $e) {
    error_log("⚠️ MySQL no disponible localmente: " . $e->getMessage());
    try {
        // 🔸 Fallback automático: conexión SSL a Supabase
        $dsn_pg = "pgsql:host=$SUPABASE_HOST;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require";
        $pdo = new PDO($dsn_pg, $SUPABASE_USER, $SUPABASE_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        define('DB_ENGINE', 'Supabase');
        error_log("✅ Conectado a Supabase remoto ($SUPABASE_HOST) con SSL");
    } catch (PDOException $e2) {
        die("❌ Error fatal: no se pudo conectar ni a MySQL ni a Supabase → " . $e2->getMessage());
    }
}
?>
