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

## Roles

- `superadmin`: catalog and user management.
- `librarian`: catalog management.
- `reader`: informative, read-only dashboard.

Users are created by a superadmin; public registration is disabled. Categories, publishers, books, and users track `created_by`, `updated_by`, and timestamps.
