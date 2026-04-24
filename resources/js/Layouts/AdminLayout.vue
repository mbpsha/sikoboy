<template>
    <div>
        <!-- Sidebar (fixed) -->
        <SidebarAdmin />

        <!-- Content Area (account for fixed sidebar width) -->
        <div class="ml-64 bg-gray-100 min-h-screen">
            <!-- Header -->
            <HeaderAdmin :title="title" />

            <!-- Slot Content -->
            <div class="p-6 pt-20">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import SidebarAdmin from "@/Components/SidebarAdmin.vue";
import HeaderAdmin from "@/Components/HeaderAdmin.vue";

defineProps({
    title: String,
});
</script>
