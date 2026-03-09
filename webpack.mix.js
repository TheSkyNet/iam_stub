require('dotenv').config();
let mix = require('laravel-mix');
/*
if (process.env.MIX_SOURCE_MAPS && ! mix.inProduction()) {
    mix.sourceMaps();
}

if (process.env.MIX_BROWSER_SYNC) {
    mix.browserSync(process.env.MIX_BROWSER_SYNC);
}
*/


mix.setPublicPath('public');

mix.webpackConfig({
    stats: 'errors-warnings',
    watchOptions: {
        ignored: [
            '**/node_modules/**',
            '**/public/**',
            '**/vendor/**',
            '**/mix-manifest.json'
        ]
    }
});

mix.js('assets/js/app.js', 'js')
    .postCss('assets/css/main.css', 'css')
    .options({
        clearConsole: false,
        processCssUrls: false,
        postCss: [
            require('@tailwindcss/postcss'),
        ],
        // Place emitted assets in clean public subfolders (absolute within project)
        fileLoaderDirs: {
            images: 'images',
            fonts: 'fonts',
        },
    })
    .disableNotifications()
    .sourceMaps()
