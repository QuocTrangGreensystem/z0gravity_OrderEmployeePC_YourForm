(function($){
	jQuery.fn.scrollTo = function(elem, center, middle, callback) { 
        if( typeof elem == 'string' )elem = $(elem);
        var leftAmount, topAmount;
        if( center != null ){
	        leftAmount = $(this).scrollLeft() - $(this).offset().left + elem.offset().left;
	        if( center )leftAmount = leftAmount - $(this).width()/2 + elem.width()/2;
	        $(this).scrollLeft(leftAmount);
	    }
	    if( middle != null ){
	        var topAmount = $(this).scrollTop($(this).scrollTop() - $(this).offset().top + elem.offset().top);
	        if( middle )topAmount = topAmount - $(this).height()/2 + elem.height()/2;
	        $(this).scrollTop(topAmount);
	    }
	    if( typeof callback == 'function' )callback.call(this, leftAmount, topAmount);
        return this; 
    };
})(jQuery);