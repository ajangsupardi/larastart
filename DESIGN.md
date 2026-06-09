# Design System

## Brand Color

| Token | Value | Usage |
|-------|-------|-------|
| `brand` | `#FF2D20` | Primary accent, interactive elements, nav active, buttons |

Warna berasal dari brand SVG (`/public/brand/brand-light.svg`).
Tailwind v4 token: `--color-brand` di `resources/css/app.css`.

## Dark Mode Hierarchy (3 levels)

| Level | Surface | Class |
|-------|---------|-------|
| Page background | Outer screen | `bg-gray-50 dark:bg-gray-950` |
| Content area | Main wrapper | `dark:bg-gray-900` |
| Cards | Content cards | `dark:bg-gray-900` |
| Special surfaces | Sidebar, auth card, etc. | `dark:bg-gray-950` |

## Cards

### Content card
`rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900`

### Summary stat card
`rounded-b-xl border border-gray-200 bg-white shadow-sm transition-colors duration-150 hover:shadow-md dark:border-gray-800 dark:bg-gray-900` + `overflow-hidden`
- Inner accent bar: `h-1 bg-{brand|emerald|amber|violet}-500`
- Content wrapper: `flex w-full flex-col p-5`
- Icon/text accent: `text-{color}-600 dark:text-{color}-400`

### Auth card
`rounded-xl border border-gray-200 bg-white p-8 shadow-lg shadow-gray-200/50 dark:border-gray-800 dark:bg-gray-950 dark:shadow-gray-950/50`

### Filter bar
`rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950`

## Typography

| Element | Classes |
|---------|---------|
| Page title (h1) | `text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100` |
| Description | `mt-1 text-sm text-gray-500 dark:text-gray-400` |
| Card heading | `text-lg font-semibold text-gray-900 dark:text-gray-100` |
| Stat value | `text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100` |
| Card label | `text-sm font-medium text-gray-500 dark:text-gray-400` |
| Body | `text-sm text-gray-500 dark:text-gray-400` |
| Muted | `text-xs text-gray-500 dark:text-gray-400` |
| Extra muted | `text-xs text-gray-400 dark:text-gray-500` |
| Breadcrumb | `text-sm text-gray-500 dark:text-gray-400` |
| Breadcrumb current | `text-gray-900 dark:text-gray-100` |
| Breadcrumb separator `/` | `text-gray-300 dark:text-gray-600` |
| Auth quote | `text-lg font-medium leading-relaxed` |

## Buttons

### Base
`rounded-lg px-4 py-2 text-sm font-medium transition-colors`

### Primary
`bg-brand text-white hover:bg-brand/90 active:scale-[0.98]`

### Secondary
`border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800`

### Icon / icon-only
`rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800`

### Danger (logout confirm)
`rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700`

## Navigation (Sidebar)

### Nav link (active)
`flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors bg-brand/10 text-brand dark:bg-brand/10 dark:text-brand`

### Nav link (inactive)
`flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100`

### Collapsed state
Add `justify-center px-0`

## Forms

### Input wrapper
`flex items-center gap-2 rounded-lg border bg-gray-50 pl-4 pr-1.5 text-sm transition-all border-gray-200 dark:border-gray-700 dark:bg-gray-900`

### Input focused
`border-brand/40 bg-white ring-2 ring-brand/20 dark:border-brand dark:bg-gray-800`

### Input element
`w-full border-0 bg-transparent py-1.5 text-sm outline-none placeholder:text-gray-400 dark:text-gray-100`

### KBD badge
`ml-auto hidden shrink-0 items-center rounded border border-gray-200 bg-white px-1.5 py-1.5 text-[11px] font-medium leading-none text-gray-400 sm:flex dark:border-gray-600 dark:bg-gray-800`

## Flash Messages

### Success
`fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-lg dark:border-green-800 dark:bg-green-950 dark:text-green-200`
- Icon circle: `flex h-5 w-5 items-center justify-center rounded-full bg-green-200 text-green-700 dark:bg-green-700 dark:text-green-200`
- Dismiss: `rounded p-0.5 transition-colors hover:bg-green-100 dark:hover:bg-green-900`

### Error
`fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-lg dark:border-red-800 dark:bg-red-950 dark:text-red-200`
- Icon circle: `flex h-5 w-5 items-center justify-center rounded-full bg-red-200 text-red-700 dark:bg-red-700 dark:text-red-200`
- Dismiss: `rounded p-0.5 transition-colors hover:bg-red-100 dark:hover:bg-red-900`

## Modals / Overlays

### Backdrop
`fixed inset-0 z-50 bg-black/50 backdrop-blur-sm`

### Modal card
`w-80 rounded-xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900`

### Confirmation icon circle
`flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-950`

## Dropdowns / Menus

### Dropdown container
`absolute right-0 top-full z-50 mt-1 w-56 overflow-hidden rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900`

### Dropdown item
`flex items-center gap-3 rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800`

## Search

### Search bar (topbar center)
`relative w-full max-w-md` wrapper, input `rounded-lg border bg-gray-50 pl-4 pr-1.5 text-sm`

### Search dropdown
`absolute left-0 top-full z-50 mt-1 w-full overflow-hidden rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900`

### Search result item
`block rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800`

## Avatars

### User avatar
`flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand to-brand/60 text-sm font-medium text-white`

## Notification Bell

### Bell icon
`relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800`

### Red dot
`absolute right-1.5 top-1.5 flex h-2 w-2 rounded-full bg-brand ring-2 ring-white dark:ring-gray-950`

## Theme Customizer

### Slide-in panel
`fixed right-0 top-0 z-50 h-full w-80 border-l border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-950`

### Section label
`mb-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400`

### Theme option (selected)
`flex w-full items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-all border-brand bg-brand/10 dark:border-brand dark:bg-brand/10`

### Theme option (unselected)
`flex w-full items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-all border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800`

## Auth Layout

### Branding panel (desktop left)
`flex w-[480px] flex-col items-center justify-between bg-brand p-12 dark:bg-gray-950 dark:bg-none`

### Form panel (desktop right)
`flex flex-1 flex-col items-center justify-center bg-white px-8 dark:bg-gray-900`
- Form width: `w-full max-w-sm`

### Auth footer text
`text-xs text-gray-400 dark:text-gray-500`

## Topbar

`flex h-16 items-center border-b border-gray-200 bg-white px-4 transition-colors duration-150 dark:border-gray-800 dark:bg-gray-900`
- 3 sections: hamburger (left) | search (center) | actions (right)

## Sidebar

### Sidebar aside
`flex flex-col border-r border-gray-200 bg-white transition-all duration-200 dark:border-gray-800 dark:bg-gray-950`
- Expanded: `w-64`, Collapsed: `w-16`
- Mobile: `fixed inset-y-0 left-0 z-50`
- Desktop: `lg:static lg:z-auto lg:translate-x-0`
- Slide: `translate-x-0` / `-translate-x-full`

### Sidebar header
`relative flex h-16 items-center justify-center border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-950`

### Sidebar footer
`flex h-16 items-center border-t border-gray-200 px-3 dark:border-gray-800`

## Footer

`flex h-16 items-center border-t border-gray-200 bg-white transition-colors duration-150 dark:border-gray-800 dark:bg-gray-900`

### Footer content
`flex w-full flex-col items-center justify-between gap-1 px-4 py-3 text-xs text-gray-500 sm:flex-row sm:px-6 sm:text-sm dark:text-gray-400`

## Page Content

### Page header area
`border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900`

### Content wrapper
`mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8`

## Borders

- Light: `border-gray-200`
- Dark: `dark:border-gray-800`

## Hover States (standard)

| Element | Light | Dark |
|---------|-------|------|
| Buttons/items | `hover:bg-gray-100` | `dark:hover:bg-gray-800` |
| Secondary buttons | `hover:bg-gray-50` | `dark:hover:bg-gray-800` |
| Text links | `hover:text-gray-700` | `dark:hover:text-gray-300` |

## Transitions & Animations

- Color-only: `transition-colors` / `transition-colors duration-150`
- All properties: `transition-all duration-150` / `transition-all duration-200`
- Svelte `fade` (150-200ms): overlays, backdrops
- Svelte `fly`: flash messages (`x:20, 300ms`), search dropdown (`y:-8, 100ms`), user menu (`y:-8, 150ms`)
- Svelte `slide` (200ms): theme customizer panel

## Z-Index Scale

| Layer | Z |
|-------|---|
| Sidebar (mobile) | `z-50` |
| Modals & overlays | `z-50` |
| Flash messages | `z-50` |
| Dropdowns | `z-50` |
| Search dropdown | `z-50` |
| Sidebar backdrop | `z-40` |
| Overlay backdrop | `z-40` |

## Spacing Scale Used

`p-1.5`, `p-2`, `p-3`, `p-4`, `p-5`, `p-6`, `p-8`, `p-12`
`px-4`, `py-2`, `py-3`, `py-6`
`gap-1`, `gap-2`, `gap-3`, `gap-4`
`mt-1`, `mt-2`, `mt-4`, `mt-6`
`mb-2`, `mb-3`, `mb-6`
