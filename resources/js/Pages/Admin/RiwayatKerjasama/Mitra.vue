<script setup>
import { onBeforeUnmount, ref, watch } from "vue";
import { router, Link } from "@inertiajs/vue3";
import {
    MagnifyingGlassIcon,
    FunnelIcon,
    DocumentTextIcon,
} from "@heroicons/vue/24/outline";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    data: Object,
    filters: Object,
    years: Array,
});

const search = ref(props.filters?.search || "");
const tahun = ref(props.filters?.tahun || "");

const filter = () => {
    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
        },
        { preserveState: true },
    );
};

let debounceTimer = null;

watch([search, tahun], () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        filter();
    }, 400);
});

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});

const goToPage = (page) => {
    if (!page || page === props.data?.current_page) return;

    router.get(
        route("admin.riwayat-kerjasama.mitra"),
        {
            search: search.value,
            tahun: tahun.value,
            page,
        },
        { preserveState: true, preserveScroll: true },
    );
};
</script>

<template>
    <AdminLayout title="Riwayat Kerjasama - Mitra">
        <div class="p-6">
            <div class="max-w-7xl mx-auto">
                <!-- SEARCH -->
                <div
                    class="bg-[#ffffff] p-4 rounded-2xl shadow-sm border-gray-300 flex gap-3 items-center overflow-x-auto"
                >
                    <div
                        class="flex items-center gap-2 bg-white rounded-xl px-4 py-2 w-full border"
                    >
                        <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                        <input
                            v-model="search"
                            placeholder="Cari Berdasarkan Mitra atau Nama Kerjasama..."
                            class="w-full outline-none text-sm"
                        />
                    </div>

                    <div class="bg-white rounded-xl p-1">
                        <FunnelIcon class="w-5 h-5 text-gray-500" />
                    </div>

                    <select
                        v-model="tahun"
                        class="bg-white border rounded-xl px-4 py-2 text-sm"
                    >
                        <option value="">Semua Tahun</option>
                        <option v-for="y in years" :key="y" :value="y">
                            {{ y }}
                        </option>
                    </select>
                </div>

                <!-- TAB -->
                <div class="flex justify-between items-center mt-6">
                    <div
                        class="bg-white border-gray-300 rounded-xl p-1 flex gap-1 shadow-sm"
                    >
                        <Link
                            :href="route('admin.riwayat-kerjasama.pemerintah')"
                            class="px-4 py-2 rounded-lg text-sm text-gray-600"
                        >
                            Pemrakarsa Boyolali
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.mitra')"
                            class="px-4 py-2 rounded-lg text-sm bg-teal-700 text-white"
                        >
                            Pemrakarsa Mitra
                        </Link>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="mt-4 bg-white rounded-2xl shadow overflow-hidden">
                    <div class="p-6 overflow-x-auto">
                        <table class="min-w-full table-auto text-sm">
                            <thead
                                class="bg-teal-700 text-white border-b border-gray-200"
                            >
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Tahun
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Mitra
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left border-r border-gray-200"
                                    >
                                        Judul
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Mulai
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Berakhir
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        Jangka
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap border-r border-gray-200"
                                    >
                                        File
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left whitespace-nowrap"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="item in data?.data || []"
                                    :key="item.no"
                                    class="border-b border-gray-200 align-middle"
                                >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.no }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.tahun }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.mitra }}
                                    </td>
                                    <td
                                        class="px-4 py-3 border-r border-gray-200"
                                    >
                                        {{ item.judul || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.mulai || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap border-r border-gray-200"
                                    >
                                        {{ item.berakhir || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap min-w-[120px] border-r border-gray-200"
                                    >
                                        {{ item.jangka_waktu || "-" }}
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap min-w-[90px] border-r border-gray-200"
                                    >
                                        <div
                                            v-if="item.file_url"
                                            class="flex items-center gap-1 text-teal-700"
                                        >
                                            <a
                                                :href="item.file_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 hover:underline text-xs"
                                            >
                                                <DocumentTextIcon
                                                    class="w-4 h-4"
                                                />
                                                lihat
                                            </a>
                                        </div>
                                        <span
                                            v-else
                                            class="text-gray-400 text-xs"
                                            >-</span
                                        >
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap min-w-[140px]"
                                    >
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs leading-none"
                                            :class="{
                                                'bg-green-100 text-green-700':
                                                    item.status === 'Aktif',
                                                'bg-red-100 text-red-600':
                                                    item.status === 'Berakhir',
                                                'bg-yellow-100 text-yellow-700':
                                                    item.status ===
                                                    'Segera Berakhir',
                                            }"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="(data?.last_page || 1) > 1"
                    class="mt-4 flex items-center justify-end gap-2"
                >
                    <button
                        class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                        :disabled="!data?.prev_page_url"
                        @click="goToPage(data.current_page - 1)"
                    >
                        Sebelumnya
                    </button>

                    <button
                        v-for="page in data.last_page"
                        :key="page"
                        class="px-3 py-2 text-sm rounded-lg border"
                        :class="
                            page === data.current_page
                                ? 'bg-teal-600 text-white border-teal-600'
                                : 'bg-white text-gray-700'
                        "
                        @click="goToPage(page)"
                    >
                        {{ page }}
                    </button>

                    <button
                        class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
                        :disabled="!data?.next_page_url"
                        @click="goToPage(data.current_page + 1)"
                    >
                        Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
