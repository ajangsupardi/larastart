<script lang="ts">
    import { Link, Form } from '@inertiajs/svelte';
    import { LoaderCircle } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import { cn } from '@/lib/utils';
</script>

<AuthLayout title="Login">
    <Form action="/login" method="post">
        {#snippet children({ errors, processing, recentlySuccessful })}
            <div class="space-y-6">
                <div class="text-center">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Welcome back
                    </h2>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                        Don't have an account?
                        <Link
                            href="/register"
                            class="font-medium text-brand hover:text-brand/80 dark:text-brand"
                        >
                            Create one
                        </Link>
                    </p>
                </div>

                {#if recentlySuccessful}
                    <div
                        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300"
                    >
                        Password reset successfully! Sign in with your new
                        password.
                    </div>
                {/if}

                <div class="space-y-4">
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
                        placeholder="Enter your password"
                        required
                        error={errors.password}
                    />
                </div>

                <div class="flex items-center justify-between">
                    <label
                        class="flex cursor-pointer items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand dark:border-gray-600 dark:bg-gray-800"
                        />
                        Remember me
                    </label>
                    <Link
                        href="/forgot-password"
                        class="text-sm font-medium text-brand hover:text-brand/80 dark:text-brand"
                    >
                        Forgot password?
                    </Link>
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
                        <span>Signing in...</span>
                    {:else}
                        <span>Sign in</span>
                    {/if}
                </button>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
