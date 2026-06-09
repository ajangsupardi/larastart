<script lang="ts">
    import { Link, Form } from '@inertiajs/svelte';
    import { LoaderCircle, CheckCircle } from '@lucide/svelte';
    import Input from '@/components/Input.svelte';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import { cn } from '@/lib/utils';
</script>

<AuthLayout title="Forgot Password">
    <Form action="/forgot-password" method="post">
        {#snippet children({ errors, processing, wasSuccessful })}
            <div class="space-y-6">
                <div class="text-center">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Reset password
                    </h2>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                        Enter your email and we'll send you a reset link.
                    </p>
                </div>

                {#if wasSuccessful}
                    <div
                        class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300"
                    >
                        <CheckCircle size={16} class="shrink-0" />
                        <span>Reset link sent! Check your email.</span>
                    </div>
                {:else}
                    <Input
                        name="email"
                        type="email"
                        label="Email"
                        placeholder="you@example.com"
                        required
                        error={errors.email}
                    />

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
                            <span>Sending...</span>
                        {:else}
                            <span>Send reset link</span>
                        {/if}
                    </button>
                {/if}

                <p class="text-center text-sm">
                    <Link
                        href="/login"
                        class="font-medium text-brand hover:text-brand/80 dark:text-brand"
                    >
                        &larr; Back to login
                    </Link>
                </p>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
