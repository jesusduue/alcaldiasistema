-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 10-01-2024 a las 12:39:59
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sist_alcaldia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificador`
--

DROP TABLE IF EXISTS `clasificador`;
CREATE TABLE IF NOT EXISTS `clasificador` (
  `id_clasificador` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_clasificador`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clasificador`
--

INSERT INTO `clasificador` (`id_clasificador`, `nombre`) VALUES
(1, 'DEUDA ACTUAL DE CATASTRO'),
(2, 'DEUDA MOROSA DE CATASTRO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contribuyente`
--

DROP TABLE IF EXISTS `contribuyente`;
CREATE TABLE IF NOT EXISTS `contribuyente` (
  `id_contribuyente` int NOT NULL AUTO_INCREMENT,
  `cedula_rif` varchar(15) NOT NULL,
  `razon_social` varchar(50) NOT NULL,
  `estado_cont` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_contribuyente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contribuyente`
--

INSERT INTO `contribuyente` (`id_contribuyente`, `cedula_rif`, `razon_social`, `estado_cont`) VALUES
(1, 'V-9200515', 'MARIA DE LOS ANGELES QUINTERO QUINTERO', 'activo'),
(2, 'V-26015816', 'JESUS MIGUEL DUQUE QUINTERO', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_recibo`
--

DROP TABLE IF EXISTS `detalle_recibo`;
CREATE TABLE IF NOT EXISTS `detalle_recibo` (
  `id_detalle_recibo` int NOT NULL AUTO_INCREMENT,
  `fecha_det_recibo` date NOT NULL,
  `cod_factura` int NOT NULL,
  `impuesto_A` int DEFAULT NULL,
  `monto_impuesto_A` decimal(10,2) NOT NULL,
  `impuesto_B` int DEFAULT NULL,
  `monto_impuesto_B` decimal(10,2) DEFAULT NULL,
  `impuesto_C` int DEFAULT NULL,
  `monto_impuesto_C` decimal(10,2) DEFAULT NULL,
  `impuesto_D` int DEFAULT NULL,
  `monto_impuesto_D` decimal(10,2) DEFAULT NULL,
  `impuesto_E` int DEFAULT NULL,
  `monto_impuesto_E` decimal(10,2) DEFAULT NULL,
  `impuesto_F` int DEFAULT NULL,
  `monto_impuesto_F` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_detalle_recibo`),
  KEY `cod_factura` (`cod_factura`),
  KEY `impuesto_A` (`impuesto_A`),
  KEY `impuesto_B` (`impuesto_B`),
  KEY `impuesto_C` (`impuesto_C`),
  KEY `impuesto_D` (`impuesto_D`),
  KEY `impuesto_E` (`impuesto_E`),
  KEY `impuesto_F` (`impuesto_F`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalle_recibo`
--

INSERT INTO `detalle_recibo` (`id_detalle_recibo`, `fecha_det_recibo`, `cod_factura`, `impuesto_A`, `monto_impuesto_A`, `impuesto_B`, `monto_impuesto_B`, `impuesto_C`, `monto_impuesto_C`, `impuesto_D`, `monto_impuesto_D`, `impuesto_E`, `monto_impuesto_E`, `impuesto_F`, `monto_impuesto_F`) VALUES
(2, '2024-01-09', 1, 1, '150.11', 2, '250.11', 1, NULL, 1, NULL, 1, NULL, 1, NULL),
(3, '2024-01-09', 2, 1, '3000.00', 1, '500.00', 1, NULL, 1, NULL, 2, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

DROP TABLE IF EXISTS `factura`;
CREATE TABLE IF NOT EXISTS `factura` (
  `num_factura` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `id_usuario` int NOT NULL,
  `cod_contribuyente` int NOT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `total_factura` decimal(10,2) DEFAULT NULL,
  `ESTADO_FACT` varchar(10) NOT NULL,
  PRIMARY KEY (`num_factura`),
  KEY `id_usuario` (`id_usuario`),
  KEY `cod_contribuyente` (`cod_contribuyente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`num_factura`, `fecha`, `id_usuario`, `cod_contribuyente`, `concepto`, `total_factura`, `ESTADO_FACT`) VALUES
(1, '2024-01-09', 1, 1, 'DESCRIPCION', '400.22', ''),
(2, '2024-01-09', 1, 1, 'DESCRIPCION', '3500.00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `rol` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`) VALUES
(1, '1'),
(2, '2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usu` int NOT NULL AUTO_INCREMENT,
  `nombre_usu` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `rol_id` int NOT NULL,
  PRIMARY KEY (`id_usu`),
  KEY `rol_id` (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usu`, `nombre_usu`, `usuario`, `clave`, `rol_id`) VALUES
(1, 'JESUS DUQUE', 'JESUS', '123456', 2);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_recibo`
--
ALTER TABLE `detalle_recibo`
  ADD CONSTRAINT `detalle_recibo_ibfk_1` FOREIGN KEY (`cod_factura`) REFERENCES `factura` (`num_factura`),
  ADD CONSTRAINT `detalle_recibo_ibfk_2` FOREIGN KEY (`impuesto_A`) REFERENCES `clasificador` (`id_clasificador`),
  ADD CONSTRAINT `detalle_recibo_ibfk_3` FOREIGN KEY (`impuesto_B`) REFERENCES `clasificador` (`id_clasificador`),
  ADD CONSTRAINT `detalle_recibo_ibfk_4` FOREIGN KEY (`impuesto_C`) REFERENCES `clasificador` (`id_clasificador`),
  ADD CONSTRAINT `detalle_recibo_ibfk_5` FOREIGN KEY (`impuesto_D`) REFERENCES `clasificador` (`id_clasificador`),
  ADD CONSTRAINT `detalle_recibo_ibfk_6` FOREIGN KEY (`impuesto_E`) REFERENCES `clasificador` (`id_clasificador`),
  ADD CONSTRAINT `detalle_recibo_ibfk_7` FOREIGN KEY (`impuesto_F`) REFERENCES `clasificador` (`id_clasificador`);

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usu`),
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`cod_contribuyente`) REFERENCES `contribuyente` (`id_contribuyente`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
