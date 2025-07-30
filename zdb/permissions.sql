-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 30-07-2025 a las 16:19:42
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tallercontrol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(48, 'view_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(49, 'view_any_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(50, 'create_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(51, 'update_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(52, 'restore_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(53, 'restore_any_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(54, 'replicate_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(55, 'reorder_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(56, 'delete_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(57, 'delete_any_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(58, 'force_delete_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(59, 'force_delete_any_rodados::herramientas', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(60, 'view_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(61, 'view_any_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(62, 'create_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(63, 'update_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(64, 'delete_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(65, 'delete_any_role', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(66, 'view_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(67, 'view_any_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(68, 'create_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(69, 'update_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(70, 'restore_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(71, 'restore_any_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(72, 'replicate_users', 'web', '2025-07-30 17:57:00', '2025-07-30 17:57:00'),
(73, 'reorder_users', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(74, 'delete_users', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(75, 'delete_any_users', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(76, 'force_delete_users', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(77, 'force_delete_any_users', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(78, 'widget_BarloventoButton', 'web', '2025-07-30 17:57:01', '2025-07-30 17:57:01'),
(79, 'view_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(80, 'view_any_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(81, 'create_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(82, 'update_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(83, 'restore_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(84, 'restore_any_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(85, 'replicate_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(86, 'reorder_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(87, 'delete_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(88, 'delete_any_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(89, 'force_delete_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(90, 'force_delete_any_mantenimiento::general', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(91, 'widget_MantenimientoGeneralPedidoList', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(92, 'widget_MantenimientoGeneralRealizadoList', 'web', '2025-07-30 17:57:08', '2025-07-30 17:57:08'),
(93, 'view_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(94, 'view_any_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(95, 'create_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(96, 'update_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(97, 'restore_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(98, 'restore_any_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(99, 'replicate_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(100, 'reorder_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(101, 'delete_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(102, 'delete_any_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(103, 'force_delete_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(104, 'force_delete_any_mantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(105, 'widget_CronogramaMantenimientos', 'web', '2025-07-30 18:57:46', '2025-07-30 18:57:46'),
(106, 'widget_MantenimientosList', 'web', '2025-07-30 18:57:47', '2025-07-30 18:57:47'),
(107, 'view_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(108, 'view_any_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(109, 'create_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(110, 'update_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(111, 'restore_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(112, 'restore_any_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(113, 'replicate_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(114, 'reorder_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(115, 'delete_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(116, 'delete_any_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(117, 'force_delete_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(118, 'force_delete_any_services', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(119, 'widget_CronogramaService', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(120, 'widget_ServiceList', 'web', '2025-07-30 18:57:49', '2025-07-30 18:57:49'),
(121, 'view_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(122, 'view_any_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(123, 'create_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(124, 'update_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(125, 'restore_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(126, 'restore_any_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(127, 'replicate_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(128, 'reorder_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(129, 'delete_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(130, 'delete_any_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(131, 'force_delete_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(132, 'force_delete_any_roturas::reparacion', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(133, 'widget_ReparacionesPropiasList', 'web', '2025-07-30 18:57:51', '2025-07-30 18:57:51'),
(134, 'widget_ReparacionesTercierizadasList', 'web', '2025-07-30 18:57:52', '2025-07-30 18:57:52'),
(135, 'view_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(136, 'view_any_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(137, 'create_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(138, 'update_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(139, 'restore_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(140, 'restore_any_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(141, 'replicate_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(142, 'reorder_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(143, 'delete_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(144, 'delete_any_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(145, 'force_delete_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(146, 'force_delete_any_combustibles::lubricantes', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(147, 'widget_NaftaList', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(148, 'widget_GasoilList', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54'),
(149, 'widget_LubricantesList', 'web', '2025-07-30 18:57:54', '2025-07-30 18:57:54');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
