<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/login.php");
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Validar campos vacíos
if (empty($email) || empty($password)) {
    header("Location: ../pages/login.php?error=" . urlencode("Todos los campos son requeridos"));
    exit;
}

// Buscar usuario
$stmt = $conn->prepare("SELECT id, nombre, password, es_admin, es_editor FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../pages/login.php?error=" . urlencode("Usuario no encontrado"));
    exit;
}

$usuario = $result->fetch_assoc();

// Verificar contraseña
if (!password_verify($password, $usuario['password'])) {
    header("Location: ../pages/login.php?error=" . urlencode("Contraseña incorrecta"));
    exit;
}

// Variables para manejar los roles
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['es_admin'] = $usuario['es_admin'];
$_SESSION['es_editor'] = $usuario['es_editor'] ?? 0;

// Redirección según el rol
if ($usuario['es_admin'] == 1) {
    header("Location: ../pages/resto-admin.php");
} elseif ($usuario['es_editor'] == 1) {
    header("Location: ../pages/resto-admin.php");
} else {
    header("Location: ../index.php");
}
exit;
?>