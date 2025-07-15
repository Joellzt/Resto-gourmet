CREATE DATABASE IF NOT EXISTS `resto_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `resto_db`;

DELIMITER $$
CREATE PROCEDURE `limpiar_reservas_antiguas` (IN `dias` INT)
BEGIN
    DELETE FROM `reservas` 
    WHERE `fecha` < DATE_SUB(CURDATE(), INTERVAL dias DAY)
    AND `estado` != 'completada';
END$$
DELIMITER ;

CREATE TABLE `horarios` (
  `id` tinyint(4) NOT NULL,
  `hora` time NOT NULL,
  `capacidad_max` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `horarios` (`id`, `hora`, `capacidad_max`) VALUES
(1, '18:00:00', 30),
(2, '19:30:00', 30),
(3, '21:00:00', 30),
(4, '22:30:00', 30);

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `horario_id` tinyint(4) NOT NULL,
  `fecha` date NOT NULL,
  `personas` int(11) NOT NULL CHECK (`personas` between 1 and 10),
  `comentarios` text DEFAULT NULL,
  `estado` enum('confirmada','cancelada','completada') DEFAULT 'confirmada',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reservas` (`id`, `usuario_id`, `horario_id`, `fecha`, `personas`, `comentarios`, `estado`, `fecha_creacion`) VALUES
(14, 5, 2, '2025-06-25', 5, '', 'cancelada', '2025-06-14 22:21:01'),
(16, 2, 3, '2025-06-16', 10, '4 adultos 4 niños', 'confirmada', '2025-06-16 22:24:24'),
(18, 3, 2, '2025-06-17', 2, 'dos adultos', 'confirmada', '2025-06-17 02:05:24');

DELIMITER $$
CREATE TRIGGER `validar_capacidad_reserva` BEFORE INSERT ON `reservas` FOR EACH ROW BEGIN
    DECLARE capacidad_actual INT;
    DECLARE capacidad_maxima INT;
    
    SELECT SUM(`personas`) INTO capacidad_actual
    FROM `reservas`
    WHERE `horario_id` = NEW.`horario_id`
    AND `fecha` = NEW.`fecha`
    AND `estado` = 'confirmada';
    
    SELECT `capacidad_max` INTO capacidad_maxima
    FROM `horarios`
    WHERE `id` = NEW.`horario_id`;
    
    IF (capacidad_actual + NEW.`personas`) > capacidad_maxima THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No hay suficiente capacidad para este horario';
    END IF;
END$$
DELIMITER ;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `es_admin` tinyint(1) DEFAULT 0,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `es_editor` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `password`, `es_admin`, `fecha_registro`, `es_editor`) VALUES
(2, 'Paulo Dominguez', '', 'paulo71@gmail.com', '', '$2y$10$8D/dKJf9oXeWtlnQwSKYFuOJsuByiEzbwo7kAdz6PGZMNiWv1.N1W', 0, '2025-06-13 17:16:49', 0),
(3, 'Joel Lorenzetti', '', 'joellorenzetti12@gmail.com', '', '$2y$10$U.lXY5QoVY0faL37V5.E7u8N5UulAWyDslv0LnZ0Ccz0zkciqCiYm', 0, '2025-06-13 17:33:57', 0),
(4, 'Juan Lorenzetti', '', 'juanlorenzetti@gmail.com', '', '$2y$10$RTVCQWW6.CDr/2r4lPRtxODlvS0N0DI1J4YevCrV3VvJzfcjWeBjG', 0, '2025-06-13 17:34:43', 0),
(5, 'Analia Bateman', '', 'anbat@gmail.com', '', '$2y$10$DDLDh3Z0ee36b1zbRpKBNuSH5bAPJsJbNaGC2ox1/25uwZzBz3kdW', 0, '2025-06-14 22:20:17', 0),
(6, 'Justin Bieber', '', 'justin@gmail.com', '', '$2y$10$YYH7hYV9JfXMTGi8L42HweWkfJ9gSLdTxg3J4rXcEbqwadzflggx2', 0, '2025-06-15 17:59:14', 0),
(7, 'Federico Marcelo', '', 'fede@gmail.com', '', '$2y$10$9N1LJLmV9rNJ62BO5VS88.9qOUz3JkGTdRpZlS5gK3eWdzHdFxw0m', 0, '2025-06-15 19:11:00', 0),
(8, 'Admin', 'Sistema', 'admin@resto.com', '1122334455', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-06-16 22:38:44', 0),
(9, 'Jostin Berley', '', 'jostin@hotmail.com', '', '$2y$10$gBSc6VO0hqB8eqVo7zpDa.NZb4Nhv2IZtMeXu5YQtXeXIz7q9800K', 0, '2025-06-16 22:51:19', 0),
(10, 'Editor del Sistema', '', 'editor@resto.com', '', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-06-17 00:11:18', 1);

CREATE VIEW `disponibilidad_horarios` AS
SELECT `h`.`id` AS `id`, `h`.`hora` AS `hora`, `h`.`capacidad_max` AS `capacidad_max`, 
count(`r`.`id`) AS `reservas_activas`, sum(`r`.`personas`) AS `personas_reservadas`, 
`h`.`capacidad_max`- ifnull(sum(`r`.`personas`),0) AS `lugares_disponibles` 
FROM (`horarios` `h` 
LEFT JOIN `reservas` `r` ON `h`.`id` = `r`.`horario_id` AND `r`.`fecha` = curdate() AND `r`.`estado` = 'confirmada') 
GROUP BY `h`.`id`;

ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hora` (`hora`);

ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_reserva` (`usuario_id`,`fecha`,`horario_id`),
  ADD UNIQUE KEY `uc_usuario_fecha` (`usuario_id`,`fecha`),
  ADD KEY `horario_id` (`horario_id`),
  ADD KEY `idx_fecha` (`fecha`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `horarios`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`);
COMMIT;