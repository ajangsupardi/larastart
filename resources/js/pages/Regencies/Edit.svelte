<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import { LoaderCircle, ArrowLeft, Building2 } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type Regency = {
        id: number;
        name: string;
        province_id: number;
        province: { id: number; name: string } | null;
    };

    let {
        regency = {} as Regency,
        provinces = [] as { id: number; name: string }[],
    } = $props();
</script>

<DashboardLayout
    title="Edit Regency"
    description="Update regency information."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Regencies', href: '/regencies' },
        { label: 'Edit' },
    ]}
>
    <div class="mx-auto max-w-2xl">
        <Form
            action={`/regencies/${regency.id}`}
            method="post"
            setDefaultsOnSuccess
        >
            {#snippet children({ errors, processing, wasSuccessful })}
                <input type="hidden" name="_method" value="put" />

                <!-- Regency Preview -->
                <div
                    class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-amber-500 to-amber-500/60 text-white shadow-md"
                    >
                        <Building2 size={28} />
                    </div>
                    <div>
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {regency.name}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Province: {regency.province?.name ?? '-'}
                        </p>
                    </div>
                </div>

                <!-- Regency Information -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Building2
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Regency Information
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Update the regency details.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="Regency Name"
                            placeholder="e.g. Bandung"
                            required
                            error={errors.name}
                            value={regency.name}
                            icon={Building2}
                        />
                        <div>
                            <label
                                for="province_id"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Province <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="province_id"
                                name="province_id"
                                required
                                class={cn(
                                    'w-full rounded-lg border bg-white px-3 py-2 text-sm outline-none transition-colors',
                                    'border-gray-200 focus:border-brand focus:ring-1 focus:ring-brand/20',
                                    'dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-brand',
                                    errors.province_id &&
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/20',
                                )}
                            >
                                <option value="">Select a province</option>
                                {#each provinces as province (province.id)}
                                    <option
                                        value={province.id}
                                        selected={province.id ===
                                            regency.province_id}
                                    >
                                        {province.name}
                                    </option>
                                {/each}
                            </select>
                            {#if errors.province_id}
                                <p class="mt-1 text-xs text-red-500">
                                    {errors.province_id}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

                {#if wasSuccessful}
                    <div
                        class="mt-6 rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300"
                    >
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-green-200 text-green-700 dark:bg-green-700 dark:text-green-200"
                                >&#10003;</span
                            >
                            Regency updated successfully!
                        </div>
                    </div>
                {/if}

                <!-- Form Actions -->
                <div
                    class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <Link
                        href="/regencies"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        <ArrowLeft size={16} />
                        Back to Regencies
                    </Link>

                    <button
                        type="submit"
                        disabled={processing}
                        class={cn(
                            'inline-flex items-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white shadow-sm transition-all active:scale-[0.98]',
                            processing
                                ? 'cursor-not-allowed bg-brand/40'
                                : 'bg-brand hover:bg-brand/90',
                        )}
                    >
                        {#if processing}
                            <LoaderCircle size={16} class="animate-spin" />
                            Saving...
                        {:else}
                            <Building2 size={16} />
                            Update Regency
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    </div>
</DashboardLayout>
