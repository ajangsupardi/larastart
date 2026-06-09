<script lang="ts">
    import { X, Sun, Moon, Monitor } from '@lucide/svelte';
    import { slide, fade } from 'svelte/transition';
    import { cn } from '@/lib/utils';
    import { themeMode } from '@/stores/theme';

    type ThemeMode = 'light' | 'dark' | 'system';

    let customizerOpen = $state(false);

    const modes: { value: ThemeMode; label: string; icon: typeof Sun }[] = [
        { value: 'light', label: 'Light', icon: Sun },
        { value: 'dark', label: 'Dark', icon: Moon },
        { value: 'system', label: 'System', icon: Monitor },
    ];

    function toggle() {
        customizerOpen = !customizerOpen;
    }

    function setTheme(mode: ThemeMode) {
        themeMode.set(mode);
    }
</script>

<button
    onclick={toggle}
    class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
    title="Theme settings"
>
    {#if $themeMode === 'dark'}
        <Moon size={18} />
    {:else}
        <Sun size={18} />
    {/if}
</button>

{#if customizerOpen}
    <div
        role="dialog"
        aria-modal="true"
        aria-label="Theme settings"
        tabindex="-1"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        onclick={toggle}
        onkeydown={(e) => {
            if (e.key === 'Escape') {
                toggle();
            }
        }}
        transition:fade={{ duration: 200 }}
    >
        <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
        <div
            role="document"
            class="fixed right-0 top-0 z-50 h-full w-80 border-l border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-950"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
            transition:slide={{ duration: 200 }}
        >
            <div class="mb-6 flex items-center justify-between">
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                    Theme
                </h3>
                <button
                    onclick={toggle}
                    class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <X size={18} />
                </button>
            </div>

            <div class="space-y-2">
                <p
                    class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                >
                    Appearance
                </p>
                {#each modes as mode (mode.value)}
                    <button
                        onclick={() => setTheme(mode.value)}
                        class={cn(
                            'flex w-full items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-all',
                            $themeMode === mode.value
                                ? 'border-brand bg-brand/10 dark:border-brand dark:bg-brand/10'
                                : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800',
                        )}
                    >
                        <mode.icon
                            size={18}
                            class={cn(
                                $themeMode === mode.value
                                    ? 'text-brand dark:text-brand'
                                    : 'text-gray-400',
                            )}
                        />
                        <span
                            class={cn(
                                'flex-1 text-left font-medium',
                                $themeMode === mode.value
                                    ? 'text-brand dark:text-brand'
                                    : 'text-gray-700 dark:text-gray-300',
                            )}
                        >
                            {mode.label}
                        </span>
                        {#if $themeMode === mode.value}
                            <span class="text-brand dark:text-brand"
                                >&#10003;</span
                            >
                        {/if}
                    </button>
                {/each}
            </div>
        </div>
    </div>
{/if}
