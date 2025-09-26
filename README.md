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
API использует Laravel Passport с Password Grant. Контроллер `AuthController` принимает решение о client_id/client_secret только из переменных окружения — поэтому обязательно создать Password Grant Client и указать его идентификатор и секрет в `backend/.env`.

1) Как создать Password Grant Client

    Внутри контейнера приложения (или локально в папке `backend`), выполните:

    ```bash
    docker exec -it hb_app php artisan passport:client --password --name="Password Grant Client"
    ```

    Вы получите на выводе `Client ID` и `Client Secret`. Сохраните их — `Client Secret` выводится только один раз.

2) Как сохранить в `.env`

    Откройте `backend/.env` и добавьте (или обновите) две переменные:

    PASSPORT_PASSWORD_CLIENT_ID=<ваш-client-id>
    PASSPORT_PASSWORD_CLIENT_SECRET=<ваш-client-secret>

    После изменения `.env` очистите и, при желании, пересоберите конфиг-кеш:

    ```bash
    docker exec -it hb_app php artisan config:clear
    docker exec -it hb_app php artisan config:cache
    ```

3) Как проверять регистрация/логин (curl)

    - Регистрация (returns access_token):

       ```bash
       curl -s -X POST http://localhost:8078/api/register \
          -H 'Content-Type: application/json' \
          -d '{"name":"Alice","email":"alice@example.com","password":"secret","password_confirmation":"secret"}' | jq '.'
       ```

    - Логин:

       ```bash
       curl -s -X POST http://localhost:8078/api/login \
          -H 'Content-Type: application/json' \
          -d '{"email":"alice@example.com","password":"secret"}' | jq '.'
       ```

    Ответ содержит поля `access_token` и `refresh_token`. Токен — Bearer JWT.

4) Примеры использования access_token (curl)

    ```bash
    TOKEN="<вставьте access_token>"
    curl -s -H "Authorization: Bearer $TOKEN" http://localhost:8078/api/categories | jq '.'
    curl -s -H "Authorization: Bearer $TOKEN" http://localhost:8078/api/transactions | jq '.'
    ```

Postman: синхронизация коллекции
--------------------------------
- В репозитории есть `backend/postman/HomeBudget.postman_collection.json`.
- Коллекция использует переменные: `baseUrl`, `client_id`, `client_secret`, `access_token`.
- Убедитесь, что в коллекции `client_id` и `client_secret` установлены как переменные (в корне коллекции). По умолчанию коллекция уже использует `{{client_id}}` и `{{client_secret}}` в теле запросов Register/Login.
- Скрипт `Tests` в запросе Login сохраняет `access_token` в collection/environment variable:

   ```js
   const json = pm.response.json();
   if (json.access_token) { pm.collectionVariables.set('access_token', json.access_token); }
   ```

   После запуска Login остальные защищённые запросы используют `Authorization: Bearer {{access_token}}` в заголовке.

Проверка: Auth-запросы только через env
--------------------------------------
- `AuthController::issueToken` использует только `config('services.passport.password_client_id')` и `config('services.passport.password_client_secret')` — то есть значения берутся из `backend/.env` и `config/services.php`. Если они не заданы, API вернёт HTTP 500 с сообщением `Password grant client not configured in env`.

Frontend и токены
------------------
- Frontend (папка `frontend`) использует Pinia и Axios. После успешного логина access_token сохраняется в `localStorage` (ключ `access_token`) и `useAuthStore` хранит данные пользователя.
- В `frontend/src/api.ts` есть Axios interceptor, который автоматически добавляет заголовок `Authorization: Bearer <token>` ко всем запросам, читая токен из `localStorage`.
- Команды для запуска фронтенда локально:

   ```bash
   cd frontend
   npm install
   npm run dev   # запуск dev-сервера (Vite)
   npm run build # сборка для production
   ```

End-to-end проверка (быстрая инструкция)
--------------------------------------
1. Убедитесь, что в `backend/.env` прописаны `PASSPORT_PASSWORD_CLIENT_ID` и `PASSPORT_PASSWORD_CLIENT_SECRET` (см. выше).
2. Поднять контейнеры: `docker compose up -d --build`.
3. Запустить миграции/сиды: `docker exec -it hb_app php artisan migrate:fresh --seed`.
4. В Postman установите collection variables `client_id` и `client_secret` (или оставьте пустыми, потому что коллекция передаёт client creds только в теле — но AuthController использует env). Для корректной работы в Postman используйте `Register`/`Login` без явной подстановки client_id/secret — токен выдаётся на основе env в backend.
5. Выполнить Login в Postman (Auth → Login) — коллекция автоматически сохранит `access_token`.
6. Откройте защищённый запрос (Transactions/ Categories) — он выполнится с заголовком `Authorization: Bearer {{access_token}}`.

Примечание о безопасности
------------------------
- В продакшене не храните client_secret в публичном репозитории. Используйте защищённые механизмы хранения секретов (deployment env, Vault и т.д.).

Если хотите, я автоматически обновлю `backend/postman/HomeBudget.postman_collection.json`, чтобы удалить любые жёстко закодированные client_id/client_secret и гарантировать, что все защищённые запросы используют `{{access_token}}` в заголовке. Сейчас коллекция уже использует переменные `{{client_id}}` и `{{client_secret}}` в теле Register/Login и `{{access_token}}` в заголовках защищённых запросов.

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
