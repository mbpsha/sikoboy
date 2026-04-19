import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'SIKOBOY';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // Try to resolve from Pages first
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const components = import.meta.glob('./Components/**/*.vue', { eager: true });
        const allComponents = { ...pages, ...components };
        
        const pageComponentPath = `./Pages/${name}.vue`;
        const componentComponentPath = `./Components/${name}.vue`;
        
        if (pageComponentPath in allComponents) {
            return allComponents[pageComponentPath];
        }
        if (componentComponentPath in allComponents) {
            return allComponents[componentComponentPath];
        }
        
        throw new Error(`Component not found: ${name}`);
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
