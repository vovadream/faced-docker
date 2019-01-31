## Установка
Скачать и обновить зависимости:
```bash
$ curl -sS https://getcomposer.org/installer | php
$ composer update
```
Конфиги:
```bash
$ cp app/.settings.php app/settings.php
$ cp .phinx.yml phinx.yml
```
Импортировать БД. Если не нужны данные, нужно удалить --with-data
```bash
$ php cli.php init-db --with-data
```

Применить миграции
```bash
$ php cli.php phinx migrate 
```


## для продакшен сервера
```bash
$ composer dump-autoload --optimize
```