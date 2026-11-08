# Personal Expense Backend

A REST API for managing personal expenses. The service provides endpoints for
users, expense categories, and expenses, and is designed to work with the
[Personal Expense frontend](https://github.com/abdoun/personal-expense-frontend).

## Technology

- PHP 8.4.27
- Symfony 5.1
- Doctrine ORM
- MySQL

## API routes

The base URL is `http://localhost` when running with the default Docker Compose
ports. Routes are defined in the controllers and use JSON request bodies.

### Users

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/users/login` | Display the sign-in form |
| `POST` | `/users/signin` | Sign in; returns the user ID or `null` |
| `POST` | `/users/` | List users (requires credentials) |
| `GET`, `POST` | `/users/{id}` | Get a user and their categories (requires that user's credentials) |

### Categories

| Method | Path | Purpose |
| --- | --- | --- |
| `POST` | `/categories/` | List categories for the authenticated user |
| `POST` | `/categories/add` | Create a category |
| `POST` | `/categories/{id}` | Get a category |
| `PATCH` | `/categories/update/{id}` | Update a category |
| `DELETE` | `/categories/delete/{id}` | Delete a category |
| `POST` | `/categories/user/{id}` | List categories for the authenticated user ID |

### Expenses

| Method | Path | Purpose |
| --- | --- | --- |
| `POST` | `/expenses/` | List expenses |
| `POST` | `/expenses/add` | Create an expense |
| `POST` | `/expenses/{id}` | Get an expense |
| `PATCH` | `/expenses/update/{id}` | Update an expense |
| `DELETE` | `/expenses/delete/{id}` | Delete an expense |
| `POST` | `/expenses/category/{id}` | List expenses for a category |
| `POST` | `/expenses/category/{id}/count` | Count expenses for a category |
| `POST` | `/expenses/user/{id}` | List expenses for a user |
| `POST` | `/expenses/user/{id}/count` | Count expenses for a user |

### Request examples

Authenticated routes expect `username` and `password` in the JSON body.
Category creation and updates also use `name` and `notes`. Expense creation and
updates use `qty`, `date_time` (`YYYY-MM-DD HH:MM:SS`), `notes`, and `category`
(the category ID).

Sign in:

```sh
curl -X POST http://localhost/users/signin \
  -H 'Content-Type: application/json' \
  -d '{"username":"YOUR_USERNAME","password":"YOUR_PASSWORD"}'
```

List categories:

```sh
curl -X POST http://localhost/categories/ \
  -H 'Content-Type: application/json' \
  -d '{"username":"YOUR_USERNAME","password":"YOUR_PASSWORD"}'
```

Create an expense:

```sh
curl -X POST http://localhost/expenses/add \
  -H 'Content-Type: application/json' \
  -d '{"username":"YOUR_USERNAME","password":"YOUR_PASSWORD","qty":-12,"date_time":"2026-11-08 10:00:00","notes":"Lunch","category":1}'
```

Most endpoints currently respond with HTTP 200 even when authentication fails;
in that case the JSON response is `null`. See `src/Controller` for response
shapes and the exact behavior of each route.

## Docker Compose setup

The repository includes a Docker Compose stack with an Apache/PHP application
container, MySQL 8, phpMyAdmin, and Redis. Install Docker Engine with the Compose
plugin, then:

1. Review `.env` and `.env.local`. Set local database credentials and ensure
   `DATABASE_URL` uses `database` as its hostname, port `3306`, and MySQL 8 as
   the server version. The hostname must be `database` from inside the
   application container; `127.0.0.1` would refer to the application container
   itself. Keep local credentials out of commits.
2. Install PHP dependencies on the host. The PHP container does not include
   Composer:

   ```sh
   composer install
   ```

3. Build and start the services:

   ```sh
   docker compose up --build -d
   ```

4. Check service status and, if needed, follow the application and database
   logs:

   ```sh
   docker compose ps
   docker compose logs -f webserver database
   ```

The API is served at `http://localhost` by default. phpMyAdmin is available at
`http://localhost:8080`. Change the corresponding host port values in `.env`
if those ports are already in use. MySQL is exposed only on the local machine
by default; the application connects to it using the Compose service name
`database`.

Stop the stack while keeping its database files with:

```sh
docker compose down
```

MySQL data is stored under `.docker/data/mysql` by default.

## Local setup without Docker

1. Install PHP 8.4.27, Composer, and MySQL.
2. Configure `DATABASE_URL` for your local database in `.env.local`.
3. Install the project dependencies:

   ```sh
   composer install
   ```

4. Configure your web server to serve the `public/` directory and route requests
   through `public/index.php`.

Do not commit local database credentials. The SQL schema and sample data are in
`migrations/personal_expense.sql`; import them only into a development database,
as the script drops and recreates tables.
