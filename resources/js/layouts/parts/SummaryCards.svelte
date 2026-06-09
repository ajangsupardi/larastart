<script lang="ts">
    import { ArrowUp, ArrowDown, Minus } from '@lucide/svelte';
    import { cn } from '@/lib/utils';

    type Card = {
        label: string;
        value: string | number;
        trend?: number;
        icon?: string;
    };

    let { cards = [] as Card[] } = $props();

    const accentBgs = [
        'bg-brand',
        'bg-emerald-500',
        'bg-amber-500',
        'bg-violet-500',
    ];

    const accentTexts = [
        'text-brand dark:text-brand',
        'text-emerald-600 dark:text-emerald-400',
        'text-amber-600 dark:text-amber-400',
        'text-violet-600 dark:text-violet-400',
    ];
</script>

{#if cards.length > 0}
    <div class="mb-6 flex flex-wrap gap-4">
        {#each cards as card, i (card.label)}
            {@const idx = i % accentBgs.length}
            <div
                class={cn(
                    'flex flex-1 basis-48 flex-col rounded-b-xl border border-gray-200 bg-white shadow-sm transition-colors duration-150 hover:shadow-md dark:border-gray-800 dark:bg-gray-900',
                    'overflow-hidden',
                )}
            >
                <div class={cn('h-1', accentBgs[idx])}></div>
                <div class="flex w-full flex-col p-5">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                            >{card.label}</span
                        >
                        {#if card.icon}
                            <span class="text-xl">{card.icon}</span>
                        {/if}
                    </div>
                    <p
                        class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
                    >
                        {card.value}
                    </p>
                    {#if card.trend !== undefined}
                        <div class="mt-2 flex items-center gap-1">
                            {#if card.trend > 0}
                                <ArrowUp
                                    size={14}
                                    class={cn('shrink-0', accentTexts[idx])}
                                />
                                <span
                                    class={cn(
                                        'text-sm font-medium',
                                        accentTexts[idx],
                                    )}
                                >
                                    +{card.trend}%
                                </span>
                            {:else if card.trend < 0}
                                <ArrowDown
                                    size={14}
                                    class={cn('shrink-0', accentTexts[idx])}
                                />
                                <span
                                    class={cn(
                                        'text-sm font-medium',
                                        accentTexts[idx],
                                    )}
                                >
                                    {card.trend}%
                                </span>
                            {:else}
                                <Minus
                                    size={14}
                                    class={cn('shrink-0', accentTexts[idx])}
                                />
                                <span
                                    class={cn(
                                        'text-sm font-medium',
                                        accentTexts[idx],
                                    )}>0%</span
                                >
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
        {/each}
    </div>
{/if}
