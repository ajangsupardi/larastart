<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppHead from '@/components/AppHead.svelte';
    import FlashMessages from '@/components/FlashMessages.svelte';
    import { sidebarOpen } from '@/stores/sidebar';
    import Footer from './parts/Footer.svelte';
    import GlobalSearch from './parts/GlobalSearch.svelte';
    import PageHeader from './parts/PageHeader.svelte';
    import Sidebar from './parts/Sidebar.svelte';
    import SummaryCards from './parts/SummaryCards.svelte';
    import TopNavbar from './parts/TopNavbar.svelte';

    type Breadcrumb = { label: string; href?: string };
    type Action = {
        label: string;
        href?: string;
        onclick?: () => void;
        variant?: 'primary' | 'secondary';
    };
    type Card = {
        label: string;
        value: string | number;
        trend?: number;
        icon?: string;
    };

    let {
        title = '',
        description = '',
        breadcrumbs = [] as Breadcrumb[],
        actions = [] as Action[],
        cards = [] as Card[],
        children,
    }: {
        title?: string;
        description?: string;
        breadcrumbs?: Breadcrumb[];
        actions?: Action[];
        cards?: Card[];
        children?: Snippet;
    } = $props();
</script>

<AppHead {title} />
<FlashMessages />

<div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
    {#if $sidebarOpen}
        <Sidebar />
    {/if}

    <div class="flex flex-1 flex-col overflow-hidden">
        <TopNavbar />

        <main class="flex flex-1 flex-col overflow-y-auto">
            <!-- Page header area -->
            {#if title || breadcrumbs.length > 0 || actions.length > 0 || cards.length > 0}
                <div
                    class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        <PageHeader
                            {title}
                            {description}
                            {breadcrumbs}
                            {actions}
                        />
                        <SummaryCards {cards} />
                    </div>
                </div>
            {/if}

            <!-- Main content area -->
            <div class="flex-1 dark:bg-gray-900">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {@render children?.()}
                </div>
            </div>
        </main>

        <Footer />
    </div>
</div>

<GlobalSearch />
