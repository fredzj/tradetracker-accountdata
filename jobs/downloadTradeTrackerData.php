<?php
/**
 * SCRIPT: downloadTradeTrackerData.php
 * PURPOSE: Download data from TradeTracker and insert the data into the database.
 * 
 * This script connects to the TradeTracker API to download various types of data, such as
 * affiliate sites, campaigns, transactions, and reports. It processes the retrieved data
 * and inserts it into the corresponding database tables. The script ensures that the data
 * is up-to-date and accurately reflects the current state of the TradeTracker account.
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
 * Logs an error message with a timestamp.
 *
 * @param string $message The error message to log.
 */
function logError($message) {
    echo date("[G:i:s] ") . $message . PHP_EOL;
}

/**
 * Fetches affiliate sites from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array &$affiliateSiteIDs Reference to an array to store affiliate site IDs.
 */
function getAffiliateSites($dbh, $client, &$affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getAffiliateSites' . PHP_EOL;

	$output_columns	=	['ID', 'name', 'URL', 'type', 'category', 'description', 'creationDate', 'status'];
	$output_table	=	'vendor_tradetracker_affiliatesites';
	$output_values	=	[];

	foreach ($client->getAffiliateSites() as $affiliateSite) {
		
		$affiliateSiteIDs[$affiliateSite->ID]	=	[];
		
        $output_values[] = [
            $affiliateSite->ID,
            $affiliateSite->name,
            $affiliateSite->URL,
            $affiliateSite->info->type->name,
            $affiliateSite->info->category->name,
            $affiliateSite->info->description,
            $affiliateSite->info->creationDate,
            $affiliateSite->info->status
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches affiliate site categories from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getAffiliateSiteCategories($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getAffiliateSiteCategories' . PHP_EOL;

	$output_columns	=	['ID', 'name'];
	$output_table	=	'vendor_tradetracker_affiliatesites_categories';
	$output_values	=	[];

	foreach ($client->getAffiliateSiteCategories() as $affiliateSiteCategory) {
		
        $output_values[] = [
            $affiliateSiteCategory->ID,
            $affiliateSiteCategory->name
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches affiliate site types from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getAffiliateSiteTypes($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getAffiliateSiteTypes' . PHP_EOL;

	$output_columns	=	['ID', 'name'];
	$output_table	=	'vendor_tradetracker_affiliatesites_types';
	$output_values	=	[];

	foreach ($client->getAffiliateSiteTypes() as $affiliateSiteType) {
		
        $output_values[] = [
            $affiliateSiteType->ID,
            $affiliateSiteType->name
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches campaign categories from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getCampaignCategories($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getCampaignCategories' . PHP_EOL;

	$output_columns	=	['ID', 'name', 'parentID'];
	$output_table	=	'vendor_tradetracker_campaigns_categories';
	$output_values	=	[];

	foreach ($client->getCampaignCategories() as $campaignCategory) {
		
        $output_values[] = [
            $campaignCategory->ID,
            $campaignCategory->name,
            ''
        ];
		
		foreach ($campaignCategory->categories as $subcategory) {
			
            $output_values[] = [
                $subcategory->ID,
                $subcategory->name,
                $campaignCategory->ID
            ];
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches extended campaign commission data from TradeTracker and inserts it into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getCampaignCommissionExtended($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getCampaignCommissionExtended' . PHP_EOL;
	
	$output_columns	=	['affiliatesiteID', 'campaignID', 'impressionCommission', 'clickCommission', 'fixedCommission', 'products'];
	$output_table	=	'vendor_tradetracker_campaigns_commissionextended';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $campaignIDs) {

		foreach ($campaignIDs as $campaignID) {
		
			$commission	=	$client->getCampaignCommissionExtended($affiliateSiteID, $campaignID);
			
			$products	=	[];
			foreach ($commission->products as $commissionProduct) {
				
                $products[$commissionProduct->campaignProduct->ID] = [
                    'name' => $commissionProduct->campaignProduct->name,
                    'leadCommission' => $commissionProduct->leadCommission,
                    'saleCommissionFixed' => $commissionProduct->saleCommissionFixed,
                    'saleCommissionVariable' => $commissionProduct->saleCommissionVariable,
                    'iLeadCommission' => $commissionProduct->iLeadCommission,
                    'iSaleCommissionFixed' => $commissionProduct->iSaleCommissionFixed,
                    'iSaleCommissionVariable' => $commissionProduct->iSaleCommissionVariable,
                    'guaranteedCommissionLead' => $commissionProduct->guaranteedCommissionLead,
                    'guaranteedCommissionSalesVariable' => $commissionProduct->guaranteedCommissionSalesVariable,
                    'guaranteedCommissionSalesFixed' => $commissionProduct->guaranteedCommissionSalesFixed
                ];
			}
				
            $output_values[] = [
                $affiliateSiteID,
                $campaignID,
                $commission->impressionCommission,
                $commission->clickCommission,
                $commission->fixedCommission,
                json_encode($products)
            ];
 		}
	}

    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches campaign news items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getCampaignNewsItems($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getCampaignNewsItems' . PHP_EOL;

	$output_columns	=	['id', 'campaignID', 'campaignNewsType', 'title', 'content', 'publishDate', 'expirationDate'];
	$output_table	=	'vendor_tradetracker_campaigns_newsitems';
	$output_values	=	[];

	foreach ($client->getCampaignNewsItems() as $campaignNewsItem) {
		
        $output_values[] = [
            $campaignNewsItem->ID,
            $campaignNewsItem->campaign->ID,
            $campaignNewsItem->campaignNewsType,
            $campaignNewsItem->title,
            str_replace("\n", '<br>', $campaignNewsItem->content),
            $campaignNewsItem->publishDate,
            $campaignNewsItem->expirationDate
        ];
	}

    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches campaigns from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array &$affiliateSiteIDs Reference to an array to store affiliate site IDs and their campaign IDs.
 */
function getCampaigns($dbh, $client, &$affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getCampaigns' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'name', 'URL', 'category', 'subCategories', 'campaignDescription', 'shopDescription', 'targetGroup', 'characteristics', 'imageURL', 'trackingURL', 'impressionCommission', 'clickCommission', 'fixedCommission', 'leadCommission', 'saleCommissionFixed', 'saleCommissionVariable', 'iLeadCommission', 'iSaleCommissionFixed', 'iSaleCommissionVariable', 'assignmentStatus', 'startDate', 'stopDate', 'timeZone', 'clickToConversion', 'policySearchEngineMarketingStatus', 'policyEmailMarketingStatus', 'policyCashbackStatus', 'policyDiscountCodeStatus', 'deeplinkingSupported', 'referencesSupported', 'leadMaximumAssessmentInterval', 'leadAverageAssessmentInterval', 'saleMaximumAssessmentInterval', 'saleAverageAssessmentInterval', 'attributionModelLead', 'attributionModelSales'];
	$output_table	=	'vendor_tradetracker_campaigns';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getCampaigns($affiliateSiteID) as $campaign) {
			
			if ($campaign->info->assignmentStatus == 'accepted') {
				
				$affiliateSiteIDs[$affiliateSiteID][]	=	$campaign->ID;
			
                $output_values[] = [
                    $affiliateSiteID,
                    $campaign->ID,
                    $campaign->name,
                    $campaign->URL,
                    $campaign->info->category->name,
                    '', // $campaign->info->subCategories->0->name;
                    $campaign->info->campaignDescription,
                    $campaign->info->shopDescription,
                    $campaign->info->targetGroup,
                    $campaign->info->characteristics,
                    $campaign->info->imageURL,
                    $campaign->info->trackingURL,
                    $campaign->info->commission->impressionCommission,
                    $campaign->info->commission->clickCommission,
                    $campaign->info->commission->fixedCommission,
                    $campaign->info->commission->leadCommission,
                    $campaign->info->commission->saleCommissionFixed,
                    $campaign->info->commission->saleCommissionVariable,
                    $campaign->info->commission->iLeadCommission,
                    $campaign->info->commission->iSaleCommissionFixed,
                    $campaign->info->commission->iSaleCommissionVariable,
                    $campaign->info->assignmentStatus,
                    $campaign->info->startDate,
                    $campaign->info->stopDate,
                    $campaign->info->timeZone,
                    $campaign->info->clickToConversion,
                    $campaign->info->policySearchEngineMarketingStatus,
                    $campaign->info->policyEmailMarketingStatus,
                    $campaign->info->policyCashbackStatus,
                    $campaign->info->policyDiscountCodeStatus,
                    $campaign->info->deeplinkingSupported,
                    $campaign->info->referencesSupported,
                    $campaign->info->leadMaximumAssessmentInterval,
                    $campaign->info->leadAverageAssessmentInterval,
                    $campaign->info->saleMaximumAssessmentInterval,
                    $campaign->info->saleAverageAssessmentInterval,
                    $campaign->info->attributionModelLead,
                    $campaign->info->attributionModelSales
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches click transactions from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getClickTransactions($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getClickTransactions' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'clickTransactionID', 'campaignID', 'reference', 'transactionType', 'transactionStatus', 'currency', 'commission', 'registrationDate', 'refererURL', 'paidOut'];
	$output_table	=	'vendor_tradetracker_click_transactions';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getClickTransactions($affiliateSiteID) as $clickTransaction) {
			
            $output_values[] = [
                $affiliateSiteID,
                $clickTransaction->ID,
                $clickTransaction->campaign->ID,
                $clickTransaction->reference,
                $clickTransaction->transactionType,
                $clickTransaction->transactionStatus,
                $clickTransaction->currency,
                $clickTransaction->commission,
                $clickTransaction->registrationDate,
                $clickTransaction->refererURL,
                $clickTransaction->paidOut
            ];
		}
	}

    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches conversion transactions from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
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
			
            $output_values[] = [
                $affiliateSiteID,
                $conversionTransaction->ID,
                $conversionTransaction->campaign->ID,
                $conversionTransaction->campaignProduct->name,
                $conversionTransaction->reference,
                $conversionTransaction->transactionType,
                $conversionTransaction->transactionStatus,
                $conversionTransaction->numTouchPointsTotal,
                $conversionTransaction->numTouchPointsAttributed,
                $conversionTransaction->attributableCommission,
                $conversionTransaction->description,
                $conversionTransaction->currency,
                $conversionTransaction->commission,
                $conversionTransaction->orderAmount,
                $conversionTransaction->IP,
                $conversionTransaction->registrationDate,
                $conversionTransaction->assessmentDate,
                $conversionTransaction->clickToConversion,
                $conversionTransaction->originatingClickDate,
                $conversionTransaction->rejectionReason,
                $conversionTransaction->paidOut,
                implode('|', $affiliateSitesPaidOut),
                $conversionTransaction->countryCode,
                $conversionTransaction->attributionModel
            ];
		}
	}

    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches feeds from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getFeeds($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getFeeds' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'feedID', 'name', 'URL', 'updateDate', 'updateInterval', 'productCount', 'assignmentStatus'];
	$output_table	=	'vendor_tradetracker_feeds';
	$output_values	=	[];

	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getFeeds($affiliateSiteID) as $feed) {
			
			if ($feed->assignmentStatus == 'accepted') {
				
                $output_values[] = [
                    $affiliateSiteID,
                    $feed->campaign->ID,
                    $feed->ID,
                    $feed->name,
                    $feed->URL,
                    $feed->updateDate,
                    $feed->updateInterval,
                    $feed->productCount,
                    $feed->assignmentStatus
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material banner dimensions from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getMaterialBannerDimensions($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerDimensions' . PHP_EOL;

	$output_columns	=	['ID', 'width', 'height', 'isCommon', 'isMobile'];
	$output_table	=	'vendor_tradetracker_material_bannerdimensions';
	$output_values	=	[];

	foreach ($client->getMaterialBannerDimensions() as $materialBannerDimension) {
		
        $output_values[] = [
            $materialBannerDimension->ID,
            $materialBannerDimension->width,
            $materialBannerDimension->height,
            $materialBannerDimension->isCommon,
            $materialBannerDimension->isMobile
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material banner flash items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialBannerFlashItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerFlashItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_bannerflashitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialBannerFlashItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    $materialItem->materialBannerDimension->ID,
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material banner image items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialBannerImageItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialBannerImageItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_bannerimageitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialBannerImageItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    $materialItem->materialBannerDimension->ID,
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material HTML items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialHTMLItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialHTMLItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_htmlitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialHTMLItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    isset($materialItem->materialBannerDimension) ? $materialItem->materialBannerDimension->ID : '',
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material incentive voucher items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialIncentiveVoucherItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialIncentiveVoucherItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'rss'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_incentivevoucheritems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialIncentiveVoucherItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    isset($materialItem->materialBannerDimension) ? $materialItem->materialBannerDimension->ID : '',
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material incentive offer items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialIncentiveOfferItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialIncentiveOfferItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript', 'rss'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_incentiveofferitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialIncentiveOfferItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    isset($materialItem->materialBannerDimension) ? $materialItem->materialBannerDimension->ID : '',
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches material text items from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getMaterialTextItems($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getMaterialTextItems' . PHP_EOL;
	
	$materialOutputTypes	=	['html', 'javascript'];
	
	$output_columns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
	$output_table	=	'vendor_tradetracker_material_textitems';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($materialOutputTypes as $materialOutputType) {
		
			foreach ($client->getMaterialTextItems($affiliateSiteID, $materialOutputType) as $materialItem) {
			
                $output_values[] = [
                    $affiliateSiteID,
                    $materialOutputType,
                    $materialItem->ID,
                    $materialItem->campaign->ID,
                    $materialItem->name,
                    $materialItem->creationDate,
                    $materialItem->modificationDate,
                    isset($materialItem->materialBannerDimension) ? $materialItem->materialBannerDimension->ID : '',
                    $materialItem->referenceSupported,
                    $materialItem->description,
                    $materialItem->conditions,
                    $materialItem->validFromDate,
                    $materialItem->validToDate,
                    $materialItem->discountFixed,
                    $materialItem->discountVariable,
                    $materialItem->voucherCode,
                    $materialItem->code
                ];
			}
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches payments from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 */
function getPayments($dbh, $client) {
	
	echo date("[G:i:s] ") . 'getPayments' . PHP_EOL;

	$output_columns	=	['invoiceNumber', 'currency', 'subTotal', 'VAT', 'endTotal', 'billDate', 'payDate'];
	$output_table	=	'vendor_tradetracker_payments';
	$output_values	=	[];

	foreach ($client->getPayments() as $payment) {
		
        $output_values[] = [
            $payment->invoiceNumber,
            $payment->currency,
            $payment->subTotal,
            $payment->VAT,
            $payment->endTotal,
            $payment->billDate,
            $payment->payDate
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches report data per affiliate site from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getReportAffiliateSite($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportAffiliateSite' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		$reportData	=	$client->getReportAffiliateSite($affiliateSiteID);
		
        $output_values[] = [
            $affiliateSiteID,
            $reportData->overallImpressionCount,
            $reportData->uniqueImpressionCount,
            $reportData->impressionCommission,
            $reportData->overallClickCount,
            $reportData->uniqueClickCount,
            $reportData->clickCommission,
            $reportData->leadCount,
            $reportData->leadCommission,
            $reportData->saleCount,
            $reportData->saleCommission,
            $reportData->fixedCommission,
            $reportData->CTR,
            $reportData->CLR,
            $reportData->CSR,
            $reportData->eCPM,
            $reportData->EPC,
            $reportData->totalCommission
        ];
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches report data per campaign from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getReportCampaign($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportCampaign' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite_campaign';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getReportCampaign($affiliateSiteID) as $reportCampaign) {
		
			$output_values[] = [
				$affiliateSiteID,
				$reportCampaign->campaign->ID,
				$reportCampaign->reportData->overallImpressionCount,
				$reportCampaign->reportData->uniqueImpressionCount,
				$reportCampaign->reportData->impressionCommission,
				$reportCampaign->reportData->overallClickCount,
				$reportCampaign->reportData->uniqueClickCount,
				$reportCampaign->reportData->clickCommission,
				$reportCampaign->reportData->leadCount,
				$reportCampaign->reportData->leadCommission,
				$reportCampaign->reportData->saleCount,
				$reportCampaign->reportData->saleCommission,
				$reportCampaign->reportData->fixedCommission,
				$reportCampaign->reportData->CTR,
				$reportCampaign->reportData->CLR,
				$reportCampaign->reportData->CSR,
				$reportCampaign->reportData->eCPM,
				$reportCampaign->reportData->EPC,
				$reportCampaign->reportData->totalCommission
			];
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
        logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

/**
 * Fetches report data per reference from TradeTracker and inserts them into the database.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SoapClient $client The TradeTracker SOAP client.
 * @param array $affiliateSiteIDs An array of affiliate site IDs.
 */
function getReportReference($dbh, $client, $affiliateSiteIDs) {
	
	echo date("[G:i:s] ") . 'getReportReference' . PHP_EOL;
	
	$output_columns	=	['affiliateSiteID', 'campaignID', 'reference', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
	$output_table	=	'vendor_tradetracker_report_affiliatesite_campaign_reference';
	$output_values	=	[];
	
	foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
		
		foreach ($client->getReportReference($affiliateSiteID) as $reportReference) {
		
			$output_values[] = [
				$affiliateSiteID,
				$reportReference->campaign->ID,
				$reportReference->reference,
				$reportReference->reportData->overallImpressionCount,
				$reportReference->reportData->uniqueImpressionCount,
				$reportReference->reportData->impressionCommission,
				$reportReference->reportData->overallClickCount,
				$reportReference->reportData->uniqueClickCount,
				$reportReference->reportData->clickCommission,
				$reportReference->reportData->leadCount,
				$reportReference->reportData->leadCommission,
				$reportReference->reportData->saleCount,
				$reportReference->reportData->saleCommission,
				$reportReference->reportData->fixedCommission,
				$reportReference->reportData->CTR,
				$reportReference->reportData->CLR,
				$reportReference->reportData->CSR,
				$reportReference->reportData->eCPM,
				$reportReference->reportData->EPC,
				$reportReference->reportData->totalCommission
			];
		}
	}
	
    try {
        dbtruncate($dbh, $output_table);
        dbinsert($dbh, $output_table, $output_columns, $output_values);
        echo date("[G:i:s] ") . '- ' . count($output_values) . ' rows inserted' . PHP_EOL;
    } catch (Exception $e) {
		logError('Caught Exception: '    . $e->getMessage());
    }
	
	return;
}

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	require 'includes/init.inc.php';
	require 'includes/vendor_tradetracker_sql.inc.php';

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
	logError('Caught PDOException: ' . $e->getMessage());
} catch (Exception $e) {
	logError('Caught Exception: '    . $e->getMessage());
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

	require 'includes/exit.inc.php';
}