<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Resto Gourmet</title>
    <!-- mi css--->
    <link rel="stylesheet" href="../assets./css/main.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="mng-log-res">
    <div class="mod-container">
        <div class="mod-header">
            <h2><i class="bi bi-person-plus"></i> Crear Cuenta</h2>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="../includes/procesar_registro.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" required>
                    <div class="invalid-feedback">
                        Por favor ingresa tu nombre completo.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    <div class="invalid-feedback">
                        Por favor ingresa un email válido.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control form-control-lg" id="telefono" name="telefono" required>
                    <div class="invalid-feedback">
                        Por favor ingresa tu teléfono.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required minlength="6">
                    <div class="invalid-feedback">
                        La contraseña debe tener al menos 6 caracteres.
                    </div>
                    <div class="form-text">
                        Mínimo 6 caracteres.
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn-acc btn-lg py-2">
                        <i class="bi bi-person-check"></i> Registrarme
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">¿Ya tenés cuenta? <a href="login.php" class="text-decoration-none">Iniciar sesión</a></p>
                </div>
            </form>
        </div>
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
        })()
    </script>
</body>

</html>