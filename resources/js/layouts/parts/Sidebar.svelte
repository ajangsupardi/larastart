<script lang="ts">
    import { Link, usePage } from '@inertiajs/svelte';
    import {
        LayoutDashboard,
        Users,
        ShieldCheck,
        Settings,
        MapPin,
        Building2,
        Landmark,
        Home,
        ChevronLeft,
        ChevronRight,
        X,
    } from '@lucide/svelte';
    import { fade } from 'svelte/transition';
    import { cn } from '@/lib/utils';
    import {
        sidebarOpen,
        sidebarCollapsed,
        toggleSidebarCollapsed,
        toggleSidebar,
    } from '@/stores/sidebar';

    type NavItem = {
        label: string;
        href: string;
        icon: typeof LayoutDashboard;
    };

    let {
        items = [
            { label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
            { label: 'Users', href: '/users', icon: Users },
            { label: 'Roles', href: '/roles', icon: ShieldCheck },
            { label: 'Provinces', href: '/provinces', icon: MapPin },
            { label: 'Regencies', href: '/regencies', icon: Building2 },
            { label: 'Districts', href: '/districts', icon: Landmark },
            { label: 'Villages', href: '/villages', icon: Home },
            { label: 'Settings', href: '#', icon: Settings },
        ] as NavItem[],
    } = $props();

    const page = usePage();

    const permissions = $derived(page.props.auth?.permissions ?? {});
    const canViewUsers = $derived(permissions.users?.length > 0);
    const canViewRoles = $derived(permissions.roles?.length > 0);
    const canViewProvinces = $derived(permissions.provinces?.length > 0);
    const canViewRegencies = $derived(permissions.regencies?.length > 0);
    const canViewDistricts = $derived(permissions.districts?.length > 0);
    const canViewVillages = $derived(permissions.villages?.length > 0);

    let visibleItems = $derived(
        items.filter((item) => {
            if (item.href === '/users') {
                return canViewUsers;
            }

            if (item.href === '/roles') {
                return canViewRoles;
            }

            if (item.href === '/provinces') {
                return canViewProvinces;
            }

            if (item.href === '/regencies') {
                return canViewRegencies;
            }

            if (item.href === '/districts') {
                return canViewDistricts;
            }

            if (item.href === '/villages') {
                return canViewVillages;
            }

            return true;
        }),
    );
</script>

<!-- Mobile overlay backdrop -->
{#if $sidebarOpen}
    <div
        role="presentation"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        onclick={toggleSidebar}
        onkeydown={(e) => {
            if (e.key === 'Escape') {
                toggleSidebar();
            }
        }}
        transition:fade={{ duration: 150 }}
    ></div>
{/if}

<!-- Sidebar -->
<!-- On desktop: static inline flex element. On mobile: fixed overlay sliding from left -->
<aside
    class={cn(
        'flex flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950',
        'transition-all duration-200',
        // Desktop: static, always visible if sidebarOpen
        'lg:static lg:z-auto',
        // Mobile: fixed overlay, slide in/out
        'fixed inset-y-0 left-0 z-50',
        $sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0',
        $sidebarCollapsed ? 'w-16' : 'w-64',
    )}
>
    <div
        class="relative flex h-16 items-center justify-center border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-950"
    >
        <a href="/">
            {#if $sidebarCollapsed}
                <img
                    src="/brand/brand-light-sm.svg"
                    alt=""
                    class="w-7 block dark:hidden"
                />
                <img
                    src="/brand/brand-dark-sm.svg"
                    alt=""
                    class="w-7 hidden dark:block"
                />
            {:else}
                <img
                    src="/brand/brand-light.svg"
                    alt={page.props.name}
                    class="h-8 block dark:hidden"
                />
                <img
                    src="/brand/brand-dark.svg"
                    alt={page.props.name}
                    class="h-8 hidden dark:block"
                />
            {/if}
        </a>
        <!-- Close button for mobile -->
        <button
            onclick={toggleSidebar}
            class="absolute right-4 rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 lg:hidden dark:hover:bg-gray-800"
        >
            <X size={18} />
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-3">
        {#each visibleItems as item (item.href)}
            <Link
                href={item.href}
                onclick={() => {
                    if (window.innerWidth < 1024) {
                        toggleSidebar();
                    }
                }}
                class={cn(
                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                    page.url === item.href
                        ? 'bg-brand/10 text-brand dark:bg-brand/10 dark:text-brand'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100',
                    $sidebarCollapsed && 'justify-center px-0',
                )}
            >
                <item.icon size={20} class="shrink-0" />
                {#if !$sidebarCollapsed}
                    <span>{item.label}</span>
                {/if}
            </Link>
        {/each}
    </nav>

    <div
        class="flex h-16 items-center border-t border-gray-200 px-3 dark:border-gray-800"
    >
        <button
            onclick={toggleSidebarCollapsed}
            class={cn(
                'flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-500 transition-colors',
                'hover:bg-gray-100 dark:hover:bg-gray-800',
                $sidebarCollapsed && 'justify-center px-0',
            )}
        >
            {#if $sidebarCollapsed}
                <ChevronRight size={18} />
            {:else}
                <ChevronLeft size={18} />
                <span>Collapse</span>
            {/if}
        </button>
    </div>
</aside>
