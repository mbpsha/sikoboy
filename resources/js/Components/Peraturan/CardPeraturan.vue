<script setup>
import { ref } from 'vue'

const props = defineProps({
    item: Object
})

const showPreview = ref(false)
</script>

<template>
    <article
        v-if="props.item"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-md hover:shadow-lg transition-shadow duration-300 min-w-0"
    >
        <!-- THUMBNAIL -->
        <div class="aspect-video bg-slate-100 overflow-hidden border-b border-slate-200 relative">
            <img
                v-if="props.item.thumbnail"
                :src="`/storage/${props.item.thumbnail}`"
                :alt="props.item.judul"
                class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
            />
            <div
                v-else
                class="flex h-full items-center justify-center flex-col gap-2"
            >
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span class="text-xs text-slate-400 font-medium">Tidak ada thumbnail</span>
            </div>

            <!-- Badge PDF -->
            <div class="absolute top-2 left-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold uppercase tracking-wide">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                    PDF
                </span>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="flex flex-col gap-3 p-4">
            <h3 class="text-sm font-semibold text-slate-800 leading-snug break-words line-clamp-2 min-w-0">
                {{ props.item.judul }}
            </h3>

            <p v-if="props.item.keterangan" class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                {{ props.item.keterangan }}
            </p>

            <!-- Aksi -->
            <div class="flex gap-2 mt-1">
                <button
                    @click="showPreview = true"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#0C505C] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#0a424b]"
                >
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Lihat Dokumen Praturan
                </button>
            </div>
        </div>
    </article>

    <!-- PDF PREVIEW MODAL -->
    <Teleport to="body">
        <div
            v-if="showPreview"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 py-8"
            @click.self="showPreview = false"
        >
            <div class="w-full max-w-4xl h-[88vh] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-slate-800 truncate">{{ props.item.judul }}</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Dokumen Peraturan</p>
                        </div>
                    </div>
                    <button
                        @click="showPreview = false"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition shrink-0 ml-3"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- PDF Viewer -->
                <div class="flex-1 bg-slate-100 overflow-hidden">
                    <iframe
                        :src="`/storage/${props.item.file}#toolbar=1&navpanes=0`"
                        class="w-full h-full border-0"
                        loading="lazy"
                    ></iframe>
                </div>

                <!-- Footer -->
                <div class="border-t border-slate-100 px-5 py-3 flex items-center justify-between shrink-0 bg-gray-50">
                    <p class="text-xs text-slate-400 truncate max-w-xs hidden sm:block">{{ props.item.judul }}</p>
                    <div class="flex gap-2 ml-auto">
                        <a
                            :href="`/storage/${props.item.file}`"
                            :download="props.item.judul || 'dokumen'"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-[#0C505C] hover:bg-[#0a424b] px-4 py-2 text-xs font-semibold text-white transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Unduh PDF
                        </a>
                        <button
                            @click="showPreview = false"
                            class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </Teleport>
</template>