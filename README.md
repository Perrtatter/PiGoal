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
 - Share category

<br>
 
## Update 
Run this on your linux shell
```bash
sudo pigoal_update
```