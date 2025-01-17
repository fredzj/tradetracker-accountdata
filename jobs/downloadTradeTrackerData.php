<?php
/*

	SCRIPT:		downloadTradeTrackerData.php
	
	PURPOSE:	Download data from TradeTracker and insert the data into the database.
	
	Copyright 2024 Fred Onis - All rights reserved.

*/
function getAffiliateSites($dbh, $client, &$affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getAffiliateSites' . PHP_EOL;

	$output_columns	=	['ID', 'name', 'URL', 'type', 'category', 'description', 'creationDate', 'status'];
	$output_table	=	'vendor_tradetracker_affiliatesites';
	$output_values	=	[];

	foreach ($client->getAffiliateSites() as $affiliateSite) {
		
		$affiliateSiteIDs[$affiliateSite->ID]	=	[];
		
		$array		=	[];
		$array[]	=	addslashes($affiliateSite->ID);
		$array[]	=	addslashes($affiliateSite->name);
		$array[]	=	addslashes($affiliateSite->URL);
		$array[]	=	addslashes($affiliateSite->info->type->name);
		$array[]	=	addslashes($affiliateSite->info->category->name);
		$array[]	=	addslashes($affiliateSite->info->description);
		$array[]	=	addslashes($affiliateSite->info->creationDate);
		$array[]	=	addslashes($affiliateSite->info->status);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getAffiliateSiteCategories($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getAffiliateSiteCategories' . PHP_EOL;

	$output_columns	=	['ID', 'name'];
	$output_table	=	'vendor_tradetracker_affiliatesites_categories';
	$output_values	=	[];

	foreach ($client->getAffiliateSiteCategories() as $affiliateSiteCategory) {
		
		$array		=	[];
		$array[]	=	addslashes($affiliateSiteCategory->ID);
		$array[]	=	addslashes($affiliateSiteCategory->name);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getAffiliateSiteTypes($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getAffiliateSiteTypes' . PHP_EOL;

	$output_columns	=	['ID', 'name'];
	$output_table	=	'vendor_tradetracker_affiliatesites_types';
	$output_values	=	[];

	foreach ($client->getAffiliateSiteTypes() as $affiliateSiteType) {
		
		$array		=	[];
		$array[]	=	addslashes($affiliateSiteType->ID);
		$array[]	=	addslashes($affiliateSiteType->name);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getCampaignCategories($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getCampaignCategories' . PHP_EOL;

	$output_columns	=	['ID', 'name', 'parentID'];
	$output_table	=	'vendor_tradetracker_campaigns_categories';
	$output_values	=	[];

	foreach ($client->getCampaignCategories() as $campaignCategory) {
		
		$array		=	[];
		$array[]	=	addslashes($campaignCategory->ID);
		$array[]	=	addslashes($campaignCategory->name);
		$array[]	=	'';
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
		
		foreach ($campaignCategory->categories as $subcategory) {
			
			$array		=	[];
			$array[]	=	addslashes($subcategory->ID);
			$array[]	=	addslashes($subcategory->name);
			$array[]	=	addslashes($campaignCategory->ID);
				
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getCampaignCommissionExtended($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getCampaignCommissionExtended' . PHP_EOL;
	
	$output_columns	=	['affiliatesiteID', 'campaignID', 'impressionCommission', 'clickCommission', 'fixedCommission', 'products'];
	$output_table	=	'vendor_tradetracker_campaigns_commissionextended';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $campaignIDs) {

		foreach ($campaignIDs as $campaignID) {
		
			$commission	=	$client->getCampaignCommissionExtended($affiliateSiteID, $campaignID);
				
			$array		=	[];
			$array[]	=	addslashes($affiliateSiteID);
			$array[]	=	addslashes($campaignID);
			$array[]	=	addslashes($commission->impressionCommission);
			$array[]	=	addslashes($commission->clickCommission);
			$array[]	=	addslashes($commission->fixedCommission);
			
			$products	=	[];
			foreach ($commission->products as $commissionProduct) {
				
				$products[$commissionProduct->campaignProduct->ID]['name']								=	$commissionProduct->campaignProduct->name;
				$products[$commissionProduct->campaignProduct->ID]['leadCommission']					=	$commissionProduct->leadCommission;
				$products[$commissionProduct->campaignProduct->ID]['saleCommissionFixed']				=	$commissionProduct->saleCommissionFixed;
				$products[$commissionProduct->campaignProduct->ID]['saleCommissionVariable']			=	$commissionProduct->saleCommissionVariable;
				$products[$commissionProduct->campaignProduct->ID]['iLeadCommission']					=	$commissionProduct->iLeadCommission;
				$products[$commissionProduct->campaignProduct->ID]['iSaleCommissionFixed']				=	$commissionProduct->iSaleCommissionFixed;
				$products[$commissionProduct->campaignProduct->ID]['iSaleCommissionVariable']			=	$commissionProduct->iSaleCommissionVariable;
				$products[$commissionProduct->campaignProduct->ID]['guaranteedCommissionLead']			=	$commissionProduct->guaranteedCommissionLead;
				$products[$commissionProduct->campaignProduct->ID]['guaranteedCommissionSalesVariable']	=	$commissionProduct->guaranteedCommissionSalesVariable;
				$products[$commissionProduct->campaignProduct->ID]['guaranteedCommissionSalesFixed']	=	$commissionProduct->guaranteedCommissionSalesFixed;
			}
			$array[]	=	addslashes(json_encode($products));
			
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}

	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getCampaignNewsItems($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getCampaignNewsItems' . PHP_EOL;

	$output_columns	=	['id', 'campaignID', 'campaignNewsType', 'title', 'content', 'publishDate', 'expirationDate'];
	$output_table	=	'vendor_tradetracker_campaigns_newsitems';
	$output_values	=	[];

	foreach ($client->getCampaignNewsItems() as $campaignNewsItem) {
		
		$campaignNewsItem	=	json_decode(json_encode($campaignNewsItem), true);
		$array		=	[];
		$array[]	=	addslashes($campaignNewsItem['ID']);
		$array[]	=	addslashes($campaignNewsItem['campaign']['ID']);
		$array[]	=	addslashes($campaignNewsItem['campaignNewsType']);
		$array[]	=	addslashes($campaignNewsItem['title']);
		$array[]	=	addslashes(str_replace("\n", '<br>', $campaignNewsItem['content']));
		$array[]	=	addslashes($campaignNewsItem['publishDate']);
		$array[]	=	addslashes($campaignNewsItem['expirationDate']);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}

	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getCampaigns($dbh, $client, &$affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getCampaigns' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'name', 'URL', 'category', 'subCategories', 'campaignDescription', 'shopDescription', 'targetGroup', 'characteristics', 'imageURL', 'trackingURL', 'impressionCommission', 'clickCommission', 'fixedCommission', 'leadCommission', 'saleCommissionFixed', 'saleCommissionVariable', 'iLeadCommission', 'iSaleCommissionFixed', 'iSaleCommissionVariable', 'assignmentStatus', 'startDate', 'stopDate', 'timeZone', 'clickToConversion', 'policySearchEngineMarketingStatus', 'policyEmailMarketingStatus', 'policyCashbackStatus', 'policyDiscountCodeStatus', 'deeplinkingSupported', 'referencesSupported', 'leadMaximumAssessmentInterval', 'leadAverageAssessmentInterval', 'saleMaximumAssessmentInterval', 'saleAverageAssessmentInterval', 'attributionModelLead', 'attributionModelSales'];
	$output_table	=	'vendor_tradetracker_campaigns';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getCampaigns($affiliateSiteID) as $campaign) {
			
			if ($campaign->info->assignmentStatus == 'accepted') {
				
				$affiliateSiteIDs[$affiliateSiteID][]	=	$campaign->ID;
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($campaign->ID);
				$array[]	=	addslashes($campaign->name);
				$array[]	=	addslashes($campaign->URL);
				$array[]	=	addslashes($campaign->info->category->name);
				$array[]	=	addslashes('');													// $campaign->info->subCategories->0->name;
				$array[]	=	addslashes($campaign->info->campaignDescription);
				$array[]	=	addslashes($campaign->info->shopDescription);
				$array[]	=	addslashes($campaign->info->targetGroup);
				$array[]	=	addslashes($campaign->info->characteristics);
				$array[]	=	addslashes($campaign->info->imageURL);
				$array[]	=	addslashes($campaign->info->trackingURL);
				$array[]	=	addslashes($campaign->info->commission->impressionCommission);
				$array[]	=	addslashes($campaign->info->commission->clickCommission);
				$array[]	=	addslashes($campaign->info->commission->fixedCommission);
				$array[]	=	addslashes($campaign->info->commission->leadCommission);
				$array[]	=	addslashes($campaign->info->commission->saleCommissionFixed);
				$array[]	=	addslashes($campaign->info->commission->saleCommissionVariable);
				$array[]	=	addslashes($campaign->info->commission->iLeadCommission);
				$array[]	=	addslashes($campaign->info->commission->iSaleCommissionFixed);
				$array[]	=	addslashes($campaign->info->commission->iSaleCommissionVariable);
				$array[]	=	addslashes($campaign->info->assignmentStatus);
				$array[]	=	addslashes($campaign->info->startDate);
				$array[]	=	addslashes($campaign->info->stopDate);
				$array[]	=	addslashes($campaign->info->timeZone);
				$array[]	=	addslashes($campaign->info->clickToConversion);
				$array[]	=	addslashes($campaign->info->policySearchEngineMarketingStatus);
				$array[]	=	addslashes($campaign->info->policyEmailMarketingStatus);
				$array[]	=	addslashes($campaign->info->policyCashbackStatus);
				$array[]	=	addslashes($campaign->info->policyDiscountCodeStatus);
				$array[]	=	addslashes($campaign->info->deeplinkingSupported);
				$array[]	=	addslashes($campaign->info->referencesSupported);
				$array[]	=	addslashes($campaign->info->leadMaximumAssessmentInterval);
				$array[]	=	addslashes($campaign->info->leadAverageAssessmentInterval);
				$array[]	=	addslashes($campaign->info->saleMaximumAssessmentInterval);
				$array[]	=	addslashes($campaign->info->saleAverageAssessmentInterval);
				$array[]	=	addslashes($campaign->info->attributionModelLead);
				$array[]	=	addslashes($campaign->info->attributionModelSales);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getClickTransactions($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getClickTransactions' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'clickTransactionID', 'campaignID', 'reference', 'transactionType', 'transactionStatus', 'currency', 'commission', 'registrationDate', 'refererURL', 'paidOut'];
	$output_table	=	'vendor_tradetracker_click_transactions';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getClickTransactions($affiliateSiteID) as $clickTransaction) {
			
			$array		=	[];
			$array[]	=	addslashes($affiliateSiteID);
			$array[]	=	addslashes($clickTransaction->ID);
			$array[]	=	addslashes($clickTransaction->campaign->ID);
			$array[]	=	addslashes($clickTransaction->reference);
			$array[]	=	addslashes($clickTransaction->transactionType);
			$array[]	=	addslashes($clickTransaction->transactionStatus);
			$array[]	=	addslashes($clickTransaction->currency);
			$array[]	=	addslashes($clickTransaction->commission);
			$array[]	=	addslashes($clickTransaction->registrationDate);
			$array[]	=	addslashes($clickTransaction->refererURL);
			$array[]	=	addslashes($clickTransaction->paidOut);

			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}

	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getConversionTransactions($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getConversionTransactions' . PHP_EOL;
	
	$output_columns	=	['affiliatesiteID', 'conversionTransactionID', 'campaignID', 'campaignProduct', 'reference', 'transactionType', 'transactionStatus', 'numTouchPointsTotal', 'numTouchPointsAttributed', 'attributableCommission', 'description', 'currency', 'commission', 'orderAmount', 'IP', 'registrationDate', 'assessmentDate', 'clickToConversion', 'originatingClickDate', 'rejectionReason', 'paidOut', 'affiliateSitesPaidOut', 'countryCode', 'attributionModel'];
	$output_table	=	'vendor_tradetracker_conversion_transactions';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getConversionTransactions($affiliateSiteID) as $conversionTransaction) {
			
			$affiliateSitesPaidOut	=	[];
			if (isset($conversionTransaction->affiliateSitesPaidOut->affiliateSites)) {
				foreach ($conversionTransaction->affiliateSitesPaidOut->affiliateSites as $affiliateSites) {
					$affiliateSitesPaidOut[]	=	$affiliateSites->affiliateSiteID . ':' . $affiliateSites->paidOut;
				}
			}
			
			$array		=	[];
			$array[]	=	addslashes($affiliateSiteID);
			$array[]	=	addslashes($conversionTransaction->ID);
			$array[]	=	addslashes($conversionTransaction->campaign->ID);
			$array[]	=	addslashes($conversionTransaction->campaignProduct->name);
			$array[]	=	addslashes($conversionTransaction->reference);
			$array[]	=	addslashes($conversionTransaction->transactionType);
			$array[]	=	addslashes($conversionTransaction->transactionStatus);
			$array[]	=	addslashes($conversionTransaction->numTouchPointsTotal);
			$array[]	=	addslashes($conversionTransaction->numTouchPointsAttributed);
			$array[]	=	addslashes($conversionTransaction->attributableCommission);
			$array[]	=	addslashes($conversionTransaction->description);
			$array[]	=	addslashes($conversionTransaction->currency);
			$array[]	=	addslashes($conversionTransaction->commission);
			$array[]	=	addslashes($conversionTransaction->orderAmount);
			$array[]	=	addslashes($conversionTransaction->IP);
			$array[]	=	addslashes($conversionTransaction->registrationDate);
			$array[]	=	addslashes($conversionTransaction->assessmentDate);
			$array[]	=	addslashes($conversionTransaction->clickToConversion);
			$array[]	=	addslashes($conversionTransaction->originatingClickDate);
			$array[]	=	addslashes($conversionTransaction->rejectionReason);
			$array[]	=	addslashes($conversionTransaction->paidOut);
			$array[]	=	addslashes(implode('|', $affiliateSitesPaidOut));
			$array[]	=	addslashes($conversionTransaction->countryCode);
			$array[]	=	addslashes($conversionTransaction->attributionModel);

			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}

	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getFeeds($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getFeeds' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'feedID', 'name', 'URL', 'updateDate', 'updateInterval', 'productCount', 'assignmentStatus'];
	$output_table	=	'vendor_tradetracker_feeds';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getFeeds($affiliateSiteID) as $feed) {
			
			if ($feed->assignmentStatus == 'accepted') {
				
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($feed->campaign->ID);
				$array[]	=	addslashes($feed->ID);
				$array[]	=	addslashes($feed->name);
				$array[]	=	addslashes($feed->URL);
				$array[]	=	addslashes($feed->updateDate);
				$array[]	=	addslashes($feed->updateInterval);
				$array[]	=	addslashes($feed->productCount);
				$array[]	=	addslashes($feed->assignmentStatus);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialBannerDimensions($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerDimensions' . PHP_EOL;

	$output_columns	=	['ID', 'width', 'height', 'isCommon', 'isMobile'];
	$output_table	=	'vendor_tradetracker_material_bannerdimensions';
	$output_values	=	[];

	foreach ($client->getMaterialBannerDimensions() as $materialBannerDimension) {
		
		$array		=	[];
		$array[]	=	addslashes($materialBannerDimension->ID);
		$array[]	=	addslashes($materialBannerDimension->width);
		$array[]	=	addslashes($materialBannerDimension->height);
		$array[]	=	addslashes($materialBannerDimension->isCommon);
		$array[]	=	addslashes($materialBannerDimension->isMobile);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialBannerFlashItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerFlashItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_bannerflashitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialBannerFlashItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialBannerImageItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerImageItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_bannerimageitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialBannerImageItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
				//
				//if ($materialItem->campaign->ID == '31985') {
				//	var_dump($materialItem); echo PHP_EOL;
				//}
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialHTMLItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialHTMLItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_htmlitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialHTMLItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				if (isset($materialItem->materialBannerDimension)) {
					$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				} else {
					$array[]	=	'';
				}
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialIncentiveVoucherItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialIncentiveVoucherItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'rss'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_incentivevoucheritems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialIncentiveVoucherItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				if (isset($materialItem->materialBannerDimension)) {
					$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				} else {
					$array[]	=	'';
				}
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialIncentiveOfferItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialIncentiveOfferItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'rss'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_incentiveofferitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialIncentiveOfferItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				if (isset($materialItem->materialBannerDimension)) {
					$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				} else {
					$array[]	=	'';
				}
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getMaterialTextItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialTextItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_textitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialTextItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
				$array		=	[];
				$array[]	=	addslashes($affiliateSiteID);
				$array[]	=	addslashes($materialOutputType);
				$array[]	=	addslashes($materialItem->ID);
				$array[]	=	addslashes($materialItem->campaign->ID);
				$array[]	=	addslashes($materialItem->name);
				$array[]	=	addslashes($materialItem->creationDate);
				$array[]	=	addslashes($materialItem->modificationDate);
				if (isset($materialItem->materialBannerDimension)) {
					$array[]	=	addslashes($materialItem->materialBannerDimension->ID);
				} else {
					$array[]	=	'';
				}
				$array[]	=	addslashes($materialItem->referenceSupported);
				$array[]	=	addslashes($materialItem->description);
				$array[]	=	addslashes($materialItem->conditions);
				$array[]	=	addslashes($materialItem->validFromDate);
				$array[]	=	addslashes($materialItem->validToDate);
				$array[]	=	addslashes($materialItem->discountFixed);
				$array[]	=	addslashes($materialItem->discountVariable);
				$array[]	=	addslashes($materialItem->voucherCode);
				$array[]	=	addslashes($materialItem->code);
				
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getPayments($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getPayments' . PHP_EOL;

	$output_columns	=	['invoiceNumber', 'currency', 'subTotal', 'VAT', 'endTotal', 'billDate', 'payDate'];
	$output_table	=	'vendor_tradetracker_payments';
	$output_values	=	[];

	foreach ($client->getPayments() as $payment) {
		
		$array		=	[];
		$array[]	=	addslashes($payment->invoiceNumber);
		$array[]	=	addslashes($payment->currency);
		$array[]	=	addslashes($payment->subTotal);
		$array[]	=	addslashes($payment->VAT);
		$array[]	=	addslashes($payment->endTotal);
		$array[]	=	addslashes($payment->billDate);
		$array[]	=	addslashes($payment->payDate);
			
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getReportAffiliateSite($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportAffiliateSite' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		$reportData	=	$client->getReportAffiliateSite($affiliateSiteID);
		
		$array		=	[];
		$array[]	=	addslashes($affiliateSiteID);
		$array[]	=	addslashes($reportData->overallImpressionCount);
		$array[]	=	addslashes($reportData->uniqueImpressionCount);
		$array[]	=	addslashes($reportData->impressionCommission);
		$array[]	=	addslashes($reportData->overallClickCount);
		$array[]	=	addslashes($reportData->uniqueClickCount);
		$array[]	=	addslashes($reportData->clickCommission);
		$array[]	=	addslashes($reportData->leadCount);
		$array[]	=	addslashes($reportData->leadCommission);
		$array[]	=	addslashes($reportData->saleCount);
		$array[]	=	addslashes($reportData->saleCommission);
		$array[]	=	addslashes($reportData->fixedCommission);
		$array[]	=	addslashes($reportData->CTR);
		$array[]	=	addslashes($reportData->CLR);
		$array[]	=	addslashes($reportData->CSR);
		$array[]	=	addslashes($reportData->eCPM);
		$array[]	=	addslashes($reportData->EPC);
		$array[]	=	addslashes($reportData->totalCommission);
		
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getReportCampaign($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportCampaign' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite_campaign';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getReportCampaign($affiliateSiteID) as $reportCampaign) {
		
			$array		=	[];
			$array[]	=	addslashes($affiliateSiteID);
			$array[]	=	addslashes($reportCampaign->campaign->ID);
			$array[]	=	addslashes($reportCampaign->reportData->overallImpressionCount);
			$array[]	=	addslashes($reportCampaign->reportData->uniqueImpressionCount);
			$array[]	=	addslashes($reportCampaign->reportData->impressionCommission);
			$array[]	=	addslashes($reportCampaign->reportData->overallClickCount);
			$array[]	=	addslashes($reportCampaign->reportData->uniqueClickCount);
			$array[]	=	addslashes($reportCampaign->reportData->clickCommission);
			$array[]	=	addslashes($reportCampaign->reportData->leadCount);
			$array[]	=	addslashes($reportCampaign->reportData->leadCommission);
			$array[]	=	addslashes($reportCampaign->reportData->saleCount);
			$array[]	=	addslashes($reportCampaign->reportData->saleCommission);
			$array[]	=	addslashes($reportCampaign->reportData->fixedCommission);
			$array[]	=	addslashes($reportCampaign->reportData->CTR);
			$array[]	=	addslashes($reportCampaign->reportData->CLR);
			$array[]	=	addslashes($reportCampaign->reportData->CSR);
			$array[]	=	addslashes($reportCampaign->reportData->eCPM);
			$array[]	=	addslashes($reportCampaign->reportData->EPC);
			$array[]	=	addslashes($reportCampaign->reportData->totalCommission);
			
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

function getReportReference($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportReference' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'reference', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite_campaign_reference';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getReportReference($affiliateSiteID) as $reportReference) {
		
			$array		=	[];
			$array[]	=	addslashes($affiliateSiteID);
			$array[]	=	addslashes($reportReference->campaign->ID);
			$array[]	=	addslashes($reportReference->reference);
			$array[]	=	addslashes($reportReference->reportData->overallImpressionCount);
			$array[]	=	addslashes($reportReference->reportData->uniqueImpressionCount);
			$array[]	=	addslashes($reportReference->reportData->impressionCommission);
			$array[]	=	addslashes($reportReference->reportData->overallClickCount);
			$array[]	=	addslashes($reportReference->reportData->uniqueClickCount);
			$array[]	=	addslashes($reportReference->reportData->clickCommission);
			$array[]	=	addslashes($reportReference->reportData->leadCount);
			$array[]	=	addslashes($reportReference->reportData->leadCommission);
			$array[]	=	addslashes($reportReference->reportData->saleCount);
			$array[]	=	addslashes($reportReference->reportData->saleCommission);
			$array[]	=	addslashes($reportReference->reportData->fixedCommission);
			$array[]	=	addslashes($reportReference->reportData->CTR);
			$array[]	=	addslashes($reportReference->reportData->CLR);
			$array[]	=	addslashes($reportReference->reportData->CSR);
			$array[]	=	addslashes($reportReference->reportData->eCPM);
			$array[]	=	addslashes($reportReference->reportData->EPC);
			$array[]	=	addslashes($reportReference->reportData->totalCommission);
			
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}
	
	dbtruncate(	$dbh, $output_table);
	dbinsert(	$dbh, $output_table, $output_columns, $output_values);
	
	echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
	$output_values	=	[];
	
	return;
}

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	date_default_timezone_set(	'Europe/Amsterdam');
	mb_internal_encoding(		'UTF-8');
	setlocale(LC_ALL,			'nl_NL.utf8');
	$time_start				=	microtime(true);
	$server_domains_root	=	substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));

	# Parse the DB configuration file
	$config_file_name_db	=	$server_domains_root . '/config/db.ini';
	if (($dbconfig			=	parse_ini_file($config_file_name_db,	FALSE, INI_SCANNER_TYPED)) === FALSE) {
		throw new Exception("Parsing file " . $config_file_name_db	. " FAILED");
	}

	# Get the SQL queries
	require $server_domains_root . '/database/sql.inc.php';

	###
	### CUSTOM INIT ROUTINE
	###

	$affiliateSiteIDs	=	[];
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh	=	dbopen($dbconfig);
	$config	=	dbget_config($dbh, 'tradetracker');

	###
	### PROCESSING ROUTINE
	###
	
	# Authentication
	$sandbox	=	false;
	$locale		=	'nl_NL';
	$demo		=	false;
	$client		=	new SoapClient('http://ws.tradetracker.com/soap/affiliate?wsdl', array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
	$client->authenticate($config['customerID'], $config['passphrase'], $sandbox, $locale, $demo);

	# 
	getAffiliateSites(					$dbh, $client, $affiliateSiteIDs);	// Array $affiliateSiteIDs is filled with affiliateSiteIDs
	getAffiliateSiteCategories(			$dbh, $client);
	getAffiliateSiteTypes(				$dbh, $client);
	getCampaignNewsItems(				$dbh, $client);
	getCampaignCategories(				$dbh, $client);
	getCampaigns(						$dbh, $client, $affiliateSiteIDs);	// Array $affiliateSiteIDs is filled with campaignIDs per affiliateSiteID
	getCampaignCommissionExtended(		$dbh, $client, $affiliateSiteIDs);
	getClickTransactions(				$dbh, $client, $affiliateSiteIDs);
	getConversionTransactions(			$dbh, $client, $affiliateSiteIDs);
	getFeeds(							$dbh, $client, $affiliateSiteIDs);
	getMaterialBannerDimensions(		$dbh, $client);
	getMaterialBannerFlashItems(		$dbh, $client, $affiliateSiteIDs);
	getMaterialBannerImageItems(		$dbh, $client, $affiliateSiteIDs);
	getMaterialHTMLItems(				$dbh, $client, $affiliateSiteIDs);
	getMaterialIncentiveVoucherItems(	$dbh, $client, $affiliateSiteIDs);
	getMaterialIncentiveOfferItems(		$dbh, $client, $affiliateSiteIDs);
	getMaterialTextItems(				$dbh, $client, $affiliateSiteIDs);
	getPayments(						$dbh, $client);
	getReportAffiliateSite(				$dbh, $client, $affiliateSiteIDs);
	getReportCampaign(					$dbh, $client, $affiliateSiteIDs);
	getReportReference(					$dbh, $client, $affiliateSiteIDs);

	###
	### DATABASE EXIT ROUTINE
	###
		
	$dbh = null;

	###
	### STANDARD EXCEPTION ROUTINE
	###

} catch (PDOException $e) {
	
	echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . PHP_EOL;
	
} catch (Exception $e) {
	
	echo date("[G:i:s] ") . 'Caught Exception: '    . $e->getMessage() . PHP_EOL;
	
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

	$execution_time	= microtime(true) - $time_start;
	
	($execution_time > 120)	?	$text		= round($execution_time / 60, 2) . " minutes"
							:	$text		= round($execution_time     , 2) . " seconds";
	
	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Memory usage' . PHP_EOL;
	echo date("[G:i:s] ") . '- current usage: '	. round(memory_get_usage()		/ (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . '- peak usage: '	. round(memory_get_peak_usage() / (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Total Execution Time: ' . $text . PHP_EOL;
}