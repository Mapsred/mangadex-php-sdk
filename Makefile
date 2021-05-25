DOCKER_COMPOSE = docker-compose
RUN = $(DOCKER_COMPOSE) run --rm
PHP = $(RUN) php

generate:
	$(RUN) generator
	rm -rf $(ls src/* -d | grep -v Builder) tests/*
	cp -r out/php/lib/* src/
	cp -r out/php/test/* tests/
	rm -rf out/
	$(MAKE) clean-namespace
	$(MAKE) clean-doc
	$(MAKE) php-cs

generate-full: generate rector php-cs

rector:
	$(PHP) vendor/bin/rector process

php-cs:
	$(PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes

clean-namespace:
	find src/ -not \( -name .svn -prune -o -name .git -prune \) -type f -print0 | xargs -0 sed -i 's#OpenAPI\\Client#Mapsred\\MangadexSDK#g'
	find tests/ -not \( -name .svn -prune -o -name .git -prune \) -type f -print0 | xargs -0 sed -i 's#OpenAPI\\Client#Mapsred\\MangadexSDK#g'

clean-doc:
	$(PHP) bin/console --cleaner

composer-install:
	$(PHP) composer install

test:
	$(PHP) vendor/bin/phpunit tests/*
