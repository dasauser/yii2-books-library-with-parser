# Initialization
```shell
composer install
docker compose up -d --build
# [0] Development, yes to all
php init
# yes
php yii migrate
docker compose exec frontend bash ./create-links
```
## Creating admin user
```shell
php yii seed
```
## Parsing books
```shell
# старт подписчика/слушателя очереди
php yii queue/listen
# старт добавления/издания джобы парсинга книг
php yii parsing/start /path/to/books.json
```