# Personal Expense Backend

A REST API for managing personal expenses. The service provides endpoints for
users, expense categories, and expenses, and is designed to work with the
[Personal Expense frontend](https://github.com/abdoun/personal-expense-frontend).

## Technology

- PHP 8.4.27
- Symfony 5.1
- Doctrine ORM
- MySQL

## API

The API is organized around three resource groups:

| Resource | Base path |
| --- | --- |
| Users | `/users` |
| Categories | `/categories` |
| Expenses | `/expenses` |

Endpoints accept the HTTP methods declared by each route. Many endpoints expect
JSON request bodies; see the controllers in `src/Controller` for request fields
and response formats.

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
