<script setup>
import Component from '@/Components/Mitra/Pengajuan/Step2.vue'
const props = defineProps({ step1Data: Object, kategoris: Array, jenisDokumen: Array, mitra: Object })
</script>

<template>
  <Component v-bind="props" />
</template>
