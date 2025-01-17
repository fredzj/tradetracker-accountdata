-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 15, 2025 at 06:36 PM
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
-- Table structure for table `vendor_tradetracker_campaigns`
--

CREATE TABLE `vendor_tradetracker_campaigns` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `affiliateSiteID` mediumint(8) UNSIGNED NOT NULL,
  `campaignID` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `subCategories` varchar(255) DEFAULT NULL,
  `campaignDescription` text DEFAULT NULL,
  `shopDescription` text DEFAULT NULL,
  `targetGroup` text DEFAULT NULL,
  `characteristics` text DEFAULT NULL,
  `imageURL` varchar(255) DEFAULT NULL,
  `trackingURL` varchar(255) DEFAULT NULL,
  `impressionCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `clickCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `fixedCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `leadCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `saleCommissionFixed` decimal(8,2) UNSIGNED DEFAULT NULL,
  `saleCommissionVariable` decimal(8,2) UNSIGNED DEFAULT NULL,
  `iLeadCommission` decimal(8,2) UNSIGNED DEFAULT NULL,
  `iSaleCommissionFixed` decimal(8,2) UNSIGNED DEFAULT NULL,
  `iSaleCommissionVariable` decimal(8,2) UNSIGNED DEFAULT NULL,
  `assignmentStatus` varchar(255) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `stopDate` date DEFAULT NULL,
  `timeZone` varchar(255) DEFAULT NULL,
  `clickToConversion` varchar(255) DEFAULT NULL,
  `policySearchEngineMarketingStatus` varchar(255) DEFAULT NULL,
  `policyEmailMarketingStatus` varchar(255) DEFAULT NULL,
  `policyCashbackStatus` varchar(255) DEFAULT NULL,
  `policyDiscountCodeStatus` varchar(255) DEFAULT NULL,
  `deeplinkingSupported` tinyint(1) DEFAULT NULL,
  `referencesSupported` tinyint(1) DEFAULT NULL,
  `leadMaximumAssessmentInterval` varchar(255) DEFAULT NULL,
  `leadAverageAssessmentInterval` varchar(255) DEFAULT NULL,
  `saleMaximumAssessmentInterval` varchar(255) DEFAULT NULL,
  `saleAverageAssessmentInterval` varchar(255) DEFAULT NULL,
  `attributionModelLead` varchar(255) DEFAULT NULL,
  `attributionModelSales` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_tradetracker_campaigns`
--
ALTER TABLE `vendor_tradetracker_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliateSiteID` (`affiliateSiteID`),
  ADD KEY `campaignID` (`campaignID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_tradetracker_campaigns`
--
ALTER TABLE `vendor_tradetracker_campaigns`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
