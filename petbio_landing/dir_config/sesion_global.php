<?php
// No iniciar sesión aquí para evitar conflictos
require_once(__DIR__ . '/conexion_petbio_hibrida.php');

if (!isset($pdo)) {
    die("❌ Conexión no inicializada.");
}
?>
