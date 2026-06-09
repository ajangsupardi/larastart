<script lang="ts">
    import { Form } from '@inertiajs/svelte';
    import { LoaderCircle } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import { cn } from '@/lib/utils';

    let { email = '', token = '' } = $props();
</script>

<AuthLayout title="Reset Password">
    <Form action="/reset-password" method="post">
        {#snippet children({ errors, processing })}
            <div class="space-y-6">
                <div class="text-center">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Set new password
                    </h2>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                        Choose a strong password for your account.
                    </p>
                </div>

                <input type="hidden" name="token" value={token} />

                <div class="space-y-4">
                    <Input
                        name="email"
                        type="email"
                        label="Email"
                        value={email}
                        required
                        error={errors.email}
                    />
                    <Input
                        name="password"
                        type="password"
                        label="New Password"
                        placeholder="Enter your new password"
                        required
                        error={errors.password}
                    />
                    <Input
                        name="password_confirmation"
                        type="password"
                        label="Confirm Password"
                        placeholder="Repeat your new password"
                        required
                    />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    class={cn(
                        'flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white shadow-sm transition-all',
                        processing
                            ? 'bg-brand/40 cursor-not-allowed'
                            : 'bg-brand hover:bg-brand/90 active:scale-[0.98]',
                    )}
                >
                    {#if processing}
                        <LoaderCircle size={16} class="animate-spin" />
                        <span>Resetting...</span>
                    {:else}
                        <span>Reset password</span>
                    {/if}
                </button>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
