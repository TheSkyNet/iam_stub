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


mix.js('assets/js/App.js', 'public/js')
    .js('assets/js/Admin.js', 'public/js')
    .sass('assets/scss/ImaLab.scss', 'public/css')
    .sass('assets/scss/Admin.scss', 'public/css')
    .sourceMaps()

