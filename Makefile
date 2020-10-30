PHP = php

DOCKER_RUN = docker run --rm --interactive --tty --volume $(PWD):/app --workdir /app jakzal/phpqa:php7.3-alpine

##
## Dependencies
## -----
##

composer-require-symfony-version: ## Require specific version of Symfony
	composer require symfony/config:$(SYMFONY_VERSION) symfony/form:$(SYMFONY_VERSION) symfony/framework-bundle:$(SYMFONY_VERSION) symfony/templating:$(SYMFONY_VERSION) symfony/security-csrf:$(SYMFONY_VERSION) symfony/twig-bundle:$(SYMFONY_VERSION) symfony/var-dumper:$(SYMFONY_VERSION) --no-update

composer-install: ## Install Composer dependencies
	composer install --no-progress --prefer-dist --optimize-autoloader --no-suggest

composer-update: ## Install Composer dependencies
	composer update --no-progress --prefer-dist --optimize-autoloader --no-suggest

pull-docker-image: ## Pull Docker image for QA
	docker pull jakzal/phpqa:1.25-php7.2-alpine

##
## Tests
## -----
##

phpspec: ## phpspec
	$(PHP) ./vendor/bin/phpspec run --format=pretty

phpunit: ## PHPUnit
	$(PHP) ./vendor/bin/phpunit --testdox

behat: ## Behat
	$(PHP) ./vendor/bin/behat --colors --strict -vv

tests: phpspec phpunit behat ## Run all tests

##
## QA
## --
##

php-cs-fixer: ## PHP-CS-Fixer
	$(DOCKER_RUN) php-cs-fixer fix src/

php-cs-fixer-dry-run: ## PHP-CS-Fixer
	$(DOCKER_RUN) php-cs-fixer fix src/ --dry-run

phpstan: ## PHPStan
	$(DOCKER_RUN) phpstan analyse --no-progress

qa: php-cs-fixer phpstan ## Run all QA tasks

.PHONY: php-cs-fixer phpstan

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
