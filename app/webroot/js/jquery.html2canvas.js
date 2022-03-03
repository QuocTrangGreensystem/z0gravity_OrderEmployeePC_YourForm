/**
  @license html2canvas v0.33 <http://html2canvas.hertzen.com>
  Copyright (c) 2011 Niklas von Hertzen. All rights reserved.
  http://www.twitter.com/niklasvh

  Released under MIT License
 */
/*
 * jQuery helper plugin for examples and tests
 */
var GanttData = [];
var GanttCallback = function(){};
(function( $ ){
    var enabled = true;
    var checkList = function(width){
        var $ls = $('#GanttChartDIV .gantt-list');
        if(width){
            $ls.width(width);
        }else{
            $ls.width(Math.max($ls.eq(0).width(),$ls.eq(1).width())+1);
        }
       
    }
    $(function(){
        
        // Milestones
        var bandwidth = $('.gantt .gantt-ms .gantt-line').width()
        var stack =  [],height = 16,icon = 16;
        $('.gantt-line .gantt-msi').each(function(){
            var $element = $(this);
            var $span = $element.find('span');
            
            var left = $element.position().left;
            var width = $span.width();
            var row = 0;
            
            if(left+width+icon >= bandwidth ){
                left -= (width + icon) * 2;
                $span.css('marginLeft' , - (width + icon ));
            }
            $(stack).each(function(k,v){
                if(left >= v){
                    return false;
                }
                row++;
            });
            stack[row] = left+width+icon;
            // $element.css('top' , row* height);
        });
        $('.gantt-ms .gantt-line').height(stack.length * height );
        $('#ExportRows').val(Number($('#ExportRows').val()) + stack.length);
        
        var $wrapper = $('#GanttChartDIV');
        
        if($wrapper.find('.gantt').length < 2){
            return;
        }
        
        var $list = $wrapper.find('table.gantt-list:first'),
        $gantt = $wrapper.find('table.gantt:first');
        var $chart = $wrapper.find('.gantt:last');
        var $side = $wrapper.find('.gantt-list:last');
        checkList();
       
        var $first, $last;
        $list.parent().
        prepend($list =  $('<table class="gantt-list gantt-head-scroll" style="border:0;"></table>').width($list.width()).append($first = $list.find('tr:first').clone()));
        $list.append($last = $('<tr class="gantt-staff"></tr>').
            append($('<td colspan="5" class="gantt-node gantt-child" style="border:0;"></td>').
                append($('<table></table>').
                    append($wrapper.find('.gantt-list:last .gantt-staff:first table:first').find('tr:first').clone()).
                    width($wrapper.find('.gantt-list:first').width()))));
        
        $wrapper.find('.gantt-chart-wrapper').prepend($gantt =  $('<table class="gantt" style="border:0;"></table>').append($gantt.find('tr:first').clone()));
        var timeoutID;
        var setPosition = function(){
            var top = $(window).scrollTop();
            var offset = $gantt.parent().offset().top;
            var css ={};
            if(enabled && top >= offset){
                css = {
                    position:'absolute',
                    top:top-offset+'px',
                    left:0,
                    display: '',
                    zIndex : 1000
                };
            }else{
                css = {
                    position:'',
                    top:'',
                    left:'',
                    display: 'none',
                    zIndex : 1000
                };
            }
            
            if($chart.offset().top >= top){
                $first.show();
                $last.hide();
            }else{
                $first.hide();
                $last.show();
            }
            
            $list.css(css);
            $gantt.css(css);
        };
        
        GanttCallback( $side.find('.gantt-staff'), $chart.find('.gantt-staff'));
        var flyStaffing = function (){
            if(GanttData.length === 0){
                if( GanttDone ){
                    GanttDone.call();
                }
                return;
            }
      //LAZYLOAD ROW DATA
            /*if($(window).scrollTop() + $(window).height() < $chart.offset().top + $chart.height() -100){
                return;
            }*/
            clearTimeout(timeoutID);
            var data = $.map(GanttData.shift(), function(val){
                return $(val);
            });
            $side.append(data[0]);
            $chart.append(data[1]);
            GanttCallback( data[0], data[1]);
            timeoutID = setTimeout(flyStaffing, 10);
        }
        $(window).ready(flyStaffing);
        $(window).scroll(setPosition);
        setPosition();
    });
    
    $(function(){
        var widthContent = $("#mcs_container table.gantt-list:first").outerWidth();
        $("#mcs_container .customScrollBox .container").width(widthContent);
        var widthContent1 = $("#mcs1_container table.gantt:first").outerWidth();
        $("#mcs1_container .customScrollBox .container").width(widthContent1);
        $("#mcs_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes"); 
        $("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes"); 
    });
    
    $.fn.html2canvas = function(options) {
        $('#flashMessage').remove();
        var $overlay = $('#overlay-container'),$loading = $('#overlay-box');
        
        $overlay.show();
        $loading.html('Please wait, Preparing export ...');
        enabled = false;
        
        $.mCustomScrollbarEnabled = false;
        
        $('.dragger_container').hide();
        
        $(window).scrollTop(0);
        
        options = options || {};
        options.elements = this[0];
        options.queue = [];
        var $this = $(this[0]);
        var $wrapper = $this.find('.gantt-chart-wrapper');
        var $side = $this.find('.gantt-side');
        var $gantt = $this.find('.gantt-chart');
        
        var $hidden = {
            show:$.noop,
            hide:$.noop
        };
        
        if($this.find('.gantt').length >= 2){
            $hidden = $this.find('.gantt:last,.gantt-list:last');
        }
        
        
        $wrapper.css({
            overflow:'visible'
        });
        
        $('#GanttChartDIV').find('.customScrollBox').css('overflow','visible').find('.container').css('left',0);
        
        $side.css({
            width: 'auto',
            position:'static',
            'float':'left',
            overflow:'visible'
        });
        $gantt.css({
            margin : 0,
            position:'static',
            'float':'left'
        });
        
        checkList('100%');
        $hidden.hide();
        
        $this.width($gantt.width()+$side.width()+1);
        
        var $canvas;
        var $table = $wrapper.find('.gantt:first');
        
        var width = $table.outerWidth();
        var height = $wrapper.outerHeight();
        
        $table.width(width);
        
        //return;
        setTimeout(function(){
            html2canvas.logging = options && options.logging;
            options.onFinish = function(){
                $canvas = $(html2canvas.Renderer(options.queue, options));
                try {
                    $('#canvasWidth').val(width);
                    $('#canvasHeight').val(height);
                    $('#canvasData').css({
                        top:0,
                        left:0,
                        position:'absolute'
                    }).val($canvas[0].toDataURL()).closest('form').submit();
                    $loading.html('Please wait, downloading ...');
                    if( $.isFunction(options.afterCanvas) ){
                        options.afterCanvas.call($this);
                    }
                    setTimeout(function(){
                        $overlay.hide();
                    },5000);
                } catch(e) {
                    $overlay.hide();
                    if ($canvas[0].nodeName.toLowerCase() === "canvas") {
                        alert("Canvas is tainted, unable to read data");
                    }
                }
                
                $wrapper.css({
                    overflow:''
                });
                $('#GanttChartDIV').find('.customScrollBox').css('overflow','');
                $side.css({
                    width: '',
                    position:'',
                    'float':'',
                    overflow:''
                });
                
                $gantt.css({
                    margin : '',
                    position:'',
                    'float':''
                });
                
                $this.css({
                    width:''
                });
                $table.width('');
                $hidden.show();
                
                $.mCustomScrollbarEnabled = true;
                $(window).resize();
                
                setTimeout(function(){
                    checkList();
                }, 100);
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

