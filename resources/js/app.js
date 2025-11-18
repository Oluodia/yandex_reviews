import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import Layout from "./pages/layouts/Layout.vue";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.vue", { eager: true });
        const path = `./pages/${name}.vue`;

        if (!pages[path]) {
            throw new Error(`Page not found: ${path}`);
        }

        let page = pages[path];

        if (page && page.default) {
            // Явная проверка: layout должен быть именно undefined
            const hasLayout = page.default.layout !== undefined;
            
            if (!hasLayout) {
                page.default.layout = Layout;
            }
            // Если layout === null или любой другое значение - оставляем как есть
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
