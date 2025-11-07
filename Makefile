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
	./vendor/bin/pint


migrate:
	php artisan migrate

console:
	php artisan tinker

test:
	php artisan test