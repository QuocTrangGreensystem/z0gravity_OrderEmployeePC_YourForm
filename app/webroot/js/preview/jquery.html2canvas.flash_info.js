/**
  @license html2canvas v0.33 <http://html2canvas.hertzen.com>
  Copyright (c) 2011 Niklas von Hertzen. All rights reserved.
  http://www.twitter.com/niklasvh

  Released under MIT License
 */
/*
 * jQuery helper plugin for examples and tests
 */
(function( $ ){


    $.fn.html2canvas = function(options) {
        var $overlay = $('#overlay-container'),$loading = $('#overlay-box');
        $overlay.show();
        $loading.html('Please wait, Preparing export ...');
        options = options || {};
        options.elements = this[0];
        options.queue = [];
        var $wrapper = $('.wd-flash-wrapper');
        var $canvas;
        setTimeout(function(){
            html2canvas.logging = options && options.logging;
            options.onFinish = function(){
               $canvas = $(html2canvas.Renderer(options.queue, options));
                try {
                    $('#canvasWidth').val(1400);
                    $('#canvasHeight').val($wrapper.height());
                    $('#canvasData').css({
                        top:0,
                        left:0,
                        position:'absolute'
                    }).val($canvas[0].toDataURL()).closest('form').submit();
                    $loading.html('Please wait, downloading ...');
                    setTimeout(function(){
                        $overlay.hide();
                        $('.wd-content-left, .flash-field-edit, .flash-field-addnew').css('display', '');
                        $('.wd-flash-wrapper').css({"margin-top": "", "max-width": ""});
                    },1000);
                 
                } catch(e) {
                    $overlay.hide();
                    if ($canvas[0].nodeName.toLowerCase() === "canvas") {
                        alert("Canvas is tainted, unable to read data");
                    }
                }
            }
            options.onRending = function(current,total){
                $loading.html('Exporting ' + Math.round((current/total) * 100)+'% ...');

            }
            options.complete = function(images){
                html2canvas.Parse(options.elements, images, options);
            };
            html2canvas.Preload(options.elements,  options);
        },1000);
    };
})( jQuery );
