# tradetrackerdashboard

## Save Database Credentials
1. Enter your hostname, databasename, username, and password in file /config/db.ini.

## Create Database Tables
2. Import all /database/*.sql files into your database.

## Save TradeTracker API Credentials
3. Create a new row in the 'config' table where column 'name' contains the value "tradetracker" and column 'configuration' contains your [TradeTracker API](https://affiliate.tradetracker.com/webService) credentials (formatted as "{"customerID":"######","passphrase":"########################################"}").

## Transfer Files
4a. Transfer /public/index.php to the webroot folder on your server.
4b. Transfer /jobs/downloadTradeTrackerData.php to the cronjobs folder on your server.
4c. Transfer /config/db.ini to the /config folder on your server.
4d. Transfer /database/sql.inc.php to the /database folder on your server.

## Download TradeTracker Data
5. Schedule downloadTradeTrackerData.php in order to get your TradeTracker account data and save these into your database.
