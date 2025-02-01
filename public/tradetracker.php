<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * SCRIPT: tradetracker.php
 * PURPOSE: Show data from TradeTracker in a dashboard.
 * 
 * This script generates a web-based dashboard that displays various data from TradeTracker.
 * It connects to the database to fetch data related to affiliate sites, campaigns, transactions,
 * and reports, and presents this data in a user-friendly format using HTML tables and Bootstrap
 * for styling. The dashboard provides an overview of the TradeTracker account, allowing users
 * to easily view and analyze the data.
 * 
 * @package tradetracker-accountdata
 * @version 1.0.0
 * @since 2024
 * @license MIT
 * 
 * COPYRIGHT: 2024 Fred Onis - All rights reserved.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * @author Fred Onis
 */
require 'classes/Database.php';
require 'classes/TradeTrackerDashboard.php';

// Set defaults
date_default_timezone_set('Europe/Amsterdam');
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'nl_NL.utf8');

$dbConfigPath = mb_substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));
$dbConfigPath = mb_substr($dbConfigPath, 0, mb_strrpos($dbConfigPath, '/')) . '/config/db.ini';
	
// Create an instance of the dashboard and get the data
try {
	
    $dashboard = new TradeTrackerDashboard($dbConfigPath);

	$html_affiliatesites			=	$dashboard->getHtmlAffiliateSites();
	$html_campaigns					=	$dashboard->getHtmlCampaigns();
	$html_feeds						=	$dashboard->getHtmlFeeds();
	$html_news_items				=	$dashboard->getHtmlCampaignNewsItems();
	$html_click_transactions		=	$dashboard->getHtmlClickTransactions();
	$html_conversion_transactions	=	$dashboard->getHtmlConversionTransactions();
	$html_payments					=	$dashboard->getHtmlPayments();
	$html_report_affiliatesite		=	$dashboard->getHtmlReportAffiliatesites();
	$html_report_campaign			=	$dashboard->getHtmlReportCampaigns();
	$html_report_reference			=	$dashboard->getHtmlReportReferences();

    require 'templates/tradetracker.php';

} catch (PDOException $e) {
    echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo date("[G:i:s] ") . 'Caught Exception: ' . $e->getMessage() . PHP_EOL;
} finally {
}