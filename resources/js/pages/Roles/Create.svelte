<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import { LoaderCircle, ArrowLeft, ShieldCheck, Info } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type Permissions = Record<string, string[]>;

    let {
        resources = {} as Record<string, string>,
        actions = {} as Record<string, string>,
    } = $props();

    let selectedPermissions = $state<Permissions>({});

    function toggleResource(resource: string, checked: boolean) {
        if (checked) {
            selectedPermissions[resource] = Object.keys(actions);
        } else {
            delete selectedPermissions[resource];
        }

        selectedPermissions = { ...selectedPermissions };
    }

    function toggleAction(resource: string, action: string, checked: boolean) {
        if (!selectedPermissions[resource]) {
            selectedPermissions[resource] = [];
        }

        if (checked) {
            selectedPermissions[resource] = [
                ...selectedPermissions[resource],
                action,
            ];
        } else {
            selectedPermissions[resource] = selectedPermissions[
                resource
            ].filter((a) => a !== action);

            if (selectedPermissions[resource].length === 0) {
                delete selectedPermissions[resource];
            }
        }

        selectedPermissions = { ...selectedPermissions };
    }

    function isResourceChecked(resource: string): boolean {
        return (
            !!selectedPermissions[resource] &&
            selectedPermissions[resource].length === Object.keys(actions).length
        );
    }

    function isResourcePartiallyChecked(resource: string): boolean {
        const perms = selectedPermissions[resource];

        return (
            !!perms &&
            perms.length > 0 &&
            perms.length < Object.keys(actions).length
        );
    }
</script>

<DashboardLayout
    title="Create Role"
    description="Define a new role and assign permissions."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Roles', href: '/roles' },
        { label: 'Create' },
    ]}
>
    <div class="mx-auto max-w-2xl">
        <Form action="/roles" method="post">
            {#snippet children({ errors, processing, wasSuccessful })}
                <!-- Basic Information -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <ShieldCheck
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Role Details
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Basic information about the role.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="Role Name"
                            placeholder="e.g. Administrator"
                            required
                            error={errors.name}
                        />
                        <Input
                            name="slug"
                            label="Slug"
                            placeholder="e.g. administrator"
                            required
                            error={errors.slug}
                        />

                        <div>
                            <label
                                for="description"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Description</label
                            >
                            <textarea
                                id="description"
                                name="description"
                                placeholder="Brief description of this role..."
                                rows="2"
                                class={cn(
                                    'block w-full rounded-lg border bg-white px-3.5 py-3 text-sm shadow-sm transition-all',
                                    'placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 resize-none',
                                    'dark:bg-gray-800 dark:text-gray-100 dark:placeholder:text-gray-500',
                                    errors.description
                                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200 dark:border-red-700 dark:focus:ring-red-800'
                                        : 'border-gray-200 focus:border-brand focus:ring-brand/20 dark:border-gray-700 dark:focus:ring-brand/20',
                                )}
                            ></textarea>
                            {#if errors.description}
                                <p
                                    class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                                >
                                    {errors.description}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div
                    class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Info
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Permissions
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Select which actions this role can perform for each
                            resource.
                        </p>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        {#each Object.entries(resources) as [resource, label] (resource)}
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            id="resource-{resource}"
                                            checked={isResourceChecked(
                                                resource,
                                            )}
                                            onchange={(e) =>
                                                toggleResource(
                                                    resource,
                                                    (
                                                        e.target as HTMLInputElement
                                                    ).checked,
                                                )}
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800"
                                        />
                                        <label
                                            for="resource-{resource}"
                                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {label}
                                        </label>
                                    </div>
                                    {#if isResourcePartiallyChecked(resource)}
                                        <span class="text-xs text-gray-400"
                                            >(partial)</span
                                        >
                                    {/if}
                                </div>
                                <div class="mt-4 flex items-center gap-6 pl-7">
                                    {#each Object.entries(actions) as [action, actionLabel] (action)}
                                        <label
                                            class="flex cursor-pointer items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            <input
                                                type="checkbox"
                                                name="permissions[{resource}][]"
                                                value={action}
                                                checked={selectedPermissions[
                                                    resource
                                                ]?.includes(action) ?? false}
                                                onchange={(e) =>
                                                    toggleAction(
                                                        resource,
                                                        action,
                                                        (
                                                            e.target as HTMLInputElement
                                                        ).checked,
                                                    )}
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800"
                                            />
                                            {actionLabel}
                                        </label>
                                    {/each}
                                </div>
                            </div>
                        {/each}
                    </div>

                    {#if errors.permissions}
                        <div class="px-6 pb-4">
                            <p class="text-sm text-red-600 dark:text-red-400">
                                {errors.permissions}
                            </p>
                        </div>
                    {/if}
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
                            Role created successfully!
                        </div>
                    </div>
                {/if}

                <!-- Form Actions -->
                <div
                    class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <Link
                        href="/roles"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        <ArrowLeft size={16} />
                        Back to Roles
                    </Link>

                    <button
                        type="submit"
                        disabled={processing}
                        class={cn(
                            'inline-flex items-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white shadow-sm transition-all active:scale-[0.98]',
                            processing
                                ? 'cursor-not-allowed bg-indigo-400'
                                : 'bg-indigo-600 hover:bg-indigo-700',
                        )}
                    >
                        {#if processing}
                            <LoaderCircle size={16} class="animate-spin" />
                            Creating...
                        {:else}
                            <ShieldCheck size={16} />
                            Create Role
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    </div>
</DashboardLayout>
