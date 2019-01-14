.PHONY: vendor

COMPOSER=./composer.phar
DOCKER_COMPOSE=$(shell which docker-compose)
PHP=$(shell which php)

all: vendor update

test:
	$(PHP) ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests

vendor: composer.phar
	$(COMPOSER) install

update: composer/update vendor/update

composer.phar:
	$(PHP) -r "readfile('https://getcomposer.org/installer');" | $(PHP)

composer/update:
	$(COMPOSER) self-update

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

elasticsearch:
	curl -H "Content-Type: application/json" -XPOST "localhost:9200/bank/_doc/_bulk?pretty&refresh" --data-binary "@tests/data/accounts.json"

vendor/update: composer.phar
	$(COMPOSER) update
