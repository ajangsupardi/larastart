<script lang="ts">
    import { usePage, router } from '@inertiajs/svelte';
    import {
        Search,
        User,
        Users,
        ShieldCheck,
        LayoutDashboard,
        FileText,
    } from '@lucide/svelte';
    import type { Component } from 'svelte';
    import { fly, fade } from 'svelte/transition';
    import { cn } from '@/lib/utils';
    import {
        closeSearch,
        searchOpen,
        searchQuery,
        searchResults,
        searchLoading,
        getFilteredStaticItems,
    } from '@/stores/search';

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const staticItems = $derived(getFilteredStaticItems(permissions));
    let combinedResults = $derived.by(() => {
        const filtered =
            $searchQuery.length >= 2
                ? staticItems.filter((item) =>
                      item.label
                          .toLowerCase()
                          .includes($searchQuery.toLowerCase()),
                  )
                : [];

        return [...filtered, ...$searchResults];
    });

    let inputEl: HTMLInputElement | undefined = $state();
    let selectedIndex = $state(0);

    $effect(() => {
        if ($searchOpen) {
            requestAnimationFrame(() => inputEl?.focus());
            selectedIndex = 0;
        }
    });

    let prevQuery = '';
    $effect(() => {
        const q = $searchQuery;

        if (q !== prevQuery) {
            prevQuery = q;
            selectedIndex = 0;
        }
    });

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

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === 'Escape') {
            e.preventDefault();
            closeSearch();

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
                closeSearch();
                setTimeout(() => router.visit(href), 0);
            }
        }
    }

    function handleOverlayClick() {
        closeSearch();
    }
</script>

{#if $searchOpen}
    <div
        role="dialog"
        aria-modal="true"
        aria-label="Search"
        tabindex="-1"
        class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 pt-[12vh] backdrop-blur-sm"
        onclick={handleOverlayClick}
        onkeydown={handleKeydown}
        transition:fade={{ duration: 150 }}
    >
        <div
            role="dialog"
            aria-modal="true"
            tabindex="-1"
            class="w-full max-w-lg overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
            transition:fly={{ y: -20, duration: 200 }}
        >
            <div
                class="flex items-center border-b border-gray-200 px-4 dark:border-gray-700"
            >
                <Search size={18} class="shrink-0 text-gray-400" />
                <input
                    bind:this={inputEl}
                    bind:value={$searchQuery}
                    onkeydown={handleKeydown}
                    placeholder="Search everything..."
                    class="w-full border-0 bg-transparent px-3 py-4 text-sm outline-none placeholder:text-gray-400 dark:text-gray-100"
                />
                {#if $searchLoading}
                    <div
                        class="h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-gray-300 border-t-brand"
                    ></div>
                {/if}
                <kbd
                    class="shrink-0 rounded border border-gray-200 bg-white px-1.5 py-1.5 text-[11px] font-medium leading-none text-gray-400 dark:border-gray-600 dark:bg-gray-800"
                >
                    ESC
                </kbd>
            </div>

            <div class="max-h-80 overflow-y-auto p-2">
                {#if $searchQuery.length < 2 && combinedResults.length > 0}
                    <p
                        class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400"
                    >
                        Pages
                    </p>
                    {#each combinedResults as item, i (item.href)}
                        {@const Icon = getIcon(item)}
                        <a
                            href={item.href}
                            onclick={(e) => {
                                e.preventDefault();
                                const href = item.href;
                                closeSearch();
                                setTimeout(() => router.visit(href), 0);
                            }}
                            class={cn(
                                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
                                i === selectedIndex
                                    ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                                    : 'text-gray-600 dark:text-gray-300',
                            )}
                        >
                            <Icon size={16} class="shrink-0 text-gray-400" />
                            <span class="truncate">{item.label}</span>
                        </a>
                    {/each}
                {:else if combinedResults.length > 0}
                    {#each combinedResults as result, i (result.href)}
                        {@const Icon = getIcon(result)}
                        <a
                            href={result.href}
                            onclick={(e) => {
                                e.preventDefault();
                                const href = result.href;
                                closeSearch();
                                setTimeout(() => router.visit(href), 0);
                            }}
                            class={cn(
                                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
                                i === selectedIndex
                                    ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100'
                                    : 'text-gray-600 dark:text-gray-300',
                            )}
                        >
                            <Icon size={16} class="shrink-0 text-gray-400" />
                            <div class="flex flex-col min-w-0">
                                <span class="truncate">{result.label}</span>
                                {#if result.description}
                                    <span class="truncate text-xs text-gray-400"
                                        >{result.description}</span
                                    >
                                {/if}
                            </div>
                        </a>
                    {/each}
                {:else if $searchQuery.length < 2}
                    <p class="p-6 text-center text-sm text-gray-400">
                        Start typing to search...
                    </p>
                {:else if $searchLoading}
                    <p class="p-4 text-center text-sm text-gray-400">
                        Searching...
                    </p>
                {:else}
                    <p class="p-4 text-center text-sm text-gray-500">
                        No results found.
                    </p>
                {/if}
            </div>
        </div>
    </div>
{/if}
