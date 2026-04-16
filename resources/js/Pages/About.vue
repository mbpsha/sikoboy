<script setup>
import LandingLayout from "@/Layouts/LandingLayout.vue";

import HeroTentang from "@/Components/Tentang/HeroTentang.vue";
import AboutSikoboy from "@/Components/Tentang/AboutSikoboy.vue";
import VisiSection from "@/Components/Tentang/VisiSection.vue";
import MisiSection from "@/Components/Tentang/MisiSection.vue";
</script>

<template>
    <LandingLayout>
        <HeroTentang />
        <AboutSikoboy />
        <VisiSection />
        <MisiSection />
    </LandingLayout>
</template>
