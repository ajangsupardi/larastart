<script lang="ts">
    import { Link, usePage } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import FlashMessages from '@/components/FlashMessages.svelte';

    const page = usePage();
    let { status } = $props<{ status: number }>();

    const titles: Record<number, string> = {
        503: 'Service Unavailable',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Forbidden',
    };

    const descriptions: Record<number, string> = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    };
</script>

<AppHead title="{status} {titles[status] || 'Error'}" />
<FlashMessages />

<div
    class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 dark:bg-gray-950"
>
    <div class="w-full max-w-md text-center">
        <Link href="/" class="mb-8 inline-block">
            <img
                src="/brand/brand-light.svg"
                alt={page.props.name}
                class="mx-auto h-9 block dark:hidden"
            />
            <img
                src="/brand/brand-dark.svg"
                alt={page.props.name}
                class="mx-auto h-9 hidden dark:block"
            />
        </Link>

        <div
            class="rounded-xl border border-gray-200 bg-white p-8 shadow-lg shadow-gray-200/50 dark:border-gray-800 dark:bg-gray-950 dark:shadow-gray-950/50"
        >
            <p class="text-6xl font-bold text-gray-900 dark:text-gray-100">
                {status}
            </p>

            <h1
                class="mt-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
            >
                {titles[status] || 'Error'}
            </h1>

            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                {descriptions[status] || 'An unexpected error occurred.'}
            </p>

            <div class="mt-8">
                <Link
                    href="/"
                    class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand/90 active:scale-[0.98]"
                >
                    Back to Home
                </Link>
            </div>
        </div>

        <p class="mt-8 text-xs text-gray-400 dark:text-gray-500">
            &copy; {new Date().getFullYear()}
            {page.props.name}
        </p>
    </div>
</div>
