# Laravel Sanctum Starter Kit

A Laravel 13 API using Sanctum bearer tokens, PHP 8.5, and Laravel Sail. The application has no frontend framework, JavaScript dependencies, or asset build.

## Create a project

Use GitHub's **Use this template** button to create your own repository, then clone it and enter its directory. Requirements: Git, Docker Engine or Docker Desktop with Docker Compose v2, and a POSIX shell (Linux, macOS, or Windows through WSL2). Host PHP, Composer, and Node are not required.

### Create with Composer

If you already have Composer 2.10+ and PHP 8.5, you can create an application directly from this private GitHub repository. Your GitHub account needs repository access, and Composer needs [GitHub authentication](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth). Configure credentials outside the project; do not put tokens in the command or commit them.

```bash
composer create-project devshaded/laravel-sanctum-starter-kit example-app dev-main \
  --prefer-dist --no-install --no-scripts \
  --repository='{"type":"vcs","url":"https://github.com/DevShaded/laravel-sanctum-starter-kit"}'
cd example-app
```

Continue with **Fresh-clone setup** below. `--no-install --no-scripts` defers dependency installation and application initialization until Docker is configured. `dev-main` selects the current starter kit branch; tagged releases can be selected once available.

The short command `composer create-project devshaded/laravel-sanctum-starter-kit --prefer-dist example-app` requires the package to be published to Packagist or available through a configured private Composer registry. This repository is currently private and is not registered on Packagist, so the explicit `--repository` option is required. Composer package names are lowercase.

### Fresh-clone setup

Create the local environment and record your local user/group IDs:

```bash
cp .env.example .env
printf '\nWWWUSER=%s\nWWWGROUP=%s\n' "$(id -u)" "$(id -g)" >> .env
```

Before starting containers, edit `.env`:

- Set `APP_NAME` to your application name.
- Set `COMPOSE_PROJECT_NAME` to a unique lowercase name such as `my-api`. This isolates containers, networks, volumes, and the built Sail image (`my-api-sail:8.5`) from other projects.
- Set `DB_DATABASE` to your application's database name before the first database startup.
- Choose unused host ports for `APP_PORT`, `FORWARD_DB_PORT`, `FORWARD_REDIS_PORT`, `FORWARD_MAILPIT_PORT`, and `FORWARD_MAILPIT_DASHBOARD_PORT`. Update `APP_URL` to match `APP_PORT`.

Sail lives in `vendor`, so download dependencies with a disposable Composer container before invoking Sail:

```bash
docker run --rm \
  --user "$(id -u):$(id -g)" \
  -e COMPOSER_HOME=/tmp/composer \
  -v "$PWD:/app" -w /app \
  composer:2.10 install --ignore-platform-reqs --no-scripts --no-plugins --no-interaction --prefer-dist

vendor/bin/sail up -d --build
vendor/bin/sail composer setup
vendor/bin/sail composer check-platform-reqs
vendor/bin/sail composer check
vendor/bin/sail composer audit --locked
```

The bootstrap container only downloads locked dependencies. Platform checks, plugins, and scripts run inside Sail with PHP 8.5 during `composer setup`; `check-platform-reqs` verifies the real runtime. Initial image builds may take several minutes. If an image build encounters a temporary package-mirror failure, rerun it.

`composer setup` installs dependencies, creates `.env` if missing, clears cached configuration, generates `APP_KEY` only when empty, and runs pending migrations. It can be rerun without rotating your key. Do not use it for production deployment. Rebuild with `vendor/bin/sail build` after changing `WWWGROUP`.

### Daily development

```bash
vendor/bin/sail up -d
vendor/bin/sail composer dev
```

`composer dev` runs the queue worker in the current terminal. Run the scheduler in another terminal when needed:

```bash
vendor/bin/sail artisan schedule:work
```

Default host ports bind to localhost:

| Service | Host port | Container address |
| --- | --- | --- |
| API | 8081 | laravel.test:80 |
| PostgreSQL | 5433 | pgsql:5432 |
| Redis | 6380 | redis:6379 |
| Mailpit dashboard | 8026 | mailpit:8025 |
| Mailpit SMTP | 1026 | mailpit:1025 |

Application connections use container service names and container ports; changing a forwarded host port does not change `DB_PORT`, `REDIS_PORT`, or `MAIL_PORT`.

Stop containers with `vendor/bin/sail stop`, or remove containers and networks with `vendor/bin/sail down`. Volumes persist. `down --volumes` deletes the project's database and Redis data. Changing `DB_DATABASE` after PostgreSQL initializes does not rename or create databases in an existing volume.

### Customize the template

- Change Composer `name`, `description`, and `keywords` in `composer.json`. Refresh the lock metadata with `vendor/bin/sail composer update --lock --no-install --no-scripts --no-plugins`.
- Customize `.env.example` application/database names and Compose project name for your repository, then update your untracked `.env`.
- Assign distinct Compose project names and all five host ports to run multiple generated projects together.
- Keep the MIT copyright notice when reusing the code; add your own copyright attribution as appropriate.
- Replace this README's title and project-specific guidance as your application grows.

## API authentication

- `GET /up` is the public health endpoint. Send `Accept: application/json` for a JSON response.
- `GET /api/user` returns the authenticated user and requires `Authorization: Bearer <token>`.
- Missing, invalid, expired, or revoked tokens return JSON with status 401. Browser session authentication and Sanctum's CSRF-cookie endpoint are disabled.
- This is an API scaffold. Registration and login endpoints have not been added.

For a local development token, open Tinker:

```bash
./vendor/bin/sail artisan tinker
```

```php
$user = App\Models\User::factory()->create();
$user->createToken('local-development', ['*'], now()->addDay())->plainTextToken;
```

Use the returned token:

```bash
curl -H 'Accept: application/json' \
  -H 'Authorization: Bearer <token>' \
  http://localhost:8081/api/user
```

The factory creates a development user; it is not an account provisioning workflow for production. Revoke its tokens with `$user->tokens()->delete()` when finished.

Tokens do not expire by default. Set the third argument of `createToken` for per-token expiry, or configure `expiration` in `config/sanctum.php` for a global lifetime in minutes. The scheduler runs `sanctum:prune-expired --hours=24` daily at midnight (application timezone), deleting tokens expired for more than 24 hours while keeping active and non-expiring tokens. Run one scheduler for the application.

## API documentation

Scramble generates the interactive reference at `/docs/api` and the OpenAPI document at `/docs/api.json`. Both are available in the local environment without browser sessions. Outside `local`, the starter kit denies access with HTTP 403 through Scramble's `RestrictedDocsAccess` middleware. Keep that restriction unless you deliberately implement an access policy for your application.


## Quality checks

[Larastan](https://github.com/larastan/larastan) extends PHPStan with Laravel support. `phpstan.neon` uses level 10, the [strictest current level](https://phpstan.org/user-guide/rule-levels), with no baseline or ignored errors. Analysis covers application code, bootstrap files, configuration, database code, routes, and tests.

[Rector](https://getrector.com/documentation/set-lists) targets the PHP version required by Composer and applies dead-code, code-quality, and type-declaration rules. Pint handles formatting.

```bash
./vendor/bin/sail composer analyse       # Larastan
./vendor/bin/sail composer rector:check  # Report changes without modifying files
./vendor/bin/sail composer rector        # Apply refactorings
./vendor/bin/sail composer format        # Format PHP
./vendor/bin/sail composer test          # PHPUnit
./vendor/bin/sail composer check         # Larastan, Rector dry run, and PHPUnit
```

Tests use Sail's separate PostgreSQL `testing` database, with isolated cache, queue, mail, and session drivers.

CI validates Composer configuration, audits locked dependencies, and runs `composer check` on PHP 8.5 with PostgreSQL 18. A second job verifies the Docker-only fresh-clone setup. Dependabot checks Composer, GitHub Actions, and Docker Compose images weekly; review and merge its pull requests after checks pass. The locally built Sail image is updated through the `laravel/sail` Composer dependency.

The Composer audit policy permits only the abandoned development dependency `symplify/rule-doc-generator-contracts`, currently required by `driftingly/rector-laravel`. All security advisories and other abandoned packages still fail the audit. Remove the exception when the upstream dependency is removed. See [Composer's dependency policy](https://getcomposer.org/doc/06-config.md#policy).

## Deployment

Sail is the local development environment. Deploy using Laravel Cloud or a production runtime with PHP 8.5 and the extensions required by `composer check-platform-reqs --no-dev`, PostgreSQL, and Redis. Serve only the `public` directory over HTTPS and allow the application to write to `storage` and `bootstrap/cache`.

Configure production secrets through your hosting platform: `APP_ENV=production`, `APP_DEBUG=false`, the HTTPS `APP_URL`, database credentials, Redis credentials, and production mail/storage settings. Preserve this application's `DB_CONNECTION=pgsql`, `QUEUE_CONNECTION=redis`, and `CACHE_STORE=redis` unless deliberately replacing those services. Assign distinct Redis databases or prefixes for applications sharing Redis.

Generate `APP_KEY` once in the production runtime, store it in the platform's secret manager, and preserve it across deployments. Do not copy the development key or regenerate it during releases. Back up the database and the key. Deploy the committed `composer.lock`; production does not need development dependencies or frontend builds.

Run these commands in the production application's runtime, using its PHP/Composer executables (Sail prefixes apply only to local development):

```bash
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
composer check-platform-reqs --no-dev
php artisan migrate --force --no-interaction
php artisan optimize
php artisan queue:restart
```

Run `php artisan queue:work --tries=3 --timeout=60` under a process supervisor that restarts exited workers. Keep the queue connection's `retry_after` greater than the worker timeout; adjust both for your jobs. `queue:restart` makes workers finish their current jobs and exit so the supervisor loads the new release. Review failed jobs with `php artisan queue:failed`.

Run the scheduler on one application node with a cron entry once per minute:

```cron
* * * * * cd /path/to/application && php artisan schedule:run >> /dev/null 2>&1
```

Use your production application's actual path and PHP binary. Monitor queue workers, scheduler failures, application logs, database backups, and the `/up` health endpoint after deployment. API documentation remains restricted in production.

## Publishing the GitHub template

Publish the repository only after local quality checks and the fresh-clone smoke test pass. Keep `.env`, credentials, dependencies, logs, and generated caches out of Git; commit `.env.example`, `composer.lock`, application files, tests, workflows, and `LICENSE`.

On GitHub, run CI on the default branch. Once both jobs pass, configure a branch ruleset requiring **PHP checks and tests** and **Fresh clone setup**, then enable **Template repository** in Settings. Repository creation, hosted Actions verification, required checks, and the template setting must be completed against the actual GitHub repository.

## License

This starter kit is licensed under the [MIT license](LICENSE).

## Laravel Boost

Boost is installed as a development dependency. `boost.json` records the selected integrations. Start Sail before connecting an agent to the Boost MCP server. Machine-local agent configuration is excluded from Git.

Refresh generated guidelines and skills after dependency changes:

```bash
./vendor/bin/sail artisan boost:update --no-interaction
```
