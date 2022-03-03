<?php
echo $html->script('jquery.validation.min'); 
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
	
));

echo $html->css(array(
	'jquery.multiSelect',
	'slick_grid/slick.grid',
	'slick_grid/slick.pager',
	'slick_grid/slick.common',
	'preview/slickgrid',
		'layout_2019',
	'preview/layout',
	
));
echo $html->css('jquery.ui.custom'); 
echo $html->css('slick_grid/slick.edit');
echo $html->css('preview/tab-admin');
echo $html->css('layout_admin_2019');

// $employee_info = $this->Session->read("Auth.employee_info");
$icons['embed'] = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" style="top:15px; position:relative;">
		  <rect id="Canvas" width="18" height="18" fill="#ff13dc" opacity="0"/>
		  <path class="svg-b" d="M8.226,14.473a.5.5,0,0,1-.483.371H7.227a.5.5,0,0,1-.472-.666L9.786,2.693a.5.5,0,0,1,.483-.37h.5a.5.5,0,0,1,.472.665Z" fill="#707070"/>
		  <path class="svg-b" d="M17.746,9.53l-4.095,4.16a.5.5,0,0,1-.713,0l-.446-.453a.5.5,0,0,1,0-.7L15.971,9,12.492,5.464a.5.5,0,0,1,0-.7l.446-.454a.5.5,0,0,1,.713,0l4.095,4.16A.76.76,0,0,1,17.746,9.53Z" fill="#707070"/>
		  <path  class="svg-b" d="M.254,8.47l4.1-4.161a.5.5,0,0,1,.713,0l.446.454a.5.5,0,0,1,0,.7L2.029,9l3.479,3.535a.5.5,0,0,1,0,.7l-.446.453a.5.5,0,0,1-.713,0L.254,9.53a.76.76,0,0,1,0-1.06Z" fill="#707070"/>
		</svg>';
?>
<style type="text/css">
	.slick-cell a.circle-name{
		height: 30px;
		line-height: 30px;
		width: 30px;
		display: inline-block;
		text-align: center;
		top: 0;
		color: #fff;
		border-radius: 50%;
		top: inherit;
	}
	.slick-header .slick-header-column{
		text-align: left;
	}
	.wd-content {
		margin-top: 30px;
	}
	.actions {
		margin-bottom: 20px;
	}
	.actions a{
		display: inline-block;
		padding-left: 20px;
		padding-right: 20px;
		height: 30px;
		line-height: 30px;
		border: 1px solid #E1E6E8;
	}
	.actions a:hover{
		text-decoration: none;
		color: #fff;
		background-color: #217FC2;
		border-color: #217FC2;
	}
	.duplicate{
		display: inline-block;
		cursor: pointer;
	}
	.duplicate i{
		font-size: 16px;
	}
	.content-duplicate a{
		margin-left: 20px;
	}
	.content-duplicate input{
		width: 85%;
		border: none;
		
	}
	.grid-canvas .slick-cell.active{
		padding: 0;
		height: 39px;
		border-bottom: 1px solid #F2F5F7;
	}
	.sso-img-button{
		width: 48px;
		height: 48px;
		margin-right: 1px;
		display: inline-block;
		text-align: center;
		background: #fff;
	}
	.sso-img-button:before{
		content: '';
		display: inline-block;
		width: 0;
		height: 100%;
		vertical-align: middle;
	}
	.sso-img-button .wd-icon{
		display: inline-block;
		vertical-align: middle;
	}
	.sso-img-button:hover img.wd-icon-hover{
		display: inline-block;
		vertical-align: middle;
	}
	.sso-img-button:hover img.wd-icon{
		display: none;
	}
	.form-style-2019 >div{
		margin-bottom: 40px;
	}
	.form-style-2019 div h4{
		margin-bottom: 10px;
	}
	.form-style-2019{
		padding: 0 0 20px 0;
	}
	.form-style-2019 .wd-input.wd-custom-checkbox label{
		font-weight: 400;
	}
	.embed-copied{
		margin-left: 5px;
	}
	.embed-copied{
		line-height: 50px;
		color: #2362B8;
		margin-right: 6px;
	}
	.embed-copied:not(.wd-hidden),
	.copy-embed-code{
		display: inline-block;
		vertical-align: middle;
	}
	.copy-group{
		position: absolute;
		right: 0;
		top: 28px;
	}
	.wd-input .copy-embed-code{
		height: 50px;
		width: 50px;
		display: none;
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		border-radius: 3px;
		padding: 0;
		box-sizing: border-box;
		display: inline-block;
		text-align: center;
		transition: all 0.3s ease;
	}
	.wd-input .copy-embed-code:hover .svg-b{
		fill: #2362B8;
	}
	.wd-list-project .wd-tab .wd-content label {
		font-size: 14px;
		width: 100%;
		font-weight: 600;
		color: #424242;
		height:auto;
		float: none;
	}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
								<div id="message-place">
									<?php
									echo $this->Session->flash();
									?>
								</div>
								<div id="access_token_display">
									<?php
									echo $this->Form->create('access_token', array(
										'type' => 'get',
										'class' => 'form-style-2019',
									));
									echo $this->Form->input('access_token', array(
										'div' => 'wd-input',
										'label' => __("Access Token", true),
										'type' =>'text',
										'rel' => 'no-history',
										'after' => '<span class="copy-group"><span class="wd-hidden embed-copied custom-color">'.__('Copied', true).'</span><span class="copy-embed-code" title="'.__('Click to copy embed code', true).'">'. $icons['embed'] .'</span></span>',
										'disabled' => 'disabled',
										'value' => $accessToken
									));
									echo $this->Form->end();
									?>
								</div>
								<?php if( count( $auth_codes) < $limit){ ?>
									<div class="actions">
										<a href="<?php echo $this->Html->url(array('action' => 'updateAuthCode')); ?>"><?php echo __('Create AuthCode', true); ?></a>
									</div>
								<?php } ?>
								<div class="wd-table" id="project_container" style="width:100%;height:400px;">

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
    <div class="action-menu">
        <a onclick="return confirm('<?php echo h(sprintf(__('Delete?', true))); ?>');" class="action-menu-item" href="<?php echo $this->Html->url(array('action' => 'deleteAuthCode', '%1$s')); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20.03 20">
		  <defs>
			<style>
			  .cls-1 {
				fill: #666;
				fill-rule: evenodd;
			  }
			</style>
		  </defs>
		  <path id="suppr" class="cls-1" d="M6644.04,275a0.933,0.933,0,0,1-.67-0.279l-8.38-8.374-8.38,8.374a0.954,0.954,0,1,1-1.35-1.347l8.38-8.374-8.38-8.374a0.954,0.954,0,0,1,1.35-1.347l8.38,8.374,8.38-8.374a0.933,0.933,0,0,1,.67-0.279,0.953,0.953,0,0,1,.67,1.626L6636.33,265l8.38,8.374A0.953,0.953,0,0,1,6644.04,275Z" transform="translate(-6624.97 -255)"/>
		</svg>
		</a>
        
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
        'id' => 'name',
        'field' => 'name',
        'name' => __('Name', true),
        'width' => 250,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
    ),
    array(
        'id' => 'code',
        'field' => 'code',
        'name' => __('Auth code', true),
        'width' => 270,
        'sortable' => true,
        'resizable' => true,
		'formatter' => 'Slick.Formatters.codeCopy'
    ),
    array(
        'id' => 'created',
        'field' => 'created',
        'name' => __('Created', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
    array(
        'id' => 'expires',
        'field' => 'expires',
        'name' => __('Expires', true),
        'width' => 120,
        'sortable' => true,
        'resizable' => true,
        'datatype' => 'datetime',
        'editor' => 'Slick.Editors.datePicker',
        'formatter' => 'Slick.Formatters.DateTime',
    ),
	// array(
        // 'id' => 'user_id',
        // 'field' => 'user_id',
        // 'name' => __('Employee', true),
        // 'width' => 150,
        // 'sortable' => true,
        // 'resizable' => true,
		// 'formatter' => 'Slick.Formatters.userAvatar'
    // ),
	array(
        'id' => 'ip',
        'field' => 'ip',
        'name' => __('IP', true),
        'width' => 170,
        'sortable' => true,
        'resizable' => true,
		'editor' => 'Slick.Editors.textBox',
    ),
	'action.' => array(
        'id' => 'action.',
        'field' => 'action.',
        'name' => '&nbsp;',
        'width' => 70,
        'minWidth' => 50,
        'maxWidth' => 80,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'formatter' => 'Slick.Formatters.Action'
    )
);
$i = 1;
$dataView = array();
$selectMaps = array();
foreach ($auth_codes as $key => $auth_code) {
    $data = array(
        'id' => $key,
        'MetaData' => array()
    );
	$_created = '';
	if( !empty($auth_code['AuthCode']['created'])){
		$_created = new DateTime($auth_code['AuthCode']['created']);
		$_created = $_created->format('d-m-Y');
	}
	$_expires = '';
	if( !empty($auth_code['AuthCode']['expires'])){
		$_expires = new DateTime($auth_code['AuthCode']['expires']);
		$_expires = $_expires->format('d-m-Y');
	}
	$data['name'] = !empty( $auth_code['AuthCode']['name'] ) ? $auth_code['AuthCode']['name'] : '';
	$data['created'] = $_created;
	$data['expires'] = $_expires;
	$data['code'] = $auth_code['AuthCode']['code'];
	$data['user_id'] = $auth_code['AuthCode']['user_id'];
	$data['ip'] = $auth_code['AuthCode']['ip'];
    $dataView[] = $data;
}
$i18n = array();

?>
<script type="text/javascript">
var timeoutID;
function get_grid_option(){
	var _option ={
		frozenColumn: '',
		enableAddRow: false,            
		showHeaderRow: false,
		rowHeight: 40,
		forceFitColumns: true,
		topPanelHeight: 40,
		headerRowHeight: 40
	};

	if( $(window).width() > 992 ){
		return _option;
	}
	else{
		_option.frozenColumn = '';
		_option.forceFitColumns = false;
		return _option;
	}
}
(function($){
	$(function(){
		var $this = SlickGridCustom;
        $this.i18n = <?php echo json_encode($i18n); ?>;
        $this.canModified =  true;
		$this.url = <?php echo json_encode($html->url(array('action' => 'updateAuthCode'))); ?>;
		var actionTemplate =  $('#action-template').html();
		$.extend(Slick.Formatters,{
			userAvatar : function(row, cell, value, columnDef, dataContext){
				avatar = '';
				// if(value && value > 0) {
					// avatar = '<p class="livrable-responsible">'+listAvartar[value]['tag'] + listAvartar[value]['full_name']+'</p>';
				// }
				return value;
				return avatar;
			},
			codeCopy : function(row, cell, value, columnDef, dataContext){
				duplicate = '<div class="content-duplicate"><span type="text">'+value+ '</span><a href="javascript:void(0);" class="duplicate" onclick="copyCode.call(this);"><i class="icon-docs"></i></a></div>';
				return duplicate;
			},
			Action : function(row, cell, value, columnDef, dataContext){
				return Slick.Formatters.HTMLData(row, cell,$this.t(actionTemplate,dataContext.code));
			},
			DateTime : function(row, cell, value, columnDef, dataContext){
				return '<div class="cell-data"><span style="text-align: right">' + value + '</span></div>';
			}
		});
        var data = <?php echo json_encode($dataView); ?>;
        var columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
        $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
        $this.fields = {
            id : {defaulValue : 0},
            name : {defaulValue : 0},
            code : {defaulValue : 0},
            expires : {defaulValue : 0},
            ip : {defaulValue : 0}
        };
        ControlGrid = $this.init($('#project_container'),data,columns, get_grid_option());
	});
})(jQuery);

	function copyCode() {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(this).closest('.content-duplicate').find('span').text()).select();
		document.execCommand("copy");
		$temp.remove();
		var  $mess = '<div id="flashMessage" class="message success">' + '<?php __('Copied');?>' + '<a href="#" class="close">x</a></div>';
		$('#message-place').html($mess)
		if( $('#message-place').is(':hidden')) $('#message-place').slideDown(500);
		clearTimeout(timeoutID);
        timeoutID = setTimeout(function(){
			$('#message-place').slideUp(500);
		}, 5000);
	}
	$(document).ready(function(){
		clearTimeout(timeoutID);
        timeoutID = setTimeout(function(){
			$('#message-place').slideUp(500);
		}, 5000);
	});
	$(window).ready(function(){
		if( Clipboard.isSupported()){
			var copied_timeout=0;
			$('.copy-embed-code').on('click', function(){
				var _this = $(this)
				$('.embed-copied').addClass('wd-hidden');
				var $temp = $("<input>");
				$("body").append($temp);
				var _cont = _this.closest('.wd-input');
				var _input = _cont.find('input');
				$temp.val(_input.val()).select();
				$temp[0].setSelectionRange(0, 99999);
				document.execCommand("copy"); /*For mobile devices*/
				$temp.remove();
				var _text = _cont.find('.embed-copied');
				_text.removeClass('wd-hidden');
				clearTimeout(copied_timeout);
				copied_timeout = setTimeout(function(){
					_text.addClass('wd-hidden');
				}, 3000)
			});
		}else{
			$('.copy-embed-code').hide();
		}
	});
</script>