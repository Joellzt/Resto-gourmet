<?php
session_start();

// Verificar si hay una reserva exitosa
if (!isset($_SESSION['reserva_exitosa'])) {
    header("Location: ../index.php");
    exit();
}

// Verificar si el usuario ha confirmado
$confirmado = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $confirmado = true;
    
    // Aquí puedes agregar lógica adicional si necesitas actualizar la BD
    require_once '../includes/conexion.php';
    $reserva_id = $_SESSION['datos_reserva']['id'] ?? null;
    
    if ($reserva_id) {
        $conn->query("UPDATE reservas SET estado = 'Confirmada' WHERE id = $reserva_id");
    }
    
    // Limpiar la sesión y redirigir
    unset($_SESSION['reserva_exitosa']);
    unset($_SESSION['datos_reserva']);
    header("Location: ../index.php");
    exit();
}

// Obtener datos de la reserva
$datos_reserva = $_SESSION['datos_reserva'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Exitosa - Resto Gourmet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Modal de confirmación -->
    <div class="modal fade show d-block" id="modalReservaExitosa" tabindex="-1" aria-labelledby="modalReservaExitosaLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalReservaExitosaLabel">¡Reserva Confirmada!</h5>
                </div>
                <div class="modal-body">
                    <p>Tu reserva ha sido registrada exitosamente:</p>
                    <ul>
                        <li><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($datos_reserva['fecha'])) ?></li>
                        <li><strong>Horario:</strong> <?= $datos_reserva['horario'] ?></li>
                        <li><strong>Personas:</strong> <?= $datos_reserva['personas'] ?></li>
                    </ul>
                    <p>Te esperamos en nuestro restaurante.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <button type="submit" name="confirmar" class="btn btn-primary">Aceptar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>