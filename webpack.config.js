const path = require('path');
const { createRequire } = require('module');

const appRequire = createRequire(path.resolve(process.cwd(), 'package.json'));
const Encore = appRequire('@symfony/webpack-encore');
const pluginName = 'inpost';

const getConfig = (pluginName, type) => {
    Encore.reset();

    Encore.setOutputPath(`public/build/azarniewicz/${pluginName}/${type}/`)
        .setPublicPath(`/build/azarniewicz/${pluginName}/${type}/`)
        .addEntry(`azarniewicz-${pluginName}-${type}`, path.resolve(__dirname, `./src/Resources/assets/${type}/entry.js`))
        .disableSingleRuntimeChunk()
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .enableSassLoader();

    const config = Encore.getWebpackConfig();
    config.name = `azarniewicz-${pluginName}-${type}`;

    return config;
};

const shopConfig = getConfig(pluginName, 'shop');
const adminConfig = getConfig(pluginName, 'admin');

module.exports = [shopConfig, adminConfig];
