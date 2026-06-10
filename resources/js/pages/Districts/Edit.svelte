<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import { LoaderCircle, ArrowLeft, Landmark } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type District = {
        id: number;
        name: string;
        regency_id: number;
        regency: { id: number; name: string } | null;
    };

    let {
        district = {} as District,
        regencies = [] as { id: number; name: string }[],
    } = $props();
</script>

<DashboardLayout
    title="Edit District"
    description="Update district information."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Districts', href: '/districts' },
        { label: 'Edit' },
    ]}
>
    <div class="mx-auto max-w-2xl">
        <Form
            action={`/districts/${district.id}`}
            method="post"
            setDefaultsOnSuccess
        >
            {#snippet children({ errors, processing, wasSuccessful })}
                <input type="hidden" name="_method" value="put" />

                <div
                    class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-violet-500/60 text-white shadow-md"
                    >
                        <Landmark size={28} />
                    </div>
                    <div>
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {district.name}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Regency: {district.regency?.name ?? '-'}
                        </p>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Landmark
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                District Information
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Update the district details.
                        </p>
                    </div>
                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="District Name"
                            placeholder="e.g. Cianjur"
                            required
                            error={errors.name}
                            value={district.name}
                            icon={Landmark}
                        />
                        <div>
                            <label
                                for="regency_id"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Regency <span class="text-red-500">*</span
                                ></label
                            >
                            <select
                                id="regency_id"
                                name="regency_id"
                                required
                                class={cn(
                                    'w-full rounded-lg border bg-white px-3 py-2 text-sm outline-none transition-colors',
                                    'border-gray-200 focus:border-brand focus:ring-1 focus:ring-brand/20',
                                    'dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-brand',
                                    errors.regency_id && 'border-red-500',
                                )}
                            >
                                <option value="">Select a regency</option>
                                {#each regencies as regency (regency.id)}
                                    <option
                                        value={regency.id}
                                        selected={regency.id ===
                                            district.regency_id}
                                        >{regency.name}</option
                                    >
                                {/each}
                            </select>
                            {#if errors.regency_id}<p
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {errors.regency_id}
                                </p>{/if}
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
                            > District updated successfully!
                        </div>
                    </div>
                {/if}

                <div
                    class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <Link
                        href="/districts"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                        ><ArrowLeft size={16} /> Back to Districts</Link
                    >
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
                        {#if processing}<LoaderCircle
                                size={16}
                                class="animate-spin"
                            /> Saving...{:else}<Landmark size={16} /> Update District{/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    </div>
</DashboardLayout>
