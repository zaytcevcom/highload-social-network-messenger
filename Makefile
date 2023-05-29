pull: pull-git pull-composer pull-migrate pull-clear

pull-git:
	git pull

pull-composer:
	/www/server/php/81/bin/php composer.phar install --no-interaction

pull-migrate:
	/www/server/php/81/bin/php composer.phar app-server migrations:migrate

pull-clear:
	rm -rf var/cache/* var/log/*

init: init-ci
init-ci: docker-down-clear \
	app-clear \
	docker-pull docker-build docker-up \
	app-init

up: docker-up
down: docker-down
restart: down up

#linter and code-style
lint: app-lint
analyze: app-analyze
validate-schema: app-db-validate-schema
cs-fix: app-cs-fix
test: app-test

update-deps: app-composer-update

#check all
check: lint analyze validate-schema #test

#Docker
docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

app-clear:
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

#Composer
app-init: app-permissions app-composer-install \
	app-wait-db-node-0 app-wait-db-node-1 app-wait-db-node-2 app-wait-proxysql \
	app-db-migrations app-db-fixtures

app-permissions:
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 var/cache var/log var/test

app-composer-install:
	docker-compose run --rm php-cli composer install

app-composer-update:
	docker-compose run --rm php-cli composer update

app-composer-autoload: #refresh autoloader
	docker-compose run --rm php-cli composer dump-autoload

app-composer-outdated: #get not updated
	docker-compose run --rm php-cli composer outdated

app-wait-proxysql:
	docker-compose run --rm php-cli wait-for-it proxysql:6033 -t 30

app-wait-db-node-0:
	docker-compose run --rm php-cli wait-for-it db-node-0:3306 -t 30

app-wait-db-node-1:
	docker-compose run --rm php-cli wait-for-it db-node-1:3306 -t 30

app-wait-db-node-2:
	docker-compose run --rm php-cli wait-for-it db-node-2:3306 -t 30

#DB
app-db-validate-schema:
	docker-compose run --rm php-cli composer app orm:validate-schema

app-db-migrations-diff:
	docker-compose run --rm php-cli composer app migrations:diff

app-db-migrations:
	docker-compose run --rm php-cli composer app migrations:migrate -- --no-interaction

app-db-fixtures:
	docker-compose run --rm php-cli composer app fixtures:load


#Lint and analyze
app-lint:
	docker-compose run --rm php-cli composer lint
	docker-compose run --rm php-cli composer php-cs-fixer fix -- --dry-run --diff

app-cs-fix:
	docker-compose run --rm php-cli composer php-cs-fixer fix

app-analyze:
	docker-compose run --rm php-cli composer psalm


#Tests
app-test:
	docker-compose run --rm php-cli composer test

app-test-coverage:
	docker-compose run --rm php-cli composer test-coverage

app-test-unit:
	docker-compose run --rm php-cli composer test -- --testsuite=unit

app-test-unit-coverage:
	docker-compose run --rm php-cli composer test-coverage -- --testsuite=unit

app-test-functional:
	docker-compose run --rm php-cli composer test -- --testsuite=functional

app-test-functional-coverage:
	docker-compose run --rm php-cli composer test-coverage -- --testsuite=functional

#Console
console:
	docker-compose run --rm php-cli composer app

#docker-compose run --rm php-cli composer require monolog/monolog
#docker-compose run --rm php-cli composer outdated --direct - просмотр мажорных обновлений
#docker-compose run --rm php-cli composer update --with-dependencies vimeo/psalm
#docker-compose run --rm php-cli composer app migrations:diff
