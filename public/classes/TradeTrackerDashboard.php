<?php
class TradeTrackerDashboard {
    private $db;
    private $dbConfigPath;

    public function __construct($dbConfigPath) {
        $this->db;
        $this->dbConfigPath = $dbConfigPath;
		$this->connectDatabase();
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
     * Generates an HTML table displaying affiliate sites from TradeTracker.
     *
     * This function fetches affiliate sites from the database and generates an HTML table
     * to display their details, including site name, status, creation date, type, category,
     * number of campaigns, and actions.
     *
     * @return string The generated HTML table as a string.
     */
    public function getHtmlAffiliateSites(): string {
        $html = '';

        try {
            foreach ($this->getAffiliateSites() as $affiliatesite) {
                $customerSiteURL = 'https://affiliate.tradetracker.com/customerSite/view/ID/' . htmlspecialchars($affiliatesite['affiliatesiteID'], ENT_QUOTES, 'UTF-8');
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        return '<table class="table table-sm" data-custom-sort="customSort"
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
    }
	
	/**
	* Generates an HTML table displaying campaigns from TradeTracker.
	*
	* This function fetches campaigns from the database and generates an HTML table
	* to display their details, including campaign name, status, category, logo, 
	* various commissions, start date, stop date, accepted affiliate sites, and actions.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlCampaigns(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $html		=	'';
		
        try {
            foreach ($this->getCampaigns() as $campaign) {
			
				$affiliateCampaignURL = 'https://affiliate.tradetracker.com/affiliateCampaign/view/ID/' . htmlspecialchars($campaign['campaignID'], ENT_QUOTES, 'UTF-8');
				$affiliateReportURL = 'https://affiliate.tradetracker.com/affiliateReport/campaign/ID/' . htmlspecialchars($campaign['campaignID'], ENT_QUOTES, 'UTF-8') . '?p%5Bgb%5D=-1&sort=&desc=0&limit=&outputType=4&c=&r=&action=&p%5Bt%5D=1&p%5Bt%5D=-1&p%5Bfd%5D=1&p%5Bfm%5D=1&p%5Bfy%5D=2005&p%5Btd%5D=3&p%5Btm%5D=11&p%5Bty%5D=2024'; // TODO: dynamic date
				
				$html .= '<tr>';
				$html .= '<td>' . htmlspecialchars($campaign['name'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td><span class="badge text-bg-' . htmlspecialchars($campaign['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($campaign['assignmentStatus'], ENT_QUOTES, 'UTF-8') . '</span></td>';
				$html .= '<td>' . htmlspecialchars($campaign['category'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td><img src="' . htmlspecialchars($campaign['imageURL'], ENT_QUOTES, 'UTF-8') . '" height="50" alt=""></td>';
		
				$html .= $this->generate_commission_cell($campaign['impressionCommission']);
				$html .= $this->generate_commission_cell($campaign['clickCommission'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['fixedCommission'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['leadCommission'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['saleCommissionFixed'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['saleCommissionVariable'], null, '%');
				$html .= $this->generate_commission_cell($campaign['iLeadCommission'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['iSaleCommissionFixed'], $fmtCUR, "EUR");
				$html .= $this->generate_commission_cell($campaign['iSaleCommissionVariable'], null, '%');
		
				$html .= '<td>' . htmlspecialchars($campaign['startDate'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars(str_replace('0000-00-00', '', $campaign['stopDate']), ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($campaign['affiliateSites'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>'
					. '<a class="text-nowrap" target="_blank" href="' . $affiliateCampaignURL . '"><i class="fa-solid fa-eye"></i> view affiliatecampaign</a><br>'
					. '<a target="_blank" href="' . $affiliateReportURL . '"><i class="fa-solid fa-table"></i> View campaign report</a>'
					. '</td>';
				$html .= '</tr>';
			}
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	/**
	* Generates an HTML table cell for a commission value.
	*
	* @param string $value The commission value.
	* @param NumberFormatter|null $formatter The number formatter for currency formatting.
	* @param string $suffix The suffix to append to the value (e.g., '%').
	* @return string The generated HTML table cell as a string.
	*/
	private function generate_commission_cell(string $value, NumberFormatter $formatter = null, string $suffix = ''): string {
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
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlCampaignNewsItems(): string {
		
		$html		=	'';

		try {
			foreach ($this->getCampaignNewsItems() as $newsitem) {
				
				$html .= '<tr>';
				$html .= '<td>' . htmlspecialchars($newsitem['name'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($newsitem['campaignNewsType'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($newsitem['title'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . $newsitem['content'] . '</td>';
				$html .= '<td>' . htmlspecialchars($newsitem['publishDate'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($newsitem['expirationDate'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '</tr>';
			}
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	public function getHtmlClickTransactions(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $html		=	'';
		
		try {
			foreach ($this->getClickTransactions() as $click) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlConversionTransactions(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $html		=	'';
		
		try {
			foreach ($this->getConversionTransactions() as $conversion) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	/**
	* Generates an HTML table displaying feed data from TradeTracker.
	*
	* This function fetches feed data from the database and generates an HTML table
	* to display their details, including campaign name, logo, feed name, assignment status,
	* last updated date, update interval, product count, accepted affiliate sites, and actions.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlFeeds(): string {
		
        $fmtDEC		=	new NumberFormatter( 'nl_NL', NumberFormatter::DECIMAL );
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
	
		try {
			foreach ($this->getFeeds() as $feed) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	/**
	* Generates an HTML table displaying payment data from TradeTracker.
	*
	* This function fetches payment data from the database and generates an HTML table
	* to display their details, including bill date, pay date, invoice number, sub total,
	* VAT, and end total.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlPayments(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $html		=	'';
		
		try {
			foreach ($this->getPayments() as $payment) {
				
				$html .= '<tr>';
				$html .= '<td>' . htmlspecialchars($payment['billDate'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($payment['payDate'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . htmlspecialchars($payment['invoiceNumber'], ENT_QUOTES, 'UTF-8') . '</td>';
				$html .= '<td>' . $fmtCUR->formatCurrency($payment['subTotal'], $payment['currency']) . '</td>';
				$html .= '<td>' . $fmtCUR->formatCurrency($payment['VAT'], $payment['currency']) . '</td>';
				$html .= '<td>' . $fmtCUR->formatCurrency($payment['endTotal'], $payment['currency']) . '</td>';
				$html .= '</tr>';
			}
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	/**
	* Generates an HTML table displaying report data by affiliate site from TradeTracker.
	*
	* This function fetches report data by affiliate site from the database and generates an HTML table
	* to display their details, including affiliate site name, overall impression count, unique impression count,
	* impression commission, overall click count, unique click count, click commission, lead count, lead commission,
	* sale count, sale commission, fixed commission, CTR, CLR, CSR, eCPM, EPC, and total commission.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlReportAffiliatesites(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $fmtDEC		=	new NumberFormatter( 'nl_NL', NumberFormatter::DECIMAL );
        $html		=	'';
		
		try {
			foreach ($this->getReportAffiliateSite() as $report) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	/**
	* Generates an HTML table displaying report data by campaign from TradeTracker.
	*
	* This function fetches report data by campaign from the database and generates an HTML table
	* to display their details, including affiliate site name, campaign name, overall impression count,
	* unique impression count, impression commission, overall click count, unique click count, click commission,
	* lead count, lead commission, sale count, sale commission, fixed commission, CTR, CLR, CSR, eCPM, EPC, and total commission.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlReportCampaigns(): string {
	
        $fmtCUR		=	new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $fmtDEC		=	new NumberFormatter( 'nl_NL', NumberFormatter::DECIMAL );
        $html		=	'';
		
		try {
			foreach ($this->getReportCampaign() as $report) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlReportReferences(): string {
	
        $fmtCUR = new NumberFormatter( 'nl_NL', NumberFormatter::CURRENCY );
        $fmtDEC = new NumberFormatter( 'nl_NL', NumberFormatter::DECIMAL );
        $html = '';
		
		try {
			foreach ($this->getReportReference() as $report) {
				
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
        } catch (PDOException $e) {
            $html .= '<tr><td colspan="7">Error fetching data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
	
		return '<table class="table table-sm" data-custom-sort="customSort"
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
	}

	private function getAffiliateSites() {
	
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
		
		return	$this->db->query($sql);
	}
	
	private function getCampaigns() {
	
		$sql			=	"
		SELECT			c.*,
						GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ')	AS	affiliateSites
		FROM			vendor_tradetracker_campaigns c
		LEFT JOIN		vendor_tradetracker_affiliatesites s			ON	s.ID				=	c.affiliateSiteID
		WHERE			c.assignmentStatus								<>	'notsignedup'
		GROUP BY		c.name";
		
		return	$this->db->query($sql);
	}
	
	private function getClickTransactions() {
	
		$sql			=	"
		SELECT			s.name											AS	affiliatesiteName,
						c.name											AS	campaignName,
						t.*
		FROM			vendor_tradetracker_affiliatesites s
		JOIN			vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID
		JOIN			vendor_tradetracker_click_transactions t		ON	t.affiliateSiteID	=	s.ID	AND	t.campaignID	=	c.campaignID
		ORDER BY		1, 2";
		
		return	$this->db->query($sql);
	}

	private function getConversionTransactions() {
	
		$sql			=	"
		SELECT			s.name											AS	affiliatesiteName,
						c.name											AS	campaignName,
						t.*
		FROM			vendor_tradetracker_affiliatesites s
		JOIN			vendor_tradetracker_campaigns c					ON	c.affiliateSiteID	=	s.ID
		JOIN			vendor_tradetracker_conversion_transactions t	ON	t.affiliateSiteID	=	s.ID	AND	t.campaignID	=	c.campaignID
		ORDER BY		1, 2";
		
		return	$this->db->query($sql);
	}
	
	private function getFeeds() {
	
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
		
		return	$this->db->query($sql);
	}
	
	private function getCampaignNewsItems() {
	
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
		
		return	$this->db->query($sql);
	}

	private function getPayments() {
	
		$sql			=	"
		SELECT			*
		FROM			vendor_tradetracker_payments
		ORDER BY		billDate	DESC";
		
		return	$this->db->query($sql);
	}
	
	private function getReportAffiliateSite() {
	
		$sql			=	"
		SELECT			s.name															AS	affiliateSiteName,
						r.*
		FROM			vendor_tradetracker_affiliatesites s
		JOIN			vendor_tradetracker_report_affiliatesite r						ON	r.affiliateSiteID	=	s.ID
		ORDER BY		s.name";
		
		return	$this->db->query($sql);
	}
	
	private function getReportCampaign() {
	
		$sql			=	"
		SELECT			s.name															AS	affiliateSiteName,
						c.name															AS	campaignName,
						r.*			
		FROM			vendor_tradetracker_affiliatesites s			
		JOIN			vendor_tradetracker_campaigns c									ON	c.affiliateSiteID	=	s.ID
		JOIN			vendor_tradetracker_report_affiliatesite_campaign r				ON	r.affiliateSiteID	=	s.ID	AND	r.campaignID	=	c.campaignID
		ORDER BY		s.name, c.name";
		
		return	$this->db->query($sql);
	}
	
	private function getReportReference() {
	
		$sql			=	"
		SELECT			s.name															AS	affiliateSiteName,
						c.name															AS	campaignName,
						r.*
		FROM			vendor_tradetracker_affiliatesites s
		JOIN			vendor_tradetracker_campaigns c									ON	c.affiliateSiteID	=	s.ID
		JOIN			vendor_tradetracker_report_affiliatesite_campaign_reference r	ON	r.affiliateSiteID	=	s.ID	AND	r.campaignID	=	c.campaignID
		ORDER BY		s.name, c.name";
		
		return	$this->db->query($sql);
	}
}