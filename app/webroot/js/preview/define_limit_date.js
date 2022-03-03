(function($) {
    function check(ele_start, ele_end){
        var dialog_calendar = $('.ui-datepicker-calendar');
        var date = dialog_calendar.find('td');
        start_date = $(ele_start).val();
        end_date = $(ele_end).val();
        if(start_date && end_date){
            start_date = new Date(start_date);
            end_date = new Date(end_date);
            $(date).each(function() {
                data_time = new Date($(this).data('time'));
                if(data_time > start_date && data_time < end_date && !$(this).hasClass('ui-state-disabled')){
                    $(this).css({"background-color": "", "border-color": ""});
                    $(this).find('a').css("color", "");
                }else if(($(ele_start).val() == $(this).data('time') || $(ele_end).val() == $(this).data('time')) && !$(this).hasClass('ui-state-disabled')){
                    $(this).css({"background-color": "#247FC3", "border-color": "#fff"});
                    $(this).find('a').css("color", "#fff");
                }
            });
        }
    }
    $.fn.define_limit_date = function(ele_start, ele_end) {
        $('body').on('click', '.ui-datepicker-header', function(e){
            check(ele_start, ele_end);
        });
        $(this).on('click', function(e){
            check(ele_start, ele_end);
        });

        $(ele_start).change(function(){
            min_date = $(ele_start).val();
            $(ele_end).datepicker('option','minDate',min_date);

        });
        $(ele_end).change(function(){
            max_date = $(ele_end).val();
            $(ele_start).datepicker('option','maxDate',max_date);

        });
    }
})(jQuery);

