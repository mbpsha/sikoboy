<script setup>
import { ref } from 'vue';

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  kerjasamaNama: {
    type: String,
    default: 'Digitalisasi Sistem Administrasi Kependudukan'
  }
});

const emit = defineEmits(['close']);

// REF UNTUK INPUT FILE
const fileInput = ref(null);
const selectedFile = ref(null);
const isUploading = ref(false);

// Fungsi buka file explorer laptop
const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

// Fungsi saat file dipilih
const handleFileSelect = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    selectedFile.value = file;
    console.log('File terpilih:', file.name);
  }
};

// Fungsi tombol upload
const uploadFile = async () => {
  if (!selectedFile.value) return;
  
  isUploading.value = true;
  try {
    // Simulasi loading 1.5 detik
    await new Promise(resolve => setTimeout(resolve, 1500));
    alert('Berhasil! File ' + selectedFile.value.name + ' telah diunggah.');
    selectedFile.value = null;
  } catch (error) {
    alert('Gagal mengunggah file.');
  } finally {
    isUploading.value = false;
  }
};

const downloadFile = (filename) => {
  const element = document.createElement('a');
  element.setAttribute('href', `/storage/dokumen/${filename}`);
  element.setAttribute('download', filename);
  element.style.display = 'none';
  document.body.appendChild(element);
  element.click();
  document.body.removeChild(element);
};

const progressItems = [
  { id: 1, title: 'Pengajuan Diterima', tanggal: 'Selesai pada 6 Juni 2027', status: 'completed', catatan: 'Tidak ada catatan', pegawai: 'Admin Bidang Tata Pemerintahan' },
  { id: 2, title: 'Pengajuan Direview', tanggal: 'Selesai pada 6 Juni 2027', status: 'completed', catatan: 'Tidak ada catatan', pegawai: 'Admin Bidang Tata Pemerintahan' },
  { id: 3, title: 'Proses 1 : Revisi Dokumen', tanggal: 'Selesai pada 6 Juni 2027', status: 'warning', catatan: 'Perbaiki penamaan kelanjutan kerjasama dan tambahkan lampiran.', pegawai: 'Admin Bidang Hukum', file: 'Draft_kerjasama_6/6/27.pdf' },
  { id: 4, title: 'Proses ...', tanggal: 'Selesai pada -', status: 'pending', catatan: '' },
  { id: 5, title: 'Kerjasama Ditandatangani', tanggal: 'Selesai pada -', status: 'pending', catatan: '' }
];

const getStatusIcon = (status) => {
  if (status === 'completed') return '✓';
  if (status === 'warning') return '⚠';
  return '⏳';
};

const getStatusColor = (status) => {
  if (status === 'completed') return '#10b981';
  if (status === 'warning') return '#ef4444';
  return '#9ca3af';
};
</script>

<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-[9999]">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[85vh] overflow-y-auto relative">
        
        <input
          ref="fileInput"
          type="file"
          accept=".pdf,.doc,.docx"
          class="hidden"
          @change="handleFileSelect"
        />

        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-start z-10">
          <div>
            <h2 class="text-2xl font-bold text-[#1f2937]">Progres Kerjasama</h2>
            <p class="text-sm text-gray-600 mt-1">{{ kerjasamaNama }}</p>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="p-6">
          <h3 class="text-sm font-semibold text-gray-700 mb-6 font-sans">Status Tracking</h3>

          <div class="space-y-1 relative">
            <div class="absolute left-[18px] top-[45px] bottom-0 w-[2px] bg-gray-200"></div>

            <div v-for="item in progressItems" :key="item.id" class="pl-16 pb-8 relative">
              <div 
                class="absolute left-0 top-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm z-10"
                :style="{ backgroundColor: getStatusColor(item.status) }"
              >
                {{ getStatusIcon(item.status) }}
              </div>

              <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-start mb-1">
                  <h4 class="font-bold text-[#1f2937] leading-tight">{{ item.title }}</h4>
                </div>
                <p class="text-[11px] text-gray-500 mb-3 uppercase tracking-wider">{{ item.tanggal }}</p>

                <div v-if="item.catatan" class="mb-4">
                  <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Catatan:</p>
                  <div :class="item.status === 'warning' ? 'bg-red-50 border-l-4 border-red-400 p-2' : 'bg-gray-100 p-2 rounded'">
                    <p class="text-xs text-gray-700">{{ item.catatan }}</p>
                  </div>
                </div>

                <div v-if="item.status === 'warning'" class="mt-4 p-4 border border-dashed border-blue-300 bg-blue-50/50 rounded-lg">
                  <p class="text-xs font-bold text-blue-800 mb-3 uppercase">Upload Dokumen Revisi</p>
                  
                  <div v-if="selectedFile" class="flex items-center justify-between bg-white p-2 rounded border border-blue-200 mb-3">
                    <span class="text-xs text-blue-700 font-medium truncate">📄 {{ selectedFile.name }}</span>
                    <button @click="selectedFile = null" class="text-red-500 text-[10px] font-bold hover:underline">HAPUS</button>
                  </div>

                  <div class="flex flex-wrap gap-2">
                    <button 
                      type="button"
                      @click="triggerFileInput"
                      class="px-4 py-2 text-xs bg-white border border-blue-400 text-blue-600 rounded-md font-semibold hover:bg-blue-50 transition"
                    >
                      Pilih File Dari Laptop
                    </button>
                    
                    <button 
                      type="button"
                      @click="uploadFile"
                      :disabled="!selectedFile || isUploading"
                      class="px-4 py-2 text-xs bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                    >
                      {{ isUploading ? 'Mohon Tunggu...' : 'Upload Sekarang' }}
                    </button>
                  </div>
                </div>

                <p v-if="item.pegawai" class="text-[10px] text-gray-500 text-right mt-4 italic">
                  Oleh: <span class="font-bold text-gray-700">{{ item.pegawai }}</span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6 flex gap-3 z-20">
          <button @click="$emit('close')" class="flex-1 py-2.5 border border-gray-300 text-gray-600 rounded-lg font-bold hover:bg-gray-50 transition uppercase text-xs tracking-widest">
            Batal
          </button>
          <button @click="$emit('close')" class="flex-1 py-2.5 bg-[#1e5a5e] text-white rounded-lg font-bold hover:bg-[#144a4d] transition uppercase text-xs tracking-widest shadow-md">
            Simpan Perubahan
          </button>
        </div>

      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f9fafb; }
::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

/* Mencegah interaksi di belakang modal saat open */
body { overflow: hidden; }
</style>