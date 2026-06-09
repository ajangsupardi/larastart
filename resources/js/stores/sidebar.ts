import { writable } from 'svelte/store';

function isMobile() {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.innerWidth < 1024;
}

export const sidebarOpen = writable(!isMobile());
export const sidebarCollapsed = writable(false);

export function toggleSidebar() {
    sidebarOpen.update((v) => !v);
}

export function toggleSidebarCollapsed() {
    sidebarCollapsed.update((v) => !v);
}
