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
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3300
DB_DATABASE=movie_watchlist
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

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
```bash 
OMDB_API_KEY=your_key_here
```


## Authentication

This API uses **Laravel Sanctum** for token-based authentication.

After registering or logging in, you will receive a token. Include it in all subsequent requests: 
```bash 
Authorization: Bearer YOUR_TOKEN
```

Sanctum was chosen over JWT because:
- It is built into Laravel with no additional dependencies
- Tokens are stored in the database, meaning logout truly invalidates the token
- JWT tokens are stateless and cannot be invalidated before expiry, which is unnecessary complexity for this use case

## Testing the API

A `requests.http` file is included in the root of the project. To use it, install the **REST Client** extension in VS Code and click `Send Request` above any endpoint.

**Important:** All requests require the `Accept: application/json` header. Without it, Laravel returns an HTML response instead of JSON.

### Available Endpoints

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | /api/register | No | Register a new user |
| POST | /api/login | No | Login and receive a token |
| POST | /api/logout | Yes | Invalidate current token |
| GET | /api/watchlist | Yes | List watchlist with pagination |
| POST | /api/watchlist | Yes | Add a movie to watchlist |
| GET | /api/watchlist/{id} | Yes | Get a single watchlist item |
| PUT | /api/watchlist/{id} | Yes | Update a watchlist item |
| DELETE | /api/watchlist/{id} | Yes | Remove a movie from watchlist |


### Adding a Movie

You can add a movie by IMDb ID or by title:

```json
{ "imdb_id": "tt0111161" }
```

```json
{ "title": "Inception" }
```

### Updatable Fields

```json
{
    "status": "watched",
    "user_rating": 9,
    "notes": "Great movie!",
    "watched_at": "2026-06-18"
}
```

### Filtering and Pagination
```bash
GET /api/watchlist?status=watched
GET /api/watchlist?status=to_watch
GET /api/watchlist?status=watching
GET /api/watchlist?per_page=5
```


## Running Tests

```bash
php artisan test
```

## Decisions and Trade-offs

**Authentication:** Sanctum over JWT — simpler setup, real logout support, no external dependencies. JWT would make sense in a microservices architecture but adds complexity without benefit here.

**Database:** MySQL was chosen as it is the most widely used database in Laravel projects and sufficient for this use case.

**Movie data structure:** Movies are stored in a separate `movies` table shared across all users. This means if 100 users add the same movie, OMDb is called only once. The `watchlist_items` table holds user-specific data (status, rating, notes).

**Adding movies:** The API supports both IMDb ID and title search. If an IMDb ID is provided it is used directly. If a title is provided, OMDb is queried by title. This is documented here as it is a decision not specified in the task.

**Duplicate prevention:** A unique constraint on `(user_id, movie_id)` prevents a user from adding the same movie twice. The API returns a `409 Conflict` in this case.

**Error handling:** Model not found errors return a clean `404` with a simple message. `APP_DEBUG` must be set to `false` in production to avoid leaking stack traces.

**What was focused on:** Clean code organization, consistent API responses using Laravel Resources, proper authentication flow, and separation of concerns (OmdbService handles all external API communication).

**What was skipped:** Docker setup, CI/CD, exhaustive test coverage, and caching of OMDb responses — as per the task instructions. Caching would be a natural next step to avoid redundant external API calls.
