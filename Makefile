DOCKER_COMPOSE = docker-compose
PHP = $(DOCKER_COMPOSE) exec php

rector:
	$(PHP) vendor/bin/rector process

php-cs:
	$(PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes
