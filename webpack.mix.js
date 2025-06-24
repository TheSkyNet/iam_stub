let mix = require('laravel-mix');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');
/*
if (process.env.MIX_SOURCE_MAPS && ! mix.inProduction()) {
    mix.sourceMaps();
}

if (process.env.MIX_BROWSER_SYNC) {
    mix.browserSync(process.env.MIX_BROWSER_SYNC);
}
*/


mix.js('assets/js/app.js', 'public/js')
    .js('assets/js/admin.js', 'public/js')
    .sass('assets/scss/main.scss', 'public/css')
    .sass('assets/scss/admin.scss', 'public/css')
    .sourceMaps()

