<?php $widget_title = !empty( $widget_title) ? $widget_title : __('progression', true); ?>
<div class="wd-widget project-progress-line-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
            <div class="widget-action">
                <a href="javascript:void(0);" onclick="wd_progress_line_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
                <a href="javascript:void(0);" onclick="wd_progress_line_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
            </div>
        </div>
        <div class="widget_content">
            <div class="progress-line">
                <div class="amrs-wrap">
                    <div id ="progress-line" class="progress-line-inner">
                        <div class="progress-label">
                            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span><?php echo __('Consumed', true);?></span></div>
                            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><?php echo __('Planed', true);?></span></div>
                        </div>
                        
                        <div id='chartContainer' style="width:<?php echo ($countLine * 50); ?>px; height:240px">
                        </div>
                        
                    </div>
                    <span id="left" class="scroll-progress scroll-progress-left"></span>
                    <span id="right" class="scroll-progress scroll-progress-right"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-line{
    box-shadow: none;
}
.amrs-wrap{
    padding: 0;
}
.progress-line .amrs-wrap{
    padding-top: 0;
}
.scroll-progress-left{
    left: 0;
}
.scroll-progress-right{
    right: 0;
}
.progress-line .progress-line-inner{
    width: 95%;
    margin: auto;
}
.jqx-rc-all.jqx-button{
    z-index: 9
}
.project-progress-line-widget.fullScreen .progress-line .amrs-wrap{
    display: inline-block;
    position: relative;
}
#tblChart tbody tr:hover td{
	background-color: transparent;
}
#svgChart .jqx-chart-axis-text{
	font-size: 11px;
    color: #666666;
    fill: #666666;
	font-family: "Open Sans";
}
</style>

<script type="text/javascript">


function wd_progress_line_expand(_element){
    var _this = $(_element);
    var _wg_container = _this.closest('.wd-widget');
    _wg_container.addClass('fullScreen');
    _wg_container.closest('li').addClass('wd_on_top');
    _this.hide();
    _wg_container.find('.primary-object-collapse').show();
    
}
function wd_progress_line_collapse(_element){
    var _this = $(_element);
    var _wg_container = _this.closest('.wd-widget');
    _wg_container.removeClass('fullScreen');
    _wg_container.closest('li').removeClass('wd_on_top');
    _this.hide();
    _wg_container.find('.primary-object-expand').show();
}
(function($){
	$("#right, #left").click(function() {
		var dir = this.id=="right" ? '+=' : '-=' ;
		$(this).closest(".amrs-wrap").find('#progress-line').stop().animate({scrollLeft: dir+'500'}, 1000);
	});
	
})(jQuery);
$(document).ready(function () {
    
    // prepare jqxChart settings
    var years    = <?php echo json_encode($setYear); ?>,
        manDays    = <?php echo json_encode($manDays); ?>,
        dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()) ?>;
		
    function caculate(value){
       value = value.from - value.to;
       return  Math.round(value * 100) / 100 ;

    }
    var settings = {
        title: "",
        description: years,
        padding: { left: 5, top: 0, right: 5, bottom: 5 },
        titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
        source: dataSets,
		showBorderLine: false,
        categoryAxis:
            {
                dataField: 'date',
                description: '',
                showGridLines: true,
				color: "#DDDDDD",
            },
        colorScheme: 'scheme02',
        seriesGroups:
            [
                {
                    type: 'splinearea',
                    showLabels: false,//default
                    valueAxis:
                    {
                        axisSize: 'auto',
                        minValue: 0,
                        maxValue: manDays,
                        unitInterval: manDays,
                        description: '',
                        displayValueAxis: false
                    },
                    series: [
                            // { dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                            { dataField: 'consumed', lineWidth: 4, displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 0}, color: '#538FFA'},
                            { dataField: 'validated', lineWidth: 4, displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: 0}, color: '#E44353'}

                        ]
                },
            ]
    };
    // setup the chart
    $('#chartContainer').jqxChart(settings);

    /* Edit by Dai Huynh
    * Show progress-circle popup
    */
    $('.progress-circle-text').on('click', function(){
        var _this = $(this);
        var _cat = _this.data('cat');
        _this.addClass('loading');
        var id = <?php echo $project_id; ?>;
        $.ajax({
            url : "/project_finances_preview/ajax/"+id,
            type: "GET",
            cache: false,
            data: {
                cat: _cat
            },
            success: function (html) {
                _this.removeClass('loading');
                 $('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0});
                var wh= $(window).height();
                if (wh < 768) {
                     $('#contentDialog').css({'max-height':600,'width':'auto'});
                } else {
                     $('#contentDialog').css({'max-height':'none','width':'auto'});
                }
                $('.wd-layout').css('overflow','visible');
                showMe();
                $('#contentDialog').html(html);
            }
        });
    });
    $('#GanttChartDIV').on('click', '.milestones-item', function(e){
        //e.preventDefault();
        var _this = $(this);
        $('#GanttChartDIV').find('.milestones-item').removeClass('active-item');
        $('#GanttChartDIV').find('.flag-item').addClass('last-item');
        var out_date = _this.hasClass('out_of_date');
        if( ! _this.hasClass('milestone-green')){
            _this.removeClass('milestone-blue').removeClass('milestone-mi').addClass('milestone-green');
        }else{
            if( out_date){
                _this.removeClass('milestone-blue').removeClass('milestone-green').addClass('milestone-mi');
            }else{
                _this.removeClass('milestone-green').removeClass('milestone-mi').addClass('milestone-blue');
            }
        }
        $(this).removeClass('last-item');
        $(this).addClass('active-item');
        var _item_id = $(this).data('id');
        $.ajax({
           url : '<?php echo $html->url('/project_milestones/change_milestone_status/' . $project_id); ?>' + '/' + _item_id,
            type : 'GET',
            data : '',
            success : function(data){
                console.log(data);
                var success = true;
                var check_date = 2;
                if( success){
                    
                }
            },
            error: function(){
                location.reload();
            }
        });
    });
    $("#dialogDetailValue").draggable({
        cancel: "#wd-tab-content",
        stop: function(event, ui){
            var position = $("#dialogDetailValue").position();
            $.ajax({
                url : "/projects/savePopupPosition",
                type: "POST",
                data: {
                    top: position.top,
                    left: position.left
                }
            });
            savePosition = position;
        }
    }); 
    // wd_progress_line_collapse

    

});

</script>