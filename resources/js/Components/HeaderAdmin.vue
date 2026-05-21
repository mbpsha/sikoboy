<template>
    <header
        class="bg-white border-b border-gray-200 sticky top-0 z-30 w-full"
    >
        <div
            class="flex items-center justify-between gap-3 px-3 sm:px-4 md:px-6 py-3 md:py-4"
        >

            <!-- LEFT -->
            <div class="flex items-center gap-2 md:gap-3 min-w-0 flex-1">

                <!-- MOBILE HAMBURGER -->
                <button
                    @click="$emit('toggleSidebar')"
                    class="lg:hidden w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition shrink-0"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>

                <!-- DESKTOP COLLAPSE -->
                <button
                    @click="$emit('toggleCollapse')"
                    class="hidden lg:flex w-10 h-10 rounded-xl border border-gray-200 items-center justify-center hover:bg-gray-100 transition shrink-0"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-700 transition-transform duration-300"
                        :class="sidebarCollapsed ? 'rotate-180' : ''"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 5l-7 7 7 7"
                        />
                    </svg>
                </button>

                <!-- TITLE -->
                <div class="min-w-0">
                    <p
                        class="text-[11px] sm:text-xs md:text-sm text-gray-500 truncate"
                    >
                        Dashboard / {{ title }}
                    </p>

                    <h1
                        class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 leading-tight truncate"
                    >
                        {{ title }}
                    </h1>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-2 md:gap-4 shrink-0">

                <!-- NOTIFICATION -->
                <div
                    ref="notificationWrapper"
                    class="relative"
                >

                    <button
                        @click.stop="toggleNotifications"
                        class="relative w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"
                            />
                        </svg>

                        <span
                            v-if="totalNotifications > 0"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center"
                        >
                            {{ totalNotifications > 9 ? '9+' : totalNotifications }}
                        </span>
                    </button>

                    <!-- DROPDOWN -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="showNotifications"
                            class="absolute right-0 mt-3 w-[92vw] sm:w-96 max-w-sm bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
                        >

                            <!-- HEADER -->
                            <div
                                class="px-4 py-3 border-b border-gray-100 flex items-center justify-between"
                            >
                                <h3 class="font-semibold text-gray-800">
                                    Notifikasi
                                </h3>

                                <button
                                    @click="markAllAsRead"
                                    class="text-sm text-teal-600 hover:text-teal-700"
                                >
                                    Tandai Dibaca
                                </button>
                            </div>

                            <!-- CONTENT -->
                            <div class="max-h-[400px] overflow-y-auto">

                                <div
                                    v-for="notif in notifications"
                                    :key="notif.id"
                                    class="p-4 border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer"
                                    @click="handleNotificationClick(notif)"
                                >
                                    <div class="flex gap-3">

                                        <!-- ICON -->
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                            :class="notif.type === 'MITRA'
                                                ? 'bg-blue-100 text-blue-600'
                                                : 'bg-green-100 text-green-600'"
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
                                                    d="M9 12h6m-6 4h6"
                                                />
                                            </svg>
                                        </div>

                                        <!-- TEXT -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span
                                                    class="text-[10px] px-2 py-1 rounded-full font-bold"
                                                    :class="notif.type === 'MITRA'
                                                        ? 'bg-blue-100 text-blue-700'
                                                        : 'bg-green-100 text-green-700'"
                                                >
                                                    {{ notif.type }}
                                                </span>

                                                <span
                                                    class="w-2 h-2 rounded-full bg-red-500 shrink-0"
                                                ></span>
                                            </div>

                                            <p
                                                class="text-sm font-semibold text-gray-800 truncate mt-1"
                                            >
                                                {{ notif.title }}
                                            </p>

                                            <p
                                                class="text-xs text-gray-500 mt-1 line-clamp-2"
                                            >
                                                {{ notif.description }}
                                            </p>

                                            <p
                                                class="text-xs mt-2 font-semibold"
                                                :class="notif.status === 'expired'
                                                    ? 'text-red-500'
                                                    : 'text-yellow-600'"
                                            >
                                                {{ notif.countdown }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- EMPTY -->
                                <div
                                    v-if="notifications.length === 0"
                                    class="p-8 text-center text-sm text-gray-500"
                                >
                                    Tidak ada notifikasi
                                </div>
                            </div>

                            <!-- FOOTER -->
                            <div
                                class="p-3 border-t border-gray-100 text-center"
                            >
                                <Link
                                    href="/admin/notifikasi"
                                    class="text-sm text-teal-600 font-semibold hover:text-teal-700"
                                >
                                    Lihat Semua →
                                </Link>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- PROFILE -->
                <div
                    class="flex items-center gap-2 md:gap-3 pl-1 md:pl-2 min-w-0"
                >
                    <div
                        class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-semibold shrink-0"
                    >
                        {{ initial }}
                    </div>

                    <div class="hidden sm:block min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate max-w-[120px] md:max-w-[180px]">
                            {{ displayName }}
                        </p>

                        <p class="text-xs text-gray-500 truncate">
                            {{ roleLabel }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import {
    computed,
    ref,
    onMounted,
    onUnmounted,
} from "vue";

import {
    usePage,
    Link,
    router,
} from "@inertiajs/vue3";

defineProps({
    title: String,
    sidebarCollapsed: Boolean,
});

defineEmits([
    "toggleSidebar",
    "toggleCollapse",
]);

const page = usePage();

const showNotifications = ref(false);

const notificationWrapper = ref(null);

const rawAdminNotifications = computed(
    () => page.props.admin_notifications ?? []
);

const closedAdminNotifications = ref([]);

const notifications = computed(() => {
    return rawAdminNotifications.value.filter(
        n => !closedAdminNotifications.value.includes(n.id)
    );
});

const authUser = computed(
    () => page.props.auth?.user ?? null
);

const displayName = computed(() => {
    if (!authUser.value) return "";

    return (
        authUser.value.username ||
        authUser.value.email?.split("@")[0] ||
        ""
    );
});

const roleLabel = computed(() => {
    return (
        authUser.value?.admin?.divisi ||
        authUser.value?.divisi ||
        "Administrator"
    );
});

const initial = computed(() => {
    return displayName.value?.charAt(0).toUpperCase() || "";
});

const totalNotifications = computed(
    () => notifications.value.length
);

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
};

const handleNotificationClick = (notif) => {

    if (!closedAdminNotifications.value.includes(notif.id)) {

        closedAdminNotifications.value.push(notif.id);

        try {
            localStorage.setItem(
                "closed_admin_notifications",
                JSON.stringify(closedAdminNotifications.value)
            );
        } catch (e) {}
    }

    router.visit(`/admin/notifikasi?highlight=${notif.id}`);
};

const markAllAsRead = () => {

    const ids = rawAdminNotifications.value.map(n => n.id);

    closedAdminNotifications.value = Array.from(
        new Set([
            ...closedAdminNotifications.value,
            ...ids
        ])
    );

    try {
        localStorage.setItem(
            "closed_admin_notifications",
            JSON.stringify(closedAdminNotifications.value)
        );
    } catch (e) {}

    showNotifications.value = false;
};

const handleClickOutside = (event) => {

    if (
        notificationWrapper.value &&
        !notificationWrapper.value.contains(event.target)
    ) {
        showNotifications.value = false;
    }
};

onMounted(() => {

    document.addEventListener(
        "click",
        handleClickOutside
    );

    try {

        const stored = localStorage.getItem(
            "closed_admin_notifications"
        );

        if (stored) {
            closedAdminNotifications.value = JSON.parse(stored);
        }

    } catch (e) {
        closedAdminNotifications.value = [];
    }
});

onUnmounted(() => {

    document.removeEventListener(
        "click",
        handleClickOutside
    );
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
