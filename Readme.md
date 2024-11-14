# Bitfinex prices

# Setup

Note: The whole app is docker based, nothing is installed and/or operated on from the local machine!

## Setup guide

1. `cp .env.example .env`
2. Change vars as needed
3. `docker network create bitfinex_tracker_net`
4. `docker compose up --build -d`
5. Generate an application key
    1. `docker compose exec app bash`
    2. `php artisan key:generate`
6. Run the migrations
    1. `docker compose exec app bash`
    2. `php artisan migrate` or `php artisan migrate:fresh`

The app is available at: http://localhost:8088/ (change to your custom port)

## Auth

Auth is provided by "Laravel Breeze" <br>
All emails are captured by `MailHog`, UI dash: http://localhost:8025/

<br>

The app requires a registration, please register at `http://localhost:8088/register` first <br>
After registration, you will be redirected to the login page, login, and you will be redirected to the dashboard

<br>

All subsequent API calls require a token. Get one:

```text
POST localhost:8088/api/v1/token
{email, pass -> form-data}
```

From there-on, use the token string as a `Bearer token` auth in subsequent requests
