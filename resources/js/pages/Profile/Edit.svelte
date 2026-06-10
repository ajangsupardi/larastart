<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import { Camera, LoaderCircle, User, Mail, Lock } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import AvatarCropModal from '@/components/AvatarCropModal.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    type UserProfile = {
        id: number;
        name: string;
        email: string;
        avatar: string | null;
        avatar_url: string | null;
        roles: string[];
    };

    let { user = {} as UserProfile } = $props();

    let showCropModal = $state(false);
    let selectedImageSrc = $state('');
    let fileInputEl: HTMLInputElement | undefined = $state();

    function getInitials(name: string) {
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    }

    function handleFileSelect() {
        fileInputEl?.click();
    }

    function onFileSelected(e: Event) {
        const input = e.target as HTMLInputElement;
        const file = input.files?.[0];
        if (!file) return;

        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            alert('Only PNG, JPG, and JPEG files are allowed.');
            input.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            selectedImageSrc = e.target?.result as string;
            showCropModal = true;
        };
        reader.readAsDataURL(file);
        input.value = '';
    }

    function onCropSuccess() {
        showCropModal = false;
        selectedImageSrc = '';
    }

    function onCropClose() {
        showCropModal = false;
        selectedImageSrc = '';
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
                    class="mb-6 flex items-center gap-5 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="relative shrink-0">
                        {#if user.avatar_url}
                            <img
                                src={user.avatar_url}
                                alt={user.name}
                                class="h-16 w-16 rounded-full object-cover shadow-md"
                            />
                        {:else}
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand to-brand/60 text-xl font-bold text-white shadow-md"
                            >
                                {getInitials(user.name ?? '')}
                            </div>
                        {/if}
                        <button
                            type="button"
                            onclick={handleFileSelect}
                            class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-gray-800 text-white shadow-sm transition-colors hover:bg-gray-700 dark:border-gray-900"
                            title="Change photo"
                        >
                            <Camera size={12} />
                        </button>
                        <input
                            type="file"
                            bind:this={fileInputEl}
                            onchange={onFileSelected}
                            accept="image/png,image/jpg,image/jpeg"
                            class="hidden"
                        />
                    </div>

                    <div class="min-w-0">
                        <h2 class="truncate text-lg font-bold text-gray-900 dark:text-gray-100">
                            {user.name}
                        </h2>
                        <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">
                            {user.roles?.join(', ') ?? 'No roles'}
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

<AvatarCropModal
    open={showCropModal}
    imageSrc={selectedImageSrc}
    onclose={onCropClose}
    onsuccess={onCropSuccess}
/>
