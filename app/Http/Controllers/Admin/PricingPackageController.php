<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPackage;
use App\Models\PricingType;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingPackageController extends Controller
{
    public function index()
    {
        $packages = PricingPackage::with(['pricingType', 'services'])
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/PricingPackages/Index', [
            'packages' => $packages,
        ]);
    }

    public function create()
    {
        $pricingTypes = PricingType::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $services = Service::where('is_active', true)
            ->get();

        return Inertia::render('Admin/PricingPackages/Create', [
            'pricingTypes' => $pricingTypes,
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pricing_type_id' => 'required|exists:pricing_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
        ]);

        $package = PricingPackage::create($validated);

        if (isset($validated['services'])) {
            $servicesToSync = [];
            foreach ($validated['services'] as $service) {
                $servicesToSync[$service['id']] = ['quantity' => $service['quantity']];
            }
            $package->services()->sync($servicesToSync);
        }

        return redirect()->route('admin.pricing-packages.index')
            ->with('success', 'Pricing package created successfully.');
    }

    public function edit(PricingPackage $pricingPackage)
    {
        $pricingPackage->load('services');

        $pricingTypes = PricingType::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $services = Service::where('is_active', true)
            ->get();

        return Inertia::render('Admin/PricingPackages/Edit', [
            'pricingPackage' => $pricingPackage,
            'pricingTypes' => $pricingTypes,
            'services' => $services,
        ]);
    }

    public function update(Request $request, PricingPackage $pricingPackage)
    {
        $validated = $request->validate([
            'pricing_type_id' => 'required|exists:pricing_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'services' => 'nullable|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
        ]);

        $pricingPackage->update($validated);

        if (isset($validated['services'])) {
            $servicesToSync = [];
            foreach ($validated['services'] as $service) {
                $servicesToSync[$service['id']] = ['quantity' => $service['quantity']];
            }
            $pricingPackage->services()->sync($servicesToSync);
        } else {
            $pricingPackage->services()->sync([]);
        }

        return redirect()->route('admin.pricing-packages.index')
            ->with('success', 'Pricing package updated successfully.');
    }

    public function destroy(PricingPackage $pricingPackage)
    {
        $pricingPackage->delete();

        return redirect()->route('admin.pricing-packages.index')
            ->with('success', 'Pricing package deleted successfully.');
    }
}
