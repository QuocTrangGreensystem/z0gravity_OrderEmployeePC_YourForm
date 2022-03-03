/**
  @license html2canvas v0.33 <http://html2canvas.hertzen.com>
  Copyright (c) 2011 Niklas von Hertzen. All rights reserved.
  http://www.twitter.com/niklasvh

  Released under MIT License
 */
/*
 * jQuery helper plugin for examples and tests
 */
var GanttData = [], Gantt = {};
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
    //edit by Thach 2013-11-25
    // them class de phan biet line
    Gantt = {
        doc : null,
        /**
         * Draw a straight line (colored one-pixel wide DIV), need to parameterize doc item
         * @method sLine
         * @return {Void}
        */
        sline : function(x1,y1,x2,y2,cl){
            var css = {
                position : 'absolute',
                left :  Math.min(x1,x2),
                top : Math.min(y1,y2),
                width : Math.abs(x2-x1) + 1,
                height : Math.abs(y2-y1) + 1,
                overflow : 'hidden',
                zIndex : 0,
                backgroundColor : '#070BED',

            };
            this.doc.append($('<div class="gantt_clears '+ cl +'"/>').css(css));
        },

        /**
         * Draw a diaganol line (calc line x,y pairs and draw multiple one-by-one sLines)
         * @method dLine
         * @return {Void}
        */
        dline : function(x1,y1,x2,y2,cl){

            var vx,vy,i;
            var dx = x2 - x1;
            var dy = y2 - y1;
            var x = x1;
            var y = y1;
            var n = Math.max(Math.abs(dx),Math.abs(dy));
            dx = dx / n;
            dy = dy / n;
            for ( i = 0; i <= n; i++ )
            {
                vx = Math.round(x);
                vy = Math.round(y);
                this.sline(vx,vy,vx,vy,cl);
                x += dx;
                y += dy;
            }
        },
        /**
         * Draw dependency line between two points (task 1 end -> task 2 start)
         * @method drawDependency
         * @return boolean
         */
        draw : function(x1,y1,x2,y2,dt,cl){

            var f = Math.round(x2-x1);
            if(f < 0){
                return false;
            }
            if(y2 - y1 < 0)
            {
                y2+= dt*2;
                this.sline(x1,y1,x1+f,y1,cl);
                this.sline(x1+f,y1,x1+f,y2,cl);
                this.dline(x2+1,y2+3,x2+3,y2+5,cl);
                this.dline(x2+1,y2+2,x2+4,y2+5,cl);
                this.dline(x2-0.6,y2+2,x2-3,y2+5,cl);
                this.dline(x2-0.6,y2+1,x2-4,y2+5,cl);
            }
            else
            {
                this.sline(x1,y1,x1+f,y1,cl);
                this.sline(x1+f,y1,x1+f,y2,cl);
                this.dline(x2+1,y2-3,x2+3,y2-5,cl);
                this.dline(x2+1,y2-2,x2+4,y2-5,cl);
                this.dline(x2-0.6,y2-2,x2-3,y2-5,cl);
                this.dline(x2-0.6,y2-1,x2-4,y2-5,cl);
            }
            return true;
        },
        init : function(){
            this.rdraw();
        },
        rdraw : function(){
            this.doc = $('#GanttChartDIV .gantt-chart-wrapper');
            var dt = this.doc.find('.gantt-line-n[id]:first').height() / 2 ;
            this.doc.find('.gantt-line div[rel]').each(function(){
                var $el = $(this), type = $el.attr('id').indexOf('line-n') !=-1 ? 'n' : 's';
                var caseLine  = 'caseline-' + ($el.attr('id').indexOf('line-n') !=-1 ? 'n' : 's');
                var $des = $('#line-' + type + '-' + $el.attr('rel') , this.doc);
                if($des.length){
                    var x1 = $des.position(),x2 = $el.position();
                    var d1 = $des.parent().position(), d2 = $el.parent().position();
                    Gantt.draw(d1.left+x1.left + $des.width(), d1.top + x1.top + dt, d2.left+x2.left, d2.top+x2.top, dt ,caseLine);
                }
            });
        }
    }

    $(function(){

        // Milestones
        var bandwidth = $('.gantt .gantt-ms .gantt-line').width();
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
                $span.css('marginTop' , '0');
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
        prepend($list =  $('<table class="gantt-list gantt-list-primary gantt-head-scroll" style="border:0;"></table>').width($list.width()).append($first = $list.find('tr:first').clone()));
        $list.append($last = $('<tr class="gantt-staff"></tr>').
            append($('<td colspan="5" class="gantt-node gantt-child" style="border:0;"></td>').
                append($('<table></table>').
                    append($wrapper.find('.gantt-list:last .gantt-staff:first table:first').find('tr:first').clone()).
                    width($wrapper.find('.gantt-list:first').width()))));

        $wrapper.find('.gantt-chart-wrapper').prepend($gantt =  $('<table class="gantt gantt-primary" style="border:0;"></table>').append($gantt.find('tr:first').clone()));
        var timeoutID;
        var setPosition = function(){
            // setTimeout(flyStaffing, 10);
            var _top = $('#wd-fragment-1').scrollTop();
			// var _top = top - parseInt($('.wd-panel').css('padding-top')) - $('.wd-title:first').height() - parseInt($('.wd-title:first').css('margin-bottom'));
            var offset = $gantt.parent().offset().top;
            var css ={};
            if(enabled && _top >= 40){
                css = {
                    position:'absolute',
                    top:_top,
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
            // if($(window).scrollTop() + $(window).height() < $chart.offset().top + $chart.height() -100){
            //     return;
            // }
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
        $('#wd-fragment-1').scroll(setPosition);
        setPosition();
    //setTimeout(flyStaffing, 100);
    });

    $(function(){
        if($('#mcs_container').length){
            var widthContent = $("#mcs_container table.gantt-list:first").outerWidth();
            $("#mcs_container .customScrollBox .container").width(widthContent);
            $("#mcs_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes");
        }
       
		var w_wrap = $('#mcs1_container .gantt-chart-wrapper').outerWidth() - 45;
		var tds = $('#mcs1_container .gantt-num:first').children('td');
		var n = 0;
		$.each(tds, function(e){
			n++;
		});
		$('#mcs1_container tr td[class*="gantt-d"] > div').width(w_wrap/n);
		var widthContent1 = $("#mcs1_container table.gantt:first").outerWidth();
		$("#mcs1_container .customScrollBox .container").width(widthContent1);
        // $("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes");
        $("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","no");
        $("#mcs1_container").css('margin-left', $('#mcs_container').width() + 'px');
		
    });
	$(window).resize(function(){
		var w_wrap = $('#mcs1_container').outerWidth() - 45;
		var tds = $('#mcs1_container .gantt-num:first').children('td');
		var n = 0;
		$.each(tds, function(e){
			n++;
		});
		$('#mcs1_container tr td[class*="gantt-d"] > div').width(w_wrap/n);
		var widthContent1 = $("#mcs1_container table.gantt:first").outerWidth();
		$("#mcs1_container .customScrollBox .container").width(widthContent1);
        // $("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes");
        $("#mcs1_container").css('margin-left', $('#mcs_container').width() + 'px');	
	});
    $(function(){
        Gantt.init();
    });

    $.fn.html2canvas = function(options) {
        $('#flashMessage').remove();
        var $overlay = $('#overlay-container'),$loading = $('#overlay-box');
        var marginLeftBeofreExport = $('#mcs1_container').css('margin-left');

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
                    $('#canvasHeight').val(height + 50);
                    $('#canvasData').css({
                        top:0,
                        left:0,
                        position:'absolute'
                    }).val($canvas[0].toDataURL()).closest('form').submit();
                    $loading.html('Please wait, downloading ...');
                    setTimeout(function(){
                        $overlay.hide();
                    },5000);
                } catch(e) {
                    $overlay.hide();
                    if ($canvas[0].nodeName.toLowerCase() === "canvas") {
                        alert("Canvas is tainted, unable to read data");
                    }
                }

                enabled = true;
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
                $('#mcs1_container').css('margin-left', marginLeftBeofreExport);
            };
            html2canvas.Preload(options.elements,  options);
        },1000);

    };
})( jQuery );
