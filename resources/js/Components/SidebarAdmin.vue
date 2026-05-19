<template>
    <aside
        class="fixed top-0 left-0 z-50 min-h-screen h-full overflow-y-auto flex flex-col text-white transition-all duration-300"
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
        <!-- TOP -->
        <div class="flex flex-col min-h-screen h-full">

            <!-- HEADER -->
            <div class="px-4 pt-4 pb-4 border-b border-teal-700 shrink-0">

                <!-- MOBILE HEADER -->
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

                <!-- LOGO -->
                <div
                    class="transition-all duration-300"
                    :class="isCollapsedDesktop ? 'flex justify-center' : ''"
                >
                    <div v-if="!props.isCollapsed || isMobile">
                        <div class="text-3xl font-extrabold tracking-widest">
                            SIKOBOY
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

            <!-- MENU -->
            <div class="flex-1 px-3 py-4 space-y-2">

                <!-- BERANDA -->
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
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9.5z"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Beranda
                    </span>
                </Link>

                <!-- PENGGUNA -->
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
                        stroke-width="1.8"
                    >
                        <circle cx="9" cy="8" r="3"/>
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 20c0-3 3-5 5-5s5 2 5 5"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Pengguna
                    </span>
                </Link>

                <!-- AJUAN -->
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
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14 3v5h5"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Ajuan Kerjasama
                    </span>
                </Link>

                <!-- RIWAYAT -->
                <div>

                    <!-- EXPANDED -->
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
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"/>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 7v5l3 2"
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
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 9l6 6 6-6"
                            />
                        </svg>
                    </button>

                    <!-- COLLAPSED -->
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
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"/>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 7v5l3 2"
                            />
                        </svg>
                    </Link>

                    <!-- SUBMENU -->
                    <div
                        v-if="showRiwayatMenu && (!props.isCollapsed || isMobile)"
                        class="mt-2 ml-5 space-y-1 border-l border-teal-700/50 pl-3"
                    >
                        <Link
                            :href="route('admin.riwayat-kerjasama.gabungan')"
                            :class="subNavClass('/admin/riwayat-kerjasama/gabungan')"
                            @click="handleMobileClose"
                        >
                            Semua Kerjasama
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.pemerintah')"
                            :class="subNavClass('/admin/riwayat-kerjasama/pemerintah')"
                            @click="handleMobileClose"
                        >
                            Pemrakarsa Boyolali
                        </Link>

                        <Link
                            :href="route('admin.riwayat-kerjasama.mitra')"
                            :class="subNavClass('/admin/riwayat-kerjasama/mitra')"
                            @click="handleMobileClose"
                        >
                            Pemrakarsa Mitra
                        </Link>
                    </div>
                </div>

                <!-- MANAJEMEN POTENSI -->
                <Link
                    :href="route('admin.manajemen-potensi.index')"
                    :class="navClass('/admin/manajemen-potensi')"
                    @click="handleMobileClose"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Manajemen Potensi
                    </span>
                </Link>

                <!-- MANAJEMEN DOKUMEN -->
                <Link
                    :href="route('admin.manajemen-dokumen.index')"
                    :class="navClass('/admin/manajemen-dokumen')"
                    @click="handleMobileClose"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
                        />
                    </svg>

                    <span
                        v-if="!props.isCollapsed || isMobile"
                        class="text-sm"
                    >
                        Manajemen Dokumen
                    </span>
                </Link>

                <!-- MANAJEMEN PERATURAN -->
                <Link
                    :href="route('admin.manajemen-peraturan.index')"
                    :class="navClass('/admin/manajemen-peraturan')"
                    @click="handleMobileClose"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
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

            <!-- BOTTOM -->
            <div class="p-3 border-t border-teal-700 shrink-0 space-y-2 mt-auto">

                <!-- PROFILE -->
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

                <!-- LOGOUT -->
                <button
                    @click="showConfirm = true"
                    class="bg-red-600 hover:bg-red-700 transition rounded-2xl font-semibold text-sm"
                    :class="(isCollapsedDesktop)
                        ? 'w-14 h-14 mx-auto flex items-center justify-center'
                        : 'w-full p-3'"
                >
                    <span v-if="!isCollapsedDesktop || isMobile">
                        Logout
                    </span>

                    <svg
                        v-else-if="!isMobile"
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- MODAL -->
        <div
            v-if="showConfirm"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60]"
        >
            <div class="bg-white rounded-2xl p-6 w-80 text-center shadow-xl">
                <h2 class="text-base font-semibold text-gray-800 mb-2">
                    Yakin ingin logout?
                </h2>

                <p class="text-sm text-gray-500 mb-5">
                    Sesi Anda akan diakhiri.
                </p>

                <div class="flex justify-center gap-3">
                    <button
                        @click="showConfirm = false"
                        class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm"
                    >
                        Batal
                    </button>

                    <button
                        @click="logout"
                        class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm"
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
