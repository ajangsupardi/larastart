<script lang="ts">
    import { router, usePage } from '@inertiajs/svelte';
    import { Search, Trash2, RotateCcw, AlertTriangle } from '@lucide/svelte';
    import DeleteConfirmModal from '@/components/DeleteConfirmModal.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type User = {
        id: number;
        name: string;
        email: string;
        avatar: string | null;
        avatar_url: string | null;
        email_verified_at: string | null;
        is_system: boolean;
        roles: { id: number; name: string }[];
        deleted_at: string;
        created_at: string;
    };

    let {
        users = { data: [] as User[], meta: {} as Record<string, any> },
        filters = {} as { search?: string },
    } = $props();

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const canUpdate = $derived(permissions.users?.includes('update') ?? false);
    const canDelete = $derived(permissions.users?.includes('delete') ?? false);

    // svelte-ignore state_referenced_locally
    let search = $state.raw(filters.search ?? '');
    let deleteTarget = $state<User | null>(null);
    let deleting = $state(false);

    let searchTimeout: ReturnType<typeof setTimeout>;

    function onSearchInput(e: Event) {
        const value = (e.target as HTMLInputElement).value;
        search = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/trash/users',
                { search: value || undefined },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        }, 300);
    }

    function restoreUser(user: User) {
        router.post(`/trash/users/${user.id}/restore`);
    }

    function confirmForceDelete(user: User) {
        deleteTarget = user;
    }

    function executeForceDelete() {
        if (!deleteTarget) {
            return;
        }

        deleting = true;
        router.delete(`/trash/users/${deleteTarget.id}`, {
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

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }
</script>

<DashboardLayout
    title="Trash - Users"
    description="View and manage soft-deleted users."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Trash', href: '/trash/users' },
        { label: 'Users' },
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
                placeholder="Search by name or email..."
                value={search}
                oninput={onSearchInput}
                class="w-full border-0 bg-transparent py-1.5 text-sm outline-none placeholder:text-gray-400 dark:text-gray-100"
            />
        </div>
    </div>

    <!-- Deleted users table -->
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
                            >User</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Email</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Roles</th
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
                    {#each users.data as user (user.id)}
                        <tr
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {#if user.avatar_url}
                                        <img
                                            src={user.avatar_url}
                                            alt={user.name}
                                            class="h-9 w-9 shrink-0 rounded-full object-cover shadow-sm opacity-60"
                                        />
                                    {:else}
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gray-400 to-gray-500 text-xs font-bold text-white shadow-sm"
                                        >
                                            {getInitials(user.name)}
                                        </div>
                                    {/if}
                                    <span
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                        >{user.name}</span
                                    >
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-gray-600 dark:text-gray-400"
                                >{user.email}</td
                            >
                            <td class="px-6 py-4">
                                {#if user.roles && user.roles.length > 0}
                                    <div class="flex flex-wrap gap-1">
                                        {#each user.roles as role (role.id)}
                                            <span
                                                class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-950 dark:text-indigo-400 dark:ring-indigo-400/20"
                                            >
                                                {role.name}
                                            </span>
                                        {/each}
                                    </div>
                                {:else}
                                    <span class="text-xs text-gray-400 italic"
                                        >No roles</span
                                    >
                                {/if}
                            </td>
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                            >
                                {formatDate(user.deleted_at)}
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    {#if canUpdate}
                                        <button
                                            onclick={() => restoreUser(user)}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-green-600 transition-colors hover:bg-green-50 hover:text-green-700 dark:text-green-400 dark:hover:bg-green-950/50 dark:hover:text-green-300"
                                            title="Restore user"
                                        >
                                            <RotateCcw size={14} />
                                            Restore
                                        </button>
                                    {/if}
                                    {#if canDelete}
                                        <button
                                            onclick={() =>
                                                confirmForceDelete(user)}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/50 dark:hover:text-red-300"
                                            title="Permanently delete user"
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
                                                No deleted users
                                            {/if}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {#if filters.search}
                                                No deleted users matching "<strong
                                                    >{filters.search}</strong
                                                >"
                                            {:else}
                                                There are no soft-deleted users
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
        {#if users.meta && users.meta.last_page > 1}
            <div
                class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 sm:flex-row dark:border-gray-800"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span
                        class="font-medium text-gray-700 dark:text-gray-300"
                        >{users.meta.from}</span
                    >
                    to
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{users.meta.to}</span
                    >
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{users.meta.total}</span
                    > entries
                </p>
                <div class="flex items-center gap-1.5">
                    {#each users.meta.links as link (link.label)}
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
    title="Permanently Delete User"
    message="This will permanently remove this user and all associated data. This action cannot be undone."
    itemName={deleteTarget?.name ?? ''}
    processing={deleting}
    onclose={() => {
        deleteTarget = null;
    }}
    onconfirm={executeForceDelete}
/>
