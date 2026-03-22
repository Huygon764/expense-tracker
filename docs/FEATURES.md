# Features

## 1. Authentication

### 1.1 Registration
- Email/password registration
- Validation: name (required, max 255), email (unique), password (min 8, confirmed)
- Auto-login after registration
- Redirects to onboarding flow

### 1.2 Login
- Email/password login
- "Remember me" option
- Session regeneration on login/logout

### 1.3 Google OAuth
- One-click Google sign-in via Laravel Socialite
- Links Google account to existing email if found
- Creates new account if no match
- Auto-login after OAuth callback

### 1.4 Password Reset
- "Forgot password" sends reset link via email
- Token-based reset with email + new password
- Validation: email (required), password (min 8, confirmed)

### 1.5 Profile Management
- Update display name
- Toggle email notifications for budget alerts
- Route: `GET/PATCH /profile`

---

## 2. Onboarding

Two-step onboarding flow for new users. Users cannot access the app until onboarding is completed.

### Step 1: Category Selection
- Displays 7 default categories: Food, Transport, Shopping, Entertainment, Bills, Health, Other
- User selects at least 1 category
- Selected categories are copied to the user's personal category list
- Validation: `default_category_ids` (required, array, min 1, each must exist in `default_categories`)

### Step 2: Budget Setup
- User sets their first budget (weekly or monthly)
- Prevents duplicate budget types
- Marks onboarding as completed (`onboarding_completed_at` timestamp)
- Validation: `type` (required, weekly|monthly), `amount` (required, numeric, min 1)

### Middleware Enforcement
- `EnsureOnboardingCompleted` middleware redirects users to step 1 if `onboarding_completed_at` is null
- Exceptions: logout and onboarding routes are always accessible

---

## 3. Categories

User-specific expense categories with visual identifiers.

### Features
- CRUD operations for personal categories
- Each category has: name, icon (optional), color (optional)
- Categories are scoped to the authenticated user
- Used by: expenses, recurring expenses, dashboard charts, reports

### Validation
- `name`: required, string, max 255
- `icon`: nullable, string, max 255
- `color`: nullable, string, max 255

---

## 4. Expenses

Core module for tracking daily spending.

### Features
- CRUD operations for expenses
- Each expense has: amount, category, note (optional), date
- Linked to recurring expense if auto-generated
- Filtering by period: today, this week, this month, this year
- Search by note text
- Pagination: 15 per page
- Triggers budget alert check after create/update/delete

### Validation
- `amount`: required, numeric, min 0
- `category_id`: required, must exist in user's categories
- `note`: nullable, string, max 65535
- `date`: required, date

---

## 5. Budgets

Total spending limits for weekly or monthly periods.

### Features
- Create weekly and/or monthly budgets (one per type per user)
- View budgets with current spent amount
- Edit budget amount or type
- Delete budgets
- Spent amount calculated from expenses within the current period

### Validation
- `type`: required, weekly|monthly
- `amount`: required, numeric, min 1
- Duplicate type prevention (error if same type already exists)

### Budget Alerts
Triggered automatically when expense spending reaches thresholds:

| Threshold | Notification Type (Monthly) | Notification Type (Weekly) |
|-----------|----------------------------|---------------------------|
| 50% | `budget_50` | `budget_weekly_50` |
| 80% | `budget_80` | `budget_weekly_80` |
| 100% | `budget_100` | `budget_weekly_100` |

- Notifications created in DB + email dispatched via queue
- Each threshold fires only once per period (deduplication by type + date range)
- Email only sent if user has `email_notification` enabled

---

## 6. Recurring Expenses

Automated expense creation on a schedule.

### Features
- Define recurring rules: title, amount, category, frequency (weekly/monthly)
- Weekly: specify day of week (0=Sunday through 6=Saturday)
- Monthly: specify day of month (1-31, handles short months)
- Toggle active/inactive status
- View next scheduled run date
- Associated expenses track the source via `recurring_expense_id`

### Validation
- `title`: required, string, max 255
- `amount`: required, numeric, min 0
- `category_id`: nullable, must exist in user's categories
- `type`: required, weekly|monthly
- `day_of_week`: required if weekly, integer 0-6
- `day_of_month`: required if monthly, integer 1-31

### Scheduled Execution
- Command: `php artisan recurring:create-expenses`
- Runs daily at 06:00 via Laravel scheduler
- Skips if expense already exists for today (prevents duplicates)
- Skips if recurring rule has no category assigned
- Supports `--dry-run` flag for testing

---

## 7. Savings Goals

Track progress toward financial targets.

### Features
- Create goals with name, target amount, and deadline
- Add deposits (amount, note, date) toward each goal
- Delete individual deposits
- Computed attributes:
  - `current_amount`: sum of all deposits
  - `progress_percentage`: (current / target) * 100, capped at 100%
  - `monthly_needed`: remaining amount / months until deadline
  - `status`: achieved | expired | on_track | behind
- Goals ordered by deadline
- Deposits paginated 15 per page, ordered by date DESC

### Goal Validation
- `name`: required, string, max 255
- `target_amount`: required, numeric, min 1
- `deadline`: required, date, must be today or later

### Deposit Validation
- `amount`: required, numeric, min 0.01
- `note`: nullable, string, max 255
- `date`: required, date

### Status Logic
- **achieved**: current_amount >= target_amount
- **expired**: deadline has passed and target not reached
- **on_track**: current savings meet or exceed expected progress based on elapsed time
- **behind**: current savings are below expected progress

---

## 8. Dashboard

Financial overview for the authenticated user.

### Data Displayed
- Monthly budget amount and spent this month
- Spent last month (comparison)
- Weekly budget and spent this week (if weekly budget exists)
- Budget alert banners at 50%, 80%, 100% thresholds
- Pie chart: expenses by category (current month)
- Bar chart: daily expenses for the last 7 days
- Recent 10 expenses list

### Chart Details
- Pie chart uses category colors; uncategorized shown as gray "Other"
- Bar chart labels use Vietnamese day abbreviations (T2-CN) with dd/mm date format

---

## 9. Statistics

Detailed spending analysis with flexible date ranges.

### Features
- Period selection: today, this week, this month, this year, custom range
- Total spent in current period
- Comparison with previous equivalent period (amount + percentage change)
- Average spending per day
- Pie chart: top 5 categories
- Bar chart: adapts to period type
  - Today: single bar
  - Week: 7 daily bars
  - Month: daily bars for the month
  - Year: 12 monthly bars
  - Custom: up to 31 data points
- Expense list with pagination (15 per page)

### Validation (custom period)
- `date_from`: required, date
- `date_to`: required, date, after_or_equal date_from

---

## 10. AI Analysis

AI-powered spending insights using Google Gemini.

### Features
- Analyzes current month's spending patterns
- Aggregates: spending by category, monthly comparison, last 7 days trend, budget utilization
- Returns structured analysis in Vietnamese:
  - Comments on spending habits
  - Savings suggestions
  - Spending predictions
- Results cached per user per day (1 hour TTL)
- Requires `GEMINI_API_KEY` configuration

### Error Handling
- Returns 401 if not authenticated
- Returns 400 if API key not configured
- Returns 500 if Gemini API fails (error logged)

---

## 11. Reports

Export expense data as PDF or Excel.

### Features
- Select date range (date_from, date_to)
- PDF report: generated with dompdf, rendered from Blade template
- Excel report: generated with Maatwebsite/Excel
  - Columns: Date, Category, Amount, Note
  - Includes total row at bottom
- Filename format: `report-{dateFrom}-{dateTo}.{pdf|xlsx}`

### Report Data
- Total spent in range
- Breakdown by category (sorted by total DESC)
- Individual expense list with category names

### Validation
- `date_from`: required, date
- `date_to`: required, date, after_or_equal date_from

---

## 12. Notifications

In-app and email notification system for budget alerts.

### Features
- In-app notifications stored in `notifications` table
- Unread count displayed in app layout (via View Composer)
- 8 most recent notifications shown in dropdown
- Full notification list page with pagination (15 per page)
- Mark individual notification as read
- Mark all notifications as read
- Email notifications dispatched via queue (`SendBudgetAlertEmailJob`)
- Email respects user's `email_notification` preference

### Notification Types
- `budget_50`, `budget_80`, `budget_100` (monthly)
- `budget_weekly_50`, `budget_weekly_80`, `budget_weekly_100` (weekly)

---

## 13. Admin Panel

Management tools for administrators.

### User Management (`/admin/users`)
- View all non-admin users with search (by name or email)
- Stats: total users, active count, disabled count
- Toggle user active/inactive status
- Cannot toggle self or other admins
- Disabled users are logged out on next request (via `ActiveUserMiddleware`)
- Pagination: 15 per page

### Default Category Management (`/admin/categories`)
- CRUD for default categories used in onboarding
- Fields: name, icon (max 10 chars), color (max 7 chars, hex), sort_order
- Categories ordered by sort_order, then name

---

## 14. Multi-Language Support

### Supported Languages
- English (`en`)
- Vietnamese (`vi`)

### Implementation
- Language switcher at `GET /language/{locale}`
- Locale stored in session
- `SetLocaleMiddleware` applies locale on every request
- All user-facing strings use `__('messages.key')` translation helper
- Translation files: `lang/{en,vi}/messages.php`, `lang/vi/validation.php`
