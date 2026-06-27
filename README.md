# 🪙 Premium Personal Finance & Budgeting SaaS Platform

[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=for-the-badge&logo=inertia)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![Testing - Pest](https://img.shields.io/badge/Tests-Pest_69_Passed-00C7B7?style=for-the-badge&logo=pest)](https://pestphp.com)
[![License - MIT](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](https://opensource.org/licenses/MIT)

An ultra-modern, high-fidelity Personal Finance and SaaS License Tracking application built on a premium full-stack architecture. Perfect as a ready-to-launch product, template, or premium SaaS starter kit. It combines the speed of **Laravel 11**, the interactive power of **Vue 3 (Composition API)** via **Inertia.js**, and the beauty of custom-crafted **Tailwind CSS**.

---

## 🎯 Value Proposition & SaaS Readiness

This repository is built to **commercial standards**, going beyond simple toy apps. It serves as a high-value blueprint for building or selling modern financial technology:

*   🚀 **Production-Ready Core**: Solid foundation using Form Requests, dedicated Database Migrations, model relationship cascades, and robust Service classes.
*   💳 **Double-Entry Fund Transfers**: Bulletproof transaction logic that keeps account histories synchronized during internal transfers.
*   📈 **Smart Forecasting Engine**: Built-in savings projections and financial health advising algorithms based on category consumption patterns.
*   🔑 **Integrated SaaS License Manager**: Built-in tool for tracking clients, products, and recurring schedules. Perfect for micro-SaaS developers to track MRR/ARR or simulate a SaaS subscription business.
*   🔔 **FCM v1 Notification Gateway**: Fully implemented server-side push notification engine utilizing dynamic Google Service Account OAuth2 JWT signing—completely free of bloated third-party SDK dependencies.
*   🧪 **100% Green Test Suite**: 69 automated Pest feature tests verifying all business limits, data mutations, security boundaries, and FCM commands.

---

## 🛠️ Features Breakdown

### 📊 Financial Insights Dashboard
An interactive center showing your complete financial health at a glance:
*   **Net Worth Tracker**: Dynamically aggregates balances across all active bank, cash, and card accounts.
*   **Active Budget Progress**: Category budget limits feature custom progress bars that transition colors dynamically (**Green** $\rightarrow$ **Yellow Alert** $\rightarrow$ **Red Limit Exceeded**).
*   **In-Place Action Modals**: Perform ledger entries, categories management, and quick fund transfers without reloading the page.

### 💳 Accounts & Double-Entry Transfers
Complete control over your cash flow:
*   **Multi-Account Ledger**: Manage Bank Accounts, Credit Cards, and Cash with customized display colors and starting balances.
*   **Fund Transfers**: Moves money between accounts using dual-linked transactions (creates an offsetting expense/income pair linked by `transfer_transaction_id`). Modifying or deleting one side automatically keeps the other side synchronized.

### 🤝 Peer-to-Peer Loans & Debts
Track what you owe and what is owed to you:
*   **Active Debt Tracking**: Record lent or borrowed transactions with contacts, due dates, and interest details.
*   **Repayment Ledger**: Log incremental repayments which automatically update the loan balance.
*   **Overdue & Outstanding Summary**: Dashboard metrics for *Total Receivables*, *Total Payables*, and overdue counters.

### ⏰ Advanced Recurring Schedules
Automate repeating transactions and bills:
*   **Custom Frequencies**: Set weekly, monthly, or quarterly schedules.
*   **Multi-Type Schedules**: Supports recurring expenses, loan installment payments, or recurring loan creations.
*   **Process & Skip System**: Interactively trigger a scheduled run manually (which writes to the ledger and increments the next due date) or skip a cycle.

### 🔑 SaaS License & Client Tracker
A powerful mini-CRM built directly into the system for software developers and freelancers:
*   **MRR & ARR Analytics**: Instant calculation of Monthly Recurring Revenue and Annual Recurring Revenue.
*   **Automated Ledger Ingestion**: Automatically records incoming subscription payments as income transactions and advances the next renewal date.

### 🔔 FCM Push Notifications Engine
Keep users engaged and informed:
*   **Dynamic OAuth2 Signing**: Generates secure Google OAuth2 tokens on-the-fly using your private service account key.
*   **Daily Reminder Queue**: Includes an artisan command `reminders:send-notifications` to scan and send alerts for:
    *   Upcoming and overdue recurring schedule items.
    *   Loans reaching their due date.
    *   SaaS licenses expiring or renewing in the next 7 days.

---

## 📂 Project Architecture (Developer Blueprint)

Explore the clean, decoupled codebase structure directly in your IDE:

*   **Database & Migration Schemas**: [database/migrations/](file:///Users/morshedhabib/Sites/budget_management/database/migrations/) — Database layout including strict foreign keys, cascading indices, and transaction-linking schemas.
*   **Models (Eloquent ORM)**: [app/Models/](file:///Users/morshedhabib/Sites/budget_management/app/Models/)
    *   [User.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/User.php) (Includes FCM tokens relation)
    *   [Account.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Account.php) (Financial institutions, cards, cash)
    *   [Budget.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Budget.php) (Spend limits configuration)
    *   [Category.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Category.php) (Income & Expense classifications)
    *   [Transaction.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Transaction.php) (Core ledger records)
    *   [Loan.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Loan.php) (Peer-to-peer agreements)
    *   [RecurringSchedule.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/RecurringSchedule.php) (Repeating task engine)
    *   [Client.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Client.php) / [License.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/License.php) (SaaS client subscriptions)
*   **Business Logic Controllers**: [app/Http/Controllers/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/)
    *   [DashboardController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/DashboardController.php) (Home aggregates and budget metrics)
    *   [AccountController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/AccountController.php) / [TransferController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/TransferController.php) (Account & transfer control)
    *   [LoanController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/LoanController.php) (Lent/borrowed flow & statistics)
    *   [RecurringScheduleController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/RecurringScheduleController.php) (Automation & processing rules)
    *   [LicenseController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/LicenseController.php) (MRR/ARR SaaS management)
    *   [ReportController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/ReportController.php) (Forecasting & charts builder)
*   **Notification Commands & Services**:
    *   [SendReminderNotifications.php](file:///Users/morshedhabib/Sites/budget_management/app/Console/Commands/SendReminderNotifications.php) (Daily CLI scheduler)
    *   [FirebaseService.php](file:///Users/morshedhabib/Sites/budget_management/app/Services/FirebaseService.php) (Token exchange & FCM v1 client)
*   **Strict Requests Validation**: [app/Http/Requests/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Requests/)
*   **Web Routes**: [routes/web.php](file:///Users/morshedhabib/Sites/budget_management/routes/web.php)
*   **Interactive Vue Pages**: [resources/js/Pages/](file:///Users/morshedhabib/Sites/budget_management/resources/js/Pages/) (Dashboard, Reports, Licenses, Loans, Recurring)
*   **Automated Tests**: [tests/Feature/](file:///Users/morshedhabib/Sites/budget_management/tests/Feature/) (69 highly focused test scripts)

---

## 🚀 Quick Setup & Installation

Get your local copy of the application up and running within minutes:

### 1. Clone & Install Dependencies
First, install the required PHP and frontend dependencies:
```bash
# Install Composer dependencies
composer install

# Install NPM dependencies
npm install
```

### 2. Environment Configuration
Create a local `.env` file from the repository template:
```bash
cp .env.example .env
```
Laravel 11 uses a local SQLite database by default. If you wish to enable the FCM Notification Reminders, configure your Firebase Credentials in `.env`:
```env
FIREBASE_API_KEY="AIzaSy..."
FIREBASE_PROJECT_ID="your-project-id"
FIREBASE_CLIENT_EMAIL="your-service-account@your-project.iam.gserviceaccount.com"
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
```

### 3. Database Initialization
Generate the application key, create the SQLite database file, and run all schema migrations:
```bash
# Generate key
php artisan key:generate

# Initialize SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed default test user (test@example.com / password)
php artisan db:seed
```

### 4. Boot Up Servers
Open two terminal windows to run the development servers concurrently:
```bash
# Terminal 1: Run Vite compilation
npm run dev

# Terminal 2: Run Laravel local server
php artisan serve
```

Access the portal in your browser at: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**. 
Log in with credentials:
- **Email**: `test@example.com`
- **Password**: `password`

---

## 🧪 Testing Suite Validation

The project is backed by a rock-solid Pest PHP test suite. It tests all user validation boundaries, transaction ledgers, SaaS logic, and FCM notifications:

```bash
php artisan test
```

### Expected Output:
```text
  Tests  Passed: 69, Assertions: 254 (1.66s)
```

---

## 📄 License
This application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). You are free to modify, white-label, or sell this platform under these license terms.
