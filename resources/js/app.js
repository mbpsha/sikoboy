import './bootstrap';
import '../css/app.css';
import axios from "axios";

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'SIKOBOY';
axios.defaults.withCredentials = true;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
        [`./Pages/${name}.vue`, `./Components/${name}.vue`],
        {
            ...import.meta.glob('./Pages/**/*.vue'),
            ...import.meta.glob('./Components/**/*.vue'),
        },
    ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, typeof Ziggy !== 'undefined' ? Ziggy : window.Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
