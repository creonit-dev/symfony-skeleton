# Project

### Первоначальная установка

- Скопируйте и настройте переменные окружения .env
  
  `cp .env.dist .env`
  
- Запустите контейнеры 

  `bin/docker up -d`
  
- Установите PHP зависимости

  `bin/composer install`
  
  >Если у вас не выполняется эта команда, попробуйте так:
  >
  >`bin/docker exec php composer install`
  
  Во время установки symfony запросит некоторые параметры 
  (например, подключение к базе)
  
- Соберите модель

  `bin/app propel:model:build`
  
- Установите ассеты

  `bin/app assets:install`
   
- Добавьте хост проекта в /etc/hosts (C:/Windows/System32/drivers/etc/hosts)

      sudo vim /etc/hosts
      
      127.0.0.1 project.localhost
  
### Работа с docker-compose
- Запустить контейнеры

  `bin/docker up -d`
  
- Остановить контейнеры

  `bin/docker down`
  
### Работа с проектом

##### Propel миграция
- Собираем модель

  `bin/app propel:model:build`
  
- Создаем миграцию

  `bin/app propel:migration:diff`
  
- Проверяем запросы в созданном файле миграции, удаляем все лишнее
  
- Выполняем запросы миграции

  `bin/app propel:migration:migrate`


##### Markup

- Для перезагрузки сборщика markup

  `bin/docker restart markup`