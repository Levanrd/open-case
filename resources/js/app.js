import '../css/app.css';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'

createInertiaApp({
  title: (title) => (title ? `${title} - Open Case` : 'Open Case'),
  withApp(app) {
    app.component('Head', Head)
    app.component('Link', Link)
  },
  pages: {
    path: './Pages',
    lazy: true,
  },
})
