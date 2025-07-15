<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Procesar eliminación de reserva
if (isset($_POST['eliminar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];
    
    // Verificar que la reserva pertenece al usuario antes de eliminar
    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $reserva_id, $_SESSION['usuario_id']);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['mensaje'] = "Reserva eliminada correctamente";
    } else {
        $_SESSION['error'] = "No se pudo eliminar la reserva o no existe";
    }
    
    header("Location: mis_reservas.php");
    exit();
}

// Obtener reservas del usuario (CONSULTA CORREGIDA)
$stmt = $conn->prepare("
    SELECT r.*, 
           TIME_FORMAT(h.hora, '%H:%i') as horario_formateado,
           h.hora as hora_original
    FROM reservas r
    JOIN horarios h ON r.horario_id = h.id
    WHERE r.usuario_id = ?
    ORDER BY r.fecha, h.hora
");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$reservas = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Mis Reservas</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>

        <?php if (count($reservas) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Personas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></td>
                                <td><?= htmlspecialchars($reserva['horario_formateado']) ?></td>
                                <td><?= htmlspecialchars($reserva['personas']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $reserva['estado'] == 'confirmada' ? 'success' : 
                                        ($reserva['estado'] == 'cancelada' ? 'danger' : 'warning')
                                    ?>">
                                        <?= ucfirst(htmlspecialchars($reserva['estado'] ?? 'Confirmada')) ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="reserva_id" value="<?= htmlspecialchars($reserva['id']) ?>">
                                        <button type="submit" name="eliminar_reserva" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta reserva?')">
                                            Eliminar
                                        </button>
                                    </form>
                                    <a href="modificar_reserva.php?id=<?= htmlspecialchars($reserva['id']) ?>" class="btn btn-primary btn-sm">
                                        Modificar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No tienes reservas registradas.</div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary">Volver al inicio</a>
            <a href="reservas.php" class="btn btn-primary ms-2">Nueva Reserva</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>