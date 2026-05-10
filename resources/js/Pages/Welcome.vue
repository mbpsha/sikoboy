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

const potentials = [
    {
        title: 'Lahan Subur dan Produktif',
        description: 'Mendukung kolaborasi sektor pertanian, peternakan, dan pengolahan hasil daerah.',
    },
    {
        title: 'Ketersediaan Bahan Baku Stabil',
        description: 'Pasokan yang konsisten menjadi fondasi kuat untuk kerja sama industri berkelanjutan.',
    },
    {
        title: 'Dukungan Pemerintah Daerah',
        description: 'Komitmen regulasi dan fasilitasi mendorong iklim investasi yang lebih sehat.',
    },
    {
        title: 'Potensi Pengembangan Agroteknologi',
        description: 'Terbuka ruang kolaborasi inovasi untuk peningkatan produktivitas dan kualitas hasil.',
    },
];
</script>

<template>
    <LandingLayout>
        <HeroSection
            title="SIKOBOY"
            subtitle="Mewujudkan pengelolaan kerja sama daerah yang efisien, transparan, dan berdampak untuk Kabupaten Boyolali."
        />

        <StatsSection :stats="stats" />
        <AboutSection />
        <PotentialSection :potensiData="potensiData" />

        <!-- Image gallery: place your image files into public/images/ -->
        <ImageGallery :images="landingImages" />
    </LandingLayout>
</template>
