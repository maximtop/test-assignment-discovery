install:
	composer install

autoload:
	composer dump-autoload

run:
	php -S localhost:8080 -t src/

lint:
	composer phpcs
