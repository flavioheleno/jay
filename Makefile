.DEFAULT_GOAL := help

composer.lock:
	@composer validate --strict
	@composer update

install: composer.json composer.lock  ## Install PHP dependencies

update: composer.json ## Update PHP dependencies
	@composer update -W

lint: install ## Run php code linter
	@./vendor/bin/parallel-lint -j $(shell nproc) --exclude ./vendor .

phpstan: install ## Run phpstan static code analysis
	@php -dopcache.enable=0 ./vendor/bin/phpstan analyse --level=max --autoload-file=./vendor/autoload.php ./src

phpunit: install ## Run phpunit test suite
	@./vendor/bin/phpunit ./tests

phpunit-%: install
	@./vendor/bin/phpunit ./tests --no-coverage --group $*

help: ## Show this help
	@printf "\033[37mUsage:\033[0m\n"
	@printf "  \033[37mmake [target]\033[0m\n\n"
	@printf "\033[34mAvailable targets:\033[0m\n"
	@grep -E '^[0-9a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[0;36m%-12s\033[m %s\n", $$1, $$2}'
	@printf "\n"
.PHONY: help
