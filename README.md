# Thimi Library

Laravel 13, Inertia 3, Vue 3, Tailwind CSS, and MySQL library-management scaffold.

## Setup

1. Create a MySQL database named `thimilibrary`.
2. Copy `.env.example` to `.env` and set the database credentials for your local MySQL account. This workspace's local `.env` is configured for the provided `root` account.
3. Run `composer install`, `npm install`, and `php artisan key:generate`.
4. Run `php artisan migrate` and `npm run build`.
5. Create the initial administrator with `php artisan library:make-superadmin`. The command prompts for the account name, email, and password without storing a default password in the repository.
6. Start the app with `composer dev` and sign in at `/login`.

Password-reset emails use Laravel's configured mailer. The example environment logs mail locally; configure SMTP before using email resets outside development.

## Continuous deployment

Pushes to `develop` run CI checks. Pushes to `production` deploy after those checks pass; `staging` is not active yet. Deployment uses SSH and `rsync` to a Linux server. The server must have PHP 8.3 with the extensions required by `composer.lock`, `rsync`, and a web server configured with this app's document root set to `<deployment path>/public`.

Prepare a dedicated deployment directory on the server (the application root, not the public web root), configure its `.env` with production settings and database credentials, and make `storage` plus `bootstrap/cache` writable by the PHP/web-server user. Keep that `.env` and `storage` on the server; the workflow deliberately excludes both from synchronization. Ensure the SSH deployment user can write the application directory and run PHP CLI commands there.

In GitHub, create a `production` environment under **Settings > Environments**, then add these values to that environment:

- Variables: `DEPLOY_HOST` (server hostname or IP), `DEPLOY_USER` (SSH account), `DEPLOY_PATH` (absolute path to the Laravel application root), and optionally `DEPLOY_PORT` (SSH port; defaults to `22`).
- Secrets: `DEPLOY_SSH_KEY` (private key for the deployment account) and `DEPLOY_KNOWN_HOSTS` (the server's verified SSH host-key line, as produced by `ssh-keyscan -p <port> <host>` and checked against the host's published fingerprint).

Install the matching public key in the deployment account's `~/.ssh/authorized_keys`. Do not use a root SSH account. Since synchronization uses `rsync --delete`, point `DEPLOY_PATH` only at this application's dedicated directory; unrelated files there will be removed. The first deployment expects the directory, `.env`, writable Laravel directories, and database to be ready. Migrations run with `--force` on each successful deployment.

## Roles

- `superadmin`: catalog and user management.
- `librarian`: catalog management.
- `reader`: informative, read-only dashboard.

Users are created by a superadmin; public registration is disabled. Categories, publishers, books, and users track `created_by`, `updated_by`, and timestamps.
