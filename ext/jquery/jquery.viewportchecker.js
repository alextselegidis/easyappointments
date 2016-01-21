/*
    Version 1.5.0
    The MIT License (MIT)

    Copyright (c) 2014 Dirk Groenen

    Permission is hereby granted, free of charge, to any person obtaining a copy of
    this software and associated documentation files (the "Software"), to deal in
    the Software without restriction, including without limitation the rights to
    use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
    the Software, and to permit persons to whom the Software is furnished to do so,
    subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
*/

(function($){
    $.fn.viewportChecker = function(useroptions){
        // Define options and extend with user
        var options = {
            classToAdd: 'visible',
            offset: 100,
            repeat: false,
            callbackFunction: function(elem, action){},
			scrollHorizontal: false
        };
        $.extend(options, useroptions);

        // Cache the given element and height of the browser
        var $elem = this,
            windowSize = (!options.scrollHorizontal) ? $(window).height() : $(window).width(),
            scrollElem = ((navigator.userAgent.toLowerCase().indexOf('webkit') != -1) ? 'body' : 'html');

        this.checkElements = function(){
        
            // Set some vars to check with
			if(!options.scrollHorizontal){
				var viewportTop = $(scrollElem).scrollTop(),
					viewportBottom = (viewportTop + windowSize);
			}
			else{
				var viewportTop = $(scrollElem).scrollLeft(),
					viewportBottom = (viewportTop + windowSize);
			}
            

            $elem.each(function(){
                var $obj = $(this),
                    objOptions = {},
                    attrOptions = {};

                //  Get any individual attribution data
                if ($obj.data('add'))
                    attrOptions.classToAdd = $obj.data('add');
                if ($obj.data('offset'))
                    attrOptions.offset = $obj.data('offset');
                if ($obj.data('repeat'))
                    attrOptions.repeat = $obj.data('repeat');
                if ($obj.data('scrollHorizontal'))
                    attrOptions.scrollHorizontal = $obj.data('scrollHorizontal');

                $.extend(objOptions, options);
                $.extend(objOptions, attrOptions);

                // If class already exists; quit
                if ($obj.hasClass(objOptions.classToAdd) && !objOptions.repeat){
                    return;
                }

                // define the top position of the element and include the offset which makes is appear earlier or later
                var elemTop = (!objOptions.scrollHorizontal) ? Math.round( $obj.offset().top ) + objOptions.offset : Math.round( $obj.offset().left ) + objOptions.offset,
                    elemBottom = elemTop + ($obj.height());

                // Add class if in viewport
                if ((elemTop < viewportBottom) && (elemBottom > viewportTop)){
                    $obj.addClass(objOptions.classToAdd);

                    // Do the callback function. Callback wil send the jQuery object as parameter
                    objOptions.callbackFunction($obj, "add");
                    
                // Remove class if not in viewport and repeat is true
                } else if ($obj.hasClass(objOptions.classToAdd) && (objOptions.repeat)){
                    $obj.removeClass(objOptions.classToAdd);

                    // Do the callback function.
                    objOptions.callbackFunction($obj, "remove");
                }
            });
        
        };

        // Run checkelements on load and scroll
        $(window).bind("load scroll touchmove", this.checkElements);

        // On resize change the height var
        $(window).resize(function(e){
            windowSize = (!options.scrollHorizontal) ? e.currentTarget.innerHeight : e.currentTarget.innerWidth;
        });
        
        // trigger inital check if elements already visible
        this.checkElements();
        
        return this;
    };
})(jQuery);
