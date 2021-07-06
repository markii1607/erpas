-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2021 at 02:57 PM
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
-- Table structure for table `paid_tax_declaration_detail_installments`
--

CREATE TABLE `paid_tax_declaration_detail_installments` (
  `id` int(5) UNSIGNED NOT NULL,
  `paid_tax_declaration_detail_id` int(5) UNSIGNED NOT NULL,
  `installment_text` text NOT NULL,
  `full_payment` decimal(22,4) NOT NULL,
  `penalty_amount` decimal(22,4) NOT NULL,
  `total` decimal(22,4) NOT NULL,
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
-- Indexes for table `paid_tax_declaration_detail_installments`
--
ALTER TABLE `paid_tax_declaration_detail_installments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `paid_tax_declaration_detail_installments`
--
ALTER TABLE `paid_tax_declaration_detail_installments`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
