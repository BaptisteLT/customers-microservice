# Drop, create, and update the test database
php bin/console doctrine:database:drop --force --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test

# Optionally load fixtures
php bin/console doctrine:fixtures:load --env=test --no-interaction