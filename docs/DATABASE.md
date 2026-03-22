# Database Schema

## Entity-Relationship Diagram

```
┌──────────────────┐       ┌──────────────────┐
│      users       │       │ default_categories│
│──────────────────│       │──────────────────│
│ id (PK)          │       │ id (PK)          │
│ name             │       │ name             │
│ email (unique)   │       │ icon             │
│ password         │       │ color            │
│ google_id (uniq) │       │ sort_order       │
│ email_notification│      │ timestamps       │
│ onboarding_      │       └──────────────────┘
│   completed_at   │
│ role             │
│ is_active        │
│ timestamps       │
└──────┬───────────┘
       │
       │ 1:N
       ├──────────────────────────────────────┐
       │                                      │
       ▼                                      ▼
┌──────────────────┐                ┌──────────────────┐
│   categories     │                │    budgets       │
│──────────────────│                │──────────────────│
│ id (PK)          │                │ id (PK)          │
│ user_id (FK)     │◄───┐          │ user_id (FK)     │
│ name             │    │          │ amount           │
│ icon             │    │          │ type             │
│ color            │    │          │ timestamps       │
│ timestamps       │    │          └──────────────────┘
└──────┬───────────┘    │
       │                │
       │ 1:N            │ N:1
       │                │
       ▼                │
┌──────────────────┐    │
│    expenses      │    │
│──────────────────│    │       ┌──────────────────────┐
│ id (PK)          │    │       │ recurring_expenses   │
│ user_id (FK)     │    │       │──────────────────────│
│ category_id (FK) │────┘       │ id (PK)              │
│ amount           │            │ user_id (FK)         │
│ note             │◄───────────│ category_id (FK,null)│
│ date             │  N:1       │ title                │
│ recurring_       │            │ amount               │
│   expense_id(FK) │────────────│ type                 │
│ timestamps       │            │ day_of_week          │
└──────────────────┘            │ day_of_month         │
                                │ is_active            │
       ┌────────────────┐       │ timestamps           │
       │ savings_goals  │       └──────────────────────┘
       │────────────────│
       │ id (PK)        │
       │ user_id (FK)   │
       │ name           │       ┌──────────────────┐
       │ target_amount  │       │  notifications   │
       │ deadline       │       │──────────────────│
       │ timestamps     │       │ id (PK)          │
       └───────┬────────┘       │ user_id (FK)     │
               │                │ type             │
               │ 1:N            │ message          │
               ▼                │ is_read          │
       ┌────────────────┐       │ timestamps       │
       │savings_deposits│       └──────────────────┘
       │────────────────│
       │ id (PK)        │
       │ savings_goal_id│
       │   (FK)         │
       │ amount         │
       │ note           │
       │ date           │
       │ timestamps     │
       └────────────────┘
```

## Tables

### users

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| name | varchar(255) | not null |
| email | varchar(255) | not null, unique |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | not null |
| remember_token | varchar(100) | nullable |
| google_id | varchar(255) | nullable, unique |
| email_notification | boolean | not null, default true |
| onboarding_completed_at | timestamp | nullable |
| role | varchar(255) | not null, default 'user' |
| is_active | boolean | not null, default true |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Has many `categories`
- Has many `expenses`
- Has many `budgets`
- Has many `recurring_expenses`
- Has many `savings_goals`
- Has many `notifications`

---

### categories

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| name | varchar(255) | not null |
| icon | varchar(255) | nullable |
| color | varchar(255) | nullable |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`
- Has many `expenses`
- Has many `recurring_expenses`

---

### default_categories

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| name | varchar(255) | not null |
| icon | varchar(255) | nullable |
| color | varchar(255) | nullable |
| sort_order | smallint unsigned | not null, default 0 |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Purpose:** Template categories used during onboarding. Users select from these to create their personal categories.

---

### expenses

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| category_id | bigint unsigned | FK -> categories.id, cascade delete |
| amount | decimal(12,2) | not null |
| note | varchar(255) | nullable |
| date | date | not null |
| recurring_expense_id | bigint unsigned | nullable, FK -> recurring_expenses.id, set null on delete |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`
- Belongs to `categories`
- Belongs to `recurring_expenses` (nullable)

---

### budgets

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| amount | decimal(12,2) | not null |
| type | varchar(255) | not null (values: 'weekly', 'monthly') |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`

**Notes:**
- One budget per type per user (enforced in controller)
- `category_id` was originally present but removed in migration `2026_02_19_200000`

---

### recurring_expenses

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| category_id | bigint unsigned | nullable, FK -> categories.id, cascade delete |
| title | varchar(255) | not null |
| amount | decimal(12,2) | not null |
| type | varchar(255) | not null (values: 'weekly', 'monthly') |
| day_of_week | tinyint unsigned | nullable (0=Sunday, 1=Monday, ..., 6=Saturday) |
| day_of_month | tinyint unsigned | nullable (1-31) |
| is_active | boolean | not null, default true |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`
- Belongs to `categories` (nullable)
- Has many `expenses`

**Notes:**
- `day_of_week` used when type='weekly', `day_of_month` used when type='monthly'
- For months shorter than `day_of_month`, falls back to last day of month

---

### savings_goals

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| name | varchar(255) | not null |
| target_amount | decimal(12,2) | not null |
| deadline | date | not null |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`
- Has many `savings_deposits`

---

### savings_deposits

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| savings_goal_id | bigint unsigned | FK -> savings_goals.id, cascade delete |
| amount | decimal(12,2) | not null |
| note | varchar(255) | nullable |
| date | date | not null |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `savings_goals`

---

### notifications

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK -> users.id, cascade delete |
| type | varchar(255) | not null |
| message | text | not null |
| is_read | boolean | not null, default false |
| created_at | timestamp | nullable |
| updated_at | timestamp | nullable |

**Relationships:**
- Belongs to `users`

**Type values:**
- `budget_50`, `budget_80`, `budget_100` (monthly budget alerts)
- `budget_weekly_50`, `budget_weekly_80`, `budget_weekly_100` (weekly budget alerts)

---

### password_reset_tokens

| Column | Type | Constraints |
|--------|------|-------------|
| email | varchar(255) | PK |
| token | varchar(255) | not null |
| created_at | timestamp | nullable |

---

### sessions

| Column | Type | Constraints |
|--------|------|-------------|
| id | varchar(255) | PK |
| user_id | bigint unsigned | nullable, indexed |
| ip_address | varchar(45) | nullable |
| user_agent | text | nullable |
| payload | longtext | not null |
| last_activity | int | not null, indexed |

---

### Laravel Infrastructure Tables

| Table | Purpose |
|-------|---------|
| cache | Key-value cache storage |
| cache_locks | Atomic lock management |
| jobs | Queued jobs (budget alert emails) |
| job_batches | Batch job tracking |
| failed_jobs | Failed job records for debugging |

## Relationship Summary

```
User (1) ──► (N) Category
User (1) ──► (N) Expense
User (1) ──► (N) Budget
User (1) ──► (N) RecurringExpense
User (1) ──► (N) SavingsGoal
User (1) ──► (N) Notification

Category (1) ──► (N) Expense
Category (1) ──► (N) RecurringExpense

RecurringExpense (1) ──► (N) Expense

SavingsGoal (1) ──► (N) SavingsDeposit
```

## Cascade Delete Rules

- Deleting a **User** cascades to: categories, expenses, budgets, recurring_expenses, savings_goals, notifications
- Deleting a **Category** cascades to: expenses, recurring_expenses
- Deleting a **SavingsGoal** cascades to: savings_deposits
- Deleting a **RecurringExpense** sets `recurring_expense_id` to NULL on related expenses
