import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { flowtrackCssInputs, splitFlowtrackCss } from './scripts/split-flowtrack-css.mjs';

splitFlowtrackCss();

export default defineConfig({
    plugins: [
        {
            name: 'flowtrack-css-split',
            buildStart() {
                splitFlowtrackCss();
            },
            handleHotUpdate(context) {
                if (context.file.endsWith('/resources/css/flowtrack.css') || context.file.endsWith('/resources/css/app.css')) {
                    splitFlowtrackCss();
                    context.server.ws.send({ type: 'full-reload' });
                    return [];
                }
            },
        },
        laravel({
            input: [
                'resources/css/login.css',
                ...flowtrackCssInputs,
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
