<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksClassroomAccess;
use App\Models\ClassroomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Authorized streaming of classroom photo files from the PRIVATE local disk.
 * No public URLs exist for these images — this is the only access path.
 */
class OrderGalleryController extends Controller
{
    use ChecksClassroomAccess;

    private const DISK = 'local';

    private const SIZES = ['thumbnail', 'medium', 'original'];

    public function photo(Request $request, ClassroomPhoto $classroomPhoto, string $size = 'medium'): StreamedResponse
    {
        abort_unless(in_array($size, self::SIZES, true), 404);

        $this->authorizeClassroomAccess($request->user(), $classroomPhoto->classroom);

        $path = match ($size) {
            'thumbnail' => $classroomPhoto->thumbnail_path,
            'original' => $classroomPhoto->image_path,
            default => $classroomPhoto->medium_path,
        } ?: $classroomPhoto->image_path;

        abort_unless($path && Storage::disk(self::DISK)->exists($path), 404);

        return Storage::disk(self::DISK)->response($path);
    }
}
