# Symfony Skeleton

### Первоначальная установка

- Скопируйте и настройте переменные окружения .env:
    `cp .env.orig .env`
  
- Запустите контейнеры:
  `docker-compose up -d`
  
- Установите PHP зависимости:
  `bin/composer install`
     
---  
  
### Работа с локальной базой данных
1. В файле .env замените переменные 
    ```
    COMPOSE_FILE=docker-compose.yml:docker-compose.override.yml:docker-compose.db.yml
    DATABASE_HOST=db
   ```
2. Остановите и запустите контейнеры заново
3. Загрузите SQL-дамп базы
   ```
   docker-compose exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" $MYSQL_DATABASE' < dump.sql
   ```
   **Где dump.sql — название файла с дампом**
  
---  
  
### Работа с docker-compose
- Запустить контейнеры:
  `docker-compose up -d`
  
- Остановить контейнеры:
  `docker-compose down`
  
---  
  
### Работа с проектом

#### Propel миграция
- Собираем модель:
  `bin/app propel:model:build`
  
- Создаем миграцию:
  `bin/app propel:migration:diff`
  
- Проверяем запросы в созданном файле миграции, удаляем все лишнее
  
- Выполняем запросы миграции:
  `bin/app propel:migration:migrate`
