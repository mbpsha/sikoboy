<script setup>
import { ref } from 'vue'

const props = defineProps({
    item: Object
})

const showPreview = ref(false)

const openPreview = () => {
    showPreview.value = true
}

const closePreview = () => {
    showPreview.value = false
}
</script>

<template>
    <article
        v-if="props.item"
        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-md"
    >
        <!-- THUMBNAIL -->
        <div class="aspect-2/1 bg-slate-100 overflow-hidden border-b border-slate-300">
            <img
                v-if="props.item.thumbnail"
                :src="`/storage/${props.item.thumbnail}`"
                :alt="props.item.judul"
                class="h-full w-full object-cover"
            />

            <div
                v-else
                class="flex h-full items-center justify-center px-6 text-center text-sm font-medium text-slate-400"
            >
                Tidak ada thumbnail
            </div>
        </div>

        <!-- CONTENT -->
        <div class="flex flex-col gap-4 p-4">
            <h3 class="text-base font-semibold text-slate-800 line-clamp-2">
                {{ props.item.judul }}
            </h3>

            <button
                @click="openPreview"
                class="w-full inline-flex items-center justify-center rounded-lg bg-[#0C505C] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0a424b]"
            >
                Lihat Dokumen Peraturan
            </button>
        </div>
    </article>

    <!-- PDF PREVIEW MODAL -->
    <div
        v-if="showPreview"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8"
    >
        <div class="w-full max-w-5xl h-[80vh] bg-white rounded-2xl shadow-2xl flex flex-col">
            <!-- HEADER -->
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-800 truncate">
                    {{ props.item.judul }}
                </h3>
                <button
                    @click="closePreview"
                    class="rounded-full bg-slate-100 px-3 py-1 text-xl font-semibold text-slate-600 transition hover:bg-slate-200"
                >
                    ✕
                </button>
            </div>

            <!-- PDF VIEWER -->
            <iframe
                :src="`/storage/${props.item.file}`"
                class="flex-1 border-0"
            ></iframe>

            <!-- FOOTER -->
            <div class="border-t border-slate-200 px-6 py-3 flex justify-end gap-3">
                <a
                    :href="`/storage/${props.item.file}`"
                    target="_blank"
                    class="inline-flex items-center rounded-full bg-teal-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-700"
                >
                    Unduh PDF
                </a>
                <button
                    @click="closePreview"
                    class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
