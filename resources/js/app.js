import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
    withApp(app) {
        // app.use(SomePlugin)
    },
    pages: {
        path: './Pages',
        resolve: (name) => import(`./Pages/${name}.vue`),
        lazy: true
    }
})
