<script setup>
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import UserProfile from '@/Components/Profile/UserProfile.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    mitra: {
        type: Object,
        default: null,
    },
    mode: {
        type: String,
        default: 'edit',
    },
});
</script>

<template>
    <Head title="Profil Mitra" />

    <LandingLayout>
        <UserProfile :user="props.user" :mitra="props.mitra" :mode="props.mode" />
    </LandingLayout>
</template>
