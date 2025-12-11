# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A photographer portfolio website built with Laravel 12, Inertia.js, Vue 3, and Tailwind CSS. The application allows photographers to showcase their work in organized albums with a clean, modern gallery interface.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API) with Inertia.js
- **Styling**: Tailwind CSS v4
- **Database**: MySQL/MariaDB (supports SQLite)
- **Image Processing**: Intervention Image v3
- **Build Tool**: Vite
- **Authentication**: Laravel Breeze (Inertia stack)

## Development Commands

### Setup
```bash
composer install
npm install --legacy-peer-deps
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

### Development
```bash
# Start both Laravel and Vite dev servers (recommended)
composer dev

# Or manually in separate terminals:
php artisan serve          # Laravel dev server (http://localhost:8000)
npm run dev                # Vite dev server with HMR
```

### Testing
```bash
php artisan test           # Run all tests
php artisan test --filter=TestName  # Run specific test
composer test              # Alternative (clears config first)
```

### Code Quality
```bash
php artisan pint           # Laravel Pint code formatter
```

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Architecture

### Inertia.js Pattern
This application uses Inertia.js, which bridges Laravel and Vue without building a separate API:
- **Server-side**: Controllers return `Inertia::render()` responses instead of views
- **Client-side**: Vue pages receive props directly from controllers
- **Routing**: All routes defined in `routes/web.php`, no separate API routes
- **Forms**: Use Inertia's form helpers for validation and error handling
- **Navigation**: Use `<Link>` components or `router.visit()` for SPA-like navigation

### Image Processing Pipeline
Photos uploaded through the admin panel are automatically processed into three sizes:
- **Original**: Stored in `storage/app/public/photos/original/` (unmodified)
- **Medium**: 1200px wide in `storage/app/public/photos/medium/`
- **Thumbnail**: 400px wide in `storage/app/public/photos/thumbnails/`

When uploading or updating photos:
1. Use `Intervention\Image\Laravel\Facades\Image` facade
2. Process all three sizes using `scaleDown()` to maintain aspect ratio
3. Store paths in the `photos` table (`image_path`, `medium_path`, `thumbnail_path`)
4. Always delete old images when replacing to prevent orphaned files

### Admin Access Control
Admin routes use the custom `admin` middleware:
- Checks `$user->is_admin` boolean field
- Registered in `bootstrap/app.php` as middleware alias
- Applied to all `/admin/*` routes with `->middleware(['auth', 'admin'])`
- Returns 403 if user is not authenticated or not an admin

### Database Relationships
Key model relationships to understand:
- `Album` → hasMany `Photo` (cascade delete)
- `Photo` → belongsTo `Album`

Photos cascade delete when albums are deleted.

## File Structure

```
app/
├── Http/Controllers/
│   ├── Admin/              # Admin CRUD controllers (albums, photos)
│   ├── GalleryController.php   # Public album/photo viewing
│   └── ProfileController.php   # User profile management
├── Models/                 # Eloquent models (Album, Photo, User)
└── Http/Middleware/IsAdmin.php  # Admin authorization

resources/js/
├── Components/             # Reusable Vue components
├── Layouts/                # Layout wrapper components
└── Pages/                  # Full-page Inertia components
    ├── Admin/              # Admin panel pages
    │   ├── Albums/         # Album management
    │   └── Photos/         # Photo management
    └── Gallery/            # Public gallery pages

routes/
├── web.php                 # All application routes
└── auth.php                # Laravel Breeze auth routes
```

## Key Conventions

### Model Slug Generation
The `Album` model auto-generates slugs in its `boot()` method:
```php
static::creating(function ($album) {
    if (empty($album->slug)) {
        $album->slug = Str::slug($album->title);
    }
});
```

### Bulk Photo Upload
`PhotoController@store` accepts an array of images:
- Validates `images.*` as image files (max 20MB each)
- Generates unique filenames with `uniqid()`
- Creates titles from original filename if no base title provided
- Increments `display_order` for each uploaded photo

### Frontend Asset Paths
Access uploaded images via the `/storage` symlink:
```js
`/storage/${photo.medium_path}`
```
Ensure `php artisan storage:link` has been run.

## Common Patterns

### Creating Inertia Pages
When adding new pages:
1. Create controller method returning `Inertia::render('PageName', [...props])`
2. Create Vue component at `resources/js/Pages/PageName.vue`
3. Define route in `routes/web.php`
4. Props are automatically available in the Vue component

### Vue Component Props
Inertia pages receive props via `defineProps()`:
```vue
<script setup>
const props = defineProps({
  photos: Array,
  album: Object
})
</script>
```

### Form Handling
Use Inertia's form helper for validation errors:
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: ''
})

const submit = () => {
  form.post(route('admin.albums.store'))
}
</script>
```

### Route Helpers (Ziggy)
Use the `route()` helper in Vue components:
```vue
<Link :href="route('gallery.show', album.slug)">
```

## Database Notes

- Default connection is MySQL/MariaDB
- SQLite is supported for quick development (set `DB_CONNECTION=sqlite` in `.env`)
- Migrations use `foreignId()->constrained()->cascadeOnDelete()` for relationships
- Photos cascade delete when albums are deleted

## NPM Dependencies Note

Some packages have peer dependency conflicts. Always install with:
```bash
npm install --legacy-peer-deps
```
