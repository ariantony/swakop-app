import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
});

Inertia.on('start', () => {
    Swal.showLoading();
})

InertiaProgress.init({ color: '#4B5563' });

Inertia.on('finish', () => {
    Swal.close();
})

Inertia.on('finish', () => {
    const { success, error } = usePage().props.value;
    
    if (success || error) {
        Swal.fire({
            title: success ? 'Berhasil!' : 'Gagal!',
            text: success || error,
            icon: success ? 'success' : 'error',
            showConfirmButton: false,
            timer: 1500,
        });
    }
})
