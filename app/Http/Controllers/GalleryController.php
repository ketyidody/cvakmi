<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GalleryController extends Controller
{
    /**
     * Display all published albums.
     */
    public function index()
    {
        $albums = Album::where('is_published', true)
            ->withCount('photos')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Gallery/Index', [
            'albums' => $albums,
        ]);
    }

    /**
     * Display a single album with its photos.
     */
    public function show(string $slug)
    {
        $album = Album::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $photos = Photo::where('album_id', $album->id)
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Gallery/Show', [
            'album' => $album,
            'photos' => $photos,
        ]);
    }

    /**
     * Display a single photo (for lightbox/detail view).
     */
    public function photo(string $albumSlug, int $photoId)
    {
        $album = Album::where('slug', $albumSlug)
            ->where('is_published', true)
            ->firstOrFail();

        $photo = Photo::where('id', $photoId)
            ->where('album_id', $album->id)
            ->firstOrFail();

        // Get previous and next photos for navigation
        $previousPhoto = Photo::where('album_id', $album->id)
            ->where('id', '<', $photo->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextPhoto = Photo::where('album_id', $album->id)
            ->where('id', '>', $photo->id)
            ->orderBy('id', 'asc')
            ->first();

        return Inertia::render('Gallery/Photo', [
            'album' => $album,
            'photo' => $photo,
            'previousPhoto' => $previousPhoto,
            'nextPhoto' => $nextPhoto,
        ]);
    }
}
