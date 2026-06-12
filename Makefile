DOCKER_COMPOSE = $(shell if docker compose version >/dev/null 2>&1; then echo "docker compose"; else echo "docker-compose"; fi)
DOCKER_EXEC = docker exec
SYMFONY_CONSOLE = $(DOCKER_COMPOSE) exec php bin/console
EXEC_PHP ?= $(DOCKER_COMPOSE) exec -T php

start: ## Setup the project
	cp .env.dist .env && \
	$(DOCKER_COMPOSE) -f docker-compose.yaml up -d && \
	$(EXEC_PHP) php composer.phar update --dev -vvv && \
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction && \
	$(SYMFONY_CONSOLE) lexik:jwt:generate-keypair --skip-if-exists --no-interaction && \
	echo "Waiting for RabbitMQ to start..."
	while ! docker exec -it sample-rabbitmq rabbitmqctl status &> /dev/null; do \
		sleep 1; \
	done
	echo "RabbitMQ started successfully!"
	$(DOCKER_EXEC) -it sample-rabbitmq rabbitmqctl add_vhost sample && \
    $(DOCKER_EXEC) -it sample-rabbitmq rabbitmqctl set_permissions -p sample sample ".*" ".*" ".*" && \
	$(SYMFONY_CONSOLE) messenger:setup-transports --no-interaction

test-coverage:
	docker exec -e XDEBUG_MODE=coverage sample-php php bin/phpunit tests/ --coverage-text

phpstan:
	docker exec sample-php php -d memory_limit=1G vendor/bin/phpstan analyze src tests --memory-limit=1G

cs-fixer:
	docker exec sample-php vendor/bin/php-cs-fixer fix --dry-run --diff

start-workers:
	$(SYMFONY_CONSOLE) app:worker:watch --restart --workers=1 --no-interaction
