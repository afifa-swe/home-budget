# Backend README

Authentication (Laravel Passport)
--------------------------------

The backend uses Laravel Passport for API authentication. A password-grant OAuth client is required and its credentials should be stored in the project's `.env`.

1. Create a password client (if you don't have one):

   php artisan passport:client --password --name="Password Grant Client" --no-interaction

   The command prints the client id and client secret. Add them to `.env`:

   PASSPORT_PASSWORD_CLIENT_ID=your-client-id
   PASSPORT_PASSWORD_CLIENT_SECRET=your-client-secret

2. Clear Laravel config cache so the env values are picked up:

   php artisan config:clear

3. Postman: load `backend/postman/HomeBudget.postman_collection.json` and create an environment with these variables:

   - baseUrl: http://localhost:8078/api
   - client_id: (from .env / passport client)
   - client_secret: (from .env / passport client)
   - access_token: (leave empty; Login/Register requests will save it automatically)

   Use the Login request to obtain an access token; subsequent requests include `Authorization: Bearer {{access_token}}`.
