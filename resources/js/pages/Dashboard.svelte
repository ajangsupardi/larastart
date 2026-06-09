<script lang="ts">
    import DashboardLayout from '@/layouts/DashboardLayout.svelte';

    let {
        stats = {} as Record<
            string,
            {
                label: string;
                value: string | number;
                trend?: number;
                icon?: string;
            }
        >,
    } = $props();

    let cards = $derived(Object.values(stats));
</script>

<DashboardLayout
    title="Dashboard"
    description="Welcome back! Here's your overview."
    breadcrumbs={[{ label: 'Home', href: '/' }, { label: 'Dashboard' }]}
    {cards}
>
    <div
        class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900"
    >
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Welcome
        </h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Use the navigation sidebar to manage users and roles. Your dashboard
            stats are shown above.
        </p>
    </div>
</DashboardLayout>
