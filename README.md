# Pigoal v2.17
<br>
 
## Requirements
For `Postgres`
```
composer require tsubasa/laravel-postgres
```

For `PHPMailer`
```
composer require phpmailer/phpmailer
```
<br>

## Run server 
Use Vscode `PHP Server`

> or 

Install PHP locally with the `pgsql` extension enabled, then run the built-in PHP server from the project root:
```
php -S localhost:3000
```
Open `http://localhost:3000` in your browser. The local `.env.json` file must contain the database connection settings.
<br>

## Go 
HTTPS URL  : https://www.pigoal.ch

<br>

## Feature
 - Login
 - Create User
 - Display category
 - Complete Goal
 - Uncomplete Goal
 - Complete category
 - Points per user
 - Leaderboard 
 - Steps
 - Create category
 - Create goals

<br>
 
## Update 
Run this on your linux shell
```bash
# go to apache
cd /var/www/html

# remove old files
sudo rm -rf *

# download news
sudo git clone https://github.com/Perrtatter/PiGoal.git temp_dir

# move it
sudo mv temp_dir/* .

# remove temps dir
sudo rm -rf tmp_dir

# update apache
sudo systemctl reload apache2
```