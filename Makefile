dev:
	symfony server:start

db-create:
	php bin/console doctrine:database:create --if-not-exists

entity:
	php bin/console make:entity

migration:
	php bin/console make:migration

migrate:
	php bin/console doctrine:migrations:migrate

db-drop:
	php bin/console doctrine:database:drop -f