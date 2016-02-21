var autoprefixer = require('autoprefixer'),
    precss = require('precss');

module.exports = {
    entry: './script.js',
    output: {
        path: __dirname,
        filename: 'bundle.js'
    },
    devtool: 'inline-source-map',
    module: {
        loaders: [
            { test: /\.css$/, loader: 'style-loader!css-loader!postcss-loader' },
            { test: /\.js$/, exclude: /node_modules/, loader: 'babel?presets[]=es2015' },
            { test: /\.(woff|woff2|ttf|svg|png|jpe?g|gif)(\?\S*)?$/, 
                loader: 'url?limit=100000&name=assets/asset-[hash].[ext]' },
            { test: /\.(eot)(\?\S*)?$/, loader: 'file?name=assets/asset-[hash].[ext]'}
        ]
    },
    postcss: function() {
        return [autoprefixer, precss]; 
    }
};