<script lang="ts">
    import { router, usePage } from '@inertiajs/svelte';
    import {
        Search,
        Trash2,
        RotateCcw,
        ShieldCheck,
        AlertTriangle,
        Users as UsersIcon,
    } from '@lucide/svelte';
    import DeleteConfirmModal from '@/components/DeleteConfirmModal.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type Role = {
        id: number;
        name: string;
        slug: string;
        description: string | null;
        permissions: Record<string, string[]>;
        is_system: boolean;
        users_count: number;
        deleted_at: string;
        created_at: string;
    };

    let {
        roles = { data: [] as Role[], meta: {} as Record<string, any> },
        filters = {} as { search?: string },
    } = $props();

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const canUpdate = $derived(permissions.roles?.includes('update') ?? false);
    const canDelete = $derived(permissions.roles?.includes('delete') ?? false);

    // svelte-ignore state_referenced_locally
    let search = $state.raw(filters.search ?? '');
    let deleteTarget = $state<Role | null>(null);
    let deleting = $state(false);

    let searchTimeout: ReturnType<typeof setTimeout>;

    function onSearchInput(e: Event) {
        const value = (e.target as HTMLInputElement).value;
        search = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/trash/roles',
                { search: value || undefined },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        }, 300);
    }

    function restoreRole(role: Role) {
        router.post(`/trash/roles/${role.id}/restore`);
    }

    function confirmForceDelete(role: Role) {
        deleteTarget = role;
    }

    function executeForceDelete() {
        if (!deleteTarget) {
            return;
        }

        deleting = true;
        router.delete(`/trash/roles/${deleteTarget.id}`, {
            onFinish: () => {
                deleting = false;
                deleteTarget = null;
            },
        });
    }

    function formatDate(date: string) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }
</script>

<DashboardLayout
    title="Trash - Roles"
    description="View and manage soft-deleted roles."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Trash', href: '/trash/roles' },
        { label: 'Roles' },
    ]}
>
    <!-- Search bar -->
    <div class="mb-6">
        <div
            class="relative flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 pl-4 pr-1.5 text-sm transition-all dark:border-gray-700 dark:bg-gray-900"
        >
            <Search size={15} class="shrink-0 text-gray-400" />
            <input
                type="text"
                placeholder="Search roles..."
                value={search}
                oninput={onSearchInput}
                class="w-full border-0 bg-transparent py-1.5 text-sm outline-none placeholder:text-gray-400 dark:text-gray-100"
            />
        </div>
    </div>

    <!-- Deleted roles table -->
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
                            >Role</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Slug</th
                        >
                        <th
                            class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >Users</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Deleted At</th
                        >
                        <th
                            class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >Actions</th
                        >
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    {#each roles.data as role (role.id)}
                        <tr
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gray-400 to-gray-500 text-xs font-bold text-white shadow-sm"
                                    >
                                        <ShieldCheck size={18} />
                                    </div>
                                    <div>
                                        <span
                                            class="font-medium text-gray-900 dark:text-gray-100"
                                            >{role.name}</span
                                        >
                                        {#if role.description}
                                            <p
                                                class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {role.description}
                                            </p>
                                        {/if}
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    {role.slug}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400"
                                >
                                    <UsersIcon size={14} />
                                    {role.users_count}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                            >
                                {formatDate(role.deleted_at)}
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    {#if canUpdate}
                                        <button
                                            onclick={() => restoreRole(role)}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-green-600 transition-colors hover:bg-green-50 hover:text-green-700 dark:text-green-400 dark:hover:bg-green-950/50 dark:hover:text-green-300"
                                            title="Restore role"
                                        >
                                            <RotateCcw size={14} />
                                            Restore
                                        </button>
                                    {/if}
                                    {#if canDelete}
                                        <button
                                            onclick={() =>
                                                confirmForceDelete(role)}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/50 dark:hover:text-red-300"
                                            title="Permanently delete role"
                                        >
                                            <Trash2 size={14} />
                                            Delete
                                        </button>
                                    {/if}
                                    {#if !canUpdate && !canDelete}
                                        <span
                                            class="text-xs text-gray-400 italic"
                                            >No actions available</span
                                        >
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <AlertTriangle
                                        size={40}
                                        class="text-gray-300 dark:text-gray-600"
                                    />
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {#if filters.search}
                                                No results found
                                            {:else}
                                                No deleted roles
                                            {/if}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {#if filters.search}
                                                No deleted roles matching "<strong
                                                    >{filters.search}</strong
                                                >"
                                            {:else}
                                                There are no soft-deleted roles
                                                to recover.
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {#if roles.meta && roles.meta.last_page > 1}
            <div
                class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 sm:flex-row dark:border-gray-800"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span
                        class="font-medium text-gray-700 dark:text-gray-300"
                        >{roles.meta.from}</span
                    >
                    to
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{roles.meta.to}</span
                    >
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{roles.meta.total}</span
                    > entries
                </p>
                <div class="flex items-center gap-1.5">
                    {#each roles.meta.links as link (link.label)}
                        {#if link.url}
                            <a
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
                            </a>
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

<DeleteConfirmModal
    open={deleteTarget !== null}
    title="Permanently Delete Role"
    message="This will permanently remove this role and all associated data. This action cannot be undone."
    itemName={deleteTarget?.name ?? ''}
    processing={deleting}
    onclose={() => {
        deleteTarget = null;
    }}
    onconfirm={executeForceDelete}
/>
