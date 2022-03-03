<?php
echo $html->script(array(
    'history_filter',
    'jquery.validation.min',
    'html2canvas',
    'jquery.html2canvas.yourform',
    'vis.min',
    'dashboard/jqx-all',
    'dashboard/jqxchart',
    'draw-progress',
	'dropzone.min',
	'easytabs/jquery.easytabs.min',
	'jquery.multiSelect',
	'sortable/Sortable.min',
	'sortable/z0-jquery-sortable',
	'tinysort/tinysort.min',
	// 'tinysort/tinysort.charorder.min',
));
echo $html->css(array(
    'gantt_v2_1',
    // 'business',
    'vis.min',
	'layout_2019',
    'preview/grid-project',
	'projects',
	'preview/projects_map',
	'dropzone.min',
	'jquery.multiSelect',
	'preview/datepicker-new',
));
echo $this->element('dialog_detail_value');
echo $this->element('dialog_projects');
?>
<script type="text/javascript">
	// HistoryFilter.auto = false;
	HistoryFilter.here = '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url = '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
	body{
		font-family: "Open Sans";
	}
	.wd-full-popup .popup-tab{
		display: none;
	}
    .nav-preview__item .nav-account{width: 180px;}
    .wd-project-filter .wd-project-filter-container{
		max-width: 1890px;
		margin: 0 auto; 
		position: relative;
		padding-right: 50px;
	} 
	/* Edit by huynh */ 
	.circle-name, #add-employee{
		width: 40px;
		height: 40px;
		border-radius: 50%;
		overflow: hidden;
		background-color: #72ADD2;
		color: #fff;
		text-transform: uppercase;
		font-size: 16px;
		text-align: center;
		line-height: 40px;
		font-weight: bold;
		display: inline-block;
		vertical-align: top;
	}
	.multiselect-filter{
		display: inline-block;
		vertical-align: top;
		position: relative;
		
	}
	.multiselect-filter .wd-combobox-filter{
		box-sizing: border-box;
		width: 190px;
		display: inline-block;
		height: 40px;
		border: 1px solid;
		vertical-align: top;
		border: 1px solid #e1e6e8;
		border-radius: 3px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		color: #424242;
		padding-left: 5px;
	}
	.multiselect-filter .wd-combobox-filter span{
		display: inline-block;
	}
	.multiselect-filter .list_multiselect{
		padding: 0 10px 10px 10px;
		position: absolute;
		top: 100%;
		width: 100%;
		background-color: #fff;
		border: 1px solid #ddd;
		z-index: 1;
		box-sizing: border-box;
		max-height: 250px;
		overflow: auto;
	}
	.multiselect-filter .list_multiselect input{
		width: 15px;
		box-sizing: border-box;
		padding: 0;
		height: 15px;
		background: transparent;
		background-color: transparent;
		box-shadow: none;
		padding: 0;
		margin: 0;
		vertical-align: middle;
	}
	.multiselect-filter .list_multiselect .listMulti{
		line-height: 25px;
	}
	.multiselect-filter .list_multiselect span{
		font-size: 13px;
		vertical-align: middle;
		display: inline-block;
		max-width: 125px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		
	}
	.circle-name a, #add-employee a, .circle-name span{color: #fff;font-weight: 600;}
	.circle-name a:hover, #add-employee a:hover{text-decoration: none;}
	.progress-slider{position: relative;display: block;text-align: left;}
	#wd-container-header #sub-nav{
		display: none;
	}
	#layout .wd-project-filter {
		width: calc( 100% - 20px) ;
		left: 0;
		position: relative;
		top: 0;
	}
	body #layout{
		min-width: 320px;
	}
	body #wd-container-main{
		position: relative;
	}
	body #wd-container-main .wd-layout.wd-project-grid{
		padding: 40px 40px 0;
		width: calc( 100% - 80px);
		max-width: 1410px;
		margin: auto;
	}
	.container-portfolio{
		position: relative;
	}
	.portfolio-item .portfolio-item-inner{
		width: 100%;
		box-shadow: 0px 0px 15px #ddd;
		margin-bottom: 40px;
	}
	.container-portfolio{
		margin: 0 -15px;
	}
	.container-portfolio .portfolio-item{
		box-sizing: border-box;
		padding: 0 15px;
		width: 100%;
		box-shadow: inherit;
		margin-bottom: 0;
		float: left;
		box-sizing: border-box;
	}
	@media( min-width: 768px){
		.container-portfolio .portfolio-item{
			width: 50%;
		}
	}
	@media( min-width: 992px){
		.container-portfolio .portfolio-item{
			width: 33.333333%;
		}
	}
	@media( min-width: 1200px){
		.container-portfolio .portfolio-item{
			width: 25%;
		}
	}

	/* End Edit by huynh */ 
	/* Dropdown */
	.list-type-project{
		display: inline-block;
		vertical-align: middle;
	}
	.wd-dropdown{
		position: relative;
		line-height: 38px;
		font-size: 14px;
		height: 38px;
		background-color: #FFFFFF;
		min-width: 160px;
		font-weight: 400;
		z-index: 10;
		border: 1px solid #E0E6E8;
	}
	.wd-dropdown .wd-caret{
		width: 30px;
		height: 100%;
		position: absolute;
		right: 0;
		top: 0;
		background: url(/img/new-icon/down.png) center no-repeat;
		float: right;
		z-index: 1;
	}
	.wd-dropdown >.popup-dropdown{
		position: absolute;
		top: calc( 100% + 1px);
		left: 0;
		/* width:  100%; */
		background: #fff;
		box-shadow: 1px 2px 7px rgba(29, 29, 27, 0.25);
		display: none;
		-webkit-animation: 0.3s lightUp;
		animation: 0.3s lightUp;
	}
	.wd-dropdown.open >.popup-dropdown{
		display: block;
	}
	.wd-dropdown .selected{
		display: block;
		padding: 0 30px 0 10px;
		overflow: hidden;
		text-overflow: ellipsis;
		font-size: inherit;
		font-weight: inherit;
		text-decoration: none;
		z-index: 2;
		position: relative;
		white-space: nowrap;
		text-transform: capitalize;
	}
	.list-type-project .wd-dropdown .selected{
		max-width: 200px;
		text-transform: uppercase;
	}
	.wd-dropdown ul li a{
		display: block;
		height: 38px;
		font-weight: 400;
		padding: 0 10px;
		border-bottom: 1px solid #E1E6E8;
		text-decoration: none;
		/*transition: all 0.3s ease;*/
		white-space: nowrap;
	}
	.wd-dropdown ul li.active,
	.wd-dropdown ul li:hover{
		background: #247fc3;
		color: #fff;
	}
	.wd-dropdown ul li.active svg .svg-color-path,
	.wd-dropdown ul li:hover svg .svg-color-path{
		fill: #fff;
	}
	@-webkit-keyframes lightUp {
		0%   {margin-top: 20px;}
		60%  {margin-top: 5px;}
		100% {margin-top: 0;}
	}
	@keyframes lightUp {
		0%   {margin-top: 20px;}
		60%  {margin-top: 5px;}
		100% {margin-top: 0;}
	}
	.wd-dropdown form{
		padding: 15px;
	}
	.wd-dropdown form li .wd-custom-checkbox{
		padding: 0 10px;
	}
	/* .wd-dropdown form li{
		line-height: 23px;
	} */

	.wd-dropdown form .wd-input{
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.btn-right {
		float: right;
	}
	form .btn-form-action{
		font-size: 14px;
		line-height: 22px;
		font-weight: 600;
		text-transform: uppercase;
		color: #fff;
		border: none;
		padding: 14px 17px;
		background-color: #C6CCCF;
		transition: all 0.3s ease;
		border-radius: 3px;
		text-decoration: none;
		background-size: 250%;
		background-image: linear-gradient(to right,#C6CCCF 0%,#C6CCCF 39%,#217FC2 64%,#217FC2 100%);
		display: inline-block;
	}
	form .btn-form-action.btn-ok {
		background: #217FC2;
		padding-left: 22px;
		padding-right: 22px;
	}
	.wd-title a {
		font-weight: 400;
	}
	.wd-custom-checkbox label .checkbox ~ .wd-checkbox,
	.wd-custom-checkbox label .checkbox-2 ~ .wd-checkbox-2{
		height: 20px;
	    width: 20px;
	    border: 1px solid #E0E6E8;
	    background-color: #FFFFFF;
	    border-radius: 3px;
	    display: inline-block;
	    vertical-align: middle;
	    position: relative;
	}
	.wd-custom-checkbox input.checkbox {
		display:none;
	}
	.wd-custom-checkbox .checkbox ~ .wd-checkbox:before,
	.wd-custom-checkbox .checkbox-2 ~ .wd-checkbox-2:before{
		content: '';
	    position: absolute;
	    top: 0;
	    left: 0;
	    height: 12px;
	    width: 12px;
	    background-color: #247FC3;
	    margin: 3px;
	    opacity: 0;
	    border-radius: 2px;
	    transition: all 0.3s ease;
	}
	.wd-custom-checkbox .checkbox:checked ~ .wd-checkbox:before,
	.wd-custom-checkbox .checkbox-2:checked ~ .wd-checkbox-2:before{
		opacity: 1;
	}
	.wd-submit:before,
	.wd-submit:after{
		content:'';
		display: table;
		clear: both;
	}
	/* End Dropdown */
	
	.header-project-layout{
		position: absolute;
		top: 30px;
		right: 0px;
		transition: all 0.3s ease;
	}
	.header-project-layout a{
		background-color: #5487FF;
		height: 40px;
		width: 40px !important;
		border-radius: 50%;
		box-sizing: border-box;
		text-align: center;
		display:block;
		transition: all 0.3s ease;
		box-shadow: 0 0 10px 1px rgba(29,29,27,0.2);
	}
	.header-project-layout:hover{
		transform: rotate(45deg);
	}
	.header-project-layout img{
		margin-top:9px;
	}
	.header-project-layout .active img{
		transform: rotate(45deg) scale(1.05);
		-webkit-transform: rotate(45deg) scale(1.05);
	}
	.project-field .multiselect {
		position: relative;
		border-right: none;
	}
	.wd-add-project .btn-submit {
		height: 48px;
		text-align: center;
		line-height: 48px;
		background-color: #5487FF;
		border-radius: 5px;
		text-transform: uppercase;
		display: block;
		font-size: 14px;
		width: calc(100% - 25px);
	}
	.wd-multiselect.multiselect .circle-name{
		height: 30px;
		width: 30px;
		line-height: 30px;
		vertical-align: middle;
	}
	.wd-multiselect .wd-combobox-content .option-content .wd-data-manager input[type = 'checkbox'] {
		display: none;
	}
	#addProjectTemplate.open.loading{
		position: fixed;
		width: 100vw;
		height: 100vh;
		z-index:99;
	}
	.search-button {
		height: 40px;
		width: 40px;
		line-height: 38px;
		border: 1px solid #E1E6E8;
		background-color: #FFFFFF;
		border-radius: 3px;
		padding: 0;
		box-sizing: border-box;
		display: inline-block;
		text-align: center;
		transition: all 0.3s ease;
	}
	.search-button:hover{
		background-color: #247FC3;
		color: #fff;
	}
	.context-filter{
		display: none;
	}
	.multiselect-filter .context-filter input{
		width: 100%;
		box-sizing: border-box;
		margin-right: 0;
		width: calc(100% - 10px);
		box-sizing: border-box;
		margin-right: 0;
		margin-left: 5px;
		margin-top: 5px;
		height: 32px;
		line-height: 32px;
	}
	.search-filter #project-filter{
		display: inline-block;
	}
	.search-filter .search-button{
		margin-right: 5px;
		vertical-align: top;
	}
	.wd-title{
		margin-bottom: 0;
	}
	.wd-title .btn{
		color: #c6cccf;
	}
	.search-filter .multiselect-filter .wd-custom-checkbox{
		padding-top: 0 !important;
	}
	
	.listMulti .wd-custom-checkbox label{
		    cursor: pointer;
	}
	.search-filter select{
		margin-right: 7px;
	}
	.search-filter .multiselect-filter .circle-name{
		width: 30px;
		height: 30px;
		margin-right: 5px;	
	}
	.search-filter .multiselect-filter{
		width: 190px;
		padding-top: 0;
	}
	.wd-combobox-filter.wd-combobox-pm [class^="wd-em-"]{
		display: none;
	}
	.wd-combobox-filter.wd-combobox-pm img{
		width: 30px;
		height: 30px;
		border-radius: 50%;
		margin-right: 5px;	
		vertical-align: top;
		margin-top: 4px;
	}
	.multiselect-filter .list_multiselect .wd-option-name .employee-name{
		text-overflow: ellipsis;
		white-space: nowrap;
		display: inline;
	}
	.multiselect-pm .list_multiselect .wd-data-filter .wd-checkbox{
		display: none;
	}
	.multiselect-filter .list_multiselect::-ms-expand{
		display: none;
	}
	#flashMessage{
		margin-bottom: 20px;
	}
	#filter-favorite{
		padding-top: 8px;
		text-align: center;
	}
	#filter-favorite svg{
		width: 20px;
		height: 20px;
		vertical-align: top;
		display: inline-block;
	}
	.svg-color-star {
		stroke: #c6cccf;
		fill: none;
		stroke-width: 4px;
	}
	#filter-favorite:hover {
		background-color: #247FC3;
	}
	#filter-favorite:hover .svg-color-star{
		stroke: #fff;
		fill: #247FC3;
	}
	#filter-favorite.active .svg-color-star {
		stroke: #247FC3;
		fill: #247FC3;
	}
	#filter-favorite.active:hover .svg-color-star {
		stroke: #fff;
		fill: #247FC3;
	}
	.list-type-project,
	.grid_order{
		display: inline-block;
		vertical-align: middle;
	}
	.grid_order .wd-dropdown ul li {
		border-bottom: 1px solid #E1E6E8;
		transition: 0.15s ease;
	}
	.grid_order .popup-dropdown li{
		display: flex;
		/* height: 32px; */
		align-items: stretch;
		justify-content: space-between;
	}
	.grid_order .popup-dropdown li.editing ,
	.grid_order .popup-dropdown li.editing a{
		background-color: #fff;
		color: #247fc3;
	}
	.grid_order .popup-dropdown li.editing input{
		color: #247fc3;
	}
	.grid_order .popup-dropdown li a{
		/* //height: 32px; */
		color: inherit;
		border: none;
		flex-grow: 1;
	}
	.grid_order .popup-dropdown li .dropdown-item--button{
		line-height: initial;
		flex-grow: 0;
		background: transparent;
		padding: 0;
		border: none;
		cursor: pointer;
	}
	.grid_order .popup-dropdown li svg{
		width: 14px;
		height: 14px;
		margin: 12px;
		display: block;
	}
	.project-item--placeholder{
		opacity: 0.6;
	}
	.project-item{
		top: 0;
		left: 0;
	}
	.project-item.has-animation{
		transition: 0.4s ease;
	}
	.project-item-overlay{
		display: none;
		position: absolute;
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: #fff;
		opacity: 0.2;
	}
	.sorting .project-item-overlay{
		display: block;
	}
	.project-item--placeholder .project-item-overlay{
		background: #5487FF;
	}
	.project-item--selected .project-item-overlay{
		background: #5487FF;
	}
	.wd-title .btn-big{
		padding-left: 20px;
		padding-right: 20px;
		width: auto;
	}
	.wd-title .btn-right{
		float: right;
		margin-left: 7px;
	}
	.wd-title .btn.hightlight-button{
		background-color: #247FC3;
		color: #fff;
	}
</style>
<?php 
// ob_clean(); debug($dataFilter);exit;
$isFilter = !empty($dataFilter) ? 1 : 0;
$_linkDashboard = '';
$canModified = $employee_info['Role']['name']== 'admin' ||  $employee_info['Employee']['update_your_form'] || !empty($profileCanUpdateYourform);;
if( !empty($screenDashboard)){ 
	foreach($screenDashboard as $screen => $screen_val){
		$_linkDashboard = array();
		$_lang = $employee_info['Employee']['language'];
		$_title = ($_lang == 'fr') ? $screen_val['name_fre'] : (($_lang == 'en') ? $screen_val['name_eng'] :  __('Dashboard', true));
		$_linkDashboard['link'] =  $html->url(array('controller' => $screen_val['controllers'], 'action' => $screen_val['functions']));
		$_linkDashboard['title'] =  $_title;
		if($screen == 'indicator') break;
	}
}
$i18ns = array(
	'-- Any --' => __('-- Any --', true),
	'Project Manager' => __('Project Manager', true),
	'Weather' => __('Weather', true),
	'Advancement' => __('Advancement', true),
	'Program' => __('Program', true),
	'Project' => __('Project', true),
	'like_this' => __('I like', true),
	'dislike_this' => __('I dislike', true),
	'add_favorite' => __('Add favorite', true),
	'remove_favorite' => __('Remove favorite', true),
);
for ($m=1; $m<=12; $m++) {
	$month = date('M', mktime(0,0,0,$m, 1, 2000));
	$i18ns[$m] = __($month,true);
	$i18ns[$month] = __($month,true);
}
function draw_line_progress($value=0){
	ob_start();
    $_css_class = ($value <= 100) ? 'green-line': 'red-line';
	$display_value = min($value, 100);
	// if(!empty($adminTaskSetting['Consumed']) || !empty($adminTaskSetting['Manual Consumed'])){
		?>
			<div class="wd-progress-slider <?php echo $_css_class;?>" data-value="<?php echo $value;?>">
				<div class="wd-progress-holder">
					<div class="wd-progress-line-holder"></div>
				</div>
				<div class="wd-progress-value-line" style="width:<?php echo $display_value;?>%;"></div>
				<div class="wd-progress-value-text">
					<div class="wd-progress-value-inner">
						<div class="wd-progress-number" style="left:<?php echo $display_value;?>%;">
							<div class="text"><?php echo round($display_value);?>%</div> 
							<input class="input-progress wd-hide" value="<?php echo $value;?>"  onchange="saveManualProgress(this);" onfocusout="progressHideInput(this)" />
						</div>
					</div>
				</div>
			</div>
		<?php
	// }
    return ob_get_clean();
}
$svg_icons = array(
	'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<rect class="svg-background" width="24" height="24"/>
		<path class="svg-color-path" d="M67.355,294.392a.962.962,0,0,1-.926-1.228l1.7-5.96a.955.955,0,0,1,.245-.416l11.5-11.5a3.075,3.075,0,0,1,4.342,0l1.278,1.278a3.071,3.071,0,0,1,0,4.342L74,292.408a.958.958,0,0,1-.415.245l-5.961,1.7A.982.982,0,0,1,67.355,294.392Zm1.4-2.366,4.053-1.158,8.935-8.935-2.9-2.895-8.934,8.934Zm14.351-11.455,1.022-1.021a1.145,1.145,0,0,0,0-1.617l-1.278-1.279a1.148,1.148,0,0,0-1.618,0l-1.022,1.023Z" transform="translate(-64.392 -272.395)"/>
	</svg>',
	'message' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		<rect class="svg-background" width="24" height="24"/>
		<path class="svg-color-path" d="M0,9.949a10,10,0,0,1,20,0,9.222,9.222,0,0,1-1.327,4.874L19.9,18.68a.972.972,0,0,1-.2.914.618.618,0,0,1-.612.3h-.306L14.9,18.68A10.391,10.391,0,0,1,10,20,10.145,10.145,0,0,1,0,9.949Zm1.837,0A8.167,8.167,0,0,0,10,18.071a7.815,7.815,0,0,0,4.286-1.219.766.766,0,0,1,.51-.1H15.1l2.653.813-.816-2.64a.887.887,0,0,1,.1-.711,7.962,7.962,0,0,0,1.225-4.264A8.323,8.323,0,0,0,10,1.827,8.167,8.167,0,0,0,1.837,9.949Zm4.592,2.843a.914.914,0,1,1,0-1.828h4.8a.914.914,0,1,1,0,1.828Zm0-3.858a1,1,0,0,1-.918-1.015.9.9,0,0,1,.918-.812h7.142a.914.914,0,1,1,0,1.827Z" transform="translate(2 2)"/>
	</svg>',
	'dashboard' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"> 
		<rect class="svg-background" width="24" height="24"/>
		<path class="svg-color-path" d="M376,138.069H360a2,2,0,0,0-2,2v10a2,2,0,0,0,2,2h16a2,2,0,0,0,2-2v-10A2,2,0,0,0,376,138.069Zm0,12H360v-10h16Zm-4,4h-8a1,1,0,0,0,0,2h8a1,1,0,0,0,0-2Z" transform="translate(-356 -135.069)"/>
	</svg>',
	'star' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
		<path class="svg-stroke-color" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"/>

	</svg>',
	'star_filter' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
		<path class="svg-color-star" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"/>

	</svg>',
	'like' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
		<rect class="svg-background" width="24" height="24"/>
		<path width="24" height="24" class="svg-stroke-color" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
	</svg>',
	'time' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
		<defs></defs>
		<rect class="svg-background" width="16" height="16"/>
		<path class="svg-color-path" d="M6,12a6,6,0,1,1,6-6A6,6,0,0,1,6,12ZM6,1.333A4.667,4.667,0,1,0,10.667,6,4.667,4.667,0,0,0,6,1.333ZM7.584,7.9l-1.9-1.143a.771.771,0,0,1-.352-.778V4A.667.667,0,0,1,6.667,4V5.586l1.648.992a.781.781,0,0,1,.267,1.041A.715.715,0,0,1,7.584,7.9Z" transform="translate(2 2)"/>
	</svg>',
);
?>
<div class="wd-project-filter wd-title"><div class="wd-project-filter-container">
	<div class="list-type-project">
		<div class="wd-dropdown">
			<span class="selected">
				<?php
				
					if (empty($prog) ){
						__d(sprintf($_domain, 'Details'), "Program");
					} else{
						$_selected = array();
						foreach ($listProgramFields as $key => $value){
							if( in_array($key, $prog) ) $_selected[] = $value;
						}
						echo implode( ', ', $_selected);
					}
				?>
			</span>
			<span class="wd-caret"></span>
			<div class="popup-dropdown">
				<?php 
				echo $this->Form->create('typeProject', array('id' => 'typeProjectForm', 'url' => array('controller' => $this->params['controller'], 'action' => $this->params['action'])) );
				?>
				<ul class="list">
				<?php 
				foreach ($listProgramFields as $key => $value){
					echo '<li>';
					?>
					<div class="wd-custom-checkbox">
						<label> 
							<input type="checkbox" class="checkbox" id="typeProjectProjectAmrProgram<?php echo $key;?>" name="data[Project][typeProject][]" <?php if(in_array($key, $prog)) echo 'checked=checked';?> value="<?php echo $key;?>"/>
							<span class="wd-checkbox"></span>
							<span><?php echo $value;?></span>
						</label>
						</div>
					<?php 
					echo '</li>';
				}
				?>
				</ul>
				<input type="hidden" value="<?php echo $appstatus;?>" name="data[Project][cate]" id="curentCate">
				<div class="wd-submit">
					<button type="submit" class="btn-form-action btn-ok btn-right" id="btnSave"><span><?php __('Save'); ?></span></button>
					<a class="btn-form-action btn-cancel cancel" id="reset_button" href="javascript:void(0)" onclick="javascript:cancel_dropdown(this);" title="<?php echo __("Cancel", true); ?>"><span><?php __('Cancel'); ?></span></a>
				</div>
				<?php echo $this->Form->end();?>
			</div>
		</div>
	</div>
    <?php
        echo $this->Form->create('Category', array('style' => 'display: inline-block'));
        $href = '';
        $href = $this->params['url'];
        if(!empty($appstatus)){
            $op = ($appstatus == 1) ? 'selected="selected"' : '';
            $ar = ($appstatus == 3) ? 'selected="selected"' : '';
            $md = ($appstatus == 4) ? 'selected="selected"' : '';
            $io = ($appstatus == 5) ? 'selected="selected"' : '';
            $io2 = ($appstatus == 6 || $appstatus == 2) ? 'selected="selected"' : '';
        }
    ?>
 
        <select class="wd-customs" id="CategoryCategory" rel="no-history">
            <option value="1" <?php echo isset($op) ? $op : '';?>><?php echo  __("In progress", true)?></option>
            <option value="6" <?php echo isset($io2) ? $io2 : '';?>><?php echo  __("Opportunity", true)?></option>
            <option value="5" <?php echo isset($io) ? $io : '';?>><?php echo  __("In progress + Opportunity", true)?></option>
            <option value="3" <?php echo isset($ar) ? $ar : '';?>><?php echo  __("Archived", true)?></option>
            <option value="4" <?php echo isset($md) ? $md : '';?>><?php echo  __("Model", true)?></option>
        </select>
        <?php // endif;?>
    <?php
        echo $this->Form->end();
    ?>
    <div class="btn open-filter-form" onclick="openFilter();">
		<!-- Them style de xoa style duoc lay tu file layout_2019.css. QuanNV 09/05/2019 -->
        <img style="margin:0;" title="<?php __('Open Search');?>"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/><span><?php __('Search...');?></span>
    </div>
	<a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Delete the filter') ?>">
		<i class="icon-refresh"></i>
	</a>
	<?php 
		$favorited_filter = !empty($filter_render) && !empty($filter_render['filter-favorite']) ? $filter_render['filter-favorite'] : '';
	?>
	<a href="javascript:void(0);" class="btn filter-favor <?php echo ($favorited_filter == 'yes') ? 'active' : ''; ?>" id="filter-favorite">
		<?php echo $svg_icons['star_filter'];?>
		<input type="hidden" name="filter-favorite" id="filter_favor_input" class="filter_favor_input disabled" value="<?php echo (!empty($loadFilter['filter-favorite']) && $loadFilter['filter-favorite'] == 'yes') ? true : false;?>"/>
	</a>
	
	<?php 
	// ob_clean(); debug( $filter_render); exit; 
	$project_grid_orders = array(
		"custom_order" => __('Your order', true),
		"alphabet_project" => sprintf(__('Alphabetical order by %s', true), __d(sprintf($_domain, 'Details'), 'Project Name', true)),
		"alphabet_program" => sprintf(__('Alphabetical order by %s', true), __d(sprintf($_domain, 'Details'), 'Program', true) .'/'. __d(sprintf($_domain, 'Details'), 'Project Name', true)),
		"newest_comment" => __('Last update', true)
	);
	$_keys = array_keys( $project_grid_orders);
	$curentGridOrder = @$filter_render['project_grid_order'];
	$curentGridOrder = isset($project_grid_orders[$curentGridOrder]) ? $curentGridOrder : $_keys[0];
	?>
	<!-- Order -->
	<div class="grid_order order-select">
		<div class="wd-dropdown grid_order_dropdown" id="grid_order_dropdown">
			<input type="hidden" class="grid_order_dropdown_selected wd-dropdown-seleted" name="project_grid_order" value="<?php echo $curentGridOrder;?>"/>
			<span class="selected">
				<?php echo $project_grid_orders[$curentGridOrder]  ;?>
			</span>
			<span class="wd-caret"></span>
			<ul class="popup-dropdown">
				<?php foreach( $project_grid_orders as $_k => $_v){
					$class = 'order-item order-item-' . $_k;
					$class .= ($_k == $curentGridOrder) ? ' active' : '';
					?>
					<li class="<?php echo $class;?>" data-value="<?php echo $_k;?>">
						<a href="javascript:void(0);" class="<?php echo $class;?>" data-text="<?php echo $_v;?>" data-value="<?php echo $_k;?>"><?php echo $_v;?></a>
						<?php if( $_k == "custom_order"){?>
							<button class="dropdown-item--button button_edit" id="dropdown-item--button-edit" title="<?php echo __('Enter to Order mode', true);?>"><?php echo $svg_icons['edit'];?></button>
						<?php } ?> 
						<input type="text" class="wd-hide input_edit" rel="no-history" value="<?php echo $_v;?>" data-key="<?php echo $_k;?>" />
					</li>
				<?php } ?>					
			</ul>
		</div>
	</div>
	<!-- <a href="javascript:void(0);" class="btn btn-text cancel-grid-sorted" id="cancel-grid-sorted">
		<i class="icon-refresh"></i>
	</a> -->
	<a href="javascript:void(0);" class="btn btn-text btn-right btn-big hightlight-button" id="save-grid-sorted" style="display: none"><?php echo __('Save custom order');?></a>
	<a href="javascript:void(0);" style="display: none" class="btn btn-text btn-right btn-big" id="cancel-grid-sorted"><?php echo __('Cancel');?></a>
	
	<!-- 
	<a href="javascript:void(0);" class="btn btn-text save-grid-sorted" id="save-grid-sorted">
		<i class="icon-cloud-upload"></i>
	</a>
	-->
	<!-- END Order -->
    <div class="search-filter">
    <span class="close-filter"><img title="Close filter"  src="<?php echo $html->url('/img/new-icon/close.png'); ?>"/></span>
    <?php
        echo $this->Form->create('Project', array('id' => 'project-filter', 'url' => array('controller' => $this->params['controller'], 'action' => $this->params['action'])) );
        $href = '';
        $href = $this->params['url']; ?>
            <?php
			if(!empty($listProgramFields)){?>
             <div class="multiselect-filter multiselect-program" style="margin-right: 3px;">
                <a href="javascript:void();" class="wd-combobox-filter wd-combobox-program"><p class="text-placeholder"><?php echo __d(sprintf($_domain, 'Details'), 'Program', true) ?></p></a>
				<div class="context-filter context-program-filter"><span><input type="text" rel="no-history" placeholder = "<?php echo  __("Search...", true)?>" ></span></div>
                <div id="wd-data-program" style="display: none;" class="wd-datas list_multiselect">
				<?php foreach($listProgramFields as $id => $nameProgram) { ?>
					<div class="wd-data-filter wd-group-<?php echo $id;?>">
						<div class="wd-custom-checkbox">
							<label class="wd-option wd-data">
								<?php echo $this->Form->input('project-program', array(
									'label' => false,
									'div' => false,
									'type' => 'checkbox',
									'rel' => 'no-history',
									'class' => 'checkbox',
									'name' => 'data[Project][project_program][]',
									'value' => $id));?>
								<span class="wd-checkbox"></span>
								<span class="wd-option-name"><?php echo $nameProgram; ?></span>
							</label>
						</div>
					</div>
			    <?php } ?>
                </div>
			</div>
            <?php } 
			echo $this->Form->input('project_name', array(
				'div' => false,
				'label' => false,
				'rel' => 'no-history',
				'id' => 'project-name',
				'placeholder' => __d(sprintf($_domain, 'Details'), 'Project Name', true),
			));
			$weatherOptions = array(
				// '' =>  __("Weather", true),
				'sun' =>  __("Sun", true),
				'rain' =>  __("Rain", true),
				'cloud' =>  __("Cloud", true),
				
			);
			?>
			 <div class="multiselect-filter multiselect-weather" style="margin-right: 3px;">
                <a href="javascript:void();" class="wd-combobox-filter wd-combobox-weather"><p class="text-placeholder"><?php echo __d(sprintf($_domain, 'KPI'), 'Weather', true); ?></p></a>
				<div class="context-filter context-weather-filter"><span><input type="text" rel="no-history" placeholder = "<?php echo  __("Search...", true)?>" ></span></div>
                <div id="wd-data-weather" style="display: none;" class="wd-datas list_multiselect">
				<?php foreach($weatherOptions as $id => $weather) { ?>
					<div class="wd-data-filter wd-group-<?php echo $id;?>">
						<div class="wd-custom-checkbox">
							<label class="wd-option wd-data">
								<?php echo $this->Form->input('project-weather', array(
									'label' => false,
									'div' => false,
									'type' => 'checkbox',
									'class' => 'checkbox',
									'rel' => 'no-history',
									'name' => 'data[Project][weather][]',
									'value' => $id));?>
								<span class="wd-checkbox"></span>
								<span class="wd-option-name"><?php echo $weather; ?></span>
							</label>
						</div>
					</div>
			    <?php } ?>
                </div>
			</div>
			<?php 
            if(!empty($listPMFields)){
				$list_pm = array();
				foreach($listPMFields as $pm_id) $list_pm[$pm_id] = $listEmployeeName[$pm_id]['fullname'];
				asort($list_pm);
				?>
				<div class="multiselect-filter multiselect-pm" style="margin-right: 3px;">
					<a href="javascript:void();" class="wd-combobox-filter wd-combobox-pm"><p class="text-placeholder"><?php echo __d(sprintf($_domain, 'Details'), 'Project Manager', true); ?></p></a>
					<div class="context-filter context-pm-filter"><span><input type="text" rel="no-history" placeholder = "<?php echo  __("Search...", true)?>" ></span></div>
					<div id="wd-data-pm" style="display: none;" class="wd-datas list_multiselect">
					<?php foreach($list_pm as $id => $pmName) { ?>
						<div class="wd-data-filter wd-group-<?php echo $id;?>">
							<div class="wd-custom-checkbox">
								<label class="wd-option wd-data">
									<?php echo $this->Form->input('project-pm', array(
										'label' => false,
										'div' => false,
										'type' => 'checkbox',
										'rel' => 'no-history',
										'class' => 'checkbox',
										'name' => 'data[Project][project_manager_id][]',
										'value' => $id));?>
									<span class="wd-checkbox"></span>
									<span class="wd-option-name"><span class="circle-name"><img  width="30" height="30" src="<?php echo $this->UserFile->avatar($id); ?>" alt= "avatar" title="<?php echo $pmName; ?>"/></span><span class="employee-name"><?php echo $pmName; ?></span></span>
								</label>
							</div>
						</div>
					<?php } ?>
					</div>
				</div>
				<?php 
             }
			echo $this->Form->input('avancement', array(
				'div' => false,
				'label' => false,
				'id' => 'avancement',
				'rel' => 'no-history',
				'placeholder' => __d(sprintf($_domain, 'KPI'), 'Comment', true),
			));
			 ?>
           

            <a class="search-button" ><img title="<?php __('Search');?>"  src="<?php echo $html->url('/img/new-icon/search.png'); ?>"/></a>
			<a href="javascript:void(0);" class="btn btn-text reset-filter hidden" id="reset-filter" onclick="resetFilter();" style="margin-right:5px;" title="<?php __('Delete the filter') ?>">
				<i class="icon-refresh"></i>
			</a>
        <?php
        echo $this->Form->end();
    ?>
    </div>
	<div class="header-project-layout">
		<?php 
		// Check Role
		$roleLogin = $employee_info['Role']['name'];
		$canAddProject = (($roleLogin == 'admin') || (!empty($employee_info['Employee']['create_a_project'])));
		if ( !empty( $profileName['ProfileProjectManager']['can_create_project'] ) ) $canAddProject = 1;
		$canAddTask = ($roleLogin == 'admin' || $roleLogin == 'pm') && $is_task_newdesign;
		$canAddEmployee = ($roleLogin == 'admin' || (!empty($employee_info['CompanyEmployeeReference']['control_resource'])));
		if ( !empty( $profileName['ProfileProjectManager']['create_resource'] ) ) $canAddEmployee = 1;
		if($canAddProject || $canAddTask || $canAddEmployee) { ?>
			<li class="add-project" id="add_new_popup"><a href="javascript:void(0);" ><img title="<?php __('Add new project / task / employee');?>"  src="<?php echo $html->url('/img/new-icon/add.png'); ?>"/></a></li>
		<?php } ?>
	</div>
	<?php echo $this->element("project_header"); ?>
</div></div>
<div id="addProjectTemplate" class="loading-mark"></div>
<div id="template_logs" class="loading-mark grid-comment-dialog project-item" style="height: 420px; width: 320px;display: none;">
    <div class="add-comment"></div>
    <div id="content_comment" style="min-height: 50px">
    <div class="append-comment"></div>
    </div>
    
</div>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout wd-project-grid">
        <div class="container-portfolio clearfix">
			<?php echo $this->Session->flash(); ?>
            <?php
            $i = 0; 
			$showProgress = 1;
			if( !empty($companyConfigs['project_progress_method']) && ($companyConfigs['project_progress_method'] == 'no_progress') ) $showProgress = 0; 
			if( 
				(empty($companyConfigs['project_progress_method']) || ( $companyConfigs['project_progress_method'] == 'consumed')) 
				&& empty($adminTaskSetting['Consumed']) 
				&& empty($adminTaskSetting['Manual Consumed'] )
			){
				$showProgress = 0; 
			}
			// debug( $logs); exit;
			foreach ($listProjects as $project) { 
				$p_id = $project['id']; 
				$_canModified = (($roleLogin == 'admin') || in_array( $p_id, $listIdModifyByPm ));
				$class = $_canModified ? 'editable' : 'disable';
				$weather_name = !empty($listWeather[$p_id ]) ? ucwords($listWeather[$p_id ]) : 'Sun';
				$weather = !empty($listWeather[$p_id ]) ? ($listWeather[$p_id ] . '.png') : 'sun.png';
				$rank = !empty($listRank[$p_id ]) ? ($listRank[$p_id ] . '.png') : 'up.png';
				$rank_class = !empty($listRank[$p_id ]) ? $listRank[$p_id ] : '';
				$initDate = !empty($projectProgress[ $p_id  ]['Completed']) ? $projectProgress[ $p_id  ]['Completed'] : 0;
				$e_id = 0; $full_name = '';
				if ( !empty($listProjectManager[$p_id ])){
					$e_id = $listProjectManager[$p_id ][0]; // first PM
					$full_name = trim($listEmployeeName[$e_id]['fullname']);
				}
				$this_description = !empty( $logs[ $p_id  ]['ProjectAmr']['description']) ? $this_description = $logs[ $p_id  ]['ProjectAmr']['description'] : '';
				$this_description_time =  !empty($this_description) ? $logs[ $p_id  ]['ProjectAmr']['updated']: '';
				$liked = !empty($project_likes['liked'][$p_id]);
				$like_class = $liked ? 'liked' : '';
				$has_favorite = !empty($favorites[$p_id]);
				$no_display = '';
				if(!empty($favorited_filter)){
					if($favorited_filter == 'yes' && empty($has_favorite)) $no_display = 'display: none';
				}
				$favorite_class = $has_favorite ? 'favorite' : '';
				$latest_update = ( !empty( $logs[ $p_id  ]['ProjectAmr']['updated']) ? $logs[ $p_id  ]['ProjectAmr']['updated'] : $project['updated']);
				$updated = new DateTime();
				$updated->setTimestamp($latest_update);
				$curent_time = new DateTime();
				$diff = date_diff($updated, $curent_time);
				$format_arr = array(
					'y' => 'cmYear',
					'm' => 'cmMonth',
					'd' => 'cmDay',
					'h' => 'cmHour',
					'i' => 'cmMinute',
				);
				$timeModified = '1 ' . __('cmMinute', true);
				foreach( (array)$diff as $k => $v){
					if( !empty($v) ){
						$plural = ($v != 1) ? 's' : '';
						$timeModified = $v . ' ' .  __($format_arr[$k] . $plural, true);
						break;
					}
					if( $k == 'i') break;
				}
				// debug($timeModified); exit; 
				
				?>
                <div class="portfolio-item project-item portfolio-item-<?php  echo $i++ . ' ' .$class; ?> <?php echo empty($has_favorite) ? 'project-not-favor' : '';?>" data-project_id="<?php echo $p_id;?>" id="project-item-<?php echo $p_id;?>" style = "<?php echo $no_display ?>">
                    <div class="portfolio-item-inner project-item-inner loading-mark">
						<div class="project-item-header">
                        <?php
							//default link 
							$link = $this->Html->url('/img/project_preview_default2x.png');
							// if file exists
							if (!empty($globals[$p_id]['global']) && $globals[$p_id ]['file'] == false) {
								// if file is image
								if (preg_match('/\.(jpg|jpeg|bmp|gif|png|swf)$/i', $globals[$p_id ]['global']['ProjectGlobalView']['attachment'])) {
									$link = $this->Html->url(array('controller' => 'project_global_views_preview', 'action' => 'attachment_index', $p_id, '?' => array('sid' => $api_key)), true);
								}
							} ?>
							<div class="open-popup" data-id="<?php echo $p_id  ?>">
								<?php 
								$href_dashboard = '#';
								if(!empty($screenDashboard)){
									$href_dashboard = $_linkDashboard['link'].'/'.$p_id ;
								}?>
								<!-- Global View -->
								<a style="display:block; height:100%; width:100%" target="_blank" href="<?php echo $href_dashboard;?>">
									<img src="<?php echo $link; ?>" alt="Image"></img>
								</a>
							</div>
							<!-- Program -->
							<div class="project-program" style="background:<?php echo !empty($listProgramColor[$p_id]) ? $listProgramColor[$p_id] : '#A7CEAE';?>">
								<span><?php echo !empty($listProgram[$p_id ]) ? $listProgram[$p_id ] : '';?></span>
							</div>
							<!-- Like - Favorite -->
							<div class="project-like-favorite">
								<ul class="inline circle size30">
									<li> 
										<span class="project-like-number number text-center"><?php echo $project_likes['countLikes'][$p_id];?></span>
										<a href="javascript:void(0);" title="<?php echo ($liked ? $i18ns['dislike_this'] : $i18ns['like_this']);?>" class="project-like-action <?php echo $like_class;?>" onclick="toggleLikeProject(<?php echo $p_id ?>);">
											<?php echo $svg_icons['like'];?>
											<!-- <i class="icon-heart"></i> -->
										</a>
									</li>
									<li> 
										<a href="javascript: void(0);" title="<?php $has_favorite ? __('Remove favorite') : __('Add favorite');?>" class="project-favorite-action <?php echo $favorite_class;?>" onclick="toggleFavoriteProject(<?php echo $p_id ?>);">
											<?php echo $svg_icons['star'];?>
										</a>
									</li>
								</ul>
							</div>
						</div>
                        <div class="project-item-content">
							<div class="project-weather-pm">
								<ul class="inline circle size40">
									<li>
										<a href="javascript: void(0);" title="<?php __($weather_name);?>" ><img src="<?php echo $html->url('/img/new-icon/' . $weather) ?>"></a>
									</li>
									<?php if( $e_id) {?>
										<li><span class="project-manager-name circle-name" title="<?php echo $full_name; ?>"><img src="<?php echo $this->UserFile->avatar($e_id);?>" alt="avatar" /></li>
									<?php } ?> 
								</ul>
							</div>
                            <div class="project-item-last-modified text-right">
								<span><?php echo $timeModified;?></span> <?php echo $svg_icons['time'];?>
							</div>
                            <div class="project-item-name">
								<div class="text-ellipsis project-name" title="<?php echo $project['project_name'] ?>">
									<a href="<?php echo $html->url(array( 'controller' => $ACLController , 'action'=> $ACLAction, $p_id )); ?>" target="_blank"><?php echo $project['project_name'] ?></a>
								</div>
								<p class="project-description" data-last-updated="<?php echo $this_description_time;?>" title="<?php echo $this_description; ?>"><?php echo $this_description; ?></p>
							</div>
                            <div class="project-item-progress">
								<?php  if($showProgress) echo draw_line_progress($initDate); ?>
							</div>
                            <div class="project-item-list-action">
								<ul class="list-inline inline rounded size40"">
									<li>
										<a target="_blank" href="<?php echo (!empty($_linkDashboard['link']) ? $_linkDashboard['link'] : '').'/'.$p_id ; ?>" title="<?php echo (!empty($_linkDashboard['title']) ? $_linkDashboard['title'] : '');?>"><?php echo $svg_icons['dashboard'];?> </a>
									</li>
									<li><a title="<?php __('Display the project');?>" target="_blank" href="<?php echo $html->url(array('controller' => 'projects', 'action' => 'your_form', $p_id )); ?>"><?php echo $svg_icons['edit'];?></a></li>
									<li><a title="<?php __('Update the progress');?>" href="javascript:void(0);" data-project_id="<?php echo $p_id;?>" onclick="open_edit_popup(this, 'ProjectAmr')" data-log-id="0"><?php echo $svg_icons['message'];?></a></li>
								</ul>
							</div>
                        </div>
                        <div class="project-item-overlay">
							
                        </div>
                    </div>
                </div>
            <?php } ?>
		</div>
	</div>
	<?php echo $this->element("comment_popup"); ?>
</div>
<div id="loading_w_plus"><p></p></div>
<div id='mock'></div>
<script type="text/javascript">
	var text_by = <?php echo json_encode(  __('by', true) ) ?>;
	var text_modified = <?php echo json_encode( __('Modified', true) ) ?>;
	var isTablet = <?php echo json_encode($isTablet) ?>;
    var isMobile = <?php echo json_encode($isMobile) ?>;
    var curent_time = <?php echo json_encode(time()) ?>;
	var savePosition = <?php echo $savePosition ?>;
	var isFilter = <?php echo json_encode($isFilter); ?>;
	var dataFilter = <?php echo json_encode($dataFilter) ?>;
	var projectProgress = <?php echo json_encode($projectProgress) ?>;
	var i18ns = <?php echo json_encode($i18ns); ?>;
	var roleLogin = <?php echo json_encode($roleLogin); ?>;
	var listIdModifyByPm = <?php echo json_encode($listIdModifyByPm); ?>;
	var showProgress = <?php echo json_encode($showProgress); ?>;
	Dropzone.autoDiscover = false;
$('.overlay').on('click', function(){
    var id = $(this).attr('data-id');
    showProjectDetail(id);
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
function showProjectDetail(id){
    jQuery.ajax({
        url : "/projects/ajax/"+id,
        type: "GET",
        cache: false,
        beforeSend: function(){
            $('#loading_w_plus').show();
        },
        success: function (html) {
            $('#loading_w_plus').hide();
            showMe();
            jQuery('#contentDialog').html(html);
            var width  = $(window).width(); 
            if(savePosition && savePosition != 'undefined' && width > 1366){
                var top = (savePosition.top > 0 && savePosition.top < 1000)? savePosition.top : 0;
                var left = (savePosition.left > 0 && savePosition.left < 1950) ? savePosition.left : 0;
                jQuery('#dialogDetailValue').css({'top': top + 'px','left': left + 'px', 'z-index':'99999'});
            } else {
                var hei = jQuery('#dialogDetailValue').height();
                if(hei < 600){
                    jQuery('#dialogDetailValue').css({'top':"5%",'left':'0', 'right': '0', 'z-index':'99999'});
                } else {
                    jQuery('#dialogDetailValue').css({'top':"5%",'left':'15%', 'z-index':'99999'});
                }
                jQuery('#wd-container-main').css({'width': width});
                $(window).resize(function(){
                    var width  = $(window).width(); 
                    jQuery('#wd-container-main').css({'width': width});
                });
            }
            try {
                jQuery(document).ready(init);
            } catch(e){
            }
            $(document).ready(function(){
                var saving = false;
                refreshMap(true);
            });
            $('#budget_db').jqxChart(settings);
            if( yourFormFilter['weather'] == 1 && $showKpiBudget ){
                var svgString1 = new XMLSerializer().serializeToString(document.querySelector('#svg_kpi_1'));
                $('#svg_kpi_1').css('margin-top', '40px');
                var canvas1 = document.getElementById("canvas_kpi");
                var ctx1 = canvas1.getContext("2d");
                var DOMURL1 = self.URL || self.webkitURL || self;
                var img1 = new Image();
                img1.crossOrigin = "Anonymous";
                var svg1 = new Blob([svgString1], {type: "image/svg+xml;charset=utf-8"});
                var url1 = DOMURL1.createObjectURL(svg1);
                img1.src = url1;
                img1.onload = function() {
                    try{
                        ctx1.drawImage(img1, 0, 0);
                        var png1 = canvas1.toDataURL("image/png");
                        document.querySelector('#png-container_kpi').innerHTML = '<img class="img_budget_export" style="display: none; width: 270px;float: left;height: 140px;margin:0; margin-top: 50px" src="'+png1+'"/>';
                        DOMURL1.revokeObjectURL(png1);
                    }catch(e){
                    }
                };
                setTimeout(function(){
                    $('.wd-table').find('#svgChart').each(function(val, index){
                        var type = $(index).closest('div').data('type');
                        var svgString = new XMLSerializer().serializeToString(index);
                        var canvas = document.getElementById("canvas_" + type);
                        canvas.width = 900;
                        canvas.height = 300;
                        var ctx = canvas.getContext("2d");
                        var DOMURL = self.URL || self.webkitURL || self;
                        var img = new Image();
                        img.width = 900;
                        img.height = 300;
                        img.crossOrigin = "Anonymous";
                        var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
                        var url = DOMURL.createObjectURL(svg);
                        img.src = url;
                        img.onload = function() {
                            try{
                                ctx.drawImage(img, 0, 0);
                                var png = canvas.toDataURL("image/png");
                                var style = 'display: none; width: 860px;float: left;height: 280px;margin:0; margin-left: 270px';
                                if(type == 'budget'){
                                    style = 'display: none; width: 860px;float: left;height: 280px;margin:0;';
                                }
                                document.querySelector('#png-container_' + type).innerHTML = '<img class="img_budget_export" style="'+style+'" src="'+png+'"/>';
                                DOMURL.revokeObjectURL(png);
                            }catch(e){
                            }
                        };
                    });
                }, 2000);
            }
            setTimeout(function(){
                $(window).resize();
            }, 2000);
        }
    });
}
function refreshMap(show){
    var query = $.trim($('#coord-input').val());
    if( query ){
        //initial google maps
        $('#map-frame').prop('src', 'https://www.google.com/maps/embed/v1/place?q=' + encodeURIComponent(query) + '&key=' + gapi);
        if( show ){
            $('#map-frame').show();
            $('#local-frame').hide();
            state = 0;
        }
    } else {
        $('#map-frame').prop('src', 'about:blank');
    }
}
$('#CategoryCategory').change(function(){
	var cate = $(this).val();
	$('#curentCate').val(cate).closest('form').submit();
    // location.href = '<?php echo $this->Html->url('/projects/index_plus/') ?>' + $(this).val() +'?view=new';
});
// $('#project-program').change(function(){
    // location.href = '<?php echo $this->Html->url('/projects/index_plus/') ?>p-' + $(this).val() +'?view=new';
// });
function openFilter(){
    var project_filter = $('.open-filter-form').closest('.wd-project-filter');
    project_filter.find('.search-filter').toggleClass('active');
    $('body').find('.wd-project-admin').toggleClass('active');
}
$('.close-filter').click(function(){
    $(this).closest('.search-filter').toggleClass('active');
    $('body').find('.wd-project-admin').toggleClass('active');
});
$(window).ready(function(){
	setTimeout(function(){
		if( !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
			$('#addProjectTemplate').addClass('loading');
			$.ajax({
				url : "/projects/add_popup/",
				type: "GET",
				cache: false,
				success: function (html) {
					$('#addProjectTemplate').empty().html(html);
					$('#add-form').trigger('reset');
					$(window).trigger('resize');
					$('#addProjectTemplate').addClass('loaded');
					$('#addProjectTemplate').removeClass('loading');
					$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
					if( $('#addProjectTemplate').hasClass('open') ){
						show_full_popup('#template_add_task_prj');
					}
				}
			});
		}
	}, 2000);
});
(function($){
    var search_button = $('.search-button');
    if( search_button.length === 0 ) return;
    search_button.on('click', function(e){
        // e.preventDefault();
        // var form = $(this).closest('#FilterIndexPlusForm');
        // var cate = $('#CategoryCategory').val();
        // var prog = form.find('#project-program').val();
        // var weather = form.find('#weather').val();
        // var project_manager = form.find('#project-manager').val();
        // var project_name = form.find('#project-name').val();
        // var avancement = form.find('#avancement').val();
        // var param = 'filter';
        // if(cate) param += '-cate_'+cate;
        // if(prog) param += '-prog_'+prog;
        // if(weather) param += '-weather_'+weather;
        // if(project_manager) param += '-pm_'+project_manager;
        // if(avancement) param += '-avancement_'+avancement;
        // if(project_name) param += '-pname_'+project_name;
        // location.href = '<?php echo $this->Html->url('/projects/index_plus/')?>' + param +'?view=new';
		 $('#project-filter').submit();
    });
    var appstatus = <?php echo json_encode($appstatus);?>,
    personDefault = <?php echo json_encode($personDefault);?>,
    cate_id = appstatus ? appstatus : 1;
    function listProjectStautus(id, view_id){
        if(id != ''){
            $.ajax({
                url: '/projects/getPersonalizedViews/' + id,
                async: false,
                beforeSend: function(){
                    $('#CategoryStatus').html('Please waiting...');
                },
                success:function(datas) {
                    var datas = JSON.parse(datas);
                    var selected = selectDefined = selectDefault = '';
                    if(view_id != null){
                        if(view_id == 0){
                            selected = 'selected="selected"';
                        } else if(view_id == -1){
                            selectDefined = 'selected="selected"';
                        } else if(view_id == -2){
                            selectDefault = 'selected="selected"';
                        }
                    }
                    var content = '<option value="0" ' + selected + '><?php echo  __("------- Select -------", true);?></option>';
                    if(personDefault == false){
                        content += '<option value="-1" ' + selectDefined + '><?php echo  __("-- Default", true);?></option>';
                    } else {
                        content += '<option value="-2" ' + selectDefault + '><?php echo  __("-- Default", true);?></option>';
                    }
                    $.each(datas, function(ind, val){
                        var selected = '';
                        if(view_id == ind && view_id != null && view_id != -2 && view_id != -1 && view_id != 0){
                            selected = 'selected="selected"';
                        }
                        content += '<option value="' +ind+ '" ' + selected + '>' + val + '</option>';
                    });
                    $('#CategoryStatus').html(content);
                }
            });
        }
    }
    listProjectStautus(appstatus, null);
    // $(document).ready(function() {
        // var n = <?php echo $i ?>;
        // if(n > 0){
			// var height = 0;
            // for( i = 0 ; i < n; i++){
                // height = Math.max( height, $('.portfolio-item-'+ i).height());
            // }
			// $('.portfolio-item').height(height);
        // }
    // });
	$('body').on('click', function(e){
		if( !($('.wd-dropdown.open').find(e.target).length ) ){
			$('.wd-dropdown.open').removeClass('open');
		}
	});
	
})(jQuery);
	
//QuanNV
	var prog = <?php echo json_encode($prog);?>;
	var openOptions = function(fieldName){
		$(fieldName).click(function(e){
			if(!(e.target.tagName == 'SPAN' || e.target.tagName == 'IMG')){
				var checked = $(this).attr('checked');
				var parent = $(this).closest('.multiselect-filter');
				if(checked){
					$(parent).find('.wd-datas').css('display', 'none');
					$(this).removeAttr('checked');
					$(parent).find('.context-filter').css('display', 'none');
				} else {
					$(parent).find('.wd-datas').css('display', 'block');
					$(this).attr('checked', 'checked');
					$(parent).find('.context-filter').css({
						'display': 'block',
						'position': 'absolute',
						'width': '100%',
						'z-index': 2,
					});
					$(parent).find('.wd-datas div:first-child').css('padding-top', '40px');
				}
			}
			return false;
		});
		
	}
	 $('body').on('click', function(e){
		if(!( $(e.target).hasClass('multiselect-filter') || $('.multiselect-filter').find(e.target).length)){
			$('.wd-datas').css('display', 'none');
			$('.wd-combobox-filter').removeAttr('checked');
			$('.context-filter').css('display', 'none');
		}
	});
	openOptions($('.wd-combobox-program'));
	openOptions($('.wd-combobox-weather'));
	openOptions($('.wd-combobox-pm'));
	var multiSelectFilter = function(fieldFilter, dataFilter, dataName){
		var $idProSelected = [];
		// fieldFilter
		var itemOptions = fieldFilter.find('.wd-data-filter');
		var _parent = fieldFilter.closest('.multiselect-filter');
		 itemOptions.each(function(){
			var data = $(this).find('.wd-data');
			/*** When load data */
			var valList = $(data).find('.checkbox').val();
			if(dataFilter['Project'] && dataFilter['Project'][dataName]){
				$.each(dataFilter['Project'][dataName], function(index, idProgram){
					if(idProgram != 0 && valList == idProgram){
						$(data).find('.checkbox').attr('checked', 'checked');
						if($.inArray(idProgram, $idProSelected) != -1){
							//do nothing
						} else {
							$(_parent).find('a.wd-combobox-filter').find('p').hide();
							_op_text = $('.wd-data-filter.wd-group-' + valList).find('span.wd-option-name').html();
							if( dataName == 'project_manager_id'){
								_op_text = $('.wd-data-filter.wd-group-' + valList).find('span.wd-option-name .circle-name').html()
							}
							$(_parent).find('a.wd-combobox-filter').append('<span data-id = "'+valList+'" class="wd-dt-'+valList+'">' + _op_text + '</span><span class="wd-em-'+valList+'">, </span>');
							$idProSelected.push(idProgram);
						}
					}

				});
			}
			/*** When click in checkbox */
			var checkBox = $(this).find('.checkbox');
			checkBox.on('change', function(){
				var _datas = $(this).val();
				if($(this).prop("checked") == true){
					$idProSelected.push(_datas);
					_op_text = $(data).find('span.wd-option-name').html();
					if( dataName == 'project_manager_id'){
						_op_text = $(data).find('span.wd-option-name .circle-name').html();
					}							
					$(_parent).find('a.wd-combobox-filter').append('<span data-id = "'+_datas+'" class="wd-dt-'+_datas+'">' + _op_text + '</span><span class="wd-em-'+_datas+'">, </span>');
					$(_parent).find('a.wd-combobox-filter').find('p').hide();
				} else {
					$idProSelected = jQuery.removeFromArray(_datas, $idProSelected);
					$(_parent).find('a.wd-combobox-filter').find('.wd-dt-' +_datas).remove();
					$(_parent).find('a.wd-combobox-filter').find('.wd-em-' +_datas).remove();
					if($idProSelected.length == 0){
						$(_parent).find('a.wd-combobox-filter').find('p').show();
					}
				}
			});
			
		});
		/*** Remove item on combobox **/
		var _combobox = $(_parent).find('.wd-combobox-filter');
		_combobox.on('click', 'span', function(e){
			e.preventDefault();
			_id = $(this).data('id');
			$('.wd-data-filter.wd-group-' + _id).find('input[value="' + _id + '"]').prop('checked', false).trigger('change');
	
		});
	}
	projectProgram = $('#wd-data-program');
	projectWeather = $('#wd-data-weather');
	projectProjectManager = $('#wd-data-pm');
	multiSelectFilter(projectProgram, dataFilter, 'project_program');
	multiSelectFilter(projectWeather, dataFilter, 'weather');
	multiSelectFilter(projectProjectManager, dataFilter, 'project_manager_id');
	
	var timeoutID = null, searchHandler = function(){
		var _parent = $(this).closest('.multiselect-filter')
		var val = $(this).val();
		var te = $('.wd-datas').find('.wd-data-filter .wd-data span').html();
		$(_parent).find('.wd-data-filter .wd-data span').each(function(){
			var $label = $(this).html();
			$label = $label.toLowerCase();
			val = val.toLowerCase();
			if(!val.length || $label.indexOf(val) != -1 || !val){
				$(this).parent().css('display', 'block');
				$(this).parent().next().css('display', 'block');
			} else{
				$(this).parent().css('display', 'none');
				$(this).parent().next().css('display', 'none');
			}
		});
	};

	$('.context-filter').find('input').click(function(e){
		e.stopImmediatePropagation();
	}).keyup(function(){
		var self = this;
		clearTimeout(timeoutID);
		timeoutID = setTimeout(function(){
			searchHandler.call(self);
		} , 200);
	});
	$('#add_new_popup').on('click', function(e){		
		$('#addProjectTemplate').addClass('open');
		// $('.add-project a').toggleClass('active');
		$(window).trigger('resize');
		var popup_width = 580;
		if( $('#addProjectTemplate').hasClass('open') && !$('#addProjectTemplate').hasClass('loaded') && !$('#addProjectTemplate').hasClass('loading')){
			$('#addProjectTemplate').addClass('loading');
			$.ajax({
				url : "/projects/add_popup/",
				type: "GET",
				cache: false,
				success: function (html) {
					$('#addProjectTemplate').empty().html(html);
					$(window).trigger('resize');
					$('input[data-return="form-return"]').val('<?php echo $this->here;?>');
					$('#addProjectTemplate').addClass('loaded');
					$('#addProjectTemplate').removeClass('loading');
					if( $('#NewTask').hasClass('active') ) popup_width = show_workload ? 1080 : 580;
					show_full_popup( '#template_add_task_prj', {width: popup_width});
				}
			});
		}
		if( $('#addProjectTemplate').hasClass('loaded') ){
			
			if( $('#NewTask').hasClass('active') ) popup_width = show_workload ? 1080 : 580;
			show_full_popup( '#template_add_task_prj', {width: popup_width});
		}
	});
	
	history_reset = function () {
		var check = false;
		$('.multiselect-filter').each(function (val, ind) {
			var text = '';
			if ($(ind).find('input').length != 0) {
				text = $(ind).find('input').val();
			} else {
				text = $(ind).find('span').html();
				if (text == "<?php __('-- Any --');?>" || text == '-- Any --') {
					text = '';

				}
			}
			if (text != '') {
				$(ind).css('border', 'solid 1px #E9E9E9');
				check = true;
			} else {
				$(ind).css('border', 'none');
			}
		});
		if (!check) {
			$('#reset-filter').addClass('hidden');
		} else {
			$('#reset-filter').removeClass('hidden');
		}
	}
	resetFilter = function () {
		$('#typeProjectForm :checked').prop('checked', false).trigger('change');
		$('#filter_favor_input').val('no').trigger('change');
		$('input#project-name').val('').trigger('change');
		$('input#avancement').val('').trigger('change');
		var input = $('.wd-project-filter-container').find('input');
		$.each(input, function(n, e){
			input.prop('checked', false).trigger('change');
		});
		$('#typeProjectForm').submit();
	}
	$('#filter-favorite').on('click', function(){
		var _input = $('#filter_favor_input');
		if( !_input.hasClass('disabled')){
			var v = ( _input.val() == 'yes');
			_input.val( v ? 'no' : 'yes' ).trigger('change');
		}
	});
	$('#filter_favor_input').on('change', function(){
		var _this = $(this);
		var val = _this.val();
		if( val == 'yes') {
			$('#filter-favorite').addClass('active');
			$('.project-not-favor').hide();
		}else{
			$('#filter-favorite').removeClass('active');
			$('.project-not-favor').show();
		}
	});
	HistoryFilter.afterLoad = function(){
		var _input = $('#filter_favor_input');
		_input.removeClass('disabled');
	}
	function init_draggable_progress(){}
	/*
	Ticket #1096
	A PM who has the right to modify the project, should be able to modify the % progress.
	*/
	<?php if( !empty($companyConfigs['project_progress_method']) && $companyConfigs['project_progress_method']=='manual'){?>
		$('body').find('.project-item.editable').on('click', '.wd-progress-number .text', function(){
			var _this = $(this);
			_this.hide();
			_this.siblings('.input-progress').removeClass('wd-hide').focus();
			_this.parent().addClass('focus');
		});
		function saveManualProgress(elm){
			var _this = $(elm);
			if(!_this.hasClass('input-progress')) {
				_this = _this.find('.input-progress');
			}
			var val = _this.val();
			var project_item = _this.closest('.project-item');
			var project_id = project_item.data('project_id');
			project_item.find('.loading-mark').addClass('loading');
			$.ajax({
				url: '/projects_preview/saveFieldYourForm/' + project_id,
				type: 'post',
				dataType: 'json',
				data: {
					field: 'manual_progress',
					value: val
				},
				beforeSend: function(){
					project_item.find('.loading-mark').addClass('loading');
				},
				success: function(res){
					if( res.result == 'success'){
						displayProgrressVal(_this.closest('.wd-progress-slider'), parseFloat( res.data.Project.manual_progress ), true);
						_this.blur(); 
						if( _this.closest('.project-item').hasClass('grid-comment-dialog')){
							var project_item = $('#project-item-' + project_id);
							displayProgrressVal(project_item.find('.wd-progress-slider:first'), parseFloat( res.data.Project.manual_progress ), true);
						}
					}else{;
						location.reload();
					}
				},
				error: function(){
					location.reload();
				},
				complete: function(){
					project_item.find('.loading-mark').removeClass('loading');
				}
			});
		}
		function progressHideInput(elm){
			var _this = $(elm).parent();
			_this.find('.text').show();
			_this.find('.input-progress').addClass('wd-hide');
			_this.removeClass('focus');
		}
		function init_draggable_progress(){
			$('body').find('.project-item.editable').find('.wd-progress-number').draggable({
				axis: 'x',
				opacity: 0.6,
				cursor: 'pointer',
				drag: function( event, ui ) {
					var container = ui.helper.closest('.wd-progress-slider');
					var _w = ui.helper.width() + parseInt(ui.helper.css('border-left-width')) + parseInt(ui.helper.css('border-right-width'));
					var max_w = container.width() - _w;
					ui.position.left = Math.max( 0, Math.min( container.width() - _w, ui.position.left ));
					var val = parseInt(ui.position.left / max_w * 100);
					displayProgrressVal(container, val);
				},
				stop: function( event, ui ) {
					saveManualProgress(ui.helper[0]);
				}
			});
		}
		init_draggable_progress();
		function displayProgrressVal(container, val, is_animation){
			var v = parseInt( Math.max(0, Math.min(val, 100)));
			var css_class= '';
			if( val > 100) css_class='red-line';
			if( is_animation){
				container.find('.wd-progress-value-line, .wd-progress-number').addClass('ease');
			}
			container.removeClass('red-line').addClass(css_class);
			container.data('value', val);
			container.find('.wd-progress-value-line').width(v + '%');
			container.find('.wd-progress-number').css('left', v + '%');
			container.find('.wd-progress-number .text').text(v + '%');
			container.find('.input-progress').val(val);
			setTimeout( function(){
				container.find('.wd-progress-value-line, .wd-progress-number').removeClass('ease');
			}, 400);
		}
	<?php } ?>
	$('#template_logs').dialog({
		position    :'center',
		autoOpen    : false,
		height      : 420,
		modal       : true,
		width       : (isTablet || isMobile) ?  320 : 520,
		minHeight   : 50,
		resizable: false
	});
	$('body').on("focusout", "#update-comment", function () {
        var _this = $(this);
        var text = $(this).val(),
		field = $(this).data("field"),
		model = $(this).data("model"),
		project_id = $(this).data("id");
        if (text != '') {
            var _html = '';
            _this.closest('.loading-mark').addClass('loading');

            $.ajax({
                url: '/projects/update_text_comment',
                type: 'POST',
                data: {
                    data: {
                        id: project_id,
                        text: text,
                        field: field,
                        model: model,
                    }
                },
                dataType: 'json',
                success: function (data) {
                    _html = _log_progress = '';
                    var latest_update;
                    if (data) {
                        latest_update = new Date(data[0]['updated'] * 1e3).toISOString().slice(0, 10);
                        latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + data[0]['update_by_employee'];
                        i = 1;
                        $.each(data, function (ind, _data) {
                            if (ind == 0 && ('id' in Object(_data))) {
								// update grid
								var project_item = $('#project-item-' + project_id);
								project_item.find('.project-item-last-modified').find('span:first').html('1 <?php __('cmMinute');?>');
								project_item.find('.project-description').html(_data.description).prop('title', _data.description);
							}
                            if (_data && ('id' in Object(_data))) {
                                name = ava_src = '';
                                comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
                                date = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
								ava_src += '<img width = 35 height = 35 src="' + js_avatar(_data['employee_id']) + '" title = "' + _data['name'] + '" />';
                                _html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment">' + comment + '</div></div></div>';
                            }
                        });
                    } else {
                        _html += '';
                    }
                    if (latest_update)
						_log_progress += '<p class="update-by-employee">' + latest_update + '</p>';
                    $('#template_logs .content-logs').empty().append(_html);
                    $('#update-comment').val('');
                    $('#template_logs .log-progress .logs-info').empty().append(_log_progress);
                    _this.closest('.loading-mark').removeClass('loading');
                }

            });
        }
    });
	function showCommentDialog($p_id) {
		// Check permission here
		var id = $p_id;
		listIdModifyByPm = Object.values( listIdModifyByPm);
		var _canModified = (( roleLogin == 'admin') || ($.inArray( '' + id, listIdModifyByPm) != -1));
		console.log(_canModified);
		var model = 'ProjectAmr';
		var field = 'ProjectAmr';
        var _html = '';
        var latest_update = '';
        var popup = $('#template_logs');
		popup.attr('data-project_id', id);
		popup.addClass('loading').dialog('open');;
        $.ajax({
            url: '/projects/getComment',
            type: 'POST',
            data: {
                id: id,
                model: model,
            },
            dataType: 'json',
            success: function (data) {
                _html = '';
                _html += '<div class="content-comment-container">';
                if (data) {
                    latest_update = '';
					if( _canModified){
						_html += '<div class="comment"><textarea data-id = ' + id + ' data-field = ' + field + ' data-model = ' + model + ' cols="30" rows="6" id="update-comment"></textarea></div>';
					}
                    _html += '<div class="content-logs">';
                    if (data[0]) {
                        latest_update = new Date(data[0]['updated'] * 1e3).toISOString().slice(0, 10);
                        latest_update = text_modified + ' ' + latest_update + ' ' + text_by + ' ' + data[0]['update_by_employee'];
                        var i = 0;
                        $.each(data, function (ind, _data) {
                            if (_data && ('id' in Object(_data))) {
                                name = ava_src = '';
                                comment = _data['description'] ? _data['description'].replace(/\n/g, "<br>") : '';
                                date = _data['updated'];
                                date = new Date(_data['updated'] * 1e3).toISOString().slice(0, 10);
                                ava_src += '<img width = 35 height = 35 src="' + js_avatar(_data['employee_id']) + '" title = "' + _data['name'] + '" />';
								_html += '<div class="content content-' + i++ + '"><div class="avatar">' + ava_src + '</div><div class="item-content"><p>' + date + '</p><div class="comment">' + comment + '</div></div></div>';
                            }
                        });
                    }
                    _html += '</div>';
                }
				_html += '</div>';
                _html_progress = "<div class='log-progress'>";
				if( showProgress) _html_progress += draw_progress_line(data['initDate']);
				_html_progress += "</div>";
                _html += _html_progress;
                _html += '<div class="logs-info"><p class="update-by-employee">' + latest_update + '</p></div></div>';
                $('#content_comment').html(_html);
                popup.dialog('option', {title: data['project_name']});
				init_draggable_progress();
            },
			complete: function(){
				popup.removeClass('loading')
			}
        });
    }
	function draw_progress_line(value){
		var _css_class = (value <= 100) ? 'green-line': 'red-line';
		var display_value = Math.min(value, 100);
		var html = '<div class="wd-progress-slider ' + _css_class + '" data-value="' + value + '"> <div class="wd-progress-holder"> <div class="wd-progress-line-holder"></div></div>';
		html += '<div class="wd-progress-value-line" style="width:' + display_value +'%;"></div><div class="wd-progress-value-text"><div class="wd-progress-value-inner"><div class="wd-progress-number" style="left:' + display_value +'%;">';
		html += '<div class="text">' + Math.round(display_value) + '%</div><input class="input-progress wd-hide" value="' + value +'"  onchange="saveManualProgress(this);" onfocusout="progressHideInput(this)" />';
		html += ' </div> </div> </div> </div>';
		return html;
	}
	function toggleLikeProject(project_id){
		var project_item = $('#project-item-' + project_id);
		project_item.find('.loading-mark').addClass('loading');
		$.ajax({
			url: '/projects/toggleLike/' + project_id,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				project_item.find('.loading-mark').addClass('loading');
			},
			success: function(res){
				if( res.result == 'success'){
					if( res.data.liked){
						project_item.find('.project-like-action').addClass('liked').prop('title', i18ns.dislike_this);
					}else{
						project_item.find('.project-like-action').removeClass('liked').prop('title', i18ns.like_this);
					}
					project_item.find('.project-like-number').html(res.data.countLikes);
				}else{;
					location.reload();
				}
			},
			error: function(){
				location.reload();
			},
			complete: function(){
				project_item.find('.loading-mark').removeClass('loading');
			}
		});
	}
	function toggleFavoriteProject(project_id){
		var project_item = $('#project-item-' + project_id);
		$.ajax({
			url: '/projects/toggleFavoriteProject/' + project_id,
			type: 'get',
			dataType: 'json',
			beforeSend: function(){
				project_item.find('.loading-mark').addClass('loading');
			},
			success: function(res){
				if( res.result == 'success'){
					if( res.favorite){
						project_item.find('.project-favorite-action').addClass('favorite').prop('title', i18ns.remove_favorite);
						project_item.removeClass('project-not-favor');
					}else{
						project_item.find('.project-favorite-action').removeClass('favorite').prop('title', i18ns.add_favorite);
						project_item.addClass('project-not-favor');
					}
					$('#filter_favor_input').trigger('change');
				}else{;
					location.reload();
				}
			},
			error: function(){
				location.reload();
			},
			complete: function(){
				project_item.find('.loading-mark').removeClass('loading');
			}
		});
	}
	
	var sortableGrid = $('.wd-project-grid .container-portfolio:first');
	function enable_grid_order(){
		sortableGrid.z0sortable({
			animation: 150,
			scroll: true,
			// sort: (('project_grid_order' in filter_render) && (filter_render['project_grid_order'] == 'custom_order')) ? 'true' : false,
			sort: true,
			dataIdAttr: 'data-project_id',
			handle: '.project-item-overlay',
			ghostClass: 'project-item--placeholder',
			multiDrag: true,
			selectedClass: 'project-item--selected',
			changeOrder: function changeOrder(a, b, c){
				console.log( typeof a, typeof b, typeof c );
			},
			onSort: function(e){
				$('#save-grid-sorted').show();
				
			}
		});
		$('#cancel-grid-sorted').show();
		$('.grid_order_dropdown ').addClass('disabled');
		sortableGrid.addClass('sorting');
	}
	$('#save-grid-sorted').on('click', function(e){
		console.log( 'Saving ',sortableGrid.z0sortable('toArray'));
		if( typeof sortableGrid != 'object') return;
		var order_project = sortableGrid.z0sortable('toArray');
		console.log( order_project);
		$.ajax({
			url: "<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'save_history_sorting'));?>",
			type: "POST",
			cache: false,
			data: { data: order_project},
			dataType: 'json', 
			beforeSend: function(){
				$('#save-grid-sorted').addClass('loading');
			},
			success: function (res) {
				if( res.result == 'success'){
					custom_order_projects = order_project;
				}
			},
			error: function(){
				// location.reload();
			},
			complete: function () {
				$('#cancel-grid-sorted').trigger('click');
			},
			
		});
		
	});

	$('#cancel-grid-sorted').on('click', function(e){
		$('#cancel-grid-sorted, #save-grid-sorted').hide();
		$('.grid_order_dropdown ').removeClass('disabled');
		$('#grid_order_dropdown').find('.grid_order_dropdown_selected').trigger('change');
		sortableGrid.removeClass('sorting');
		// sortableGrid.z0sortable('destroy');
	});
	$('#dropdown-item--button-edit').on('click', function(e){
		var _this = $(this);
		_this.closest('li').find('a:first').trigger('click');
		enable_grid_order();
	});
	var custom_order_projects = <?php echo json_encode( !empty($userCustomOrders) ? $userCustomOrders : array());?>;
	custom_order_projects = Object.values(custom_order_projects);
	$('#grid_order_dropdown').find('.grid_order_dropdown_selected').on('change', function(e){
		var _this = $(this);
		var _selected = _this.val();
		var _listProjects = '.container-portfolio >.project-item';
		tinysort.defaults.natural  = 'true';
		
		var ul = $('.container-portfolio')
				,lis = ul.find('.project-item').filter(':visible')
				,liHeight = lis[0].offsetHeight
				,liWidth = lis[0].offsetWidth
			;
		if( !lis.length ) return true;
		var project_per_row = 0;
		let _top = lis[0].offsetTop;
		$.each(lis, function(i, _pr){
			if( _pr.offsetTop != _top) return false; // stop the loop
			project_per_row++;
		});
		var is_animation = (lis.length < 50); 
			if( is_animation){
				ul.height(ul[0].offsetHeight);
				for (var i= 0,l=lis.length;i<l;i++) {
					var li = lis[i];
					$(li).css({
						'position': 'absolute',
						'top': parseInt(i/project_per_row)*liHeight,
						'left': parseInt(i%project_per_row)*liWidth
					});
				}
				lis.addClass('has-animation');
			}
		var _items = {};
		switch( _selected){
			case 'custom_order':
				_items = tinysort(lis, {
					sortFunction:function (a,b){
						pid_a = $(a.elm).data('project_id');
						pid_b = $(b.elm).data('project_id');
						// console.log( pid_a, pid_b);
						var pos_a = custom_order_projects.indexOf(pid_a.toString());
						var pos_b = custom_order_projects.indexOf(pid_b.toString());
						// console.log( pos_a, pos_b);
						if( pos_a == -1 ) return  1;
						if( pos_b == -1 ) return -1;
						return (pos_a >= pos_b) ? 1 : -1;
					}
				});
				break;
			case 'newest_comment':
				_items = tinysort(lis, {
					selector:'.project-description',
					data: 'last-updated',
					order: 'desc'
				});
				break;
			case 'alphabet_program':
				_items = tinysort(lis, {selector:'.project-program >span'},{selector:'.project-name >a'})
				break;
			case 'alphabet_project':
			default:
				_items = tinysort(lis, '.project-name >a')
				break;
		}
		if( is_animation){
			lis = $(_listProjects).filter(':visible');
			$.each( lis , function( i, elm){
				if( $(elm).is(':visible')){
					setTimeout((function(elm,i){
						$(elm).css({
							'position': 'absolute',
							'top': parseInt(i/project_per_row)*liHeight,
							'left': parseInt(i%project_per_row)*liWidth
						});
					}).bind(null,elm,i),40);
					i++;
				}
			});
			// Revert style after animation
			setTimeout( function(){
				lis.removeClass('has-animation');
				ul.css('height' , '');
				lis.css({
					position : '',
					top : '',
					left : '',
				});
			}, 500);
		}
	});
</script>