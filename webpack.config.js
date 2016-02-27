var debug = process.env.NODE_ENV !== 'production',
    webpack = require('webpack'),
    autoprefixer = require('autoprefixer'),
    precss = require('precss'),
    nesting = require('postcss-nesting'),
    color = require('postcss-color-function');

module.exports = {
    entry: './script.js',
    output: {
        path: __dirname,
        filename: 'bundle.js'
    },
    module: {
        loaders: [
            { test: /style.css$/, loader: 'style!css!postcss' },
            { test: /node_modules\/.*.css$/, loader: 'style!css' },
            { test: /\.js$/, exclude: /node_modules/, loader: 'babel?presets[]=es2015' },
            { test: /\.(woff|woff2|ttf|svg|png|jpe?g|gif)(\?\S*)?$/, 
                loader: 'url?limit=100000&name=assets/asset-[hash].[ext]' },
            { test: /\.(eot)(\?\S*)?$/, loader: 'file?name=assets/asset-[hash].[ext]'}
        ]
    },
    plugins: debug ? [] : [
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.OccurenceOrderPlugin(),
        new webpack.optimize.UglifyJsPlugin({ sourcemap: false })
    ],
    postcss: function() {
        return [precss, color, autoprefixer, nesting]; 
    }
};