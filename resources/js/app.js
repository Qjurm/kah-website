import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue'),
    ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Global translation helper
        app.config.globalProperties.__ = (key) => {
            const translations = props.initialPage.props.translations || {};
            return translations[key] || key;
        };

        // For legacy components (mixin)
        app.mixin({
            methods: {
                __(key) {
                    const translations = this.$page.props.translations || {};
                    return translations[key] || key;
                }
            }
        });

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
