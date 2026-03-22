# Application Flows

## 1. Registration and Onboarding Flow

```
┌─────────┐    ┌──────────┐    ┌───────────────┐    ┌───────────────┐    ┌───────────┐
│ Register │───►│ Auto     │───►│ Onboarding    │───►│ Onboarding    │───►│ Dashboard │
│ Form     │    │ Login    │    │ Step 1:       │    │ Step 2:       │    │           │
│          │    │          │    │ Pick          │    │ Set Budget    │    │           │
└─────────┘    └──────────┘    │ Categories    │    │ (weekly/      │    └───────────┘
                               └───────────────┘    │  monthly)     │
                                                    └───────────────┘
```

### Detailed Steps

1. **User visits `/register`**
   - Fills in: name, email, password, password confirmation
   - Validation: name (required, max 255), email (unique), password (min 8, confirmed)

2. **Account created, auto-login**
   - User record created with hashed password
   - `onboarding_completed_at` is NULL
   - Session regenerated, redirected to `/dashboard`

3. **Middleware redirects to onboarding**
   - `EnsureOnboardingCompleted` middleware detects NULL `onboarding_completed_at`
   - Redirects to `/onboarding/step1`

4. **Step 1: Category Selection** (`GET /onboarding/step1`)
   - Displays 7 default categories (Food, Transport, Shopping, Entertainment, Bills, Health, Other)
   - User selects at least 1 category
   - `POST /onboarding/step1`: Creates personal `categories` records copied from `default_categories`
   - Redirects to step 2

5. **Step 2: Budget Setup** (`GET /onboarding/step2`)
   - User chooses budget type (weekly or monthly) and sets amount
   - `POST /onboarding/step2`: Creates `budgets` record
   - Sets `onboarding_completed_at = now()` on user
   - Redirects to `/dashboard` with success message

6. **Dashboard accessible**
   - Onboarding middleware passes, user sees full dashboard

### Google OAuth Variant

```
┌─────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────┐
│ Click        │───►│ Google       │───►│ Callback:    │───►│ Onboard  │
│ "Login with  │    │ Consent      │    │ Find/Create  │    │ or       │
│  Google"     │    │ Screen       │    │ User         │    │ Dashboard│
└─────────────┘    └──────────────┘    └──────────────┘    └──────────┘
```

- **Existing user with google_id**: Login directly
- **Existing user with matching email**: Links google_id, login
- **New user**: Creates account (random password), starts onboarding

---

## 2. Create Expense Flow

```
┌──────────┐    ┌──────────────┐    ┌───────────────┐    ┌──────────────┐
│ Click    │───►│ Expense Form │───►│ Validate &    │───►│ Check Budget │
│ "Add     │    │ - Amount     │    │ Save Expense  │    │ Thresholds   │
│  Expense"│    │ - Category   │    │               │    │              │
└──────────┘    │ - Note       │    └───────────────┘    └──────┬───────┘
                │ - Date       │                                │
                └──────────────┘                                ▼
                                                   ┌────────────────────┐
                                                   │ If threshold hit:  │
                                                   │ 1. Create notif    │
                                                   │ 2. Dispatch email  │
                                                   │    job (queued)    │
                                                   └────────────────────┘
```

### Detailed Steps

1. **User visits `/expenses/create`**
   - Form shows: amount input, category dropdown (user's categories), note textarea, date picker

2. **User submits form** (`POST /expenses`)
   - Validation: amount (numeric, min 0), category_id (exists in user's categories), note (optional, max 65535), date (required)

3. **Expense saved**
   - Creates `expenses` record with `user_id = Auth::id()`

4. **Budget alert check** (`BudgetAlertService::checkAndNotify`)
   - Loads all user budgets (weekly + monthly)
   - For each budget, calculates spent in current period
   - Checks ratio against 50%, 80%, 100% thresholds
   - For each exceeded threshold:
     - Checks if notification already exists for this type + period (deduplication)
     - If new: creates `notifications` record
     - Dispatches `SendBudgetAlertEmailJob` to queue

5. **Redirect to expense list** with success message

---

## 3. Budget Alert Flow

```
┌──────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ Trigger:     │───►│ BudgetAlert      │───►│ For each budget:│
│ - Expense    │    │ Service          │    │ Calculate ratio │
│   CRUD       │    │ .checkAndNotify()│    │ spent / amount  │
│ - Scheduled  │    │                  │    │                 │
│   command    │    └──────────────────┘    └────────┬────────┘
└──────────────┘                                     │
                                                     ▼
                                            ┌────────────────┐
                                            │ Threshold hit? │
                                            │ 50% / 80% /100%│
                                            └───────┬────────┘
                                                    │ Yes
                                                    ▼
                                            ┌────────────────┐
                                            │ Already notified│
                                            │ this period?   │
                                            └───────┬────────┘
                                                    │ No
                                                    ▼
                                    ┌───────────────────────────┐
                                    │ 1. Create Notification    │
                                    │ 2. Dispatch email job     │
                                    └───────────────┬───────────┘
                                                    │
                                                    ▼ (Queue)
                                    ┌───────────────────────────┐
                                    │ SendBudgetAlertEmailJob   │
                                    │ - Check email_notification│
                                    │ - Send BudgetAlertMail    │
                                    └───────────────────────────┘
```

### Triggers

1. **Real-time**: After any expense create/update/delete (via `ExpenseController`)
2. **Scheduled**: `budget:check-thresholds` command runs daily at 07:00
   - Iterates all users with budgets
   - Calls `checkAndNotify` for each

### Deduplication Logic
- Notification type = `budget_{level}` or `budget_weekly_{level}`
- Checks if notification of same type exists within current period dates
- Monthly period: start of month to end of month
- Weekly period: start of week to end of week

### Email Delivery
- Job checks `user.email_notification` before sending
- Email subject varies by threshold type (uses translation keys)
- Queue driver: `database` (requires `php artisan queue:work`)

---

## 4. Recurring Expenses Flow

```
┌──────────────────┐    ┌─────────────────────┐    ┌──────────────────┐
│ Scheduler runs   │───►│ recurring:create-    │───►│ For each active  │
│ daily at 06:00   │    │ expenses command     │    │ recurring rule:  │
└──────────────────┘    └─────────────────────┘    └────────┬─────────┘
                                                            │
                                                            ▼
                                                   ┌────────────────┐
                                                   │ shouldRunToday?│
                                                   └───────┬────────┘
                                                           │ Yes
                                                           ▼
                                                   ┌────────────────┐
                                                   │ Already created│
                                                   │ today?         │
                                                   └───────┬────────┘
                                                           │ No
                                                           ▼
                                                   ┌────────────────┐
                                                   │ Has category?  │
                                                   └───────┬────────┘
                                                           │ Yes
                                                           ▼
                                                   ┌────────────────────┐
                                                   │ Create expense:    │
                                                   │ - user_id          │
                                                   │ - category_id      │
                                                   │ - amount           │
                                                   │ - note = title     │
                                                   │ - date = today     │
                                                   │ - recurring_       │
                                                   │   expense_id       │
                                                   └────────────────────┘
```

### Schedule Logic

**Weekly type:**
- Compares `day_of_week` (0-6, 0=Sunday) with today's day of week

**Monthly type:**
- Compares `day_of_month` (1-31) with today's day
- If `day_of_month` > days in current month, runs on last day of month

### Safety Checks
1. Only processes `is_active = true` recurring expenses
2. Skips if expense with same `recurring_expense_id` + today's date already exists
3. Skips if `category_id` is NULL (logs warning)
4. Wraps each creation in try/catch (logs error, continues to next)

### User Management Flow

```
User creates          User can toggle        Scheduler processes
recurring rule  ───►  active/inactive  ───►  active rules daily
(POST /recurring-     (PATCH /recurring-     (06:00 via cron)
 expenses)             expenses/{id}/toggle)
```

---

## 5. Savings Goals and Deposits Flow

```
┌──────────────┐    ┌──────────────┐    ┌──────────────────┐
│ Create Goal  │───►│ Goal exists  │───►│ Add deposits     │
│ - Name       │    │ with status  │    │ over time        │
│ - Target $   │    │ tracking     │    │ - Amount         │
│ - Deadline   │    │              │    │ - Note           │
└──────────────┘    └──────────────┘    │ - Date           │
                                        └────────┬─────────┘
                                                 │
                                                 ▼
                                        ┌──────────────────┐
                                        │ Status computed: │
                                        │ - achieved       │
                                        │ - on_track       │
                                        │ - behind         │
                                        │ - expired        │
                                        └──────────────────┘
```

### Create Goal

1. User visits `/savings-goals/create`
2. Fills in: name, target amount (min 1), deadline (today or later)
3. Goal created, appears in list ordered by deadline

### Add Deposit

1. User visits `/savings-goals/{id}/deposits`
2. Sees deposit history (paginated, newest first) and deposit form
3. Submits: amount (min 0.01), optional note, date
4. Deposit linked to goal via `savings_goal_id`

### Status Computation (real-time, not stored)

```
                          ┌──────────────────┐
                          │ current >= target │──► achieved
                          └────────┬─────────┘
                                   │ No
                                   ▼
                          ┌──────────────────┐
                          │ deadline passed? │──► expired
                          └────────┬─────────┘
                                   │ No
                                   ▼
                          ┌──────────────────────────┐
                          │ expected = target *       │
                          │ (elapsed / total months)  │
                          │                          │
                          │ current >= expected?     │
                          └──────────┬───────────────┘
                                     │
                              Yes ◄──┴──► No
                              │           │
                          on_track     behind
```

### Delete Deposit
- User can delete individual deposits
- Authorization: must own the savings goal that the deposit belongs to

---

## 6. AI Analysis Flow

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ User clicks  │───►│ Check cache  │───►│ Build data   │───►│ Call Gemini  │
│ "AI Analyze" │    │ for today    │    │ aggregation  │    │ API          │
│ (AJAX POST)  │    │              │    │              │    │              │
└──────────────┘    └──────┬───────┘    └──────────────┘    └──────┬───────┘
                           │                                       │
                     Cache hit?                              ┌─────▼───────┐
                     Yes ──► Return cached                   │ Return      │
                                                             │ markdown    │
                                                             │ analysis    │
                                                             │ (cached 1h) │
                                                             └─────────────┘
```

### Data Aggregated for AI

| Data Point | Source |
|-----------|--------|
| Spending by category (this month) | expenses grouped by category |
| Total spent this month | expenses sum |
| Total spent last month | expenses sum |
| Last 7 days daily totals | expenses grouped by date |
| Monthly budget amount | budgets where type=monthly |
| Weekly budget amount | budgets where type=weekly |
| Spent this week | expenses sum (current week) |

### Prompt Structure
- Language: Vietnamese
- Input: JSON-formatted spending data with budget utilization percentages
- Output sections: Comments, Savings Tips, Predictions

### Caching
- Key: `ai_analysis_{userId}_{Y-m-d}`
- TTL: 3600 seconds (1 hour)
- One cached result per user per day

---

## 7. Admin Flows

### Disable User Flow

```
┌─────────────┐    ┌──────────────────┐    ┌────────────────────────┐
│ Admin clicks │───►│ Toggle           │───►│ User's next request:   │
│ "Disable"   │    │ is_active=false  │    │ ActiveUserMiddleware   │
│ on user     │    │                  │    │ logs out + redirects   │
└─────────────┘    └──────────────────┘    │ to login with error    │
                                           └────────────────────────┘
```

### Manage Default Categories

```
Admin CRUD on            Affects new users          Existing users
default_categories  ───► during onboarding     ───► are NOT affected
                         (step 1 selection)
```

---

## 8. Request Lifecycle (Protected Routes)

```
Request
  │
  ▼
[web middleware group]
  │
  ├─► SetLocaleMiddleware (sets app locale from session)
  │
  ▼
[auth middleware]
  │
  ├─► Not authenticated? ──► Redirect to /login
  │
  ▼
[active middleware]
  │
  ├─► User disabled? ──► Logout + redirect to /login with error
  │
  ▼
[onboarding middleware]
  │
  ├─► Onboarding incomplete? ──► Redirect to /onboarding/step1
  │
  ▼
[Controller]
  │
  ├─► Manual auth check: $model->user_id !== Auth::id() ──► abort(403)
  │
  ▼
[Response]
```
