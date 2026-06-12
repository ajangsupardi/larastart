<script lang="ts">
    import { Form, router } from '@inertiajs/svelte';
    import {
        LoaderCircle,
        Settings,
        Sun,
        Moon,
        Monitor,
        Lock,
        Bell,
        Info,
        Trash2,
    } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';
    import { themeMode } from '@/stores/theme';

    type SettingsData = {
        name: string;
        timezone: string;
        email_notifications: boolean;
        activity_log_days: number;
    };

    type SystemInfo = {
        php_version: string;
        laravel_version: string;
        os: string;
        disk_total: string;
        disk_free: string;
        db_size: string;
        app_url: string;
        app_env: string;
    };

    let { settings = {} as SettingsData, system = {} as SystemInfo } = $props();

    let activeTab = $state('general');

    const tabs = [
        { id: 'general', label: 'General', icon: Settings },
        { id: 'appearance', label: 'Appearance', icon: Sun },
        { id: 'security', label: 'Security', icon: Lock },
        { id: 'notifications', label: 'Notifications', icon: Bell },
        { id: 'system', label: 'System Info', icon: Info },
    ];

    const timezones = [
        'UTC',
        'America/New_York',
        'America/Chicago',
        'America/Denver',
        'America/Los_Angeles',
        'America/Anchorage',
        'Pacific/Honolulu',
        'America/Toronto',
        'America/Vancouver',
        'Europe/London',
        'Europe/Paris',
        'Europe/Berlin',
        'Europe/Moscow',
        'Asia/Tokyo',
        'Asia/Shanghai',
        'Asia/Singapore',
        'Asia/Kolkata',
        'Asia/Dubai',
        'Australia/Sydney',
        'Australia/Melbourne',
        'Pacific/Auckland',
    ];

    function formatBytes(bytes: string | number): string {
        const b = typeof bytes === 'string' ? parseInt(bytes, 10) : bytes;

        if (isNaN(b) || b <= 0) {
            return 'N/A';
        }

        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(b) / Math.log(1024));

        return (b / Math.pow(1024, i)).toFixed(2) + ' ' + units[i];
    }

    function clearCache() {
        if (confirm('Are you sure you want to clear all caches?')) {
            router.post('/settings/clear-cache');
        }
    }

    function setTheme(mode: 'light' | 'dark' | 'system') {
        themeMode.set(mode);
    }
</script>

<DashboardLayout
    title="Settings"
    description="Manage your application settings."
    breadcrumbs={[
        { label: 'Dashboard', href: '/dashboard' },
        { label: 'Settings' },
    ]}
>
    <!-- Tab Navigation -->
    <div class="mb-6 flex flex-wrap gap-2">
        {#each tabs as tab (tab.id)}
            <button
                onclick={() => (activeTab = tab.id)}
                class={cn(
                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                    activeTab === tab.id
                        ? 'bg-brand text-white shadow-sm'
                        : 'bg-white text-gray-600 hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800',
                    'border border-gray-200 dark:border-gray-800',
                )}
            >
                <tab.icon size={16} />
                {tab.label}
            </button>
        {/each}
    </div>

    <!-- General Tab -->
    {#if activeTab === 'general'}
        <Form action="/settings/general" method="put">
            {#snippet children({ errors, processing })}
                <input type="hidden" name="_method" value="put" />
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Settings
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                General Settings
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Configure your application's basic settings.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="name"
                            label="Application Name"
                            placeholder="My Application"
                            required
                            error={errors.name}
                            value={settings.name}
                        />
                        <div>
                            <label
                                for="timezone"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Timezone
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="timezone"
                                id="timezone"
                                required
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                {#each timezones as tz (tz)}
                                    <option
                                        value={tz}
                                        selected={tz === settings.timezone}
                                    >
                                        {tz}
                                    </option>
                                {/each}
                            </select>
                            {#if errors.timezone}
                                <p
                                    class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                                >
                                    {errors.timezone}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

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
                            Save Changes
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    {/if}

    <!-- Appearance Tab -->
    {#if activeTab === 'appearance'}
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
        >
            <div
                class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
            >
                <div class="flex items-center gap-2">
                    <Sun size={18} class="text-gray-500 dark:text-gray-400" />
                    <h2
                        class="text-base font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Appearance
                    </h2>
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    Choose how the application looks on your device.
                </p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <button
                        onclick={() => setTheme('light')}
                        class={cn(
                            'flex flex-col items-center gap-3 rounded-xl border-2 p-6 transition-all',
                            $themeMode === 'light'
                                ? 'border-brand bg-brand/5'
                                : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600',
                        )}
                    >
                        <div
                            class={cn(
                                'flex h-12 w-12 items-center justify-center rounded-full',
                                $themeMode === 'light'
                                    ? 'bg-brand text-white'
                                    : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                            )}
                        >
                            <Sun size={24} />
                        </div>
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            Light
                        </span>
                    </button>

                    <button
                        onclick={() => setTheme('dark')}
                        class={cn(
                            'flex flex-col items-center gap-3 rounded-xl border-2 p-6 transition-all',
                            $themeMode === 'dark'
                                ? 'border-brand bg-brand/5'
                                : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600',
                        )}
                    >
                        <div
                            class={cn(
                                'flex h-12 w-12 items-center justify-center rounded-full',
                                $themeMode === 'dark'
                                    ? 'bg-brand text-white'
                                    : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                            )}
                        >
                            <Moon size={24} />
                        </div>
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            Dark
                        </span>
                    </button>

                    <button
                        onclick={() => setTheme('system')}
                        class={cn(
                            'flex flex-col items-center gap-3 rounded-xl border-2 p-6 transition-all',
                            $themeMode === 'system'
                                ? 'border-brand bg-brand/5'
                                : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600',
                        )}
                    >
                        <div
                            class={cn(
                                'flex h-12 w-12 items-center justify-center rounded-full',
                                $themeMode === 'system'
                                    ? 'bg-brand text-white'
                                    : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                            )}
                        >
                            <Monitor size={24} />
                        </div>
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            System
                        </span>
                    </button>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    "System" mode automatically switches between light and dark
                    based on your device's settings.
                </p>
            </div>
        </div>
    {/if}

    <!-- Security Tab -->
    {#if activeTab === 'security'}
        <Form action="/settings/password" method="put">
            {#snippet children({ errors, processing })}
                <input type="hidden" name="_method" value="put" />
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
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
                            Ensure your account stays secure by using a strong
                            password.
                        </p>
                    </div>

                    <div class="space-y-5 p-6">
                        <Input
                            name="current_password"
                            type="password"
                            label="Current Password"
                            placeholder="Enter current password"
                            required
                            error={errors.current_password}
                            icon={Lock}
                        />
                        <Input
                            name="password"
                            type="password"
                            label="New Password"
                            placeholder="Enter new password"
                            required
                            error={errors.password}
                            icon={Lock}
                        />
                        <Input
                            name="password_confirmation"
                            type="password"
                            label="Confirm New Password"
                            placeholder="Confirm new password"
                            required
                            icon={Lock}
                        />
                    </div>
                </div>

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
                            Updating...
                        {:else}
                            <Lock size={16} />
                            Update Password
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    {/if}

    <!-- Notifications Tab -->
    {#if activeTab === 'notifications'}
        <Form action="/settings/notifications" method="put">
            {#snippet children({ errors, processing })}
                <input type="hidden" name="_method" value="put" />
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div
                        class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
                    >
                        <div class="flex items-center gap-2">
                            <Bell
                                size={18}
                                class="text-gray-500 dark:text-gray-400"
                            />
                            <h2
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                Notification Settings
                            </h2>
                        </div>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Configure how you receive notifications.
                        </p>
                    </div>

                    <div class="space-y-6 p-6">
                        <!-- Email Notifications Toggle -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Email Notifications
                                </p>
                                <p
                                    class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Receive email notifications for important
                                    updates.
                                </p>
                            </div>
                            <label
                                class="relative inline-flex cursor-pointer items-center"
                            >
                                <input
                                    type="checkbox"
                                    name="email_notifications"
                                    value="1"
                                    checked={settings.email_notifications}
                                    class="peer sr-only"
                                />
                                <div
                                    class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand peer-checked:after:translate-x-full peer-checked:after:border-white dark:bg-gray-700 dark:peer-checked:bg-brand"
                                ></div>
                            </label>
                        </div>

                        <!-- Activity Log Retention -->
                        <div>
                            <label
                                for="activity_log_days"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Activity Log Retention (days)
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="activity_log_days"
                                id="activity_log_days"
                                min="7"
                                max="365"
                                required
                                value={settings.activity_log_days}
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition-all focus:border-brand focus:ring-2 focus:ring-brand/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            />
                            <p
                                class="mt-1.5 text-sm text-gray-500 dark:text-gray-400"
                            >
                                How long to keep activity logs (7-365 days).
                            </p>
                            {#if errors.activity_log_days}
                                <p
                                    class="mt-1.5 text-sm text-red-600 dark:text-red-400"
                                >
                                    {errors.activity_log_days}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

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
                            <Bell size={16} />
                            Save Preferences
                        {/if}
                    </button>
                </div>
            {/snippet}
        </Form>
    {/if}

    <!-- System Info Tab -->
    {#if activeTab === 'system'}
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
        >
            <div
                class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/50"
            >
                <div class="flex items-center gap-2">
                    <Info size={18} class="text-gray-500 dark:text-gray-400" />
                    <h2
                        class="text-base font-semibold text-gray-900 dark:text-gray-100"
                    >
                        System Information
                    </h2>
                </div>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    Overview of your application environment.
                </p>
            </div>

            <div class="p-6">
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            PHP Version
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {system.php_version}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Laravel Version
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {system.laravel_version}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Application URL
                        </p>
                        <p
                            class="mt-1 truncate text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {system.app_url}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Environment
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {system.app_env}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Disk Total
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {formatBytes(system.disk_total)}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Disk Free
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {formatBytes(system.disk_free)}
                        </p>
                    </div>

                    <div
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p
                            class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
                        >
                            Database Size
                        </p>
                        <p
                            class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            {system.db_size}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700"
                >
                    <button
                        onclick={clearCache}
                        class={cn(
                            'inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-800 dark:bg-gray-900 dark:text-red-400 dark:hover:bg-red-950/30',
                        )}
                    >
                        <Trash2 size={16} />
                        Clear All Caches
                    </button>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        This will clear config, route, view, and application
                        caches.
                    </p>
                </div>
            </div>
        </div>
    {/if}
</DashboardLayout>
