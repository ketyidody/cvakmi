<?php

use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ClassroomPhotoController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\PricingPackageController;
use App\Http\Controllers\Admin\PricingTypeController;
use App\Http\Controllers\Admin\PrintOptionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderGalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WizardController;
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
        'canRegister' => false, // Registration is invite-only via the order link
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
    // Parents don't use the admin dashboard — send them into the wizard.
    if (! auth()->user()?->is_admin) {
        return redirect()->route('order.start');
    }

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

    // Photo order system
    Route::resource('classrooms', ClassroomController::class)->except('show');
    Route::post('classroom-photos/reorder', [ClassroomPhotoController::class, 'reorder'])
        ->name('classroom-photos.reorder');
    Route::resource('classroom-photos', ClassroomPhotoController::class)
        ->parameters(['classroom-photos' => 'classroomPhoto'])
        ->except('show');
    Route::resource('print-options', PrintOptionController::class)
        ->parameters(['print-options' => 'printOption'])
        ->except('show');
    Route::resource('packages', PackageController::class)->except('show');
    Route::resource('orders', AdminOrderController::class)
        ->only(['index', 'show', 'update', 'destroy']);

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

// Parent-facing wizard. One order can span multiple classrooms; one package
// covers the whole order. /objednavka dispatches to the right step.
Route::middleware('auth')->prefix('objednavka')->name('order.')->group(function () {
    Route::get('/', [WizardController::class, 'start'])->name('start');

    Route::get('/balik', [WizardController::class, 'package'])->name('package');
    Route::post('/balik', [WizardController::class, 'savePackages'])->name('package.save');

    Route::get('/fotky/{classroom}', [WizardController::class, 'photos'])->name('photos');

    Route::get('/sumar', [WizardController::class, 'summary'])->name('summary');
    Route::post('/odoslat', [WizardController::class, 'submit'])->name('submit');

    Route::get('/foto/{classroomPhoto}/{size?}', [OrderGalleryController::class, 'photo'])->name('photo');

    Route::post('/kosik/polozka', [CartController::class, 'addItem'])->name('cart.add');
    Route::patch('/kosik/{item}', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/kosik/{item}', [CartController::class, 'removeItem'])->name('cart.remove');

    Route::get('/objednavky', [OrderController::class, 'index'])->name('history');
    Route::get('/objednavky/{order}', [OrderController::class, 'show'])->name('show');
});

// Public gallery routes
Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [GalleryController::class, 'index'])->name('index');
    Route::get('/{slug}', [GalleryController::class, 'show'])->name('show');
    Route::get('/{slug}/photo/{photo}', [GalleryController::class, 'photo'])->name('photo');
});

require __DIR__.'/auth.php';
