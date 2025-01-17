-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 15, 2025 at 06:42 PM
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
-- Table structure for table `vendor_tradetracker_report_affiliatesite_campaign_reference`
--

CREATE TABLE `vendor_tradetracker_report_affiliatesite_campaign_reference` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `affiliateSiteID` mediumint(8) UNSIGNED NOT NULL,
  `campaignID` mediumint(8) UNSIGNED NOT NULL,
  `reference` varchar(255) NOT NULL,
  `overallImpressionCount` mediumint(8) UNSIGNED DEFAULT NULL,
  `uniqueImpressionCount` mediumint(8) UNSIGNED DEFAULT NULL,
  `impressionCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `overallClickCount` mediumint(8) UNSIGNED DEFAULT NULL,
  `uniqueClickCount` mediumint(8) UNSIGNED DEFAULT NULL,
  `clickCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `leadCount` mediumint(8) UNSIGNED DEFAULT NULL,
  `leadCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `saleCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `fixedCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `CTR` decimal(8,2) UNSIGNED DEFAULT NULL,
  `CLR` decimal(8,2) UNSIGNED DEFAULT NULL,
  `CSR` decimal(8,2) UNSIGNED DEFAULT NULL,
  `eCPM` decimal(8,2) UNSIGNED DEFAULT NULL,
  `EPC` decimal(8,2) UNSIGNED DEFAULT NULL,
  `totalCommission` decimal(8,2) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci COMMENT='empty';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_tradetracker_report_affiliatesite_campaign_reference`
--
ALTER TABLE `vendor_tradetracker_report_affiliatesite_campaign_reference`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliateSiteID` (`affiliateSiteID`),
  ADD KEY `campaignID` (`campaignID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_tradetracker_report_affiliatesite_campaign_reference`
--
ALTER TABLE `vendor_tradetracker_report_affiliatesite_campaign_reference`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
