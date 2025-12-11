<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('display_order')
            ->orderBy('review_date', 'desc')
            ->get();

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Reviews/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'thumbnail_image' => 'nullable|image|max:2048',
            'full_name' => 'required|string|max:255',
            'review_date' => 'required|date',
            'source' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail_image')) {
            $validated['thumbnail_image'] = $request->file('thumbnail_image')->store('reviews', 'public');
        }

        Review::create($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    public function edit(Review $review)
    {
        return Inertia::render('Admin/Reviews/Edit', [
            'review' => $review,
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'thumbnail_image' => 'nullable|image|max:2048',
            'full_name' => 'required|string|max:255',
            'review_date' => 'required|date',
            'source' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail_image')) {
            if ($review->thumbnail_image) {
                Storage::disk('public')->delete($review->thumbnail_image);
            }
            $validated['thumbnail_image'] = $request->file('thumbnail_image')->store('reviews', 'public');
        }

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        if ($review->thumbnail_image) {
            Storage::disk('public')->delete($review->thumbnail_image);
        }

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
