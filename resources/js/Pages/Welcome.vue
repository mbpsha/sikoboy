<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import StatsSection from '@/Components/Landing/StatsSection.vue';
import AboutSection from '@/Components/Landing/AboutSection.vue';
import PotentialSection from '@/Components/Landing/PotentialSection.vue';
import landingImages from '@/images';

const stats = [
    { label: 'Jumlah Kerja Sama', value: '147' },
    { label: 'Masa Berlaku <6 Bulan', value: '11' },
    { label: 'Masa Berlaku <3 Bulan', value: '13' },
    { label: 'Masa Berlaku Habis', value: '37' },
];

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
        <PotentialSection :items="potentials" />

        <!-- Image gallery: place your image files into public/images/ -->
        <ImageGallery :images="landingImages" />
    </LandingLayout>
</template>
