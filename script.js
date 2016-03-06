'use strict';

// Require Assets
require('./node_modules/bootstrap/dist/css/bootstrap.min.css');
require('./style.css');
window.jQuery = window.$ = require('./node_modules/jquery/dist/jquery.min.js');
require('./node_modules/bootstrap/dist/js/bootstrap.min.js');

// Page Initialization
$(() => {
    if ($('.fancybox').length > 0) { // Load fancybox only when it's needed.
        require([
            './node_modules/fancybox/dist/css/jquery.fancybox.css',
            './node_modules/fancybox/dist/helpers/css/jquery.fancybox-buttons.css',
            './node_modules/fancybox/dist/helpers/css/jquery.fancybox-thumbs.css',
            './node_modules/fancybox/dist/js/jquery.fancybox.js',
            './node_modules/fancybox/dist/helpers/js/jquery.fancybox-buttons.js',
            './node_modules/fancybox/dist/helpers/js/jquery.fancybox-media.js',
            './node_modules/fancybox/dist/helpers/js/jquery.fancybox-thumbs.js'
        ], function() {
            $('.fancybox').fancybox();
        });
    }

    // Fade out the body whenever we click on an external link.
    const excluded = [
        '.fancybox',
        '.fancybox-nav',
        '.fancybox-item',
        '.ea-button.primary',
        '.upcoming a'
    ];

    $('body').on('click', 'a:not(' + excluded.join(',') + ')', function(e) {
        e.preventDefault();

        $('body').fadeOut('fast', 'linear', () => {
            location.href = $(this).attr('href');
        });
    });

    // Give Webpack some time to load the styles.
    $('.loading').fadeOut(850);
});
