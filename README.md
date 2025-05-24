## Запуск проекта

### 1. Клонирование и установка

```bash
git clone https://github.com/you/backend-test.git
cd backend-test
cp .env .env.local
```

### 2. Запуск через Docker
```bash
docker-compose up -d --build
```

### 3. Установка зависимостей (в контейнере)
```bash
docker exec -it sio_test composer install
```

### 4. Создание базы данных и сидов
```bash
docker exec -it sio_test php bin/console doctrine:database:create
docker exec -it sio_test php bin/console doctrine:migrations:migrate
docker exec -it sio_test php bin/console app:seed-database
```

### Запуск тестов:
```bash
docker exec -it sio_test php bin/phpunit
```