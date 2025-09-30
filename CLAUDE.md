# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 blog application with AI-powered content generation, built using Filament for admin panel, SQLite database, and Firebase for push notifications. The blog features automatic visit tracking with bot detection, AI-generated posts using multiple providers, and a custom status tracking system.

## Common Commands

### Development
```bash
# Start development environment (server + queue + logs + vite)
composer dev

# Start individual services
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

### Testing
```bash
# Run all tests
composer test
# Or directly
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run specific test method
php artisan test --filter test_method_name
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Fix specific file
./vendor/bin/pint path/to/file.php
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Create migration
php artisan make:migration create_table_name

# Refresh database (drop all tables and re-migrate)
php artisan migrate:fresh
```

### Asset Building
```bash
# Build assets for production
npm run build

# Watch for changes
npm run dev
```

### Cache Management
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan event:cache
php artisan view:cache
php artisan route:cache
```

### Custom Commands
```bash
# Generate sitemap
php artisan generate:sitemap
```

### Filament
```bash
# Optimize Filament
php artisan filament:optimize

# Create Filament user
php artisan make:filament-user

# Upgrade Filament (automatically runs post-autoload)
php artisan filament:upgrade
```

## Architecture

### Core Patterns

**Status Management**: Models like `Post` use the `HasStatus` trait which provides:
- Automatic status tracking with timestamps
- Status history via polymorphic relationship
- Helper methods: `isStatus()`, `updateStatus()`, `fillStatus()`
- Status constants defined in model (e.g., `Post::STATUS_CREATED`, `Post::STATUS_COMPLETED`)

**Actions Pattern**: Business logic is encapsulated in Action classes under `app/Actions/`:
- `Action.php` - Base action class
- `Post/SendToGenerateAction.php` - Queue post for AI generation
- `Post/RegenerateAction.php` - Regenerate post content
- `Post/GetToGenerateAction.php` - Get posts ready for generation
- `GenerateSitemapAction.php` - Generate sitemap

**Filament Resources**: Admin interface uses a structured approach:
- `app/Filament/Resources/{Model}/` - Resource files
- `app/Filament/Resources/{Model}/Tables/` - Reusable table configurations
- Example: `Visits/Tables/VisitsTable.php` defines table structure used across resource

### AI Content Generation

The system supports multiple AI providers through a unified interface:
- `app/Services/AiGenerator/AiGenerator.php` - Main generator interface
- `app/Services/AiGenerator/AiGeneratorEnum.php` - Supported providers enum
- `app/Services/AiGenerator/Drivers/` - Provider-specific implementations
- Posts track AI metadata: `driver` and `model` fields

Posts have statuses for generation lifecycle:
- `created` - Initial state
- `generate` - Queued for generation
- `regenerate` - Queued for regeneration
- `completed` - Content generated successfully
- `error` - Generation failed

### Visit Tracking System

**Middleware**: `TrackVisits` middleware captures page visits:
- Only tracks GET requests with 200 responses
- Excludes AJAX, admin panel, telescope, and log-viewer
- Automatically detects and categorizes bots via user agent analysis
- Links visits to blog posts when available

**Bot Detection**: `Visit` model includes comprehensive bot detection:
- 40+ bot patterns including search engines, social media, tools
- Categorizes by bot type (Google, Bing, Facebook, etc.)
- Falls back to "Unknown Bot" for generic patterns
- Visit tracking automatically sets `bot_name` on creation

**Analytics Methods** in `Visit` model:
- `getTotalVisits()` - Total visit count
- `getTodayVisits()` - Today's visits
- `getTodayUniqueVisits()` - Unique IPs today
- `getUniqueVisitors()` - All-time unique IPs
- `getPopularPages($limit)` - Most visited pages
- `getVisitsByDate($days)` - Daily visit trends

**Scopes**: `today()`, `thisWeek()`, `thisMonth()`

### Database

- Default connection: SQLite (`database/database.sqlite`)
- Migrations in `database/migrations/`
- Models use standard Laravel Eloquent patterns
- Most models use `HasFactory` trait for testing/seeding

### Models and Relationships

**Post** (central model):
- `belongsTo`: `Category`, `Author`, `BasePrompt`
- `belongsToMany`: `Tags` (pivot table)
- `hasMany`: `Visits`
- Scopes: `published()` - filters by `published_at <= now()`
- Auto-generates UUID and slug on creation/update

**Visit**:
- `belongsTo`: `Post`
- Timestamps disabled (`$timestamps = false`)
- Uses custom `created_at` field

### Services

**FirebaseService** (`app/Services/FirebaseService.php`):
- Handles push notification functionality
- Manages Firebase configuration

### Frontend

- Vite for asset bundling
- TailwindCSS 4.0 for styling
- Assets in `resources/css/` and `resources/js/`
- Views follow Laravel Blade conventions

### Routes

- Web routes: `routes/web.php` - Blog pages, Firebase config
- API routes: `routes/api.php` - AI chat endpoint, push subscriptions
- Admin panel: Filament handles routing automatically (`/admin`)

## Development Workflow

1. **Adding a new feature to Filament**: Create resource files in `app/Filament/Resources/{Model}/`, extract table configuration to separate classes in `Tables/` subdirectory for reusability.

2. **Creating business logic**: Use Action classes in `app/Actions/` rather than putting logic directly in controllers or models.

3. **Working with statuses**: When adding status-tracked features, use the `HasStatus` trait and define status constants in the model class.

4. **Bot detection updates**: Add new bot patterns to the `Visit::botNames()` array with pattern => name mapping.

5. **Database changes**: Always create migrations. Use `php artisan make:migration` and run `php artisan migrate` after.

6. **AI Generator**: When adding new AI providers, create driver in `app/Services/AiGenerator/Drivers/` and update enum.

## Environment

- PHP 8.2+
- Laravel 12.x
- Default database: SQLite
- Queue driver configured per environment
- Telescope and Log Viewer require authentication (admin users only)

## Deployment

See `deploy.sh` for production deployment workflow:
1. Pull latest code
2. Install composer dependencies (production)
3. Run migrations
4. Run install command
5. Cache config and events
6. Clear views
7. Optimize Filament
