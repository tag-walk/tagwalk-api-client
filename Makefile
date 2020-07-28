APP_ENV?=prod

.DEFAULT_GOAL := help
.PHONY: help install test cs-fixer phpstan

help:
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'


##---------------------------------------------------------------------------
## setup
##---------------------------------------------------------------------------

install:		## Install dependencies
install:
	composer install --no-interaction

test:			## Run tests
test: phpstan phpunit

cs-fixer:		## Run php-cs-fixer
cs-fixer:
	vendor/bin/phpcs

phpcs-error:	## Run phpcs (error only)
phpcs-error:
	vendor/bin/phpcs -n

phpstan:		## Run phpstan
phpstan:
	vendor/bin/phpstan analyse -l 1 -c phpstan.neon . --ansi

phpunit:		## Run phpunit
phpunit:
	vendor/bin/phpunit --coverage-text

##---------------------------------------------------------------------------
