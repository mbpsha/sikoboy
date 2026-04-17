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

// Logic File Upload
const fileInput = ref(null);
const selectedFile = ref(null);
const isUploading = ref(false);

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileSelect = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    selectedFile.value = file;
  }
};

const handleUpload = async () => {
  if (!selectedFile.value) return;
  
  isUploading.value = true;
  // Simulasi upload ke server
  await new Promise(resolve => setTimeout(resolve, 1500)); 
  
  alert(`File ${selectedFile.value.name} berhasil diunggah!`);
  isUploading.value = false;
  selectedFile.value = null;
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

// Data Progress
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
  <div v-if="isOpen" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[85vh] flex flex-col relative overflow-hidden">
      
      <input
        ref="fileInput"
        type="file"
        accept=".pdf"
        class="hidden"
        @change="handleFileSelect"
      />

      <div class="bg-white border-b border-gray-100 p-6 flex justify-between items-start shrink-0">
        <div>
          <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Progres Kerjasama</h2>
          <p class="text-sm text-gray-500 mt-1">{{ kerjasamaNama }}</p>
        </div>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition p-2 bg-gray-50 rounded-full">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <div class="p-6 overflow-y-auto flex-1">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Status Tracking</h3>

        <div class="space-y-1 relative">
          <div class="absolute left-[19px] top-[40px] bottom-0 w-[2px] bg-gray-200"></div>

          <div v-for="item in progressItems" :key="item.id" class="pl-14 pb-8 relative group">
            
            <div 
              class="absolute left-0 top-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10 transition-transform group-hover:scale-110"
              :style="{ backgroundColor: getStatusColor(item.status) }"
            >
              {{ getStatusIcon(item.status) }}
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
              <div class="flex justify-between items-start mb-1">
                <h4 class="font-bold text-gray-800 leading-tight">{{ item.title }}</h4>
              </div>
              <p class="text-[11px] font-medium text-gray-400 mb-4 tracking-wide uppercase">{{ item.tanggal }}</p>

              <div v-if="item.catatan" class="mb-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Catatan:</p>
                <div :class="[
                  'p-3 rounded-lg text-xs leading-relaxed',
                  item.status === 'warning' ? 'bg-red-50 text-red-700 border-l-4 border-red-400' : 'bg-gray-50 text-gray-600'
                ]">
                  {{ item.catatan }}
                </div>
              </div>

              <div v-if="item.file" class="mb-4">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Dokumen Terlampir:</p>
                <button
                  @click="downloadFile(item.file)"
                  class="w-full flex items-center justify-between p-3 bg-blue-50 border border-blue-100 rounded-lg group/file hover:bg-blue-100 transition"
                >
                  <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-500 rounded text-white"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg></div>
                    <span class="text-xs font-semibold text-blue-700 truncate max-w-[200px]">{{ item.file }}</span>
                  </div>
                  <span class="text-[10px] font-bold text-blue-500 group-hover/file:translate-x-1 transition-transform uppercase">Unduh</span>
                </button>
              </div>

              <div v-if="item.status === 'warning'" class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-3">Tindakan Diperlukan:</p>
                
                <div v-if="selectedFile" class="mb-3 flex items-center gap-2 p-2 bg-green-50 rounded-md border border-green-100">
                  <span class="text-[10px] text-green-700 font-bold flex-1 truncate">✓ {{ selectedFile.name }}</span>
                  <button @click="selectedFile = null" class="text-red-500 hover:text-red-700 font-bold text-[10px]">BATAL</button>
                </div>

                <div class="flex gap-2">
                  <button 
                    @click="triggerFileInput"
                    class="flex-1 px-4 py-2 text-xs font-bold border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition active:scale-95"
                  >
                    {{ selectedFile ? 'Ganti File' : 'Pilih File' }}
                  </button>
                  <button 
                    @click="handleUpload"
                    :disabled="!selectedFile || isUploading"
                    class="flex-1 px-4 py-2 text-xs font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:scale-100 transition active:scale-95 flex items-center justify-center gap-2 shadow-md"
                  >
                    <span v-if="isUploading" class="animate-spin text-lg">◌</span>
                    {{ isUploading ? 'Mengirim...' : 'Upload Revisi' }}
                  </button>
                </div>
              </div>

              <p v-if="item.pegawai" class="text-[10px] text-gray-400 text-right mt-4 italic">
                Diproses oleh: <span class="font-bold text-gray-600">{{ item.pegawai }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 border-t border-gray-100 p-6 flex justify-end gap-3 shrink-0">
        <button @click="$emit('close')" class="px-8 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition">TUTUP</button>
        <button 
          @click="$emit('close')"
          class="px-10 py-2.5 bg-[#1e5a5e] text-white rounded-xl text-xs font-bold hover:bg-[#144a4d] transition shadow-lg shadow-teal-900/20 active:scale-95 uppercase tracking-widest"
        >
          Simpan Progress
        </button>
      </div>

    </div>
  </div>
</template>

<style scoped>
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 20px; }
::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>