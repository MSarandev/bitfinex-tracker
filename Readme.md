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

**NOTE** You can **ONLY** register through the UI for now!!!

<br>

All subsequent API calls require a token. Get one:

```text
POST localhost:8088/api/v1/token
{email, pass -> form-data}
```

From there-on, use the token string as a `Bearer token` auth in subsequent requests

The token has a TTL of 90 minutes by default. <br>
Only one token can be active at a time, if you request a new one, the old one(s) are deleted!

## Scheduling

The app supports out-of-the-box scheduling with a custom `schedule` container. <br>
After the normal `docker compose up --build` you'll see the scheduler, which switches between
serving php-fpm and the scheduler<br>

## Emails

All emails are captured by `MailHog`, UI dash: http://localhost:8025/

# Feature requests

## Price action

A price action allows you as a user to subscribe to changes in price for a given symbol
in both `above` and `below` states. <br>

Use the `New Price Action` request in the postman collection: `{{app_url}}v1/price-action`

If a price action is confirmed an email will be sent by an agent<br>
Manual triggering for testing can be done by running the command: `php artisan app:price-action-notifications` in the
`app` container

The above trigger is checked every minute by the scheduler, thus email spam is expected :)

### Activation/Deactivation

The price action record remains `active` until you disable it. <br>
You can do so with the `Deactivate price action` request in the postman collection
`PUT {{app_url}}v1/price-action/1/deactivate`

You can further re-activate it with `Activate price action` request
`PUT {{app_url}}v1/price-action/1/activate`

### Deletion

You can fully delete a price action record by calling the `Delete price action` request
`DELETE {{app_url}}v1/price-action/1`

## Percent change

A percent change (percent delta) allows you as a user to subscribe to changes in price for a given symbol
by a given percentage in a time frame<br>

Use the `New Percent delta` request in the postman collection: `{{app_url}}v1/percent-delta`
to create one

If a percent delta is confirmed an email will be sent by an agent<br>
Manual triggering for testing can be done by running the command: `php artisan app:percent-change-notifications` in the
`app` container

### Activation/Deactivation

The percent delta record remains `active` until you disable it. <br>
You can do so with the `Deactivate price action` request in the postman collection
`PUT {{app_url}}v1/percent-delta/1/deactivate`

You can further re-activate it with `Activate price action` request
`PUT {{app_url}}v1/percent-delta/1/activate`

### Deletion

You can fully delete a price action record by calling the `Delete price action` request
`DELETE {{app_url}}v1/percent-delta/1`

# Documentation
Auto-generated docs by <a href="https://scramble.dedoc.co/">SCRAMBLE</a>: <br> http://localhost:8088/docs/api

# Postman
A postman collection is included called - `postmanCollection`. You can import it and use all the requests inside. 
**NOTE** Remember to register through the UI first!!!

# Tests
Code coverage reached - 49.3%

From within the `app` container run either:
```text
php artisan test --coverage --parallel --processes=4 --testsuite=Unit --stop-on-failure
php artisan test --coverage --parallel --processes=4 --testsuite=Feature --stop-on-failure
```

Or if you want to run the whole suite:
```text
php artisan test --coverage --parallel --processes=4 --stop-on-failure
```

Single test files can be executed like:
```text
 php artisan test tests/Feature/Controllers/API/PercentDeltaControllerTest.php
```
