-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2021 at 08:07 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erpas`
--

-- --------------------------------------------------------

--
-- Table structure for table `tax_declaration_classifications`
--

CREATE TABLE `tax_declaration_classifications` (
  `id` int(5) UNSIGNED NOT NULL,
  `tax_declaration_id` int(5) NOT NULL,
  `classification_id` int(11) NOT NULL,
  `market_value_id` int(5) UNSIGNED DEFAULT NULL,
  `area` decimal(22,2) NOT NULL,
  `unit_measurement` text NOT NULL,
  `area_in_sqm` decimal(22,6) DEFAULT NULL,
  `area_in_ha` decimal(22,6) DEFAULT NULL,
  `market_value` decimal(22,2) NOT NULL,
  `actual_use` text DEFAULT NULL,
  `assessment_level` int(5) NOT NULL COMMENT '%',
  `assessed_value` decimal(22,2) NOT NULL,
  `adjustment` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(5) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tax_declaration_classifications`
--
ALTER TABLE `tax_declaration_classifications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tax_declaration_classifications`
--
ALTER TABLE `tax_declaration_classifications`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
