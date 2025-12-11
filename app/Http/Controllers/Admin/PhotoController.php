<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Photo::with('album');

        if ($request->has('album_id')) {
            $query->where('album_id', $request->album_id);
        }

        $photos = $query->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $albums = Album::orderBy('title')->get();

        return Inertia::render('Admin/Photos/Index', [
            'photos' => $photos,
            'albums' => $albums,
            'filters' => $request->only('album_id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $albums = Album::orderBy('title')->get();

        return Inertia::render('Admin/Photos/Create', [
            'albums' => $albums,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'album_id' => 'required|exists:albums,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'required|image|max:20480', // 20MB max per image
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        $uploadedCount = 0;
        $currentOrder = $validated['display_order'] ?? 0;

        foreach ($request->file('images') as $index => $image) {
            // Generate unique filename
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            // Store original image
            $originalPath = $image->store('photos/original', 'public');

            // Process image with Intervention Image
            $imageInstance = Image::read($image->getRealPath());

            // Generate medium size (max 1200px wide, maintain aspect ratio)
            $mediumImage = Image::read($image->getRealPath());
            $mediumImage->scaleDown(width: 1200);
            $mediumPath = 'photos/medium/' . $filename;
            Storage::disk('public')->put($mediumPath, $mediumImage->encode());

            // Generate thumbnail (max 400px wide, maintain aspect ratio)
            $thumbnail = Image::read($image->getRealPath());
            $thumbnail->scaleDown(width: 400);
            $thumbnailPath = 'photos/thumbnails/' . $filename;
            Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

            // Generate title from filename if no base title provided
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $photoTitle = $validated['title']
                ? $validated['title'] . ' - ' . $originalName
                : $originalName;

            // Create photo record
            Photo::create([
                'album_id' => $validated['album_id'],
                'title' => $photoTitle,
                'description' => $validated['description'],
                'image_path' => $originalPath,
                'medium_path' => $mediumPath,
                'thumbnail_path' => $thumbnailPath,
                'width' => $imageInstance->width(),
                'height' => $imageInstance->height(),
                'file_size' => $image->getSize(),
                'display_order' => $currentOrder + $index,
                'is_featured' => $index === 0 && ($validated['is_featured'] ?? false),
            ]);

            $uploadedCount++;
        }

        $message = $uploadedCount === 1
            ? 'Photo uploaded successfully.'
            : "{$uploadedCount} photos uploaded successfully.";

        return redirect()->route('admin.photos.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        $photo->load('album');

        return Inertia::render('Admin/Photos/Show', [
            'photo' => $photo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Photo $photo)
    {
        $albums = Album::orderBy('title')->get();

        return Inertia::render('Admin/Photos/Edit', [
            'photo' => $photo,
            'albums' => $albums,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Photo $photo)
    {
        $validated = $request->validate([
            'album_id' => 'required|exists:albums,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old images
            if ($photo->image_path) {
                Storage::disk('public')->delete($photo->image_path);
            }
            if ($photo->thumbnail_path) {
                Storage::disk('public')->delete($photo->thumbnail_path);
            }
            if ($photo->medium_path) {
                Storage::disk('public')->delete($photo->medium_path);
            }

            $image = $request->file('image');

            // Generate unique filename
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            // Store original image
            $originalPath = $image->store('photos/original', 'public');

            // Process image with Intervention Image
            $imageInstance = Image::read($image->getRealPath());

            // Get original dimensions
            $validated['width'] = $imageInstance->width();
            $validated['height'] = $imageInstance->height();
            $validated['file_size'] = $image->getSize();
            $validated['image_path'] = $originalPath;

            // Generate medium size (max 1200px wide, maintain aspect ratio)
            $mediumImage = Image::read($image->getRealPath());
            $mediumImage->scaleDown(width: 1200);
            $mediumPath = 'photos/medium/' . $filename;
            Storage::disk('public')->put($mediumPath, $mediumImage->encode());
            $validated['medium_path'] = $mediumPath;

            // Generate thumbnail (max 400px wide, maintain aspect ratio)
            $thumbnail = Image::read($image->getRealPath());
            $thumbnail->scaleDown(width: 400);
            $thumbnailPath = 'photos/thumbnails/' . $filename;
            Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
            $validated['thumbnail_path'] = $thumbnailPath;
        }

        unset($validated['image']);
        $photo->update($validated);

        return redirect()->route('admin.photos.index')
            ->with('success', 'Photo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        // Delete all image sizes
        if ($photo->image_path) {
            Storage::disk('public')->delete($photo->image_path);
        }
        if ($photo->thumbnail_path) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }
        if ($photo->medium_path) {
            Storage::disk('public')->delete($photo->medium_path);
        }

        $photo->delete();

        return redirect()->route('admin.photos.index')
            ->with('success', 'Photo deleted successfully.');
    }
}
