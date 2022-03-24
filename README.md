Для того чтобы развернуть проект, необходимо выполнить следующие комманды

```
cp .env.example .env
composer install
./vendor/bin/sail php artisan key:generate
./vendor/bin/sail up -d
./vendor/bin/sail php artisan migrate
```

Для миграции данных из csv в таблицу customers необходимо переместить
файл в директорию
`storage/app/resource`
и запустить команду `./vendor/bin/sail php artisan migrate:csv`.
По умолчанию принимается файл с названием *random.csv*, чтобы передать другое название,
необходимо передать параметр `--file=filename`.

Отчет об ошибках помещается в директорию `storage/app/reports`
