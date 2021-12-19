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
  .js('resources/js/submit.js', 'public/js')
  .js('resources/js/constants/constant.js', 'public/js')
  .js('resources/js/constants/user.js', 'public/js/userConstant.js')
  .js('resources/js/constants/ride.js', 'public/js/rideConstant.js')
  .sass('resources/sass/app.scss', 'public/css', {
      sassOptions: {
        includePaths: [
          'node_modules',
          'node_modules/bootstrap-honoka/scss'
        ]
      }
  });


mix.js('resources/js/home.js', 'public/js/home.js').vue();
mix.js('resources/js/loginForm.js', 'public/js/loginForm.js').vue();
mix.js('resources/js/registerForm.js', 'public/js/registerForm.js').vue();
mix.js('resources/js/createRideForm.js', 'public/js/createRideForm.js').vue();
mix.js('resources/js/rideAdmin.js', 'public/js/rideAdmin.js').vue();
mix.js('resources/js/rideDetail.js', 'public/js/rideDetail.js').vue();
mix.js('resources/js/userConfig.js', 'public/js/userConfig.js').vue();
mix.js('resources/js/deleteUser.js', 'public/js/deleteUser.js').vue();
mix.js('resources/js/participated.js', 'public/js/participated.js').vue();
mix.js('resources/js/search.js', 'public/js/search.js').vue();
mix.js('resources/js/meetingPlaceRegister.js', 'public/js/meetingPlaceRegister.js').vue();
mix.js('resources/js/registerRideRoute.js', 'public/js/registerRideRoute.js').vue();
mix.js('resources/js/userProfile.js', 'public/js/userProfile.js').vue();
mix.js('resources/js/registerOAuthForm.js', 'public/js/registerOAuthForm.js').vue();
mix.js('resources/js/deleteOAuthUser.js', 'public/js/deleteOAuthUser.js')
