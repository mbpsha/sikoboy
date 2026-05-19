<template>
    <div>
        <!-- Sidebar (fixed) -->
        <SidebarAdmin v-model:collapsed="isSidebarCollapsed" />

        <!-- Content Area (account for fixed sidebar width) -->
        <div
            class="bg-gray-100 min-h-screen transition-all duration-300"
            :class="isSidebarCollapsed ? 'ml-20' : 'ml-64'"
        >
            <!-- Header -->
            <HeaderAdmin :title="title" />

            <div class="p-4 pt-20 sm:p-6 sm:pt-20 space-y-4 sm:space-y-6">
                <h1
                    v-if="title"
                    class="text-xl sm:text-2xl font-bold text-teal-700 leading-tight"
                >
                    {{ title }}
                </h1>
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});

const isSidebarCollapsed = ref(false);
const SIDEBAR_STORAGE_KEY = "admin_sidebar_collapsed";
const SIDEBAR_COLLAPSED_VALUE = "1";

onMounted(() => {
    try {
        isSidebarCollapsed.value = localStorage.getItem(SIDEBAR_STORAGE_KEY) === SIDEBAR_COLLAPSED_VALUE;
    } catch (error) {
        isSidebarCollapsed.value = false;
        if (import.meta.env.DEV) {
            console.error("Failed to load sidebar state:", error);
        }
    }
});

watch(isSidebarCollapsed, (collapsed) => {
    try {
        localStorage.setItem(SIDEBAR_STORAGE_KEY, collapsed ? SIDEBAR_COLLAPSED_VALUE : "0");
    } catch (error) {
        if (import.meta.env.DEV) {
            console.error("Failed to store sidebar state:", error);
        }
    }
});
</script>
