<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PrintOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('printOptions')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Packages/Index', [
            'packages' => $packages,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Packages/Create', [
            'printOptions' => $this->activePrintOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $package = Package::create($this->packageAttributes($validated));
        $package->printOptions()->sync($this->syncPayload($validated['allowances'] ?? []));

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created.');
    }

    public function edit(Package $package)
    {
        $package->load('printOptions');

        return Inertia::render('Admin/Packages/Edit', [
            'package' => $package,
            'printOptions' => $this->activePrintOptions(),
            // Map of print_option_id => included_quantity for the form's editor.
            'allowances' => $package->printOptions
                ->mapWithKeys(fn ($o) => [$o->id => $o->pivot->included_quantity])
                ->all(),
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $validated = $this->validatePayload($request);

        $package->update($this->packageAttributes($validated));
        $package->printOptions()->sync($this->syncPayload($validated['allowances'] ?? []));

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'allowances' => 'nullable|array',
            'allowances.*.print_option_id' => 'required|exists:print_options,id',
            'allowances.*.included_quantity' => 'required|integer|min:1|max:999',
        ]);
    }

    private function packageAttributes(array $validated): array
    {
        return array_intersect_key($validated, array_flip([
            'name', 'description', 'price', 'display_order', 'is_active',
        ]));
    }

    /**
     * Convert the form's allowance rows into the pivot sync payload, ignoring
     * duplicates by keeping the last seen included_quantity per print option.
     */
    private function syncPayload(array $allowances): array
    {
        $sync = [];
        foreach ($allowances as $row) {
            $sync[(int) $row['print_option_id']] = [
                'included_quantity' => (int) $row['included_quantity'],
            ];
        }

        return $sync;
    }

    private function activePrintOptions()
    {
        return PrintOption::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get(['id', 'name', 'price']);
    }
}
