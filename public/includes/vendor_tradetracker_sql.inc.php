<?php
/*

	SCRIPT:		sql.inc.php
	
	PURPOSE:	Database routines for TradeTracker dashboard.
	
	Copyright 2024 Fred Onis - All rights reserved.

	dbget
	dbget_tradetracker_affiliatesites
	dbget_tradetracker_campaigns
	dbget_tradetracker_clicks
	dbget_tradetracker_feeds
	dbget_tradetracker_newsitems
	dbget_tradetracker_payments
	dbget_tradetracker_report_affiliatesites
	dbget_tradetracker_report_affiliatesites_campaigns
	dbget_tradetracker_report_affiliatesites_campaigns_references
	dbopen
*/

function dbget($dbh, $sql) {
	
	$stmt			=	$dbh->prepare($sql);
	
	$stmt->execute();
	$fetched_rows	=	$stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$stmt			=	null;

	return	$fetched_rows;
}

function dbget_config($dbh, $name) {

	$sql			=	"
	SELECT			configuration
	FROM			config
	WHERE			name	=	'$name'";
	
	$rows	=	dbget($dbh, $sql);
	
	return	json_decode($rows[0]['configuration'], true);
}

function dbget_tradetracker_affiliatesites($dbh) {

	$sql			=	"
	SELECT			s.ID											AS	affiliatesiteID,
					s.name,
					s.URL,
					s.type,
					s.category,
					s.description,
					s.creationDate,
					s.status,
					COUNT(c.id)										AS	num_campaigns
	FROM			vendor_tradetracker_affiliatesites s
	LEFT JOIN		vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID	AND
																		c.assignmentStatus	=	'accepted'
	GROUP BY		1
	ORDER BY		name";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_campaigns($dbh) {

	$sql			=	"
	SELECT			c.*,
					GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ')	AS	affiliateSites
	FROM			vendor_tradetracker_campaigns c
	LEFT JOIN		vendor_tradetracker_affiliatesites s			ON	s.ID				=	c.affiliateSiteID
	WHERE			c.assignmentStatus								<>	'notsignedup'
	GROUP BY		c.name";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_clicks($dbh) {

	$sql			=	"
	SELECT			s.name											AS	affiliatesiteName,
					c.name											AS	campaignName,
					t.*
	FROM			vendor_tradetracker_affiliatesites s
	JOIN			vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID
	JOIN			vendor_tradetracker_click_transactions t		ON	t.affiliateSiteID	=	s.ID	AND	t.campaignID	=	c.campaignID
	ORDER BY		1, 2";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_conversions($dbh) {

	$sql			=	"
	SELECT			s.name											AS	affiliatesiteName,
					c.name											AS	campaignName,
					t.*
	FROM			vendor_tradetracker_affiliatesites s
	JOIN			vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID
	JOIN			vendor_tradetracker_conversion_transactions t	ON	t.affiliateSiteID	=	s.ID	AND	t.campaignID	=	c.campaignID
	ORDER BY		1, 2";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_feeds($dbh) {

	$sql			=	"
	SELECT			c.name											AS	campaignName,
					c.imageURL,
					f.*,
					GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ')	AS	affiliateSites
	FROM			vendor_tradetracker_affiliatesites s
	JOIN			vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID
	JOIN			vendor_tradetracker_feeds f						ON	f.affiliateSiteID	=	s.ID	AND	f.campaignID	=	c.campaignID
	WHERE			f.assignmentStatus								<>	'notsignedup'
	AND				c.assignmentStatus								<>	'notsignedup'
	GROUP BY		f.feedID
	ORDER BY		c.name, f.name";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_newsitems($dbh) {

	$sql			=	"
	SELECT			DISTINCT
					c.name,
					n.campaignNewsType,
					n.title,
					n.content,
					n.publishDate,
					n.expirationDate
	FROM			vendor_tradetracker_campaigns_newsitems n
	LEFT JOIN		vendor_tradetracker_campaigns c			ON	c.campaignID		=	n.campaignID
	WHERE			c.assignmentStatus						=	'accepted'
	ORDER BY		5 DESC, 6 DESC, 1";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_payments($dbh) {

	$sql			=	"
	SELECT			*
	FROM			vendor_tradetracker_payments
	ORDER BY		billDate	DESC";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_report_affiliatesites($dbh) {

	$sql			=	"
	SELECT			s.name															AS	affiliateSiteName,
					r.*
	FROM			vendor_tradetracker_affiliatesites s
	JOIN			vendor_tradetracker_report_affiliatesite r						ON	r.affiliateSiteID	=	s.ID
	ORDER BY		s.name";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_report_affiliatesites_campaigns($dbh) {

	$sql			=	"
	SELECT			s.name															AS	affiliateSiteName,
					c.name															AS	campaignName,
					r.*			
	FROM			vendor_tradetracker_affiliatesites s			
	JOIN			vendor_tradetracker_campaigns c									ON	c.affiliateSiteID	=	s.ID
	JOIN			vendor_tradetracker_report_affiliatesite_campaign r				ON	r.affiliateSiteID	=	s.ID	AND	r.campaignID	=	c.campaignID
	ORDER BY		s.name, c.name";
	
	return	dbget($dbh, $sql);
}

function dbget_tradetracker_report_affiliatesites_campaigns_references($dbh) {

	$sql			=	"
	SELECT			s.name															AS	affiliateSiteName,
					c.name															AS	campaignName,
					r.*
	FROM			vendor_tradetracker_affiliatesites s
	JOIN			vendor_tradetracker_campaigns c									ON	c.affiliateSiteID	=	s.ID
	JOIN			vendor_tradetracker_report_affiliatesite_campaign_reference r	ON	r.affiliateSiteID	=	s.ID	AND	r.campaignID	=	c.campaignID
	ORDER BY		s.name, c.name";
	
	return	dbget($dbh, $sql);
}

function dbopen($dbconfig) {

	$dbh	=	new PDO($dbconfig['db_pdo_driver_name']	. ':host=' . $dbconfig['db_hostname']  . ';dbname='	. $dbconfig['db_database'] . ';charset=utf8mb4',
						$dbconfig['db_username'],
						$dbconfig['db_password'],
						array(
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::ATTR_PERSISTENT => false
						)
	);
	
	return $dbh;
}