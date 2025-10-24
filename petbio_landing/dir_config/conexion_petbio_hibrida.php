<?php
// ==========================================================
// 🧩 conexión_petbio_hibrida.php
// 🔹 Conexión híbrida MySQL → Supabase (IPv4 Render compatible)
// 🔹 Prioriza MySQL local, usa Supabase si falla
// ==========================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// ⚙️ CONFIG MYSQL LOCAL / RENDER
// ===============================
$MYSQL_HOST = getenv('MYSQL_HOST') ?: '127.0.0.1';
$MYSQL_PORT = getenv('MYSQL_PORT') ?: '3306';
$MYSQL_USER = getenv('MYSQL_USER') ?: 'root';
$MYSQL_PASS = getenv('MYSQL_PASSWORD') ?: 'R00t_Segura_2025!';
$MYSQL_DB   = getenv('MYSQL_DATABASE') ?: 'db__produccion_petbio_segura_2025';

// ===============================
// ⚙️ CONFIG SUPABASE (PostgreSQL)
// ===============================
$SUPABASE_HOST = getenv('SUPABASE_HOST') ?: 'db.jbsxvonnrahhfffeacdy.supabase.co';
$SUPABASE_PORT = getenv('SUPABASE_PORT') ?: '5432';
$SUPABASE_USER = getenv('SUPABASE_USER') ?: 'postgres';
$SUPABASE_PASS = getenv('SUPABASE_PASS') ?: 'BiometriaPetbio2025*';
$SUPABASE_DB   = getenv('SUPABASE_DB')   ?: 'postgres';

try {
    // =======================================================
    // 🔹 1. Intentar conexión local MySQL
    // =======================================================
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
        // =======================================================
        // 🔹 2. Resolver solo IPv4 de Supabase
        // =======================================================
        $ipv4 = trim(shell_exec("getent hosts $SUPABASE_HOST | awk '{print \$1}' | grep -E '^[0-9.]+' | head -n1"));

        if (!$ipv4) {
            // fallback a gethostbyname (solo devuelve IPv4 si existe)
            $ipv4 = gethostbyname($SUPABASE_HOST);
        }

        if (empty($ipv4) || $ipv4 === $SUPABASE_HOST) {
            throw new Exception("No se pudo resolver IPv4 para $SUPABASE_HOST");
        }

        // =======================================================
        // 🔹 3. Conexión Supabase (PostgreSQL IPv4 + SSL)
        // =======================================================
        $dsn = "pgsql:host=$ipv4;port=$SUPABASE_PORT;dbname=$SUPABASE_DB;sslmode=require";

        $pdo = new PDO($dsn, $SUPABASE_USER, $SUPABASE_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        define('DB_ENGINE', 'Supabase');
        error_log("✅ Conectado a Supabase ($ipv4:$SUPABASE_PORT) con SSL (IPv4)");
    } catch (Exception | PDOException $e2) {
        // =======================================================
        // 🔹 4. Error fatal (ni MySQL ni Supabase)
        // =======================================================
        error_log("❌ Error híbrido: " . $e2->getMessage());
        die("❌ Error fatal: no se pudo conectar ni a MySQL ni a Supabase → " . $e2->getMessage());
    }
}
?>
