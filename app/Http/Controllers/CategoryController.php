<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Auth::user()->categories()->create($request->validated());

        return redirect()->back();
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->update($request->validated());

        return redirect()->back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->back();
    }
}
