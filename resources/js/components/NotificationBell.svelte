<script lang="ts">
    import { Bell } from '@lucide/svelte';
    import { fly } from 'svelte/transition';

    let isOpen = $state(false);
    let unreadCount = $state(0);
    let activities = $state<Activity[]>([]);
    let dropdownEl: HTMLDivElement | undefined = $state();
    let intervalId: ReturnType<typeof setInterval> | undefined = $state();

    interface ActivityUser {
        id: number;
        name: string;
    }

    interface Activity {
        id: number;
        user: ActivityUser;
        action: string;
        auditable_type: string;
        auditable_id: number;
        created_at: string;
    }

    function getActionLabel(action: string): string {
        switch (action) {
            case 'created':
                return 'Created';
            case 'updated':
                return 'Updated';
            case 'deleted':
                return 'Deleted';
            default:
                return action;
        }
    }

    function getActionColor(action: string): string {
        switch (action) {
            case 'created':
                return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            case 'updated':
                return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
            case 'deleted':
                return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            default:
                return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
        }
    }

    function formatTimeAgo(dateStr: string): string {
        const date = new Date(dateStr);
        const now = new Date();
        const diffMs = now.getTime() - date.getTime();
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHr = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHr / 24);

        if (diffSec < 60) {
            return 'just now';
        }

        if (diffMin < 60) {
            return `${diffMin}m ago`;
        }

        if (diffHr < 24) {
            return `${diffHr}h ago`;
        }

        return `${diffDay}d ago`;
    }

    async function fetchUnreadCount() {
        try {
            const response = await fetch('/notifications/unread');
            const data = await response.json();
            unreadCount = data.count;
        } catch {
            // silently fail
        }
    }

    async function fetchActivities() {
        try {
            const response = await fetch('/notifications');
            activities = await response.json();
        } catch {
            // silently fail
        }
    }

    async function markAsRead() {
        try {
            await fetch('/notifications/mark-read', { method: 'POST' });
            unreadCount = 0;
        } catch {
            // silently fail
        }
    }

    function toggle() {
        isOpen = !isOpen;

        if (isOpen) {
            fetchActivities();
            markAsRead();
        }
    }

    function handleClickOutside(event: MouseEvent) {
        if (dropdownEl && !dropdownEl.contains(event.target as Node)) {
            isOpen = false;
        }
    }

    $effect(() => {
        fetchUnreadCount();
        intervalId = setInterval(fetchUnreadCount, 30000);

        document.addEventListener('click', handleClickOutside);

        return () => {
            if (intervalId) {
                clearInterval(intervalId);
            }

            document.removeEventListener('click', handleClickOutside);
        };
    });
</script>

<div class="relative" bind:this={dropdownEl}>
    <button
        onclick={toggle}
        class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
        title="Notifications"
    >
        <Bell size={18} />
        {#if unreadCount > 0}
            <span
                class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1 text-[10px] font-medium text-white ring-2 ring-white dark:ring-gray-950"
            >
                {unreadCount > 99 ? '99+' : unreadCount}
            </span>
        {/if}
    </button>

    {#if isOpen}
        <div
            class="absolute right-0 top-full z-50 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
            transition:fly={{ y: -8, duration: 150 }}
            onclick={(e) => e.stopPropagation()}
        >
            <div
                class="border-b border-gray-100 px-4 py-3 dark:border-gray-800"
            >
                <h3
                    class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                >
                    Recent Activity
                </h3>
            </div>

            <div class="max-h-96 overflow-y-auto">
                {#if activities.length === 0}
                    <div class="px-4 py-8 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No recent activity
                        </p>
                    </div>
                {:else}
                    {#each activities as activity (activity.id)}
                        <div
                            class="border-b border-gray-50 px-4 py-3 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-sm text-gray-700 dark:text-gray-300"
                                    >
                                        <span
                                            class="font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {activity.user?.name ?? 'System'}
                                        </span>
                                        {getActionLabel(
                                            activity.action,
                                        ).toLowerCase()}
                                        <span
                                            class="font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {activity.auditable_type}
                                        </span>
                                        #{activity.auditable_id}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {getActionColor(
                                                activity.action,
                                            )}"
                                        >
                                            {getActionLabel(activity.action)}
                                        </span>
                                        <span
                                            class="text-xs text-gray-400 dark:text-gray-500"
                                        >
                                            {formatTimeAgo(activity.created_at)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/each}
                {/if}
            </div>
        </div>
    {/if}
</div>
