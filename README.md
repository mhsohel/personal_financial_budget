# Personal Budget Management System

A high-fidelity, premium personal finance and budget tracker application built with a modern full-stack architecture. It leverages **Laravel 11**, **Inertia.js**, and **Vue 3 (Composition API)** with **Tailwind CSS**.

---

## ✨ Features

- 🔐 **Secure User Authentication**: Complete registration, login, session management, and profile customization out-of-the-box via Laravel Breeze.
- 💳 **Multi-Account Support**: Manage multiple financial accounts (e.g. Cash, Card, Bank) with initial balance settings, custom display colors, and real-time balance calculations.
- 💸 **Income & Expense Tracking**: Fully featured CRUD ledger to record your financial transactions with custom categories, accounts, dates, and detailed descriptions.
- 🔄 **Account Fund Transfers**: Transfer funds between accounts with dual-linked ledger records (automatically logs offsetting expense/income transaction pairs).
- 🎯 **Smart Budgeting**: Set monthly spending limits per category (e.g. Food: $500, Utilities: $200).
- 📊 **Financial Insights Dashboard**:
  - Real-time balances: Displays overall net balance across all accounts, monthly income, and monthly expenses.
  - Visual budget targets: Custom interactive progress bars that dynamically transition color (Green $\rightarrow$ Yellow warning $\rightarrow$ Red alert) as you approach or exceed your limits.
  - Interactive Modals: Seamlessly log transactions, manage categories, perform transfers, and update monthly limits in-place.
- 📈 **Reports & Predictions**:
  - Historical trends: Renders a custom high-fidelity SVG chart showing monthly income, expenses, and savings over the last 6 months.
  - Category breakdown: Displays a stacked horizontal percentage bar detailing category outflows.
  - Savings predictions: Forecasts total net worth changes (savings growth/loss) over 3, 6, and 12 months with custom financial health advice.
- 🔑 **SaaS License Management**:
  - Track clients, products, pricing models, next renewal dates, and status.
  - Calculate MRR (Monthly Recurring Revenue) and ARR (Annual Recurring Revenue).
  - Automatically logs payments to the transaction ledger and advances renewal dates.
- 🤝 **Loan & Repayment Tracking**:
  - Track loans lent to or borrowed from individuals.
  - Dedicated loans dashboard featuring total receivables, total payables, and overdue stats.
  - Logs repayments as transaction records which dynamically decrement the outstanding loan balance until marked as repaid.
- ⏰ **Recurring Schedules**:
  - Define recurring payment profiles for expenses, loan installments, or recurring loans.
  - Flexible frequencies: weekly, monthly, quarterly.
  - Manually process the next due instance or skip to the following cycle.
- 🔔 **FCM Push Notification Reminders**:
  - Automated daily alerts for overdue loans, recurring schedules, and upcoming SaaS license renewals.
  - Powered by a custom Firebase service with dynamic service account token exchange and the modern FCM v1 API.
- 🧪 **Complete Test Coverage**: Includes 69 Pest feature tests verifying authentication boundaries, accounts management, fund transfers, loans, recurring schedules, SaaS licenses, report computations, and Firebase push notification reminders.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11, PHP 8.2+ (lean Resource Controllers, Form Requests for strict validation, Eloquent API Resources).
- **Frontend**: Vue 3 (`<script setup>` syntax), Inertia.js (state-sharing & routing bridge), Vite, and Tailwind CSS.
- **Database**: SQLite (portable local storage).
- **Testing**: Pest PHP (modern testing framework).
- **Push Services**: Firebase Cloud Messaging (FCM) v1.

---

## 🚀 Setup & Installation

Follow these steps to run the application locally on your machine:

### Prerequisites
Make sure you have the following installed:
- **PHP** (8.2 or higher)
- **Composer**
- **Node.js** & **npm**

### Step 1: Install Dependencies
Install PHP and Node.js packages:
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 2: Configure Environment
Copy the example environment file:
```bash
cp .env.example .env
```
Ensure that the database is set to SQLite in your `.env` file (this is default in Laravel 11):
```env
DB_CONNECTION=sqlite
```

#### Optional: Configure Firebase Messaging for Push Notifications
To enable push notification reminders, add your Firebase credentials in your `.env` file:
```env
FIREBASE_API_KEY=your-firebase-api-key
FIREBASE_AUTH_DOMAIN=your-auth-domain
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_STORAGE_BUCKET=your-storage-bucket
FIREBASE_MESSAGING_SENDER_ID=your-sender-id
FIREBASE_APP_ID=your-app-id
FIREBASE_VAPID_KEY=your-public-vapid-key
FIREBASE_CLIENT_EMAIL=your-service-account-client-email
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
```

### Step 3: Run Database Migrations
Create the SQLite database file and migrate the schema:
```bash
# Create SQLite file
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### Step 4: Build Assets & Run Development Servers
Start the Vite development asset server and the Laravel server:
```bash
# Run Vite compiler (in one terminal)
npm run dev

# Run Laravel local server (in another terminal)
php artisan serve
```

The application will now be accessible at [http://127.0.0.1:8000](http://127.0.0.1:8000).

To process due reminders and send push notifications manually:
```bash
php artisan reminders:send-notifications
```

---

## 🧪 Running Tests

A suite of Pest tests validates the core transaction logic, budget calculations, multi-account structures, loans, and push notifications.

Run the test suite using:
```bash
php artisan test
```

Expected output:
```text
Passes: 69
Assertions: 254
```

---

## 📂 Project Architecture

Key application directories and files:
- **Database Schema**: [database/migrations/](file:///Users/morshedhabib/Sites/budget_management/database/migrations/)
- **Models**: [app/Models/](file:///Users/morshedhabib/Sites/budget_management/app/Models/) 
  - [User.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/User.php)
  - [Account.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Account.php)
  - [Budget.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Budget.php)
  - [Category.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Category.php)
  - [Client.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Client.php)
  - [License.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/License.php)
  - [Loan.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Loan.php)
  - [RecurringSchedule.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/RecurringSchedule.php)
  - [Transaction.php](file:///Users/morshedhabib/Sites/budget_management/app/Models/Transaction.php)
- **Controllers**: [app/Http/Controllers/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/)
  - [DashboardController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/DashboardController.php)
  - [AccountController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/AccountController.php)
  - [TransferController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/TransferController.php)
  - [CategoryController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/CategoryController.php)
  - [BudgetController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/BudgetController.php)
  - [TransactionController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/TransactionController.php)
  - [ReportController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/ReportController.php)
  - [LicenseController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/LicenseController.php)
  - [LoanController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/LoanController.php)
  - [RecurringScheduleController.php](file:///Users/morshedhabib/Sites/budget_management/app/Http/Controllers/RecurringScheduleController.php)
- **Console Commands**:
  - [SendReminderNotifications.php](file:///Users/morshedhabib/Sites/budget_management/app/Console/Commands/SendReminderNotifications.php)
- **Services**:
  - [FirebaseService.php](file:///Users/morshedhabib/Sites/budget_management/app/Services/FirebaseService.php)
- **Form Validation**: [app/Http/Requests/](file:///Users/morshedhabib/Sites/budget_management/app/Http/Requests/)
- **Vue Views**: [resources/js/Pages/](file:///Users/morshedhabib/Sites/budget_management/resources/js/Pages/) (Dashboard.vue, Reports/Index.vue, Licenses/Index.vue, Loans/Index.vue, Recurring/Index.vue)
- **Web Routes**: [routes/web.php](file:///Users/morshedhabib/Sites/budget_management/routes/web.php)
- **Feature Tests**: [tests/Feature/](file:///Users/morshedhabib/Sites/budget_management/tests/Feature/)

---

## 📄 License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT). This application is free to use and distribute under the same license terms.
