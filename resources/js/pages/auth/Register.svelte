<script lang="ts">
    import { Link, Form } from '@inertiajs/svelte';
    import { LoaderCircle } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import { cn } from '@/lib/utils';
</script>

<AuthLayout title="Register">
    <Form action="/register" method="post">
        {#snippet children({ errors, processing })}
            <div class="space-y-6">
                <div class="text-center">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Create an account
                    </h2>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                        Already have an account?
                        <Link
                            href="/login"
                            class="font-medium text-brand hover:text-brand/80 dark:text-brand"
                        >
                            Sign in
                        </Link>
                    </p>
                </div>

                <div class="space-y-4">
                    <Input
                        name="name"
                        type="text"
                        label="Name"
                        placeholder="Your full name"
                        required
                        error={errors.name}
                    />
                    <Input
                        name="email"
                        type="email"
                        label="Email"
                        placeholder="you@example.com"
                        required
                        error={errors.email}
                    />
                    <Input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="Create a strong password"
                        required
                        error={errors.password}
                    />
                    <Input
                        name="password_confirmation"
                        type="password"
                        label="Confirm Password"
                        placeholder="Repeat your password"
                        required
                        error={errors.password_confirmation}
                    />
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    By creating an account, you agree to our
                    <span
                        class="font-medium text-brand hover:text-brand/80 dark:text-brand cursor-default"
                        >Terms of Service</span
                    >
                    and
                    <span
                        class="font-medium text-brand hover:text-brand/80 dark:text-brand cursor-default"
                        >Privacy Policy</span
                    >.
                </p>

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
                        <span>Creating account...</span>
                    {:else}
                        <span>Create account</span>
                    {/if}
                </button>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
