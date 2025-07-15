<?php
session_start();
$nombre_usuario = $_SESSION['usuario_nombre'] ?? '';

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de sesión - Resto Gourmet</title>
        <link rel="stylesheet" href="../assets./css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="mng-log-res">
    <div class="mod-container">
        <h1><i class="bi bi-box-arrow-right"></i> Sesión cerrada</h1>
        <?php if (!empty($nombre_usuario)): ?>
            <p class="lead">Hasta pronto, <?= htmlspecialchars($nombre_usuario) ?>.</p>
        <?php else: ?>
            <p class="lead">Has cerrado sesión correctamente.</p>
        <?php endif; ?>
        <p>Gracias por visitar Resto Gourmet.</p>
        <a href="../index.php" class="btn btn-danger d-flex justify-content-center">
            <i class="bi bi-house-door"></i> Volver al inicio
        </a>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>