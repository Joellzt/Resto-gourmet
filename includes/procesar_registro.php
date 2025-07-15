<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verificar->bind_param("s", $email);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $mensaje = "El correo ya está registrado";
        $tipo = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $password);
        
        if ($stmt->execute()) {
            $mensaje = "Usuario registrado correctamente";
            $tipo = "exito";
            header("Refresh: 3; url=../pages/login.php");
        } else {
            $mensaje = "Error al registrar usuario";
            $tipo = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm border-0" style="max-width: 400px; width: 100%">
            <div class="card-body text-center p-4">
                <div class="display-4 mb-3 <?php echo $tipo === 'exito' ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $tipo === 'exito' ? '✓' : '✕'; ?>
                </div>
                <h2 class="h4 mb-3 fw-normal">
                    <?php echo $tipo === 'exito' ? 'Registro exitoso' : 'Error en el registro'; ?>
                </h2>
                <p class="text-muted mb-4"><?php echo $mensaje; ?></p>
                
                <?php if ($tipo === 'exito'): ?>
                    <div class="progress mb-3" style="height: 4px">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated w-100"></div>
                    </div>
                    <p class="small text-muted mb-0">Redirigiendo al login...</p>
                <?php else: ?>
                    <a href="../index.php" class="btn btn-primary px-4">Volver a intentar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>