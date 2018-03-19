
var Encore      = require('@symfony/webpack-encore');
var uglifier    = require('uglifyjs-webpack-plugin');

Encore

    .setOutputPath('../src/NetBS/CoreBundle/Resources/public/dist/')
    .setPublicPath('/dist')
    .addEntry('app', './assets/js/app.js')
    .enableSassLoader(function(options) {
        options.outputStyle = 'compressed';
    })
    .autoProvidejQuery()
    .enableSourceMaps(!Encore.isProduction)
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()

    .addPlugin(new uglifier())
;

module.exports = Encore.getWebpackConfig();
