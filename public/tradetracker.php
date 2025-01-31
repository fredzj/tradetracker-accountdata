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

 /**
 * Generates an HTML table displaying affiliate sites from TradeTracker.
 *
 * This function fetches affiliate sites from the database and generates an HTML table
 * to display their details, including site name, status, creation date, type, category,
 * number of campaigns, and actions.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_affiliatesites($dbh) {
	
	$html		=	'';
	
	foreach (dbget_tradetracker_affiliatesites($dbh) as $affiliatesite) {
		
		$customerSiteURL	=	'https://affiliate.tradetracker.com/customerSite/view/ID/' . $affiliatesite['affiliatesiteID'];
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($affiliatesite['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><span class="badge text-bg-' . htmlspecialchars($affiliatesite['status'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($affiliatesite['status'], ENT_QUOTES, 'UTF-8') . '</span></td>';
        $html .= '<td>' . htmlspecialchars($affiliatesite['creationDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($affiliatesite['type'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($affiliatesite['category'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($affiliatesite['num_campaigns'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>'
              . '<a target="_blank" href="' . htmlspecialchars($affiliatesite['URL'], ENT_QUOTES, 'UTF-8') . '"><i class="fa-solid fa-browser"></i> visit web site</a><br>'
              . '<a target="_blank" href="' . htmlspecialchars($customerSiteURL, ENT_QUOTES, 'UTF-8') . '"><i class="fa-solid fa-eye"></i> view affiliate site</a>'
              . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="name" data-sortable="true">Site Name</th>'
          . '<th scope="col" data-field="status" data-sortable="true">Status</th>'
          . '<th scope="col" data-field="date" data-sortable="true">Creation Date</th>'
          . '<th scope="col" data-field="type" data-sortable="true">Type</th>'
          . '<th scope="col" data-field="category" data-sortable="true">Category</th>'
          . '<th scope="col" data-field="campaigns" data-sortable="true" data-align="right"># Campaigns</th>'
          . '<th scope="col" data-field="actions">Actions</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying campaigns from TradeTracker.
 *
 * This function fetches campaigns from the database and generates an HTML table
 * to display their details, including campaign name, status, category, logo, 
 * various commissions, start date, stop date, accepted affiliate sites, and actions.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_campaigns($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_campaigns($dbh) as $campaign) {
		
        $affiliateCampaignURL = 'https://affiliate.tradetracker.com/affiliateCampaign/view/ID/' . htmlspecialchars($campaign['campaignID'], ENT_QUOTES, 'UTF-8');
        $affiliateReportURL = 'https://affiliate.tradetracker.com/affiliateReport/campaign/ID/' . htmlspecialchars($campaign['campaignID'], ENT_QUOTES, 'UTF-8') . '?p%5Bgb%5D=-1&sort=&desc=0&limit=&outputType=4&c=&r=&action=&p%5Bt%5D=1&p%5Bt%5D=-1&p%5Bfd%5D=1&p%5Bfm%5D=1&p%5Bfy%5D=2005&p%5Btd%5D=3&p%5Btm%5D=11&p%5Bty%5D=2024'; // TODO: dynamic date
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($campaign['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><span class="badge text-bg-' . htmlspecialchars($campaign['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($campaign['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '</span></td>';
        $html .= '<td>' . htmlspecialchars($campaign['category'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><img src="' . htmlspecialchars($campaign['imageURL'], ENT_QUOTES, 'UTF-8') . '" height="50" alt=""></td>';

        $html .= generate_commission_cell($campaign['impressionCommission']);
        $html .= generate_commission_cell($campaign['clickCommission'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['fixedCommission'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['leadCommission'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['saleCommissionFixed'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['saleCommissionVariable'], null, '%');
        $html .= generate_commission_cell($campaign['iLeadCommission'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['iSaleCommissionFixed'], $fmtCUR, "EUR");
        $html .= generate_commission_cell($campaign['iSaleCommissionVariable'], null, '%');

        $html .= '<td>' . htmlspecialchars($campaign['startDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars(str_replace('0000-00-00', '', $campaign['stopDate']), ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($campaign['affiliateSites'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>'
              . '<a class="text-nowrap" target="_blank" href="' . $affiliateCampaignURL . '"><i class="fa-solid fa-eye"></i> view affiliatecampaign</a><br>'
              . '<a target="_blank" href="' . $affiliateReportURL . '"><i class="fa-solid fa-table"></i> View campaign report</a>'
              . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" rowspan="2" data-field="campaign" data-sortable="true">Campaign Name</th>'
          . '<th scope="col" rowspan="2" data-field="Status" data-sortable="true">Assignment<br>Status</th>'
          . '<th scope="col" rowspan="2" data-field="category" data-sortable="true">Category</th>'
          . '<th scope="col" rowspan="2" data-field="logo">Logo</th>'
          . '<th colspan="9">Commission</th>'
          . '<th scope="col" rowspan="2" data-field="startDate" data-sortable="true">Start Date</th>'
          . '<th scope="col" rowspan="2" data-field="stopDate" data-sortable="true">Stop Date</th>'
          . '<th scope="col" rowspan="2" data-field="affiliateSites">Accepted Affiliate Sites</th>'
          . '<th scope="col" rowspan="2" data-field="actions">Actions</th>'
          . '</tr>'
          . '<tr>'
          . '<th scope="col" data-field="impressionCommission" data-sortable="true" data-align="right">Impression<br>Comm</th>'
          . '<th scope="col" data-field="clickCommission" data-sortable="true" data-align="right">Click<br>Comm</th>'
          . '<th scope="col" data-field="fixedCommission" data-sortable="true" data-align="right">Fixed<br>Comm</th>'
          . '<th scope="col" data-field="leadCommission" data-sortable="true" data-align="right">Lead<br>Comm</th>'
          . '<th scope="col" data-field="saleCommissionFixed" data-sortable="true" data-align="right">Sale<br>Comm<br>Fixed</th>'
          . '<th scope="col" data-field="saleCommissionVariable" data-sortable="true" data-align="right">Sale<br>Comm<br>Variable</th>'
          . '<th scope="col" data-field="iLeadCommission" data-sortable="true" data-align="right">iLead<br>Comm</th>'
          . '<th scope="col" data-field="iSaleCommissionFixed" data-sortable="true" data-align="right">iSale<br>Comm<br>Fixed</th>'
          . '<th scope="col" data-field="iSaleCommissionVariable" data-sortable="true" data-align="right">iSale<br>Comm<br>Variable</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';

	return $html;
}

/**
 * Generates an HTML table cell for a commission value.
 *
 * @param string $value The commission value.
 * @param NumberFormatter|null $formatter The number formatter for currency formatting.
 * @param string $suffix The suffix to append to the value (e.g., '%').
 * @return string The generated HTML table cell as a string.
 */
function generate_commission_cell($value, $formatter = null, $suffix = '') {
    if ($value <> '0.00') {
        if ($formatter) {
            return '<td>' . $formatter->formatCurrency($value, $suffix) . '</td>';
        } else {
            return '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . $suffix . '</td>';
        }
    } else {
        return '<td></td>';
    }
}

/**
 * Generates an HTML table displaying campaign news items from TradeTracker.
 *
 * This function fetches campaign news items from the database and generates an HTML table
 * to display their details, including campaign name, type, title, content, publish date, 
 * and expiration date.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_campaign_news_items($dbh) {
	
	$html		=	'';
	
	foreach (dbget_tradetracker_newsitems($dbh) as $newsitem) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($newsitem['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($newsitem['campaignNewsType'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($newsitem['title'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $newsitem['content'] . '</td>';
        $html .= '<td>' . htmlspecialchars($newsitem['publishDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($newsitem['expirationDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="campaign" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="type" data-sortable="true">Type</th>'
          . '<th scope="col" data-field="title" data-sortable="true">Title</th>'
          . '<th scope="col" data-field="content" data-sortable="true">Content</th>'
          . '<th scope="col" data-field="publishDate" data-sortable="true">Published</th>'
          . '<th scope="col" data-field="expirationDate" data-sortable="true">Expiration</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying click transactions from TradeTracker.
 *
 * This function fetches click transactions from the database and generates an HTML table
 * to display their details, including affiliate site name, campaign name, click transaction ID,
 * reference, transaction type, transaction status, commission, registration date, referer URL, 
 * and whether the transaction has been paid out.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_click_transactions($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_clicks($dbh) as $click) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($click['affiliatesiteName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($click['campaignName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($click['clickTransactionID'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($click['reference'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($click['transactionType'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><span class="badge text-bg-' . htmlspecialchars($click['transactionStatus'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($click['transactionStatus'], ENT_QUOTES, 'UTF-8') . '</span></td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($click['commission'], $click['currency']) . '</td>';
        $html .= '<td>' . htmlspecialchars($click['registrationDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($click['refererURL'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . ($click['paidOut'] ? 'yes' : 'no') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="site" data-sortable="true">Affiliate Site</th>'
          . '<th scope="col" data-field="campaign" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="ID" data-sortable="true">Click Transaction ID</th>'
          . '<th scope="col" data-field="reference" data-sortable="true">Reference</th>'
          . '<th scope="col" data-field="transactionType" data-sortable="true">Transaction Type</th>'
          . '<th scope="col" data-field="transactionStatus" data-sortable="true">Transaction Status</th>'
          . '<th scope="col" data-field="commission" data-sortable="true" data-align="right">Commission</th>'
          . '<th scope="col" data-field="registrationDate" data-sortable="true">Registration Date</th>'
          . '<th scope="col" data-field="refererURL" data-sortable="true">Referer URL</th>'
          . '<th scope="col" data-field="paidOut" data-sortable="true">Paid Out</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying conversion transactions from TradeTracker.
 *
 * This function fetches conversion transactions from the database and generates an HTML table
 * to display their details, including affiliate site name, campaign name, campaign product, reference,
 * transaction type, transaction status, total touchpoints, attributed touchpoints, attributable commission,
 * description, commission, order amount, IP, registration date, assessment date, click to conversion time,
 * originating click date, rejection reason, paid out status, affiliate sites paid out, country code, and attribution model.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_conversion_transactions($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_conversions($dbh) as $conversion) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($conversion['affiliatesiteName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['campaignName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['campaignProduct'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['reference'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['transactionType'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><span class="badge text-bg-' . htmlspecialchars($conversion['transactionStatus'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($conversion['transactionStatus'], ENT_QUOTES, 'UTF-8') . '</span></td>';
        $html .= '<td>' . htmlspecialchars($conversion['numTouchPointsTotal'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['numTouchPointsAttributed'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($conversion['attributableCommission'], $conversion['currency']) . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['description'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($conversion['commission'], $conversion['currency']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($conversion['orderAmount'], $conversion['currency']) . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['IP'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['registrationDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['assessmentDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['clickToConversion'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['originatingClickDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['rejectionReason'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . ($conversion['paidOut'] ? 'yes' : 'no') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['affiliateSitesPaidOut'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['countryCode'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($conversion['attributionModel'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="affiliatesiteName" data-sortable="true">Affiliate Site</th>'
          . '<th scope="col" data-field="campaignName" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="campaignProduct" data-sortable="true">Campaign<br>Product</th>'
          . '<th scope="col" data-field="reference" data-sortable="true">Reference</th>'
          . '<th scope="col" data-field="transactionType" data-sortable="true">Transaction<br>Type</th>'
          . '<th scope="col" data-field="transactionStatus" data-sortable="true">Transaction<br>Status</th>'
          . '<th scope="col" data-field="numTouchPointsTotal" data-sortable="true" data-align="right"># Total<br>Touchpoints</th>'
          . '<th scope="col" data-field="numTouchPointsAttributed" data-sortable="true" data-align="right"># Attributed<br>Touchpoints</th>'
          . '<th scope="col" data-field="attributableCommission" data-sortable="true" data-align="right">Attributable<br>Commission</th>'
          . '<th scope="col" data-field="description" data-sortable="true">Description</th>'
          . '<th scope="col" data-field="commission" data-sortable="true" data-align="right">Commission</th>'
          . '<th scope="col" data-field="orderAmount" data-sortable="true" data-align="right">Order<br>Amount</th>'
          . '<th scope="col" data-field="IP" data-sortable="true">IP</th>'
          . '<th scope="col" data-field="registrationDate" data-sortable="true">Registration<br>Date</th>'
          . '<th scope="col" data-field="assessmentDate" data-sortable="true">Assessment<br>Date</th>'
          . '<th scope="col" data-field="clickToConversion" data-sortable="true" data-align="right">Click to<br>Conversion</th>'
          . '<th scope="col" data-field="originatingClickDate" data-sortable="true">Originating<br>Click Date</th>'
          . '<th scope="col" data-field="rejectionReason" data-sortable="true">Rejection<br>Reason</th>'
          . '<th scope="col" data-field="paidOut" data-sortable="true">Paid Out</th>'
          . '<th scope="col" data-field="affiliateSitesPaidOut" data-sortable="true">Affiliate Sites<br>Paid Out</th>'
          . '<th scope="col" data-field="countryCode" data-sortable="true">Country<br>Code</th>'
          . '<th scope="col" data-field="attributionModel" data-sortable="true">Attribution<br>Model</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying feed data from TradeTracker.
 *
 * This function fetches feed data from the database and generates an HTML table
 * to display their details, including campaign name, logo, feed name, assignment status,
 * last updated date, update interval, product count, accepted affiliate sites, and actions.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtDEC The number formatter for decimal formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_feeds($dbh, $fmtDEC) {
	
	$html		=	'';
	$intervals	=	[ 'P1D' => 'every day'
					, 'P1M' => 'every month'
					, 'PT1H' => 'every hour'
					, 'PT2H' => 'every 2 hours'
					, 'PT3H' => 'every 3 hours'
					, 'PT4H' => 'every 4 hours'
					, 'PT5H' => 'every 5 hours'
					, 'PT6H' => 'every 6 hours'
					, 'PT12H' => 'every 12 hours'];

	foreach (dbget_tradetracker_feeds($dbh) as $feed) {
		
		$affiliateCampaignURL	=	'https://affiliate.tradetracker.com/affiliateCampaign/view/ID/' . $feed['campaignID'];
		$affiliateMaterialURL	=	'https://affiliate.tradetracker.com/affiliateMaterial/generateFeedURL?productFeedID=' . $feed['feedID'];
		$interval				=	$feed['updateInterval'];
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($feed['campaignName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><img src="' . htmlspecialchars($feed['imageURL'], ENT_QUOTES, 'UTF-8') . '" height="50" alt=""></td>';
        $html .= '<td>' . htmlspecialchars($feed['campaignName'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($feed['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td><span class="badge text-bg-' . htmlspecialchars($feed['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($feed['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '</span></td>';
        $html .= '<td>' . htmlspecialchars($feed['updateDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        if (array_key_exists($interval, $intervals)) {
            $html .= '<td>' . htmlspecialchars($intervals[$interval], ENT_QUOTES, 'UTF-8') . '</td>';
        } else {
            $html .= '<td>' . htmlspecialchars($feed['updateInterval'], ENT_QUOTES, 'UTF-8') . '</td>';
        }
        $html .= '<td>' . $fmtDEC->format($feed['productCount']) . '</td>';
        $html .= '<td>' . htmlspecialchars($feed['affiliateSites'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>'
              . '<a class="text-nowrap" target="_blank" href="' . htmlspecialchars($affiliateCampaignURL, ENT_QUOTES, 'UTF-8') . '"><i class="fa-solid fa-eye"></i> view affiliatecampaign</a><br>'
              . '<a target="_blank" href="' . htmlspecialchars($affiliateMaterialURL, ENT_QUOTES, 'UTF-8') . '"><i class="fa-solid fa-gears"></i> generate feed URL</a><br>'
              . '<a target="_blank" href="' . htmlspecialchars($feed['URL'], ENT_QUOTES, 'UTF-8') . '"><i class="fa-solid fa-download"></i> download feed</a>'
              . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="campaign" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="logo">Logo</th>'
          . '<th scope="col" data-field="name" data-sortable="true">Feed Name</th>'
          . '<th scope="col" data-field="status" data-sortable="true">Assignment Status</th>'
          . '<th scope="col" data-field="date" data-sortable="true">Last Updated</th>'
          . '<th scope="col" data-field="interval" data-sortable="true">Update Interval</th>'
          . '<th scope="col" data-field="count" data-sortable="true" data-align="right">Product Count</th>'
          . '<th scope="col" data-field="sites">Accepted Affiliate Sites</th>'
          . '<th scope="col" data-field="actions">Actions</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying payment data from TradeTracker.
 *
 * This function fetches payment data from the database and generates an HTML table
 * to display their details, including bill date, pay date, invoice number, sub total,
 * VAT, and end total.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_payments($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_payments($dbh) as $payment) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($payment['billDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($payment['payDate'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($payment['invoiceNumber'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($payment['subTotal'], $payment['currency']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($payment['VAT'], $payment['currency']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($payment['endTotal'], $payment['currency']) . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="billDate" data-sortable="true">Bill Date</th>'
          . '<th scope="col" data-field="payDate" data-sortable="true">Pay Date</th>'
          . '<th scope="col" data-field="invoiceNumber" data-sortable="true">Invoice Number</th>'
          . '<th scope="col" data-field="subTotal" data-sortable="true" data-align="right">Sub Total</th>'
          . '<th scope="col" data-field="VAT" data-sortable="true" data-align="right">VAT</th>'
          . '<th scope="col" data-field="endTotal" data-sortable="true" data-align="right">End Total</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying report data by affiliate site from TradeTracker.
 *
 * This function fetches report data by affiliate site from the database and generates an HTML table
 * to display their details, including affiliate site name, overall impression count, unique impression count,
 * impression commission, overall click count, unique click count, click commission, lead count, lead commission,
 * sale count, sale commission, fixed commission, CTR, CLR, CSR, eCPM, EPC, and total commission.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @param NumberFormatter $fmtDEC The number formatter for decimal formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_report_affiliatesites($dbh, $fmtCUR, $fmtDEC) {

	$html		=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites($dbh) as $report) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($report['affiliateSiteName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['impressionCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallClickCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueClickCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['clickCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['leadCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['leadCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['saleCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['saleCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['fixedCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['CTR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CLR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CSR']) . '%</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['totalCommission'], 'EUR') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="affiliatesiteName" data-sortable="true">Affiliate Site</th>'
          . '<th scope="col" data-field="overallImpressionCount" data-sortable="true" data-align="right">Overall<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="uniqueImpressionCount" data-sortable="true" data-align="right">Unique<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="impressionCommission" data-sortable="true" data-align="right">Impression<br>Commission</th>'
          . '<th scope="col" data-field="overallClickCount" data-sortable="true" data-align="right">Overall<br>Click<br>Count</th>'
          . '<th scope="col" data-field="uniqueClickCount" data-sortable="true" data-align="right">Unique<br>Click<br>Count</th>'
          . '<th scope="col" data-field="clickCommission" data-sortable="true" data-align="right">Click<br>Commission</th>'
          . '<th scope="col" data-field="leadCount" data-sortable="true" data-align="right">Lead<br>Count</th>'
          . '<th scope="col" data-field="leadCommission" data-sortable="true" data-align="right">Lead<br>Commission</th>'
          . '<th scope="col" data-field="saleCount" data-sortable="true" data-align="right">Sale<br>Count</th>'
          . '<th scope="col" data-field="saleCommission" data-sortable="true" data-align="right">Sale<br>Commission</th>'
          . '<th scope="col" data-field="fixedCommission" data-sortable="true" data-align="right">Fixed<br>Commission</th>'
          . '<th scope="col" data-field="CTR" data-sortable="true" data-align="right">CTR</th>'
          . '<th scope="col" data-field="CLR" data-sortable="true" data-align="right">CLR</th>'
          . '<th scope="col" data-field="CSR" data-sortable="true" data-align="right">CSR</th>'
          . '<th scope="col" data-field="eCPM" data-sortable="true" data-align="right">eCPM</th>'
          . '<th scope="col" data-field="EPC" data-sortable="true" data-align="right">EPC</th>'
          . '<th scope="col" data-field="totalCommission" data-sortable="true" data-align="right">Total<br>Commission</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying report data by campaign from TradeTracker.
 *
 * This function fetches report data by campaign from the database and generates an HTML table
 * to display their details, including affiliate site name, campaign name, overall impression count,
 * unique impression count, impression commission, overall click count, unique click count, click commission,
 * lead count, lead commission, sale count, sale commission, fixed commission, CTR, CLR, CSR, eCPM, EPC, and total commission.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @param NumberFormatter $fmtDEC The number formatter for decimal formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_report_campaigns($dbh, $fmtCUR, $fmtDEC) {

	$html		=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites_campaigns($dbh) as $report) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($report['affiliateSiteName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($report['campaignName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['impressionCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallClickCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueClickCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['clickCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['leadCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['leadCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['saleCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['saleCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['fixedCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['CTR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CLR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CSR']) . '%</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['totalCommission'], 'EUR') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="affiliatesiteName" data-sortable="true">Affiliate Site</th>'
          . '<th scope="col" data-field="campaignName" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="overallImpressionCount" data-sortable="true" data-align="right">Overall<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="uniqueImpressionCount" data-sortable="true" data-align="right">Unique<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="impressionCommission" data-sortable="true" data-align="right">Impression<br>Commission</th>'
          . '<th scope="col" data-field="overallClickCount" data-sortable="true" data-align="right">Overall<br>Click<br>Count</th>'
          . '<th scope="col" data-field="uniqueClickCount" data-sortable="true" data-align="right">Unique<br>Click<br>Count</th>'
          . '<th scope="col" data-field="clickCommission" data-sortable="true" data-align="right">Click<br>Commission</th>'
          . '<th scope="col" data-field="leadCount" data-sortable="true" data-align="right">Lead<br>Count</th>'
          . '<th scope="col" data-field="leadCommission" data-sortable="true" data-align="right">Lead<br>Commission</th>'
          . '<th scope="col" data-field="saleCount" data-sortable="true" data-align="right">Sale<br>Count</th>'
          . '<th scope="col" data-field="saleCommission" data-sortable="true" data-align="right">Sale<br>Commission</th>'
          . '<th scope="col" data-field="fixedCommission" data-sortable="true" data-align="right">Fixed<br>Commission</th>'
          . '<th scope="col" data-field="CTR" data-sortable="true" data-align="right">CTR</th>'
          . '<th scope="col" data-field="CLR" data-sortable="true" data-align="right">CLR</th>'
          . '<th scope="col" data-field="CSR" data-sortable="true" data-align="right">CSR</th>'
          . '<th scope="col" data-field="eCPM" data-sortable="true" data-align="right">eCPM</th>'
          . '<th scope="col" data-field="EPC" data-sortable="true" data-align="right">EPC</th>'
          . '<th scope="col" data-field="totalCommission" data-sortable="true" data-align="right">Total<br>Commission</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

/**
 * Generates an HTML table displaying report data by reference from TradeTracker.
 *
 * This function fetches report data by reference from the database and generates an HTML table
 * to display their details, including affiliate site name, campaign name, reference, 
 * overall impression count, unique impression count, impression commission, overall click count,
 * unique click count, click commission, lead count, lead commission, sale count, sale commission,
 * fixed commission, CTR, CLR, CSR, eCPM, EPC, and total commission.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param NumberFormatter $fmtCUR The number formatter for currency formatting.
 * @param NumberFormatter $fmtDEC The number formatter for decimal formatting.
 * @return string The generated HTML table as a string.
 */
function get_html_report_references($dbh, $fmtCUR, $fmtDEC) {

	$html			=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites_campaigns_references($dbh) as $report) {
		
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($report['affiliateSiteName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($report['campaignName'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($report['reference'], ENT_QUOTES, 'UTF-8') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueImpressionCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['impressionCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['overallClickCount']) . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['uniqueClickCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['clickCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['leadCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['leadCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['saleCount']) . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['saleCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['fixedCommission'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtDEC->format($report['CTR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CLR']) . '%</td>';
        $html .= '<td>' . $fmtDEC->format($report['CSR']) . '%</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') . '</td>';
        $html .= '<td>' . $fmtCUR->formatCurrency($report['totalCommission'], 'EUR') . '</td>';
        $html .= '</tr>';
	}

    $html = '<table class="table table-sm" data-custom-sort="customSort"
                                data-toggle="table"
                                data-pagination="true"
                                data-search="true"
                                data-show-export="true">'
          . '<thead><tr>'
          . '<th scope="col" data-field="affiliatesiteName" data-sortable="true">Affiliate Site</th>'
          . '<th scope="col" data-field="campaignName" data-sortable="true">Campaign</th>'
          . '<th scope="col" data-field="reference" data-sortable="true">Reference</th>'
          . '<th scope="col" data-field="overallImpressionCount" data-sortable="true" data-align="right">Overall<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="uniqueImpressionCount" data-sortable="true" data-align="right">Unique<br>Impression<br>Count</th>'
          . '<th scope="col" data-field="impressionCommission" data-sortable="true" data-align="right">Impression<br>Commission</th>'
          . '<th scope="col" data-field="overallClickCount" data-sortable="true" data-align="right">Overall<br>Click<br>Count</th>'
          . '<th scope="col" data-field="uniqueClickCount" data-sortable="true" data-align="right">Unique<br>Click<br>Count</th>'
          . '<th scope="col" data-field="clickCommission" data-sortable="true" data-align="right">Click<br>Commission</th>'
          . '<th scope="col" data-field="leadCount" data-sortable="true" data-align="right">Lead<br>Count</th>'
          . '<th scope="col" data-field="leadCommission" data-sortable="true" data-align="right">Lead<br>Commission</th>'
          . '<th scope="col" data-field="saleCommission" data-sortable="true" data-align="right">Sale<br>Commission</th>'
          . '<th scope="col" data-field="fixedCommission" data-sortable="true" data-align="right">Fixed<br>Commission</th>'
          . '<th scope="col" data-field="CTR" data-sortable="true" data-align="right">CTR</th>'
          . '<th scope="col" data-field="CLR" data-sortable="true" data-align="right">CLR</th>'
          . '<th scope="col" data-field="CSR" data-sortable="true" data-align="right">CSR</th>'
          . '<th scope="col" data-field="eCPM" data-sortable="true" data-align="right">eCPM</th>'
          . '<th scope="col" data-field="EPC" data-sortable="true" data-align="right">EPC</th>'
          . '<th scope="col" data-field="totalCommission" data-sortable="true" data-align="right">Total<br>Commission</th>'
          . '</tr></thead>'
          . '<tbody>'
          . $html
          . '</tbody>'
          . '</table>';
	
	return $html;
}

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	date_default_timezone_set(	'Europe/Amsterdam');
	mb_internal_encoding(		'UTF-8');
	setlocale(LC_ALL,			'nl_NL.utf8');
	$server_domains_root	=	substr(__DIR__, 0, mb_stripos(__DIR__, 'public'));

	# Parse the DB configuration file
	$config_file_name_db	=	$server_domains_root . 'config/db.ini';
	if (($dbconfig			=	parse_ini_file($config_file_name_db,	FALSE, INI_SCANNER_TYPED)) === FALSE) {
		throw new Exception("Parsing file " . $config_file_name_db	. " FAILED");
	}
	
	# Get the SQL queries
	require $server_domains_root . 'database/sql.inc.php';
	
	###
	### CUSTOM INIT ROUTINE
	###
	
	$fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
	$fmtDEC		=	new NumberFormatter( 'nl_NL', NumberFormatter::DECIMAL );
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh		=	dbopen($dbconfig);

	###
	### PROCESSING ROUTINE
	###
	
	$html_affiliatesites			=	get_html_affiliatesites(			$dbh);
			
	$html_campaigns					=	get_html_campaigns(					$dbh, $fmtCUR);
		
	$html_feeds						=	get_html_feeds(						$dbh, 			$fmtDEC);
			
	$html_news_items				=	get_html_campaign_news_items(		$dbh);
		
	$html_click_transactions		=	get_html_click_transactions(		$dbh, $fmtCUR);
		
	$html_conversion_transactions	=	get_html_conversion_transactions(	$dbh, $fmtCUR);
	
	$html_payments					=	get_html_payments(					$dbh, $fmtCUR);
				
	$html_report_affiliatesite		=	get_html_report_affiliatesites(		$dbh, $fmtCUR,	$fmtDEC);
					
	$html_report_campaign			=	get_html_report_campaigns(			$dbh, $fmtCUR,	$fmtDEC);
					
	$html_report_reference			=	get_html_report_references(			$dbh, $fmtCUR,	$fmtDEC);

	###
	### DATABASE EXIT ROUTINE
	###
		
	$dbh = null;

	###
	### STANDARD EXCEPTION ROUTINE
	###

} catch (PDOException $e) {
	
	echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . '<br/>';
	
} catch (Exception $e) {
	
	echo date("[G:i:s] ") . 'Caught Exception: ' . $e->getMessage() . '<br/>';
	
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TradeTracker WebServices</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">
	<style>
	body {
		font-size: small;
	}
	img {
		background-color: #fff;
	}
	.text-bg-accepted {
		color: #fff !important;
		background-color: RGBA(var(--bs-success-rgb), var(--bs-bg-opacity, 1)) !important;
	}
	.text-bg-notsignedup {
		color: #999 !important;
		background-color: RGBA(var(--bs-light-rgb), var(--bs-bg-opacity, 1)) !important;
	}
	.text-bg-onhold {
		color: #fff !important;
		background-color: RGBA(var(--bs-info-rgb), var(--bs-bg-opacity, 1)) !important;
	}
	.text-bg-pending {
		color: #fff !important;
		background-color: RGBA(var(--bs-info-rgb),		var(--bs-bg-opacity, 1)) !important;
	}
	.text-bg-rejected {
		color: #fff !important;
		background-color: RGBA(var(--bs-danger-rgb), var(--bs-bg-opacity, 1)) !important;
	}
	.text-bg-signedout {
		color: #fff !important;
		background-color: RGBA(var(--bs-warning-rgb), var(--bs-bg-opacity, 1)) !important;
	}
	</style>
	<script src="https://kit.fontawesome.com/da52944850.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container-fluid">

	<h1>TradeTracker Dashboard</h1>

	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="sites-tab"			data-bs-toggle="tab" data-bs-target="#sites-tab-pane"		type="button" role="tab" aria-controls="sites-tab-pane"		aria-selected="true">Affiliate Sites</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="campaigns-tab"		data-bs-toggle="tab" data-bs-target="#campaigns-tab-pane"	type="button" role="tab" aria-controls="campaigns-tab-pane"	aria-selected="false">Campaigns</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="feeds-tab"			data-bs-toggle="tab" data-bs-target="#feeds-tab-pane"		type="button" role="tab" aria-controls="feeds-tab-pane"		aria-selected="false">Feeds</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="clicks-tab"			data-bs-toggle="tab" data-bs-target="#clicks-tab-pane"		type="button" role="tab" aria-controls="clicks-tab-pane"		aria-selected="false">Clicks</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="conversions-tab"	data-bs-toggle="tab" data-bs-target="#conversions-tab-pane"	type="button" role="tab" aria-controls="conversions-tab-pane"		aria-selected="false">Conversions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="payments-tab"		data-bs-toggle="tab" data-bs-target="#payments-tab-pane"	type="button" role="tab" aria-controls="payments-tab-pane"	aria-selected="false">Payments</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="repaff-tab"			data-bs-toggle="tab" data-bs-target="#repaff-tab-pane"		type="button" role="tab" aria-controls="repaff-tab-pane"		aria-selected="false">Report by Affiliate Site</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="repcam-tab"			data-bs-toggle="tab" data-bs-target="#repcam-tab-pane"		type="button" role="tab" aria-controls="repcam-tab-pane"		aria-selected="false">Report by Campaign</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="repref-tab"			data-bs-toggle="tab" data-bs-target="#repref-tab-pane"		type="button" role="tab" aria-controls="repref-tab-pane"		aria-selected="false">Report by Reference</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="news-tab"			data-bs-toggle="tab" data-bs-target="#news-tab-pane"		type="button" role="tab" aria-controls="news-tab-pane"		aria-selected="false">News</button>
		</li>
	</ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="sites-tab-pane" role="tabpanel" aria-labelledby="sites-tab" tabindex="0">
            <?php echo $html_affiliatesites; ?>
        </div>
        <div class="tab-pane fade" id="campaigns-tab-pane" role="tabpanel" aria-labelledby="campaigns-tab" tabindex="0">
            <?php echo $html_campaigns; ?>
        </div>
        <div class="tab-pane fade" id="clicks-tab-pane" role="tabpanel" aria-labelledby="clicks-tab" tabindex="0">
            <?php echo $html_click_transactions; ?>
        </div>
        <div class="tab-pane fade" id="conversions-tab-pane" role="tabpanel" aria-labelledby="conversions-tab" tabindex="0">
            <?php echo $html_conversion_transactions; ?>
        </div>
        <div class="tab-pane fade" id="feeds-tab-pane" role="tabpanel" aria-labelledby="feeds-tab" tabindex="0">
            <?php echo $html_feeds; ?>
        </div>
        <div class="tab-pane fade" id="news-tab-pane" role="tabpanel" aria-labelledby="news-tab" tabindex="0">
            <?php echo $html_news_items; ?>
        </div>
        <div class="tab-pane fade" id="payments-tab-pane" role="tabpanel" aria-labelledby="payments-tab" tabindex="0">
            <?php echo $html_payments; ?>
        </div>
        <div class="tab-pane fade" id="repaff-tab-pane" role="tabpanel" aria-labelledby="repaff-tab" tabindex="0">
            <?php echo $html_report_affiliatesite; ?>
        </div>
        <div class="tab-pane fade" id="repcam-tab-pane" role="tabpanel" aria-labelledby="repcam-tab" tabindex="0">
            <?php echo $html_report_campaign; ?>
        </div>
        <div class="tab-pane fade" id="repref-tab-pane" role="tabpanel" aria-labelledby="repref-tab" tabindex="0">
            <?php echo $html_report_reference; ?>
        </div>
    </div>

	<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/extensions/export/bootstrap-table-export.min.js"></script>
	<script>
		function customSort(sortName, sortOrder, data) {
			var order = sortOrder === 'desc' ? -1 : 1
			data.sort(function (a, b) {
			var aa = +((a[sortName] + '').replace(/[^\d]/g, ''))
			var bb = +((b[sortName] + '').replace(/[^\d]/g, ''))
			if (aa < bb) {
				return order * -1
			}
			if (aa > bb) {
				return order
			}
			return 0
			})
		}
	</script>
</div>
</body>
</html>