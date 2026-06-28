# ⚡ FinFlow — Premium Financial & SaaS Management Boilerplate

FinFlow is a high-fidelity, production-ready personal finance tracker and SaaS client management boilerplate. Built with a modern monolithic architecture using **Laravel 11**, **Inertia.js**, and **Vue 3 (Composition API)**, it features premium aesthetics, dark-mode support, micro-animations, and full-height responsive navigation.

This repository is optimized for developers seeking a robust, commercially saleable SaaS starter kit or a highly polished self-hosted financial manager.

---

## 🎯 Key Capabilities & Commercial Features

### 1. 📊 Income & Expense Ledger
*   **Real-time Balances**: Instantly tracks total net worth, monthly income, and monthly expenses.
*   **Visual Budget Targets**: Dynamic progress bars that automatically shift color based on consumption thresholds (Green $\rightarrow$ Yellow warning $\rightarrow$ Red alert) to warn users as they approach limits.
*   **Accounts Management**: Support for multiple account types (Cash, Bank, Mobile Wallet, etc.) with custom initial balances. **Cash** is automatically seeded as the default account upon sign-up.
*   **Category Management**: Seeding of 10 essential financial categories for immediate use.

### 2. 🔑 SaaS License Tracker & Automated Billing
*   **Subscription Logs**: Records active/inactive client subscriptions, pricing packages, billing cycles, and next renewal dates.
*   **Automated Payment Ledger**: Logs client subscription payments directly to the transaction ledger and automatically advances the renewal date by a billing cycle.
*   **MRR & ARR Tracking**: Real-time calculation of Monthly Recurring Revenue (MRR) and Annual Recurring Revenue (ARR) on a unified dashboard.

### 3. 🛠️ Superadmin Management Panel
*   **User Ban / Unban Toggle**: Immediately terminates banned user sessions and blocks system access via a global `AbortsIfBanned` middleware.
*   **Detailed Module Permissions**: Superadmin can dynamically enable/disable access to modules (Ledger, Budgets, SaaS, Loans, Recurring Items) per user.
*   **Dynamic Link Hiding**: Sidebar menu links are automatically hidden on the client-side for any modules not authorized for the user.
*   **Safe Deletion**: Complete user deletion flow with safety guard clauses protecting against self-action.

### 4. 🔔 Firebase Push Notifications
*   **Opt-in Flow**: Native browser push notification subscription card in the header bar.
*   **Service Worker Registry**: Integrates custom service workers (`firebase-messaging-sw.js`) to handle notifications background/foreground dispatching.

---

## 🛠️ Tech Stack

*   **Core Framework**: [Laravel 11](https://laravel.com) (PHP 8.2+)
*   **Frontend Bridge**: [Inertia.js](https://inertiajs.com) (Seamless routing & state sharing)
*   **UI Layer**: [Vue 3](https://vuejs.org) (Composition API, `<script setup>`) & [Tailwind CSS](https://tailwindcss.com)
*   **Data Layer**: SQLite (Highly portable, zero-configuration local database)
*   **Testing Suite**: [Pest PHP](https://pestphp.com) (Modern testing framework)

---

## 🚀 Setup & Installation

Follow these steps to run the application locally:

### Prerequisites
*   **PHP** (8.2 or higher)
*   **Composer**
*   **Node.js** & **npm**

### Step 1: Clone & Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### Step 2: Configure Environment
Copy the example environment configuration:
```bash
cp .env.example .env
```
By default, the application is pre-configured to use SQLite. If you wish to enable **Firebase Push Notifications**, add your credentials in `.env`:
```env
FIREBASE_API_KEY=your_api_key
FIREBASE_AUTH_DOMAIN=your_auth_domain
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_STORAGE_BUCKET=your_storage_bucket
FIREBASE_MESSAGING_SENDER_ID=your_messaging_sender_id
FIREBASE_APP_ID=your_app_id
FIREBASE_VAPID_KEY=your_public_vapid_key
```

### Step 3: Initialize Database
Create the local SQLite database file and run migrations:
```bash
# Create SQLite file
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### Step 4: Build Assets & Start Servers
Start the Vite development compiler and local PHP development server:
```bash
# Terminal 1: Vite compiler
npm run dev

# Terminal 2: Laravel server
php artisan serve
```
The application will be accessible at [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 🧪 Testing Suite

We maintain a strict quality assurance suite with **86 automated Pest tests** covering authentication, module permission gates, ledger CRUD, report charts, push notifications, and superadmin controllers.

Run the test suite using:
```bash
php artisan test
```

### Expected Output
```text
Tests:  86 passed (315 assertions)
Time:   1.66s
```

---

## 📂 Project Architecture

Key application entrypoints and resources:
*   **Database Schema**: [database/migrations/](file:///Users/morshedhabib/Sites/budget_management/database/migrations/)
*   **Eloquent Models**: [app/Models/](file:///Users/morshedhabib/Sites/budget_management/app/Models/) (User, Account, Category, Budget, Transaction, Client, License, PremiumServiceOrder)
*   **Controllers**: [app/Http/Controllers/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/) (DashboardController, AccountController, CategoryController, TransactionController, ReportController, LicenseController, SuperadminController, PremiumServiceOrderController)
*   **Inertia Middleware**: [app/Http/Middleware/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Middleware/) (HandleInertiaRequests, AbortsIfBanned, CheckModulePermission, EnsureUserIsSuperadmin)
*   **Vue Components & Layouts**: [resources/js/](file:///Users/morshedhabib/Sites/budget_management/resources/js/)
*   **Route Definitions**: [routes/web.php](file:///Users/morshedhabib/Sites/budget_management/routes/web.php)
*   **Automated Tests**: [tests/Feature/](file:///Users/morshedhabib/Sites/budget_management/tests/Feature/) (BudgetManagementTest, ReportTest, LicenseTest, SuperadminTest, PremiumServiceOrderTest)

---

## 📄 License

This software is open-source and licensed under the [MIT license](https://opensource.org/licenses/MIT). Developed by **PRANTIK-SOFT** (Mobile: +8801735254295, Email: mhsohel017@gmail.com).
