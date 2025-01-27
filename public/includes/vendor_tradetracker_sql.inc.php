<?php
/**
 * SCRIPT: vendor_tradetracker_sql.inc.php
 * PURPOSE: Database routines for TradeTracker dashboard.
 * 
 * This file contains functions for interacting with the database, including
 * executing SELECT queries, fetching configuration values, inserting multiple
 * rows into a table, and opening a database connection.
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

/**
 * Executes a SELECT query and returns the fetched rows.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param string $sql The SQL query to execute.
 * @param array $params The parameters to bind to the SQL query.
 * @return array The fetched rows as an associative array.
 */
function dbget($dbh, $sql, $params = []) {

    try {
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $fetched_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $fetched_rows;

    } catch (PDOException $e) {
        logError('Caught PDOException: ' . $e->getMessage());
        return [];
    }
}

/**
 * Fetches a configuration value from the database and decodes it from JSON.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param string $name The name of the configuration to fetch.
 * @return mixed The decoded configuration value, or null if not found.
 */
function dbget_config($dbh, $name) {

    try {
        $sql = "SELECT configuration FROM config WHERE name = :name";
        $rows = dbget($dbh, $sql, ['name' => $name]);
        if (count($rows) > 0) {
            return json_decode($rows[0]['configuration'], true);
        } else {
            throw new Exception('Configuration not found for name: ' . $name);
        }

    } catch (Exception $e) {
        logError('Caught Exception: ' . $e->getMessage());
        return null;
    }
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

/**
 * Opens a database connection using the provided configuration.
 *
 * @param array $dbconfig The database configuration.
 * @return PDO|null The PDO database connection handle, or null on failure.
 */
function dbopen($dbconfig) {

    try {
        // Validate configuration
        if (empty($dbconfig['db_pdo_driver_name']) || empty($dbconfig['db_hostname']) || empty($dbconfig['db_database']) || empty($dbconfig['db_username']) || empty($dbconfig['db_password'])) {
            throw new InvalidArgumentException('Invalid database configuration');
        }

        // Create PDO instance
        $dsn = $dbconfig['db_pdo_driver_name'] . ':host=' . $dbconfig['db_hostname'] . ';dbname=' . $dbconfig['db_database'] . ';charset=utf8mb4';
        $dbh = new PDO(
            $dsn,
            $dbconfig['db_username'],
            $dbconfig['db_password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false
            ]
        );

        return $dbh;

    } catch (PDOException $e) {
        logError('Caught PDOException: ' . $e->getMessage());
        return null;

    } catch (InvalidArgumentException $e) {
        logError('Caught InvalidArgumentException: ' . $e->getMessage());
        return null;
    }
}
