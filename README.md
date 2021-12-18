# Stracker - the Strategy Tracker

This is a ultility tool that I made to automatically track my investment returns on PrimeXBT. The PHP part (everything outside the logger directory) is used as an UI for me to interact with this ultility tool so that I can add strategies to track and record transactions I performed. The Node.js part (the scripts inside the logger directory) will then automatically create a log of my investment returns based on the added strategies and recorded transactions. 

## Technologies Used

PHP part: 
 - PHP 7.3
 - Sqlite3
 - curl

Node.js part: 
 - Node.js
 - node-cron

## Limitations

However, due to the small scale of my efforts to track my investment returns, I could not keep up with all the irregularities that might happen, so that this ultility tool has some major limitations: 
 - It assumes that the exchange rates between cryptocurrencies never change
 - It assumes that PrimeXBT strategies will never be closed by their strategy managers