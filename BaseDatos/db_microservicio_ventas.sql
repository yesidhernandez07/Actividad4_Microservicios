-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-08-2026 a las 03:46:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_microservicio_ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `producto` varchar(150) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_cliente`, `producto`, `cantidad`, `precio`, `total`, `fecha_venta`) VALUES
(2, 2, 'Martillo', 2, 25000.00, 50000.00, '2026-08-16 23:08:05'),
(3, 2, 'Destornillador', 1, 15000.00, 15000.00, '2026-08-16 23:08:42'),
(4, 9999, 'Taladro', 1, 80000.00, 80000.00, '2026-08-16 23:09:34'),
(5, 9999, 'Taladro', 1, 80000.00, 80000.00, '2026-08-16 23:13:16'),
(6, 2, 'Destornillador', 1, 15000.00, 15000.00, '2026-08-16 23:19:42'),
(7, 2, 'Taladro', 1, 150000.00, 150000.00, '2026-08-16 23:40:20'),
(9, 2, 'Taladro', 1, 150000.00, 150000.00, '2026-08-17 00:23:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
