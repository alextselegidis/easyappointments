var autoprefixer = require('autoprefixer'),
    precss = require('precss');

module.exports = {
    entry: './script.js',
    output: {
        path: __dirname,
        filename: 'script.min.js'
    },
    module: {
        loaders: [
            { test: /\.css$/, loader: 'style!css!postcss' },
            { test: /\.js$/, exclude: /node_modules/, loader: 'babel?presets[]=es2015' }
        ]
    },
    postcss: function() {
        return [autoprefixer, precss]; 
    }
};