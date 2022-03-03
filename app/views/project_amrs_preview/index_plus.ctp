<?php
echo $html->script('jshashtable-2.1');
echo $html->script('jquery.numberformatter-1.2.3');
echo $html->script('jquery.formatCurrency-1.4.0');
echo $html->script('jquery.validation.min');
echo $html->css('jquery.multiSelect');
echo $html->script('validateDate');
echo $html->css('dd');
echo $html->script('jquery.dd');
echo $html->css('gantt');
echo $html->css('preview/project_amr');
echo $this->Html->script(array(
    'dashboard/jqx-all',
    'dashboard/jqxchart',
    'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider',
    'chart/highcharts.js',
    'chart/exporting.js',
    'html2canvas',
    'jquery.html2canvas.organization',
    'jquery.scrollTo',
    'autosize.min',
    'gridster/jquery.gridster.min'
));
echo $this->Html->css(array(
    'dashboard/jqx.base',
    'dashboard/jqx.web',
    'gridster/jquery.gridster.min'
));
$EPM_see_the_budget = isset($companyConfigs['EPM_see_the_budget']) && !empty($companyConfigs['EPM_see_the_budget']) ?  true : false;
$md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
?>
<?php
echo $this->Form->create('Export', array('url' => array('controller' => 'project_amrs', 'action' => 'export_pdf'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));

echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
//echo $this->Form->hidden('rows', array('value' => //$rows));
echo $this->Form->end();
?>

<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
$list_wid = array(
    'project_milestones' => 'Milestones',
    'project_pictures' => 'Vision',
    'project_location' => 'Localisation',
    'indicator_assign_object' => 'Participants & Objectifs',
    'project_budget' => 'Budget',
    'project_progress_line' => 'Progression',
    'task' => 'Tâches',
    'project_messages' => 'Messages',
    'planning' => 'Planning',
    'risk' => 'Risques',
    'project_created_value' => 'Créateur valeur'
);

if( !function_exists('wd_layout_setting')){
    function wd_layout_setting($list_wid, $layout_setting){ ?>
        <div class="wd-layout-setting">
            <a href="javascript:void(0);" class="close" title="<?php __('Close')?>"></a>
            <div class="wd-layout-title">
                <h4><?php echo __('Display settings', true); ?></h4>
                <p><?php echo __('Drag and drop to re-arrange the display', true); ?></p>
            </div>
            <div id ="layout-setting" class="layout-setting gridster">
                <ul>
                    <?php
                     foreach ($list_wid as $key => $value) {
                        $row = ($layout_setting && $layout_setting[$key]['row']) ? $layout_setting[$key]['row'] : 1;
                        $display = ($layout_setting && $layout_setting[$key]['display']) ? $layout_setting[$key]['display'] : 0;
                        $col = ($layout_setting && $layout_setting[$key]['col']) ? $layout_setting[$key]['col'] : 1;
                        $sizex = ($layout_setting && $layout_setting[$key]['sizex']) ? $layout_setting[$key]['sizex'] : 1;
                        $sizey = ($layout_setting && $layout_setting[$key]['sizey']) ? $layout_setting[$key]['sizey'] : 1;
                        $name = $value ? $value : '';
                        $class_status = ($display == 1) ? '' : 'disabled';
                     ?>
                    <li  data-widget= "<?php echo $key; ?>" data-row="<?php echo $row; ?>" data-col="<?php echo $col; ?>" data-sizex="<?php echo $sizex; ?>" data-sizey="<?php echo $sizey; ?>" class="<?php echo $class_status; ?>">
                        <p class="layout-name"><?php echo __($name, true); ?></p>
                        <a href="javascript:void(0);" onclick="displayWidget(this);" id="acd<?php echo $key;?>" data-display = '<?php echo $display; ?>' title="<?php __('Display')?>"></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="wd-submit">
                <button onclick="submitSetting(this);return false;" class="btn-form-action btn-ok btn-right" id="btnSave">
                    <span><?php echo __('Save', true); ?></span>
                </button>
                <a class="btn-form-action btn-cancel close" id="reset_button" href="javascript:void(0);">
                    <?php echo __('Cancel', true); ?>
                </a>
            </div>
        </div>
    <?php }
} ?>
<div id="wd-container-main" class="wd-project-detail">
    <div id="chart-wrapper" class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>
            <div class="wd-tab"> 
                <div class="wd-panel">
                    <div id="title-1" class="wd-title">
                        <a href="javascript:void(0);" onclick="openLayoutSetting(this);"  title="<?php __('Setting')?>" class="button-setting btn"></a>
                        <a href="<?php echo $html->url("/project_amrs_preview/exportExcel/" . $projectName['Project']['id']) ?>" class="btn export-excel-icon-all hide-on-mobile" id="export-submit" title="<?php __('Export Excel')?>"></a>
                    </div>
                    <div class="wd-input wd-weather-list">
                        <?php $_disabled = !(($canModified && !$_isProfile) || $_canWrite ) ? 'disabled' : '' ?>
                        <ul>
                            <li class="<?php echo @$this->data["ProjectAmr"]["weather"] == 'sun' ? 'checked' : ''; ?>"><input class="input_weather" checked="true" <?php echo $_disabled ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/new-icon/sun.png') ?>"  /></li>
                            <li class="<?php echo @$this->data["ProjectAmr"]["weather"] == 'cloud' ? 'checked' : ''; ?>"><input class="input_weather" <?php echo $_disabled ?> type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/new-icon/cloud.png') ?>"  /></li>
                            <li class="<?php echo @$this->data["ProjectAmr"]["weather"] == 'rain' ? 'checked' : ''; ?>"><input class="input_weather" <?php echo  $_disabled ?> type="radio" <?php echo @$this->data["ProjectAmr"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/new-icon/rain.png') ?>"  /></li>
                        </ul>
                        <ul style="margin-left: 7px;">
                            <li class="<?php echo @$this->data["ProjectAmr"]["rank"] == 'up' ? 'checked' : ''; ?>"><input class="input_weather" checked="true" <?php echo  $_disabled ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["rank"] == 'up' ? 'checked' : 'checked'; ?> value="up" name="data[ProjectAmr][rank][]" type="radio" /> <img title="Up"  src="<?php echo $html->url('/img/new-icon/project_rank/up.png') ?>"  /></li>
                            <li class="<?php echo @$this->data["ProjectAmr"]["rank"] == 'down' ? 'checked' : ''; ?>"><input class="input_weather" <?php echo $_disabled ?> type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'down' ? 'checked' : ''; ?> value="down" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;" /> <img title="Down" src="<?php echo $html->url('/img/new-icon/project_rank/down.png') ?>"  /></li>
                            <li class="<?php echo @$this->data["ProjectAmr"]["rank"] == 'mid' ? 'checked' : ''; ?>"><input class="input_weather" <?php echo  $_disabled ?> type="radio" <?php echo @$this->data["ProjectAmr"]["rank"] == 'mid' ? 'checked' : ''; ?> value="mid" name="data[ProjectAmr][rank][]" style="width: 25px;margin-top: 8px;"   /> <img title="Mid"  src="<?php echo $html->url('/img/new-icon/project_rank/mid.png');?>"/></li>
                        </ul>
                    </div>
                    <div class="wd-title" style="margin-left: 7px;">
                        <a href="javascript:void(0);" onclick="expandScreen();" class="btn hide-on-mobile" id="expand">
                            <img title="Expand"  src="<?php echo $html->url('/img/new-icon/expand.png') ?>"  />
                        </a>
                    </div>
                    <?php wd_layout_setting($list_wid, $layout_setting); ?>
                
                    <?php
                    //tat ca widget nam trong views/elements/widgets/
                    //mobile version se co dang: mkpi-ten_widget
                    /*
                    =======Cach them moi widget========
                    1. kpi_settings_controller
                        ::get
                            $default
                                ten_widget|01
                                //0 = hide
                                //1 = show
                    2. tao file widget
                    Tat ca code css, js cua widget nen cho vao file widget luon, ko nen de o day
                    */
                    ?>
                    <div id="indicator-layout" class="indicator-layout gridster"><ul>
                        <?php  foreach ($list_wid as $key => $value) {
                            $row = ($layout_setting && $layout_setting[$key]['row']) ? $layout_setting[$key]['row'] : 1;
                            $display = ($layout_setting && $layout_setting[$key]['display']) ? $layout_setting[$key]['display'] : 0;
                            $col = ($layout_setting && $layout_setting[$key]['col']) ? $layout_setting[$key]['col'] : 1;
                            $sizex = ($layout_setting && $layout_setting[$key]['sizex']) ? $layout_setting[$key]['sizex'] : 1;
                            $sizey = ($layout_setting && $layout_setting[$key]['sizey']) ? $layout_setting[$key]['sizey'] : 1;
                            $name = $value ? $value : ''; 
							
							$file = 'widgets'.DS.$key;
							
							// $widget_data = 
                            if($display == 1) { ?>
								<li data-widget= "<?php echo $key; ?>" data-row="<?php echo $row; ?>" data-col="<?php echo $col; ?>" data-sizex="<?php echo $sizex; ?>" data-sizey="<?php echo $sizey; ?>">
									<!-- 
									<p class="layout-name" data-file="<?php echo $file;?>"><?php echo __($name, true); ?></p> 
									-->
									<?php if( file_exists(ELEMENTS . DS . $file . '.ctp') ){
										//echo '<div class="kpi-widget kpi-visible-' . $visible . '">';
										echo $this->element($file, array(
											'type' => $type
										));
										//echo '</div>';
									} ?>
								</li>
                            <?php }
                        } ?>
                    </ul></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->element('dialog_projects');
echo $validation->bind("ProjectAmr");
echo $html->script('jquery.ba-bbq.min');
echo $html->script('jquery.multiSelect');
?>

<style type="text/css">
    .setvalidation{
        border-color: red !important;
    }
</style>
<script language="javascript">

    function submitSetting(){
        var _wid_item = $('#layout-setting').find('li');
        var _data = [];
        i = 0;
        _wid_item.each(function(){
            _row = $(this).data('row');
            _col = $(this).data('col');
            _sizex = $(this).data('sizex');
            _sizey = $(this).data('sizey');
            _widget = $(this).data('widget');
            _display = $(this).find('a').data('display');
            _data[i] = _widget +'|row_'+_row +'-col_'+_col+'-sizex_'+_sizex+'-sizey_'+_sizey+ '|'+ _display;
            i++;
        });
        $.ajax({
            url: "<?php echo $html->url(array('action' => 'save_layout_setting')) ?>",
            type : 'POST',
            data:  { data: JSON.stringify(_data)},
            dataType: 'JSON',
            success: function(response) {
               
            },
            complete: function(){
                // hide loading
                // can change
               
            }
        });
    } 

    $(".input_weather").on('change', function(){
        var field = $(this).attr('name');
        var value = $(this).val();
        $.ajax({
            url: '/project_amrs/updateWeather/',
            type: 'POST',
            data: {
                data: {
                    project_id : <?php echo $project_id ?>,
                    field: field,
                    value: value
                }
            },
        });
    });

    function displayWidget(_this){
        _display = $(_this).data('display');
        if(_display == 1){
            _display = 0;
            $(_this).closest('li').addClass('disabled');
        }else{
            _display = 1;
            $(_this).closest('li').removeClass('disabled');
        }
        $(_this).data('display',_display);
        // $(_this).attr('data-display',_display);

    }

    var ly_setting;
    function openLayoutSetting(_this){
        $('.wd-layout-setting').toggleClass('open');
        ly_setting = $(".layout-setting ul").gridster({
            namespace: '#layout-setting',
            widget_base_dimensions: [290, 140],
            widget_margins: [10, 10],
            cols : 2,
            max_cols: 2,
            resize: {
                enabled: true
            }
        }).data('gridster');
    } 
    $( document ).ready(function() {
        var gridster;
        _wd_base_width = Math.round(($('#indicator-layout').width() - 50) / 2) ;
        gridster = $(".indicator-layout > ul").gridster({
            namespace: '#indicator-layout',
            widget_base_dimensions: ['auto' , 172],
            widget_margins: [20, 20],
            cols : 2,
            max_cols: 2,
            resize: { enabled: false},
            draggable: {ignore_dragging: true},
        }).data('gridster');
  
        setTimeout(function(){
            var _widget_item = $('#indicator-layout').find('li');
            _widget_item.each(function(){
                _wd_height = $(this).find('.wd-widget').height();
                _wd_width = $(this).width();
                _wd_row = $(this).data('row');
                if(_wd_height){
                    gridster.fit_to_content_width_responsive( $(this), _wd_width , _wd_height,_wd_row);
                } 
            });
        }, 1000);

        $('.wd-weather-list li').click(function(){
            _parent = $(this).closest('ul');
            _parent.find('li').removeClass('checked');
            $(this).addClass('checked');
            $(this).find('.input_weather').attr('checked','checked');
            $(this).find('.input_weather').change();
        });

        
        $('.close').click(function(){
             $('.wd-layout-setting').removeClass('open');
        });
        $('body').click(function(e){
            if(!( $(e.target).hasClass('button-setting') || $('.button-setting').find(e.target).length || $(e.target).hasClass('wd-layout-setting') || $('.wd-layout-setting').find(e.target).length)){
                $('.wd-layout-setting').removeClass('open');
            }
        });

    });

</script>

<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        <?php echo __('Please wait, Preparing export ...', true); ?>
    </div>
</div>
