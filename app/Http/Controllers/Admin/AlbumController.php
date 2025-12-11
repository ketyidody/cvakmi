<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $albums = Album::withCount('photos')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Albums/Index', [
            'albums' => $albums,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Albums/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:20480',
            'display_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('albums', 'public');
        }

        Album::create($validated);

        return redirect()->route('admin.albums.index')
            ->with('success', 'Album created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        $album->load(['photos' => function ($query) {
            $query->orderBy('display_order');
        }]);

        return Inertia::render('Admin/Albums/Show', [
            'album' => $album,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        return Inertia::render('Admin/Albums/Edit', [
            'album' => $album,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:20480',
            'display_order' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($album->cover_image) {
                Storage::disk('public')->delete($album->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')
                ->store('albums', 'public');
        }

        $album->update($validated);

        return redirect()->route('admin.albums.index')
            ->with('success', 'Album updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        // Delete cover image if exists
        if ($album->cover_image) {
            Storage::disk('public')->delete($album->cover_image);
        }

        $album->delete();

        return redirect()->route('admin.albums.index')
            ->with('success', 'Album deleted successfully.');
    }
}
