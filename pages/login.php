<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: " . ($_SESSION['es_admin'] == 1 ? 'resto-admin.php' : 'index.php'));
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Resto Gourmet</title>
    <link rel="stylesheet" href="../assets./css/main.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

</head>

<body class="mng-log-res">
    <div class="mod-container">
        <div class="mod-header">
            <h2><i class="bi bi-person" style="font-size: 2.5rem;"></i></h2>
            <h2 class="mb-3">Iniciar Sesión</h2>
            <p class="text-muted">Ingresá tus credenciales para acceder</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="../includes/procesar_login.php" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                <div class="invalid-feedback">
                    Por favor ingresa tu email.
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback">
                    Por favor ingresa tu contraseña.
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn-acc btn-lg py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </div>

            <div class="text-center">
                <p class="mb-0">¿No tenés cuenta? <a href="registro.php" class="link-secondary">Registrate aquí</a></p>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        (function() {
            'use strict'

            const forms = document.querySelectorAll('.needs-validation')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })

            // Mostrar/ocultar contraseña
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        })()
    </script>
</body>

</html>