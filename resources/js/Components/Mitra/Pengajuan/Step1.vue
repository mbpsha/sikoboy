<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';

const form = useForm({
  nama_mitra: '',
  no_hp: '',
  alamat: '',
  pic: '',
});

const submit = () => {
  form.post(route('mitra.pengajuan.step1.store'));
};
</script>

<template>
  <div class="min-h-screen bg-[#8AB4BB] flex flex-col font-sans relative">
    <Header />

    <main class="flex-1 flex flex-col">
      
      <div class="max-w-5xl mx-auto w-full px-4 sm:px-6 md:px-10 pt-28 sm:pt-32 pb-10 sm:pb-16">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#17464E]">Profil Mitra</h1>
        <p class="text-xs sm:text-sm text-[#17464E]/80 mt-2">Kelola informasi dan pantau status pengajuan kerjasama Anda</p>
      </div>

      <div class="bg-[#17464E] rounded-t-[30px] sm:rounded-t-[40px] pt-16 sm:pt-24 pb-20 sm:pb-32 text-center shadow-inner">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
          <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Form Pengajuan Kerjasama</h2>
          <p class="text-xs sm:text-[15px] text-gray-200 leading-relaxed max-w-2xl mx-auto px-2">
            Mitra eksternal dapat mengajukan kerjasama kepada pemerintah<br class="hidden md:block"/>
            kabupaten boyolali melalui sistem ini
          </p>
        </div>
      </div>

      <div class="flex-1 bg-[#8AB4BB] relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
          
          <div class="relative -mt-20 sm:-mt-24 mb-16 sm:mb-20">
            
            <div class="relative flex items-center justify-center max-w-xs mx-auto mb-8 sm:mb-10">
              <div class="absolute left-0 right-0 h-[2px] bg-gray-400/50 z-0"></div>
              <div class="flex justify-between w-full relative z-10">
                <div class="flex items-center justify-center w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-[#17464E] text-white font-bold shadow-md border-4 border-[#8AB4BB] text-sm sm:text-base">1</div>
                <div class="flex items-center justify-center w-9 sm:w-11 h-9 sm:h-11 rounded-full bg-gray-400 text-white font-bold shadow-md border-4 border-[#8AB4BB] text-sm sm:text-base">2</div>
              </div>
            </div>

            <div class="bg-white rounded-[20px] sm:rounded-[24px] p-5 sm:p-8 md:p-14 shadow-[0_15px_50px_rgba(0,0,0,0.15)]">
              <h3 class="text-lg sm:text-xl font-bold text-[#17464E] mb-6 sm:mb-10">Data Kelengkapan Mitra</h3>

              <form @submit.prevent="submit" class="space-y-6 sm:space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 md:gap-x-10 md:gap-y-8">
                  
                  <div class="space-y-2">
                    <label class="block text-xs sm:text-sm font-bold text-[#17464E]">Nama Mitra <span class="text-red-500">*</span></label>
                    <input v-model="form.nama_mitra" type="text" placeholder="Nama Mitra" class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all" />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-xs sm:text-sm font-bold text-[#17464E]">Nomor Hp <span class="text-red-500">*</span></label>
                    <input v-model="form.no_hp" type="tel" placeholder="Nomor Hp" class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all" />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-xs sm:text-sm font-bold text-[#17464E]">Alamat <span class="text-red-500">*</span></label>
                    <input v-model="form.alamat" type="text" placeholder="Masukkan Alamat" class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all" />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-xs sm:text-sm font-bold text-[#17464E]">PIC <span class="text-red-500">*</span></label>
                    <input v-model="form.pic" type="text" placeholder="Nama PIC" class="w-full px-4 sm:px-5 py-2 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg sm:rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-[#17464E]/20 outline-none transition-all" />
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center pt-4 sm:pt-6">
                  <Link :href="route('portal-mitra')" class="px-8 sm:px-14 py-2 sm:py-3 bg-[#D1D5DB] text-[#4B5563] rounded-lg sm:rounded-xl font-bold hover:bg-gray-300 transition-all text-sm sm:text-base">Batal</Link>
                  <button type="submit" class="px-8 sm:px-14 py-2 sm:py-3 bg-[#336D71] text-white rounded-lg sm:rounded-xl font-bold hover:bg-[#28575a] shadow-lg transition-all text-sm sm:text-base">Simpan Data</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>