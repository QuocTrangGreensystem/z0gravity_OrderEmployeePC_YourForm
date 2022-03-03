<?php 
$company_id = !empty($employee_info['Company']['id']) ? $employee_info['Company']['id'] : 0;
$titleMenu = ClassRegistry::init('Menu')->find('first', array(
	'recursive' => -1,
	'conditions' => array(
		'company_id' => $company_id,
		'model' => 'project',
		'controllers' => 'project_risks',
		'functions' => 'index'
	),
	'fields' => array('name_eng','name_fre')
));
$langCode = Configure::read('Config.langCode');
$language = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
$risk_options = !empty($options) ? Set::combine( $options, '{n}.id', '{n}.display') : array();
$widget_title = !empty( $widget_title) ? $widget_title : get_risk_title($company_id);
function get_risk_title($company_id){
	$titleMenu = ClassRegistry::init('Menu')->find('first', array(
		'recursive' => -1,
		'conditions' => array(
			'company_id' => $company_id,
			'model' => 'project',
			'controllers' => 'project_risks',
			'functions' => 'index'
		),
		'fields' => array('name_eng','name_fre')
	));
	$langCode = Configure::read('Config.langCode');
	$language = ($langCode == 'fr') ? 'name_fre' : 'name_eng';
	return (!empty( $titleMenu['Menu'][$language]) ? $titleMenu['Menu'][$language] : __('Risks', true));
}
?>
<div class="wd-widget project-risk-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
        </div>
        <div class="widget_content">
            <div class="risk-dashboard">
                <div class="title-horizontal"><?php echo __('Severity', true) ?></div>
                <div class="dashboard-content">
                    <ul class="row-dashboard">
						<li class="item_2_0"><span class="text-horizontal"><?php echo __('FORTE', 'true');?></span>
							<div class="list-item"></div>
						</li>
						<li class="item_2_1">
							<div class="list-item"></div>
						</li>
						<li class="item_2_2">
							<div class="list-item"></div>
						</li>
					</ul>
					<ul class="row-dashboard">
						<li class="item_1_0"><span class="text-horizontal"><?php echo __('MOYENNE', 'true');?></span>
							<div class="list-item"></div>
						</li>
						<li class="item_1_1">
							<div class="list-item"></div>
						</li>
						<li class="item_1_2">
							<div class="list-item"></div>
						</li>
					</ul>
					<ul class="row-dashboard">
						<li class="item_0_0"><span class="text-horizontal"><?php echo __('FAIBLE', 'true');?></span>
						<span class="text-vertical"><?php echo __('FAIBLE', 'true');?></span>
							<div class="list-item"></div>
						</li>
						<li class="item_0_1"><span class="text-vertical"><?php echo __('MOYENNE', 'true');?></span>
							<div class="list-item"></div>
						</li>
						<li class="item_0_2"><span class="text-vertical"><?php echo __('FORTE', 'true');?></span>
							<div class="list-item"></div>
						</li>
					</ul>
                </div>
                <div class="title-vertical"><?php echo __('Occurrence', true) ?></div>
                <ul class="list-dashboard"></ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var risk_options = <?php echo json_encode($risk_options); ?>;
    var risk_dashboard  = $('.risk-dashboard').length;
    if(risk_dashboard){
        var ele_content = $('.risk-dashboard');
        var list_dashboard = $('.list-dashboard');
        $.ajax({
            url : '<?php echo $html->url('/project_risks_preview/dashboard/' . $project_id) ?>',
            type : 'POST',
            dataType : 'json',
            success : function(data){
                _list = '';
                if(data){
                    $('.risk-dashboard').removeClass('loading');
                    $('.item-dash').remove();
                    $.each(data, function(ind, _data) {
						var is_display = risk_options[_data.project_issue_status_id] || "0";
						console.log( is_display);
						if( is_display != '0'){
							ele_content.find(".item_" +_data['dashboard_type']).find('.list-item').append('<p class="item-dash" title="'+ _data['project_risk']+'">' +  _data.weight + '</p>');
							_list += '<li class="list_'+_data['dashboard_type']+'"><span class="circle">' +  _data.weight + '</span> '+ _data['project_risk']+' </li>';
						}
                    });
                }
                list_dashboard.empty().append(_list);
            },
            beforeSend : function(){
                $('.risk-dashboard').addClass('loading');
            },
            error: function(){
                $('.risk-dashboard').removeClass('loading');
            }
        });
    };
</script>
<style>
/* Dashboard*/

.risk-dashboard{
    width: 565px;   
    background-color: #FFFFFF;
}
.dashboard-content{
    height: 360px;  
    margin: auto;
    text-align: center;
}

.dashboard-content ul li{
    width: 140px;
    height: 120px;
    display: inline-block;
    vertical-align: top;

}
.dashboard-content ul li{
    position: relative;
    border-top:  1px solid #E1E6E8;
    border-left:  1px solid #E1E6E8;
}
.dashboard-content ul li:not(:first-child){
    margin-left: -4px;
}
.dashboard-content ul li:last-child{
    border-right:  1px solid #E1E6E8;
}
.dashboard-content ul:last-child li{
    border-bottom:  1px solid #E1E6E8;
}

.dashboard-content ul li:first-child{
    border-left:  1px solid #E1E6E8;
}
.dashboard-content ul li span{
    color: #C6CCCF; 
    font-weight: 600;
    font-size: 12px;
    text-align: left;
    display: block;
    position: absolute;
    background-color: #fff;
}
.dashboard-content ul li span.text-vertical{
    left: 50%;
    transform: translateX(-50%);
    bottom: -9px;
    width: 70px;
    text-align: center;
}
.dashboard-content ul li span.text-horizontal{
    position: absolute;
    background: #fff;
    display: block;
    width: 70px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    margin-left: -35px;
    transform: rotate(-90deg);
    margin-top: 45px;
}
.wd-title{
    overflow: inherit;
    position: relative;
}

.risk-dashboard .row-dashboard li{
    position: relative;
}
.risk-dashboard .row-dashboard li .list-item{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width:  100%;
    text-align: center;
    max-width: 110px;
    max-height: 110px;
    overflow: hidden;
    padding: 10px;
}
.risk-dashboard .row-dashboard li p{
    height: 40px;
    width: 40px;
    border-radius: 50%;
    display: block;
    background-color: #E4AF63;
    z-index: 2;
    box-shadow: 0 0 10px 1px rgba(0,0,1,0.16);
    line-height: 40px;
    color: #fff;
    font-weight: 600;
    font-size: 16px;
    margin: 0;
    display: inline-block;
    margin: -5px;
}
.risk-dashboard .row-dashboard li:first-child p{
    background-color: #6EAF79;
}
.risk-dashboard .row-dashboard li:last-child p{
    background-color: #F05352;
}
.title-vertical, .title-horizontal{
    color: #C6CCCF;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 24px;
    text-align: center;
    text-transform: uppercase;
}
.title-vertical{
    margin-top: 35px;
    letter-spacing: 35px;
    position: relative;
    top: 0px;
    left: 16px;
}
.title-horizontal{
    position: absolute;
    display: block;
    width: 360px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    margin-left: -152px;
    transform: rotate(-90deg);
    margin-top: 158px;
    letter-spacing: 41px;
}

.list-dashboard li{
    color: #242424;
    font-size: 14px;    
    line-height: 27px;
    display: inline-block;
}
.list-dashboard li:not(:last-child){
    margin-right: 13px;
}
.list-dashboard li .circle{
    height: 14px;   width: 14px;
    border-radius: 50%;
    display: inline-block;
    vertical-align: middle;
    margin-right: 7px;
	line-height: 14px;
    color: #fff;
    font-weight: 400;
    font-size: 10px;
    text-align: center;
}

.list-dashboard li.list_2_0 .circle,
.list-dashboard li.list_1_0 .circle,
.list-dashboard li.list_0_0 .circle{
    background-color: #6EAF79;
}
.list-dashboard li.list_2_1 .circle,
.list-dashboard li.list_1_1 .circle,
.list-dashboard li.list_0_1 .circle{
    background-color: #E4AF63;
}
.list-dashboard li.list_2_2 .circle,
.list-dashboard li.list_1_2 .circle,
.list-dashboard li.list_0_2 .circle{
    background-color: #F05352;
}
.risk-dashboard ul.list-dashboard{
    margin-top: 17px;
}
</style>