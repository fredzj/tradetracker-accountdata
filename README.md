﻿# tradetracker-accountdata

## Prerequisites
- PHP
- MySQL or MariaDB
- TradeTracker API credentials

## Setup Instructions

### 1. Save Database Credentials
Create a file `/config/db.ini` and enter your database credentials in the following format:
```ini
hostname=your_hostname
databasename=your_databasename
username=your_username
password=your_password
```

### 2. Create Database Tables
Import all SQL files from the database directory into your database:
```sh
mysql -u your_username -p your_databasename < /path/to/database/file.sql
```

### 3. Save TradeTracker API Credentials
Insert a new row into the config table with your TradeTracker API credentials:
```sql
INSERT INTO config (name, configuration) VALUES ('tradetracker', '{"customerID":"######","passphrase":"########################################","sandbox":"","locale":"nl_NL","demo":""}');
```

### 4. Transfer Files
Transfer all files to your server.  

### 5. Import TradeTracker Data
Schedule `importTradeTrackerData.php` to run periodically to import your TradeTracker account data into your database. You can use a cron job for this:
```sh
# Example cron job to run the script daily at midnight
0 0 * * * /usr/bin/php /path/to/importTradeTrackerData.php
```

### 6. View TradeTracker Dashboard
Open `tradetracker.php` in your browser to view the TradeTracker dashboard.


