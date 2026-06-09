<script lang="ts">
    import { X, AlertTriangle } from '@lucide/svelte';
    import { fade, fly } from 'svelte/transition';
    import { cn } from '@/lib/utils';

    type DeleteConfirmModalProps = {
        open: boolean;
        title?: string;
        message: string;
        itemName: string;
        processing?: boolean;
        onclose: () => void;
        onconfirm: () => void;
    };

    let {
        open = false,
        title = 'Confirm Deletion',
        message = '',
        itemName = '',
        processing = false,
        onclose,
        onconfirm,
    }: DeleteConfirmModalProps = $props();
</script>

{#if open}
    <div
        role="presentation"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        onclick={onclose}
        onkeydown={(e) => {
            if (e.key === 'Escape') {
                onclose();
            }
        }}
        transition:fade={{ duration: 150 }}
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label={title}
            tabindex="-1"
            class="w-80 overflow-hidden rounded-xl bg-white shadow-xl dark:bg-gray-900"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
            transition:fly={{ y: 20, duration: 200 }}
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-950"
                    >
                        <AlertTriangle
                            size={20}
                            class="text-red-600 dark:text-red-400"
                        />
                    </div>
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                    >
                        {title}
                    </h3>
                </div>
                <button
                    onclick={onclose}
                    class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <X size={18} />
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                {#if message}
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {message}
                    </p>
                {/if}
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <span
                        class="font-semibold text-gray-900 dark:text-gray-100"
                        >"{itemName}"</span
                    >? This action cannot be undone.
                </p>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4 dark:border-gray-800"
            >
                <button
                    onclick={onclose}
                    disabled={processing}
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Cancel
                </button>
                <button
                    onclick={onconfirm}
                    disabled={processing}
                    class={cn(
                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-all',
                        processing
                            ? 'cursor-not-allowed bg-red-400'
                            : 'bg-red-600 hover:bg-red-700 active:scale-[0.98]',
                    )}
                >
                    {#if processing}
                        <svg
                            class="h-4 w-4 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>
                        Deleting...
                    {:else}
                        Delete
                    {/if}
                </button>
            </div>
        </div>
    </div>
{/if}
