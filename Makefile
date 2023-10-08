php-cs-fix:
	vendor/bin/php-cs-fixer fix -vvv
tests:
	vendor/bin/phpunit -c "phpunit.xml"