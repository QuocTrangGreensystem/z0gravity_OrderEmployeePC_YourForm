<?php 

if(!empty($project_id)){

    $project_info = ClassRegistry::init('Project')->find('first', array(
        'recursive' => -1,
        'fields' => array('project_name', 'start_date', 'end_date'),
        'conditions' => array('id' => $project_id)
    ));

    $projectArms = ClassRegistry::init('ProjectAmr')->find('first', array(
        'recursive' => -1,
        'fields' => array('weather'),
        'conditions' => array('project_id' => $project_id)
    ));

    $projectCurrentDate = abs(strtotime(date('Y-m-d', time())) - strtotime($project_info['Project']['end_date']));
    $projectDate = abs(strtotime($project_info['Project']['end_date']) - strtotime($project_info['Project']['start_date']));
    $initDate = 100;
    if($projectCurrentDate < $projectDate) $initDate = floor(($projectCurrentDate / $projectDate) * 100);

?>
<style>
	.wd-title h2.wd-t1, span.title-global{
		display: none;
	}
	.wd-layout-heading .project-progress .progress-full{
		margin-bottom: 0;
	}
</style>
<?php 
function draw_line_progress($value){
        $_html = '';
        $_color_gray = '#E2E6E8';
        $_color_green = array('#6EAF79', '#89BB92', '#AACCB0', '#C1D6C5', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9' );
        $_color_blue =  array('#6FB0CF', '#87BFDA', '#A3CCE0', '#BBDAE9', '#D6E8F0');
        $_use_color = $value > 50 ? $_color_green : $_color_blue;
        $_index = 1; $_current_color = '';
        for( $_index = 1; $_index <= 10; $_index++){
            $_current_color = $_index*10 <= $value ? $_use_color[(intval($value/10) - $_index)] : $_color_gray;
            $_html .= '<span class="progress-node" style="background: ' . $_current_color . '"></span>';
        }
        return $_html;
    }
    ?>
<div class="wd-layout-heading">
    <ul>
        <?php
            $icon_weather = !empty($projectArms['ProjectAmr']['weather']) ? $projectArms['ProjectAmr']['weather'] : '';
            $url = (!empty($icon_weather)) ? $html->url('/img/new-icon/'.$icon_weather.'.png') : '';
         ?>
        <li><div class="heading-back"><a href="javascript:void(0)" onclick="goBack()"><i class="icon-arrow-left"></i><span><?php echo __('Back', true);?></span></a></div></li>
        <li><div class="heading-weather icon-weather"><img title="weather"  src="<?php echo $url; ?>"/></div></li>
        <li><div class="heading-project-title" title="<?php echo sprintf(__("%s", true), $project_info['Project']['project_name']); ?>" ><?php echo sprintf(__("%s", true), $project_info['Project']['project_name']); ?></div></li>
    </ul>

    <div class="project-progress <?php echo ($initDate > 50) ? 'late-progress' : ''; ?>">
        <span><?php echo __('Project progress', true);?></span>
        <p class="progress-full"> <?php echo draw_line_progress($initDate);?> </p>
    </div>
    <div style="clear: both"></div>
</div>
<script>
function goBack() {
    window.history.back();
}

    var title = $('.heading-project-title');
    var e_width = 0;
    title.closest('li').siblings().each(function(){
        e_width += $(this).width();
        e_width +=4;
    });
    title.css('max-width',title.closest('ul').width() - e_width - title.css('padding-left'));
$(window).on('resize', function(){
    var title = $('.heading-project-title');
    var e_width = 0;
    title.closest('li').siblings().each(function(){
        e_width += $(this).width();
        e_width +=4;
    });
    title.css('max-width',title.closest('ul').width() - e_width - title.css('padding-left'))
});
</script>
<?php } ?>