-- MariaDB dump 10.17  Distrib 10.4.8-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: erpas
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB

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
-- Table structure for table `tax_declarations`
--

DROP TABLE IF EXISTS `tax_declarations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_declarations` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `revision_year_id` int(5) unsigned NOT NULL,
  `td_no` text NOT NULL,
  `pin` text NOT NULL,
  `owner` text NOT NULL,
  `owner_tin` text DEFAULT NULL,
  `owner_address` text DEFAULT NULL,
  `beneficiary` text DEFAULT NULL,
  `beneficiary_tin` text DEFAULT NULL,
  `beneficiary_address` text DEFAULT NULL,
  `beneficiary_tel_no` text DEFAULT NULL,
  `prop_location_street` text DEFAULT NULL,
  `barangay_id` int(5) unsigned NOT NULL,
  `oct_tct_cloa_no` text DEFAULT NULL,
  `cct` text DEFAULT NULL,
  `survey_no` text DEFAULT NULL,
  `lot_no` text DEFAULT NULL,
  `block_no` text DEFAULT NULL,
  `dated` date DEFAULT NULL,
  `boundaries` text DEFAULT NULL,
  `boundaries_north` text DEFAULT NULL,
  `boundaries_south` text DEFAULT NULL,
  `boundaries_east` text DEFAULT NULL,
  `boundaries_west` text DEFAULT NULL,
  `property_kind` text NOT NULL,
  `description` text DEFAULT NULL,
  `no_of_storey` int(5) DEFAULT NULL,
  `others_specified` text DEFAULT NULL,
  `total_market_value` decimal(22,2) NOT NULL,
  `total_assessed_value` decimal(22,2) NOT NULL,
  `total_assessed_value_words` text NOT NULL,
  `is_taxable` tinyint(1) DEFAULT NULL,
  `is_exempt` tinyint(1) DEFAULT NULL,
  `effectivity` text DEFAULT NULL,
  `canceled_td_id` int(5) DEFAULT NULL COMMENT '[tax_declaration_id]',
  `ordinance_no` text DEFAULT NULL,
  `ordinance_date` date DEFAULT NULL,
  `approvers` text NOT NULL,
  `memoranda` text DEFAULT NULL,
  `status` int(5) DEFAULT 1 COMMENT '1-active | 2-retired | 3-canceled',
  `is_paid` tinyint(1) DEFAULT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(5) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-07 13:47:39
