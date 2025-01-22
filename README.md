# tradetracker-accountdata

## Save Database Credentials
1. Enter your hostname, databasename, username, and password in file /config/db.ini.

## Create Database Tables
2. Import all /database/*.sql files into your database.

## Save TradeTracker API Credentials
3. Create a new row in the 'config' table where column 'name' contains the value "tradetracker" and column 'configuration' contains your [TradeTracker API](https://affiliate.tradetracker.com/webService) credentials (formatted as "{"customerID":"######","passphrase":"########################################"}").

## Transfer Files
4. Transfer all files to your server.  

## Download TradeTracker Data
5. Schedule downloadTradeTrackerData.php in order to import your TradeTracker account data and save these into your database.

## View TradeTracker Dashboard
6. Open tradetracker.php in your browser.
