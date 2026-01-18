-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: eletcad_db
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.24.04.2

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
-- Dumping data for table `simbolos`
--

LOCK TABLES `simbolos` WRITE;
/*!40000 ALTER TABLE `simbolos` DISABLE KEYS */;
INSERT INTO `simbolos` (`id`, `nome`, `sigla_padrao`, `categoria`, `simbolo_svg`, `footprint_layout`, `configuracao_tag`, `bornes`, `logica_contatos`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Contator','K','bobina','<rect x=\"20\" y=\"35\" width=\"60\" height=\"30\" stroke=\"black\" stroke-width=\"2\" fill=\"none\" />\r\n<line x1=\"50\" y1=\"20\" x2=\"50\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"50\" y2=\"80\" stroke=\"black\" stroke-width=\"2\" />\r\n','[]','[]','[{\"x\": \"50\", \"y\": \"20\", \"id\": \"A1\", \"tipo\": \"comando\"}, {\"x\": 50, \"y\": \"80\", \"id\": \"A2\", \"tipo\": \"comando\"}]','[]','2025-12-26 23:27:42','2025-12-27 01:20:04',NULL),(4,'Contato NO','K','comando','<line x1=\"50\" y1=\"0\" x2=\"50\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"50\" y2=\"100\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"25\" y2=\"30\" stroke=\"black\" stroke-width=\"2\" />','[]','[]','[{\"x\": 50, \"y\": 1, \"id\": \"13\", \"tipo\": \"comando\"}, {\"x\": 50, \"y\": \"100\", \"id\": \"14\", \"tipo\": \"comando\"}]','[]','2025-12-27 00:44:43','2025-12-27 01:04:02',NULL),(5,'Contato NC','K','comando','<line x1=\"50\" y1=\"0\" x2=\"50\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"50\" y2=\"100\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"75\" y2=\"30\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"35\" x2=\"75\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />','[]','[]','[{\"x\": \"50\", \"y\": \"0\", \"id\": \"11\", \"tipo\": \"comando\"}, {\"x\": \"50\", \"y\": \"100\", \"id\": \"12\", \"tipo\": \"comando\"}]','[]','2025-12-27 01:03:27','2025-12-27 01:03:27',NULL),(6,'Relé térmico NC','F','comando','<line x1=\"50\" y1=\"0\" x2=\"50\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"50\" y2=\"100\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"65\" x2=\"75\" y2=\"30\" stroke=\"black\" stroke-width=\"2\" />\r\n<line x1=\"50\" y1=\"35\" x2=\"75\" y2=\"35\" stroke=\"black\" stroke-width=\"2\" />\r\n<polyline points=\"25,50 30,50 30,40 40,40 40,50 45,50\" \r\nstroke=\"black\" stroke-width=\"2\" fill=\"none\" />','[]','[]','[{\"x\": \"50\", \"y\": \"0\", \"id\": \"95\", \"tipo\": \"comando\"}, {\"x\": 50, \"y\": \"100\", \"id\": \"96\", \"tipo\": \"comando\"}]','[]','2025-12-27 01:13:47','2025-12-27 01:13:47',NULL),(7,'Ponto de passagem de cabo','X','conexao','\r\n<circle cx=\"50\" cy=\"50\" r=\"2\" stroke=\"black\" stroke-width=\"2\" fill=\"none\" />','[]','[]','[{\"x\": \"50\", \"y\": \"50\", \"id\": \"1\", \"tipo\": \"comando\"}]','[]','2025-12-27 02:09:19','2025-12-27 02:43:39',NULL);
/*!40000 ALTER TABLE `simbolos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-27  2:07:05
