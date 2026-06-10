<script lang="ts">
    import { Link, router, usePage } from '@inertiajs/svelte';
    import { Search, Trash2, Pencil, Landmark } from '@lucide/svelte';
    import DeleteConfirmModal from '@/components/DeleteConfirmModal.svelte';
    import Select2 from '@/components/Select2.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type District = {
        id: number;
        name: string;
        regency: {
            id: number;
            name: string;
            province: { id: number; name: string };
        } | null;
        regency_id: number;
        created_at: string;
    };

    let {
        districts = { data: [] as District[], meta: {} as Record<string, any> },
        filters = {} as { search?: string; regency_id?: string },
        regencies = [] as { id: number; name: string }[],
        stats = [] as {
            label: string;
            value: string | number;
            trend?: number;
            icon?: string;
        }[],
    } = $props();

    const page = usePage();
    const permissions = $derived(page.props.auth?.permissions ?? {});
    const canCreate = $derived(
        permissions.districts?.includes('create') ?? false,
    );
    const canUpdate = $derived(
        permissions.districts?.includes('update') ?? false,
    );
    const canDelete = $derived(
        permissions.districts?.includes('delete') ?? false,
    );

    // svelte-ignore state_referenced_locally
    let search = $state.raw(filters.search ?? '');
    // svelte-ignore state_referenced_locally
    let regencyFilter = $state.raw(filters.regency_id ?? '');
    let deleteTarget = $state<District | null>(null);
    let deleting = $state(false);
    let searchTimeout: ReturnType<typeof setTimeout>;

    function onSearchInput(e: Event) {
        const value = (e.target as HTMLInputElement).value;
        search = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/districts',
                {
                    search: value || undefined,
                    regency_id: regencyFilter || undefined,
                },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        }, 300);
    }

    function handleFilterChange(val: string) {
        regencyFilter = val;
        router.get(
            '/districts',
            { search: search || undefined, regency_id: val || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function confirmDelete(district: District) {
        deleteTarget = district;
    }

    function executeDelete() {
        if (!deleteTarget) {
            return;
        }

        deleting = true;
        router.delete(`/districts/${deleteTarget.id}`, {
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
    title="Districts"
    description="Manage all districts."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Districts' },
    ]}
    actions={canCreate
        ? [
              {
                  label: 'Create District',
                  href: '/districts/create',
                  variant: 'primary',
              },
          ]
        : []}
    cards={stats}
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <div
                class="relative flex items-center gap-2 rounded-lg border border-gray-200 bg-white pl-4 pr-3 py-2 text-sm transition-all dark:border-gray-600 dark:bg-gray-800"
            >
                <Search size={15} class="shrink-0 text-gray-400" />
                <input
                    type="text"
                    placeholder="Search by name..."
                    value={search}
                    oninput={onSearchInput}
                    class="w-full border-0 bg-transparent py-0.5 text-sm outline-none placeholder:text-gray-400 dark:text-white"
                />
            </div>
        </div>
        <div class="w-full sm:w-64">
            <Select2
                value={regencyFilter}
                items={regencies.map((r) => ({ value: r.id, label: r.name }))}
                placeholder="All Regencies"
                onchange={handleFilterChange}
            />
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
                            >Name</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Regency</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Province</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Created At</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Actions</th
                        >
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    {#each districts.data as district (district.id)}
                        <tr
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-violet-500/60 text-xs font-bold text-white shadow-sm"
                                    >
                                        <Landmark size={16} />
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                        >{district.name}</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-400/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600/20"
                                >
                                    {district.regency?.name ?? '-'}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{district.regency?.province?.name ?? '-'}</td
                            >
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{formatDate(district.created_at)}</td
                            >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    {#if canUpdate}
                                        <Link
                                            href={`/districts/${district.id}/edit`}
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 active:scale-[0.98] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                        >
                                            <Pencil size={14} /> Edit
                                        </Link>
                                    {/if}
                                    {#if canDelete}
                                        <button
                                            onclick={() =>
                                                confirmDelete(district)}
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm transition-all hover:bg-red-50 active:scale-[0.98] dark:border-red-900 dark:bg-gray-800 dark:text-red-400"
                                        >
                                            <Trash2 size={14} /> Delete
                                        </button>
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <Landmark
                                        size={40}
                                        class="text-gray-300 dark:text-gray-600"
                                    />
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {#if filters.search || filters.regency_id}
                                            No results found
                                        {:else}
                                            No districts yet
                                        {/if}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        {#if districts.meta && districts.meta.last_page > 1}
            <div
                class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 sm:flex-row dark:border-gray-800"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span
                        class="font-medium text-gray-700 dark:text-gray-300"
                        >{districts.meta.from}</span
                    >
                    to
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{districts.meta.to}</span
                    >
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{districts.meta.total}</span
                    > entries
                </p>
                <div class="flex items-center gap-1.5">
                    {#each districts.meta.links as link (link.label)}
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

<DeleteConfirmModal
    open={deleteTarget !== null}
    title="Delete District"
    message="This will permanently remove this district and all associated data."
    itemName={deleteTarget?.name ?? ''}
    processing={deleting}
    onclose={() => {
        deleteTarget = null;
    }}
    onconfirm={executeDelete}
/>
