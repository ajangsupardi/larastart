<script lang="ts">
    import { Link, router, usePage } from '@inertiajs/svelte';
    import {
        Menu,
        Search,
        LogOut,
        Moon,
        Sun,
        Bell,
        User,
        Users,
        LayoutDashboard,
        ShieldCheck,
        FileText,
    } from '@lucide/svelte';
    import { onMount } from 'svelte';
    import type { Component } from 'svelte';
    import { fly, fade } from 'svelte/transition';
    import { cn } from '@/lib/utils';
    import {
        openSearch,
        searchOpen,
        searchQuery,
        searchResults,
        searchLoading,
        getFilteredStaticItems,
    } from '@/stores/search';
    import { toggleSidebar } from '@/stores/sidebar';
    import { themeMode } from '@/stores/theme';

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const staticItems = $derived(getFilteredStaticItems(permissions));
    let combinedResults = $derived.by(() => {
        const filtered =
            localQuery.length >= 2
                ? staticItems.filter((item) =>
                      item.label
                          .toLowerCase()
                          .includes(localQuery.toLowerCase()),
                  )
                : [];

        return [...filtered, ...$searchResults];
    });
    let userMenuOpen = $state(false);
    let showLogoutConfirm = $state(false);
    let searchFocused = $state(false);
    let selectedIndex = $state(0);
    let inputEl: HTMLInputElement | undefined = $state();
    let localQuery = $state('');

    function getIcon(item: { type: string; icon?: string }): Component {
        if (item.icon) {
            switch (item.icon) {
                case 'LayoutDashboard':
                    return LayoutDashboard;
                case 'Users':
                    return Users;
                case 'ShieldCheck':
                    return ShieldCheck;
                case 'User':
                    return User;
            }
        }

        switch (item.type) {
            case 'user':
                return User;
            case 'role':
                return ShieldCheck;
            default:
                return FileText;
        }
    }

    function handleSearchKeydown(e: KeyboardEvent) {
        if (e.key === 'Escape') {
            searchFocused = false;
            inputEl?.blur();
            localQuery = '';
            searchQuery.set('');

            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(
                selectedIndex + 1,
                combinedResults.length - 1,
            );
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
        }

        if (e.key === 'Enter') {
            e.preventDefault();
            const target = combinedResults[selectedIndex];

            if (target) {
                const href = target.href;
                searchFocused = false;
                inputEl?.blur();
                localQuery = '';
                searchQuery.set('');
                router.visit(href);
            }
        }
    }

    function selectResult(result: { href: string }) {
        const href = result.href;
        searchFocused = false;
        inputEl?.blur();
        localQuery = '';
        searchQuery.set('');
        router.visit(href);
    }

    let prevQuery = '';
    $effect(() => {
        const q = $searchQuery;

        if (q !== prevQuery) {
            prevQuery = q;
            selectedIndex = 0;
        }
    });

    $effect(() => {
        if ($searchOpen) {
            localQuery = '';
            searchFocused = false;
            inputEl?.blur();
        }
    });

    function handleLocalInput() {
        searchQuery.set(localQuery);
    }

    function handleLogout() {
        userMenuOpen = false;
        showLogoutConfirm = true;
    }

    function confirmLogout() {
        router.post('/logout');
    }

    function cancelLogout() {
        showLogoutConfirm = false;
    }

    function toggleTheme() {
        themeMode.update((m) => {
            if (m === 'dark') {
                return 'light';
            }

            return 'dark';
        });
    }

    onMount(() => {
        function handleKeydown(e: KeyboardEvent) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchFocused = false;
                localQuery = '';
                searchQuery.set('');
                openSearch();
            }

            if (e.key === 'Escape') {
                if (showLogoutConfirm) {
                    showLogoutConfirm = false;

                    return;
                }

                userMenuOpen = false;

                if (searchFocused) {
                    searchFocused = false;
                    localQuery = '';
                    searchQuery.set('');
                }
            }
        }
        function handleClickOutside(e: MouseEvent) {
            if (showLogoutConfirm) {
                return;
            }

            const target = e.target as HTMLElement;

            if (!target.closest('[data-user-menu]')) {
                userMenuOpen = false;
            }

            if (!target.closest('[data-inline-search]')) {
                searchFocused = false;
            }
        }
        window.addEventListener('keydown', handleKeydown);
        window.addEventListener('click', handleClickOutside);

        return () => {
            window.removeEventListener('keydown', handleKeydown);
            window.removeEventListener('click', handleClickOutside);
        };
    });
</script>

<header
    class="flex h-16 items-center border-b border-gray-200 bg-white px-4 transition-colors duration-150 dark:border-gray-800 dark:bg-gray-900"
>
    <!-- Left section (hamburger) -->
    <div class="flex items-center gap-1">
        <button
            onclick={toggleSidebar}
            class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 lg:hidden dark:hover:bg-gray-800"
            title="Open sidebar"
        >
            <Menu size={20} />
        </button>
    </div>

    <!-- Center section (inline search) -->
    <div class="flex flex-1 justify-center px-4" data-inline-search>
        <div class="relative w-full max-w-md">
            <div
                class={cn(
                    'flex items-center gap-2 rounded-lg border bg-gray-50 pl-4 pr-1.5 text-sm transition-all w-full',
                    'border-gray-200 dark:border-gray-700 dark:bg-gray-900',
                    searchFocused &&
                        'border-brand/40 bg-white ring-2 ring-brand/20 dark:border-brand dark:bg-gray-800',
                )}
            >
                <Search size={15} class="shrink-0 text-gray-400" />
                <input
                    bind:this={inputEl}
                    bind:value={localQuery}
                    oninput={handleLocalInput}
                    onfocus={() => {
                        searchFocused = true;
                    }}
                    onkeydown={handleSearchKeydown}
                    placeholder="Search everything..."
                    class="w-full border-0 bg-transparent py-1.5 text-sm outline-none placeholder:text-gray-400 dark:text-gray-100"
                />
                {#if $searchLoading}
                    <div
                        class="h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-gray-300 border-t-brand"
                    ></div>
                {/if}
                <kbd
                    class="ml-auto hidden shrink-0 items-center rounded border border-gray-200 bg-white px-1.5 py-1.5 text-[11px] font-medium leading-none text-gray-400 sm:flex dark:border-gray-600 dark:bg-gray-800"
                >
                    Ctrl+K
                </kbd>
            </div>

            {#if searchFocused && !$searchOpen && localQuery.length >= 2}
                {#if combinedResults.length > 0 || $searchLoading}
                    <div
                        class="absolute left-0 top-full z-50 mt-1 w-full overflow-hidden rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900"
                        transition:fly={{ y: -8, duration: 100 }}
                    >
                        {#if $searchLoading && combinedResults.length === 0}
                            <p class="px-4 py-3 text-sm text-gray-400">
                                Searching...
                            </p>
                        {:else}
                            {#each combinedResults as result, i (result.href)}
                                {@const Icon = getIcon(result)}
                                <a
                                    href={result.href}
                                    onclick={(e) => {
                                        e.preventDefault();
                                        selectResult(result);
                                    }}
                                    class={cn(
                                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
                                        i === selectedIndex
                                            ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                                            : 'text-gray-600 dark:text-gray-300',
                                    )}
                                >
                                    <Icon
                                        size={16}
                                        class="shrink-0 text-gray-400"
                                    />
                                    <div class="flex flex-col min-w-0">
                                        <span class="truncate"
                                            >{result.label}</span
                                        >
                                        {#if result.description}
                                            <span
                                                class="truncate text-xs text-gray-400"
                                                >{result.description}</span
                                            >
                                        {/if}
                                    </div>
                                </a>
                            {/each}
                        {/if}
                    </div>
                {:else if !$searchLoading}
                    <div
                        class="absolute left-0 top-full z-50 mt-1 w-full overflow-hidden rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900"
                        transition:fly={{ y: -8, duration: 100 }}
                    >
                        <p class="px-4 py-3 text-sm text-gray-400">
                            No results found.
                        </p>
                    </div>
                {/if}
            {/if}
        </div>
    </div>

    <!-- Right section -->
    <div class="flex items-center gap-1">
        <!-- Theme toggle -->
        <button
            onclick={toggleTheme}
            class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
            title={$themeMode === 'dark'
                ? 'Switch to light mode'
                : 'Switch to dark mode'}
        >
            {#if $themeMode === 'dark'}
                <Sun size={18} />
            {:else}
                <Moon size={18} />
            {/if}
        </button>

        <!-- Notification bell -->
        <button
            class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
            title="Notifications"
        >
            <Bell size={18} />
            <span
                class="absolute right-1.5 top-1.5 flex h-2 w-2 rounded-full bg-brand ring-2 ring-white dark:ring-gray-950"
            ></span>
        </button>

        <!-- User menu -->
        {#if page.props.auth?.user}
            <div class="relative pl-2" data-user-menu>
                <button
                    onclick={() => (userMenuOpen = !userMenuOpen)}
                    class="flex items-center rounded-lg p-1 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand to-brand/60 text-sm font-medium text-white"
                    >
                        {page.props.auth.user.name.charAt(0).toUpperCase()}
                    </div>
                </button>

                {#if userMenuOpen}
                    <div
                        class="absolute right-0 top-full z-50 mt-1 w-56 overflow-hidden rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-700 dark:bg-gray-900"
                        transition:fly={{ y: -8, duration: 150 }}
                    >
                        <div class="rounded-md px-3 py-2.5">
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-gray-100"
                            >
                                {page.props.auth.user.name}
                            </p>
                            <p
                                class="truncate text-xs text-gray-500 dark:text-gray-400"
                            >
                                {page.props.auth.user.email}
                            </p>
                        </div>

                        <Link
                            href="/profile"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <User size={16} class="text-gray-400" />
                            <span>Edit Profile</span>
                        </Link>

                        <button
                            onclick={handleLogout}
                            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <LogOut size={16} class="text-gray-400" />
                            <span>Sign out</span>
                        </button>
                    </div>
                {/if}
            </div>
        {/if}
    </div>
</header>

<!-- Logout confirmation modal -->
{#if showLogoutConfirm}
    <div
        role="presentation"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        onclick={cancelLogout}
        onkeydown={(e) => {
            if (e.key === 'Escape') {
                cancelLogout();
            }
        }}
        transition:fade={{ duration: 150 }}
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label="Confirm sign out"
            tabindex="-1"
            class="w-80 rounded-xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
            transition:fly={{ y: -10, duration: 150 }}
        >
            <div class="flex flex-col items-center text-center">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-950"
                >
                    <LogOut size={28} class="text-red-600 dark:text-red-400" />
                </div>
                <h3
                    class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                    Sign out
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to sign out?
                </p>
            </div>
            <div class="mt-6 flex items-center justify-center gap-3">
                <button
                    onclick={cancelLogout}
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Cancel
                </button>
                <button
                    onclick={confirmLogout}
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700"
                >
                    Sign out
                </button>
            </div>
        </div>
    </div>
{/if}
