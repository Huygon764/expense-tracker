---
name: Expense Tracker Implementation
overview: "Plan triển khai app expense-tracker (Laravel 12 + Blade + Tailwind), 15 phase từ tạo project mới đến deploy Docker VPS. Auth tự viết: session-based (Laravel chuẩn, session cookie http-only). Không dùng codebase có sẵn, không Breeze."
todos: []
isProject: false
---

# Plan: expense-tracker – từng phase cụ thể

## Bối cảnh

- **Tên app:** expense-tracker.
- **Bắt đầu từ không:** Tạo project Laravel mới trong thư mục `expense-tracker` (không dùng codebase có sẵn).
- **Auth:** Tự viết: session-based (Laravel guard, session cookie http-only – best practice PHP/Laravel), không Breeze.
- **Frontend:** Blade + Tailwind + Vite; dùng **yarn** (không npm).
- **Mục tiêu:** Multi-user quản lý chi tiêu, ngân sách, thống kê, AI phân tích, báo cáo, deploy Docker + VPS.

## Phạm vi tính năng (tự mô tả)

- **Auth:** Đăng ký / đăng nhập / đăng xuất (form + Google), quên mật khẩu, đổi profile.
- **Danh mục:** CRUD danh mục (mỗi user riêng); onboarding: chọn danh mục mặc định → set thu nhập hàng tháng → set ngân sách tổng (bắt buộc).
- **Chi tiêu:** CRUD, filter theo ngày/tuần/tháng/năm, search theo ghi chú, phân trang.
- **Ngân sách:** Tổng (tuần/tháng) + theo danh mục (optional), progress bar.
- **Chi tiêu định kỳ:** CRUD, chu kỳ tuần/tháng; cron tự tạo expense vào ngày set.
- **Mục tiêu tiết kiệm:** CRUD, theo dõi tiến độ, tính “mỗi tháng cần tiết kiệm”.
- **Dashboard:** Tổng quan tháng, đã chi/ngân sách, cảnh báo 50%/80%/100%, so sánh tháng trước, chart (pie theo category, bar 7 ngày), chi tiêu gần đây.
- **Thống kê:** Filter theo khoảng thời gian, chart chi tiết.
- **Cảnh báo:** In-app (icon chuông) + email qua Queue khi đạt ngưỡng ngân sách.
- **AI (Gemini):** Modal phân tích thói quen, gợi ý tiết kiệm, dự đoán chi cuối tháng.
- **Báo cáo:** Export PDF (DomPDF), Export Excel (Laravel Excel).

## Database schema (tham chiếu trong plan)

- **users:** id, name, email, password, google_id (nullable), email_notification (boolean), monthly_income (nullable, decimal 12,2), onboarding_completed_at (nullable, timestamp), timestamps (+ remember_token, email_verified_at nếu cần).
- **password_reset_tokens:** email, token, created_at.
- **categories:** id, user_id, name, icon, color, timestamps.
- **expenses:** id, user_id, category_id, amount, note (nullable), date, timestamps.
- **budgets:** id, user_id, category_id (nullable), amount, type (weekly/monthly), timestamps.
- **recurring_expenses:** id, user_id, category_id, title, amount, type, day_of_week, day_of_month, is_active, timestamps.
- **savings_goals:** id, user_id, name, target_amount, deadline, timestamps (current_amount tính tự động, không lưu DB).
- **notifications:** id, user_id, type (50%/80%/100%), message, is_read, timestamps.
- **jobs:** (Laravel queue).

---

## Phase 1: Setup – Tạo project PHP mới (Laravel từ đầu)

**Mục đích:** Tạo project Laravel mới tên **expense-tracker**, cấu hình DB + frontend stack + packages.

- **1.1 Tạo project mới**
  - `composer create-project laravel/laravel expense-tracker` – không Breeze, không copy từ codebase có sẵn.
  - PHP 8.2+, Laravel 12.
- **1.2 Database**
  - `.env`: `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (placeholder; không hardcode).
  - Tạo database MySQL (local hoặc VPS).
- **1.3 Frontend (Blade + Tailwind + Vite) – dùng yarn**
  - Cài Tailwind: `yarn add -D tailwindcss postcss autoprefixer`, init `tailwind.config.js`, `content` trỏ tới `resources/views/**/*.blade.php`. CSS entry `resources/css/app.css` với `@tailwind`; Vite build.
  - `yarn add chart.js` (dashboard & thống kê).
- **1.4 Packages Composer**
  - `laravel/socialite`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`, client Gemini (e.g. `google/generative-ai-php` hoặc HTTP).
- **1.5 Config**
  - Socialite: `config/services.php` – `google` (client_id, client_secret, redirect).
  - Mail + Queue: `.env` `MAIL_*`, queue driver; migration jobs nếu database queue.
  - Session: Laravel mặc định (session driver file/database/redis); đảm bảo session cookie httpOnly (Laravel bật sẵn trong `config/session.php`).
- **Deliverable:** Project **expense-tracker** chạy được (`php artisan serve`, `yarn dev`), MySQL OK, Tailwind + Vite OK, sẵn sàng Auth session-based.

---

## Phase 2: Auth – Session-based (tự viết, không Breeze)

**Mục đích:** Auth theo chuẩn PHP/Laravel: session-based, session cookie http-only (Laravel mặc định). Đăng ký, đăng nhập, đăng xuất, quên MK, profile, Google login.

- **2.1 Schema `users`**
  - Migration `users`: `id`, `name`, `email`, `password`, `google_id` (nullable), `email_notification` (boolean, default true), `monthly_income` (nullable, decimal 12,2), `onboarding_completed_at` (nullable, timestamp), `remember_token`, `email_verified_at` (nullable), `timestamps`. Bảng `password_reset_tokens` (Laravel chuẩn). Bảng `sessions` (Laravel có sẵn hoặc migration khi dùng session driver database).
- **2.2 Session + guard**
  - Dùng Laravel guard `web` (session): cookie session httpOnly do Laravel quản lý (`config/session.php`: `http_only` = true). Không cài thêm package JWT.
  - Route cần bảo vệ: middleware `auth` (Laravel sẵn có).
- **2.3 Đăng ký / Đăng nhập / Đăng xuất**
  - Route: get/post `register`, get/post `login`, post `logout`.
  - RegisterController: validate (name, email, password, password_confirmation), Hash::make, tạo user, `auth()->login($user)`, `request()->session()->regenerate()`, redirect dashboard/onboarding.
  - LoginController: validate (email, password), `auth()->attempt($credentials)` (với remember nếu cần), `request()->session()->regenerate()`, redirect. Logout: `auth()->logout()`, `request()->session()->invalidate()`, `request()->session()->regenerateToken()`, redirect login.
  - Blade: form register, form login (Tailwind).
- **2.4 Quên mật khẩu**
  - Route: get/post `forgot-password`, get/post `reset-password/{token}`. Laravel `Password::sendResetLink`, `Password::reset`; bảng `password_reset_tokens`; mail config; view form email + form đặt lại mật khẩu.
- **2.5 Google Login**
  - Route: get `auth/google`, get `auth/google/callback`. Socialite redirect; callback lấy user Google, tìm/tạo user (google_id, email), `auth()->login($user)`, `request()->session()->regenerate()`, redirect dashboard/onboarding.
- **2.6 Đổi thông tin profile**
  - Route (middleware `auth`): get/post `profile` (hoặc Profile/Settings). Controller: cập nhật name, email (nếu cho phép), email_notification, **monthly_income**; validate; chỉ user hiện tại.
- **Deliverable:** Auth hoạt động session-based, session cookie http-only; không Breeze, không JWT.

---

## Phase 3: Database schema đầy đủ + Categories + Onboarding

**Mục đích:** Tạo đủ bảng theo Database schema ở đầu plan; Categories CRUD; flow onboarding (chọn danh mục mặc định + set ngân sách lần đầu).

- **3.1 Migrations**
  - **users:** Đã đủ ở Phase 2.
  - **categories:** `id`, `user_id`, `name`, `icon`, `color`, `timestamps`.
  - **expenses:** `id`, `user_id`, `category_id` (FK), `amount`, `note` (nullable), `date`, `timestamps`.
  - **budgets:** `id`, `user_id`, `category_id` (nullable), `amount`, `type` (enum weekly/monthly), `timestamps`.
  - **recurring_expenses:** `id`, `user_id`, `category_id`, `title`, `amount`, `type`, `day_of_week`, `day_of_month`, `is_active`, `timestamps`.
  - **savings_goals:** `id`, `user_id`, `name`, `target_amount`, `deadline`, `timestamps` (không có cột current_amount).
  - **notifications:** `id`, `user_id`, `type` (string: 50%, 80%, 100%), `message`, `is_read`, `timestamps`.
  Chạy migrations theo thứ tự (users → categories → expenses, budgets, …).
- **3.2 Models**
  - Eloquent: `User`, `Category`, `Expense`, `Budget`, `RecurringExpense`, `SavingsGoal`, `Notification` với quan hệ (hasMany, belongsTo), fillable, casts.
- **3.3 Categories CRUD**
  - Resource controller: index, create, store, edit, update, destroy. Scope theo `user_id`.
- **3.4 Onboarding flow**
  - Middleware hoặc logic: user có `onboarding_completed_at` null → redirect onboarding.
  - Trang onboarding 3 bước:
    - **Bước 1:** Chọn danh mục mặc định (danh sách mẫu hoặc tạo sẵn).
    - **Bước 2:** Set thu nhập hàng tháng (`monthly_income`) – lưu vào `users.monthly_income`.
    - **Bước 3:** Set ngân sách tổng (tuần/tháng) – bắt buộc, lưu vào `budgets`.
  - Sau khi hoàn thành bước 3: `user->onboarding_completed_at = now()`, save → redirect Dashboard.
- **Deliverable:** DB đủ bảng theo schema trên, CRUD categories, flow đăng ký xong → onboarding 3 bước → Dashboard.

---

## Phase 4: Expenses CRUD + Search + Filter + Phân trang

- Resource `ExpenseController`: index (list), create, store, edit, update, destroy. Luôn scope `user_id`.
- **Filter:** Theo khoảng ngày (ngày/tuần/tháng/năm) – query params hoặc form.
- **Search:** Theo `note` (LIKE).
- **Phân trang:** `->paginate()` trên index.
- Form: amount, category_id (dropdown), note, date.

---

## Phase 5: Budgets + Progress tracking

- CRUD budgets: ngân sách tổng (category_id = null) và ngân sách theo category (optional). Trường `type`: weekly / monthly.
- Dashboard (hoặc trang budgets): tính “đã chi” trong kỳ (theo type) so với `amount`, hiển thị progress bar. Logic tính: sum(expenses) trong khoảng ngày tương ứng (tuần hiện tại / tháng hiện tại).

---

## Phase 6: Recurring Expenses

- CRUD recurring_expenses: title, amount, category_id, type (weekly/monthly), day_of_week hoặc day_of_month.
- **Cron/Command:** Job chạy hàng ngày (schedule): kiểm tra các bản ghi `is_active`, nếu hôm nay trùng day_of_week hoặc day_of_month thì tạo bản ghi `expenses` tương ứng (user_id, category_id, amount, note từ title, date = today).

---

## Phase 7: Savings Goals

- CRUD savings_goals: name, target_amount, deadline (không có cột current_amount).
- **current_amount (tự động tính):** = SUM(monthly_income − tổng chi tiêu) theo từng tháng, cộng dồn từ trước đến nay (tiết kiệm thực tế = thu nhập tháng − chi tiêu tháng, rồi sum các tháng). Dữ liệu: `users.monthly_income` (có thể thay đổi theo tháng nếu lưu lịch sử, hoặc dùng giá trị hiện tại cho quá khứ), `expenses` theo tháng. Hiển thị progress bar: current_amount / target_amount; “mỗi tháng cần tiết kiệm” = (target_amount − current_amount) / số tháng còn lại đến deadline.
- **Profile/Settings:** Cho phép sửa `monthly_income` (đã nêu ở Phase 2.6); dùng giá trị này khi tính current_amount (và có thể dùng cho các tháng trước nếu không lưu lịch sử thu nhập từng tháng).

---

## Phase 8: Dashboard + Charts

- **Nội dung:** Tổng quan tháng hiện tại; đã chi / ngân sách (progress bar); cảnh báo 50%/80%/100%; so sánh với tháng trước; pie chart chi tiêu theo category; bar chart 7 ngày gần nhất; danh sách chi tiêu gần đây.
- **Chart:** Dùng Chart.js (đã cài ở Phase 1), truyền data từ controller (aggregate theo category, theo ngày).

---

## Phase 9: Notifications + Email Queue

- **In-app:** Bảng `notifications`: khi đạt 50%/80%/100% ngân sách (check trong logic dashboard hoặc job), tạo notification (type, message). Icon chuông trên layout: đếm chưa đọc, list dropdown, đánh dấu đã đọc.
- **Email:** Job queue: khi tạo notification (hoặc khi đạt ngưỡng), dispatch job gửi email (chỉ nếu user.email_notification = true). Dùng Laravel Mail + Queue.

---

## Phase 10: Phân tích AI (Gemini)

- Modal (hoặc trang) “Phân tích AI”: nút mở, gửi dữ liệu tổng hợp (chi tiêu theo category, theo thời gian, ngân sách – không gửi raw PII) lên Gemini API.
- Prompt: nhận xét thói quen, gợi ý tiết kiệm, dự đoán chi tiêu cuối tháng. Hiển thị response trong modal. Dùng Google Gemini (free tier), key trong `.env`.

---

## Phase 11: Reports (Export PDF / Excel)

- Trang hoặc section “Báo cáo”: chọn khoảng thời gian.
  - **PDF:** DomPDF, view Blade in báo cáo (tổng chi, theo category, bảng chi tiêu) → stream/download.
  - **Excel:** Laravel Excel, export cùng dataset (hoặc cấu trúc tương tự).

---

## Phase 12: Thống kê (Statistics)

- Trang Statistics: filter theo ngày/tuần/tháng/năm; chart chi tiết (có thể tái dùng component Chart.js từ Dashboard); bảng/số liệu tùy thiết kế.

---

## Phase 13: UI/UX polish + Responsive

- Đồng bộ layout (Blade + Tailwind): sidebar/nav, màu, typography; responsive mobile/tablet; loading/feedback khi submit; thông báo lỗi/thành công nhất quán.

---

## Phase 14: Testing + Fix bugs

- Feature test: auth, onboarding, expenses CRUD, budgets, recurring (tạo expense từ recurring). Fix lỗi phát sinh.

---

## Phase 15: Docker + Deploy VPS

- Dockerfile (PHP-FPM + Composer), docker-compose: app, MySQL, Nginx, queue worker (và scheduler nếu dùng cron trong container). Config Nginx, env trên VPS. Chạy migrations, queue worker, cron cho recurring + scheduler.

---

## Lưu ý thực hiện

- **Security:** Không đọc/ghi `.env` hay credentials trong plan; dùng placeholder và hướng dẫn cấu hình.
- **Thứ tự:** Phase 1 → 2 → 3 là nền tảng; Phase 4–7 là core domain; Phase 8–12 là dashboard, thống kê, AI, báo cáo; Phase 13–15 là hoàn thiện và deploy.
- **Codebase:** Toàn bộ trong project **expense-tracker** tạo ở Phase 1. Package manager frontend: **yarn**.

