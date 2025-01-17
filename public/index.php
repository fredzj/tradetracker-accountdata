<?php
/*

	SCRIPT:		index.php
	
	PURPOSE:	Show data from TradeTracker in a dashboard.
	
	Copyright 2024 Fred Onis - All rights reserved.
	
	get_html_affiliatesites
	get_html_campaigns
	get_html_campaign_news
	get_html_clicks
	get_html_conversions
	get_html_feeds
	get_html_payments
	get_html_report_affiliatesites
	get_html_report_campaigns
	get_html_report_references

*/
function get_html_affiliatesites($dbh) {
	
	$html		=	'';
	
	foreach (dbget_tradetracker_affiliatesites($dbh) as $affiliatesite) {
		
		$customerSiteURL	=	'https://affiliate.tradetracker.com/customerSite/view/ID/' . $affiliatesite['affiliatesiteID'];
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $affiliatesite['name'] . '</td>';
		$html	.=	'<td><span class="badge text-bg-' . $affiliatesite['status'] .  '">' . $affiliatesite['status'] .  '</span></td>';
		$html	.=	'<td>' . $affiliatesite['creationDate'] . '</td>';
		$html	.=	'<td>' . $affiliatesite['type'] . '</td>';
		$html	.=	'<td>' . $affiliatesite['category'] . '</td>';
		$html	.=	'<td>' . $affiliatesite['num_campaigns'] . '</td>';
		$html	.=	'<td>'
				.	'<a target="_blank" href="' . $affiliatesite['URL'] . '"><i class="fa-solid fa-browser"	></i> visit web site</a><br>'
				.	'<a target="_blank" href="' . $customerSiteURL .  '"	><i class="fa-solid fa-eye"		></i> view affiliate site</a>'
				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="name"	 	data-sortable="true"					>Site Name</th>'
			.	'<th scope="col" data-field="status" 	data-sortable="true"					>Status</th>'
			.	'<th scope="col" data-field="date"	 	data-sortable="true"					>Creation Date</th>'
			.	'<th scope="col" data-field="type"	 	data-sortable="true"					>Type</th>'
			.	'<th scope="col" data-field="category"	data-sortable="true"					>Category</th>'
			.	'<th scope="col" data-field="campaigns"	data-sortable="true" data-align="right"	># Campaigns</th>'
			.	'<th scope="col" data-field="actions"	 										>Actions</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_campaigns($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_campaigns($dbh) as $campaign) {
		
		$affiliateCampaignURL	=	'https://affiliate.tradetracker.com/affiliateCampaign/view/ID/' . $campaign['campaignID'];
		$affiliateReportURL		=	'https://affiliate.tradetracker.com/affiliateReport/campaign/ID/' . $campaign['campaignID'] . '?p%5Bgb%5D=-1&sort=&desc=0&limit=&outputType=4&c=&r=&action=&p%5Bt%5D=1&p%5Bt%5D=-1&p%5Bfd%5D=1&p%5Bfm%5D=1&p%5Bfy%5D=2005&p%5Btd%5D=3&p%5Btm%5D=11&p%5Bty%5D=2024';// TODO: dynamic date
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $campaign['name'] . '</td>';
		$html	.=	'<td><span class="badge text-bg-' . $campaign['assignmentStatus'] .  '">' . $campaign['assignmentStatus'] .  '</span></td>';
		$html	.=	'<td>' . $campaign['category'] . '</td>';
		$html	.=	'<td><img src="' . $campaign['imageURL'] . '" height="50" alt=""></td>';

		if ($campaign['impressionCommission'] <> '0.00') {
			$html	.=	'<td>' . $campaign['impressionCommission'] . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['clickCommission'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['clickCommission'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['fixedCommission'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['fixedCommission'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['leadCommission'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['leadCommission'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['saleCommissionFixed'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['saleCommissionFixed'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['saleCommissionVariable'] <> '0.00') {
			$html	.=	'<td>' . $campaign['saleCommissionVariable'] . '%</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['iLeadCommission'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['iLeadCommission'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['iSaleCommissionFixed'] <> '0.00') {
			$html	.=	'<td>' . $fmtCUR->formatCurrency($campaign['iSaleCommissionFixed'], "EUR") . '</td>';
		} else {
			$html	.=	'<td></td>';
		}
		if ($campaign['iSaleCommissionVariable'] <> '0.00') {
			$html	.=	'<td>' . $campaign['iSaleCommissionVariable'] . '%</td>';
		} else {
			$html	.=	'<td></td>';
		}
		
		$html	.=	'<td>' . $campaign['startDate'] .												'</td>';
		$html	.=	'<td>' . str_replace('0000-00-00', '', $campaign['stopDate']) .					'</td>';
		$html	.=	'<td>' . $campaign['affiliateSites'] .											'</td>';
		$html	.=	'<td>'
				.	'<a class="text-nowrap" target="_blank" href="' . $affiliateCampaignURL .  '"><i class="fa-solid fa-eye"></i> view affiliatecampaign</a><br>'
				.	'<a target="_blank" href="' . $affiliateReportURL . '"><i class="fa-solid fa-table"></i> View campaign report</a>'
				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" rowspan="2" data-field="campaign"				 	data-sortable="true"					>Campaign Name</th>'
			.	'<th scope="col" rowspan="2" data-field="Status"	 				data-sortable="true"					>Assignment<br>Status</th>'
			.	'<th scope="col" rowspan="2" data-field="category"	 				data-sortable="true"					>Category</th>'
			.	'<th scope="col" rowspan="2" data-field="logo"					 											>Logo</th>'
			.	'<th colspan="9">Commission</th>'
			.	'<th scope="col" rowspan="2" data-field="startDate"	 				data-sortable="true"					>Start Date</th>'
			.	'<th scope="col" rowspan="2" data-field="stopDate"	 				data-sortable="true"					>Stop Date</th>'
			.	'<th scope="col" rowspan="2" data-field="affiliateSites"		 											>Accepted Affiliate Sites</th>'
			.	'<th scope="col" rowspan="2" data-field="actions"	 														>Actions</th>'
			.	'</tr>'
			.	'<tr>'
			.	'<th scope="col" data-field="impressionCommission"	 	data-sortable="true" data-align="right"	>Impression<br>Comm</th>'
			.	'<th scope="col" data-field="clickCommission"	 		data-sortable="true" data-align="right"	>Click<br>Comm</th>'
			.	'<th scope="col" data-field="fixedCommission"		 	data-sortable="true" data-align="right"	>Fixed<br>Comm</th>'
			.	'<th scope="col" data-field="leadCommission"		 	data-sortable="true" data-align="right"	>Lead<br>Comm</th>'
			.	'<th scope="col" data-field="saleCommissionFixed"	 	data-sortable="true" data-align="right"	>Sale<br>Comm<br>Fixed</th>'
			.	'<th scope="col" data-field="saleCommissionVariable" 	data-sortable="true" data-align="right"	>Sale<br>Comm<br>Variable</th>'
			.	'<th scope="col" data-field="iLeadCommission"	 		data-sortable="true" data-align="right"	>iLead<br>Comm</th>'
			.	'<th scope="col" data-field="iSaleCommissionFixed"	 	data-sortable="true" data-align="right"	>iSale<br>Comm<br>Fixed</th>'
			.	'<th scope="col" data-field="iSaleCommissionVariable" 	data-sortable="true" data-align="right"	>iSale<br>Comm<br>Variable</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';

	return $html;
}

function get_html_campaign_news_items($dbh) {
	
	$html		=	'';
	
	foreach (dbget_tradetracker_newsitems($dbh) as $newsitem) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $newsitem['name'] .				'</td>';
		$html	.=	'<td>' . $newsitem['campaignNewsType'] .	'</td>';
		$html	.=	'<td>' . $newsitem['title'] .				'</td>';
		$html	.=	'<td>' . $newsitem['content'] .				'</td>';
		$html	.=	'<td>' . $newsitem['publishDate'] .			'</td>';
		$html	.=	'<td>' . $newsitem['expirationDate'] .		'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="campaign"			data-sortable="true">Campaign</th>'
			.	'<th scope="col" data-field="type"	 			data-sortable="true">Type</th>'
			.	'<th scope="col" data-field="title"				data-sortable="true">Title</th>'
			.	'<th scope="col" data-field="content" 			data-sortable="true">Content</th>'
			.	'<th scope="col" data-field="publishDate" 		data-sortable="true">Published</th>'
			.	'<th scope="col" data-field="expirationDate"	data-sortable="true">Expiration</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_click_transactions($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_clicks($dbh) as $click) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $click['affiliatesiteName'] .											'</td>';
		$html	.=	'<td>' . $click['campaignName'] .												'</td>';
		$html	.=	'<td>' . $click['clickTransactionID'] .											'</td>';
		$html	.=	'<td>' . $click['reference'] .													'</td>';
		$html	.=	'<td>' . $click['transactionType'] .											'</td>';
		$html	.=	'<td><span class="badge text-bg-' . $click['transactionStatus'] .  '">' . $click['transactionStatus'] .	'</span></td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($click['commission'], $click['currency']) .	'</td>';
		$html	.=	'<td>' . $click['registrationDate'] .											'</td>';
		$html	.=	'<td>' . $click['refererURL'] .													'</td>';
		$html	.=	'<td>' . ($click['paidOut'] ? 'yes' : 'no') .									'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="site"	 			data-sortable="true"					>Affiliate Site</th>'
			.	'<th scope="col" data-field="campaign"			data-sortable="true"					>Campaign</th>'
			.	'<th scope="col" data-field="ID"	 			data-sortable="true"					>Click Transaction ID</th>'
			.	'<th scope="col" data-field="reference"			data-sortable="true"					>Reference</th>'
			.	'<th scope="col" data-field="transactionType" 	data-sortable="true"					>Transaction Type</th>'
			.	'<th scope="col" data-field="transactionStatus" data-sortable="true"					>Transaction Status</th>'
			.	'<th scope="col" data-field="commission"		data-sortable="true" data-align="right"	>Commission</th>'
			.	'<th scope="col" data-field="registrationDate"	data-sortable="true"					>Registration Date</th>'
			.	'<th scope="col" data-field="refererURL"		data-sortable="true"					>Referer URL</th>'
			.	'<th scope="col" data-field="paidOut"			data-sortable="true"					>Paid Out</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_conversion_transactions($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_conversions($dbh) as $conversion) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $conversion['affiliatesiteName'] .															'</td>';
		$html	.=	'<td>' . $conversion['campaignName'] .																'</td>';
		$html	.=	'<td>' . $conversion['campaignProduct'] .															'</td>';
		$html	.=	'<td>' . $conversion['reference'] .																	'</td>';
		$html	.=	'<td>' . $conversion['transactionType'] .															'</td>';
		$html	.=	'<td><span class="badge text-bg-' . $click['transactionStatus'] .  '">' . $click['transactionStatus'] .	'</span></td>';
		$html	.=	'<td>' . $conversion['numTouchPointsTotal'] .														'</td>';
		$html	.=	'<td>' . $conversion['numTouchPointsAttributed'] .													'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($conversion['attributableCommission'], $conversion['currency']) .	'</td>';
		$html	.=	'<td>' . $conversion['description'] .																'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($click['commission'], $conversion['currency']) .					'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($conversion['orderAmount'], $conversion['currency']) .				'</td>';
		$html	.=	'<td>' . $conversion['IP'] .																		'</td>';
		$html	.=	'<td>' . $conversion['registrationDate'] .															'</td>';
		$html	.=	'<td>' . $conversion['assessmentDate'] .															'</td>';
		$html	.=	'<td>' . $conversion['clickToConversion'] .															'</td>';
		$html	.=	'<td>' . $conversion['originatingClickDate'] .														'</td>';
		$html	.=	'<td>' . $conversion['rejectionReason'] .															'</td>';
		$html	.=	'<td>' . ($conversion['paidOut'] ? 'yes' : 'no') .													'</td>';
		$html	.=	'<td>' . $conversion['affiliateSitesPaidOut'] .														'</td>';
		$html	.=	'<td>' . $conversion['countryCode'] .																'</td>';
		$html	.=	'<td>' . $conversion['attributionModel'] .															'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="affiliatesiteName"	 		data-sortable="true"					>Affiliate Site</th>'
			.	'<th scope="col" data-field="campaignName"				data-sortable="true"					>Campaign</th>'
			.	'<th scope="col" data-field="campaignProduct"	 		data-sortable="true"					>Campaign<br>Product</th>'
			.	'<th scope="col" data-field="reference"					data-sortable="true"					>Reference</th>'
			.	'<th scope="col" data-field="transactionType" 			data-sortable="true"					>Transaction<br>Type</th>'
			.	'<th scope="col" data-field="transactionStatus"			data-sortable="true"					>Transaction<br>Status</th>'
			.	'<th scope="col" data-field="numTouchPointsTotal"		data-sortable="true" data-align="right"	># Total<br>Touchpoints</th>'
			.	'<th scope="col" data-field="numTouchPointsAttributed"	data-sortable="true" data-align="right"	># Attributed<br>Touchpoints</th>'
			.	'<th scope="col" data-field="attributableCommission"	data-sortable="true" data-align="right"	>Attributable<br>Commission</th>'
			.	'<th scope="col" data-field="description"				data-sortable="true"					>Description</th>'
			.	'<th scope="col" data-field="commission"				data-sortable="true" data-align="right"	>Commission</th>'
			.	'<th scope="col" data-field="orderAmount"				data-sortable="true" data-align="right"	>Order<br>Amount</th>'
			.	'<th scope="col" data-field="IP"						data-sortable="true"					>IP</th>'
			.	'<th scope="col" data-field="registrationDate"			data-sortable="true"					>Registration<br>Date</th>'
			.	'<th scope="col" data-field="assessmentDate"			data-sortable="true"					>Assessment<br>Date</th>'
			.	'<th scope="col" data-field="clickToConversion"			data-sortable="true" data-align="right"	>Click to<br>Conversion</th>'
			.	'<th scope="col" data-field="originatingClickDate"		data-sortable="true"					>Originating<br>Click Date</th>'
			.	'<th scope="col" data-field="rejectionReason"			data-sortable="true"					>Rejection<br>Reason</th>'
			.	'<th scope="col" data-field="paidOut"					data-sortable="true"					>Paid Out</th>'
			.	'<th scope="col" data-field="affiliateSitesPaidOut"		data-sortable="true"					>Affiliate Sites<br>Paid Out</th>'
			.	'<th scope="col" data-field="countryCode"				data-sortable="true"					>Country<br>Code</th>'
			.	'<th scope="col" data-field="attributionModel"			data-sortable="true"					>Attribution<br>Model</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

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
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $feed['campaignName'] . '</td>';
		$html	.=	'<td><img src="' . $feed['imageURL'] . '" height="50" alt=""></td>';
		$html	.=	'<td>' . $feed['campaignName'] . ' - ' . $feed['name'] . '</td>';
		$html	.=	'<td><span class="badge text-bg-' . $feed['assignmentStatus'] .  '">' . $feed['assignmentStatus'] .  '</span></td>';
		$html	.=	'<td>' . $feed['updateDate'] . '</td>';
		if (array_key_exists($interval, $intervals)) {
			$html	.=	'<td>' . $intervals[$interval] . '</td>';
		} else {
			$html	.=	'<td>' . $feed['updateInterval'] . '</td>';
		}
		$html	.=	'<td>' . $fmtDEC->format($feed['productCount']) . '</td>';
		$html	.=	'<td>' . $feed['affiliateSites'] . '</td>';
		$html	.=	'<td>'
				.	'<a class="text-nowrap" target="_blank" href="' . $affiliateCampaignURL .  '"><i class="fa-solid fa-eye"></i> view affiliatecampaign</a><br>'
				.	'<a target="_blank" href="' . $affiliateMaterialURL .  '"><i class="fa-solid fa-gears"></i> generate feed URL</a><br>'
				.	'<a target="_blank" href="' . $feed['URL'] . '"><i class="fa-solid fa-download"></i> download feed</a>'
				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="campaign"	data-sortable="true"					>Campaign</th>'
			.	'<th scope="col" data-field="logo"												>Logo</th>'
			.	'<th scope="col" data-field="name"	 	data-sortable="true"					>Feed Name</th>'
			.	'<th scope="col" data-field="status"	data-sortable="true"					>Assignment Status</th>'
			.	'<th scope="col" data-field="date"	 	data-sortable="true"					>Last Updated</th>'
			.	'<th scope="col" data-field="interval" 	data-sortable="true"					>Update Interval</th>'
			.	'<th scope="col" data-field="count"	 	data-sortable="true" data-align="right"	>Product Count</th>'
			.	'<th scope="col" data-field="sites"	 											>Accepted Affiliate Sites</th>'
			.	'<th scope="col" data-field="actions"	 										>Actions</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_payments($dbh, $fmtCUR) {

	$html		=	'';
	
	foreach (dbget_tradetracker_payments($dbh) as $payment) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $payment['billDate'] .													'</td>';
		$html	.=	'<td>' . $payment['payDate'] .													'</td>';
		$html	.=	'<td>' . $payment['invoiceNumber'] .											'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($payment['subTotal'],	$payment['currency']) .	'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($payment['VAT'],		$payment['currency']) .	'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($payment['endTotal'],	$payment['currency']) .	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="billDate"		data-sortable="true"					>Bill Date</th>'
			.	'<th scope="col" data-field="payDate"		data-sortable="true"					>Pay Date</th>'
			.	'<th scope="col" data-field="invoiceNumber"	data-sortable="true"					>Invoice Number</th>'
			.	'<th scope="col" data-field="subTotal"		data-sortable="true" data-align="right"	>Sub Total</th>'
			.	'<th scope="col" data-field="VAT" 			data-sortable="true" data-align="right"	>VAT</th>'
			.	'<th scope="col" data-field="endTotal" 		data-sortable="true" data-align="right"	>End Total</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_report_affiliatesites($dbh, $fmtCUR, $fmtDEC) {

	$html		=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites($dbh) as $report) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $report['affiliateSiteName'] .											'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['impressionCommission'],	'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallClickCount']) .						'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueClickCount']) .							'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['clickCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['leadCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['leadCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['saleCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['saleCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['fixedCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CTR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CLR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CSR']) .										'%</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['totalCommission'],		'EUR') .	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="affiliatesiteName"	 		data-sortable="true"					>Affiliate Site</th>'
			.	'<th scope="col" data-field="overallImpressionCount"	data-sortable="true" data-align="right"	>Overall<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="uniqueImpressionCount"		data-sortable="true" data-align="right"	>Unique<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="impressionCommission"		data-sortable="true" data-align="right"	>Impression<br>Commission</th>'
			.	'<th scope="col" data-field="overallClickCount" 		data-sortable="true" data-align="right"	>Overall<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="uniqueClickCount" 			data-sortable="true" data-align="right"	>Unique<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="clickCommission"			data-sortable="true" data-align="right"	>Click<br>Commission</th>'
			.	'<th scope="col" data-field="leadCount"					data-sortable="true" data-align="right"	>Lead<br>Count</th>'
			.	'<th scope="col" data-field="leadCommission"			data-sortable="true" data-align="right"	>Lead<br>Commission</th>'
			.	'<th scope="col" data-field="saleCount"					data-sortable="true" data-align="right"	>Sale<br>Count</th>'
			.	'<th scope="col" data-field="saleCommission"			data-sortable="true" data-align="right"	>Sale<br>Commission</th>'
			.	'<th scope="col" data-field="fixedCommission"			data-sortable="true" data-align="right"	>Fixed<br>Commission</th>'
			.	'<th scope="col" data-field="CTR"						data-sortable="true" data-align="right"	>CTR</th>'
			.	'<th scope="col" data-field="CLR"						data-sortable="true" data-align="right"	>CLR</th>'
			.	'<th scope="col" data-field="CSR"						data-sortable="true" data-align="right"	>CSR</th>'
			.	'<th scope="col" data-field="eCPM"						data-sortable="true" data-align="right"	>eCPM</th>'
			.	'<th scope="col" data-field="EPC"						data-sortable="true" data-align="right"	>EPC</th>'
			.	'<th scope="col" data-field="totalCommission"			data-sortable="true" data-align="right"	>Total<br>Commission</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_report_campaigns($dbh, $fmtCUR, $fmtDEC) {

	$html		=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites_campaigns($dbh) as $report) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $report['affiliateSiteName'] .											'</td>';
		$html	.=	'<td>' . $report['campaignName'] .												'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['impressionCommission'],	'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallClickCount']) .						'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueClickCount']) .							'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['clickCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['leadCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['leadCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['saleeCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['saleCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['fixedCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CTR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CLR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CSR']) .										'%</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['totalCommission'],		'EUR') .	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="affiliatesiteName"	 		data-sortable="true"					>Affiliate Site</th>'
			.	'<th scope="col" data-field="campaignName"	 			data-sortable="true"					>Campaign</th>'
			.	'<th scope="col" data-field="overallImpressionCount"	data-sortable="true" data-align="right"	>Overall<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="uniqueImpressionCount"		data-sortable="true" data-align="right"	>Unique<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="impressionCommission"		data-sortable="true" data-align="right"	>Impression<br>Commission</th>'
			.	'<th scope="col" data-field="overallClickCount" 		data-sortable="true" data-align="right"	>Overall<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="uniqueClickCount" 			data-sortable="true" data-align="right"	>Unique<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="clickCommission"			data-sortable="true" data-align="right"	>Click<br>Commission</th>'
			.	'<th scope="col" data-field="leadCount"					data-sortable="true" data-align="right"	>Lead<br>Count</th>'
			.	'<th scope="col" data-field="leadCommission"			data-sortable="true" data-align="right"	>Lead<br>Commission</th>'
			.	'<th scope="col" data-field="leadCount"					data-sortable="true" data-align="right"	>Sale<br>Count</th>'
			.	'<th scope="col" data-field="saleCommission"			data-sortable="true" data-align="right"	>Sale<br>Commission</th>'
			.	'<th scope="col" data-field="fixedCommission"			data-sortable="true" data-align="right"	>Fixed<br>Commission</th>'
			.	'<th scope="col" data-field="CTR"						data-sortable="true" data-align="right"	>CTR</th>'
			.	'<th scope="col" data-field="CLR"						data-sortable="true" data-align="right"	>CLR</th>'
			.	'<th scope="col" data-field="CSR"						data-sortable="true" data-align="right"	>CSR</th>'
			.	'<th scope="col" data-field="eCPM"						data-sortable="true" data-align="right"	>eCPM</th>'
			.	'<th scope="col" data-field="EPC"						data-sortable="true" data-align="right"	>EPC</th>'
			.	'<th scope="col" data-field="totalCommission"			data-sortable="true" data-align="right"	>Total<br>Commission</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_report_references($dbh, $fmtCUR, $fmtDEC) {

	$html			=	'';
	
	foreach (dbget_tradetracker_report_affiliatesites_campaigns_references($dbh) as $report) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $report['affiliateSiteName'] .											'</td>';
		$html	.=	'<td>' . $report['campaignName'] .												'</td>';
		$html	.=	'<td>' . $report['reference'] .													'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueImpressionCount']) .					'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['impressionCommission'],	'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['overallClickCount']) .						'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['uniqueClickCount']) .							'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['clickCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['leadCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['leadCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['saleeCount']) .								'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['saleCommission'],			'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['fixedCommission'],		'EUR') .	'</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CTR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CLR']) .										'%</td>';
		$html	.=	'<td>' . $fmtDEC->format($report['CSR']) .										'%</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['eCPM'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['EPC'], 'EUR') .						'</td>';
		$html	.=	'<td>' . $fmtCUR->formatCurrency($report['totalCommission'],		'EUR') .	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort="customSort"
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="affiliatesiteName"	 		data-sortable="true"					>Affiliate Site</th>'
			.	'<th scope="col" data-field="campaignName"	 			data-sortable="true"					>Campaign</th>'
			.	'<th scope="col" data-field="reference"	 				data-sortable="true"					>Reference</th>'
			.	'<th scope="col" data-field="overallImpressionCount"	data-sortable="true" data-align="right"	>Overall<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="uniqueImpressionCount"		data-sortable="true" data-align="right"	>Unique<br>Impression<br>Count</th>'
			.	'<th scope="col" data-field="impressionCommission"		data-sortable="true" data-align="right"	>Impression<br>Commission</th>'
			.	'<th scope="col" data-field="overallClickCount" 		data-sortable="true" data-align="right"	>Overall<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="uniqueClickCount" 			data-sortable="true" data-align="right"	>Unique<br>Click<br>Count</th>'
			.	'<th scope="col" data-field="clickCommission"			data-sortable="true" data-align="right"	>Click<br>Commission</th>'
			.	'<th scope="col" data-field="leadCount"					data-sortable="true" data-align="right"	>Lead<br>Count</th>'
			.	'<th scope="col" data-field="leadCommission"			data-sortable="true" data-align="right"	>Lead<br>Commission</th>'
			.	'<th scope="col" data-field="saleCommission"			data-sortable="true" data-align="right"	>Sale<br>Commission</th>'
			.	'<th scope="col" data-field="fixedCommission"			data-sortable="true" data-align="right"	>Fixed<br>Commission</th>'
			.	'<th scope="col" data-field="CTR"						data-sortable="true" data-align="right"	>CTR</th>'
			.	'<th scope="col" data-field="CLR"						data-sortable="true" data-align="right"	>CLR</th>'
			.	'<th scope="col" data-field="CSR"						data-sortable="true" data-align="right"	>CSR</th>'
			.	'<th scope="col" data-field="eCPM"						data-sortable="true" data-align="right"	>eCPM</th>'
			.	'<th scope="col" data-field="EPC"						data-sortable="true" data-align="right"	>EPC</th>'
			.	'<th scope="col" data-field="totalCommission"			data-sortable="true" data-align="right"	>Total<br>Commission</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
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
		<div class="tab-pane fade show active"	id="sites-tab-pane"			role="tabpanel" aria-labelledby="sites-tab"			tabindex="0">
			<?php	echo $html_affiliatesites; ?>
		</div>
		<div class="tab-pane fade"				id="campaigns-tab-pane"		role="tabpanel" aria-labelledby="campaigns-tab"		tabindex="0">
			<?php	echo $html_campaigns; ?>
		</div>
		<div class="tab-pane fade"				id="clicks-tab-pane"		role="tabpanel" aria-labelledby="clicks-tab"		tabindex="0">
			<?php	echo $html_click_transactions; ?>
		</div>
		<div class="tab-pane fade"				id="conversions-tab-pane"	role="tabpanel" aria-labelledby="conversions-tab"	tabindex="0">
			<?php	echo $html_conversion_transactions; ?>
		</div>
		<div class="tab-pane fade"				id="feeds-tab-pane"			role="tabpanel" aria-labelledby="feeds-tab"			tabindex="0">
			<?php	echo $html_feeds; ?>
		</div>
		<div class="tab-pane fade"				id="news-tab-pane"			role="tabpanel" aria-labelledby="news-tab"			tabindex="0">
			<?php	echo $html_news_items; ?>
		</div>
		<div class="tab-pane fade"				id="payments-tab-pane"		role="tabpanel" aria-labelledby="payments-tab"		tabindex="0">
			<?php	echo $html_payments; ?>
		</div>
		<div class="tab-pane fade"				id="repaff-tab-pane"		role="tabpanel" aria-labelledby="repaff-tab"		tabindex="0">
			<?php	echo $html_report_affiliatesite; ?>
		</div>
		<div class="tab-pane fade"				id="repcam-tab-pane"		role="tabpanel" aria-labelledby="repcam-tab"		tabindex="0">
			<?php	echo $html_report_campaign; ?>
		</div>
		<div class="tab-pane fade"				id="repref-tab-pane"		role="tabpanel" aria-labelledby="repref-tab"		tabindex="0">
			<?php	echo $html_report_reference; ?>
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