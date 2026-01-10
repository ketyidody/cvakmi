<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\PricingPackageController;
use App\Http\Controllers\Admin\PricingTypeController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $featuredPhotos = \App\Models\Photo::where('is_featured', true)
        ->with('album')
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();

    $albums = \App\Models\Album::where('is_published', true)
        ->withCount('photos')
        ->orderBy('display_order')
        ->limit(6)
        ->get();

    return Inertia::render('Welcome', [
        'featuredPhotos' => $featuredPhotos,
        'albums' => $albums,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('homepage');

// Static pages
Route::get('/o-mne', function () {
    return Inertia::render('About');
})->name('about');

//Route::get('/recenzie', function () {
//    $reviews = \App\Models\Review::where('is_active', true)
//        ->orderBy('display_order')
//        ->orderBy('review_date', 'desc')
//        ->get();
//
//    return Inertia::render('Reviews', [
//        'reviews' => $reviews,
//    ]);
//})->name('reviews');
//
//Route::get('/cennik', function () {
//    $pricingTypes = \App\Models\PricingType::where('is_active', true)
//        ->with(['packages' => function ($query) {
//            $query->where('is_active', true)
//                ->with('services')
//                ->orderBy('display_order')
//                ->orderBy('price');
//        }])
//        ->orderBy('display_order')
//        ->get();
//
//    $services = \App\Models\Service::where('is_active', true)
//        ->whereDoesntHave('packages')
//        ->get();
//
//    return Inertia::render('Pricing', [
//        'pricingTypes' => $pricingTypes,
//        'additionalServices' => $services,
//    ]);
//})->name('pricing');
//
//Route::get('/kontakt', function () {
//    return Inertia::render('Contact');
//})->name('contact');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('albums', AlbumController::class);
    Route::post('photos/reorder', [PhotoController::class, 'reorder'])->name('photos.reorder');
    Route::resource('photos', PhotoController::class);
    Route::resource('pricing-types', PricingTypeController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('pricing-packages', PricingPackageController::class);
    Route::resource('reviews', ReviewController::class);
});

// Public gallery routes
Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [GalleryController::class, 'index'])->name('index');
    Route::get('/{slug}', [GalleryController::class, 'show'])->name('show');
    Route::get('/{slug}/photo/{photo}', [GalleryController::class, 'photo'])->name('photo');
});

require __DIR__.'/auth.php';
