<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Produces watermarked variants of classroom photos on demand and caches
 * them on the private disk. The cache key is derived from the configured
 * watermark text — changing it in settings invalidates old variants
 * implicitly (next request renders fresh).
 */
class PhotoWatermarkService
{
    private const DISK = 'local';

    private const FONT_PATH = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';

    // Bump when the watermark layout changes — old cached variants then
    // sit on a different path and the new layout renders on next request.
    private const LAYOUT_VERSION = 2;

    /**
     * Resolve the disk path that should be streamed for a given source.
     * Returns the source path unchanged when no watermark is configured
     * or when the font file is unavailable.
     */
    public function variantPath(string $sourcePath): string
    {
        $text = trim((string) Setting::current()->watermark_text);
        if ($text === '' || ! is_file(self::FONT_PATH)) {
            return $sourcePath;
        }

        $hash = substr(hash('sha256', self::LAYOUT_VERSION.':'.$text), 0, 12);
        $info = pathinfo($sourcePath);
        $variant = ($info['dirname'] ?: '.').'/wm-'.$hash.'/'.$info['basename'];

        $disk = Storage::disk(self::DISK);
        if ($disk->exists($variant)) {
            return $variant;
        }

        $image = Image::read($disk->get($sourcePath));
        $this->draw($image, $text);
        $disk->put($variant, (string) $image->encode());

        return $variant;
    }

    private function draw($image, string $text): void
    {
        $width = $image->width();
        $height = $image->height();

        // Tile the text diagonally across the image. Approximate the text's
        // rendered footprint from the font size so tiles never overlap.
        $fontSize = max(18, (int) round($width * 0.035));
        $approxTextWidth = (int) round($fontSize * 0.55 * max(1, mb_strlen($text)));
        $stepX = max($fontSize * 4, $approxTextWidth + $fontSize * 2);
        $stepY = max($fontSize * 5, (int) round($fontSize * 6));

        // Offset every other row to break the grid into a brick pattern,
        // and overshoot the bounds so rotated text covers the edges.
        for ($y = -$stepY; $y < $height + $stepY; $y += $stepY) {
            $rowOffset = ((int) ($y / $stepY) % 2) * (int) ($stepX / 2);
            for ($x = -$stepX; $x < $width + $stepX; $x += $stepX) {
                $image->text($text, $x + $rowOffset, $y, function ($font) use ($fontSize) {
                    $font->file(self::FONT_PATH);
                    $font->size($fontSize);
                    $font->color('rgba(255, 255, 255, 0.35)');
                    $font->align('left');
                    $font->valign('middle');
                    $font->angle(30);
                });
            }
        }
    }
}
