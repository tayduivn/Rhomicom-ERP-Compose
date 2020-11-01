-- MySQL dump 10.13  Distrib 8.0.21, for Linux (x86_64)
--
-- Host: localhost    Database: demo_flexcodesdk
-- ------------------------------------------------------
-- Server version	8.0.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `demo_device`
--

DROP TABLE IF EXISTS `demo_device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `demo_device` (
  `device_name` varchar(50) NOT NULL,
  `sn` varchar(50) NOT NULL,
  `vc` varchar(50) NOT NULL,
  `ac` varchar(50) NOT NULL,
  `vkey` varchar(50) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demo_device`
--

LOCK TABLES `demo_device` WRITE;
/*!40000 ALTER TABLE `demo_device` DISABLE KEYS */;
INSERT INTO `demo_device` VALUES ('Rho2','X0013WGHMF','A3B23D037B2EB3E','1P17A3545A31B5D52089E02F','EBB1E8B895BDAC5C10A769D655D46E05');
/*!40000 ALTER TABLE `demo_device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demo_finger`
--

DROP TABLE IF EXISTS `demo_finger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `demo_finger` (
  `user_id` int unsigned NOT NULL,
  `finger_id` int unsigned NOT NULL,
  `finger_data` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demo_finger`
--

LOCK TABLES `demo_finger` WRITE;
/*!40000 ALTER TABLE `demo_finger` DISABLE KEYS */;
/*!40000 ALTER TABLE `demo_finger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demo_log`
--

DROP TABLE IF EXISTS `demo_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `demo_log` (
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_name` varchar(50) NOT NULL,
  `data` text NOT NULL COMMENT 'sn+pc time',
  PRIMARY KEY (`log_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demo_log`
--

LOCK TABLES `demo_log` WRITE;
/*!40000 ALTER TABLE `demo_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `demo_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demo_user`
--

DROP TABLE IF EXISTS `demo_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `demo_user` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `person_cust_id` bigint NOT NULL DEFAULT '-1',
  `prsn_type` varchar(5) NOT NULL DEFAULT 'IND',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9647 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demo_user`
--

LOCK TABLES `demo_user` WRITE;
/*!40000 ALTER TABLE `demo_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `demo_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'demo_flexcodesdk'
--

--
-- Dumping routines for database 'demo_flexcodesdk'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-30 13:15:20
