# Expense Tracker

Personal expense management application built with Laravel 12. Track daily expenses, set budgets, manage recurring payments, save toward goals, and get AI-powered spending analysis.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade templates, Tailwind CSS v4, Chart.js |
| Build | Vite 7 |
| Database | MySQL 8+ (SQLite for tests) |
| Queue | Database driver |
| Auth | Laravel built-in + Google OAuth (Socialite) |
| AI | Google Gemini API (`gemini-2.5-flash`) |
| PDF | barryvdh/laravel-dompdf |
| Excel | Maatwebsite/Excel 3.x |
| Mail | SMTP (Mailtrap for dev) |
| i18n | English, Vietnamese |

## System Requirements

- PHP >= 8.2 with extensions: `mbstring`, `xml`, `ctype`, `json`, `bcmath`, `pdo_mysql`
- Composer 2.x
- Node.js >= 18 + npm (or yarn)
- MySQL 8+ (or MariaDB 10.5+)
- Git

## Installation

### 1. Clone the repository

```bash
git clone <repo-url> expense-tracker
cd expense-tracker
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure the following:

#### Database

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Google OAuth (optional)

Get credentials from [Google Cloud Console](https://console.cloud.google.com/apis/credentials):

```dotenv
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
# GOOGLE_REDIRECT_URI defaults to APP_URL/auth/google/callback
```

#### Google Gemini AI (optional)

Get API key from [Google AI Studio](https://aistudio.google.com/apikey):

```dotenv
GEMINI_API_KEY=your_api_key
# GEMINI_MODEL=gemini-2.5-flash   (default, optional)
```

#### Mail (optional, for budget alerts)

Using [Mailtrap](https://mailtrap.io) for development:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="hello@example.com"
```

#### Locale

```dotenv
APP_LOCALE=en          # or 'vi' for Vietnamese
APP_FALLBACK_LOCALE=en
```

### 4. Database setup

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- 7 default expense categories
- Admin account
- Test user account

### 5. Build frontend assets

```bash
npm run build
```

### 6. Start the application

**Quick start (all services):**

```bash
composer dev
```

This concurrently runs: web server, queue worker, log viewer (Pail), and Vite dev server.

**Or start individually:**

```bash
php artisan serve          # Web server at http://localhost:8000
npm run dev                # Vite dev server (HMR)
php artisan queue:work     # Queue worker (for email notifications)
```

### 7. Scheduled tasks (production)

Add to crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

This runs:
- `recurring:create-expenses` daily at 06:00 -- creates expenses from active recurring rules
- `budget:check-thresholds` daily at 07:00 -- checks budgets and sends alert notifications

## Default Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | password |
| User | test@example.com | password |

## Key Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `APP_KEY` | Yes | Auto-generated via `key:generate` |
| `DB_*` | Yes | MySQL connection settings |
| `GOOGLE_CLIENT_ID` | No | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | No | Google OAuth client secret |
| `GEMINI_API_KEY` | No | Google Gemini API key for AI analysis |
| `GEMINI_MODEL` | No | Gemini model name (default: `gemini-2.5-flash`) |
| `MAIL_*` | No | SMTP settings for budget alert emails |
| `QUEUE_CONNECTION` | No | Default: `database` |
| `APP_LOCALE` | No | Default: `en` (supports `en`, `vi`) |

## Running Tests

```bash
php artisan test
```

Tests use SQLite in-memory database (configured in `phpunit.xml`).
