# HomeBudget

Краткое описание
----------------
HomeBudget — онлайн-сервис для учёта доходов и расходов семьи.

Технологический стек
--------------------
- Backend: Laravel (PHP 8.2), PostgreSQL, Nginx, Docker
- Frontend: Vue 3, Vite, Vuetify, Pinia, Axios

Функционал
---------
- CRUD транзакций (доходы/расходы)
- Категории доходов и расходов (конфигурируемые)
- Автоматическая статистика по месяцам с вычислением running balance
- Авторизация необязательна (демо-проект)

Установка
---------
1. Клонировать репозиторий:

   git clone <REPO_URL>
   cd home-budget

2. Запустить через Docker:

   docker compose up -d --build

3. Backend — выполнить миграции и сиды:

   # если хотите выполнить внутри контейнера backend (рекомендуется)
   docker compose exec backend php artisan migrate --seed

   Или локально в папке backend:

   php artisan migrate --seed

4. Frontend — сборка (в контейнере node или локально):

   # внутри контейнера с node, если настроено
   docker compose exec frontend npm install
   docker compose exec frontend npm run build

   # локально в папке frontend
   npm install
   npm run build

Замечания
---------
- Dev-сервер фронтенда: запускается командой `npm run dev` в папке `frontend` и доступен по умолчанию на порту 5176.
- Произведённая сборка фронтенда (`dist`/`build`) можно проксировать через Nginx (в Docker) для обслуживания статических файлов.

Адреса по умолчанию
--------------------
- Backend API: http://localhost:8078/api
- Frontend (dev): http://localhost:5176
- Собранный frontend (dist): можно проксировать через Nginx

Примеры API эндпоинтов
----------------------
- Получить список транзакций (GET):

  GET /api/transactions
  Response: 200 OK
  [
    {
      "id": 1,
      "type": "income",
      "category": "Salary",
      "occurred_at": "2025-09-01",
      "amount": 1000.00,
      "running_balance": 1000.00,
      "comment": "Зарплата"
    },
    ...
  ]

- Создать транзакцию (POST):

  POST /api/transactions
  Content-Type: application/json

  {
    "type": "expense",
    "category": "Groceries",
    "occurred_at": "2025-09-10",
    "amount": 50.25,
    "comment": "Продукты"
  }

  Response: 201 Created
  {
    "id": 42,
    "type": "expense",
    "category": "Groceries",
    "occurred_at": "2025-09-10",
    "amount": 50.25,
    "running_balance": 949.75,
    "comment": "Продукты"
  }

- Получить категории (GET):

  GET /api/categories
  Response: 200 OK
  {
    "income": ["Salary", "Gift"],
    "expense": ["Groceries", "Rent", "Transport"]
  }

Screenshots
-----------
(Добавьте скриншоты интерфейса сюда позже — например: `screenshots/transactions.png`, `screenshots/dashboard.png`)

Future Improvements
-------------------
- Добавить интерактивные графики по доходам/расходам (Chart.js / ECharts)
- Добавить полноценную авторизацию и роли пользователей
- Экспорт/импорт транзакций в CSV/Excel
- Фильтры и сложные запросы по категориям/датам
- Режим многовалютности и истории курсов

Контакты
--------
Проект — демо/учебный. Вопросы по использованию размещённого кода можно отправлять через систему контроля версий (issues) в репозитории.

---

Автор: HomeBudget (demo)
