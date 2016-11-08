'use strict';

const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const precss = require('precss');
const color = require('postcss-color-function');

const debug = process.env.NODE_ENV !== 'production';
const plugins = [];

plugins.push(new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
    'window.jQuery': 'jquery'
}));

if (!debug) {
    plugins.push(new webpack.optimize.DedupePlugin());
    plugins.push(new webpack.optimize.OccurenceOrderPlugin());
    plugins.push(new webpack.optimize.UglifyJsPlugin({sourcemap: false}));
}

module.exports = {
    entry: './script.js',
    output: {
        path: __dirname,
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            {test: /style.css$/, loader: 'style!css!postcss'},
            {test: /node_modules.*.css$/, loader: 'style!css'},
            {test: /\.js$/, exclude: /node_modules/, loader: 'babel?presets[]=es2015'},
            {test: /\.(woff|woff2|ttf|svg|png|jpe?g|gif)(\?\S*)?$/,
                loader: 'url?limit=100000&name=assets/asset-[hash].[ext]'},
            {test: /\.(eot)(\?\S*)?$/, loader: 'file?name=assets/asset-[hash].[ext]'}
        ]
    },
    plugins,
    postcss: () => [precss, color, autoprefixer]
};