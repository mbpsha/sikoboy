<template>
    <aside
        style="background-color: #0c505c"
        class="text-white h-screen fixed left-0 top-0 z-40 flex flex-col justify-between overflow-hidden transition-all duration-300"
        :class="isCollapsed ? 'w-20' : 'w-64'"
    >
        <!-- TOP -->
        <div class="overflow-y-auto flex-1">
            <!-- LOGO -->
            <div :class="isCollapsed ? 'px-2 pt-4 pb-3' : 'px-6 pt-6 pb-4'">
                <div class="flex items-center" :class="isCollapsed ? 'justify-center' : 'justify-between'">
                    <div v-if="!isCollapsed">
                        <div class="text-3xl font-extrabold tracking-widest">
                            SIKOBOY
                        </div>
                        <p class="text-xs text-teal-100 mt-1">Admin Dashboard</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-lg p-2 hover:bg-teal-700/40 transition"
                        @click="isCollapsed = !isCollapsed"
                        :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 transition-transform"
                            :class="isCollapsed ? 'rotate-180' : ''"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11 5l-7 7 7 7M20 5v14"
                            />
                        </svg>
                    </button>
                </div>
                <div class="mt-4 border-t border-teal-700"></div>
            </div>

            <!-- MENU -->
            <nav class="mt-2 space-y-1" :class="isCollapsed ? 'px-2' : 'px-3'">
                <Link :href="route('admin.dashboard')" :class="navClass('/admin/dashboard')" aria-label="Beranda">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Beranda</span>
                </Link>

                <Link :href="route('admin.pengguna.index')" :class="navClass('/admin/pengguna')" aria-label="Pengguna">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c0-3 3-5 5-5s5 2 5 5"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Pengguna</span>
                </Link>

                <Link :href="route('admin.data-kerjasama.index')" :class="navClass('/admin/data-kerjasama')" aria-label="Ajuan Kerjasama">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Ajuan Kerjasama</span>
                </Link>

                <template v-if="isCollapsed">
                    <div ref="collapsedRiwayatMenuRef" class="relative">
                        <button
                            type="button"
                            :class="navClass('/admin/riwayat-kerjasama')"
                            aria-label="Riwayat Kerjasama"
                            @click="showCollapsedRiwayatMenu = !showCollapsedRiwayatMenu"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                            </svg>
                        </button>
                        <div
                            v-if="showCollapsedRiwayatMenu"
                            class="absolute left-full top-0 ml-2 w-52 rounded-xl border border-teal-700/40 bg-teal-900 shadow-xl p-2 space-y-1 z-50"
                        >
                            <Link :href="route('admin.riwayat-kerjasama.gabungan')" :class="subNavClass('/admin/riwayat-kerjasama/gabungan')">
                                <span class="text-sm">Semua Kerjasama</span>
                            </Link>
                            <Link :href="route('admin.riwayat-kerjasama.pemerintah')" :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')">
                                <span class="text-sm">Pemrakarsa Boyolali</span>
                            </Link>
                            <Link :href="route('admin.riwayat-kerjasama.mitra')" :class="subNavClass('/admin/riwayat-kerjasama/mitra')">
                                <span class="text-sm">Pemrakarsa Mitra</span>
                            </Link>
                        </div>
                    </div>
                </template>
                <div v-else>
                    <button
                        type="button"
                        @click="showRiwayatMenu = !showRiwayatMenu"
                        :class="navClass('/admin/riwayat-kerjasama')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                        </svg>
                        <span class="text-sm flex-1 text-left">Riwayat Kerjasama</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform"
                            :class="showRiwayatMenu ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    <div v-if="showRiwayatMenu" class="mt-1 ml-5 space-y-1 border-l border-teal-700/50 pl-3">
                        <Link :href="route('admin.riwayat-kerjasama.gabungan')" :class="subNavClass('/admin/riwayat-kerjasama/gabungan')">
                            <span class="text-sm">Semua Kerjasama</span>
                        </Link>
                        <Link :href="route('admin.riwayat-kerjasama.pemerintah')" :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')">
                            <span class="text-sm">Pemrakarsa Boyolali</span>
                        </Link>
                        <Link :href="route('admin.riwayat-kerjasama.mitra')" :class="subNavClass('/admin/riwayat-kerjasama/mitra')">
                            <span class="text-sm">Pemrakarsa Mitra</span>
                        </Link>
                    </div>
                </div>

                <Link :href="route('admin.manajemen-potensi.index')" :class="navClass('/admin/manajemen-potensi')" aria-label="Manajemen Potensi">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <circle cx="9" cy="6" r="2"/>
                        <circle cx="15" cy="12" r="2"/>
                        <circle cx="11" cy="18" r="2"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Manajemen Potensi</span>
                </Link>

                <Link :href="route('admin.manajemen-dokumen.index')" :class="navClass('/admin/manajemen-dokumen')" aria-label="Manajemen Dokumen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v6M9 17h6"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Manajemen Dokumen</span>
                </Link>

                <Link :href="route('admin.manajemen-peraturan.index')" :class="navClass('/admin/manajemen-peraturan')" aria-label="Manajemen Peraturan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5"/>
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm">Manajemen Peraturan</span>
                </Link>
            </nav>
        </div>

        <!-- BOTTOM -->
        <div class="p-4 space-y-2">
            <Link
                :href="route('admin.profile.show')"
                class="flex items-center rounded-xl transition w-full group border"
                aria-label="Profil Admin"
                :class="[
                    page.url?.startsWith('/admin/profile')
                        ? 'bg-white/15 border-white/30'
                        : 'border-transparent hover:bg-white/10 hover:border-white/10',
                    isCollapsed ? 'justify-center px-2 py-2.5' : 'gap-3 px-3 py-2.5'
                ]"
            >
                <div
                    class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition"
                    :class="page.url?.startsWith('/admin/profile')
                        ? 'bg-white text-teal-800'
                        : 'bg-white/20 text-white group-hover:bg-white/30'"
                >
                    {{ adminInitial }}
                </div>
                <div v-if="!isCollapsed" class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate leading-tight">{{ adminName }}</p>
                    <p class="text-xs text-teal-300 leading-tight">Lihat Profil</p>
                </div>
                <svg v-if="!isCollapsed" class="w-4 h-4 text-white/40 shrink-0 group-hover:text-white/70 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </Link>

            <button
                @click="showConfirm = true"
                class="w-full bg-red-600 hover:bg-red-700 transition rounded-xl font-semibold text-sm flex items-center justify-center p-2.5"
                aria-label="Logout"
                :title="isCollapsed ? 'Logout' : ''"
            >
                <svg v-if="isCollapsed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                <span v-else>Logout</span>
            </button>
        </div>

        <!-- MODAL KONFIRMASI LOGOUT -->
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
</template>

<script setup>
import { Link, usePage, router } from "@inertiajs/vue3"
import { ref, watch, computed, onMounted, onUnmounted } from "vue"

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false,
    },
})
const emit = defineEmits(["update:collapsed"])

const page          = usePage()
const showConfirm   = ref(false)
const showRiwayatMenu = ref(
    page.url?.startsWith("/admin/riwayat-kerjasama") ?? false
)
const showCollapsedRiwayatMenu = ref(false)
const collapsedRiwayatMenuRef = ref(null)
const isCollapsed = computed({
    get: () => props.collapsed,
    set: (value) => emit("update:collapsed", value),
})

const closeCollapsedMenu = () => {
    showCollapsedRiwayatMenu.value = false
}

watch(
    () => page.url,
    (url) => {
        closeCollapsedMenu()
        if (url?.startsWith("/admin/riwayat-kerjasama")) {
            showRiwayatMenu.value = true
        }
    }
)

watch(isCollapsed, (collapsed) => {
    closeCollapsedMenu()
    if (collapsed) {
        showRiwayatMenu.value = false
    } else {
        showRiwayatMenu.value = page.url?.startsWith("/admin/riwayat-kerjasama") ?? false
    }
})

const handleDocumentClick = (event) => {
    if (!showCollapsedRiwayatMenu.value) {
        return
    }

    if (collapsedRiwayatMenuRef.value && !collapsedRiwayatMenuRef.value.contains(event.target)) {
        closeCollapsedMenu()
    }
}

const handleEscapeKey = (event) => {
    if (event.key === "Escape") {
        closeCollapsedMenu()
    }
}

onMounted(() => {
    document.addEventListener("click", handleDocumentClick)
    document.addEventListener("keydown", handleEscapeKey)
})

onUnmounted(() => {
    document.removeEventListener("click", handleDocumentClick)
    document.removeEventListener("keydown", handleEscapeKey)
})

// ✅ Nama dan inisial admin dari shared auth props
const adminName = computed(() => {
    const auth = page.props.auth?.user
    return auth?.username || auth?.email?.split('@')[0] || 'Admin'
})

const adminInitial = computed(() => {
    return adminName.value.charAt(0).toUpperCase()
})

const navClass = (url) => {
    const base = isCollapsed.value
        ? "flex items-center justify-center px-0 py-2 rounded-full transition w-full"
        : "flex items-center gap-3 px-3 py-2 rounded-full transition w-full"
    if (page.url && page.url.startsWith(url)) {
        return base + " bg-teal-100 text-teal-900 font-semibold"
    }
    return base + " text-white/90 hover:bg-teal-700/30 hover:text-white"
}

const subNavClass = (url) => {
    const base = "flex items-center gap-3 px-3 py-2 rounded-lg transition w-full text-left"
    if (page.url && page.url.startsWith(url)) {
        return base + " bg-white text-teal-900 font-semibold"
    }
    return base + " text-white/80 hover:bg-teal-700/25 hover:text-white"
}

const logout = () => {
    router.post(
        route("logout"),
        {},
        { onSuccess: () => (window.location.href = "/") }
    )
}
</script>
