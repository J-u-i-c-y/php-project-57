install:
	composer install
	cp .env.example .env
	php artisan key:gen --ansi
	php artisan migrate
	npm ci
	npm run build

start:
	php artisan serve --host 0.0.0.0

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 app tests

migrate:
	php artisan migrate:fresh

console:
	php artisan tinker

test:
	php artisan test