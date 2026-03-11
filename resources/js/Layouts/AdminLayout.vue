<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from '@headlessui/vue';
import { Bars3Icon, XMarkIcon, UserCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), current: route().current('admin.dashboard') },
    { name: 'Mitra', href: route('admin.partners.index'), current: route().current('admin.partners.*') },
];

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <FlashMessages />
        <Disclosure as="nav" class="bg-white shadow-sm" v-slot="{ open }">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <span class="text-xl font-bold text-green-600">SIKOBOY Admin</span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'border-green-500 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium',
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <Menu as="div" class="relative ml-3">
                            <div>
                                <MenuButton
                                    class="flex rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                >
                                    <span class="sr-only">Open user menu</span>
                                    <UserCircleIcon class="h-8 w-8 text-gray-400" />
                                </MenuButton>
                            </div>
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <MenuItems
                                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                >
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                        <div class="font-medium">{{ user?.display_name }}</div>
                                        <div class="text-xs text-gray-500">{{ user?.email }}</div>
                                    </div>
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            @click="logout"
                                            :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                                        >
                                            Logout
                                        </button>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <DisclosureButton
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
                        >
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>
                </div>
            </div>

            <DisclosurePanel class="sm:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <DisclosureButton
                        v-for="item in navigation"
                        :key="item.name"
                        as="a"
                        :href="item.href"
                        :class="[
                            item.current
                                ? 'border-green-500 bg-green-50 text-green-700'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700',
                            'block border-l-4 py-2 pl-3 pr-4 text-base font-medium',
                        ]"
                    >
                        {{ item.name }}
                    </DisclosureButton>
                </div>
                <div class="border-t border-gray-200 pb-3 pt-4">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <UserCircleIcon class="h-10 w-10 text-gray-400" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ user?.display_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user?.email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <DisclosureButton
                            as="button"
                            @click="logout"
                            class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                        >
                            Logout
                        </DisclosureButton>
                    </div>
                </div>
            </DisclosurePanel>
        </Disclosure>

        <main>
            <slot />
        </main>
    </div>
</template>
