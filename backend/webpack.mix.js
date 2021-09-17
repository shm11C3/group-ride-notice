const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sourceMaps().js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps();

mix.js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css', {
      sassOptions: {
        includePaths: [
          'node_modules',
          'node_modules/bootstrap-honoka/scss'
        ]
      }
  });

mix.js('resources/js/loginForm.js', 'public/js/loginForm.js').vue();
