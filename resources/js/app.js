import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import Layout from "./Pages/layouts/Layout.vue";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const path = `./Pages/${name}.vue`;

        // Проверяем, существует ли страница
        if (!pages[path]) {
            throw new Error(`Page not found: ${path}`);
        }

        let page = pages[path];

        // Безопасная проверка и добавление layout
        if (page && page.default) {
            // Если у компонента нет своего layout, устанавливаем layout по умолчанию
            if (!page.default.layout) {
                page.default.layout = Layout;
            }
        } else {
            console.error("Invalid page module:", page);
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
