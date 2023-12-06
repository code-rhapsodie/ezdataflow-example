const Encore = require('@symfony/webpack-encore');
const path = require('path');
const getIbexaConfig = require('./ibexa.webpack.config.js');
const ibexaConfig = getIbexaConfig(Encore);
const customConfigs = require('./ibexa.webpack.custom.configs.js');

Encore.reset();
Encore.setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableSassLoader()
    .enableReactPreset()
    .enableSingleRuntimeChunk()

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    });

// Welcome page stylesheets
Encore.addEntry('app', './assets/app.js');

const projectConfig = Encore.getWebpackConfig();

projectConfig.name = 'app';

module.exports = [ibexaConfig, ...customConfigs, projectConfig];

// uncomment this line if you've commented-out the above lines
// module.exports = [ eZConfig, ibexaConfig, ...customConfigs ];
