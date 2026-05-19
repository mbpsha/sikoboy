<template>
  <AdminLayout title="Ajuan Kerjasama" @click="closeAllFilters">
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

      <!-- Search & Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-3">
          <input
            v-model="local.search"
            placeholder="Cari berdasarkan mitra atau nama kerjasama..."
            class="flex-1 min-w-[220px] rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition"
          />
          <select v-model="local.tahun" @change="applyFilters" class="rounded-full px-4 py-2.5 text-sm border border-gray-200 bg-gray-50 focus:outline-none focus:border-teal-600">
            <option value="">Semua Tahun</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
          <button @click="applyFilters" class="bg-teal-700 hover:bg-teal-800 text-white text-sm px-5 py-2.5 rounded-full font-medium transition">
            Filter
          </button>
          <button v-if="local.search || local.tahun || local.status" @click="resetAllFilters" class="bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm px-5 py-2.5 rounded-full font-medium transition">
            Reset
          </button>
        </div>
      </div>

      <!-- Header with Add Button -->
      <div class="flex items-center justify-between">

        <div class="relative ml-0 md:ml-auto">
          <button
            @click="showAddMenu = !showAddMenu"
            class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kerjasama
          </button>
          <div
            v-if="showAddMenu"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden z-10"
          >
            <Link href="/admin/riwayat-kerjasama/pemerintah" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition">
              ➕ Pemerintah
            </Link>
            <Link href="/admin/riwayat-kerjasama/mitra" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition border-t">
              ➕ Mitra
            </Link>
            <Link href="/admin/riwayat-kerjasama/gabungan" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-medium transition border-t">
              ➕ Gabungan
            </Link>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-teal-700 text-white text-xs uppercase tracking-wide">
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">No</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1">
                    <span>Tahun</span>
                    <button v-if="local.tahun" @click="local.tahun = ''; applyFilters()" class="text-yellow-300 hover:text-yellow-100" title="Clear filter">✕</button>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 relative">
                    <span>Mitra</span>
                    <button @click.stop="openFilterColumn = openFilterColumn === 'mitra' ? null : 'mitra'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- MITRA FILTER DROPDOWN -->
                    <div v-show="openFilterColumn === 'mitra'" @click.stop class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200 max-w-xs">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueMitra" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.mitra.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.mitra.push(val)
                            } else {
                              columnFilters.mitra = columnFilters.mitra.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.mitra = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Judul</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 relative">
                    <span>Jenis Kerjasama</span>
                    <button @click.stop="openFilterColumn = openFilterColumn === 'jenis_kerjasama' ? null : 'jenis_kerjasama'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- JENIS KERJASAMA FILTER DROPDOWN -->
                    <div v-show="openFilterColumn === 'jenis_kerjasama'" @click.stop class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueJenisKerjasama" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.jenis_kerjasama.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.jenis_kerjasama.push(val)
                            } else {
                              columnFilters.jenis_kerjasama = columnFilters.jenis_kerjasama.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.jenis_kerjasama = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 relative">
                    <span>Jenis Dokumen</span>
                    <button @click.stop="openFilterColumn = openFilterColumn === 'jenis_dokumen' ? null : 'jenis_dokumen'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- JENIS DOKUMEN FILTER DROPDOWN -->
                    <div v-show="openFilterColumn === 'jenis_dokumen'" @click.stop class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueJenisDokumen" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.jenis_dokumen.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.jenis_dokumen.push(val)
                            } else {
                              columnFilters.jenis_dokumen = columnFilters.jenis_dokumen.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.jenis_dokumen = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">
                  <div class="flex items-center justify-between gap-1 relative">
                    <span>Urusan</span>
                    <button @click.stop="openFilterColumn = openFilterColumn === 'urusan' ? null : 'urusan'" class="text-yellow-300 hover:text-yellow-100 flex items-center justify-center w-6 h-6 rounded-full bg-white/10 hover:bg-white/20">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.657a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <!-- URUSAN FILTER DROPDOWN -->
                    <div v-show="openFilterColumn === 'urusan'" @click.stop class="absolute left-0 top-full mt-1 bg-white text-black text-sm rounded-lg shadow-2xl z-50 p-3 min-w-max border border-gray-200">
                      <div class="mb-2 max-h-40 overflow-y-auto">
                        <label v-for="val in uniqueUrusan" :key="val" class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                          <input type="checkbox" :checked="columnFilters.urusan.includes(val)" @change="(e) => {
                            if (e.target.checked) {
                              columnFilters.urusan.push(val)
                            } else {
                              columnFilters.urusan = columnFilters.urusan.filter(v => v !== val)
                            }
                          }" class="cursor-pointer" />
                          <span class="text-xs">{{ val }}</span>
                        </label>
                      </div>
                      <button @click="columnFilters.urusan = []" class="w-full px-2 py-1 bg-gray-300 hover:bg-gray-400 rounded text-xs">Clear</button>
                    </div>
                  </div>
                </th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Mulai</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Berakhir</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Jangka Waktu</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">File</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Pembiayaan</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">No. Surat Mitra</th>
                <th class="py-3 px-4 text-left font-medium border-r border-white/10">Proses</th>
                <th class="py-3 px-4 text-left font-medium">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(k, idx) in filteredKerjasama" :key="k.id_kerjasama" class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-500 text-xs">{{ indexOffset + idx + 1 }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tahun }}</td>
                <td class="py-3 px-4 max-w-[130px] truncate font-medium">{{ k.mitra ?? '—' }}</td>
                <td class="py-3 px-4 max-w-[220px] leading-snug">{{ k.judul }}</td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span v-if="k.jenis_kerjasama" class="px-3 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700">
                    {{ k.jenis_kerjasama }}
                  </span>
                  <span v-else class="text-gray-400">—</span>
                </td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.jenis_dokumen ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600">{{ k.urusan ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tanggal_mulai ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.tanggal_berakhir ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ formatJangkaWaktu(k.tanggal_mulai, k.tanggal_berakhir) }}</td>
                <td class="py-3 px-4">
                  <div class="flex flex-col gap-1">
                    <a v-if="k.file_url" :href="k.file_url" target="_blank" class="text-teal-700 hover:text-teal-900 font-medium text-xs underline-offset-2 hover:underline">
                      � Lihat Dokumen
                    </a>
                    <a v-if="k.latest_mitra_revision" :href="'/storage/' + k.latest_mitra_revision.lokasi_file" target="_blank" class="text-blue-700 hover:text-blue-900 text-xs">
                      📄 Lihat revisi: {{ k.latest_mitra_revision.nama_file }}
                    </a>
                  </div>
                </td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.pembiayaan ?? '—' }}</td>
                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ k.nomor_suratM ?? k.nomor_surat ?? k.nomor_suratP ?? '—' }}</td>

                <!-- Proses column -->
                <td class="py-3 px-4 align-top">
                  <div class="space-y-1.5 min-w-[180px]">
                    <button
                      v-for="(p, pi) in (k.proses || [])" :key="pi"
                      @click.prevent="openProcessModal(k, p)"
                      class="w-full text-left px-3 py-2 rounded-lg text-xs transition cursor-pointer"
                      :class="k.is_finalized
                        ? 'bg-orange-100 text-orange-700 border border-orange-200'
                        : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                    >
                      <span v-if="k.is_finalized" class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                        {{ p.label || p.title }} — <span class="font-semibold">Selesai</span>
                      </span>
                      <span v-else>{{ p.label || p.title }}</span>
                    </button>
                    <p v-if="!(k.proses || []).length" class="text-xs text-gray-400 italic">Belum ada proses.</p>
                    <!-- Tambah Proses — sembunyikan jika sudah selesai -->
                    <template v-if="!k.is_finalized">
                      <button
                        @click.prevent="toggleAddForm(k.id_kerjasama)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-teal-700 hover:bg-teal-50 text-xs font-medium transition mt-1"
                      >
                        <span class="text-base leading-none">+</span> Tambah Proses
                      </button>
                      <div v-if="showAddFormFor[k.id_kerjasama]" class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
                        <input
                          v-model="newProcessForm[k.id_kerjasama].title"
                          placeholder="Contoh: Proses 1 - Revisi"
                          class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500"
                        />
                        <div class="flex gap-2">
                          <button @click.prevent="addProcess(k)" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Tambah</button>
                          <button @click.prevent="cancelAdd(k.id_kerjasama)" class="flex-1 bg-white border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg transition">Batal</button>
                          <button @click.prevent="finishAddProcess(k)" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Selesai Proses</button>
                        </div>
                      </div>
                    </template>

                    <!-- Label history jika sudah finalized -->
                    <p v-else class="text-xs text-orange-500 font-medium mt-1 italic">✓ Proses selesai</p>
                  </div>
                </td>

                <!-- Status column -->
                <td class="py-3 px-4">
                  <span
                    class="inline-block px-2.5 py-1 rounded-full text-xs font-medium"
                    :class="statusBadgeClass(k)"
                  >
                    {{ k.status_display ?? (k.status_persetujuan ? (k.status_persetujuan === 'disetujui' ? 'Diterima' : k.status_persetujuan) : 'Diterima') }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Footer -->
        <div v-if="(kerjasama?.last_page || 1) > 1" class="px-5 py-3.5 flex items-center justify-between border-t border-gray-100">
          <span class="text-xs text-gray-500">Tampilkan {{ kerjasama.per_page }} data / halaman</span>
          <div class="flex items-center justify-end gap-2">
            <button
              class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
              :disabled="!kerjasama.prev_page_url"
              @click.prevent="goToPage(kerjasama.current_page - 1)"
            >Sebelumnya</button>

            <button
              v-for="page in kerjasama.last_page"
              :key="page"
              @click.prevent="goToPage(page)"
              class="px-3 py-2 text-sm rounded-lg border"
              :class="page === kerjasama.current_page ? 'bg-teal-600 text-white' : 'bg-white'"
            >
              {{ page }}
            </button>

            <button
              class="px-3 py-2 text-sm rounded-lg border bg-white disabled:opacity-50"
              :disabled="!kerjasama.next_page_url"
              @click.prevent="goToPage(kerjasama.current_page + 1)"
            >Berikutnya</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Process Modal -->
    <Teleport to="body">
      <div v-if="showProcessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-4">
          <div>
            <h3 class="text-base font-semibold text-gray-900">Update Proses Kerjasama</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ activeKerjasama?.judul }}</p>
          </div>

          <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500">Status:</span>
            <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-medium">
              {{ activeProcess?.title }}
            </span>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Penanggung Jawab</label>
            <input
              :value="activeProcess.penanggung"
              readonly
              class="w-full border border-gray-100 rounded-lg px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
            <textarea v-model="activeProcess.catatan" rows="4"
              :readonly="isProcessReadOnly"
              :class="isProcessReadOnly ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : 'focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500'"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm transition resize-none" />
            <p v-if="isProcessReadOnly" class="text-xs text-orange-600 mt-1">
              ℹ️ Proses ini sudah diisi — tidak bisa diubah atau upload file.
            </p>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Upload Dokumen (PDF)</label>

            <!-- Tampilkan file yang sudah ada jika proses read-only -->
            <div v-if="isProcessReadOnly && activeProcess?.file" class="mb-3 p-3 bg-blue-50 border border-blue-100 rounded-lg">
              <p class="text-xs text-blue-700 font-medium mb-1">✓ File Tersimpan:</p>
              <a :href="'/storage/' + activeProcess.file" target="_blank" class="text-xs text-blue-600 underline">
                {{ activeProcess.file.split('/').pop() }}
              </a>
            </div>

            <input ref="processFileInput" type="file" accept="application/pdf" class="hidden" @change="onFileSelect" :disabled="isProcessReadOnly" />
            <div
              @click.prevent.stop="!isProcessReadOnly && triggerProcessFileInput()"
              @dragover.prevent
              @drop.prevent="!isProcessReadOnly && handleProcessDrop($event)"
              :class="['border-2 border-dashed rounded-xl p-6 text-center transition', isProcessReadOnly ? 'border-gray-200 bg-gray-50 cursor-not-allowed' : 'border-gray-300 hover:border-teal-600 cursor-pointer']"
            >
              <div class="flex flex-col items-center">
                <svg class="w-10 h-10 text-teal-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <p class="font-semibold text-[#17464E] mb-1">Drag & Drop Dokumen Kerjasama (PDF)</p>
                <p class="text-xs text-gray-600 mb-3">{{ isProcessReadOnly ? 'Proses sudah diisi — tidak bisa diubah.' : 'atau klik untuk memilih file' }}</p>
                <button v-if="!isProcessReadOnly" type="button" class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm">Pilih File</button>
                <p v-if="fileName" class="text-sm text-gray-600 mt-3">✓ {{ fileName }}</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button @click="closeProcessModal" class="px-4 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition">Tutup</button>
            <button v-if="!isProcessReadOnly" @click.prevent="saveProcessUpdate" class="px-4 py-2 text-sm rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition">Simpan</button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, reactive, watch } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  kerjasama: Object,
  filters: Object,
})

const page = usePage()
const currentUsername    = computed(() => page.props.auth?.user?.username ?? '')
const currentUserDivisi = computed(() => page.props.auth?.user?.divisi ?? currentUsername.value)

const kerjasama = computed(() => props.kerjasama ?? {
  data: [], per_page: 15, prev_page_url: null, next_page_url: null, current_page: 1,
})

const filters     = computed(() => props.filters ?? {})
const indexOffset = computed(() =>
  kerjasama.value.current_page ? (kerjasama.value.current_page - 1) * kerjasama.value.per_page : 0
)

const local = ref({
  search: filters.value?.search ?? '',
  tahun:  filters.value?.tahun  ?? '',
  status: filters.value?.status ?? '',
})

const columnFilters = ref({ mitra: [], jenis_kerjasama: [], jenis_dokumen: [], urusan: [] })
const openFilterColumn = ref(null)
const closeAllFilters  = () => { openFilterColumn.value = null }
const showAddMenu      = ref(false)

const uniqueMitra          = computed(() => [...new Set(kerjasama.value.data.map(i => i.mitra))].filter(Boolean).sort())
const uniqueJenisKerjasama = computed(() => [...new Set(kerjasama.value.data.map(i => i.jenis_kerjasama))].filter(Boolean).sort())
const uniqueJenisDokumen   = computed(() => [...new Set(kerjasama.value.data.map(i => i.jenis_dokumen))].filter(Boolean).sort())
const uniqueUrusan         = computed(() => [...new Set(kerjasama.value.data.map(i => i.urusan))].filter(Boolean).sort())

const filteredKerjasama = computed(() => {
  // preserve any existing proses; ensure it's at least an array
  let data = (kerjasama.value.data || []).map(i => ({ ...i, proses: Array.isArray(i.proses) ? i.proses : [] }))

  if (columnFilters.value.mitra.length)           data = data.filter(i => columnFilters.value.mitra.includes(i.mitra))
  if (columnFilters.value.jenis_kerjasama.length) data = data.filter(i => columnFilters.value.jenis_kerjasama.includes(i.jenis_kerjasama))
  if (columnFilters.value.jenis_dokumen.length)   data = data.filter(i => columnFilters.value.jenis_dokumen.includes(i.jenis_dokumen))
  if (columnFilters.value.urusan.length)           data = data.filter(i => columnFilters.value.urusan.includes(i.urusan))
  return data
})

const years = computed(() => {
  const now = new Date().getFullYear()
  return Array.from({ length: 6 }).map((_, i) => now - i)
})

function statusBadgeClass(k) {
  const status = (k?.status_display || '').toString().toLowerCase()
  // finished / finalized -> orange badge
  if (k?.is_finalized || status === 'selesai') return 'bg-orange-100 text-orange-800'
  if (status.startsWith('proses'))              return 'bg-blue-100 text-blue-800'
  if (status === 'segera berakhir')            return 'bg-yellow-100 text-yellow-800'
  if (status === 'berakhir')                   return 'bg-red-100 text-red-800'
  // explicit approved flag
  if (k?.status_persetujuan === 'disetujui')   return 'bg-green-100 text-green-800'
  // default -> treat as "Diterima" with green badge
  return 'bg-green-100 text-green-800'
}

// Format duration between two ISO date strings into "X tahun Y bulan Z hari"
function formatJangkaWaktu(startStr, endStr) {
  if (!startStr || !endStr) return '—'
  const start = new Date(startStr)
  const end = new Date(endStr)
  if (isNaN(start) || isNaN(end) || end < start) return '—'

  let years = end.getFullYear() - start.getFullYear()
  let months = end.getMonth() - start.getMonth()
  let days = end.getDate() - start.getDate()

  if (days < 0) {
    // borrow days from previous month
    const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0) // last day of previous month
    days += prevMonth.getDate()
    months -= 1
  }

  if (months < 0) {
    months += 12
    years -= 1
  }

  const parts = []
  if (years > 0) parts.push(`${years} ${years === 1 ? 'tahun' : 'tahun'}`)
  if (months > 0) parts.push(`${months} ${months === 1 ? 'bulan' : 'bulan'}`)
  if (days > 0) parts.push(`${days} ${days === 1 ? 'hari' : 'hari'}`)
  return parts.length ? parts.join(' ') : '0 hari'
}

let searchTimeout = null
watch(() => local.value.search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => applyFilters(), 500)
})

function applyFilters() {
  const params = {}
  const q = (local.value.search || '').trim()
  if (q)               params.search = q
  if (local.value.tahun)  params.tahun  = local.value.tahun
  if (local.value.status) params.status = local.value.status
  router.get(route('admin.data-kerjasama.index'), params, { preserveState: true })
}

function resetAllFilters() {
  local.value = { search: '', tahun: '', status: '' }
  columnFilters.value = { mitra: [], jenis_kerjasama: [], jenis_dokumen: [], urusan: [] }
  router.get(route('admin.data-kerjasama.index'), {}, { preserveState: false })
}

function goToPage(p) {
  if (!p) return
  const params = {}
  if (local.value.search) params.search = local.value.search
  if (local.value.tahun)  params.tahun  = local.value.tahun
  router.get(route('admin.data-kerjasama.index'), { ...params, page: p }, { preserveState: true, preserveScroll: true })
}

// ─── Add process form ────────────────────────────────────────────────────────
const showAddFormFor = reactive({})
const newProcessForm = reactive({})

function toggleAddForm(id) {
  showAddFormFor[id] = !showAddFormFor[id]
  if (!newProcessForm[id]) newProcessForm[id] = { title: '' }
}

function cancelAdd(id) {
  showAddFormFor[id] = false
  if (newProcessForm[id]) newProcessForm[id].title = ''
}

function addProcess(k) {
  const id    = k.id_kerjasama
  const title = (newProcessForm[id]?.title || '').trim()
  if (!title) return

  if (!k.proses) k.proses = []
  k.proses.push({ id: null, label: title, title, catatan: '', penanggung: currentUserDivisi.value, __temp: true })

  newProcessForm[id].title = ''
  showAddFormFor[id] = false
}

// ✅ Selesaikan proses → simpan ke riwayat mitra
async function finishAddProcess(k) {
  const confirmed = await Swal.fire({
    title: 'Selesaikan Proses?',
    text: 'Data akan disimpan ke Riwayat Kerjasama Mitra.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, selesaikan',
    cancelButtonText: 'Batal',
  }).then(r => r.isConfirmed)

  if (!confirmed) return

  const id    = k.id_kerjasama
  const title = (newProcessForm[id]?.title || '').trim() || 'Proses Selesai'

  const fd = new FormData()
  fd.append('title',       title)
  fd.append('catatan',     'Semua proses telah diselesaikan.')
  fd.append('penanggung',  currentUserDivisi.value)
  fd.append('is_finished', '1')

  router.post(
    route('admin.data-kerjasama.proses.store', id),
    fd,
    {
      preserveScroll: true,
      onSuccess: () => {
        newProcessForm[id].title = ''
        showAddFormFor[id] = false
        Swal.fire({
          icon: 'success',
          title: 'Proses Selesai!',
          text: 'Data kerjasama telah dipindahkan ke Riwayat Kerjasama Mitra.',
          timer: 2000,
          showConfirmButton: false,
        }).then(() => router.visit(route('admin.riwayat-kerjasama.mitra')))
      },
      onError: (e) => {
        console.error('Gagal:', e)
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan proses.' })
      },
    }
  )
}

// ─── Process Modal ────────────────────────────────────────────────────────────
const showProcessModal = ref(false)
const activeProcess    = ref(null)
const activeKerjasama  = ref(null)
const fileToUpload     = ref(null)
const fileName         = ref('')
const processFileInput = ref(null)

const isProcessReadOnly = computed(() => {
  if (!activeProcess.value) return false
  // Proses temp (baru ditambah, belum disimpan) → selalu bisa diedit
  if (activeProcess.value.__temp) return false
  // Kerjasama sudah finalized → read only
  if (activeKerjasama.value?.is_finalized) return true
  // Proses lama yang sudah punya id dan catatan → read only
  if (activeProcess.value.id && activeProcess.value.catatan?.trim()) return true
  return false
})

function openProcessModal(k, p) {
  activeKerjasama.value  = k
  activeProcess.value    = { ...p, penanggung: p.penanggung || currentUserDivisi.value, catatan: p.id ? (p.catatan || '') : '' }
  showProcessModal.value = true
  fileToUpload.value     = null
  fileName.value         = ''
}

function closeProcessModal() {
  showProcessModal.value = false
  activeProcess.value    = null
  activeKerjasama.value  = null
  fileToUpload.value     = null
  fileName.value         = ''
}

function onFileSelect(e) {
  const f = e.target.files?.[0] ?? null
  fileToUpload.value = f
  fileName.value     = f ? f.name : ''
}

function triggerProcessFileInput() { processFileInput.value?.click() }

function handleProcessDrop(e) {
  const file = e.dataTransfer.files?.[0] ?? null
  if (file?.type === 'application/pdf') { fileToUpload.value = file; fileName.value = file.name }
}

// ✅ Simpan proses (tanpa selesai)
function saveProcessUpdate() {
  const k = activeKerjasama.value
  const p = activeProcess.value
  if (!k || !p) return

  const fd = new FormData()
  fd.append('title',      p.title      ?? '')
  fd.append('penanggung', p.penanggung ?? currentUserDivisi.value)
  fd.append('catatan',    p.catatan    ?? '')
  if (fileToUpload.value) fd.append('file', fileToUpload.value)

  const isNew = !p.id

  if (isNew) {
    router.post(route('admin.data-kerjasama.proses.store', k.id_kerjasama), fd, {
      preserveScroll: true,
      onSuccess: () => { fileToUpload.value = null; closeProcessModal() },
      onError: (e) => console.error('Gagal simpan proses baru:', e),
    })
  } else {
    fd.append('_method', 'PUT')
    router.post(route('admin.data-kerjasama.proses.update', [k.id_kerjasama, p.id]), fd, {
      preserveScroll: true,
      onSuccess: () => { fileToUpload.value = null; closeProcessModal() },
      onError: (e) => console.error('Gagal simpan proses:', e),
    })
  }
}
</script>
