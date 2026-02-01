# Uruchomienie

docker compose up -d --build

docker compose exec php composer install

docker compose exec php php bin/console doctrine:database:create --if-not-exists

docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

