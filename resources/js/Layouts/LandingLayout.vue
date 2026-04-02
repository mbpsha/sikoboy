<template>
  <div class="min-h-screen bg-slate-50 text-slate-800">
    <Header />

    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
</script>
