<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import {
        LoaderCircle,
        ArrowLeft,
        UserPlus,
        Mail,
        Lock,
    } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';
</script>

<DashboardLayout
    title="Create User"
    description="Add a new user to the system."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Users', href: '/users' },
        { label: 'Create' },
    ]}
>
    <div class="mx-auto max-w-2xl">
        <Form action="/users" method="post" resetOnSuccess setDefaultsOnSuccess>
            {#snippet children({ errors, processing, wasSuccessful })}
                <!-- Profile Information -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <UserPlus
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Account Information
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Enter the basic details for the new user.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="Full Name"
                            placeholder="John Doe"
                            required
                            error={errors.name}
                            icon={UserPlus}
                        />
                        <Input
                            name="email"
                            type="email"
                            label="Email Address"
                            placeholder="john@example.com"
                            required
                            error={errors.email}
                            icon={Mail}
                        />
                    </div>
                </div>

                <!-- Password Section -->
                <div
                    class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Lock
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Password
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Set a secure password for the new user.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="password"
                            type="password"
                            label="Password"
                            placeholder="Enter password"
                            required
                            error={errors.password}
                            icon={Lock}
                        />
                        <Input
                            name="password_confirmation"
                            type="password"
                            label="Confirm Password"
                            placeholder="Confirm password"
                            required
                            icon={Lock}
                        />
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
                            User created successfully!
                        </div>
                    </div>
                {/if}

                <!-- Form Actions -->
                <div
                    class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <Link
                        href="/users"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                    >
                        <ArrowLeft size={16} />
                        Back to Users
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
                            Creating...
                        {:else}
                            <UserPlus size={16} />
                            Create User
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    </div>
</DashboardLayout>
