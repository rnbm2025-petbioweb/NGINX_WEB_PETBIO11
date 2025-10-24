<?php
/**
 * 🌐 Conexión híbrida PETBIO: MySQL local (Termux) → fallback Supabase remoto
 * Optimizado para Render + Termux
 */

$is_render = getenv('RENDER');
$MYSQL_HOST = '127.0.0.1';
$MYSQL_PORT = '3306';
$MYSQL_USER = 'root';
$MYSQL_PASS = 'R00t_Segura_2025!';
$MYSQL_DB   = 'db__produccion_petbio_segura_2025';

$SUPABASE_HOST = 'db.jbsxvonnrahhfffeacdy.supabase.co';
$SUPABASE_PORT = '5432';
$SUPABASE_USER = 'postgres';
$SUPABASE_PASS = 'R00t_Segura_2025!';
$SUPABASE_DB   = 'postgres';

try {
    if ($is_render) {
        throw new PDOException("MySQL no disponible en Render");
    }

    // 🔹 Intentar conexión local (solo Termux)
    $dsn = "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8mb4;unix_socket=/data/data/com.termux/files/usr/var/run/mysqld.sock";
    $pdo = new PDO($dsn, $MYSQL_USER, $MYSQL_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    define('DB_ENGINE', 'MySQL');
    error_log("✅ Conectado a MySQL local");
} catch (PDOException $e) {
    error_log("⚠️ MySQL no disponible: " . $e->getMessage());
    try {
        // 🔸 Conexión SSL a Supabase (solo IPv4)
//        $dsn_pg = "pgsql:host=$SUPABASE_HOST;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require";
        $dsn_pg = "pgsql:host=0.tcp.supabase.co;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require";

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
