-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2021 at 10:16 AM
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
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(5) UNSIGNED NOT NULL,
  `code` text NOT NULL,
  `name` text NOT NULL,
  `no_of_sections` int(5) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(5) UNSIGNED NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `code`, `name`, `no_of_sections`, `is_active`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, '01', 'Sample Barangay', 120, 0, 1, '2021-04-28 13:58:28', 1, '2021-04-28 13:59:34'),
(2, '02', 'Sample Name 2', 155, 0, 1, '2021-04-28 13:59:15', 1, '2021-04-28 13:59:37'),
(3, '01', 'Barangay 01 Poblacion', 0, 1, 1, '2021-04-29 17:35:40', 1, '2021-04-29 17:35:40'),
(4, '02', 'Barangay 02 Poblacion', 0, 1, 1, '2021-04-29 17:35:53', 1, '2021-04-29 17:35:53'),
(5, '03', 'Barangay 03 Poblacion', 0, 1, 1, '2021-04-29 17:36:04', 1, '2021-04-29 17:36:04'),
(6, '04', 'Barangay 04 Poblacion', 0, 1, 1, '2021-04-29 17:36:16', 1, '2021-04-29 17:36:16'),
(7, '04', 'Barangay 04 Poblacion', 0, 1, 1, '2021-04-29 17:36:16', 1, '2021-04-29 17:36:16'),
(8, '05', 'Barangay 05 Poblacion', 0, 1, 1, '2021-04-29 17:36:29', 1, '2021-04-29 17:36:29'),
(9, '06', 'Binitayan', 0, 1, 1, '2021-04-29 17:36:37', 1, '2021-04-29 17:36:37'),
(10, '07', 'Calbayog', 0, 1, 1, '2021-04-29 17:36:50', 1, '2021-04-29 17:36:50'),
(11, '08', 'Canaway', 0, 1, 1, '2021-04-29 17:37:00', 1, '2021-04-29 17:37:00'),
(12, '09', 'Salvacion', 0, 1, 1, '2021-04-29 17:37:28', 1, '2021-04-29 17:37:28'),
(13, '010', 'San Antonio - Santicon', 0, 1, 1, '2021-04-29 17:37:47', 1, '2021-04-29 17:37:47'),
(14, '011', 'San Antonio - Sulong', 0, 1, 1, '2021-04-29 17:38:08', 1, '2021-04-29 17:38:08'),
(15, '012', 'San Francisco', 0, 1, 1, '2021-04-29 17:38:25', 1, '2021-04-29 17:38:25'),
(16, '013', 'San Isidro Ilawod', 0, 1, 1, '2021-04-29 17:39:21', 1, '2021-04-29 17:39:21'),
(17, '014', 'San Isidro Iraya', 0, 1, 1, '2021-04-29 17:39:42', 1, '2021-04-29 17:39:42'),
(18, '015', 'San Jose', 0, 1, 1, '2021-04-29 17:39:51', 1, '2021-04-29 17:39:51'),
(19, '016', 'San Roque', 0, 1, 1, '2021-04-29 17:40:05', 1, '2021-04-29 17:40:15'),
(20, '017', 'Sta. Cruz', 0, 1, 1, '2021-04-29 17:40:29', 1, '2021-04-29 17:40:29'),
(21, '018', 'Sta. Teresa', 0, 1, 1, '2021-04-29 17:40:39', 1, '2021-04-29 17:40:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
