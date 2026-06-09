<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { cn } from '@/lib/utils';

    type Breadcrumb = { label: string; href?: string };
    type Action = {
        label: string;
        href?: string;
        onclick?: () => void;
        variant?: 'primary' | 'secondary';
    };

    let {
        title = '',
        description = '',
        breadcrumbs = [] as Breadcrumb[],
        actions = [] as Action[],
    } = $props();
</script>

<div class="mb-6">
    {#if breadcrumbs.length > 0}
        <nav aria-label="Breadcrumb" class="mb-2">
            <ol
                class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
            >
                {#each breadcrumbs as crumb, i (i)}
                    <li class="flex items-center gap-2">
                        {#if i > 0}
                            <span class="text-gray-300 dark:text-gray-600"
                                >/</span
                            >
                        {/if}
                        {#if crumb.href}
                            <Link
                                href={crumb.href}
                                class="transition-colors hover:text-gray-700 dark:hover:text-gray-300"
                            >
                                {crumb.label}
                            </Link>
                        {:else}
                            <span class="text-gray-900 dark:text-gray-100"
                                >{crumb.label}</span
                            >
                        {/if}
                    </li>
                {/each}
            </ol>
        </nav>
    {/if}

    <div class="flex items-start justify-between gap-4">
        <div>
            {#if title}
                <h1
                    class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
                >
                    {title}
                </h1>
            {/if}
            {#if description}
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {description}
                </p>
            {/if}
        </div>

        {#if actions.length > 0}
            <div class="flex shrink-0 items-center gap-2">
                {#each actions as action (action.label)}
                    {#if action.href}
                        <Link
                            href={action.href}
                            class={cn(
                                'rounded-lg px-4 py-2 text-sm font-medium transition-colors active:scale-[0.98]',
                                action.variant === 'secondary'
                                    ? 'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800'
                                    : 'bg-brand text-white hover:bg-brand/90',
                            )}
                        >
                            {action.label}
                        </Link>
                    {:else}
                        <button
                            onclick={action.onclick}
                            class={cn(
                                'rounded-lg px-4 py-2 text-sm font-medium transition-colors active:scale-[0.98]',
                                action.variant === 'secondary'
                                    ? 'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800'
                                    : 'bg-brand text-white hover:bg-brand/90',
                            )}
                        >
                            {action.label}
                        </button>
                    {/if}
                {/each}
            </div>
        {/if}
    </div>
</div>
