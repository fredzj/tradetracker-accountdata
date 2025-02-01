<?php
/**
 * Class TradeTrackerDataImporter
 *
 * This class is responsible for importing data from the TradeTracker API into the database.
 * It handles various types of data, such as affiliate sites, campaigns, transactions, and reports.
 * The class ensures that the data is up-to-date and accurately reflects the current state of the TradeTracker account.
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
class TradeTrackerDataImporter {
    private $db;
	private $dbConfigPath;
    private $client;
    private $config;
    private $inputUrl;
    private $timeStart;

    public function __construct($dbConfigPath, $inputUrl) {
        $this->db;
		$this->dbConfigPath = $dbConfigPath;
        $this->inputUrl = $inputUrl;
        $this->registerExitHandler();
		$this->connectDatabase();
        $this->getConfig();
        $this->authenticate();
    }

    /**
     * Register the exit handler.
     *
     * @return void
     */
    private function registerExitHandler(): void {
        $this->timeStart = microtime(true);
        register_shutdown_function([new ExitHandler($this->timeStart), 'handleExit']);
    }

    /**
     * Retrieves the TradeTracker configuration from the database.
     *
     * This method fetches the configuration from the 'config' table where the name is 'tradetracker',
     * decodes the JSON configuration, and stores it in the $config property.
     *
     * @return void
     */
    private function getConfig(): void {
        $sql = "SELECT configuration FROM config WHERE name = 'tradetracker'";
        $rows = $this->db->select($sql);
    
        if (empty($rows)) {
            $this->logMessage('Error: Configuration for TradeTracker not found.');
            return;
        }

        $this->config = json_decode($rows[0]['configuration'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logMessage('Error: Failed to decode JSON configuration - ' . json_last_error_msg());
            $this->config = [];
        }
    }

	/**
	 * Connects to the database using the configuration file.
	 *
	 * This method reads the database configuration from the specified INI file,
	 * parses the configuration, and establishes a connection to the database.
	 * If the configuration file cannot be parsed, an exception is thrown.
	 *
	 * @throws Exception If the configuration file cannot be parsed.
	 * @return void
	 */
	private function connectDatabase() {
		if (($dbConfig = parse_ini_file($this->dbConfigPath, FALSE, INI_SCANNER_TYPED)) === FALSE) {
			throw new Exception("Parsing file " . $this->dbConfigPath	. " FAILED");
		}
		$this->db = new Database($dbConfig);
		unset($dbConfig);
	}

    /**
     * Authenticates the client with the TradeTracker API using SOAP.
     *
     * This method sets up the SOAP client and authenticates using the provided configuration.
     *
     * @return void
     */
    private function authenticate(): void {
        try {
            $this->client = new SoapClient($this->inputUrl, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
            $this->client->authenticate($this->config['customerID'], $this->config['passphrase'], $this->config['sandbox'], $this->config['locale'], $this->config['demo']);
            $this->logMessage('Successfully authenticated with TradeTracker API.');
        } catch (SoapFault $e) {
            $this->logMessage('SOAP Error: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

    /**
     * Download and process data from TradeTracker.
     *
     * @return void
     */
    public function import(): void {
        $affiliateSiteIDs = [];
        $this->getAffiliateSites($affiliateSiteIDs);
        $this->getAffiliateSiteCategories();
        $this->getAffiliateSiteTypes();
        $this->getCampaignNewsItems();
        $this->getCampaignCategories();
        $this->getCampaigns($affiliateSiteIDs);
        $this->getCampaignCommissionExtended($affiliateSiteIDs);
        $this->getClickTransactions($affiliateSiteIDs);
        $this->getConversionTransactions($affiliateSiteIDs);
        $this->getFeeds($affiliateSiteIDs);
        $this->getMaterialBannerDimensions();
        $this->getMaterialBannerFlashItems($affiliateSiteIDs);
        $this->getMaterialBannerImageItems($affiliateSiteIDs);
        $this->getMaterialHTMLItems($affiliateSiteIDs);
        $this->getMaterialIncentiveVoucherItems($affiliateSiteIDs);
        $this->getMaterialIncentiveOfferItems($affiliateSiteIDs);
        $this->getMaterialTextItems($affiliateSiteIDs);
        $this->getPayments();
        $this->getReportAffiliateSite($affiliateSiteIDs);
        $this->getReportCampaign($affiliateSiteIDs);
        $this->getReportReference($affiliateSiteIDs);
    }

    /**
     * Fetches affiliate sites from TradeTracker and inserts them into the database.
     *
     * @param array &$affiliateSiteIDs Reference to an array to store affiliate site IDs.
     *
     * @return void
     */
    private function getAffiliateSites(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getAffiliateSites');

        $outputColumns	=	['ID', 'name', 'URL', 'type', 'category', 'description', 'creationDate', 'status'];
        $outputTable	=	'vendor_tradetracker_affiliatesites';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getAffiliateSites() as $affiliateSite) {
				
				$affiliateSiteIDs[$affiliateSite->ID]	=	[];
				
				$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches affiliate site categories from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getAffiliateSiteCategories(): void {
	
        $this->logMessage('getAffiliateSiteCategories');

        $outputColumns	=	['ID', 'name'];
        $outputTable	=	'vendor_tradetracker_affiliatesites_categories';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getAffiliateSiteCategories() as $affiliateSiteCategory) {
				
				$outputValues[] = [
					$affiliateSiteCategory->ID,
					$affiliateSiteCategory->name
				];
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches affiliate site types from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getAffiliateSiteTypes(): void {
 	
        $this->logMessage('getAffiliateSiteTypes');

        $outputColumns	=	['ID', 'name'];
        $outputTable	=	'vendor_tradetracker_affiliatesites_types';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getAffiliateSiteTypes() as $affiliateSiteType) {
				
				$outputValues[] = [
					$affiliateSiteType->ID,
					$affiliateSiteType->name
				];
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches campaign news items from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getCampaignNewsItems(): void {
	
        $this->logMessage('getCampaignNewsItems');

        $outputColumns	=	['id', 'campaignID', 'campaignNewsType', 'title', 'content', 'publishDate', 'expirationDate'];
        $outputTable	=	'vendor_tradetracker_campaigns_newsitems';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getCampaignNewsItems() as $campaignNewsItem) {
				
				$outputValues[] = [
					$campaignNewsItem->ID,
					$campaignNewsItem->campaign->ID,
					$campaignNewsItem->campaignNewsType,
					$campaignNewsItem->title,
					str_replace("\n", '<br>', $campaignNewsItem->content),
					$campaignNewsItem->publishDate,
					$campaignNewsItem->expirationDate
				];
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches campaign categories from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getCampaignCategories(): void {
	
        $this->logMessage('getCampaignCategories');

        $outputColumns	=	['ID', 'name', 'parentID'];
        $outputTable	=	'vendor_tradetracker_campaigns_categories';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getCampaignCategories() as $campaignCategory) {
				
				$outputValues[] = [
					$campaignCategory->ID,
					$campaignCategory->name,
					''
				];
				
				foreach ($campaignCategory->categories as $subcategory) {
					
					$outputValues[] = [
						$subcategory->ID,
						$subcategory->name,
						$campaignCategory->ID
					];
				}
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches campaigns from TradeTracker and inserts them into the database.
	 *
	 * @param array &$affiliateSiteIDs Reference to an array to store affiliate site IDs and their campaign IDs.
     *
     * @return void
	 */
    private function getCampaigns(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getCampaigns');
	
        $outputColumns	=	['affiliateSiteID', 'campaignID', 'name', 'URL', 'category', 'subCategories', 'campaignDescription', 'shopDescription', 'targetGroup', 'characteristics', 'imageURL', 'trackingURL', 'impressionCommission', 'clickCommission', 'fixedCommission', 'leadCommission', 'saleCommissionFixed', 'saleCommissionVariable', 'iLeadCommission', 'iSaleCommissionFixed', 'iSaleCommissionVariable', 'assignmentStatus', 'startDate', 'stopDate', 'timeZone', 'clickToConversion', 'policySearchEngineMarketingStatus', 'policyEmailMarketingStatus', 'policyCashbackStatus', 'policyDiscountCodeStatus', 'deeplinkingSupported', 'referencesSupported', 'leadMaximumAssessmentInterval', 'leadAverageAssessmentInterval', 'saleMaximumAssessmentInterval', 'saleAverageAssessmentInterval', 'attributionModelLead', 'attributionModelSales'];
        $outputTable	=	'vendor_tradetracker_campaigns';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($this->client->getCampaigns($affiliateSiteID) as $campaign) {
					
					if ($campaign->info->assignmentStatus == 'accepted') {
						
						$affiliateSiteIDs[$affiliateSiteID][]	=	$campaign->ID;
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches extended campaign commission data from TradeTracker and inserts it into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getCampaignCommissionExtended(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getCampaignCommissionExtended');
	
        $outputColumns	=	['affiliatesiteID', 'campaignID', 'impressionCommission', 'clickCommission', 'fixedCommission', 'products'];
        $outputTable	=	'vendor_tradetracker_campaigns_commissionextended';
        $outputValues	=	[];
    
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $campaignIDs) {
				foreach ($campaignIDs as $campaignID) {
				
					$commission	=	$this->client->getCampaignCommissionExtended($affiliateSiteID, $campaignID);
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
						
					$outputValues[] = [
						$affiliateSiteID,
						$campaignID,
						$commission->impressionCommission,
						$commission->clickCommission,
						$commission->fixedCommission,
						json_encode($products)
					];
				}
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches click transactions from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getClickTransactions(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getClickTransactions');
	
        $outputColumns	=	['affiliateSiteID', 'clickTransactionID', 'campaignID', 'reference', 'transactionType', 'transactionStatus', 'currency', 'commission', 'registrationDate', 'refererURL', 'paidOut'];
        $outputTable	=	'vendor_tradetracker_click_transactions';
        $outputValues	=	[];
    
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($this->client->getClickTransactions($affiliateSiteID) as $clickTransaction) {
					
					$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches conversion transactions from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getConversionTransactions(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getConversionTransactions');
	
        $outputColumns	=	['affiliatesiteID', 'conversionTransactionID', 'campaignID', 'campaignProduct', 'reference', 'transactionType', 'transactionStatus', 'numTouchPointsTotal', 'numTouchPointsAttributed', 'attributableCommission', 'description', 'currency', 'commission', 'orderAmount', 'IP', 'registrationDate', 'assessmentDate', 'clickToConversion', 'originatingClickDate', 'rejectionReason', 'paidOut', 'affiliateSitesPaidOut', 'countryCode', 'attributionModel'];
        $outputTable	=	'vendor_tradetracker_conversion_transactions';
        $outputValues	=	[];
    
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($this->client->getConversionTransactions($affiliateSiteID) as $conversionTransaction) {
					
					$affiliateSitesPaidOut	=	[];
					if (isset($conversionTransaction->affiliateSitesPaidOut->affiliateSites)) {
						foreach ($conversionTransaction->affiliateSitesPaidOut->affiliateSites as $affiliateSites) {
							$affiliateSitesPaidOut[]	=	$affiliateSites->affiliateSiteID . ':' . $affiliateSites->paidOut;
						}
					}
					
					$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches feeds from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getFeeds(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getFeeds');
	
        $outputColumns	=	['affiliateSiteID', 'campaignID', 'feedID', 'name', 'URL', 'updateDate', 'updateInterval', 'productCount', 'assignmentStatus'];
        $outputTable	=	'vendor_tradetracker_feeds';
        $outputValues	=	[];
    
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($this->client->getFeeds($affiliateSiteID) as $feed) {
					
					if ($feed->assignmentStatus == 'accepted') {
						
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material banner dimensions from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getMaterialBannerDimensions(): void {
	
        $this->logMessage('getMaterialBannerDimensions');

        $outputColumns	=	['ID', 'width', 'height', 'isCommon', 'isMobile'];
        $outputTable	=	'vendor_tradetracker_material_bannerdimensions';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getMaterialBannerDimensions() as $materialBannerDimension) {
				
				$outputValues[] = [
					$materialBannerDimension->ID,
					$materialBannerDimension->width,
					$materialBannerDimension->height,
					$materialBannerDimension->isCommon,
					$materialBannerDimension->isMobile
				];
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material banner flash items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialBannerFlashItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialBannerFlashItems');
	
        $materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_bannerflashitems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialBannerFlashItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material banner image items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialBannerImageItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialBannerImageItems');
	
        $materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_bannerimageitems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialBannerImageItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material HTML items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialHTMLItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialHTMLItems');
	
        $materialOutputTypes	=	['html', 'javascript', 'iframe', 'popup', 'popunder'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_htmlitems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialHTMLItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material incentive voucher items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialIncentiveVoucherItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialIncentiveVoucherItems');
	
        $materialOutputTypes	=	['html', 'javascript', 'rss'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_incentivevoucheritems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialIncentiveVoucherItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material incentive offer items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialIncentiveOfferItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialIncentiveOfferItems');
	
        $materialOutputTypes	=	['html', 'javascript', 'rss'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_incentiveofferitems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialIncentiveOfferItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches material text items from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getMaterialTextItems(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getMaterialTextItems');
	
        $materialOutputTypes	=	['html', 'javascript'];
        
        $outputColumns	=	['affiliateSiteID', 'materialOutputType', 'materialItemID', 'campaignID', 'name', 'creationDate', 'modificationDate', 'materialBannerDimensionID', 'referenceSupported', 'description', 'conditions', 'validFromDate', 'validToDate', 'discountFixed', 'discountVariable', 'voucherCode', 'code'];
        $outputTable	=	'vendor_tradetracker_material_textitems';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($materialOutputTypes as $materialOutputType) {
					foreach ($this->client->getMaterialTextItems($affiliateSiteID, $materialOutputType) as $materialItem) {
					
						$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches payments from TradeTracker and inserts them into the database.
     *
     * @return void
	 */
    private function getPayments(): void {
	
        $this->logMessage('getPayments');

        $outputColumns	=	['invoiceNumber', 'currency', 'subTotal', 'VAT', 'endTotal', 'billDate', 'payDate'];
        $outputTable	=	'vendor_tradetracker_payments';
        $outputValues	=	[];
    
		try {
			foreach ($this->client->getPayments() as $payment) {
				
				$outputValues[] = [
					$payment->invoiceNumber,
					$payment->currency,
					$payment->subTotal,
					$payment->VAT,
					$payment->endTotal,
					$payment->billDate,
					$payment->payDate
				];
			}
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches report data per affiliate site from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getReportAffiliateSite(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getReportAffiliateSite');
	
        $outputColumns	=	['affiliateSiteID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
        $outputTable	=	'vendor_tradetracker_report_affiliatesite';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				
				$reportData	=	$this->client->getReportAffiliateSite($affiliateSiteID);
				
				$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

	/**
	 * Fetches report data per campaign from TradeTracker and inserts them into the database.
	 *
	 * @param array $affiliateSiteIDs An array of affiliate site IDs.
     *
     * @return void
	 */
    private function getReportCampaign(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getReportCampaign');
	
        $outputColumns	=	['affiliateSiteID', 'campaignID', 'overallImpressionCount', 'uniqueImpressionCount', 'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 'EPC', 'totalCommission'];
        $outputTable	=	'vendor_tradetracker_report_affiliatesite_campaign';
        $outputValues	=	[];
        
		try {
			foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
				foreach ($this->client->getReportCampaign($affiliateSiteID) as $reportCampaign) {
				
					$outputValues[] = [
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
			$this->db->truncate($outputTable);
			$this->db->dbinsert($outputTable, $outputColumns, $outputValues);
			$this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

    /**
     * Fetches report reference data from TradeTracker and inserts it into the database.
     *
     * This method retrieves report reference data for each affiliate site ID from the TradeTracker API.
     * It processes the retrieved data and inserts it into the specified database table.
     *
     * @param array $affiliateSiteIDs An array of affiliate site IDs to fetch report references for.
     * @return void
     */
    private function getReportReference(array &$affiliateSiteIDs): void {
	
        $this->logMessage('getReportReference');
	
        $outputColumns  = [
            'affiliateSiteID', 'campaignID', 'reference', 'overallImpressionCount', 'uniqueImpressionCount', 
            'impressionCommission', 'overallClickCount', 'uniqueClickCount', 'clickCommission', 'leadCount', 
            'leadCommission', 'saleCount', 'saleCommission', 'fixedCommission', 'CTR', 'CLR', 'CSR', 'eCPM', 
            'EPC', 'totalCommission'
        ];
        $outputTable	=	'vendor_tradetracker_report_affiliatesite_campaign_reference';
        $outputValues	=	[];
        
        try {
            foreach ($affiliateSiteIDs as $affiliateSiteID => $dummy) {
                $reportReferences = $this->client->getReportReference($affiliateSiteID);
                $outputValues = array_merge($outputValues, $this->processReportReferences($affiliateSiteID, $reportReferences));
            }

            $this->db->truncate($outputTable);
            $this->db->dbinsert($outputTable, $outputColumns, $outputValues);
            $this->logMessage('- ' . count($outputValues) . ' rows inserted');

        } catch (Exception $e) {
            $this->logMessage('Error: ' . $e->getMessage());
        }
    }

    /**
     * Processes report reference data for a given affiliate site ID.
     *
     * This method processes the report reference data retrieved from the TradeTracker API
     * and formats it into an array suitable for database insertion.
     *
     * @param int $affiliateSiteID The affiliate site ID for which the report references are being processed.
     * @param array $reportReferences An array of report reference objects retrieved from the TradeTracker API.
     * @return array An array of processed report reference data ready for database insertion.
     */
    private function processReportReferences(int $affiliateSiteID, array $reportReferences): array {

        $outputValues = [];
    
        foreach ($reportReferences as $reportReference) {
            $outputValues[] = [
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
    
        return $outputValues;
    }
    /**
     * Logs a message with a timestamp.
     *
     * @param string $message The message to log.
     *
     * @return string
     */
    private function logMessage($message) {
        echo date("[G:i:s] ") . $message . PHP_EOL;
    }
}