# simpleinventory
tes inventory

git clone https://github.com/blackalder/simpleinventory.git

backend:
```
cd simpleinventory/backend
```

sesuaikan .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory
DB_USERNAME=root
DB_PASSWORD=bemobile04
```
```
composer install
php artisan migrate:fresh
php artisan storage:link
php artisan serve
```
http://localhost:8000/

frontend:
```
cd simpleinventory/frontend

npm i

npm run dev
```
go to http://localhost:8080/
