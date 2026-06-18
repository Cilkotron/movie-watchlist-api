# Movie Watchlist API

A REST API that allows authenticated users to manage a personal movie watchlist, enriched with data from the OMDb external API.

## Requirements

- PHP 8.2+
- Composer
- MySQL

## Setup

1. Clone the repository:
```bash
git clone https://github.com/Cilkotron/movie-watchlist-api.git
cd movie-watchlist-api
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file and configure it:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up your database in `.env`:


5. Run migrations:
```bash
php artisan migrate
```

6. Start the server:
```bash
php artisan serve
```

## External API

This project uses **OMDb API** to fetch movie details.

1. Get a free API key at [omdbapi.com](https://www.omdbapi.com/apikey.aspx)
2. Add it to your `.env`:

## Authentication

This API uses **Laravel Sanctum** for token-based authentication.

After registering or logging in, you will receive a token. Include it in all subsequent requests:

Sanctum was chosen over JWT because:
- It is built into Laravel with no additional dependencies
- Tokens are stored in the database, meaning logout truly invalidates the token
- JWT tokens are stateless and cannot be invalidated before expiry, which is unnecessary complexity for this use case

## Testing the API

A `requests.http` file is included in the root of the project. To use it, install the **REST Client** extension in VS Code and click `Send Request` above any endpoint.

### Available Endpoints

| Method | Endpoint | Auth Required |
|--------|----------|---------------|
| POST | /api/register | No |
| POST | /api/login | No |
| POST | /api/logout | Yes |

## Running Tests

```bash
php artisan test
```

## Decisions and Trade-offs

**Authentication:** Sanctum over JWT — simpler setup, real logout support, no external dependencies. JWT would make sense in a microservices architecture but adds complexity without benefit here.

**Database:** MySQL was chosen as it is the most widely used database in Laravel projects and sufficient for this use case.

**What was focused on:** Clean code organization, consistent API responses, and proper authentication flow.

**What was skipped:** Docker setup, CI/CD, and exhaustive test coverage — as per the task instructions.
