<script lang="ts">
    import { Eye, EyeOff } from '@lucide/svelte';
    import type { Component } from 'svelte';
    import { cn } from '@/lib/utils';

    let {
        label = '',
        name = '',
        type = 'text',
        placeholder = '',
        error = '',
        required = false,
        value = '',
        icon: Icon = undefined,
        oninput,
    }: {
        label?: string;
        name: string;
        type?: string;
        placeholder?: string;
        error?: string;
        required?: boolean;
        value?: string;
        icon?: Component;
        oninput?: (e: Event) => void;
    } = $props();

    let showPassword = $state(false);
    let inputEl: HTMLInputElement | undefined = $state();

    let resolvedType = $derived(
        type === 'password' && showPassword ? 'text' : type,
    );
</script>

<div>
    {#if label}
        <label
            for={name}
            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
        >
            {label}
            {#if required}
                <span class="text-red-500">*</span>
            {/if}
        </label>
    {/if}
    <div
        class={cn(
            'flex items-center gap-2 rounded-lg border bg-gray-50 pl-4 pr-1.5 text-sm transition-all',
            'border-gray-200 dark:border-gray-700 dark:bg-gray-900',
            error ? 'border-red-300 dark:border-red-700' : '',
        )}
    >
        {#if Icon}
            <Icon size={16} class="shrink-0 text-gray-400" />
        {/if}
        <input
            bind:this={inputEl}
            {name}
            type={resolvedType}
            {placeholder}
            {required}
            {value}
            {oninput}
            id={name}
            autocomplete={name === 'email'
                ? 'email'
                : name === 'password'
                  ? 'current-password'
                  : name === 'password_confirmation'
                    ? 'new-password'
                    : name === 'name'
                      ? 'name'
                      : 'off'}
            class={cn(
                'w-full border-0 bg-transparent py-1.5 text-sm outline-none',
                'placeholder:text-gray-400 dark:text-gray-100',
                Icon ? 'pl-0' : '',
            )}
        />
        {#if type === 'password'}
            <button
                type="button"
                onclick={() => {
                    showPassword = !showPassword;
                    inputEl?.focus();
                }}
                class="shrink-0 rounded p-0.5 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"
                tabindex="-1"
            >
                {#if showPassword}
                    <EyeOff size={16} />
                {:else}
                    <Eye size={16} />
                {/if}
            </button>
        {/if}
    </div>
    {#if error}
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{error}</p>
    {/if}
</div>
