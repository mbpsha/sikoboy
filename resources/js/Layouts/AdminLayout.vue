<template>
    <div class="min-h-screen bg-gray-100">
        <SidebarAdmin
            :is-mobile="isMobile"
            :is-desktop-collapsed="isDesktopCollapsed"
            :is-mobile-open="isMobileSidebarOpen"
            @close-mobile="closeMobileSidebar"
            @request-expand="isDesktopCollapsed = false"
        />

        <div :class="contentClass">
            <HeaderAdmin
                :title="title"
                :is-mobile="isMobile"
                :is-sidebar-collapsed="isDesktopCollapsed"
                @toggle-sidebar="handleSidebarToggle"
            />

            <div class="p-4 pt-20 sm:p-6 sm:pt-20">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});

const DESKTOP_BREAKPOINT = 1024;

const isMobile = ref(false);
const isDesktopCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);

const syncViewport = () => {
    if (typeof window === "undefined") {
        return;
    }

    const mobile = window.innerWidth < DESKTOP_BREAKPOINT;
    isMobile.value = mobile;

    if (!mobile) {
        isMobileSidebarOpen.value = false;
    }
};

const handleSidebarToggle = () => {
    if (isMobile.value) {
        isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
        return;
    }

    isDesktopCollapsed.value = !isDesktopCollapsed.value;
};

const closeMobileSidebar = () => {
    isMobileSidebarOpen.value = false;
};

const contentClass = computed(() => {
    const base = "min-h-screen transition-all duration-300";

    if (isMobile.value) {
        return base;
    }

    return `${base} ${isDesktopCollapsed.value ? "lg:ml-20" : "lg:ml-64"}`;
});

onMounted(() => {
    syncViewport();
    window.addEventListener("resize", syncViewport);
});

onUnmounted(() => {
    window.removeEventListener("resize", syncViewport);
});
</script>
