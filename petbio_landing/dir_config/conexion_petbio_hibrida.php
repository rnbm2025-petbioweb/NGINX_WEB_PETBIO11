<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$MYSQL_HOST = getenv('MYSQL_HOST') ?: '127.0.0.1';
$MYSQL_PORT = getenv('MYSQL_PORT') ?: '3306';
$MYSQL_USER = getenv('MYSQL_USER') ?: 'root';
$MYSQL_PASS = getenv('MYSQL_PASSWORD') ?: 'R00t_Segura_2025!';
$MYSQL_DB   = getenv('MYSQL_DATABASE') ?: 'db__produccion_petbio_segura_2025';

$SUPABASE_HOST = getenv('SUPABASE_HOST') ?: 'db.jbsxvonnrahhfffeacdy.supabase.co';
$SUPABASE_PORT = getenv('SUPABASE_PORT') ?: '5432';
$SUPABASE_USER = getenv('SUPABASE_USER') ?: 'postgres';
$SUPABASE_PASS = getenv('SUPABASE_PASS') ?: 'BiometriaPetbio2025*';
$SUPABASE_DB   = getenv('SUPABASE_DB')   ?: 'postgres';

try {
    $pdo = new PDO(
        "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8mb4",
        $MYSQL_USER,
        $MYSQL_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    define('DB_ENGINE', 'MySQL');
    error_log("✅ Conectado a MySQL local ($MYSQL_HOST:$MYSQL_PORT)");
} catch (PDOException $e) {
    error_log("⚠️ MySQL no disponible: " . $e->getMessage());

    try {
        // 🔸 Resolver solo IPv4 para Supabase
        $ipv4 = gethostbyname($SUPABASE_HOST);
        if ($ipv4 === $SUPABASE_HOST || empty($ipv4)) {
            throw new Exception("No se pudo resolver IPv4 para $SUPABASE_HOST");
        }

        // 🔸 Forzar conexión IPv4 con SSL
        $dsn = "pgsql:host=$ipv4;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require";
        $pdo = new PDO($dsn, $SUPABASE_USER, $SUPABASE_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        define('DB_ENGINE', 'Supabase');
        error_log("✅ Conectado a Supabase ($ipv4:$SUPABASE_PORT) con SSL (IPv4)");
    } catch (Exception | PDOException $e2) {
        die("❌ Error fatal: no se pudo conectar ni a MySQL ni a Supabase → " . $e2->getMessage());
    }
}
?>
