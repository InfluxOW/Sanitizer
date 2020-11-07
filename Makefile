test:
	vendor/bin/phpunit
test-coverage:
	vendor/bin/phpunit --coverage-clover build/logs/clover.xml
lint:
	composer exec phpcs -v
lint-fix:
	composer exec phpcbf -v
setup:
	composer install