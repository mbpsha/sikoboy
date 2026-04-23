<script setup>
import CardDokumen from './CardDokumen.vue'
import ExtraTemplate from './ExtraTemplate.vue'
import CTASection from './CTASection.vue'

const props = defineProps({
  kategoris: { type: Array, default: () => [] }
})

const getTemplate = (matchName) => {
  if (!props.kategoris || props.kategoris.length === 0) {
    return '/storage/docs/your-template.docx'
  }
  const found = props.kategoris.find(k => {
    if (!k.nama_kategori) return false
    return k.nama_kategori.toLowerCase().includes((matchName||'').toLowerCase())
  })
  if (found && found.file_template) return found.file_template
  const firstWith = props.kategoris.find(k => k.file_template)
  return (firstWith && firstWith.file_template) || '/storage/docs/your-template.docx'
}

const getPreview = (matchName) => {
  if (!props.kategoris || props.kategoris.length === 0) return null
  const found = props.kategoris.find(k => {
    if (!k.nama_kategori) return false
    return k.nama_kategori.toLowerCase().includes((matchName||'').toLowerCase())
  })
  if (found && found.preview) return found.preview
  const firstWith = props.kategoris.find(k => k.preview)
  return (firstWith && firstWith.preview) || null
}
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

      <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
        <!-- KSDD -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDD</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Daerah Lain)</p>

          <div class="mt-8 space-y-6">
            <CardDokumen
              title="PERJANJIAN KERJA SAMA"
              description="Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pemerintah Daerah Kabupaten Boyolali"
              href="public\docs\Template PKS KSDD.docx"
              preview="public\docs\Template PKS KSDD.pdf"
            />

            <CardDokumen
              title="KESEPAKATAN BERSAMA"
              description="Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dan Pemerintah Daerah Kabupaten Boyolali"
              href="public\docs\Template KESEPAKATAN BERSAMA KSDD.docx"
              preview="public\docs\Template KESEPAKATAN BERSAMA KSDD.pdf"
            />
          </div>
        </div>

        <!-- KSDPK -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDPK</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Pihak Ketiga)</p>

          <div class="mt-8 space-y-6">
            <CardDokumen
              title="PERJANJIAN KERJA SAMA"
              description="Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pihak Ketiga"
              href="/public/docs/Template PKS KSDPK.docx"
              preview="/public/docs/Template PKS KSDPK.pdf"
            />

            <CardDokumen
              title="KESEPAKATAN BERSAMA"
              description="Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dengan Pihak Ketiga"
              href="/public/docs/Template KESEPAKATAN BERSAMA KSDPK.docx"
              preview="/public/docs/Template KESEPAKATAN BERSAMA KSDPK.pdf"
            />
          </div>
        </div>
      </div>

      <!-- Additional templates -->
      <div class="mt-12 max-w-3xl mx-auto space-y-8 px-4 sm:px-0">
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">Nota Kesepakatan Sinergi</h3>
          <div class="mt-6 flex justify-center">
            <CardDokumen
              title="SINERGI"
              description="Nota Kesepakatan antara Kementerian/Lembaga/Instansi Vertikal dan Pemerintah Kabupaten Boyolali"
              href="/public/docs/Template NOTA KESEPAKATAN.docx"
              preview="/public/docs/Template NOTA KESEPAKATAN.pdf"
            />
          </div>
        </div>

        <ExtraTemplate />
      </div>
      <CTASection />

    </div>
</template>
