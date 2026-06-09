<script lang="ts">
    import { usePage } from '@inertiajs/svelte';

    const page = usePage();
    let { year = new Date().getFullYear() } = $props();
</script>

<footer
    class="flex h-16 items-center border-t border-gray-200 bg-white transition-colors duration-150 dark:border-gray-800 dark:bg-gray-900"
>
    <div
        class="flex w-full flex-col items-center justify-between gap-1 px-4 py-3 text-xs text-gray-500 sm:flex-row sm:px-6 sm:text-sm dark:text-gray-400"
    >
        <span>&copy; {year} {page.props.name}. All rights reserved.</span>
        <span>Built with Laravel v13</span>
    </div>
</footer>
