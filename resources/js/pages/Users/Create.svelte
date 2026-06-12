<script lang="ts">
    import { Form, Link } from '@inertiajs/svelte';
    import {
        LoaderCircle,
        ArrowLeft,
        UserPlus,
        Mail,
        Lock,
        Camera,
    } from '@lucide/svelte';
    import AvatarCropModal from '@/components/AvatarCropModal.svelte';
    import Input from '@/components/Input.svelte';
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';
    import { cn } from '@/lib/utils';

    let showCropModal = $state(false);
    let selectedImageSrc = $state('');
    let fileInputEl: HTMLInputElement | undefined = $state();
    let avatarFileInputEl: HTMLInputElement | undefined = $state();

    function handleFileSelect() {
        fileInputEl?.click();
    }

    function onFileSelected(e: Event) {
        const input = e.target as HTMLInputElement;
        const file = input.files?.[0];

        if (!file) {
            return;
        }

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

    function onAvatarCropped(croppedFile: File) {
        if (avatarFileInputEl) {
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            avatarFileInputEl.files = dt.files;
        }
    }
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
        <Form action="/users" method="post">
            {#snippet children({ errors, processing })}
                <!-- Avatar -->
                <div
                    class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="relative shrink-0">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand to-brand/60 text-xl font-bold text-white shadow-md"
                        >
                            <UserPlus size={24} />
                        </div>
                        <button
                            type="button"
                            onclick={handleFileSelect}
                            class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-gray-800 text-white shadow-sm transition-colors hover:bg-gray-700 dark:border-gray-900"
                            title="Set photo"
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
                    <div>
                        <h3
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            Profile Photo
                        </h3>
                        <p
                            class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Optional. Crop to 400×400 on next step.
                        </p>
                    </div>
                </div>

                <input
                    type="file"
                    name="avatar"
                    bind:this={avatarFileInputEl}
                    class="hidden"
                    accept="image/webp"
                />

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

<AvatarCropModal
    open={showCropModal}
    imageSrc={selectedImageSrc}
    onclose={onCropClose}
    onsuccess={onCropSuccess}
    oncropsave={onAvatarCropped}
/>
