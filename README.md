git clone https://github.com/sagarkumar66/crud-app.git

cd crud-app

cp .env.example .env

open .env and update DB_DATABASE (database details)

run : composer install

run : php artisan key:generate

run : php artisan migrate:fresh --seed

run : php artisan serve

Open new terminal

run : npm install

run : npm run dev

Best of luck