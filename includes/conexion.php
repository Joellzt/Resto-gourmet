<?php
$host = "localhost";
$usuario = "root";
$pass = "";
$base_de_datos = "resto_db";

$conn = new mysqli($host, $usuario, $pass, $base_de_datos);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>
