<script setup>
import LandingLayout from '@/Layouts/LandingLayout.vue'
import { Link } from '@inertiajs/vue3'
import DokumenHero from '@/Components/Dokumen/HeroSection.vue'
</script>

<template>
  <LandingLayout>
    <DokumenHero />

    <div class="max-w-7xl mx-auto px-6 py-12">

      <div class="grid md:grid-cols-2 gap-8">
        <!-- KSDD -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDD</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Daerah Lain)</p>

          <div class="mt-8 space-y-6">
            <!-- PERJANJIAN KERJA SAMA -->
            <div class="bg-[#D4E4E8] rounded-2xl p-6 shadow-lg">
              <h4 class="font-bold text-center text-[#0C505C] text-lg">PERJANJIAN KERJA SAMA</h4>
              <p class="text-xs text-center text-slate-600 mt-3 px-4">Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pemerintah Daerah Kabupaten Boyolali</p>
              <div class="mt-4 flex justify-center">
                <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  DOCX
                </span>
              </div>
              <div class="mt-4 flex justify-center">
                <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  Lihat Dokumen Kerja Sama
                </Link>
              </div>
            </div>

            <!-- KESEPAKATAN BERSAMA -->
            <div class="bg-[#D4E4E8] rounded-2xl p-6 shadow-lg">
              <h4 class="font-bold text-center text-[#0C505C] text-lg">KESEPAKATAN BERSAMA</h4>
              <p class="text-xs text-center text-slate-600 mt-3 px-4">Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dan Pemerintah Daerah Kabupaten Boyolali</p>
              <div class="mt-4 flex justify-center">
                <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  DOCX
                </span>
              </div>
              <div class="mt-4 flex justify-center">
                <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  Lihat Dokumen Kerja Sama
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- KSDPK -->
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">KSDPK</h3>
          <p class="text-md font-bold text-[#0C505C] text-center text-slate-600 mt-2">(Kerja Sama Daerah Dengan Pihak Ketiga)</p>

          <div class="mt-8 space-y-6">
            <!-- PERJANJIAN KERJA SAMA -->
            <div class="bg-[#D4E4E8] rounded-2xl p-6 shadow-lg">
              <h4 class="font-bold text-center text-[#0C505C] text-lg">PERJANJIAN KERJA SAMA</h4>
              <p class="text-xs text-center text-slate-600 mt-3 px-4">Perjanjian Kerja Sama antara Pemerintah Kabupaten Boyolali dengan Pihak Ketiga</p>
              <div class="mt-4 flex justify-center">
                <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  DOCX
                </span>
              </div>
              <div class="mt-4 flex justify-center">
                <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  Lihat Dokumen Kerja Sama
                </Link>
              </div>
            </div>

            <!-- KESEPAKATAN BERSAMA -->
            <div class="bg-[#D4E4E8] rounded-2xl p-6 shadow-lg">
              <h4 class="font-bold text-center text-[#0C505C] text-lg">KESEPAKATAN BERSAMA</h4>
              <p class="text-xs text-center text-slate-600 mt-3 px-4">Kesepakatan bersama antara Pemerintah Daerah Kabupaten Boyolali dengan Pihak Ketiga</p>
              <div class="mt-4 flex justify-center">
                <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  DOCX
                </span>
              </div>
              <div class="mt-4 flex justify-center">
                <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  Lihat Dokumen Kerja Sama
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional templates -->
      <div class="mt-12 max-w-3xl mx-auto space-y-8">
        <div class="bg-white/80 backdrop-blur rounded-3xl p-8 shadow-xl border border-white/50">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">Nota Kesepakatan Sinergi</h3>
          <div class="mt-6 flex justify-center">
            <div class="bg-[#D4E4E8] rounded-2xl p-6 shadow-lg max-w-md w-full">
              <h4 class="font-bold text-center text-[#0C505C]">SINERGI</h4>
              <p class="text-xs text-center text-slate-600 mt-3 px-4">Nota Kesepakatan antara Kementerian/Lembaga/Instansi Vertikal dan Pemerintah Kabupaten Boyolali</p>
              <div class="mt-4 flex justify-center">
                <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  DOCX
                </span>
              </div>
              <div class="mt-4 flex justify-center">
                <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  Lihat Dokumen Kerja Sama
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-[#D4E4E8] rounded-3xl p-8 shadow-xl">
          <h3 class="text-2xl font-bold text-[#0C505C] text-center">Kerangka Acuan Kerja</h3>
          <div class="mt-6 flex justify-center">
            <span class="text-xs inline-flex items-center gap-1 text-[#0C8BA3] font-semibold">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              DOCX
            </span>
          </div>
          <div class="mt-4 flex justify-center">
            <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-6 py-2 rounded-full shadow hover:shadow-md transition font-medium text-sm underline decoration-2 decoration-[#0C505C] underline-offset-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              Lihat Dokumen Kerja Sama
            </Link>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-12 mb-24">
        <div class="bg-[#0C505C] text-white rounded-3xl p-10 shadow-2xl text-center">
          <h3 class="text-2xl font-bold">Siap Mengajukan Kerja Sama?</h3>
          <p class="mt-3 text-sm text-white/90 max-w-2xl mx-auto">*Buat dokumen Anda secara otomatis dan pastikan Anda mengisi form pengajuan Kerja Sama dengan informasi yang dapat dipertanggungjawabkan</p>
          <div class="mt-6">
            <Link href="#" class="inline-flex items-center gap-2 bg-white text-[#0C505C] px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition font-semibold">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Ajukan Kerja Sama
            </Link>
          </div>
        </div>
      </div>

    </div>
  </LandingLayout>
</template>