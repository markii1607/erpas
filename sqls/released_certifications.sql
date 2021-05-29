-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2021 at 11:57 AM
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
-- Table structure for table `released_certifications`
--

CREATE TABLE `released_certifications` (
  `id` int(5) UNSIGNED NOT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT 'A-No Property Cert | B- | C-',
  `declaree` text NOT NULL,
  `requestor` text NOT NULL,
  `purpose` text NOT NULL,
  `request_date` date NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `or_no` text NOT NULL,
  `prepared_by` int(5) NOT NULL COMMENT 'user_id',
  `verified_by` int(5) NOT NULL COMMENT 'user_id',
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
-- Indexes for table `released_certifications`
--
ALTER TABLE `released_certifications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `released_certifications`
--
ALTER TABLE `released_certifications`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
