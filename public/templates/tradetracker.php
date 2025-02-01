<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TradeTracker Dashboard</title>
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