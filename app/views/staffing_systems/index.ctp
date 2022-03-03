<?php
    echo $html->script(array(
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drag-2.0.min',
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid/slick.grid',
        'slick_grid_custom',
        'jquery.validation.min',
		'progress/nanobar'
    ));

    echo $html->css(array(
        'jquery.multiSelect',
        'slick_grid/slick.grid',
        'slick_grid/slick.pager',
        'slick_grid/slick.common',
        'slick_grid/slick.edit',
        'jquery.ui.custom',
		'preview/tab-admin',
		'layout_admin_2019'
    ));
    $employee_info = $this->Session->read("Auth.employee_info");
?>
<style type="text/css">
.wd-header th{ line-height:41px; }
.valWorkload{ text-align:right; }
.error{ background:#F00 !important; color:#FFF !important; }
input[type='radio']{ margin-top:3px;}
label{ font-weight:bold; margin-right:10px; margin-top:-3px; }
#table-control{ margin-left:-5px !important; padding-bottom:10px; }
select{ padding:3px 5px}
input[type='button']{ padding:3px 5px; cursor:pointer}
.valueDecimal{ text-align:right}
.wd-bt-big a.wd-hover-advance-tooltip{
	background:url(<?php echo $this->Html->webroot('img/rebuild.jpg'); ?>) 0 2px  !important;
}
.modal{ position:absolute; top:0; left:0; bottom:0; width:100%; height:100%; z-index:99999999999999; background:transparent; display:none }
/*.invalid{background: #F00 !important;}*/
.wd-list-project .wd-tab .wd-content label {
	width: 100px;
	margin-top: 10px;
}
.wd-bt-big a.wd-hover-advance-tooltip:hover {
	background: url(/img/rebuild.jpg) 0 2px !important;
}
</style>
<div class="modal"></div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
				$arrMonth = array(
					'01'=>'Jan',
					'02'=>'Feb',
					'03'=>'March',
					'04'=>'Apri',
					'05'=>'May',
					'06'=>'Jun',
					'07'=>'July',
					'08'=>'Aug',
					'09'=>'Sep',
					'10'=>'Oct',
					'11'=>'Nov',
					'12'=>'Dec',
				);
				$URL = $this->Html->url(array('action'=>'rebuilds'));
				$currentYear = date('Y',time());
				$startYear = $currentYear - 5 ;
				$endYear = $currentYear + 6 ;
				$disable = $done == false ? "disabled='disabled'" : '';
				$checkActivity = $checkProject = '';
				if($keyword == 'Activity')
				{
					$checkActivity = 'checked="checked"';
				}
				else
				{
					$checkProject = 'checked="checked"';
				}
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                        	<?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <h2 class="wd-t3 paddingTop"><?php __('Checking Staffing') ?></h2>
                                <div id="table-control">
                                	<label><input type="radio" value="Project" <?php echo $checkProject; echo $disable; ?> name="typeChecking" /><?php echo __('Project',true); ?></label>
                                    <label><input type="radio" <?php echo $checkActivity; echo $disable; ?> value="Activity" name="typeChecking" /><?php echo __('Activity',true); ?></label>
                                    <?php /*
                                    <select name="checkingMonth" id="checkingMonth">
                                    <option value="-1"> -- None -- </option>
                                    <?php foreach($arrMonth as $key=>$value)
                                    {
                                        ?>
                                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php
                                    }
                                    ?>
                                    </select>
                                    <select name="checkingYear" id="checkingYear">
                                    <option value="-1"> -- None -- </option>
                                    <?php for( $i = $startYear; $i < $endYear; $i++ )
                                    {
                                        $selected = '';
                                        //if($currentYear == $i)	$selected = "selected";
                                        ?>
                                        <option <?php echo $selected; ?> value="<?php echo $i;?>"><?php echo $i;?></option>
                                        <?php
                                    }
                                    ?>
                                    </select>
									*/
									?>

                                    <input type="button" value="<?php echo __('Checking', true) ?>" id="btnCheck"  onclick="setStatusSystem(); resetViewData(); checkingStaffing();" />
                                    <?php echo $this->Session->flash(); ?>
                                </div>
                                <div id="progressBar" class = 'progressBar'>
                                </div>
                                <br clear="all"  />
                                <br />
                                <div class="wd-table" id="project_container" style="width:100%;height:500px;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="action-template" style="display: none;">
    <div style="margin: 0 auto !important; width: 54px;">
        <div class="wd-bt-big">
            <a onclick="rebuild('<?php echo '%1$s'; ?>','<?php echo '%2$s'; ?>');" id="row_<?php echo '%2$s'; ?>"  class="wd-hover-advance-tooltip" href="javascript:;">Rebuild</a>
        </div>
    </div>
</div>
<?php
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}
$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 60,
        'sortable' => true,
        'resizable' => false,
        'noFilter' => 1,
    ),
    array(
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true
    ),
    array(
        'id' => 'workload',
        'field' => 'workload',
        'name' => __('Workload / Consumed from task', true),
        'width' => 260,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    array(
        'id' => 'staffingE',
        'field' => 'staffingE',
        'name' => __('Workload / Consumed in staffing (E)', true),
        'width' => 270,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    array(
        'id' => 'staffingP',
        'field' => 'staffingP',
        'name' => __('Workload / Consumed in staffing (P)', true),
        'width' => 270,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    /*array(
        'id' => 'staffingS',
        'field' => 'staffingS',
        'name' => __('Workload / Consumed in staffing (S)', true),
        'width' => 270,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),*/
	array(
        'id' => 'staffingP2',
        'field' => 'staffingP2',
        'name' => __('Workload staffing (Profile)', true),
        'width' => 270,
        'sortable' => true,
		'formatter' => 'Slick.Formatters.valueDecimal',
        'resizable' => true
    ),
    /*array(
        'id' => 'great',
        'field' => 'great',
        'name' => __('Great', true),
        'width' => 60,
        'sortable' => true,
        'resizable' => true
    ),*/
    array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => __('Rebuild', true),
        'width' => 70,
        'sortable' => false,
        'resizable' => true,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
        ));
$i = 1;
$dataView = array();
$selectMaps = array();

App::import("vendor", "str_utility");
$str_utility = new str_utility();

foreach ($results as $key => $result) {
    $data = array(
        'id' => $key,
        'no.' => $key,
        'MetaData' => array()
    );
    $data['name'] = (string) $result['name'];
    $data['workload'] = (string) $result['workload'] .' / '. (string) $result['consumed'];
    $data['staffingE'] = (string) $result['staffingE'] .' / '. (string) $result['staffingConsumedE'];
    $data['staffingP'] = (string) $result['staffingP'] .' / '. (string) $result['staffingConsumedP'];
    $data['staffingS'] = (string) $result['staffingS'] .' / '. (string) $result['staffingConsumedS'];
	$data['staffingP2'] = (string) $result['staffingP2'];

	$data['staffingConsumedE'] = (string) $result['staffingConsumedE'];
    $data['staffingConsumedP'] = (string) $result['staffingConsumedP'];
    $data['staffingConsumedS'] = (string) $result['staffingConsumedS'];

	$data['cls_staffingE'] = (string) $result['cls_staffingE'];
    $data['cls_staffingP'] = (string) $result['cls_staffingP'];
    $data['cls_staffingS'] = (string) $result['cls_staffingS'];
	 $data['cls_staffingP2'] = (string) $result['cls_staffingP2'];
	$data['rebuild'] = $result['rebuild'];
    $data['action.'] = '';
    $dataView[] = $data;
}
$i18n = array();
?>
<script type="text/javascript">
//function checkingStaffing(){
//	var type = $('input:radio[name = "typeChecking"]:checked').val();
//	var month = $('#checkingMonth').val();
//	var year = $('#checkingYear').val();
//	var data = '';
//	$.ajax({
//		url  : "/staffing_systems/index/ajax/",
//		type : "POST",
//		data : {
//			data:{'type' : type,'month' : month, 'year' : year}
//		},
//		success : function(html){
//			$('#staffing_content').html(html);
//		}
//	});
//}

var progress = 0;
var done = false;
var auto ;

//FIRST TIME EXECUTE STAFFING SYSTEM : init
var nanobar = new Nanobar();
progress = <?php echo json_encode($progress);?>;
progress = <?php echo json_encode($progress);?>;
//END

//PROGRESS
function setValueProgress(progress)
{
	nanobar.go(progress);
	$('#progressBar div div').html(progress + '% &nbsp;');
}
//setValueProgress(progress);
//END PROGRESS


var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
var keyword = '<?php echo $keyword; ?>';

function autoProgressBar()
{
	$('#btnCheck').hide();
	var i = 1;
	auto =setInterval(function(){
		if(i%2==0)
		$('#progressBar div').css('backgroundPosition','25% 50%');
		else
		$('#progressBar div').css('backgroundPosition','50% 50%');
		i++;
		/*if(progress >= 100)
		{
			clearInterval(auto);
			$('input:radio[name = "typeChecking"]').removeAttr('disabled');
			$('#btnCheck').show();
			$('#progressBar').hide();
			return;
		}*/
		progress = parseFloat(progress) + 0.01;
		progress = parseFloat(progress).toFixed(2);
		setValueProgress(progress);
	},500);
}
function setStatusSystem(){
	$('#flashMessage').removeClass('warning');
	$('#flashMessage').addClass('success');
	$('#flashMessage').html(loading + '<?php echo __('In progress...', true) ?>');
	$('#progressBar').show();
	progress = 0;
	setValueProgress(progress);
	done = false;
	$('input:radio[name = "typeChecking"]').attr('disabled','disabled');
	autoProgressBar();
}

var DataValidator = {},ControlGrid,IuploadComplete = function(json){
    var data = ControlGrid.eval('currentEditor');
    data.onComplete(json);
};
function rebuild(key,id){
	$(loading).insertBefore('#row_'+id);
	$.ajax({
		url  : "/staffing_systems/rebuild/"+key+"/"+id,
		type : "POST",
		data : {},
		success : function(data){
			$('#loadingElm').remove();
			if(data == 1)
			$('#row_'+id).remove();
		}
	});
}
(function($){
    $(function(){
        var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
        var actionTemplate =  $('#action-template').html();
        $.extend(Slick.Formatters,{
            Action : function(row, cell, value, columnDef, dataContext){
				//invalid
				if(dataContext.rebuild)
				{
					return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,keyword,dataContext.id), columnDef, dataContext);
				}
            },
			valueDecimal : function(row, cell, value, columnDef, dataContext){
				return '<div class="valueDecimal ">'+value+'</div>'
			}
        });
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0}
        };
        $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
        ControlGrid = $this.init($('#project_container'),data,columns, {showHeaderRow: false});
		$('.wd-bt-big').parent().parent().parent().addClass('invalid');
		resetViewData = function(){
			ControlGrid.getData().setItems([]);
			$('.modal').show();
		};
        checkingStaffing = function(){

			//clearInterval(flag);
			var type = $('input:radio[name = "typeChecking"]:checked').val();
			var month = $('#checkingMonth').val();
			var year = $('#checkingYear').val();
			var data = '';
			$('#flashMessage').html(loading + '<?php echo __('In progress...', true) ?>');
			$.ajax({
				url  : "/staffing_systems/index/ajax/",
				type : "POST",
				data : {
					data:{'type' : type,'month' : month, 'year' : year}
				},
				success : function(data){
					data = JSON.parse(data);
					results = data.results;
					count = data.count;
					done = data.done;
					progress = data.progress;
					keywordTmp = keyword;
					keyword = data.keyword;
					if(results)
					{
						if(keywordTmp == keyword) {
							//var newData = ControlGrid.getData().getItems;
						}
						else {
							//ControlGrid.getData().setItems([]);
							ControlGrid.getData().setItems([]);
							//var newData = new Array();
						}
						var newData = new Array();
						jQuery.each(results, function(ind, val){
							var _newData = {
									'action.': "",
									'id': ind,
									'name': val.name,
									'no.': ind,
									'rebuild': val.rebuild,
									'staffingE': val.staffingE +' / '+ val.staffingConsumedE,
									'staffingP': val.staffingP +' / '+ val.staffingConsumedP,
									//'staffingS': val.staffingS +' / '+ val.staffingConsumedS,
									'staffingP2': val.staffingP2,
									'workload': val.workload +' / '+ val.consumed
								};
								//newData.push(_newData);
								ControlGrid.getData().addItem(_newData);
						});
						//ControlGrid.getData().setItems(newData);
						setValueProgress(progress);
						if(done === false)
						{
							//('#flashMessage').removeClass('warning');
							//$('#flashMessage').addClass('success');
							checkingStaffing();
							$('#flashMessage').html(loading + '<?php echo __('In progress...', true) ?>');
						}
						else
						{
							clearInterval(auto);
							//progress = 0;
							$('input:radio[name = "typeChecking"]').removeAttr('disabled');
							$('#btnCheck').show();
							$('#progressBar').hide();
							if(count == 0)
							{
								$('#flashMessage').removeClass('warning');
								$('#flashMessage').addClass('success');
								$('#flashMessage').html('Done!');
							}
							else
							{

								$('#flashMessage').removeClass('success');
								$('#flashMessage').addClass('warning');
                                var msgP = '<?php echo __('Project invalid!') ?>';
                                var msgA = '<?php echo __('Activity invalid!') ?>';
                                var msg = keyword == 'Project' ? msgP : msgA;
								$('#flashMessage').html('<a href="<?php echo $URL; ?>/'+keyword+'" target="_blank">'+count+' '+msg+'</a>');
							}
							$('.modal').hide();
						}
						$('#flashMessage').show();
					}
				}
			});
        };
		/*if(done === false)
		{
			autoProgressBar();
			var flag = setTimeout(function(){
				checkingStaffing();
			},1500);
		}*/
    });
})(jQuery);
</script>
