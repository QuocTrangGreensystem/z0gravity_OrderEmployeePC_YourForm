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
        var scrollCurrent = $(".wmd-view").scrollLeft();

        $overlay.show();
        $loading.html('Please wait, Preparing export ...');
        $(window).scrollTop(0);
        options = options || {};
        options.elements = this[0];
        options.queue = [];
        var $wrapper = $('#chart-wrapper');
        $wrapper.addClass('exportGantt');
        
    //     $wrapper.css({
    //         width: '1400px',
    //        overflowX:'visible',
    //    });
        var $canvas;

        // return;
        setTimeout(function(){
            html2canvas.logging = options && options.logging;
            options.onFinish = function(){
               $canvas = $(html2canvas.Renderer(options.queue, options));
                try {
                    $('#canvasWidth').val(1400);
                    $('#canvasHeight').val($wrapper.height());
					$('.img_budget_export').css('display', '');
                    $('#canvasData').css({
                        top:0,
                        left:0,
                        position:'absolute'
                    }).val($canvas[0].toDataURL()).closest('form').submit();
                    $loading.html('Please wait, downloading ...');
                    setTimeout(function(){
                        $overlay.hide();
                        $('.wmd-view-topscroll').css('overflow-x', 'auto');
                        $(".wmd-view-topscroll").scrollLeft(scrollCurrent);
                        $('#wd-tab-content').addClass('normal-scroll');
                        $('#chart-wrapper').css('overflow', 'hidden');
                        $('#chart-wrapper').css('margin', '0 auto');
                        $('body').css('overflow', 'hidden');
                        $('#project-name').css('display', 'none');
                        $('#img_location').css('display', 'none');
                        $('#wd-fragment-2').css('display', '');
                        $('#svg_inve_chard').css('display', '');
                        $('#svg_circle_chard').css('display', '');
                        $('#svg_kpi_1').css('display', '');
                        $('#svg_fy_1').css('display', '');
                        $('#svg_fy_2').css('display', '');
                        $('#svg_fy_3').css('display', '');
                        $('.progress-label').css('display', '');
                        $('.scroll-progress').css('display', '');
                        $('#svg_finance_two_plus').css('display', '');
                        $('#label_fy').css({'position':'', 'margin-top': '0'});
                        $('.svg_budget').css('display', '');
                        $('.img_budget_export').css('display', 'none');
                        $('.weather-pdf').css('display', 'none');
                        $('.element-empty').remove();
                        collapseScreen();
                        scrollPage();
                    },5000);
                } catch(e) {
                    $overlay.hide();
                    $('.wmd-view-topscroll').css('overflow-x', 'auto');
                    $(".wmd-view-topscroll").scrollLeft(scrollCurrent);
                    if ($canvas[0].nodeName.toLowerCase() === "canvas") {
                        alert("Canvas is tainted, unable to read data");
                    }
                }
                $wrapper.removeClass('exportGantt');
                $wrapper.css({
                    overflowX:'auto'
                });
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
