<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksClassroomAccess;
use App\Models\ClassroomPhoto;
use App\Services\PhotoWatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Authorized streaming of classroom photo files from the PRIVATE local disk.
 * No public URLs exist for these images — this is the only access path.
 *
 * Medium/thumbnail sizes are served with the configured watermark baked in.
 * Originals are admin-only (parents never need them — the photographer
 * delivers the unwatermarked print after the order is placed).
 */
class OrderGalleryController extends Controller
{
    use ChecksClassroomAccess;

    private const DISK = 'local';

    private const SIZES = ['thumbnail', 'medium', 'original'];

    public function __construct(private readonly PhotoWatermarkService $watermark) {}

    public function photo(Request $request, ClassroomPhoto $classroomPhoto, string $size = 'medium'): StreamedResponse
    {
        abort_unless(in_array($size, self::SIZES, true), 404);

        $this->authorizeClassroomAccess($request->user(), $classroomPhoto->classroom);

        if ($size === 'original' && ! $request->user()?->is_admin) {
            abort(403);
        }

        $sourcePath = match ($size) {
            'thumbnail' => $classroomPhoto->thumbnail_path,
            'original' => $classroomPhoto->image_path,
            default => $classroomPhoto->medium_path,
        } ?: $classroomPhoto->image_path;

        abort_unless($sourcePath && Storage::disk(self::DISK)->exists($sourcePath), 404);

        // Originals stream as-is; smaller sizes pick up the configured
        // watermark (the service is a no-op when no text is set).
        $path = $size === 'original'
            ? $sourcePath
            : $this->watermark->variantPath($sourcePath);

        return Storage::disk(self::DISK)->response($path, headers: [
            'Cache-Control' => 'private, no-store',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
