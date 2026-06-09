import { writable } from 'svelte/store';

export type SearchResult = {
    label: string;
    description?: string;
    href: string;
    type: 'user' | 'role' | 'page';
    icon?: string;
};

const staticItems: SearchResult[] = [
    {
        label: 'Dashboard',
        href: '/dashboard',
        type: 'page',
        icon: 'LayoutDashboard',
    },
    { label: 'Users', href: '/users', type: 'page', icon: 'Users' },
    { label: 'Roles', href: '/roles', type: 'page', icon: 'ShieldCheck' },
    { label: 'Edit Profile', href: '/profile', type: 'page', icon: 'User' },
];

export function getFilteredStaticItems(
    permissions: Record<string, string[]>,
): SearchResult[] {
    const canViewUsers = permissions.users?.length > 0;
    const canViewRoles = permissions.roles?.length > 0;

    return staticItems.filter((item) => {
        if (item.href === '/users') {
            return canViewUsers;
        }

        if (item.href === '/roles') {
            return canViewRoles;
        }

        return true;
    });
}

export const searchOpen = writable(false);
export const searchQuery = writable('');
export const searchResults = writable<SearchResult[]>([]);
export const searchLoading = writable(false);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

searchQuery.subscribe((query) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    if (!query || query.length < 2) {
        searchResults.set([]);
        searchLoading.set(false);

        return;
    }

    searchLoading.set(true);

    debounceTimer = setTimeout(async () => {
        try {
            const response = await fetch(
                `/search?q=${encodeURIComponent(query)}`,
            );
            const data = await response.json();
            searchResults.set(data.results ?? []);
        } catch {
            searchResults.set([]);
        } finally {
            searchLoading.set(false);
        }
    }, 300);
});

export function openSearch(initialResults?: SearchResult[]) {
    searchQuery.set('');
    searchResults.set(initialResults ?? []);
    searchOpen.set(true);
}

export function closeSearch() {
    searchOpen.set(false);
}
