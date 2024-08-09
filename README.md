# Initialization
```shell
composer install
docker compose up -d --build
php init
php yii migrate
docker compose exec frontend bash ./create-links
```
## Parsing books
```shell
# старт подписчика/слушателя очереди
php yii queue/listen
# старт добавления/издания джобы парсинга книг
php yii parsing/start /path/to/books.json
```