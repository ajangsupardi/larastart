<script lang="ts">
    import { Link, router, usePage } from '@inertiajs/svelte';
    import { Search, Trash2, Pencil, Home, Download } from '@lucide/svelte';
    import DeleteConfirmModal from '@/components/DeleteConfirmModal.svelte';
    import Select2 from '@/components/Select2.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type Village = {
        id: number;
        name: string;
        postal_code: string | null;
        district: {
            id: number;
            name: string;
            regency: {
                id: number;
                name: string;
                province: { id: number; name: string };
            };
        } | null;
        district_id: number;
        created_at: string;
    };

    let {
        villages = { data: [] as Village[], meta: {} as Record<string, any> },
        filters = {} as { search?: string; district_id?: string },
        districts = [] as { id: number; name: string }[],
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
        permissions.villages?.includes('create') ?? false,
    );
    const canUpdate = $derived(
        permissions.villages?.includes('update') ?? false,
    );
    const canDelete = $derived(
        permissions.villages?.includes('delete') ?? false,
    );

    // svelte-ignore state_referenced_locally
    let search = $state.raw(filters.search ?? '');
    // svelte-ignore state_referenced_locally
    let districtFilter = $state.raw(filters.district_id ?? '');
    let deleteTarget = $state<Village | null>(null);
    let deleting = $state(false);

    let searchTimeout: ReturnType<typeof setTimeout>;

    function onSearchInput(e: Event) {
        const value = (e.target as HTMLInputElement).value;
        search = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/villages',
                {
                    search: value || undefined,
                    district_id: districtFilter || undefined,
                },
                { preserveState: true, preserveScroll: true, replace: true },
            );
        }, 300);
    }

    function handleFilterChange(val: string) {
        districtFilter = val;
        router.get(
            '/villages',
            { search: search || undefined, district_id: val || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function confirmDelete(village: Village) {
        deleteTarget = village;
    }

    function executeDelete() {
        if (!deleteTarget) {
            return;
        }

        deleting = true;
        router.delete(`/villages/${deleteTarget.id}`, {
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
    title="Villages"
    description="Manage all villages."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Villages' },
    ]}
    actions={canCreate
        ? [
              {
                  label: 'Create Village',
                  href: '/villages/create',
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
                    placeholder="Search by name or postal code..."
                    value={search}
                    oninput={onSearchInput}
                    class="w-full border-0 bg-transparent py-0.5 text-sm outline-none placeholder:text-gray-400 dark:text-white"
                />
            </div>
        </div>
        <div class="w-full sm:w-64">
            <Select2
                value={districtFilter}
                items={districts.map((d) => ({ value: d.id, label: d.name }))}
                placeholder="All Districts"
                onchange={handleFilterChange}
            />
        </div>
        <a
            href="/export/villages?search={search ||
                ''}&district_id={districtFilter || ''}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50 hover:text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
            title="Export CSV"
        >
            <Download size={16} />
        </a>
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
                            >Postal Code</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >District</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Regency</th
                        >
                        <th
                            class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300"
                            >Created At</th
                        >
                        <th
                            class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                            >Actions</th
                        >
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    {#each villages.data as village (village.id)}
                        <tr
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-600 text-xs font-bold text-white shadow-sm"
                                    >
                                        <Home size={16} />
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                        >{village.name}</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-400/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600/20"
                                >
                                    {village.postal_code ?? '-'}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{village.district?.name ?? '-'}</td
                            >
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{village.district?.regency?.name ?? '-'}</td
                            >
                            <td
                                class="px-6 py-4 text-gray-500 dark:text-gray-400"
                                >{formatDate(village.created_at)}</td
                            >
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    {#if canUpdate}
                                        <Link
                                            href={`/villages/${village.id}/edit`}
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                                        >
                                            <Pencil size={14} /> Edit
                                        </Link>
                                    {/if}
                                    {#if canDelete}
                                        <button
                                            onclick={() =>
                                                confirmDelete(village)}
                                            class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:text-gray-500 dark:hover:bg-red-950/50 dark:hover:text-red-400"
                                            title="Delete village"
                                        >
                                            <Trash2 size={15} />
                                        </button>
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {:else}
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <Home
                                        size={40}
                                        class="text-gray-300 dark:text-gray-600"
                                    />
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {filters.search
                                            ? 'No results found'
                                            : 'No villages yet'}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>

        {#if villages.meta && villages.meta.last_page > 1}
            <div
                class="flex flex-col items-center justify-between gap-4 border-t border-gray-200 px-6 py-4 sm:flex-row dark:border-gray-800"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span
                        class="font-medium text-gray-700 dark:text-gray-300"
                        >{villages.meta.from}</span
                    >
                    to
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{villages.meta.to}</span
                    >
                    of
                    <span class="font-medium text-gray-700 dark:text-gray-300"
                        >{villages.meta.total}</span
                    > entries
                </p>
                <div class="flex items-center gap-1.5">
                    {#each villages.meta.links as link (link.label)}
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
    title="Delete Village"
    message="This will permanently remove this village."
    itemName={deleteTarget?.name ?? ''}
    processing={deleting}
    onclose={() => {
        deleteTarget = null;
    }}
    onconfirm={executeDelete}
/>
