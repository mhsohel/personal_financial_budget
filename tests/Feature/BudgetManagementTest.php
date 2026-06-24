<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Budget;
use Carbon\Carbon;

test('unauthenticated users are redirected from budget endpoints', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->post('/categories', [])->assertRedirect('/login');
    $this->post('/transactions', [])->assertRedirect('/login');
    $this->post('/budgets', [])->assertRedirect('/login');
});

test('user can create a category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/categories', [
        'name' => 'Groceries',
        'type' => 'expense',
        'color' => '#EF4444',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Groceries',
        'type' => 'expense',
        'color' => '#EF4444',
    ]);
});

test('user can log a transaction', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Salary',
        'type' => 'income',
        'color' => '#10B981',
    ]);

    $response = $this->actingAs($user)->post('/transactions', [
        'category_id' => $category->id,
        'amount' => 1250.50,
        'type' => 'income',
        'transaction_date' => Carbon::now()->format('Y-m-d'),
        'description' => 'Monthly payout',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'amount' => 1250.50,
        'type' => 'income',
        'description' => 'Monthly payout',
    ]);
});

test('user can set budget limits', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Utilities',
        'type' => 'expense',
        'color' => '#3B82F6',
    ]);

    $response = $this->actingAs($user)->post('/budgets', [
        'category_id' => $category->id,
        'amount' => 300.00,
        'month' => Carbon::now()->format('Y-m'),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('budgets', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'amount' => 300.00,
        'month' => Carbon::now()->format('Y-m'),
    ]);
});

test('dashboard correctly calculates net balance and budget limits', function () {
    $user = User::factory()->create();

    // 1. Create Categories
    $salaryCat = Category::create([
        'user_id' => $user->id,
        'name' => 'Salary',
        'type' => 'income',
        'color' => '#10B981',
    ]);

    $foodCat = Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'color' => '#EF4444',
    ]);

    // 2. Set Budget
    Budget::create([
        'user_id' => $user->id,
        'category_id' => $foodCat->id,
        'amount' => 500.00,
        'month' => Carbon::now()->format('Y-m'),
    ]);

    // 3. Add Transactions
    Transaction::create([
        'user_id' => $user->id,
        'category_id' => $salaryCat->id,
        'amount' => 2000.00,
        'type' => 'income',
        'transaction_date' => Carbon::now(),
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'category_id' => $foodCat->id,
        'amount' => 150.00,
        'type' => 'expense',
        'transaction_date' => Carbon::now(),
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);

    // Verify stats sent to Inertia dashboard
    $inertiaData = $response->original->getData()['page']['props'];

    expect($inertiaData['stats']['net_balance'])->toBe(1850.0);
    expect($inertiaData['stats']['monthly_income'])->toBe(2000.0);
    expect($inertiaData['stats']['monthly_expenses'])->toBe(150.0);

    // Verify budget statistics for Food
    $foodData = collect($inertiaData['categories'])->firstWhere('name', 'Food');
    expect($foodData['spent'])->toBe(150.0);
    expect($foodData['budget_limit'])->toBe(500.0);
    expect($foodData['percentage_used'])->toBe(30.0);
});
