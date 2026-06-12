<div align="center">

# Larastart

Production-ready Laravel starter kit with modern stack

![Preview](public/screenshot.png)

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-v3-9553F1?style=for-the-badge)](https://inertiajs.com)
[![Svelte](https://img.shields.io/badge/Svelte-5-FF3E00?style=for-the-badge&logo=svelte&logoColor=white)](https://svelte.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Pest](https://img.shields.io/badge/Pest-v4-FDAE21?style=for-the-badge)](https://pestphp.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## Overview

Larastart is a production-ready Laravel 13 starter kit built with Inertia.js v3 + Svelte 5. It ships with a complete admin dashboard, role-based access control, geographic data management, and a full settings system — everything you need to launch a modern web application without building boilerplate from scratch.

**Key features:**

- Role-Based Access Control (RBAC) with permission and ownership middleware
- Full authentication flow (login, register, forgot password, email verification)
- CRUD for users, roles, geographic data, and occupations
- Activity audit log and real-time notifications
- CSV export for all resources
- Soft delete with trash/restore system
- Collapsible sidebar, global search (Ctrl+K), and responsive design
- Dark mode with system preference support
- Type-safe routing via Wayfinder

---

## Tech Stack

| Layer | Technologies |
|-------|-------------|
| **Backend** | Laravel 13, PHP 8.4, SQLite (dev/test) |
| **Frontend** | Inertia.js v3, Svelte 5, Tailwind CSS v4, Vite 8 |
| **Code Quality** | Laravel Pint, ESLint, Prettier, TypeScript |
| **Testing** | Pest v4 |

---

## Features

### Authentication & Authorization

- Login, Register, Forgot Password, Email Verification
- Role-Based Access Control (RBAC) with 7 resources: users, roles, provinces, regencies, districts, villages, occupations
- `permission` middleware on all routes (`middleware('permission:resource,action')`)
- `ownership` middleware — users can only edit their own data unless they have admin privileges
- Soft deletes for users and roles

### User Management

- Full CRUD with avatar upload (cropped to 400x400 WebP via Cropper.js)
- System user protection (default super admin cannot be deleted)
- Pagination and search with debounced input

### Geographic Data

- Province → Regency → District → Village hierarchy for Indonesian geographic data
- Indonesian postal code data (auto-downloaded via seeder)
- Cascading dropdowns for location selection

### Occupations

- 89 official job types from Dukcapil (Indonesian civil registration)
- Full CRUD with search and pagination

### Settings

- General settings (app name, timezone)
- Appearance (light/dark/system theme with localStorage persistence)
- Security (change password)
- Notifications preferences
- System info (PHP version, Laravel version, disk usage)
- Cache management

### Audit & Monitoring

- Activity audit log (who did what, when)
- Real-time notifications (bell icon in navbar)
- CSV export for all resources
- Soft delete trash with restore and force-delete

### Developer Experience

- Type-safe routing via Wayfinder (auto-generated TypeScript route helpers)
- Global search with Ctrl+K keyboard shortcut
- Collapsible sidebar with grouped navigation
- Responsive design (mobile-friendly with hamburger menu)

---

## Getting Started

### Quick Setup

```bash
# Clone the repo
git clone git@github.com:ajangsupardi/larastart.git
cd larastart

# Setup (installs deps, generates key, runs migrations, builds frontend)
composer run setup
```

### Manual Setup

```bash
# Clone the repo
git clone git@github.com:ajangsupardi/larastart.git
cd larastart

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Run database migrations with seed data
php artisan migrate --force

# Install Node dependencies and build assets
npm install
npm run build
```

### Start Development

```bash
composer run dev
```

This starts all services concurrently:

| Service | Purpose |
|---------|---------|
| `php artisan serve` | HTTP server |
| `php artisan queue:listen` | Queue worker |
| `php artisan pail` | Log viewer |
| `npm run dev` | Vite dev server |

---

## Default Accounts

After seeding, the following accounts are available:

| Email | Password | Role |
|-------|----------|------|
| `super@example.com` | `password` | Super Administrator |
| `admin@example.com` | `password` | Administrator |
| `editor@example.com` | `password` | User |
| `viewer@example.com` | `password` | User |

---

## Commands

### Composer Scripts

| Command | Description |
|---------|-------------|
| `composer run setup` | Install deps, generate key, run migrations, build frontend |
| `composer run dev` | Start all dev services (server, queue, logs, Vite) |
| `composer run test` | Clear config, run Pint check, run Pest tests |
| `composer run lint` | Format PHP code with Laravel Pint |
| `composer run lint:check` | Check PHP formatting without writing |
| `composer run ci:check` | Full CI check (lint + format + types + tests) |

### NPM Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Start Vite dev server |
| `npm run build` | Build production assets |
| `npm run lint` | Fix JS/TS linting with ESLint |
| `npm run lint:check` | Check JS/TS linting without writing |
| `npm run format` | Format code with Prettier |
| `npm run format:check` | Check formatting without writing |
| `npm run types:check` | TypeScript type checking via svelte-check |

### Artisan & CLI

| Command | Description |
|---------|-------------|
| `php artisan test --compact` | Run all Pest tests |
| `vendor/bin/pint --dirty --format agent` | Format only modified PHP files |

---

## Architecture

```
routes/web.php  →  Controllers  →  Inertia::render()  →  Svelte pages
                                                              ↓
                              Wayfinder generates TypeScript route helpers
                              from Laravel routes → @/routes/ & @/actions/
```

**RBAC flow:**
1. Permissions are stored as a JSON object on each role (`{ resource: [actions] }`)
2. `CheckPermission` middleware validates `{resource, action}` on every route
3. `CheckOwnership` middleware ensures users can only modify their own records
4. Permissions are passed to frontend as props for conditional UI rendering

**Directory structure:**

```
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controller classes
│   │   ├── Middleware/        # CheckPermission, CheckOwnership, HandleInertiaRequests
│   │   └── Requests/          # Form request validation
│   ├── Models/                # User, Role, Province, Regency, District, Village, Occupation, AuditLog, Setting
│   └── Notifications/         # Email notifications
├── database/
│   ├── factories/             # Model factories
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders (includes geographic + occupation data)
├── resources/js/
│   ├── actions/               # Wayfinder-generated controller actions
│   ├── components/            # Reusable Svelte components
│   ├── layouts/               # Dashboard & Auth layouts
│   │   └── parts/             # Sidebar, TopNavbar, Footer, etc.
│   ├── lib/                   # Utility functions
│   ├── pages/                 # Inertia page components (auth, Users, Roles, Settings, etc.)
│   ├── routes/                # Wayfinder-generated route helpers
│   ├── stores/                # Svelte writable stores
│   └── types/                 # TypeScript type definitions
├── routes/                    # Laravel route definitions
└── tests/                     # Pest test suites
```

---

## License

This project is open-sourced under the [MIT License](LICENSE).
