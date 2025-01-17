-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 15, 2025 at 06:40 PM
-- Server version: 10.6.18-MariaDB-cll-lve
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u10919p285003_see`
--

-- --------------------------------------------------------

--
-- Table structure for table `vendor_tradetracker_material_htmlitems`
--

CREATE TABLE `vendor_tradetracker_material_htmlitems` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `affiliateSiteID` mediumint(8) UNSIGNED NOT NULL,
  `materialOutputType` varchar(255) NOT NULL,
  `materialItemID` mediumint(8) UNSIGNED NOT NULL,
  `campaignID` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creationDate` date DEFAULT NULL,
  `modificationDate` date DEFAULT NULL,
  `materialBannerDimensionID` tinyint(3) UNSIGNED DEFAULT NULL,
  `referenceSupported` tinyint(1) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `conditions` varchar(255) DEFAULT NULL,
  `validFromDate` date DEFAULT NULL,
  `validToDate` date DEFAULT NULL,
  `discountFixed` decimal(8,2) UNSIGNED DEFAULT NULL,
  `discountVariable` decimal(8,2) UNSIGNED DEFAULT NULL,
  `voucherCode` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_tradetracker_material_htmlitems`
--
ALTER TABLE `vendor_tradetracker_material_htmlitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliateSiteID` (`affiliateSiteID`),
  ADD KEY `campaignID` (`campaignID`),
  ADD KEY `materialBannerDimensionID` (`materialBannerDimensionID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_tradetracker_material_htmlitems`
--
ALTER TABLE `vendor_tradetracker_material_htmlitems`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
