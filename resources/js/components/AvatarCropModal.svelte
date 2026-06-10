<script lang="ts">
    import { X, Crop, LoaderCircle } from '@lucide/svelte';
    import { fade, fly } from 'svelte/transition';
    import { useForm } from '@inertiajs/svelte';
    import Cropper from 'cropperjs';

    type AvatarCropModalProps = {
        open: boolean;
        imageSrc: string;
        onclose: () => void;
        onsuccess: () => void;
        oncropsave?: (file: File) => void;
    };

    let { open = false, imageSrc = '', onclose, onsuccess, oncropsave }: AvatarCropModalProps = $props();

    let imageEl: HTMLImageElement | undefined = $state();
    let containerEl: HTMLDivElement | undefined = $state();
    let cropper: Cropper | null = $state(null);

    const form = useForm({
        avatar: null as File | null,
    });

    let isDragging = $state(false);

    $effect(() => {
        const el = containerEl;
        if (!el) return;

        function onStart() { isDragging = true; }
        function onEnd() { isDragging = false; }

        el.addEventListener('actionstart', onStart);
        el.addEventListener('actionend', onEnd);

        return () => {
            el.removeEventListener('actionstart', onStart);
            el.removeEventListener('actionend', onEnd);
        };
    });

    $effect(() => {
        const el = containerEl;
        if (!el) return;

        const handler: EventListener = (e) => {
            if (isDragging) {
                e.stopPropagation();
            }
        };

        el.addEventListener('wheel', handler, { capture: true } as AddEventListenerOptions);

        return () => el.removeEventListener('wheel', handler, { capture: true } as AddEventListenerOptions);
    });

    $effect(() => {
        if (open && imageSrc && imageEl && containerEl) {
            const c = new Cropper(imageEl, {
                container: containerEl,
            });
            cropper = c;

            const image = c.getCropperImage();
            if (image) {
                image.$ready().then(() => {
                    image.initialCenterSize = 'cover';

                    const selections = c.getCropperSelections();
                    if (selections && selections.length > 0) {
                        const sel = selections[0];
                        sel.aspectRatio = 1;
                        sel.initialCoverage = 0.8;
                    }
                });
            }
        }

        return () => {
            cropper?.destroy();
            cropper = null;
        };
    });

    function handleCancel() {
        cropper?.destroy();
        cropper = null;
        onclose();
    }

    async function handleSave() {
        if (!cropper) return;

        const selections = cropper.getCropperSelections();
        if (!selections || selections.length === 0) return;

        const selection = selections[0];
        const canvas = await selection.$toCanvas({ width: 400, height: 400 });

        canvas.toBlob((blob) => {
            if (!blob) return;

            const file = new File([blob], 'avatar.webp', { type: 'image/webp' });

            if (oncropsave) {
                cropper?.destroy();
                cropper = null;
                oncropsave(file);
                onsuccess();
                return;
            }

            form.avatar = file;
            form.post('/profile/avatar', {
                onSuccess: () => {
                    cropper?.destroy();
                    cropper = null;
                    onsuccess();
                },
                onError: () => form.clearErrors(),
                preserveScroll: true,
            });
        }, 'image/webp', 0.9);
    }
</script>

{#if open}
    <div
        role="presentation"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        onclick={handleCancel}
        onkeydown={(e) => {
            if (e.key === 'Escape') {
                handleCancel();
            }
        }}
        transition:fade={{ duration: 150 }}
    >
        <div
            role="dialog"
            aria-modal="true"
            aria-label="Crop Avatar"
            tabindex="-1"
            class="flex w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white shadow-xl dark:bg-gray-900"
            onclick={(e) => e.stopPropagation()}
            onkeydown={(e) => e.stopPropagation()}
            transition:fly={{ y: 20, duration: 200 }}
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand/10">
                        <Crop size={20} class="text-brand" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Crop Avatar
                    </h3>
                </div>
                <button
                    onclick={handleCancel}
                    class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <X size={18} />
                </button>
            </div>

            <div bind:this={containerEl} class="flex bg-gray-950 h-72 max-h-[50vh] overflow-hidden overscroll-behavior-[none] *:flex-1">
                <img
                    bind:this={imageEl}
                    src={imageSrc}
                    alt="Avatar to crop"
                />
            </div>

            <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                {#if form.errors.avatar}
                    <p class="mb-3 text-xs text-red-500 dark:text-red-400">
                        {form.errors.avatar}
                    </p>
                {/if}
                <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                    Drag to reposition. Scroll to zoom (pause dragging first). Saved as 400×400.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button
                        onclick={handleCancel}
                        disabled={form.processing}
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        onclick={handleSave}
                        disabled={form.processing}
                        class="inline-flex items-center gap-2 rounded-lg bg-brand px-6 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-brand/90 disabled:cursor-not-allowed disabled:bg-brand/40 active:scale-[0.98]"
                    >
                        {#if form.processing}
                            <LoaderCircle size={16} class="animate-spin" />
                            Saving...
                        {:else}
                            <Crop size={16} />
                            Save
                        {/if}
                    </button>
                </div>
            </div>
        </div>
    </div>
{/if}
