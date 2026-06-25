<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();

        // 1. Calculate Historical Trends (Last 6 Months)
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $monthLabel = $monthDate->format('M Y');
            
            $startDate = $monthDate->copy()->startOfMonth();
            $endDate = $monthDate->copy()->endOfMonth();
            
            $income = $user->transactions()
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
                
            $expense = $user->transactions()
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');

            $savings = $income - $expense;
            $savingsRate = $income > 0 ? round(($savings / $income) * 100, 2) : 0.0;
            
            $trends[] = [
                'month_key' => $monthKey,
                'label' => $monthLabel,
                'income' => (float) $income,
                'expense' => (float) $expense,
                'savings' => (float) $savings,
                'savings_rate' => (float) $savingsRate,
            ];
        }

        // 2. Calculate Category Breakdown (Last 6 Months aggregated)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $todayEnd = Carbon::now()->endOfMonth();

        $categoryExpenses = $user->categories()
            ->where('type', 'expense')
            ->get()
            ->map(function (Category $category) use ($sixMonthsAgo, $todayEnd) {
                $totalSpent = $category->transactions()
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$sixMonthsAgo, $todayEnd])
                    ->sum('amount');

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color ?? '#3B82F6',
                    'total' => (float) $totalSpent,
                ];
            })
            ->filter(fn($cat) => $cat['total'] > 0)
            ->values();

        // 3. Projections based on Last 3 Months average
        $threeMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth();
        
        $totalIncomeLast3 = $user->transactions()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$threeMonthsAgo, $todayEnd])
            ->sum('amount');

        $totalExpenseLast3 = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$threeMonthsAgo, $todayEnd])
            ->sum('amount');

        $avgIncome = round($totalIncomeLast3 / 3, 2);
        $avgExpense = round($totalExpenseLast3 / 3, 2);
        $avgSavings = $avgIncome - $avgExpense;
        $avgSavingsRate = $avgIncome > 0 ? round(($avgSavings / $avgIncome) * 100, 2) : 0.0;

        // Projections
        $projection3m = round($avgSavings * 3, 2);
        $projection6m = round($avgSavings * 6, 2);
        $projection12m = round($avgSavings * 12, 2);

        // 4. Accounts Report
        $accountsReport = $user->accounts()->get()->map(function ($account) {
            $inflows = $account->transactions()->where('type', 'income')->sum('amount');
            $outflows = $account->transactions()->where('type', 'expense')->sum('amount');
            return [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
                'color' => $account->color ?? '#6366f1',
                'initial_balance' => (float) $account->initial_balance,
                'total_inflows' => (float) $inflows,
                'total_outflows' => (float) $outflows,
                'net_flow' => (float) ($inflows - $outflows),
                'current_balance' => (float) ($account->initial_balance + $inflows - $outflows),
            ];
        });

        return Inertia::render('Reports/Index', [
            'trends' => $trends,
            'category_expenses' => $categoryExpenses,
            'accounts_report' => $accountsReport,
            'averages' => [
                'income' => (float) $avgIncome,
                'expense' => (float) $avgExpense,
                'savings' => (float) $avgSavings,
                'savings_rate' => (float) $avgSavingsRate,
            ],
            'projections' => [
                'three_months' => (float) $projection3m,
                'six_months' => (float) $projection6m,
                'twelve_months' => (float) $projection12m,
            ]
        ]);
    }

    public function forecast(Request $request): Response
    {
        $user = Auth::user();

        // 1. Starting liquid balance / Net Worth calculation
        $accounts = $user->accounts()->get()->map(function ($account) {
            $inflows = $account->transactions()->where('type', 'income')->sum('amount');
            $outflows = $account->transactions()->where('type', 'expense')->sum('amount');
            return [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
                'color' => $account->color ?? '#6366f1',
                'current_balance' => (float) ($account->initial_balance + $inflows - $outflows),
            ];
        });

        $startingNetWorth = $accounts->sum('current_balance');

        // 2. Calculate average monthly income/expense (last 3 months, excluding SaaS and Loan specific transactions)
        $todayEnd = Carbon::now()->endOfMonth();
        $threeMonthsAgo = Carbon::now()->subMonths(2)->startOfMonth();

        $standardIncome = $user->transactions()
            ->where('type', 'income')
            ->whereNull('loan_id')
            ->whereNull('license_id')
            ->whereBetween('transaction_date', [$threeMonthsAgo, $todayEnd])
            ->sum('amount');

        $standardExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereNull('loan_id')
            ->whereNull('license_id')
            ->whereBetween('transaction_date', [$threeMonthsAgo, $todayEnd])
            ->sum('amount');

        $avgIncome = round($standardIncome / 3, 2);
        $avgExpense = round($standardExpense / 3, 2);

        // 3. Load active SaaS licenses
        $licenses = $user->licenses()
            ->where('status', 'active')
            ->get()
            ->map(function ($license) {
                return [
                    'id' => $license->id,
                    'client_name' => $license->client->name ?? 'Direct License',
                    'amount' => (float) $license->amount,
                    'billing_cycle' => $license->billing_cycle,
                    'next_renewal_date' => $license->next_renewal_date ? $license->next_renewal_date->format('Y-m-d') : null,
                ];
            });

        // 4. Load active Loans
        $loans = $user->loans()
            ->where('status', 'active')
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'person_name' => $loan->person_name,
                    'type' => $loan->type,
                    'amount' => (float) $loan->amount,
                    'due_date' => $loan->due_date ? $loan->due_date->format('Y-m-d') : null,
                    'description' => $loan->description,
                ];
            });

        // 5. Load Budgets & Average Spend
        $budgets = $user->budgets()
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($group) use ($threeMonthsAgo, $todayEnd) {
                $budget = $group->sortByDesc('month')->first();
                $category = $budget->category;
                if (!$category) return null;

                $threeMonthSpend = $category->transactions()
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$threeMonthsAgo, $todayEnd])
                    ->sum('amount');

                return [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'color' => $category->color ?? '#3B82F6',
                    'limit' => (float) $budget->amount,
                    'avg_spend' => round($threeMonthSpend / 3, 2),
                ];
            })
            ->filter()
            ->values();

        // 6. Generate 12-Month list (labels and dates) for predictions
        $timelineMonths = [];
        $currentMonth = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthDate = $currentMonth->copy()->addMonths($i);
            $timelineMonths[] = [
                'month_key' => $monthDate->format('Y-m'),
                'label' => $monthDate->format('M Y'),
                'year' => (int) $monthDate->format('Y'),
                'month' => (int) $monthDate->format('m'),
            ];
        }

        return Inertia::render('Reports/Forecast', [
            'accounts' => $accounts,
            'starting_net_worth' => (float) $startingNetWorth,
            'averages' => [
                'income' => (float) $avgIncome,
                'expense' => (float) $avgExpense,
            ],
            'licenses' => $licenses,
            'loans' => $loans,
            'budgets' => $budgets,
            'timeline_months' => $timelineMonths,
        ]);
    }
}
