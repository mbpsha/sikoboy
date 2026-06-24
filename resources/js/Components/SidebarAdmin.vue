<template>
    <aside
        class="fixed top-0 left-0 z-50 h-screen flex flex-col text-white transition-all duration-300"
        :class="[
            isMobile
                ? (
                    props.isOpen
                        ? 'translate-x-0 w-64'
                        : '-translate-x-full w-64'
                )
                : (
                    props.isCollapsed
                        ? 'w-[88px]'
                        : 'w-[280px]'
                )
        ]"
        style="background-color: #0c505c"
    >
        <div class="flex flex-col h-full overflow-hidden">

            <div class="px-4 pt-4 pb-4 border-b border-teal-700 shrink-0">

                <div class="flex items-center justify-between mb-3 lg:hidden">
                    <h2 class="font-semibold text-lg">
                        Menu
                    </h2>

                    <button
                        @click="emit('close')"
                        class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center transition"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div
                    class="transition-all duration-300"
                    :class="isCollapsedDesktop ? 'flex justify-center' : ''"
                >
                    <div v-if="!props.isCollapsed || isMobile">
                        <div class="text-3xl font-extrabold tracking-widest">
                            SIKARSA
                        </div>

                        <p class="text-xs text-teal-100 mt-1">
                            Admin Dashboard
                        </p>
                    </div>

                    <div
                        v-else-if="!isMobile"
                        class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl font-bold"
                    >
                        S
                    </div>
                </div>
            </div>

            <div class="flex-1 px-3 py-4 space-y-2 overflow-y-auto custom-scrollbar">

                <Link
                    :href="route('admin.dashboard')"
                    :class="navClass('/admin/dashboard')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Beranda
                    </span>
                </Link>

                <Link
                    :href="route('admin.pengguna.index')"
                    :class="navClass('/admin/pengguna')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Pengguna
                    </span>
                </Link>

                <Link
                    :href="route('admin.data-kerjasama.index')"
                    :class="navClass('/admin/data-kerjasama')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Ajuan Kerjasama
                    </span>
                </Link>

                <div>

                    <button
                        v-if="!props.isCollapsed || isMobile"
                        type="button"
                        @click="showRiwayatMenu = !showRiwayatMenu"
                        :class="navClass('/admin/riwayat-kerjasama')"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>

                        <span
                            class="text-sm flex-1 text-left"
                        >
                            Riwayat Kerjasama
                        </span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform"
                            :class="showRiwayatMenu ? 'rotate-180' : ''"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <Link
                        v-else-if="!isMobile"
                        :href="route('admin.riwayat-kerjasama.gabungan')"
                        :class="navClass('/admin/riwayat-kerjasama')"
                        @click="handleMobileClose"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </Link>

                    <div
                        v-if="showRiwayatMenu && (!props.isCollapsed || isMobile)"
                        class="mt-2 ml-5 space-y-1 border-l border-teal-700/50 pl-3"
                    >
                        <Link
                            :href="route('admin.riwayat-kerjasama.gabungan')"
                            :class="subNavClass('/admin/riwayat-kerjasama/gabungan')"
                            @click="handleMobileClose"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-2 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                />
                            </svg>
                            Semua Kerjasama
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.pemerintah')"
                            :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')"
                            @click="handleMobileClose"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-2 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                            Pemrakarsa Boyolali
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.mitra')"
                            :class="subNavClass('/admin/riwayat-kerjasama/mitra')"
                            @click="handleMobileClose"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-2 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                            Pemrakarsa Mitra
                        </Link>
                    </div>
                </div>

                <Link
                    :href="route('admin.manajemen-potensi.index')"
                    :class="navClass('/admin/manajemen-potensi')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Manajemen Potensi
                    </span>
                </Link>

                <Link
                    :href="route('admin.manajemen-dokumen.index')"
                    :class="navClass('/admin/manajemen-dokumen')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Manajemen Dokumen
                    </span>
                </Link>

                <Link
                    :href="route('admin.manajemen-peraturan.index')"
                    :class="navClass('/admin/manajemen-peraturan')"
                    @click="handleMobileClose"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14 2v6h6"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Manajemen Peraturan
                    </span>
                </Link>
            </div>

            <div class="p-3 border-t border-teal-700 shrink-0 space-y-2 mt-auto">

                <Link
                    :href="route('admin.profile.show')"
                    @click="handleMobileClose"
                    :class="[
                        (isCollapsedDesktop)
                            ? 'flex justify-center items-center p-2 rounded-2xl hover:bg-white/10 transition'
                            : 'flex items-center gap-3 px-3 py-2.5 rounded-2xl hover:bg-white/10 transition'
                    ]"
                >
                    <div
                        class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold shrink-0"
                    >
                        {{ adminInitial }}
                    </div>

                    <div
                        v-if="!props.isCollapsed || isMobile"
                        class="flex-1 min-w-0"
                    >
                        <p class="text-sm font-semibold truncate">
                            {{ adminName }}
                        </p>

                        <p class="text-xs text-teal-300">
                            Lihat Profil
                        </p>
                    </div>
                </Link>

                <button
                    @click="showConfirm = true"
                    class="bg-red-600 hover:bg-red-700 transition rounded-2xl font-semibold text-sm"
                    :class="(isCollapsedDesktop)
                        ? 'w-14 h-14 mx-auto flex items-center justify-center'
                        : 'w-full p-3 flex items-center justify-center gap-2'"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>

                    <span v-if="!isCollapsedDesktop || isMobile">
                        Logout
                    </span>
                </button>
            </div>
        </div>

        <div
            v-if="showConfirm"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4"
        >
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm text-center shadow-2xl">

                <div class="mx-auto w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-7 h-7 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>

                <h2 class="text-lg font-bold text-gray-800 mb-2">
                    Yakin ingin logout?
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Sesi Anda akan diakhiri dan Anda harus login kembali.
                </p>

                <div class="flex gap-3">
                    <button
                        @click="showConfirm = false"
                        class="flex-1 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition"
                    >
                        Batal
                    </button>

                    <button
                        @click="logout"
                        class="flex-1 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition"
                    >
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted } from "vue";

const props = defineProps({
    isOpen: Boolean,
    isCollapsed: Boolean,
});

const emit = defineEmits(["close"]);

const page = usePage();

const showConfirm = ref(false);

const showRiwayatMenu = ref(
    page.url?.startsWith("/admin/riwayat-kerjasama") ?? false
);

const isMobile = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024;
};

onMounted(() => {
    checkMobile();
    window.addEventListener("resize", checkMobile);
});

onUnmounted(() => {
    window.removeEventListener("resize", checkMobile);
});

watch(
    () => page.url,
    (url) => {
        if (url?.startsWith("/admin/riwayat-kerjasama")) {
            showRiwayatMenu.value = true;
        }
    }
);

const adminName = computed(() => {
    const auth = page.props.auth?.user;

    return auth?.username ||
        auth?.email?.split("@")[0] ||
        "Admin";
});

const adminInitial = computed(() => {
    return adminName.value.charAt(0).toUpperCase();
});

const navClass = (url) => {

    const collapsedDesktop =
        props.isCollapsed && !isMobile.value;

    const base = collapsedDesktop
        ? "flex items-center justify-center w-14 h-14 mx-auto rounded-2xl transition"
        : "flex items-center gap-3 px-4 py-3 rounded-2xl transition w-full";

    if (page.url && page.url.startsWith(url)) {
        return base + " bg-white/30 border border-white/20";
    }

    return base + " text-white/80 hover:bg-teal-700/25 hover:text-white";
};

const subNavClass = (url) => {

    const base =
        "flex items-center px-3 py-2 rounded-xl transition w-full text-sm";

    if (page.url && page.url.startsWith(url)) {
        return base + " bg-white text-teal-900 font-semibold";
    }

    return base + " text-white/80 hover:bg-teal-700/25 hover:text-white";
};

const handleMobileClose = () => {

    if (window.innerWidth < 1024) {
        emit("close");
    }
};

const isCollapsedDesktop =
    computed(() =>
        props.isCollapsed && !isMobile.value
    );

const logout = () => {

    router.post(
        route("logout"),
        {},
        {
            onSuccess: () => {
                window.location.href = "/";
            },
        }
    );
};
</script>
