<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import { LoaderCircle, User, Mail, Lock } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type UserProfile = {
        id: number;
        name: string;
        email: string;
    };

    let { user = {} as UserProfile } = $props();

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
    title="Edit Profile"
    description="Manage your account settings."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Profile' },
    ]}
>
    <div class="mx-auto max-w-2xl">
        <Form action="/profile" method="post">
            {#snippet children({ errors, processing, wasSuccessful })}
                <input type="hidden" name="_method" value="put" />

                <!-- User Avatar Preview -->
                <div
                    class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand to-brand/60 text-xl font-bold text-white shadow-md"
                    >
                        {getInitials(user.name ?? '')}
                    </div>
                    <div>
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {user.name}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {user.email}
                        </p>
                    </div>
                </div>

                <!-- Profile Information -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <User
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Profile Information
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Update your personal details.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="Full Name"
                            placeholder="Your full name"
                            required
                            error={errors.name}
                            value={user.name}
                            icon={User}
                        />
                        <Input
                            name="email"
                            type="email"
                            label="Email Address"
                            placeholder="you@example.com"
                            required
                            error={errors.email}
                            value={user.email}
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
                                Change Password
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Leave blank to keep your current password.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="password"
                            type="password"
                            label="New Password"
                            placeholder="Enter new password"
                            error={errors.password}
                            icon={Lock}
                        />
                        <Input
                            name="password_confirmation"
                            type="password"
                            label="Confirm New Password"
                            placeholder="Confirm new password"
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
                            Profile updated successfully!
                        </div>
                    </div>
                {/if}

                <!-- Form Actions -->
                <div
                    class="mt-6 flex items-center justify-end rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900"
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
                        {#if processing}
                            <LoaderCircle size={16} class="animate-spin" />
                            Saving...
                        {:else}
                            <User size={16} />
                            Update Profile
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    </div>
</DashboardLayout>
