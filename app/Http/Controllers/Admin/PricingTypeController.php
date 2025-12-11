<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingTypeController extends Controller
{
    public function index()
    {
        $types = PricingType::withCount('packages')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/PricingTypes/Index', [
            'types' => $types,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PricingTypes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        PricingType::create($validated);

        return redirect()->route('admin.pricing-types.index')
            ->with('success', 'Pricing type created successfully.');
    }

    public function edit(PricingType $pricingType)
    {
        return Inertia::render('Admin/PricingTypes/Edit', [
            'pricingType' => $pricingType,
        ]);
    }

    public function update(Request $request, PricingType $pricingType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $pricingType->update($validated);

        return redirect()->route('admin.pricing-types.index')
            ->with('success', 'Pricing type updated successfully.');
    }

    public function destroy(PricingType $pricingType)
    {
        $pricingType->delete();

        return redirect()->route('admin.pricing-types.index')
            ->with('success', 'Pricing type deleted successfully.');
    }
}
