# Pigoal v2.beta.14
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

Install PHP in local ( enable `pqsl` extension ) and run PHP Server :
```
php -S locahost:3000
```
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
```