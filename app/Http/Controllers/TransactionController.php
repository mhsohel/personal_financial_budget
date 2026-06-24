<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        Auth::user()->transactions()->create($request->validated());

        return redirect()->back();
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($transaction) {
            if ($transaction->is_transfer && $transaction->transfer_transaction_id) {
                $linked = Transaction::find($transaction->transfer_transaction_id);
                if ($linked) {
                    $linked->update(['transfer_transaction_id' => null]);
                    $linked->delete();
                }
            }

            $loanId = $transaction->loan_id;

            $transaction->delete();

            if ($loanId) {
                $loan = \App\Models\Loan::find($loanId);
                if ($loan) {
                    $repaymentSum = $loan->transactions()
                        ->where('type', $loan->type === 'lent' ? 'income' : 'expense')
                        ->sum('amount');

                    $loan->update([
                        'status' => $repaymentSum >= $loan->amount ? 'repaid' : 'active',
                    ]);
                }
            }
        });

        return redirect()->back();
    }
}
