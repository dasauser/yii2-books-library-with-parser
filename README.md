# Initialization
```shell
composer install
docker compose up -d --build
php init
php yii migrate
docker compose exec frontend bash ./create-links
```