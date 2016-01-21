// Check user agent device type.
// @link http://stackoverflow.com/a/16755700/1718162
var Environment = {
    // Mobile or desktop compatible event name, to be used with '.on' function.
    TOUCH_DOWN_EVENT_NAME: 'mousedown touchstart',
    TOUCH_UP_EVENT_NAME: 'mouseup touchend',
    TOUCH_MOVE_EVENT_NAME: 'mousemove touchmove',
    TOUCH_DOUBLE_TAB_EVENT_NAME: 'dblclick dbltap',

    isAndroid: function() {
        return navigator.userAgent.match(/Android/i);
    },
    isBlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    isIOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    isOpera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    isWindows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    isMobile: function() {
        return (Environment.isAndroid() || Environment.isBlackBerry() || Environment.isIOS() || Environment.isOpera()
            || Environment.isWindows());
    }
};

$(document).ready(function() {
    $(".fancybox").fancybox();

    var shareUrl = encodeURIComponent('http://easyappointments.org');
    var shareTitle = encodeURIComponent('Easy!Appointments - Open Source Appointment Scheduler')
    var shareText = encodeURIComponent('Use Easy!Appointments as your online appointment '
            + 'scheduler. It is Easy! & Free!');

    $('#facebook').click(function() {
        window.open(
            'https://www.facebook.com/sharer/sharer.php?u=' + shareUrl
                + '&p[title]=' + shareTitle + '&p[summary]=' + shareText,
            'facebook-share-dialog',
            'width=626,height=436');
    });

    $('#google-plus').click(function() {
        window.open(
            'https://plus.google.com/share?url=' + shareUrl,
            '',
            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    });

    $('#twitter').click(function() {
        window.open(
            'https://twitter.com/share?url=' + shareUrl
                + '&text=' + shareText,
            '',
            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    });

    $('#linkedin').click(function() {
        window.open(
            'http://www.linkedin.com/shareArticle?mini=true&url='+ shareUrl
                + '&title=' + shareTitle + '&summary=' + shareText,
            '',
            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
    });

    if (Environment.isMobile()) {
        $('.fadeIn').removeClass('fadeIn'); // mobile devices do not support fade in sections
    }

    $fadeIn = $('.fadeIn');

    if ($fadeIn.length > 0) {
        $fadeIn.viewportChecker({
            classToAdd: 'show',
            offset: 100
        });
    }
});
