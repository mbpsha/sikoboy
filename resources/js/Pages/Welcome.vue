<script setup>
import { defineProps } from 'vue'
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import StatsSection from '@/Components/Landing/StatsSection.vue';
import AboutSection from '@/Components/Landing/AboutSection.vue';
import PotentialSection from '@/Components/Landing/PotentialSection.vue';
import landingImages from '@/images';

defineProps({
    potensiData: {
        type: Object,
        default: () => ({})
    },
    stats: {
        type: Array,
        default: () => [
            { label: 'Jumlah Kerja Sama', value: '0' },
            { label: 'Masa Berlaku <6 Bulan', value: '0' },
            { label: 'Masa Berlaku <3 Bulan', value: '0' },
            { label: 'Masa Berlaku Habis', value: '0' },
        ]
    }
});

</script>

<template>
    <LandingLayout>
        <HeroSection
            title="S I K E R S A"
            subtitle="Mewujudkan pengelolaan kerja sama daerah yang efisien, transparan, dan berdampak untuk Kabupaten Boyolali."
        />

        <StatsSection :stats="stats" />
        <AboutSection />
        <PotentialSection :potensiData="potensiData" />

        <!-- Image gallery: place your image files into public/images/ -->
        <ImageGallery :images="landingImages" />
    </LandingLayout>
</template>
