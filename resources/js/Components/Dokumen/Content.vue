<script setup>
import CardDokumen from './CardDokumen.vue'
import CTASection from './CTASection.vue'
import ExtraTemplate from './ExtraTemplate.vue'

const props = defineProps({
  dokumenGroups: { type: Array, default: () => [] }
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">


      <div
        v-if="dokumenGroups.length"
        class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8"
      >
        <section
          v-for="group in dokumenGroups"
          :key="group.nama_kategori"
          class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50"
        >
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">
            {{ group.label }}
          </h3>

          <p
            v-if="group.deskripsi"
            class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2"
          >
            ({{ group.deskripsi }})
          </p>

          <div class="mt-8 space-y-6">
            <CardDokumen
              v-for="item in group.items"
              :key="item.id"
              :title="item.title"
              :description="item.description"
              :badge="item.badge"
              :href="item.href"
              :preview="item.preview"
            />
          </div>
        </section>
      </div>

      <div
        v-else
        class="bg-white/80 backdrop-blur rounded-3xl p-10 shadow-xl border border-white/50 text-center text-slate-600"
      >
        Belum ada template dokumen yang aktif.
      </div>

      <div class="mt-6 md:mt-8">
        <ExtraTemplate />
      </div>

      <div class="mt-6 md:mt-8">
        <CTASection />
      </div>

    </div>
</template>
