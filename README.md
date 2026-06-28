# Personal Budget Management System

A high-fidelity, premium personal finance and budget tracker application built with a modern full-stack architecture. It leverages **Laravel 11**, **Inertia.js**, and **Vue 3 (Composition API)** with **Tailwind CSS**.

---

## ✨ Features

- 🔐 **Secure User Authentication**: Complete registration, login, session management, and profile customization out-of-the-box via Laravel Breeze.
- 💸 **Income & Expense Tracking**: Fully featured CRUD ledger to record your financial transactions with custom categories, dates, and detailed descriptions.
- 🎯 **Smart Budgeting**: Set monthly spending limits per category (e.g. Food: $500, Utilities: $200).
- 📊 **Financial Insights Dashboard**:
  - Real-time balances: Displays overall net balance, monthly income, and monthly expenses.
  - Visual budget targets: Custom interactive progress bars that dynamically transition color (Green $\rightarrow$ Yellow warning $\rightarrow$ Red alert) as you approach or exceed your limits.
  - Interactive Modals: Seamlessly log transactions, manage categories, and update monthly limits in-place.
- 📈 **Reports & Predictions**:
  - Historical trends: Renders a custom high-fidelity SVG chart showing monthly income, expenses, and savings over the last 6 months.
  - Category breakdown: Displays a stacked horizontal percentage bar detailing category outflows.
  - Savings predictions: Forecasts total net worth changes (savings growth/loss) over 3, 6, and 12 months with custom financial health advice.
- 🔑 **SaaS License Management**:
  - Track clients, products, pricing models, next renewal dates, and status.
  - Calculate MRR (Monthly Recurring Revenue) and ARR (Annual Recurring Revenue).
  - Automatically logs payments to the transaction ledger and advances renewal dates.
- 🧪 **Complete Test Coverage**: Includes 36 Pest feature tests verifying authentication boundaries, CRUD operations, reports calculations, and SaaS license management.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11, PHP 8.2+ (lean Resource Controllers, Form Requests for strict validation, Eloquent API Resources).
- **Frontend**: Vue 3 (`<script setup>` syntax), Inertia.js (state-sharing & routing bridge), Vite, and Tailwind CSS.
- **Database**: SQLite (portable local storage).
- **Testing**: Pest PHP (modern testing framework).

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

---

## 🧪 Running Tests

A suite of Pest tests validates the core transaction logic, budget calculations, and authentication boundaries.

Run the test suite using:
```bash
php artisan test
```

Expected output:
```text
Passes: 36
Assertions: 113
```

---

## 📂 Project Architecture

Key application directories and files:
- **Database Schema**: [migrations](file:///Users/morshedhabib/Downloads/anti/database/migrations/)
- **Models**: [app/Models/](file:///Users/morshedhabib/Downloads/anti/app/Models/) (User, Category, Budget, Transaction, Client, License)
- **Controllers**: [app/Http/Controllers/](file:///Users/morshedhabib/Downloads/anti/app/Http/Controllers/) (DashboardController, CategoryController, BudgetController, TransactionController, ReportController, LicenseController)
- **Form Validation**: [app/Http/Requests/](file:///Users/morshedhabib/Downloads/anti/app/Http/Requests/) (StoreCategoryRequest, StoreBudgetRequest, StoreTransactionRequest, StoreClientRequest, StoreLicenseRequest)
- **Vue Views**: [resources/js/Pages/](file:///Users/morshedhabib/Downloads/anti/resources/js/Pages/) (Dashboard.vue, Reports/Index.vue, Licenses/Index.vue)
- **Web Routes**: [routes/web.php](file:///Users/morshedhabib/Downloads/anti/routes/web.php)
- **Feature Tests**: [tests/Feature/BudgetManagementTest.php](file:///Users/morshedhabib/Downloads/anti/tests/Feature/BudgetManagementTest.php), [tests/Feature/ReportTest.php](file:///Users/morshedhabib/Downloads/anti/tests/Feature/ReportTest.php), [tests/Feature/LicenseTest.php](file:///Users/morshedhabib/Downloads/anti/tests/Feature/LicenseTest.php)

---

## 📄 License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT). This application is free to use and distribute under the same license terms.
