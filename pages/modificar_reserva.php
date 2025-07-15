<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verificar que se proporcionó un ID de reserva
if (!isset($_GET['id'])) {
    header("Location: mis_reservas.php");
    exit();
}

$reserva_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener los datos actuales de la reserva (CONSULTA CORREGIDA)
$stmt = $conn->prepare("
    SELECT r.*, 
           TIME_FORMAT(h.hora, '%H:%i') as horario_formateado
    FROM reservas r
    JOIN horarios h ON r.horario_id = h.id
    WHERE r.id = ? AND r.usuario_id = ?
");
$stmt->bind_param("ii", $reserva_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$reserva = $result->fetch_assoc();

// Verificar que la reserva existe y pertenece al usuario
if (!$reserva) {
    $_SESSION['error'] = "Reserva no encontrada o no tienes permisos para modificarla";
    header("Location: mis_reservas.php");
    exit();
}

// Obtener todos los horarios disponibles (CONSULTA CORREGIDA)
$horarios = $conn->query("SELECT id, TIME_FORMAT(hora, '%H:%i') as horario FROM horarios ORDER BY hora");

// Procesar el formulario de modificación (CONSULTAS CORREGIDAS)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $horario_id = (int)$_POST['horario_id'];
    $personas = (int)$_POST['personas'];
    $comentarios = $conn->real_escape_string($_POST['comentarios'] ?? '');

    // Validar disponibilidad del nuevo horario (CONSULTA CORREGIDA)
    $stmt = $conn->prepare("
        SELECT h.capacidad_max, 
               IFNULL(SUM(r.personas), 0) as total
        FROM horarios h
        LEFT JOIN reservas r ON h.id = r.horario_id 
            AND r.fecha = ? 
            AND r.id != ?
            AND r.estado = 'confirmada'
        WHERE h.id = ?
        GROUP BY h.id
    ");
    $stmt->bind_param("sii", $fecha, $reserva_id, $horario_id);
    $stmt->execute();
    $disponibilidad = $stmt->get_result()->fetch_assoc();

    if ($disponibilidad && ($disponibilidad['total'] + $personas) <= $disponibilidad['capacidad_max']) {
        // Actualizar reserva (CONSULTA PREPARADA)
        $stmt = $conn->prepare("
            UPDATE reservas SET 
                fecha = ?,
                horario_id = ?,
                personas = ?,
                comentarios = ?,
                estado = 'confirmada'
            WHERE id = ? AND usuario_id = ?
        ");
        $stmt->bind_param("sissii", $fecha, $horario_id, $personas, $comentarios, $reserva_id, $usuario_id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Reserva modificada correctamente";
            header("Location: mis_reservas.php");
            exit();
        } else {
            $error = "Error al actualizar la reserva. Intenta nuevamente.";
        }
    } else {
        $error = "No hay suficiente disponibilidad para el horario seleccionado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva - Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="form-container">
            <h2 class="form-title">Modificar Reserva</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" required 
                           value="<?= htmlspecialchars($reserva['fecha']) ?>"
                           min="<?= date('Y-m-d') ?>" 
                           max="<?= date('Y-m-d', strtotime('+3 months')) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Horario</label>
                    <select name="horario_id" class="form-select" required>
                        <option value="">Seleccionar horario</option>
                        <?php while($horario = $horarios->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($horario['id']) ?>" 
                                <?= ($horario['id'] == $reserva['horario_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($horario['horario']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Número de personas (1-10)</label>
                    <input type="number" name="personas" class="form-control" 
                           min="1" max="10" required
                           value="<?= htmlspecialchars($reserva['personas']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Comentarios (opcional)</label>
                    <textarea name="comentarios" class="form-control" rows="3"><?= 
                        htmlspecialchars($reserva['comentarios'] ?? '') ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2">Guardar Cambios</button>
                    <a href="mis_reservas.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>