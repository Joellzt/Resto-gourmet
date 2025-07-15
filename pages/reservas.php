<?php
session_start();
require_once '../includes/conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar tabla horarios
$table_check = $conn->query("SHOW TABLES LIKE 'horarios'");
if ($table_check->num_rows == 0) {
    die("Error: La tabla de horarios no está configurada. Contacta al administrador.");
}

// Obtener horarios disponibles (corregido para usar la tabla horarios)
$horarios = $conn->query("SELECT id, TIME_FORMAT(hora, '%H:%i') as hora_formateada FROM horarios ORDER BY hora");
if (!$horarios) {
    die("Error al cargar horarios: " . $conn->error);
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar inputs
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $horario_id = (int)$_POST['horario_id'];
    $personas = (int)$_POST['personas'];
    $comentarios = $conn->real_escape_string($_POST['comentarios'] ?? '');
    $usuario_id = (int)$_SESSION['usuario_id'];

    // Verificar si ya tiene reserva para este día (usando consulta preparada)
    $stmt = $conn->prepare("SELECT id FROM reservas WHERE usuario_id = ? AND fecha = ?");
    $stmt->bind_param("is", $usuario_id, $fecha);
    $stmt->execute();
    $reserva_existente = $stmt->get_result();
    
    if ($reserva_existente->num_rows > 0) {
        $_SESSION['mensaje_error'] = "No puedes tener más de 1 reservación por día. Por favor modifica tu reserva existente.";
        $_SESSION['mostrar_mensaje'] = true;
        header("Location: reservas.php");
        exit;
    }

    // Validar disponibilidad (corregido para usar la tabla horarios)
    $stmt = $conn->prepare("
        SELECT h.capacidad_max, IFNULL(SUM(r.personas), 0) as total 
        FROM horarios h
        LEFT JOIN reservas r ON h.id = r.horario_id AND r.fecha = ? AND r.estado = 'confirmada'
        WHERE h.id = ?
        GROUP BY h.id
    ");
    $stmt->bind_param("si", $fecha, $horario_id);
    $stmt->execute();
    $disponibilidad = $stmt->get_result()->fetch_assoc();

    if (!$disponibilidad || ($disponibilidad['total'] + $personas) > $disponibilidad['capacidad_max']) {
        $error = "No hay suficiente disponibilidad para el horario seleccionado.";
    } else {
        // Insertar reserva (usando consulta preparada)
        $stmt = $conn->prepare("
            INSERT INTO reservas (usuario_id, horario_id, fecha, personas, comentarios) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisis", $usuario_id, $horario_id, $fecha, $personas, $comentarios);
        
        if ($stmt->execute()) {
            $_SESSION['reserva_exitosa'] = true;
            $_SESSION['datos_reserva'] = [
                'id' => $conn->insert_id,
                'fecha' => $fecha,
                'horario' => $conn->query("SELECT TIME_FORMAT(hora, '%H:%i') as hora FROM horarios WHERE id = $horario_id")->fetch_assoc()['hora'],
                'personas' => $personas
            ];
            header("Location: reserva_exitosa.php");
            exit;
        } else {
            $error = "Error al guardar la reserva. Intenta nuevamente.";
        }
    }
}

// Limpiar mensaje después de mostrarse
if (isset($_GET['limpiar_mensaje'])) {
    unset($_SESSION['mostrar_mensaje']);
    unset($_SESSION['mensaje_error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar - Resto Gourmet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Nueva Reserva</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="border p-4 rounded">
            <div class="mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required 
                       min="<?= date('Y-m-d') ?>" 
                       max="<?= date('Y-m-d', strtotime('+3 months')) ?>"
                       value="<?= date('Y-m-d') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Horario</label>
                <select name="horario_id" class="form-select" required>
                    <option value="">Seleccionar horario</option>
                    <?php while($horario = $horarios->fetch_assoc()): ?>
                        <option value="<?= $horario['id'] ?>"><?= htmlspecialchars($horario['hora_formateada']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de personas (1-10)</label>
                <input type="number" name="personas" class="form-control" min="1" max="10" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Comentarios (opcional)</label>
                <textarea name="comentarios" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2">Confirmar Reserva</button>
                <a href="../index.php" class="btn btn-secondary">Cancelar</a>
            </div>
            
            <?php if (isset($_SESSION['mostrar_mensaje']) && isset($_SESSION['mensaje_error'])): ?>
                <div class="reserva-alert mt-3" id="mensajeReserva">
                    <span class="close-message" onclick="cerrarMensaje()">×</span>
                    <p><?= htmlspecialchars($_SESSION['mensaje_error']) ?></p>
                    <p>¿Deseas <a href="mis_reservas.php">ver o modificar tu reserva existente</a>?</p>
                </div>
                
                <script>
                    function cerrarMensaje() {
                        document.getElementById('mensajeReserva').style.display = 'none';
                        window.location.href = 'reservas.php?limpiar_mensaje=1';
                    }
                    setTimeout(function() {
                        cerrarMensaje();
                    }, 5000);
                </script>
            <?php endif; ?>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>