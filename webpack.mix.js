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


mix.js('assets/js/app.js', 'public/js')
    .sass('assets/scss/main.scss', 'public/css')
    .options({
        postCss: [
            require('tailwindcss'),
            require('autoprefixer'),
        ],
        // Place emitted assets in clean public subfolders (absolute within project)
        fileLoaderDirs: {
            images: 'public/images',
            fonts: 'public/fonts',
        },
    })
    .sourceMaps()
