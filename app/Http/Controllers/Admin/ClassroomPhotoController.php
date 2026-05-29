<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassroomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;

class ClassroomPhotoController extends Controller
{
    /**
     * Private disk used for all classroom photo files. These are never exposed
     * through the public /storage symlink — they are streamed via order.photo.
     */
    private const DISK = 'local';

    public function index(Request $request)
    {
        $query = ClassroomPhoto::with('classroom');

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $query->orderBy('display_order')->orderBy('created_at', 'desc');

        if ($request->boolean('all')) {
            $photos = $query->get();
        } else {
            $photos = $query->paginate(24)->withQueryString();
        }

        return Inertia::render('Admin/ClassroomPhotos/Index', [
            'photos' => $photos,
            'classrooms' => Classroom::orderBy('name')->get(),
            'filters' => $request->only('classroom_id'),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Admin/ClassroomPhotos/Create', [
            'classrooms' => Classroom::orderBy('name')->get(),
            'selectedClassroomId' => $request->integer('classroom_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title' => 'nullable|string|max:255',
            'images' => 'required|array',
            'images.*' => 'required|image|max:20480',
        ]);

        $currentOrder = (int) ClassroomPhoto::where('classroom_id', $validated['classroom_id'])->max('display_order');
        $uploadedCount = 0;

        foreach ($request->file('images') as $image) {
            $filename = uniqid().'.'.$image->getClientOriginalExtension();

            $originalPath = $image->storeAs('classroom-photos/original', $filename, self::DISK);

            $source = Image::read($image->getRealPath());

            $medium = Image::read($image->getRealPath())->scaleDown(width: 1200);
            $mediumPath = 'classroom-photos/medium/'.$filename;
            Storage::disk(self::DISK)->put($mediumPath, $medium->encode());

            $thumbnail = Image::read($image->getRealPath())->scaleDown(width: 400);
            $thumbnailPath = 'classroom-photos/thumbnails/'.$filename;
            Storage::disk(self::DISK)->put($thumbnailPath, $thumbnail->encode());

            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $photoTitle = $validated['title']
                ? $validated['title'].' - '.$originalName
                : $originalName;

            ClassroomPhoto::create([
                'classroom_id' => $validated['classroom_id'],
                'title' => $photoTitle,
                'image_path' => $originalPath,
                'medium_path' => $mediumPath,
                'thumbnail_path' => $thumbnailPath,
                'width' => $source->width(),
                'height' => $source->height(),
                'file_size' => $image->getSize(),
                'display_order' => ++$currentOrder,
            ]);

            $uploadedCount++;
        }

        $message = $uploadedCount === 1
            ? 'Photo uploaded successfully.'
            : "{$uploadedCount} photos uploaded successfully.";

        return redirect()
            ->route('admin.classroom-photos.index', ['classroom_id' => $validated['classroom_id']])
            ->with('success', $message);
    }

    public function edit(ClassroomPhoto $classroomPhoto)
    {
        return Inertia::render('Admin/ClassroomPhotos/Edit', [
            'photo' => $classroomPhoto->load('classroom'),
            'classrooms' => Classroom::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ClassroomPhoto $classroomPhoto)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'display_order' => 'nullable|integer',
        ]);

        $classroomPhoto->update($validated);

        return redirect()
            ->route('admin.classroom-photos.index', ['classroom_id' => $classroomPhoto->classroom_id])
            ->with('success', 'Photo updated successfully.');
    }

    public function destroy(ClassroomPhoto $classroomPhoto)
    {
        $classroomId = $classroomPhoto->classroom_id;
        self::deletePhotoFiles($classroomPhoto);
        $classroomPhoto->delete();

        return redirect()
            ->route('admin.classroom-photos.index', ['classroom_id' => $classroomId])
            ->with('success', 'Photo deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'photo_order' => 'required|array',
            'photo_order.*' => 'required|exists:classroom_photos,id',
        ]);

        $matching = ClassroomPhoto::where('classroom_id', $validated['classroom_id'])
            ->whereIn('id', $validated['photo_order'])
            ->count();

        if ($matching !== count($validated['photo_order'])) {
            return back()->withErrors([
                'photo_order' => 'Some photos do not belong to this classroom.',
            ]);
        }

        foreach ($validated['photo_order'] as $index => $photoId) {
            ClassroomPhoto::where('id', $photoId)->update(['display_order' => $index + 1]);
        }

        return redirect()
            ->route('admin.classroom-photos.index', ['classroom_id' => $validated['classroom_id']])
            ->with('success', 'Photo order updated successfully.');
    }

    /**
     * Delete every stored size for a photo from the private disk.
     */
    public static function deletePhotoFiles(ClassroomPhoto $photo): void
    {
        foreach (['image_path', 'medium_path', 'thumbnail_path'] as $attr) {
            if ($photo->{$attr}) {
                Storage::disk(self::DISK)->delete($photo->{$attr});
            }
        }
    }
}
