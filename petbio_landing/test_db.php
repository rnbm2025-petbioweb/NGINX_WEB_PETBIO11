<?php
// ==========================================================
// 🔍 TEST DE CONEXIÓN MARIADB LOCAL (Termux)
// Autor: ChatGPT + Juan Osorno
// Fecha: 2025-10-24
// ==========================================================

// Datos de conexión
$host = "127.0.0.1";  // 👈 importante: usar IP, no "localhost"
$user = "root";
$pass = "R00t_Segura_2025!";
$db   = "db__produccion_petbio_segura_2025";

// Ruta del socket (para fallback si el TCP falla)
$socket_path = "/data/data/com.termux/files/usr/var/run/mysqld.sock";

// ----------------------------------------------------------
// 1️⃣ Intentar conexión por TCP/IP
// ----------------------------------------------------------
$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "⚠️ Conexión TCP falló: " . $conn->connect_error . "\n";
    echo "🔁 Intentando conexión por socket directo...\n";

    // ----------------------------------------------------------
    // 2️⃣ Intentar conexión usando socket directo
    // ----------------------------------------------------------
    $conn = @new mysqli('localhost', $user, $pass, $db, 0, $socket_path);

    if ($conn->connect_error) {
        die("❌ Error de conexión total: " . $conn->connect_error . "\n");
    } else {
        echo "✅ Conexión exitosa a la base de datos (por socket).\n";
    }
} else {
    echo "✅ Conexión exitosa a la base de datos (por TCP/IP).\n";
}

// ----------------------------------------------------------
// 3️⃣ Mostrar tablas disponibles
// ----------------------------------------------------------
$result = $conn->query("SHOW TABLES;");
if ($result && $result->num_rows > 0) {
    echo "📋 Tablas encontradas en '$db':\n";
    while ($row = $result->fetch_array()) {
        echo " - " . $row[0] . "\n";
    }
} else {
    echo "⚠️ No se encontraron tablas o no se pudo ejecutar la consulta.\n";
}

// ----------------------------------------------------------
$conn->close();
?>
