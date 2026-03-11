<script setup>
import { Link } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <FlashMessages />
        <slot />
    </div>
</template>
