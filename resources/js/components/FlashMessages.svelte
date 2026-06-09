<script lang="ts">
    import { usePage } from '@inertiajs/svelte';
    import { X } from '@lucide/svelte';
    import { fly } from 'svelte/transition';

    const page = usePage();

    // eslint-disable-next-line svelte/prefer-writable-derived
    let success = $state(page.props.flash?.success ?? '');
    // eslint-disable-next-line svelte/prefer-writable-derived
    let error = $state(page.props.flash?.error ?? '');

    $effect(() => {
        success = page.props.flash?.success ?? '';
    });

    $effect(() => {
        error = page.props.flash?.error ?? '';
    });

    $effect(() => {
        if (success) {
            const t = setTimeout(() => {
                success = '';
            }, 4000);

            return () => clearTimeout(t);
        }
    });

    $effect(() => {
        if (error) {
            const t = setTimeout(() => {
                error = '';
            }, 4000);

            return () => clearTimeout(t);
        }
    });

    function dismissSuccess() {
        success = '';
    }
    function dismissError() {
        error = '';
    }
</script>

{#if success}
    <div
        role="alert"
        class="fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-lg dark:border-green-800 dark:bg-green-950 dark:text-green-200"
        transition:fly={{ x: 20, duration: 300 }}
    >
        <span
            class="flex h-5 w-5 items-center justify-center rounded-full bg-green-200 text-green-700 dark:bg-green-700 dark:text-green-200"
            >&#10003;</span
        >
        <span class="flex-1">{success}</span>
        <button
            onclick={dismissSuccess}
            class="rounded p-0.5 transition-colors hover:bg-green-100 dark:hover:bg-green-900"
        >
            <X size={14} />
        </button>
    </div>
{/if}

{#if error}
    <div
        role="alert"
        class="fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-lg dark:border-red-800 dark:bg-red-950 dark:text-red-200"
        transition:fly={{ x: 20, duration: 300 }}
    >
        <span
            class="flex h-5 w-5 items-center justify-center rounded-full bg-red-200 text-red-700 dark:bg-red-700 dark:text-red-200"
            >&#10005;</span
        >
        <span class="flex-1">{error}</span>
        <button
            onclick={dismissError}
            class="rounded p-0.5 transition-colors hover:bg-red-100 dark:hover:bg-red-900"
        >
            <X size={14} />
        </button>
    </div>
{/if}
