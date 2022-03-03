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
    $(function(){
        
        
        var widthContent = $("#mcs_container table.gantt-list:first").outerWidth();
        $("#mcs_container .customScrollBox .container").width(widthContent);
        var widthContent1 = $("#mcs1_container table.gantt:first").outerWidth();
        $("#mcs1_container .customScrollBox .container").width(widthContent1);
        var widthWindow = $(window).width();
        $("#mcs1_container .dragger_container").width(widthWindow - 340);
        $("#mcs_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes"); 
        $("#mcs1_container").mCustomScrollbar("horizontal",500,"easeOutCirc",0,"fixed","yes"); 
        
        var $wrapper = $('#GanttChartDIV');
  
        $wrapper.find('.gantt-list:first').parent().
        append($('<table class="gantt-list" style="border:0;"><tr class="gantt-staff"><td colspan="5" class="gantt-node gantt-child" style="border:0;"><table id="gantt-side-id1"></table></td></tr></tbody></table>'));//,
        var $list = $('#gantt-side-id1').append($wrapper.find('.gantt-side .gantt-staff:first table:first').find('tr:first').clone()).width($wrapper.find('.gantt-list:first').width()).hide();
        
        $wrapper.find('.gantt-chart-wrapper').
        append($('<table class="gantt"><tr class="gantt-staff"><td colspan="5" class="gantt-node gantt-child"><table id="gantt-id1"></table></td></tr></tbody></table>'));//,
        var $gantt = $('#gantt-id1').append($wrapper.find('.gantt-chart-wrapper .gantt-staff:first table:first').find('tr:first').clone()).hide();
        
        var $chart = $wrapper.find('.gantt:first');
        var $side = $wrapper.find('.gantt-list:first');
        var timeoutID;
        
        var setPosition = function(){
            flyStaffing();
            var top = $(window).scrollTop();
            var offset = $wrapper.offset().top;
            var css ={};
            if(enabled && top >= offset){
                css = {
                    position:'absolute',
                    top:top-offset+'px',
                    left:0
                };
                $list.show();
                $gantt.show();
            }else{
                css = {
                    position:'',
                    top:'',
                    left:''
                };
                $list.hide();
                $gantt.hide();
                
            }
            $list.css(css);
            $gantt.css(css);
        };
        
        GanttCallback( $side.find('.gantt-staff'), $chart.find('.gantt-staff'));
        
        var flyStaffing = function (){
            if(GanttData.length === 0){
                return;
            }
            clearTimeout(timeoutID);
            if($(window).scrollTop() + $(window).height() < $chart.offset().top + $chart.height() -100){
                return;
            }
            clearTimeout(timeoutID);
            var data = $.map(GanttData.shift(), function(val){
                return $(val);
            });
            $side.append(data[0]);
            $chart.append(data[1]);
            GanttCallback( data[0], data[1]);
            timeoutID = setTimeout(flyStaffing, 10);
        }
        $(window).scroll(setPosition);
        setPosition();
    });
    
})( jQuery );

