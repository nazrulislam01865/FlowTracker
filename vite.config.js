import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import {
    flowtrackCssDependencies,
    flowtrackCssInputs,
    splitFlowtrackCss,
} from './scripts/split-flowtrack-css.mjs';

splitFlowtrackCss();

export default defineConfig({
    plugins: [
        {
            name: 'flowtrack-css-split',
            buildStart() {
                splitFlowtrackCss();
            },
            handleHotUpdate(context) {
                const normalizedFile = context.file.replaceAll('\\', '/');
                const dependencies = new Set(
                    flowtrackCssDependencies().map((file) => file.replaceAll('\\', '/'))
                );

                if (dependencies.has(normalizedFile)) {
                    splitFlowtrackCss();
                    context.server.ws.send({ type: 'full-reload' });
                    return [];
                }
            },
        },
        laravel({
            input: [
                'resources/css/login.css',
                'resources/css/legacy/prelude.css',
                ...flowtrackCssInputs,
                'resources/css/legacy/shell-a.css',
                'resources/css/modules/dashboard/legacy-prototype.css',
                'resources/css/legacy/shell-b.css',
                'resources/css/migration/components.css',
                'resources/css/modules/orders/index.css',
                'resources/css/modules/work/index.css',
                'resources/css/modules/setup/index.css',
                'resources/css/modules/dashboard/migration.css',
                'resources/css/modules/documents/filters.css',
                'resources/css/modules/inquiries/filters.css',
                'resources/css/modules/clients/filters.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
