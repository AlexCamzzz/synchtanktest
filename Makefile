fresh-start:
	php backend/bin/console doctrine:database:drop --force
    php backend/bin/console doctrine:database:create
    php backend/bin/console doctrine:migrations:migrate --force
