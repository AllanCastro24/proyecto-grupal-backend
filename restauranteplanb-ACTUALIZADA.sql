-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-05-2022 a las 21:31:00
-- Versión del servidor: 5.7.33
-- Versión de PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `restauranteplanb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `nombre`, `apellidos`) VALUES
(1, 'Daniel', 'Silverio Acosta'),
(2, 'Mauricio', 'Robles Balderrama'),
(3, 'Francisco', 'Araujo Meza'),
(4, 'Joel', 'Bueno Sarabia'),
(5, 'Abram', 'Camargo López'),
(6, 'Mauricio', 'Corona Picos'),
(7, 'Allan', 'Castro Aguilar'),
(8, 'Daniel', 'Silverio Acosta'),
(9, 'Cristo', 'Rios'),
(10, 'Adrian', 'Borquez Parra'),
(11, 'Daniel', 'Zayas Olguin'),
(12, 'Ulises', 'Hernandez'),
(13, 'Daniel', 'Silverio Acosta'),
(14, 'Daniel', 'Silverio Acosta'),
(15, 'Daniel', 'Silverio Acosta'),
(16, 'Daniel', 'Silverio Acosta'),
(17, 'Daniel', 'Silverio Acosta'),
(18, 'Daniel', 'Silverio Acosta'),
(19, 'Daniel', 'Silverio Acosta'),
(20, 'Daniel', 'Silverio Acosta'),
(21, 'Daniel', 'Silverio Acosta'),
(22, 'Daniel', 'Silverio Acosta'),
(23, 'Daniel', 'Silverio Acosta'),
(24, 'Daniel', 'Silverio Acosta'),
(25, 'Mauricio', 'Robles Balderrama');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_fijos`
--

CREATE TABLE `gastos_fijos` (
  `id_gasto` int(11) NOT NULL,
  `tipo_gasto` int(11) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `cantidad` decimal(10,0) NOT NULL,
  `fecha` date NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `periodicidad` date DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `gastos_fijos`
--

INSERT INTO `gastos_fijos` (`id_gasto`, `tipo_gasto`, `descripcion`, `cantidad`, `fecha`, `id_sucursal`, `periodicidad`, `status`) VALUES
(1, 1, 'prueba', '100', '2022-03-24', 1, '2022-03-23', 1),
(2, 2, 'prueba2', '150', '2022-03-31', 3, '2022-03-31', 1),
(3, 2, 'modificado', '20', '2022-04-04', 1, '2022-04-04', 2),
(4, 1, 'test-update', '150', '2022-04-04', 3, '2022-03-31', 2),
(5, 2, 'insertado-update', '30', '2022-04-05', 3, '2022-04-25', 2),
(6, 2, 'insertado-2', '150', '2022-04-04', 3, '2022-03-31', 2);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `mas_vendido`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `mas_vendido` (
`id_producto` int(11)
,`fecha` date
,`cantidad` decimal(26,1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `menos_vendido`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `menos_vendido` (
`id_producto` int(11)
,`fecha` date
,`cantidad` decimal(26,1)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) NOT NULL,
  `nom_sucursal` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id_sucursal`, `nom_sucursal`) VALUES
(1, 'SUCURSAL-1'),
(2, 'SUCURSAL-2'),
(3, 'SUCURSAL-3'),
(4, 'SUCURSAL-4'),
(5, 'SUCURSAL-5'),
(6, 'SUCURSAL-6'),
(7, 'SUCURSAL-7'),
(8, 'SUCURSAL-8'),
(9, 'SUCURSAL-9'),
(10, 'SUCURSAL-10'),
(11, 'SUCURSAL-11'),
(12, 'SUCURSAL-12'),
(13, 'SUCURSAL-13'),
(14, 'SUCURSAL-14'),
(15, 'SUCURSAL-15'),
(16, 'SUCURSAL-16'),
(17, 'SUCURSAL-17'),
(18, 'SUCURSAL-18'),
(19, 'SUCURSAL-19'),
(20, 'SUCURSAL-20'),
(21, 'SUCURSAL-21'),
(22, 'SUCURSAL-22'),
(23, 'SUCURSAL-23'),
(24, 'SUCURSAL-24'),
(25, 'SUCURSAL-25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_gasto`
--

CREATE TABLE `tipo_gasto` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_gasto`
--

INSERT INTO `tipo_gasto` (`id_tipo`, `nombre`, `status`) VALUES
(1, 'tipo 1', 1),
(2, 'tipo 2', 1),
(3, 'test-update', 1),
(4, 'insertado-update', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` decimal(4,1) DEFAULT NULL,
  `id_mesero` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_producto`, `cantidad`, `id_mesero`, `fecha`, `id_sucursal`) VALUES
(1, 1, '271.2', 1, '2021-03-30', 1),
(2, 2, '136.6', 2, '2022-02-07', 2),
(3, 3, '187.0', 3, '2021-09-10', 3),
(4, 4, '6.8', 4, '2021-11-09', 4),
(5, 5, '83.1', 5, '2021-06-21', 5),
(6, 6, '193.1', 6, '2021-10-29', 6),
(7, 7, '312.1', 7, '2021-08-30', 7),
(8, 8, '374.6', 8, '2021-05-29', 8),
(9, 9, '5.4', 9, '2021-06-28', 9),
(10, 10, '162.3', 10, '2021-01-22', 10),
(11, 11, '210.7', 11, '2021-08-22', 11),
(12, 12, '65.2', 12, '2022-01-19', 12),
(13, 13, '259.9', 13, '2021-11-21', 13),
(14, 14, '306.2', 14, '2021-02-12', 14),
(15, 15, '92.0', 15, '2022-01-23', 15),
(16, 16, '91.9', 16, '2022-03-02', 16),
(17, 17, '399.1', 17, '2021-02-18', 17),
(18, 18, '82.4', 18, '2020-12-08', 18),
(19, 19, '237.2', 19, '2022-02-10', 19),
(20, 20, '54.3', 20, '2020-11-30', 20),
(21, 21, '184.0', 21, '2021-10-29', 21),
(22, 22, '321.1', 22, '2021-01-17', 22),
(23, 23, '64.1', 23, '2021-04-20', 23),
(24, 24, '106.4', 24, '2020-12-20', 24),
(25, 25, '49.8', 25, '2020-10-09', 25),
(26, 26, '66.7', 26, '2021-04-07', 26),
(27, 27, '315.9', 27, '2021-12-24', 27),
(28, 28, '346.3', 28, '2021-05-04', 28),
(29, 29, '44.5', 29, '2021-02-05', 29),
(30, 30, '330.0', 30, '2021-09-22', 30),
(31, 31, '337.7', 31, '2021-07-30', 31),
(32, 32, '86.1', 32, '2021-05-22', 32),
(33, 33, '256.4', 33, '2021-08-13', 33),
(34, 34, '253.6', 34, '2021-01-29', 34),
(35, 35, '130.2', 35, '2021-09-23', 35),
(36, 36, '305.7', 36, '2021-11-23', 36),
(37, 37, '150.5', 37, '2021-05-02', 37),
(38, 38, '251.8', 38, '2021-05-21', 38),
(39, 39, '248.9', 39, '2021-10-05', 39),
(40, 40, '40.2', 40, '2021-07-15', 40),
(41, 41, '399.2', 41, '2021-11-25', 41),
(42, 42, '299.7', 42, '2020-11-21', 42),
(43, 43, '359.2', 43, '2021-06-03', 43),
(44, 44, '373.6', 44, '2021-05-18', 44),
(45, 45, '328.2', 45, '2021-02-19', 45),
(46, 46, '266.1', 46, '2021-04-24', 46),
(47, 47, '144.4', 47, '2021-12-16', 47),
(48, 48, '245.0', 48, '2021-05-23', 48),
(49, 49, '154.4', 49, '2020-10-24', 49),
(50, 50, '199.5', 50, '2020-11-08', 50),
(51, 51, '145.4', 51, '2021-08-12', 51),
(52, 52, '270.8', 52, '2020-12-29', 52),
(53, 53, '220.5', 53, '2020-10-19', 53),
(54, 54, '134.3', 54, '2021-10-28', 54),
(55, 55, '318.5', 55, '2020-10-23', 55),
(56, 56, '80.7', 56, '2021-02-10', 56),
(57, 57, '158.2', 57, '2021-11-03', 57),
(58, 58, '350.8', 58, '2021-05-19', 58),
(59, 59, '291.8', 59, '2021-02-19', 59),
(60, 60, '330.3', 60, '2021-05-18', 60),
(61, 61, '286.8', 61, '2021-07-14', 61),
(62, 62, '383.6', 62, '2022-02-14', 62),
(63, 63, '237.1', 63, '2021-11-27', 63),
(64, 64, '266.7', 64, '2022-02-17', 64),
(65, 65, '350.4', 65, '2021-11-28', 65),
(66, 66, '334.0', 66, '2021-06-30', 66),
(67, 67, '89.2', 67, '2022-02-18', 67),
(68, 68, '328.9', 68, '2021-05-10', 68),
(69, 69, '249.7', 69, '2021-03-29', 69),
(70, 70, '252.6', 70, '2021-07-06', 70),
(71, 71, '170.7', 71, '2021-03-09', 71),
(72, 72, '191.9', 72, '2021-02-02', 72),
(73, 73, '243.7', 73, '2021-11-14', 73),
(74, 74, '96.5', 74, '2021-03-24', 74),
(75, 75, '36.7', 75, '2021-02-01', 75),
(76, 76, '381.7', 76, '2021-04-15', 76),
(77, 77, '70.5', 77, '2020-12-27', 77),
(78, 78, '207.2', 78, '2020-11-29', 78),
(79, 79, '234.1', 79, '2021-07-28', 79),
(80, 80, '367.6', 80, '2021-06-20', 80),
(81, 81, '102.9', 81, '2021-12-26', 81),
(82, 82, '79.6', 82, '2022-03-07', 82),
(83, 83, '155.8', 83, '2021-02-12', 83),
(84, 84, '144.7', 84, '2020-10-25', 84),
(85, 85, '306.6', 85, '2021-12-15', 85),
(86, 86, '358.5', 86, '2020-12-12', 86),
(87, 87, '397.3', 87, '2020-12-24', 87),
(88, 88, '339.0', 88, '2020-12-15', 88),
(89, 89, '382.2', 89, '2021-11-04', 89),
(90, 90, '57.4', 90, '2020-11-30', 90),
(91, 91, '266.3', 91, '2020-11-08', 91),
(92, 92, '375.9', 92, '2021-05-27', 92),
(93, 93, '222.3', 93, '2021-01-11', 93),
(94, 94, '359.8', 94, '2021-03-15', 94),
(95, 95, '54.5', 95, '2021-03-29', 95),
(96, 96, '390.0', 96, '2021-12-30', 96),
(97, 97, '51.6', 97, '2021-10-28', 97),
(98, 98, '140.9', 98, '2020-11-14', 98),
(99, 99, '340.8', 99, '2022-01-25', 99),
(100, 100, '64.8', 100, '2021-09-25', 100),
(NULL, 1, '100.0', 1, '2022-04-27', 1),
(NULL, 2, '30.0', 2, '2022-05-10', 2);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ventas4`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ventas4` (
`id_venta` int(11)
,`id_producto` int(11)
,`cantidad` decimal(4,1)
,`nombre` varchar(50)
,`fecha` date
,`nom_sucursal` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `mas_vendido`
--
DROP TABLE IF EXISTS `mas_vendido`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mas_vendido`  AS SELECT `ventas`.`id_producto` AS `id_producto`, `ventas`.`fecha` AS `fecha`, sum(`ventas`.`cantidad`) AS `cantidad` FROM `ventas` GROUP BY `ventas`.`id_producto`, `ventas`.`fecha` ORDER BY `cantidad` AS `DESCdesc` ASC  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `menos_vendido`
--
DROP TABLE IF EXISTS `menos_vendido`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `menos_vendido`  AS SELECT `ventas`.`id_producto` AS `id_producto`, `ventas`.`fecha` AS `fecha`, sum(`ventas`.`cantidad`) AS `cantidad` FROM `ventas` GROUP BY `ventas`.`id_producto`, `ventas`.`fecha` ORDER BY `cantidad` ASC  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ventas4`
--
DROP TABLE IF EXISTS `vista_ventas4`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas4`  AS SELECT `ventas`.`id_venta` AS `id_venta`, `ventas`.`id_producto` AS `id_producto`, `ventas`.`cantidad` AS `cantidad`, `empleado`.`nombre` AS `nombre`, `ventas`.`fecha` AS `fecha`, `sucursal`.`nom_sucursal` AS `nom_sucursal` FROM ((`ventas` join `empleado` on((`ventas`.`id_mesero` = `empleado`.`id_empleado`))) join `sucursal` on((`ventas`.`id_sucursal` = `sucursal`.`id_sucursal`)))  ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `gastos_fijos`
--
ALTER TABLE `gastos_fijos`
  ADD PRIMARY KEY (`id_gasto`);

--
-- Indices de la tabla `tipo_gasto`
--
ALTER TABLE `tipo_gasto`
  ADD PRIMARY KEY (`id_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `gastos_fijos`
--
ALTER TABLE `gastos_fijos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipo_gasto`
--
ALTER TABLE `tipo_gasto`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
