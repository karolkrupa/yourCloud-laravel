let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    module: {
        loaders: [
            {
                test: /\.html$/,
                loader: "underscore-template-loader",
            }
        ]
    },
});

mix.js('resources/assets/js/laravelBootstrap.js', 'public/js')
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/fontawesome-all.js', 'public/js')
    .js('resources/assets/js/folderView/folderViewApp.js', 'public/js/folderView.js')
    .js('resources/assets/js/settingsView/settingsView.js', 'public/js/settingsView.js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/loginPage.scss', 'public/css')
   .sass('resources/assets/sass/settingsPage/settingsPage.scss', 'public/css');
   // .sass('resources/assets/sass/folder_page.scss', 'public/css');

mix.copyDirectory('resources/assets/images', 'public/images');
