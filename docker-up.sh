docker compose build
docker compose up -d
docker exec -d api composer install --no-scripts --no-autoloader
docker exec -d api composer dump-autoload --optimize