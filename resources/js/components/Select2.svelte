<script lang="ts">
    import Select from 'svelte-select';

    interface Props {
        value?: string | number;
        items: { value: string | number; label: string }[];
        placeholder?: string;
        onchange?: (value: string) => void;
    }

    let {
        value = '',
        items = [],
        placeholder = 'Select...',
        onchange,
    }: Props = $props();

    function handleChange(event: CustomEvent) {
        const selected = event.detail;
        onchange?.(selected?.value ?? '');
    }

    function handleClear() {
        onchange?.('');
    }
</script>

<Select
    {items}
    {placeholder}
    value={items.find((item) => item.value === value) ?? null}
    clearable={true}
    searchable={true}
    showChevron={true}
    on:change={handleChange}
    on:clear={handleClear}
/>

<style>
    :global(.svelte-select) {
        font-size: 0.875rem;
        min-height: 2.5rem;
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        background-color: #ffffff;
    }

    :global(.svelte-select input) {
        font-size: 0.875rem;
        color: #111827;
    }

    :global(.svelte-select .placeholder) {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    :global(.svelte-select .selected) {
        color: #111827;
        font-size: 0.875rem;
    }

    :global(.svelte-select .chevron) {
        color: #6b7280;
    }

    :global(.svelte-select .clear) {
        color: #9ca3af;
    }

    :global(.svelte-select-list) {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        background-color: #ffffff;
        z-index: 9999;
    }

    :global(.svelte-select-list .item) {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #111827;
        background-color: transparent;
    }

    :global(.svelte-select-list .item:hover),
    :global(.svelte-select-list .item.active) {
        background-color: #f3f4f6;
        color: #111827;
    }

    :global(.svelte-select-list .item.active) {
        background-color: #ff2d20;
        color: white;
    }

    :global(.svelte-select-item) {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #111827;
        background-color: transparent;
    }

    :global(.svelte-select-item:hover) {
        background-color: #f3f4f6;
    }

    :global(.svelte-select-item.active) {
        background-color: #ff2d20;
        color: white;
    }

    :global(.svelte-select-input) {
        color: #111827;
    }

    :global(.svelte-select .svelte-select-input input) {
        color: #111827;
    }

    /* Dark mode */
    :global(.dark .svelte-select) {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
    }

    :global(.dark .svelte-select input) {
        color: #f3f4f6 !important;
    }

    :global(.dark .svelte-select .placeholder) {
        color: #9ca3af !important;
    }

    :global(.dark .svelte-select .selected) {
        color: #f3f4f6 !important;
    }

    :global(.dark .svelte-select .chevron) {
        color: #9ca3af !important;
    }

    :global(.dark .svelte-select .clear) {
        color: #9ca3af !important;
    }

    :global(.dark .svelte-select-list) {
        background-color: #1f2937 !important;
        border-color: #4b5563 !important;
    }

    :global(.dark .svelte-select-list .item) {
        color: #f3f4f6 !important;
        background-color: transparent !important;
    }

    :global(.dark .svelte-select-list .item:hover) {
        background-color: #374151 !important;
    }

    :global(.dark .svelte-select-list .item.active) {
        background-color: #ff2d20 !important;
        color: white !important;
    }

    :global(.dark .svelte-select-item) {
        color: #f3f4f6 !important;
        background-color: transparent !important;
    }

    :global(.dark .svelte-select-item:hover) {
        background-color: #374151 !important;
    }

    :global(.dark .svelte-select-item.active) {
        background-color: #ff2d20 !important;
        color: white !important;
    }

    :global(.dark .svelte-select-input) {
        color: #f3f4f6 !important;
    }

    :global(.dark .svelte-select .svelte-select-input input) {
        color: #f3f4f6 !important;
    }
</style>
