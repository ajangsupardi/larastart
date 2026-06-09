import { writable, get } from 'svelte/store';

type ThemeMode = 'light' | 'dark' | 'system';

function getInitialTheme(): ThemeMode {
    if (typeof window === 'undefined') {
        return 'system';
    }

    const stored = localStorage.getItem('theme') as ThemeMode | null;

    if (stored && ['light', 'dark', 'system'].includes(stored)) {
        return stored;
    }

    return 'system';
}

function applyTheme(mode: ThemeMode) {
    if (typeof window === 'undefined') {
        return;
    }

    const isDark =
        mode === 'dark' ||
        (mode === 'system' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches);

    document.documentElement.classList.toggle('dark', isDark);
}

export const themeMode = writable<ThemeMode>(getInitialTheme());

// Apply on change, persist to localStorage
themeMode.subscribe((mode) => {
    if (typeof window === 'undefined') {
        return;
    }

    localStorage.setItem('theme', mode);
    applyTheme(mode);
});

// React to OS scheme changes when in 'system' mode
if (typeof window !== 'undefined') {
    window
        .matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', () => {
            const mode = get(themeMode);

            if (mode === 'system') {
                applyTheme('system');
            }
        });
}
