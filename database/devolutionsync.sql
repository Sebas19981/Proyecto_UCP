
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for usuarios
-- (TABLA PADRE - sin dependencias, se crea primero)
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `USR` varchar(10) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `PAS` varchar(10) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `NOMBRE` varchar(60) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `GRADO` int(1) NOT NULL,
  PRIMARY KEY (`USR`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- ----------------------------
-- Table structure for producto
-- (TABLA PADRE - sin dependencias, se crea segundo)
-- Charset unificado a utf8mb4 para compatibilidad FK
-- ----------------------------
DROP TABLE IF EXISTS `producto`;
CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Item` int(11) NOT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `minimo` float NOT NULL,
  `maximo` float NOT NULL,
  `pesoProm` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codItem` (`Item`)
) ENGINE=InnoDB AUTO_INCREMENT=768 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- ----------------------------
-- Table structure for devoluciones
-- (TABLA HIJA - depende de: usuarios, producto)
-- Tipos ajustados para FK:
--   codigo_producto:    VARCHAR(50) -> INT(11)
--   usuario_creador:    VARCHAR(50) -> VARCHAR(10)
--   usuario_revisor:    VARCHAR(50) -> VARCHAR(10)
-- ----------------------------
DROP TABLE IF EXISTS `devoluciones`;
CREATE TABLE `devoluciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nit` varchar(20) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre_cliente` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_spanish2_ci,
  `correo_solicitante` varchar(150) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codigo_producto` int(11) NOT NULL,
  `descripcion_producto` text COLLATE utf8mb4_spanish2_ci,
  `unidad` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `kg` decimal(10,2) DEFAULT NULL,
  `motivo` enum('devolucion','faltante','sobrante') COLLATE utf8mb4_spanish2_ci NOT NULL,
  `cantidad_und` int(11) DEFAULT NULL,
  `cantidad_kg` decimal(10,2) DEFAULT NULL,
  `evidencia` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_spanish2_ci,
  `estado` enum('pendiente','aprobado','rechazado') COLLATE utf8mb4_spanish2_ci DEFAULT 'pendiente',
  `observacion_admin` text COLLATE utf8mb4_spanish2_ci,
  `codigo_admin` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `usuario_creador` varchar(10) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `usuario_revisor` varchar(10) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_codigo_producto` (`codigo_producto`),
  KEY `idx_usuario_creador` (`usuario_creador`),
  KEY `idx_usuario_revisor` (`usuario_revisor`),
  KEY `idx_estado_fecha` (`estado`, `fecha_creacion`),
  KEY `idx_nit` (`nit`),
  CONSTRAINT `fk_devolucion_producto` FOREIGN KEY (`codigo_producto`) REFERENCES `producto` (`Item`) ON UPDATE CASCADE,
  CONSTRAINT `fk_devolucion_creador` FOREIGN KEY (`usuario_creador`) REFERENCES `usuarios` (`USR`) ON UPDATE CASCADE,
  CONSTRAINT `fk_devolucion_revisor` FOREIGN KEY (`usuario_revisor`) REFERENCES `usuarios` (`USR`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- ----------------------------
-- Table structure for notificaciones
-- (TABLA HIJA - depende de: devoluciones, usuarios)
-- Tipo ajustado para FK:
--   usuario_destino: VARCHAR(50) -> VARCHAR(10)
-- ----------------------------
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_devolucion` int(11) NOT NULL,
  `mensaje` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_destino` varchar(10) COLLATE utf8mb4_spanish2_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_devolucion` (`id_devolucion`),
  KEY `idx_usuario_leida` (`usuario_destino`, `leida`),
  CONSTRAINT `fk_notificacion_devolucion` FOREIGN KEY (`id_devolucion`) REFERENCES `devoluciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notificacion_usuario` FOREIGN KEY (`usuario_destino`) REFERENCES `usuarios` (`USR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- ----------------------------
-- Table structure for login_attempts
-- (TABLA HIJA - depende de: usuarios)
-- Tipo ajustado para FK:
--   username: VARCHAR(100) -> VARCHAR(10)
-- ----------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `username` varchar(10) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `attempts` int(11) DEFAULT '1',
  `last_attempt` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_username` (`ip_address`, `username`),
  KEY `idx_last_attempt` (`last_attempt`),
  CONSTRAINT `fk_login_usuario` FOREIGN KEY (`username`) REFERENCES `usuarios` (`USR`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

SET FOREIGN_KEY_CHECKS=1;
