php artisan make:model User -m
php artisan make:model Role -m
php artisan make:model UserRole -m

php artisan make:migration create_roles_table
php artisan make:migration create_user_roles_table


php artisan make:controller AuthController



----------------------------------
php artisan migrate
php artisan migrate:fresh