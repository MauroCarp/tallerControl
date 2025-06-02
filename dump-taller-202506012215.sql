-- MariaDB dump 10.19-11.2.3-MariaDB, for osx10.16 (x86_64)
--
-- Host: localhost    Database: taller
-- ------------------------------------------------------
-- Server version	11.2.3-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `combustibles_lubricantes`
--

DROP TABLE IF EXISTS `combustibles_lubricantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combustibles_lubricantes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `ingresoLitros` double(8,2) NOT NULL,
  `egresosLitros` double(8,2) NOT NULL,
  `origen` varchar(255) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `combustibles_lubricantes`
--

LOCK TABLES `combustibles_lubricantes` WRITE;
/*!40000 ALTER TABLE `combustibles_lubricantes` DISABLE KEYS */;
/*!40000 ALTER TABLE `combustibles_lubricantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `insumos`
--

DROP TABLE IF EXISTS `insumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `insumos` (
  `id` bigint(20) unsigned DEFAULT NULL,
  `insumo` varchar(10) DEFAULT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) DEFAULT NULL,
  `cartaPorte` varchar(255) DEFAULT NULL,
  `vendedor` varchar(255) DEFAULT NULL,
  `pesoBruto` double(8,2) DEFAULT NULL,
  `tara` double(8,2) DEFAULT NULL,
  `humedad` int(11) DEFAULT NULL,
  `mermaHumedad` int(11) DEFAULT NULL,
  `calidad` varchar(255) DEFAULT NULL,
  `materiasExtraneas` varchar(255) DEFAULT NULL,
  `tierra` tinyint(1) DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `insumos`
--

LOCK TABLES `insumos` WRITE;
/*!40000 ALTER TABLE `insumos` DISABLE KEYS */;
/*!40000 ALTER TABLE `insumos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mantenimientosservices`
--

DROP TABLE IF EXISTS `mantenimientosservices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mantenimientosservices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `rodadoHerramienta_id` int(11) NOT NULL,
  `responsable` varchar(255) NOT NULL,
  `turno` varchar(200) NOT NULL,
  `tareas` varchar(255) NOT NULL,
  `horasMotor` int(11) NOT NULL,
  `km` int(11) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `isMantenimiento` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mantenimientosservices`
--

LOCK TABLES `mantenimientosservices` WRITE;
/*!40000 ALTER TABLE `mantenimientosservices` DISABLE KEYS */;
INSERT INTO `mantenimientosservices` VALUES
(1,'2025-05-28',2,'Mauro','Mañana','[\"1\",\"2\",\"4\",\"8\",\"7\"]',2500,25000,NULL,NULL,'2025-05-28 21:47:12','2025-05-28 21:47:12');
/*!40000 ALTER TABLE `mantenimientosservices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2025_04_07_233219_create_mantenimientosServices_table',1),
(6,'2025_04_08_015001_create_rodadosHerramientas_table',1),
(7,'2025_05_26_234508_create_reparaciones_table',1),
(8,'2025_05_26_235117_create_combustibles_lubricantes_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reparaciones`
--

DROP TABLE IF EXISTS `reparaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reparaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `rodadoHerramienta_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `operario` varchar(255) NOT NULL,
  `encargado` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `importe` double(8,2) NOT NULL,
  `horas` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reparaciones`
--

LOCK TABLES `reparaciones` WRITE;
/*!40000 ALTER TABLE `reparaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `reparaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rodados_herramientas`
--

DROP TABLE IF EXISTS `rodados_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rodados_herramientas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `frecuencia` int(11) NOT NULL,
  `agenda` varchar(255) DEFAULT NULL,
  `serviceHoras` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `unidadService` varchar(100) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rodados_herramientas`
--

LOCK TABLES `rodados_herramientas` WRITE;
/*!40000 ALTER TABLE `rodados_herramientas` DISABLE KEYS */;
INSERT INTO `rodados_herramientas` VALUES
(1,'Cuatriciclo',7,'{\'Martes\':\'Tarde\'}',5000,'2025-05-28 15:42:15','Horas','2025-05-31 01:42:13'),
(2,'Massey 630',7,'{\'Martes\':\'Tarde\'}',300,'2025-05-28 15:42:38','Horas','2025-05-28 15:42:38'),
(3,'Massey 6712',7,'{\'Miércoles\':\'Tarde\'}',300,'2025-05-28 15:42:51','Horas','2025-05-28 15:42:51'),
(4,'Mixer',7,'{\'Jueves\':\'Tarde\'}',0,'2025-05-28 15:43:05','0','2025-05-28 15:43:05'),
(5,'Valtra',7,'{\'Jueves\':\'Tarde\'}',300,'2025-05-28 15:43:28','Horas','2025-05-28 15:43:28'),
(6,'Michigan',7,'{\'Viernes\':\'Tarde\'}',300,'2025-05-28 15:43:41','Horas','2025-05-28 15:43:41'),
(7,'Deutz',14,'{\'Lunes\':\'Tarde\'}',300,'2025-05-28 15:44:00','Horas','2025-05-28 15:44:00'),
(8,'Toyota de oficina',14,'{\'Lunes\':\'Tarde\'}',10000,'2025-05-28 15:49:56','Km','2025-05-28 15:49:56'),
(9,'Pauny',14,'{\'Lunes\':\'Tarde\'}',300,'2025-05-28 15:50:10','Horas','2025-05-28 15:50:10'),
(10,'Generador',30,'{\'Lunes\':\'Mañana\'}',2,'2025-05-28 18:56:18','Meses','2025-05-28 18:56:18'),
(11,'Embolsadora',0,NULL,0,'2025-05-28 18:58:59','0','2025-05-28 18:58:59'),
(13,'Moscato',0,NULL,0,'2025-05-28 20:13:31','0','2025-05-28 20:13:31');
/*!40000 ALTER TABLE `rodados_herramientas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Mauro','mauro@mauro.com',NULL,'$2y$10$t8xmZ9fo7l93Bkoz4E1mgebKKf03jTjhJ9lD2.IKG6lbq8gEzATKO','PCHZ3f9Hs2SHQEbWuScL6KFwO90agGRKgxmhCeJnaIl8xTQnnl7wz0cKB9Wr','2025-05-28 14:33:34','2025-05-28 14:33:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'taller'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-01 22:15:04
