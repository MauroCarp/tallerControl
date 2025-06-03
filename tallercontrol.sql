-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 03-06-2025 a las 19:40:31
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
-- Estructura de tabla para la tabla `combustibles_lubricantes`
--

DROP TABLE IF EXISTS `combustibles_lubricantes`;
CREATE TABLE IF NOT EXISTS `combustibles_lubricantes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `ingresoLitros` double(8,2) NOT NULL,
  `egresosLitros` double(8,2) NOT NULL,
  `origen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

DROP TABLE IF EXISTS `insumos`;
CREATE TABLE IF NOT EXISTS `insumos` (
  `id` bigint UNSIGNED DEFAULT NULL,
  `insumo` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cartaPorte` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendedor` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pesoBruto` double(8,2) DEFAULT NULL,
  `tara` double(8,2) DEFAULT NULL,
  `humedad` int DEFAULT NULL,
  `mermaHumedad` int DEFAULT NULL,
  `calidad` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `materiasExtraneas` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tierra` tinyint(1) DEFAULT NULL,
  `destino` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientosservices`
--

DROP TABLE IF EXISTS `mantenimientosservices`;
CREATE TABLE IF NOT EXISTS `mantenimientosservices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `rodadoHerramienta_id` int NOT NULL,
  `responsable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `turno` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tareas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `horasMotor` int NOT NULL,
  `km` int NOT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isMantenimiento` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimientosservices`
--

INSERT INTO `mantenimientosservices` (`id`, `fecha`, `rodadoHerramienta_id`, `responsable`, `turno`, `tareas`, `horasMotor`, `km`, `observaciones`, `isMantenimiento`, `created_at`, `updated_at`) VALUES
(1, '2025-05-28', 2, 'Mauro', 'Mañana', '[\"1\",\"2\",\"4\",\"8\",\"7\"]', 2500, 25000, NULL, 1, '2025-05-28 21:47:12', '2025-05-28 21:47:12'),
(2, '2025-06-02', 5, 'mAURO', 'Mañana', '[\"1\",\"2\",\"6\"]', 5000, 25000, NULL, 0, '2025-06-02 17:25:23', '2025-06-02 17:25:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_04_07_233219_create_mantenimientosServices_table', 1),
(6, '2025_04_08_015001_create_rodadosHerramientas_table', 1),
(7, '2025_05_26_234508_create_reparaciones_table', 1),
(8, '2025_05_26_235117_create_combustibles_lubricantes_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb3_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparaciones`
--

DROP TABLE IF EXISTS `reparaciones`;
CREATE TABLE IF NOT EXISTS `reparaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `rodadoHerramienta_id` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `encargado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcionReparacion` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importe` float DEFAULT NULL,
  `horas` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reparaciones`
--

INSERT INTO `reparaciones` (`id`, `fecha`, `rodadoHerramienta_id`, `descripcion`, `operario`, `encargado`, `descripcionReparacion`, `tipo`, `importe`, `horas`, `created_at`, `updated_at`) VALUES
(1, '2025-06-03', 1, 'Corte de cadena', 'Prueba operario', 'Prueba Encargado', 'Se reemplaza por cadena nueva', 'Propia', NULL, 2, '2025-06-03 22:25:03', '2025-06-03 22:25:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rodados_herramientas`
--

DROP TABLE IF EXISTS `rodados_herramientas`;
CREATE TABLE IF NOT EXISTS `rodados_herramientas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frecuencia` int NOT NULL,
  `agenda` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serviceHoras` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `unidadService` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rodados_herramientas`
--

INSERT INTO `rodados_herramientas` (`id`, `nombre`, `frecuencia`, `agenda`, `serviceHoras`, `created_at`, `unidadService`, `updated_at`) VALUES
(1, 'Cuatriciclo', 7, '{\'Martes\':\'Tarde\'}', 5000, '2025-05-28 15:42:15', 'Horas', '2025-05-31 01:42:13'),
(2, 'Massey 630', 7, '{\'Martes\':\'Tarde\'}', 300, '2025-05-28 15:42:38', 'Horas', '2025-05-28 15:42:38'),
(3, 'Massey 6712', 7, '{\'Miércoles\':\'Tarde\'}', 300, '2025-05-28 15:42:51', 'Horas', '2025-05-28 15:42:51'),
(4, 'Mixer', 7, '{\'Jueves\':\'Tarde\'}', 0, '2025-05-28 15:43:05', '0', '2025-05-28 15:43:05'),
(5, 'Valtra', 7, '{\'Jueves\':\'Tarde\'}', 300, '2025-05-28 15:43:28', 'Horas', '2025-05-28 15:43:28'),
(6, 'Michigan', 7, '{\'Viernes\':\'Tarde\'}', 300, '2025-05-28 15:43:41', 'Horas', '2025-05-28 15:43:41'),
(7, 'Deutz', 14, '{\'Lunes\':\'Tarde\'}', 300, '2025-05-28 15:44:00', 'Horas', '2025-05-28 15:44:00'),
(8, 'Toyota de oficina', 14, '{\'Lunes\':\'Tarde\'}', 10000, '2025-05-28 15:49:56', 'Km', '2025-05-28 15:49:56'),
(9, 'Pauny', 14, '{\'Lunes\':\'Tarde\'}', 300, '2025-05-28 15:50:10', 'Horas', '2025-05-28 15:50:10'),
(10, 'Generador', 30, '{\'Lunes\':\'Mañana\'}', 2, '2025-05-28 18:56:18', 'Meses', '2025-05-28 18:56:18'),
(11, 'Embolsadora', 0, NULL, 0, '2025-05-28 18:58:59', '0', '2025-05-28 18:58:59'),
(13, 'Moscato', 0, NULL, 0, '2025-05-28 20:13:31', '0', '2025-05-28 20:13:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Mauro', 'mauro@mauro.com', NULL, '$2y$10$t8xmZ9fo7l93Bkoz4E1mgebKKf03jTjhJ9lD2.IKG6lbq8gEzATKO', 'PCHZ3f9Hs2SHQEbWuScL6KFwO90agGRKgxmhCeJnaIl8xTQnnl7wz0cKB9Wr', '2025-05-28 14:33:34', '2025-05-28 14:33:34');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
