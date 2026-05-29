<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount(['photos', 'parents'])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Classrooms/Index', [
            'classrooms' => $classrooms,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Classrooms/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom created successfully.');
    }

    public function edit(Classroom $classroom)
    {
        return Inertia::render('Admin/Classrooms/Edit', [
            'classroom' => $classroom,
        ]);
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom updated successfully.');
    }

    public function destroy(Classroom $classroom)
    {
        // Photos cascade-delete in the DB; remove their files from the private disk first.
        foreach ($classroom->photos as $photo) {
            ClassroomPhotoController::deletePhotoFiles($photo);
        }

        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom deleted successfully.');
    }
}
