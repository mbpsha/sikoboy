<template>
    <div>
        <div
            v-if="isMobile && isMobileOpen"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"
            @click="$emit('close-mobile')"
        ></div>

        <aside
            :class="asideClass"
            style="background-color: #0c505c"
        >
            <div class="overflow-y-auto flex-1">
                <div :class="['pt-6 pb-4', showLabel ? 'px-6' : 'px-3 text-center']">
                    <div class="text-2xl font-extrabold tracking-widest">SIKOBOY</div>
                    <p v-if="showLabel" class="text-xs text-teal-100 mt-1">Admin Dashboard</p>
                    <div class="mt-4 border-t border-teal-700"></div>
                </div>

                <nav :class="['mt-2 space-y-1', showLabel ? 'px-3' : 'px-2']">
                    <Link
                        :href="route('admin.dashboard')"
                        :class="navClass('/admin/dashboard')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Beranda</span>
                    </Link>

                    <Link
                        :href="route('admin.pengguna.index')"
                        :class="navClass('/admin/pengguna')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="9" cy="8" r="3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-3 3-5 5-5s5 2 5 5"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Pengguna</span>
                    </Link>

                    <Link
                        :href="route('admin.data-kerjasama.index')"
                        :class="navClass('/admin/data-kerjasama')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Ajuan Kerjasama</span>
                    </Link>

                    <div>
                        <button
                            type="button"
                            @click="toggleRiwayatMenu"
                            :class="navClass('/admin/riwayat-kerjasama')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                            </svg>
                            <span v-if="showLabel" class="text-sm flex-1 text-left">Riwayat Kerjasama</span>
                            <svg
                                v-if="showLabel"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 transition-transform"
                                :class="showRiwayatMenu ? 'rotate-180' : ''"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>

                        <div
                            v-if="showRiwayatMenu && showLabel"
                            class="mt-1 ml-5 space-y-1 border-l border-teal-700/50 pl-3"
                        >
                            <Link
                                :href="route('admin.riwayat-kerjasama.gabungan')"
                                :class="subNavClass('/admin/riwayat-kerjasama/gabungan')"
                                @click="handleNavigation"
                            >
                                <span class="text-sm">Semua Kerjasama</span>
                            </Link>
                            <Link
                                :href="route('admin.riwayat-kerjasama.pemerintah')"
                                :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')"
                                @click="handleNavigation"
                            >
                                <span class="text-sm">Pemrakarsa Boyolali</span>
                            </Link>
                            <Link
                                :href="route('admin.riwayat-kerjasama.mitra')"
                                :class="subNavClass('/admin/riwayat-kerjasama/mitra')"
                                @click="handleNavigation"
                            >
                                <span class="text-sm">Pemrakarsa Mitra</span>
                            </Link>
                        </div>
                    </div>

                    <Link
                        :href="route('admin.manajemen-potensi.index')"
                        :class="navClass('/admin/manajemen-potensi')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <circle cx="9" cy="6" r="2"/>
                            <circle cx="15" cy="12" r="2"/>
                            <circle cx="11" cy="18" r="2"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Manajemen Potensi</span>
                    </Link>

                    <Link
                        :href="route('admin.manajemen-dokumen.index')"
                        :class="navClass('/admin/manajemen-dokumen')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6M9 17h6"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Manajemen Dokumen</span>
                    </Link>

                    <Link
                        :href="route('admin.manajemen-peraturan.index')"
                        :class="navClass('/admin/manajemen-peraturan')"
                        @click="handleNavigation"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                        </svg>
                        <span v-if="showLabel" class="text-sm">Manajemen Peraturan</span>
                    </Link>
                </nav>
            </div>

            <div :class="['p-4 space-y-2', showLabel ? '' : 'px-2']">
                <Link
                    :href="route('admin.profile.show')"
                    class="flex items-center px-3 py-2.5 rounded-xl transition w-full group border"
                    :class="[
                        page.url?.startsWith('/admin/profile')
                            ? 'bg-white/15 border-white/30'
                            : 'border-transparent hover:bg-white/10 hover:border-white/10',
                        showLabel ? 'gap-3' : 'justify-center',
                    ]"
                    @click="handleNavigation"
                >
                    <div
                        class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition"
                        :class="page.url?.startsWith('/admin/profile')
                            ? 'bg-white text-teal-800'
                            : 'bg-white/20 text-white group-hover:bg-white/30'"
                    >
                        {{ adminInitial }}
                    </div>
                    <div v-if="showLabel" class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate leading-tight">{{ adminName }}</p>
                        <p class="text-xs text-teal-300 leading-tight">Lihat Profil</p>
                    </div>
                    <svg
                        v-if="showLabel"
                        class="w-4 h-4 text-white/40 shrink-0 group-hover:text-white/70 transition"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>

                <button
                    @click="showConfirm = true"
                    class="w-full transition p-2.5 rounded-xl font-semibold text-sm bg-red-600 hover:bg-red-700"
                    :class="showLabel ? '' : 'flex items-center justify-center'"
                    :aria-label="showLabel ? 'Logout' : 'Logout admin'"
                >
                    <span v-if="showLabel">Logout</span>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                    </svg>
                </button>
            </div>

            <div v-if="showConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 w-80 text-center shadow-xl">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-800 mb-1">Yakin ingin logout?</h2>
                    <p class="text-sm text-gray-500 mb-5">Sesi Anda akan diakhiri.</p>
                    <div class="flex justify-center gap-3">
                        <button @click="showConfirm = false" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                            Batal
                        </button>
                        <button @click="logout" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";

const props = defineProps({
    isMobile: {
        type: Boolean,
        default: false,
    },
    isDesktopCollapsed: {
        type: Boolean,
        default: false,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close-mobile", "request-expand"]);

const page = usePage();
const showConfirm = ref(false);
const showRiwayatMenu = ref(
    page.url?.startsWith("/admin/riwayat-kerjasama") ?? false,
);

watch(
    () => page.url,
    (url) => {
        if (url?.startsWith("/admin/riwayat-kerjasama")) {
            showRiwayatMenu.value = true;
        }
    },
);

const showLabel = computed(() => props.isMobile || !props.isDesktopCollapsed);

const asideClass = computed(() => {
    const base =
        "text-white h-screen fixed left-0 top-0 z-40 flex flex-col justify-between overflow-hidden transition-all duration-300 ease-in-out";

    if (props.isMobile) {
        return `${base} w-64 ${props.isMobileOpen ? "translate-x-0" : "-translate-x-full"}`;
    }

    return `${base} ${props.isDesktopCollapsed ? "w-20" : "w-64"}`;
});

const adminName = computed(() => {
    const auth = page.props.auth?.user;
    return auth?.username || auth?.email?.split("@")[0] || "Admin";
});

const adminInitial = computed(() => {
    return adminName.value.charAt(0).toUpperCase();
});

const navClass = (url) => {
    const alignment = showLabel.value ? "gap-3 px-3" : "justify-center px-0";
    const base = `flex items-center ${alignment} py-2 rounded-full transition w-full`;

    if (page.url && page.url.startsWith(url)) {
        return `${base} bg-teal-100 text-teal-900 font-semibold`;
    }

    return `${base} text-white/90 hover:bg-teal-700/30 hover:text-white`;
};

const subNavClass = (url) => {
    const base = "flex items-center gap-3 px-3 py-2 rounded-lg transition w-full text-left";
    if (page.url && page.url.startsWith(url)) {
        return `${base} bg-white text-teal-900 font-semibold`;
    }
    return `${base} text-white/80 hover:bg-teal-700/25 hover:text-white`;
};

const toggleRiwayatMenu = () => {
    if (!showLabel.value) {
        emit("request-expand");
        return;
    }

    showRiwayatMenu.value = !showRiwayatMenu.value;
};

const handleNavigation = () => {
    if (props.isMobile) {
        emit("close-mobile");
    }
};

const logout = () => {
    router.post(route("logout"), {}, { onSuccess: () => (window.location.href = "/") });
};
</script>
