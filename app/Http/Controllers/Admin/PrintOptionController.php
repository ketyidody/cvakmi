<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrintOptionController extends Controller
{
    public function index()
    {
        $printOptions = PrintOption::orderBy('display_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/PrintOptions/Index', [
            'printOptions' => $printOptions,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PrintOptions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        PrintOption::create($validated);

        return redirect()->route('admin.print-options.index')
            ->with('success', 'Print option created successfully.');
    }

    public function edit(PrintOption $printOption)
    {
        return Inertia::render('Admin/PrintOptions/Edit', [
            'printOption' => $printOption,
        ]);
    }

    public function update(Request $request, PrintOption $printOption)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $printOption->update($validated);

        return redirect()->route('admin.print-options.index')
            ->with('success', 'Print option updated successfully.');
    }

    public function destroy(PrintOption $printOption)
    {
        $printOption->delete();

        return redirect()->route('admin.print-options.index')
            ->with('success', 'Print option deleted successfully.');
    }
}
