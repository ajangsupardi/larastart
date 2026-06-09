<script lang="ts">
    import { Link, usePage } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import FlashMessages from '@/components/FlashMessages.svelte';

    const page = usePage();
    let { title = '', children } = $props();
</script>

<AppHead {title} />
<FlashMessages />

<!-- Mobile layout (< lg) -->
<div
    class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 dark:bg-gray-900 lg:hidden"
>
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <Link href="/">
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
        </div>

        <div
            class="rounded-xl border border-gray-200 bg-white p-8 shadow-lg shadow-gray-200/50 dark:border-gray-800 dark:bg-gray-950 dark:shadow-gray-950/50"
        >
            {@render children?.()}
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                &copy; {new Date().getFullYear()}
                {page.props.name}
            </p>
        </div>
    </div>
</div>

<!-- Desktop layout (lg+) -->
<div class="hidden min-h-screen lg:flex">
    <!-- Left panel: brand -->
    <div
        class="flex w-[480px] flex-col items-center justify-between bg-brand p-12 dark:bg-gray-950 dark:bg-none"
    >
        <Link href="/">
            <img
                src="/brand/brand-dark.svg"
                alt={page.props.name}
                class="h-9"
            />
        </Link>

        <div class="max-w-sm">
            <p class="text-lg font-medium leading-relaxed text-white/90">
                "The best way to get started is to quit talking and begin
                doing."
            </p>
            <p class="mt-4 text-sm text-white/70 dark:text-white/60">
                — Walt Disney
            </p>
        </div>

        <p class="text-sm text-white/70 dark:text-white/60">
            &copy; {new Date().getFullYear()}
            {page.props.name}
        </p>
    </div>

    <!-- Right panel: form -->
    <div
        class="flex flex-1 flex-col items-center justify-center bg-white dark:bg-gray-900"
    >
        <div class="w-full max-w-sm px-8">
            {@render children?.()}
        </div>
    </div>
</div>
