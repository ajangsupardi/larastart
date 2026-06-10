# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 13 + Inertia.js v3 + Svelte 5 + Tailwind CSS v4. Uses Pest v4 for tests, Pint for PHP formatting, ESLint + Prettier for frontend. SQLite in development and testing.

## Commands

```bash
# First-time setup
composer run setup

# Start all dev services (PHP server, queue, log viewer, Vite)
composer run dev

# Run all tests
php artisan test --compact

# Run specific test
php artisan test --compact --filter=UserControllerTest

# PHP formatting
composer lint                  # fix
composer lint:check            # check only

# JS/TS linting
npm run lint                   # fix
npm run lint:check             # check only

# Frontend formatting
npm run format                 # fix
npm run format:check           # check only

# TypeScript type check
npm run types:check

# Full CI check (lint + format + types + tests)
composer ci:check
```

After modifying PHP files, run `vendor/bin/pint --dirty --format agent` to auto-fix formatting.

## Architecture

### Request Lifecycle

1. Laravel routes (`routes/web.php`) dispatch to controllers
2. Controllers return `Inertia::render('PageName', $props)` instead of Blade views
3. Inertia serves Svelte components from `resources/js/pages/` as the frontend

### Shared Inertia Props

`HandleInertiaRequests` middleware (`app/Http/Middleware/HandleInertiaRequests.php`) injects into every page:
- `auth.user` — authenticated user with roles loaded
- `auth.permissions` — merged permissions map `{ resource: string[] }` built from all user roles
- `flash.success` / `flash.error` — session flash messages
- `name` — app name

### RBAC System

Permissions live as a JSON field on `roles` table: `{ resource: [actions] }`. The `CheckPermission` middleware (aliased as `permission`) guards routes:

```php
->middleware('permission:users,create')
```

`User::hasPermission($resource, $action)` checks across all assigned roles. The frontend receives the merged permissions map via shared props to conditionally render UI elements.

### Wayfinder (Type-Safe Routes)

Wayfinder auto-generates TypeScript helpers on build. Import controller actions from `@/actions/` and named routes from `@/routes/`:

```ts
import { UserController } from '@/actions/App/Http/Controllers/UserController';
import { users } from '@/routes/users';
```

Re-run `npm run build` or `npm run dev` after adding/renaming routes to regenerate these files.

### Frontend Structure

- `resources/js/pages/` — Inertia page components (maps 1:1 to controller renders)
- `resources/js/layouts/` — `DashboardLayout.svelte` and `AuthLayout.svelte`
- `resources/js/layouts/parts/` — Sidebar, TopNavbar, Footer, GlobalSearch, ThemeCustomizer, etc.
- `resources/js/components/` — shared components (Input, FlashMessages, DeleteConfirmModal, AppHead)
- `resources/js/stores/` — Svelte writable stores: `theme`, `sidebar`, `search`
- `resources/js/types/` — TypeScript types

### Theme System

Dark mode uses Tailwind's `dark:` variant. The `themeMode` store in `resources/js/stores/theme.ts` persists `light | dark | system` to `localStorage` and toggles the `dark` class on `<html>`.

## Design System

See `DESIGN.md` for the full token reference. Key conventions:

- Brand color: `bg-brand` / `text-brand` (maps to `#FF2D20`, defined as `--color-brand` in `resources/css/app.css`)
- Dark mode bg hierarchy: `bg-gray-50 dark:bg-gray-950` (page) → `dark:bg-gray-900` (content/cards) → `dark:bg-gray-950` (sidebar, auth)
- Content card: `rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900`
- Active nav link: `bg-brand/10 text-brand dark:bg-brand/10 dark:text-brand`
- Utility: `cn()` from `resources/js/lib/utils.ts` (clsx + tailwind-merge)

## Testing

Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`). All Feature tests use `RefreshDatabase`. Create tests with:

```bash
php artisan make:test --pest SomeFeatureTest
```

Default seeded accounts: `super@example.com`, `admin@example.com`, `editor@example.com`, `viewer@example.com` — all with password `password`.

## Conventions

- Follow sibling file patterns for structure, naming, and approach before writing new code
- Use Wayfinder imports for all route/URL generation on the frontend
- Use Form Request classes for all controller validation
- Use Eloquent API Resources (`UserResource`, `RoleResource`) when returning model data
- PHP 8 constructor property promotion; explicit return types on all methods
- All new routes go in `routes/web.php` using named routes
