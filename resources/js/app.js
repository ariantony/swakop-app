import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import common from './common';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

window.url = (path = '/') => {
    const sanitizer = /^\/+|\/+$/g
    const end = /\/+$/
    const { $url } = usePage().props.value
    const url = $url.replace(end, '') + '/' + path.replace(sanitizer, '').replace(/\/+/g, '/')

    return url.replace(end, '')
}

window.isAdmin = () => {
    const { isAdmin } = usePage().props.value
    return isAdmin
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mixin({
                methods: {
                    ...common,
                    url,
                    isAdmin
                },
            })
            .mount(el);
    },
});

Object.keys(common).forEach(c => window[c] = common[c])
window.url = url
window.isAdmin = isAdmin

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
        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            },
            heightAuto: false,
        }).fire({
            icon: success ? 'success' : 'error',
            title: success ? 'Berhasil !' : 'Error !',
            text: success ? success : error,
        });
    }
})

const token = () => {
    const { $token } = usePage().props.value
    $token && (axios.defaults.headers.common['Authorization'] = `Bearer ${$token}`)
}

Inertia.on('start', token)
Inertia.on('finish', token)
Inertia.on('before', token)