-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: alat_berat
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `password` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `no_telp` (`no_telp`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (2,'Raka Akmal','raka','Snya5088@gmail.com','087865992731','admin','$2y$10$bxiruR7rH.4C1Dfr2m3efe3NUWne36.a9qwjXW7pXRJhXfFTijaP2','active','2026-06-24 00:57:25'),(5,'Muhammad Pai Fuda Udah','pai','pai@gmail.com','081382038861','superadmin','$2a$12$3zCF5TpJQtx/UfZBXY9VreZDdczPH8f3OGnhsSPTlAtv4L0tHrLB6','active','2026-06-29 08:46:17');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alat_berat`
--

DROP TABLE IF EXISTS `alat_berat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alat_berat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `spesifikasi` text,
  `lokasi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `nama` (`nama`,`deskripsi`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alat_berat`
--

LOCK TABLES `alat_berat` WRITE;
/*!40000 ALTER TABLE `alat_berat` DISABLE KEYS */;
INSERT INTO `alat_berat` VALUES (25,'CRANE',NULL,'Forklift terdiri dari beberapa komponen utama seperti sasis, tiang (mast), dan pelindung atas. Alat ini menggunakan garpu (fork) yang dapat dinaikkan atau diturunkan melalui sistem hidraulik untuk memindahkan material.','1777568796_crane.webp','tersedia','Spesifikasi dapat bervariasi tergantung pada model dan merek (seperti Toyota, Mitsubishi, atau CAT). Berikut adalah gambaran spesifikasi umum untuk kelas menengah','Pamulang'),(26,'FORKLIFT',NULL,'Forklift terdiri dari beberapa komponen utama seperti sasis, tiang (mast), dan pelindung atas. Alat ini menggunakan garpu (fork) yang dapat dinaikkan atau diturunkan melalui sistem hidraulik untuk memindahkan material.','1777569295_forklift.webp','tersedia','Spesifikasi dapat bervariasi tergantung pada model dan merek (seperti Toyota, Mitsubishi, atau CAT). Berikut adalah gambaran spesifikasi umum untuk kelas menengah','Pamulang'),(39,'Forklift',NULL,'tes','1780994510_6a27d1ce16969.webp','tersedia','tes','pamulang'),(40,'Forklift',NULL,'','1780995286_6a27d4d6ef449.webp','Tidak Tersedia','','Bogor'),(41,'ROIKHAN RIJAL FIRDAUS',NULL,'roi','1782263559_6a3b2f076b414.png','tersedia','roi','Pamulang');
/*!40000 ALTER TABLE `alat_berat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harga_alat`
--

DROP TABLE IF EXISTS `harga_alat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harga_alat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `alat_id` int DEFAULT NULL,
  `berat` varchar(50) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `jam` int DEFAULT '7',
  `keterangan` varchar(255) DEFAULT '6 Jam Kerja 1 Jam Istirahat',
  PRIMARY KEY (`id`),
  KEY `alat_id` (`alat_id`),
  CONSTRAINT `harga_alat_ibfk_1` FOREIGN KEY (`alat_id`) REFERENCES `alat_berat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harga_alat`
--

LOCK TABLES `harga_alat` WRITE;
/*!40000 ALTER TABLE `harga_alat` DISABLE KEYS */;
INSERT INTO `harga_alat` VALUES (21,25,'7 ton',9000000,7,'6 Jam Kerja 1 Jam Istirahat'),(22,26,'7 ton',5000000,7,'6 Jam Kerja 1 Jam Istirahat'),(42,39,'3 ton',1500000,7,'6 Jam Kerja 1 Jam Istirahat'),(43,40,'7 ton',4000000,7,'6 Jam Kerja 1 Jam Istirahat'),(44,41,'7 ton',12000000,7,'6 Jam Kerja 1 Jam Istirahat');
/*!40000 ALTER TABLE `harga_alat` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-29 15:54:43
