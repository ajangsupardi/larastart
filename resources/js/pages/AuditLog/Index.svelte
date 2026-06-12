<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { Search, Activity } from '@lucide/svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type AuditUser = {
        id: number;
        name: string;
    } | null;

    type AuditLog = {
        id: number;
        user: AuditUser;
        auditable_type: string;
        auditable_id: number;
        action: string;
        old_values: Record<string, unknown> | null;
        new_values: Record<string, unknown> | null;
        ip_address: string | null;
        user_agent: string | null;
        created_at: string;
    };

    let {
        auditLogs = {
            data: [] as AuditLog[],
            meta: {} as Record<string, any>,
        },
        filters = {} as { search?: string },
    } = $props();

    // svelte-ignore state_referenced_locally
    let search = $state.raw(filters.search ?? '');

    let searchTimeout: ReturnType<typeof setTimeout>;

    function onSearchInput(e: Event) {
        const value = (e.target as HTMLInputElement).value;
        search = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/audit-log',
                { search: value || undefined },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        }, 300);
    }

    function formatDate(date: string) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }

    function actionBadge(action: string) {
        switch (action) {
            case 'created':
                return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            case 'updated':
                return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
            case 'deleted':
                return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            default:
                return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
        }
    }

    function getDetails(log: AuditLog): {
        text: string;
        changes?: { field: string; old: unknown; new: unknown }[];
    } {
        const model = log.auditable_type;

        if (log.action === 'created' && log.new_values) {
            const name =
                log.new_values.name ??
                log.new_values.title ??
                Object.values(log.new_values)[0];

            return { text: `Created ${model} "${name}"` };
        }

        if (log.action === 'updated' && log.old_values && log.new_values) {
            const changes = Object.keys(log.new_values)
                .filter((k) => k !== 'updated_at')
                .map((k) => ({
                    field: k,
                    old: log.old_values![k],
                    new: log.new_values![k],
                }))
                .slice(0, 3);

            if (changes.length === 0) {
                return { text: `Updated ${model}` };
            }

            return {
                text: `Updated ${model}`,
                changes,
            };
        }

        if (log.action === 'deleted' && log.old_values) {
            const name =
                log.old_values.name ??
                log.old_values.title ??
                Object.values(log.old_values)[0];

            return { text: `Deleted ${model} "${name}"` };
        }

        return { text: `${log.action} ${model}` };
    }
</script>

<DashboardLayout
    title="Audit Log"
    description="Track all create, update, and delete operations."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Audit Log' },
    ]}
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <div
                class="relative flex items-center gap-2 rounded-lg border border-gray-200 bg-white pl-4 pr-3 py-2 text-sm transition-all dark:border-gray-600 dark:bg-gray-800"
            >
                <Search size={15} class="shrink-0 text-gray-400" />
                <input
                    type="text"
                    placeholder="Search by user or model..."
                    value={search}
                    oninput={onSearchInput}
                    class="w-full border-0 bg-transparent py-0.5 text-sm outline-none placeholder:text-gray-400 dark:text-white"
                />
            </div>
        </div>
    </div>

    <div
        class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr
                        class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950"
                    >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Date</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >User</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Action</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Model</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Details</th
                        >
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    {#each auditLogs.data as log (log.id)}
                        <tr
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{formatDate(log.created_at)}</td
                            >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 text-xs font-bold text-white shadow-sm"
                                    >
                                        {getInitials(log.user?.name ?? 'S')}
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                        >{log.user?.name ?? 'System'}</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {actionBadge(
                                        log.action,
                                    )}"
                                >
                                    {log.action}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="font-medium text-gray-900 dark:text-gray-100"
                                    >{log.auditable_type}</span
                                >
                                <span
                                    class="ml-1 text-gray-400 dark:text-gray-500"
                                    >#{log.auditable_id}</span
                                >
                            </td>
                            <td
                                class="max-w-xs px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                            >
                                <span class="block">{getDetails(log).text}</span
                                >
                                {#if getDetails(log).changes}
                                    <div class="mt-1 space-y-0.5">
                                        {#each getDetails(log).changes as change (change.field)}
                                            <div
                                                class="flex items-center gap-1 text-xs"
                                            >
                                                <span
                                                    class="font-medium text-gray-600 dark:text-gray-300"
                                                    >{change.field}:</span
                                                >
                                                <span
                                                    class="text-red-500 line-through dark:text-red-400"
                                                    >{change.old}</span
                                                >
                                                <span class="text-gray-400"
                                                    >→</span
                                                >
                                                <span
                                                    class="text-green-600 dark:text-green-400"
                                                    >{change.new}</span
                                                >
                                            </div>
                                        {/each}
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <Activity
                                        size={40}
                                        class="text-gray-300 dark:text-gray-600"
                                    />
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {#if filters.search}
                                            No results found
                                        {:else}
                                            No audit logs yet
                                        {/if}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        {#if auditLogs.meta && auditLogs.meta.last_page > 1}
            <div
                class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 sm:flex-row dark:border-gray-800"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span
                        class="font-medium text-gray-700 dark:text-gray-300"
                        >{auditLogs.meta.from}</span
                    >
                    to
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{auditLogs.meta.to}</span
                    >
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{auditLogs.meta.total}</span
                    > entries
                </p>
                <div class="flex items-center gap-1.5">
                    {#each auditLogs.meta.links as link (link.label)}
                        {#if link.url}
                            <Link
                                href={link.url}
                                class={cn(
                                    'rounded-lg px-3 py-1.5 text-sm font-medium transition-all',
                                    link.active
                                        ? 'bg-brand text-white shadow-sm'
                                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800',
                                )}
                            >
                                <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                                {@html link.label}
                            </Link>
                        {:else}
                            <span
                                class="rounded-lg px-3 py-1.5 text-sm text-gray-400 dark:text-gray-600"
                            >
                                <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                                {@html link.label}
                            </span>
                        {/if}
                    {/each}
                </div>
            </div>
        {/if}
    </div>
</DashboardLayout>
