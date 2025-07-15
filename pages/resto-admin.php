<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['es_admin'] != 1 && $_SESSION['es_editor'] != 1)) {
    header("Location: ../login.php");
    exit;
}

// Determinar tipo de usuario
$es_admin = ($_SESSION['es_admin'] == 1);
$es_editor = ($_SESSION['es_editor'] == 1);

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar reserva
    if (isset($_POST['eliminar_reserva'])) {
        $reserva_id = $_POST['reserva_id'];
        $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->bind_param("i", $reserva_id);
        $stmt->execute();
    }

    // Actualizar reserva
    if (isset($_POST['actualizar_reserva'])) {
        $reserva_id = $_POST['reserva_id'];
        $fecha = $_POST['fecha'];
        $horario_id = $_POST['horario_id'];
        $personas = $_POST['personas'];
        $estado = $_POST['estado'];

        $stmt = $conn->prepare("UPDATE reservas SET fecha = ?, horario_id = ?, personas = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("siisi", $fecha, $horario_id, $personas, $estado, $reserva_id);
        $stmt->execute();
    }

    // Eliminar usuario (SOLO para admin)
    if (isset($_POST['eliminar_usuario']) && $es_admin) {
        $usuario_id = $_POST['usuario_id'];
        $conn->query("DELETE FROM reservas WHERE usuario_id = $usuario_id");
        $conn->query("DELETE FROM usuarios WHERE id = $usuario_id");
    }
}

// Obtener datos
$usuarios = [];
$reservas = [];
$horarios = [];

try {
    // Consulta para usuarios
    $usuarios_result = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
    if ($usuarios_result) {
        $usuarios = $usuarios_result;
    } else {
        throw new Exception("Error al obtener usuarios: " . $conn->error);
    }

    // Consulta para reservas
    $reservas_result = $conn->query("
        SELECT r.*, 
               u.nombre AS usuario_nombre, 
               u.email, 
               TIME_FORMAT(h.hora, '%H:%i') AS horario_display
        FROM reservas r
        JOIN usuarios u ON r.usuario_id = u.id
        JOIN horarios h ON r.horario_id = h.id
        ORDER BY r.fecha DESC, h.hora DESC
    ");
    if ($reservas_result) {
        $reservas = $reservas_result;
    } else {
        throw new Exception("Error al obtener reservas: " . $conn->error);
    }

    // Consulta para horarios
    $horarios_result = $conn->query("SELECT id, TIME_FORMAT(hora, '%H:%i') as horario FROM horarios ORDER BY hora");
    if ($horarios_result) {
        $horarios = $horarios_result;
    } else {
        throw new Exception("Error al obtener horarios: " . $conn->error);
    }
} catch (Exception $e) {
    $error_message = htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel <?= $es_admin ? 'Admin' : 'Editor' ?> - Resto Gourmet</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center">
            <i class="bi bi-egg-fried fs-4 me-2"></i>
            <span class="fs-5 fw-bold">Resto <?= $es_admin ? 'Admin' : 'Editor' ?></span>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="#usuarios" class="nav-link" data-bs-toggle="collapse" data-bs-target="#usuarios-nav">
                    <i class="bi bi-people"></i>
                    <span>Usuarios</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul id="usuarios-nav" class="nav-content collapse show">
                    <li><a href="#usuarios" class="active">Lista de Usuarios</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#reservas" class="nav-link" data-bs-toggle="collapse" data-bs-target="#reservas-nav">
                    <i class="bi bi-calendar-check"></i>
                    <span>Reservas</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul id="reservas-nav" class="nav-content collapse show">
                    <li><a href="#reservas">Todas las Reservas</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <header class="admin-header d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h4 mb-0">Panel de <?= $es_admin ? 'Administración' : 'Editor' ?></h1>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-dark dropdown-toggle" type="button" id="adminDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['nombre']) ?>
                    <span class="badge bg-<?= $es_admin ? 'primary' : 'success' ?> ms-1">
                        <?= $es_admin ? 'Admin' : 'Editor' ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="../index.php">
                            <i class="bi bi-house-door me-2"></i>Volver al sitio</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="./logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </header>

        <!-- Content -->
        <div class="container-fluid py-4">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <!-- Sección Usuarios (visible para ambos) -->
            <div class="admin-card" id="usuarios">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Usuarios Registrados</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Registro</th>
                                    <th>Rol</th>
                                    <?php if ($es_admin): ?>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $usuario['id'] ?></td>
                                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                            <td>
                                                <?php if ($usuario['es_admin'] == 1): ?>
                                                    <span class="badge bg-primary">Admin</span>
                                                <?php elseif ($usuario['es_editor'] == 1): ?>
                                                    <span class="badge bg-success">Editor</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Usuario</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($es_admin): ?>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">
                                                        <button type="submit" name="eliminar_usuario" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('¿Eliminar este usuario y todas sus reservas?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= $es_admin ? '6' : '5' ?>" class="text-center">No hay usuarios registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección Reservas -->
            <div class="admin-card" id="reservas">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Reservas</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Personas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($reservas && $reservas->num_rows > 0): ?>
                                    <?php while ($reserva = $reservas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $reserva['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($reserva['usuario_nombre']) ?></strong><br>
                                                <small class="text-muted"><?= $reserva['email'] ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></td>
                                            <td><?= $reserva['horario_display'] ?></td>
                                            <td><?= $reserva['personas'] ?></td>
                                            <td>
                                                <span class="badge rounded-pill bg-<?=
                                                    $reserva['estado'] == 'Confirmada' ? 'success' : ($reserva['estado'] == 'Cancelada' ? 'danger' : 'warning')
                                                ?>">
                                                    <?= $reserva['estado'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editarReservaModal"
                                                    onclick="cargarDatosReserva(
                                                        '<?= $reserva['id'] ?>',
                                                        '<?= $reserva['fecha'] ?>',
                                                        '<?= $reserva['horario_id'] ?>',
                                                        '<?= $reserva['personas'] ?>',
                                                        '<?= $reserva['estado'] ?>'
                                                    )">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                                    <button type="submit" name="eliminar_reserva" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¿Eliminar esta reserva?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay reservas registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Reserva -->
    <div class="modal fade" id="editarReservaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="reserva_id" id="modalReservaId">

                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" id="modalFecha" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Horario</label>
                            <select name="horario_id" id="modalHorario" class="form-select" required>
                                <?php if ($horarios && $horarios->num_rows > 0): ?>
                                    <?php while ($horario = $horarios->fetch_assoc()): ?>
                                        <option value="<?= $horario['id'] ?>"><?= $horario['horario'] ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="">No hay horarios disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Personas</label>
                            <input type="number" name="personas" id="modalPersonas" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" id="modalEstado" class="form-select" required>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="actualizar_reserva" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar en móviles
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Cargar datos en el modal de edición
        function cargarDatosReserva(id, fecha, horarioId, personas, estado) {
            document.getElementById('modalReservaId').value = id;
            document.getElementById('modalFecha').value = fecha;
            document.getElementById('modalHorario').value = horarioId;
            document.getElementById('modalPersonas').value = personas;
            document.getElementById('modalEstado').value = estado;
        }
    </script>
</body>
</html>