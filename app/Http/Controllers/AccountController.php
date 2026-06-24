<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        Auth::user()->accounts()->create($request->validated());

        return redirect()->back();
    }

    public function update(StoreAccountRequest $request, Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $account->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Account $account): RedirectResponse
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $account->delete();

        return redirect()->back();
    }
}
