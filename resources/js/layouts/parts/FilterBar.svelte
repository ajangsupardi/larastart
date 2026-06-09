<script lang="ts">
    import { SlidersHorizontal } from '@lucide/svelte';
    import { cn } from '@/lib/utils';

    let { children } = $props();
</script>

{#if children}
    <div
        class={cn(
            'mb-6 flex flex-wrap items-center gap-3 rounded-xl border border-gray-200 bg-white p-4',
            'dark:border-gray-800 dark:bg-gray-900',
        )}
    >
        <SlidersHorizontal size={16} class="text-gray-400" />
        {@render children?.()}
    </div>
{/if}
