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
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

app-clear:
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/* var/tarantool/*'

#Composer
app-init: app-permissions app-composer-install \
	app-wait-db-node-0 app-wait-db-node-1 app-wait-db-node-2 app-wait-proxysql \
	setup-sharding \
	app-db-migrations app-db-fixtures

app-permissions:
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 var/cache var/log var/test var/tarantool

app-composer-install:
	docker compose run --rm php-cli composer install

app-composer-update:
	docker compose run --rm php-cli composer update

app-composer-autoload: #refresh autoloader
	docker compose run --rm php-cli composer dump-autoload

app-composer-outdated: #get not updated
	docker compose run --rm php-cli composer outdated

app-wait-proxysql:
	docker compose run --rm php-cli wait-for-it proxysql:6033 -t 30

app-wait-db-node-0:
	docker compose run --rm php-cli wait-for-it db-node-0:3306 -t 30

app-wait-db-node-1:
	docker compose run --rm php-cli wait-for-it db-node-1:3306 -t 30

app-wait-db-node-2:
	docker compose run --rm php-cli wait-for-it db-node-2:3306 -t 30

#DB
app-db-validate-schema:
	docker compose run --rm php-cli composer app orm:validate-schema

app-db-migrations-diff:
	docker compose run --rm php-cli composer app migrations:diff

app-db-migrations:
	docker compose run --rm php-cli composer app migrations:migrate -- --no-interaction

app-db-fixtures:
	docker compose run --rm php-cli composer app fixtures:load


#Lint and analyze
app-lint:
	docker compose run --rm php-cli composer lint
	docker compose run --rm php-cli composer php-cs-fixer fix -- --dry-run --diff

app-cs-fix:
	docker compose run --rm php-cli composer php-cs-fixer fix

app-analyze:
	docker compose run --rm php-cli composer psalm


#Tests
app-test:
	docker compose run --rm php-cli composer test

app-test-coverage:
	docker compose run --rm php-cli composer test-coverage

app-test-unit:
	docker compose run --rm php-cli composer test -- --testsuite=unit

app-test-unit-coverage:
	docker compose run --rm php-cli composer test-coverage -- --testsuite=unit

app-test-functional:
	docker compose run --rm php-cli composer test -- --testsuite=functional

app-test-functional-coverage:
	docker compose run --rm php-cli composer test-coverage -- --testsuite=functional

#Console
console:
	docker compose run --rm php-cli composer app

setup-sharding: setup-sharding-node-1 setup-sharding-node-2

setup-sharding-node-1:
	docker exec -it -e MYSQL_PWD=1234567890 hl-mysql-node-1 mysql -u root \
	-e "\
		use hl-messenger; \
		CREATE TABLE conversation_message (id INT AUTO_INCREMENT NOT NULL, shard_id INT NOT NULL, conversation_id INT NOT NULL, user_id INT NOT NULL, text LONGTEXT NOT NULL, created_at INT NOT NULL, updated_at INT DEFAULT NULL, deleted_at INT DEFAULT NULL, INDEX IDX_CONVERSATION (conversation_id), INDEX IDX_USER (user_id), INDEX IDX_CREATED_AT (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci' ENGINE = InnoDB; \
	"

setup-sharding-node-2:
	docker exec -it -e MYSQL_PWD=1234567890 hl-mysql-node-2 mysql -u root \
	-e "\
		use hl-messenger; \
		CREATE TABLE conversation_message (id INT AUTO_INCREMENT NOT NULL, shard_id INT NOT NULL, conversation_id INT NOT NULL, user_id INT NOT NULL, text LONGTEXT NOT NULL, created_at INT NOT NULL, updated_at INT DEFAULT NULL, deleted_at INT DEFAULT NULL, INDEX IDX_CONVERSATION (conversation_id), INDEX IDX_USER (user_id), INDEX IDX_CREATED_AT (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci' ENGINE = InnoDB; \
	"
