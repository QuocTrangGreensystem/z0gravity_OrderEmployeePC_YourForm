function set_slick_table_height(){
	var s_table = $('.wd-table');
	if( typeof wdTable != 'undefined') s_table = wdTable;
	if( !s_table.length) return;
    var heightTable = $(window).height() - s_table.offset().top - parseInt($('.wd-panel').css('padding-bottom')) - parseInt($('.wd-tab').css('margin-bottom')) - 1;
    if( heightTable > 400){
		s_table.css({
			height: heightTable,
		});
	}else{
		s_table.css('height',400);
	}
	$('.slick-viewport').css({
		height:  heightTable - $('.slick-pane-header:first').height() - $('.slick-headerrow-columns:visible:first').height()
    });
}
set_slick_table_height();
$(window).on('load resize',function(){
	set_slick_table_height();
});
$(document).ready(function(){
	set_slick_table_height();
});