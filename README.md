# simpleinventory
tes inventory

git clone https://github.com/blackalder/simpleinventory.git

backend:
cd simpleinventory/backend 
composer install
php artisan migrate:fresh
php artisan storage:link
php artisan serve
http://localhost:8000/

frontend:
cd simpleinventory/frontend
npm i
npm run dev
go to http://localhost:8080/
