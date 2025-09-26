# Home Budget — семейная бухгалтерия

Краткое описание
-----------------
Приложение для учёта доходов и расходов семьи с поддержкой категорий и месячной статистики. Предназначено как учебный/демо-проект с готовой Docker-настройкой и API для интеграции фронтенда.

Стек технологий
---------------
- Backend: Laravel (PHP 8.2) с Laravel Passport (Password Grant)
- База: PostgreSQL
- Frontend: Vue 3 + Vite + Vuetify + Pinia
- Docker: контейнеры nginx, php-fpm, node, postgres (docker-compose)
- Postman: коллекция для тестирования API

Установка и запуск
------------------
1. Клонируйте репозиторий:

   git clone <REPO_URL>
   cd home-budget

2. Скопируйте и настройте окружение для backend (файл `backend/.env`):

   - Установите параметры подключения к БД (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).
   - Укажите URL приложения `APP_URL` (по умолчанию http://localhost:8078).
   - Для аутентификации Passport добавьте:

     PASSPORT_PASSWORD_CLIENT_ID=your-client-id
     PASSPORT_PASSWORD_CLIENT_SECRET=your-client-secret

   Примечание: client_id и client_secret можно получить командой внутри контейнера приложения:

     docker exec -it hb_app php artisan passport:client --password --name="Password Grant Client" --no-interaction

3. Поднять контейнеры:

   docker compose up -d --build

4. Выполнить миграции и сиды (внутри бэкенд-контейнера):

   docker compose exec hb_app php artisan migrate --seed

   (для полных пересборок/очистки можно использовать)

   docker exec -it hb_app php artisan migrate:fresh --seed
   docker exec -it hb_app php artisan config:clear

5. Сборка фронтенда (локально или в контейнере node):

   # локально в папке frontend
   cd frontend
   npm install
   npm run build

   # или через контейнер (если настроен)
   docker compose exec frontend npm install
   docker compose exec frontend npm run build

Авторизация (Laravel Passport)
------------------------------
API использует Laravel Passport с Password Grant. Для корректной работы логина/регистрации/получения токена нужно указать в окружении backend следующие переменные:

- PASSPORT_PASSWORD_CLIENT_ID
- PASSPORT_PASSWORD_CLIENT_SECRET

Доступные маршруты для аутентификации:

- POST /api/register — регистрация (возвращает access_token)
- POST /api/login — логин (возвращает access_token)
- GET  /api/user — получить данные текущего пользователя (требует Authorization)

API (краткий список основных эндпоинтов)
-------------------------------------
- /api/transactions — CRUD транзакций (создание, чтение, обновление, удаление). Защищённый маршрут — требует Bearer токен.
- /api/categories — CRUD категорий доходов/расходов. Защищённый.
- /api/stats — статистика (ежемесячные / сводные данные). Защищённый.

Краткая структура бэкенда
------------------------
- Модели: `App\Models\User`, `App\Models\Transaction`, `App\Models\Category`.
- Контроллеры API (в `app/Http/Controllers/Api`): `AuthController` (register/login/user), `TransactionController`, `CategoryController` и обработчик статистики.
- Маршруты: `routes/api.php` содержит маршруты для аутентификации и защищённые маршруты для транзакций, категорий и статистики.

Frontend
--------
- Vue 3 приложение находится в папке `frontend`.
- Логин/регистрация реализованы в компонентах `LoginView.vue` и `RegisterView.vue`.
- Хранилище состояния: Pinia (есть `useAuthStore` для хранения `access_token` и данных пользователя).
- Axios interceptor автоматически добавляет заголовок `Authorization: Bearer <token>` ко всем запросам (если токен сохранён в `localStorage`).

Postman
-------
- В репозитории есть коллекция для тестирования API: `backend/postman/HomeBudget.postman_collection.json`.
- Коллекция использует переменные: `baseUrl`, `client_id`, `client_secret`, `access_token`.

Полезные команды
----------------
- Поднять контейнеры: docker compose up -d --build
- Выполнить миграции и сиды: docker compose exec hb_app php artisan migrate --seed
- Полный сброс БД и сиды: docker exec -it hb_app php artisan migrate:fresh --seed
- Очистить конфигурацию: docker exec -it hb_app php artisan config:clear

Лицензия и контакты
-------------------
Лицензию и контактную информацию можно добавить здесь. По умолчанию можно использовать MIT.

---

Если нужно, могу дополнить README примерами запросов с curl, export-ом готового Postman Environment или короткой инструкцией по отладке контейнеров (docker compose logs и т.д.).
