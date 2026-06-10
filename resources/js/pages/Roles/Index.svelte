<script lang="ts">
    import { Link, router, usePage } from '@inertiajs/svelte';
    import {
        Search,
        ShieldCheck,
        Pencil,
        Trash2,
        Users as UsersIcon,
        Lock,
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
        created_at: string;
    };

    let {
        roles = { data: [] as Role[], meta: {} as Record<string, any> },
        filters = {} as { search?: string },
    } = $props();

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const canCreate = $derived(permissions.roles?.includes('create') ?? false);
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
                '/roles',
                { search: value || undefined },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        }, 300);
    }

    function confirmDelete(role: Role) {
        deleteTarget = role;
    }

    function executeDelete() {
        if (!deleteTarget) {
            return;
        }

        deleting = true;
        router.delete(`/roles/${deleteTarget.id}`, {
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
    title="Roles"
    description="Manage roles and their permissions."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Roles' },
    ]}
    actions={canCreate
        ? [{ label: 'Create Role', href: '/roles/create', variant: 'primary' }]
        : []}
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

    <!-- Roles table -->
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
                            class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >Users</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Created</th
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
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-indigo-500/60 text-xs font-bold text-white shadow-sm"
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
                                >{formatDate(role.created_at)}</td
                            >
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {#if canUpdate}
                                        <Link
                                            href={`/roles/${role.id}/edit`}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                        >
                                            <Pencil size={14} />
                                            Edit
                                        </Link>
                                    {/if}
                                    {#if canDelete && !role.is_system}
                                        <button
                                            onclick={() => confirmDelete(role)}
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:text-gray-500 dark:hover:bg-red-950/50 dark:hover:text-red-400"
                                            title="Delete role"
                                        >
                                            <Trash2 size={15} />
                                        </button>
                                    {/if}
                                    {#if role.is_system}
                                        <span
                                            class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                        >
                                            <Lock size={12} />
                                            System
                                        </span>
                                    {/if}
                                    {#if !canUpdate && !canDelete && !role.is_system}
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
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <ShieldCheck
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
                                                No roles yet
                                            {/if}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {#if filters.search}
                                                No roles matching "<strong
                                                    >{filters.search}</strong
                                                >"
                                            {:else}
                                                Create your first role to get
                                                started.
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
                            <Link
                                href={link.url}
                                class={cn(
                                    'rounded-lg px-3 py-1.5 text-sm font-medium transition-all',
                                    link.active
                                        ? 'bg-indigo-600 text-white shadow-sm'
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

<DeleteConfirmModal
    open={deleteTarget !== null}
    title="Delete Role"
    message="This will permanently remove this role."
    itemName={deleteTarget?.name ?? ''}
    processing={deleting}
    onclose={() => {
        deleteTarget = null;
    }}
    onconfirm={executeDelete}
/>
