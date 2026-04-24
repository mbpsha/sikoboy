<template>
    <aside
        class="bg-teal-900 text-white w-full h-full flex flex-col justify-between"
    >
        <!-- TOP -->
        <div>
            <!-- LOGO -->
            <div class="p-6 text-2xl font-bold tracking-widest">
                SIKOBOY
                <p class="text-sm font-normal mt-1">Admin Dashboard</p>
            </div>

            <!-- MENU -->
            <nav class="mt-6 space-y-2 px-4">
                <Link
                    :href="route('admin.dashboard')"
                    :class="navClass('/admin/dashboard')"
                >
                    Beranda
                </Link>

                <Link href="#" :class="navClass('/admin/pengguna')">
                    Pengguna
                </Link>

                <Link href="#" :class="navClass('/admin/kerjasama')">
                    Data Kerjasama
                </Link>

                <Link
                    :href="route('admin.riwayat-kerjasama.pemerintah')"
                    :class="navClass('/admin/riwayat-kerjasama')"
                >
                    Riwayat Kerjasama
                </Link>

                <Link href="#" :class="navClass('/admin/potensi')">
                    Manajemen Potensi
                </Link>

                <Link href="#" :class="navClass('/admin/dokumen')">
                    Manajemen Dokumen
                </Link>
            </nav>
        </div>

        <!-- BOTTOM (LOGOUT) -->
        <div class="p-4">
            <button
                @click="showConfirm = true"
                class="w-full bg-red-600 hover:bg-red-700 transition p-3 rounded-lg font-semibold"
            >
                Logout
            </button>
        </div>

        <!-- MODAL KONFIRMASI -->
        <div
            v-if="showConfirm"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-xl p-6 w-80 text-center">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    Yakin ingin logout?
                </h2>

                <div class="flex justify-center gap-4">
                    <button
                        @click="showConfirm = false"
                        class="px-4 py-2 bg-gray-600 rounded-lg"
                    >
                        Batal
                    </button>

                    <button
                        @click="logout"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";

const page = usePage();
const showConfirm = ref(false);

const navClass = (url) => {
    return [
        "flex items-center gap-3 p-3 rounded-lg transition",
        page.url.startsWith(url)
            ? "bg-teal-700 text-white"
            : "hover:bg-teal-700",
    ];
};

// LOGOUT FUNCTION
const logout = () => {
    router.post(
        route("logout"),
        {},
        {
            onSuccess: () => (window.location.href = "/"),
        },
    );
};
</script>
