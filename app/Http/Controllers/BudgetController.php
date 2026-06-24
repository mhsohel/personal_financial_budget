<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Auth::user()->budgets()->updateOrCreate(
            [
                'category_id' => $validated['category_id'],
                'month' => $validated['month'],
            ],
            [
                'amount' => $validated['amount'],
            ]
        );

        return redirect()->back();
    }
}
