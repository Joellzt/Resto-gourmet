<?php
session_start();
$es_usuario_logueado = isset($_SESSION['usuario_id']);
$url_reservas = $es_usuario_logueado ? './pages/reservas.php' : './pages/login.php?redirect=reservas';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resto Reservas Gourmet</title>
    <!-- CSS -->
    <link rel="stylesheet" href="./assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <header class="header-reserv position-relative overflow-hidden">
        <img src="./assets/img/resto-header.jpg" alt="Restaurante Gourmet"
            class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">

        <!-- Contenido principal  -->
        <div class="position-relative z-1 d-flex flex-column">
            <!-- Navbar transparente -->
            <nav class="navbar navbar-expand-lg py-3 bg-transparent">
                <div class="container">
                    <a class="navbar-brand text-white fw-bold" href="./index.php">
                        <i class="bi bi-egg-fried me-2"></i>Resto Gourmet
                    </a>

                    <div class="ms-auto d-flex align-items-center">
                        <?php if ($es_usuario_logueado): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?= htmlspecialchars($_SESSION['nombre']) ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="./pages/mis_reservas.php">
                                            <i class="bi bi-calendar-check me-2"></i>Mis reservas</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="./pages/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="./pages/login.php" class="btn btn-outline-light btn-sm me-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión
                            </a>
                            <a href="./pages/registro.php" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-person-plus me-1"></i>Registrarse
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>

            <!-- Hero Section  -->
            <section class="hero-reserv py-5 flex-grow-1 d-flex align-items-center">
                <div class="container text-center py-5">
                    <h1 class="display-3 fw-bold mb-4 text-white">
                        Resto Reservas Gourmet
                    </h1>
                    <p class="lead mb-5 text-white fw-bold">
                        Disfrutá de una experiencia gastronómica única
                    </p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap pt-5">
                        <a href="<?= $url_reservas ?>" class="btn btn-outline-danger btn-lg px-4 py-2 rounded-pill">
                            <i class="bi bi-calendar-check me-2"></i>Reservar mesa
                        </a>
                        <a href="./pages/menu.html" class="btn btn-outline-success btn-lg px-4 py-2 rounded-pill">
                            <i class="bi bi-menu-up me-2"></i>Ver menú
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1 py-5 bg-black">
        <section class="container my-5 ">
            <div class="text-center mb-5">
                <h2 class="display-6 text-white fw-medium fs-1">Desde 1960</h2>
                <p class="text-light fw-medium fs-5 pt-2">Servicio excepcional en el corazón de Buenos Aires</p>
            </div>

            <div class="row g-4">
                <!-- Ubicación -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm bg-black">
                        <a class="link-offset-2 link-underline link-underline-opacity-0" href="https://www.google.com/maps" target="_blank">

                            <div class="card-body text-center p-5">
                                <i class="bi bi-geo-alt-fill text-white fs-1 mb-3"></i>
                                <h3 class="h5 card-title fw-bold text-white">Ubicación</h3>
                                <p class="card-text text-light">Argentina 1355, Buenos Aires</p>
                            </div>
                        </a>

                    </div>
                </div>

                <!-- Eventos -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm bg-black">
                        <div class="card-body text-center p-5">
                            <i class="bi bi-music-note-beamed text-white fs-1 mb-3"></i>
                            <h3 class="h5 card-title fw-bold text-white">Noches de Tango</h3>
                            <p class="card-text text-light">Viernes 21:00 hs</p>
                        </div>
                    </div>
                </div>

                <!-- Bodega -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a class="link-offset-2 link-underline link-underline-opacity-0" href="https://www.astrologianora.com.ar/joel1" target="_blank">
                            <div class="card-body text-center p-5 bg-black">
                                <i class="bi bi-cup-fill text-white fs-1 mb-3 "></i>
                                <h3 class="h5 card-title fw-bold text-white">Café de especialidad</h3>
                                <p class="card-text text-light">Alianza con Flat&white</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Preguntas Frecuentes -->
        <section class="container my-5 py-5 bg-black rounded-5">
            <div class="text-center mb-5">
                <h2 class="display-6 text-white fw-medium fs-1">Preguntas Frecuentes</h2>
                <p class="text-white-50 fs-5">Encuentra respuestas a las dudas más comunes</p>
            </div>

            <div class="accordion" id="faqAccordion">
                <!-- Pregunta 1 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed bg-dark text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne"
                            aria-expanded="false" aria-controls="collapseOne">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            ¿Cómo puedo hacer una reserva?
                        </button>
                    </h3>
                    <div id="collapseOne" class="accordion-collapse collapse bg-dark text-white"
                        aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Puedes realizar tu reserva directamente desde nuestro sitio web haciendo clic en el botón "Reservar mesa".
                            Si es tu primera vez, deberás registrarte. También puedes llamarnos al 11 1234-5678 de lunes a domingo
                            de 10:00 a 22:00 hs.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-dark text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                            aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            ¿Cuál es la política de cancelación?
                        </button>
                    </h3>
                    <div id="collapseTwo" class="accordion-collapse collapse bg-dark text-white"
                        aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Aceptamos cancelaciones sin cargo hasta 24 horas antes de la reserva. Para cancelaciones con menos de 24 horas
                            de anticipación, se aplicará un cargo equivalente al 50% del valor del menú por persona. Puedes cancelar
                            directamente desde tu perfil en nuestro sitio web.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed bg-dark text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                            aria-expanded="false" aria-controls="collapseThree">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            ¿Tienen opciones para vegetarianos o celíacos?
                        </button>
                    </h3>
                    <div id="collapseThree" class="accordion-collapse collapse bg-dark text-white"
                        aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí, contamos con un menú especial para vegetarianos, veganos y celíacos. Te recomendamos indicar cualquier
                            restricción alimentaria al momento de hacer la reserva para que nuestro equipo pueda prepararse adecuadamente.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed bg-dark text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFour"
                            aria-expanded="false" aria-controls="collapseFour">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            ¿Aceptan tarjetas de crédito?
                        </button>
                    </h3>
                    <div id="collapseFour" class="accordion-collapse collapse bg-dark text-white"
                        aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí, aceptamos todas las tarjetas de crédito y débito principales (Visa, Mastercard, American Express).
                            También puedes pagar en efectivo o mediante transferencia bancaria previa coordinación.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="accordion-item mb-3 border-0">
                    <h3 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed bg-dark text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFive"
                            aria-expanded="false" aria-controls="collapseFive">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            ¿Tienen estacionamiento propio?
                        </button>
                    </h3>
                    <div id="collapseFive" class="accordion-collapse collapse bg-dark text-white"
                        aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            No contamos con estacionamiento propio, pero hay varios estacionamientos públicos en las inmediaciones.
                            El más cercano se encuentra a media cuadra, en la calle Perú 1200. Ofrecemos un descuento del 20%
                            presentando el ticket de estacionamiento.
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 gap-5">
                <p class="text-white-50 fs-5">¿No encontraste lo que buscabas?</p>
                <a href="https://mail.google.com/mail/u/0/#inbox" class="btn btn-outline-light">
                    <i class="bi bi-envelope me-2"></i>Contáctanos
                </a>
            </div>
        </section>
    </main>

    <!-- Footer Mejorado -->
    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <div class="row g-4">
                <!-- Columna 1: Logo y descripción -->
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-egg-fried fs-3 text-white me-2"></i>
                        <span class="fs-4 fw-bold">Resto Gourmet</span>
                    </div>
                    <p class="text-emphasis">
                        Desde 1960 ofreciendo una experiencia gastronómica única en el corazón de Buenos Aires.
                        Tradición, calidad y servicio excepcional.
                    </p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <!-- Columna 2: Horarios -->
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4 text-white">Horarios</h5>
                    <ul class="list-unstyled text-emphasis">
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Lunes a Sabados</span>
                            <span>18:00 - 22:30 hs</span>
                        </li>
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Domingo</span>
                            <span>Cerrado</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <h6 class="fw-bold text-white">Noches de Tango</h6>
                        <p class="text-emphasis mb-0">Viernes 21:00 hs - Reserva con anticipación</p>
                    </div>
                </div>

                <!-- Columna 3: Contacto -->
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4 text-white">Contacto</h5>
                    <ul class="list-unstyled text-emphasis">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-geo-alt-fill text-white me-2 mt-1"></i>
                            <span>Argentina 1355, CABA, Buenos Aires</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-telephone-fill text-white me-2"></i>
                            <span>11 1234-5678</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-envelope-fill text-white me-2"></i>
                            <span>reservas@restogourmet.com</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-whatsapp text-white me-2"></i>
                            <span>11 9876-5432</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row">
                <div class="text-center text-md-start d-flex justify-content-center">
                    <p class="mb-0 text-emphasis">
                        © <?= date('Y') ?> Resto Gourmet. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>