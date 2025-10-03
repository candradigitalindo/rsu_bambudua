<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $items = ExpenseCategory::orderBy('name')->paginate(25);
        return view('pages.masterdata.expense_categories.index', compact('items'));
    }

    public function create()
    {
        return view('pages.masterdata.expense_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable',
        ]);
        ExpenseCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        return redirect()->route('master.expense-categories.index')->with('success', 'Kategori Pengeluaran ditambahkan.');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return view('pages.masterdata.expense_categories.edit', compact('expense_category'));
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expense_category->id . ',id',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $expense_category->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        return redirect()->route('master.expense-categories.index')->with('success', 'Kategori Pengeluaran diperbarui.');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();
        return back()->with('success', 'Kategori Pengeluaran dihapus.');
    }
}
