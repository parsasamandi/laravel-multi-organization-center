const mix = require('laravel-mix');
const CompressionPlugin = require('compression-webpack-plugin');

mix.setPublicPath('public');
mix.setResourceRoot('../');

// Mix
mix.js('resources/assets/js/app.js',  'public/js')
    .sass('resources/assets/sass/app.scss','public/css')
    // Authentication
    .sass('resources/assets/sass/auth.scss','public/css/auth.css')
    // Home js
    .js('resources/assets/js/main.js','public/js/main.js')
    // Isotope
    .js('resources/assets/js/isotope.js','public/js/isotope.js')
    // Ajax request handler
    .scripts('resources/assets/js/RequestHandler.js', 'public/js/RequestHandler.js')
    // Utilities 
    .scripts('resources/assets/js/utils.js', 'public/js/utils.js')
    // Fonts
    .copy('resources/assets/fonts','public/fonts')
    // Images
    .copy('resources/assets/images','public/images')
    // DataTable Persian language
    .copy('resources/assets/js/persian.json','public/js/persian.json')
    // DataTable English language
    .copy('resources/assets/js/english.json','public/js/english.json');

mix.sourceMaps();
mix.version();
mix.extract();
mix.disableNotifications();