<script lang="ts">
    import { Link, Form } from '@inertiajs/svelte';
    import { LoaderCircle, Mail } from '@lucide/svelte';
    import AuthLayout from '@/layouts/AuthLayout.svelte';
    import { cn } from '@/lib/utils';
</script>

<AuthLayout title="Verify Email">
    <Form action="/email/verification-notification" method="post">
        {#snippet children({ processing, wasSuccessful })}
            <div class="space-y-6 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand/10 dark:bg-brand/10"
                >
                    <Mail size={32} class="text-brand dark:text-brand" />
                </div>

                <div>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Verify your email
                    </h2>
                    <p
                        class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                    >
                        We've sent a verification link to your email address.<br
                        />
                        Click the link to activate your account.
                    </p>
                </div>

                {#if wasSuccessful}
                    <div
                        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-950 dark:text-green-300"
                    >
                        A new verification link has been sent!
                    </div>
                {/if}

                <button
                    type="submit"
                    disabled={processing}
                    class={cn(
                        'flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition-all',
                        processing
                            ? 'bg-brand/40 cursor-not-allowed'
                            : 'bg-brand hover:bg-brand/90 active:scale-[0.98]',
                    )}
                >
                    {#if processing}
                        <LoaderCircle size={16} class="animate-spin" />
                        <span>Sending...</span>
                    {:else}
                        <span>Resend verification email</span>
                    {/if}
                </button>

                <div class="pt-2">
                    <Link
                        href="/logout"
                        method="post"
                        class="text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Logout
                    </Link>
                </div>
            </div>
        {/snippet}
    </Form>
</AuthLayout>
