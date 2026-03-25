# Copilot Instructions for TripAC FMIS

## Project Overview
**TRIPAC FMIS** is a Laravel 12 fraternity/sorority member management system with role-based access control. It tracks chapters, members, and users across three roles: Super Admin, Chapter Admin, and Member.

## Architecture & Data Flow

### Core Models & Relationships
- **User** (auth model): `ROLE_SUPER_ADMIN` manages all chapters; `ROLE_CHAPTER_ADMIN` manages one chapter; `ROLE_MEMBER` is data only
- **Chapter**: Contains members and admins. One-to-many with User and Member
- **Member**: Fraternity/sorority member records with initiation dates (GT, MWW) and batch tracking
- **Relationship Flow**: `User -> Chapter -> Member` (User optionally assigned to one chapter)

### Key Files Structure
- `app/Models/` - Eloquent models with role constants defined in User.php
- `routes/web.php` - Auth routes from Laravel Breeze + custom password change flow + role-based dashboards
- `app/Http/Middleware/` - **RoleMiddleware** (role gate), **ForcePasswordChange** (first-login redirect)
- `database/seeders/` - Initial data: SuperAdminSeeder creates default admin (username: `superadmin`, password: `password`)

## Authentication & Authorization

### Middleware Pipeline
1. **ForcePasswordChange** (global web): Redirects `first_login=true` users to `/change-password` route before accessing other pages
2. **RoleMiddleware**: Use syntax `middleware(['auth', 'role:super_admin,chapter_admin'])` to gate routes by role
3. **Standard Laravel auth**: Uses session-based web guard (no API tokens currently)

### Role Usage Pattern
```php
// Routes gated by role (see web.php line 36-38)
Route::middleware(['auth', 'role:' . User::ROLE_SUPER_ADMIN])->group(function () { ... });

// Check roles in controllers/models
if ($request->user()->isSuperAdmin()) { ... }
```

## Development Workflows

### Setup & Installation
```bash
composer run-script setup  # Full setup: composer install, migrations, npm install, build
php artisan migrate        # Run migrations (uses SQLite by default in testing, configure DB_* in .env)
npm install               # Install Tailwind CSS, Alpinejs, Vite deps
```

### Development Server
```bash
composer run-script dev   # Concurrent: Laravel serve + queue listener + pail logs + Vite HMR
php artisan serve         # Single server on port 8000
php artisan tinker        # Interactive shell for model experimentation
```

### Testing
```bash
composer run-script test  # Runs: config:clear + phpunit (Unit + Feature suites)
php artisan test --filter=ClassName  # Run specific test class
```

### Database
- Migrations: `database/migrations/` timestamped format
- Seeders: Run via `php artisan migrate:fresh --seed` (use carefully—destroys data)
- Factories: `database/factories/UserFactory.php` for test data generation

## Project-Specific Patterns

### First-Login Force Password Change
- New users created with `first_login = true`
- Middleware redirects to `/change-password` (route in web.php lines 17-31)
- POST endpoint sets `first_login = false` and bcrypts new password
- Only exception to redirect: already on `/change-password` or `/logout` routes

### User Status & Role Fields
- `role` enum: `super_admin`, `chapter_admin`, `member` (stored in users table)
- `status` enum: `active`, `inactive` (controls account availability)
- `chapter_id` nullable foreign key: NULL for super admin, required for chapter admin/member users

### Blade View & Frontend
- Templating: `resources/views/` Blade templates (Laravel's PHP-based template engine)
- Styling: **Tailwind CSS 3** + **AlpineJS** for interactivity (configured in `tailwind.config.js`)
- Asset build: **Vite** (configured in `vite.config.js`, input files: `resources/css/app.css`, `resources/js/app.js`)
- Layouts: Base layout likely in `resources/views/layouts/`

### Authentication Routes
- Powered by **Laravel Breeze** (official starter kit)
- Controllers: `app/Http/Controllers/Auth/` (RegisteredUserController, AuthenticatedSessionController, etc.)
- Routes auto-registered via `routes/auth.php` included in web.php
- Custom password change logic overlays Breeze defaults

## Common Tasks

### Add a New Route with Role Check
```php
// In routes/web.php
Route::middleware(['auth', 'role:' . User::ROLE_CHAPTER_ADMIN])->group(function () {
    Route::get('/chapter/members', [ChapterMemberController::class, 'index'])->name('chapter.members');
});
```

### Create a New Model with Seeder
1. Generate: `php artisan make:model ModelName -m -s` (creates model, migration, seeder)
2. Define relations in model (use `hasMany()`, `belongsTo()` methods)
3. Update migration with schema
4. Update DatabaseSeeder to call new seeder

### Query Data in Routes/Controllers
Use Eloquent (ORM):
```php
$chapters = Chapter::where('status', 'active')->with('members')->get();
$user->chapter()->associate($chapter)->save();
```

## External Dependencies & Config
- **PHP 8.2+** required (composer.json specifies `^8.2`)
- **Laravel Framework 12**: All config in `config/` directory
- **Database**: SQLite by default (perfect for development), configure PostgreSQL/MySQL in `config/database.php`
- **Cache**: Array driver in testing, configure in `config/cache.php`
- **Email**: Array driver by default, set MAIL_* in .env for real SMTP
- **No external APIs integrated currently**

## File Locations Reference
| Purpose | Location |
|---------|----------|
| Authentication logic | `routes/auth.php`, `app/Http/Controllers/Auth/` |
| Models & relationships | `app/Models/User.php`, `Chapter.php`, `Member.php` |
| Middleware | `app/Http/Middleware/RoleMiddleware.php`, `ForcePasswordChange.php` |
| Blade views | `resources/views/` (dashboard, auth, profile, components) |
| CSS/JS assets | `resources/css/app.css`, `resources/js/app.js` |
| Database setup | `database/migrations/`, `seeders/` |
| Tests | `tests/Feature/`, `tests/Unit/` |
| Web routes | `routes/web.php` |

## Environment Setup
Copy `.env.example` to `.env` and configure:
- `APP_KEY`: Generated via `php artisan key:generate`
- `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Set for your database
- `MAIL_*`: Configure for password reset emails
- Default .env points to SQLite at `database/database.sqlite`
