require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import StravaFixSetup from './Integrations/StravaFixSetup';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import VueEasyLightbox from 'vue-easy-lightbox'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(VueEasyLightbox)
            .component('task-strava-upload', StravaFixSetup)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
