test:
	composer prepare-test
	vendor/bin/phpunit

code:
	vendor/bin/phpcbf
	vendor/bin/phpcs
	vendor/bin/phpcbf
	vendor/bin/phpcs
	php vendor/bin/phpinsights --no-interaction --min-quality=85 --min-complexity=90 --min-architecture=85 --min-style=80
