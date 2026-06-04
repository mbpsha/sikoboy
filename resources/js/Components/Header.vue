<script setup>
import { computed, onMounted, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3"; // ← Import router ditambahkan
import logo from "@/images/logo_byl.png";

const page = usePage();
const user = computed(() => page.props?.auth?.user ?? null);
const isActive = (url) => {
    if (url === "/") {
        return page.url === "/";
    }
    return page.url === url;
};
// Inertia exposes shared props under `props` — check `auth.user` for logged-in user
const isAuthenticated = computed(
    () => !!(page.props && page.props.auth && page.props.auth.user),
);

const portalHref = computed(() => {
    if (!user.value) {
        return '/login/mitra';
    }

    if (user.value.role === 'admin') {
        return '/admin/dashboard';
    }

    return '/mitra/profile';
});
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-3 sm:px-6 py-3 sm:py-4">
      <!-- Left: emblem + authority text -->
      <div class="flex items-center gap-1 rounded-full px-2 sm:px-5 py-2" style="background: rgba(49,113,124,0.6);">
        <img :src="logo" alt="Boyolali Logo" class="h-10 sm:h-14 md:h-17 w-10 sm:w-14 md:w-17 object-contain" />
        <div class="text-left text-white hidden sm:block">
          <div class="font-semibold text-xs sm:text-sm md:text-base leading-tight">Sekretariat Daerah</div>
          <div class="font-medium tracking-wide text-xs sm:text-sm md:text-base leading-tight">Kabupaten Boyolali</div>
        </div>
      </div>

            <!-- Center: pill nav -->
            <div class="hidden lg:flex items-center justify-center flex-1">
                <nav
                    class="inline-flex items-center gap-0 rounded-full px-2 py-2 shadow-lg"
                    style="background-color: rgba(23, 70, 78, 0.95)"
                >
                    <!-- Beranda -->
                    <Link
                        href="/"
                        :class="[
                            'mx-2 px-4 py-1 text-sm rounded-full transition',
                            isActive('/')
                                ? 'bg-white text-[#17464E] font-semibold'
                                : 'text-white/90 hover:bg-white/20',
                        ]"
                    >
                        Beranda
                    </Link>

                    <!-- Tentang -->
                    <Link
                        href="/about"
                        :class="[
                            'mx-2 px-4 py-1 text-sm rounded-full transition',
                            isActive('/about')
                                ? 'bg-white text-[#17464E] font-semibold'
                                : 'text-white/90 hover:bg-white/20',
                        ]"
                    >
                        Tentang
                    </Link>

                    <!-- Peraturan -->
                    <Link
                        href="/peraturan"
                        :class="[
                            'mx-2 px-4 py-1 text-sm rounded-full transition',
                            isActive('/peraturan')
                                ? 'bg-white text-[#17464E] font-semibold'
                                : 'text-white/90 hover:bg-white/20',
                        ]"
                    >
                        Peraturan
                    </Link>

                    <!-- Dokumen -->
                    <Link
                        href="/template-dokumen"
                        :class="[
                            'mx-2 px-4 py-1 text-sm rounded-full transition',
                            isActive('/template-dokumen')
                                ? 'bg-white text-[#17464E] font-semibold'
                                : 'text-white/90 hover:bg-white/20',
                        ]"
                    >
                        Dokumen
                    </Link>

                    <!-- Kontak -->
                    <Link
                        href="/kontak"
                        :class="[
                            'mx-2 px-4 py-1 text-sm rounded-full transition',
                            isActive('/kontak')
                                ? 'bg-white text-[#17464E] font-semibold'
                                : 'text-white/90 hover:bg-white/20',
                        ]"
                    >
                        Kontak
                    </Link>
                </nav>
            </div>

            <!-- Right: CTAs when unauthenticated, Portal when authenticated -->
            <div class="flex items-center gap-3">
                <template v-if="!isAuthenticated">
                    <!-- pill CTAs with soft background -->
                    <div
                        class="flex items-center gap-3 rounded-full px-3 py-1"
                        style="background: rgba(49, 113, 124, 0.06)"
                    >
                        <Link
                            href="/register"
                            class="rounded-full bg-white text-[#17464E] px-5 py-2 text-sm font-semibold shadow-sm hover:bg-[#265e63]"
                            >Daftar</Link
                        >
                        <Link
                            href="/login/mitra"
                            class="rounded-full bg-[#31717C] px-5 py-2 text-sm font-semibold text-white shadow-md ring-2 ring-white hover:bg-[#265e63]"
                            >Masuk</Link
                        >
                    </div>
                </template>
                <template v-else>
                    <Link
                        :href="portalHref"
                        class="flex items-center gap-2 rounded-full bg-[#17464E] px-4 py-2 text-white font-medium shadow"
                    >
                        <svg
                            class="w-5 h-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M8 7h8M8 12h8M8 17h8" />
                        </svg>
                        <span>Portal Mitra</span>
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
