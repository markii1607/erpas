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
-- Table structure for table `tax_declarations`
--

CREATE TABLE `tax_declarations` (
  `id` int(5) UNSIGNED NOT NULL,
  `revision_year_id` int(5) UNSIGNED NOT NULL,
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
  `barangay_id` int(5) UNSIGNED NOT NULL,
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
-- Indexes for table `tax_declarations`
--
ALTER TABLE `tax_declarations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tax_declarations`
--
ALTER TABLE `tax_declarations`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
