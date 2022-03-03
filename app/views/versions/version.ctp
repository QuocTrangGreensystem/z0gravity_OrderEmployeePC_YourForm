<?php echo $html->script('jquery.min'); ?>
<title><?php __("z0 GRAVITY :: Version") ?></title>
<style type="text/css">
body{
	padding: 0;
	margin: 0;
}
html.page-login{
	background-color: #ffffff;
}
.header-wrap{
	width: 1140px;
	margin: auto;
	padding: 10px 0;
}
header{
    z-index: 3;
    transition: all 350ms ease;
    background-color: #f1f6fa;
}
#wd-container-main{
    width: 1140px;
    height: 100%;
    margin: auto;
}
#VersionContent{height: 120px !important;width: 75% !important;}
#VersionName{width: 25% !important;}
.wd-list-project .wd-tab .wd-content label{
	width: 100px;
}
.wd-content .wd-version{
	margin-bottom: 20px;
}
.wd-content a.wd-reset{
	height: 40px;
    width: 40px;
    line-height: 38px;
    display: none;
    border: 1px solid #E1E6E8;
    background-color: #FFFFFF;
    border-radius: 3px;
    padding: 0;
    box-sizing: border-box;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    color: #666;
	display: inline-block;
}
.wd-content{
	color: #424242;
	font-size: 16px;
	font-family: 'Roboto Condensed';
}
.wd-content a.wd-reset:hover{
	background-color: #247FC3;
    color: #fff;
}
.wd-content a.wd-reset:before{
	margin-right: 0;
}
.wd-content a.wd-reset:hover:before{
	color: #fff;
}
.wd-content select.version{
	height: 40px;
    width: 100px;
    border: 1px solid #E0E6E8;
    padding: 0 20px 0 10px;
    color: #666666;
    font-size: 14px;
    line-height: 38px;
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    background: url(/img/new-icon/down.png) no-repeat right 10px center #fff !important;
}
.version-title, .version-log-title{
	margin-top: 20px;
	margin-bottom: 10px;
	font-size: 16px;
	color: #247FC3;
}
.version-log-title{
	font-size: 14px;
	margin-top: 0;
	margin-bottom: 10px;
}
.wd-version-action{
	margin-top: 20px;
}
.wd-list-project .wd-tab .wd-content label{
	float: none;
}
.wd-content{
	position: relative;
}
.wd-content.loading:before{
	content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255, 0.6) url(/img/business/wait-1.gif) no-repeat center center;
    background-size: 30px;
    z-index: 3;
    display: block;
}
.header-wrap .wd-nav{
	float: right;
	list-style: none;
	margin:0;
}
.header-wrap .wd-nav li{
	display: inline-block;
}
.header-wrap .wd-nav li:not(:last-child){
	margin-right: 15px;
}
.header-wrap .wd-nav li a {
    font-size: 14px;
    line-height: 40px;
    display: block;
    text-decoration: none;
    text-transform: uppercase;
    color: #242424;
	font-weight: bold;
	font-family: 'Roboto Condensed';
}
.header-wrap .wd-nav li a:hover{
	color: #247FC3;
}
</style>
<header id="header" class="site-header" role="header">
	<div class="header-wrap">
		<a href="<?php echo $html->url('/') ?>" class="logo">
			<svg version="1.1" id="logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="156" height="42" viewBox="0 0 225 60" style="enable-background:new 0 0 225 60;" xml:space="preserve">
				<style type="text/css">
				.st1{fill-rule:evenodd;clip-rule:evenodd;fill:#217FC2;}
				.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#041839;}
				</style>
				<g id="icon" transform="translate(700 329.324)">
				<path id="Tracé_1086" class="st1" d="M-688-290.6c4.9,9.9,16.9,14,26.8,9.2c4-2,7.2-5.2,9.2-9.2c1-2,1.6-4.1,1.9-6.3
				c0-0.4,0.1-0.8,0.1-1.2c0-0.4,0-0.8,0-1.2c0-0.4,0-0.8,0-1.2c0-0.4-0.1-0.8-0.1-1.2H-670v4.9h14.8c-1.4,8.2-9.1,13.7-17.3,12.3
				c-6.3-1.1-11.3-6-12.3-12.3c-0.1-0.4-0.1-0.8-0.2-1.2s-0.1-0.8-0.1-1.2c0-0.4,0-0.8,0.1-1.2c0-0.4,0.1-0.8,0.2-1.2
				c1.4-8.2,9.1-13.7,17.3-12.4c2.7,0.5,5.2,1.6,7.3,3.4c0.3,0.3,0.6,0.5,0.9,0.8l3.5-3.5c-7.8-7.8-20.5-7.8-28.3,0
				c-1.5,1.6-2.8,3.4-3.8,5.3c-1,2-1.6,4.1-1.9,6.3h-5.1c1.4-13.8,13.6-23.8,27.4-22.5c5.8,0.6,11.2,3.1,15.3,7.2l3.5-3.5
				c-11.7-11.7-30.7-11.7-42.4,0c-2.7,2.7-4.9,6-6.4,9.5c-1.2,2.9-2,6-2.3,9.2c0,0.4-0.1,0.8-0.1,1.2c0,0.4,0,0.8,0,1.2
				c0,0.4,0,0.8,0,1.2c0,0.4,0,0.8,0.1,1.2h10.1C-689.6-294.7-688.9-292.6-688-290.6z"></path>
				<path id="Tracé_1087" class="st0" d="M-640.1-301.8h-5c0,0.4,0.1,0.8,0.1,1.2c0,0.4,0,0.8,0,1.2c0,0.4,0,0.8,0,1.2
				c0,0.4,0,0.8-0.1,1.2c-1.4,13.8-13.6,23.8-27.4,22.5c-5.8-0.6-11.2-3.1-15.3-7.2l-3.5,3.5c2.7,2.7,6,4.9,9.5,6.4
				c1.8,0.8,3.7,1.4,5.7,1.8c3.9,0.8,8,0.8,11.9,0c3.9-0.8,7.5-2.3,10.8-4.5c4.8-3.3,8.6-7.8,10.9-13.2c1.2-2.9,2-6,2.3-9.2
				c0-0.4,0.1-0.8,0.1-1.2c0-0.4,0-0.8,0-1.2c0-0.4,0-0.8,0-1.2C-640-300.9-640.1-301.3-640.1-301.8z"></path>
				</g>
				<path id="txt" class="st0 st2" d="M215.1,45h-4.3l3.4-7.9l-6.4-15.5h4.5l4.1,11.1l4.2-11.1h4.3L215.1,45z M199.2,32.6v-7.7h-1.9v-3.3
				h1.9v-3.9h4.1v3.9h3.6v3.3h-3.6v7.7c0,1.1,0.4,1.5,1.7,1.5h1.9v3.4h-2.5C201.3,37.5,199.2,36.2,199.2,32.6L199.2,32.6z M192.7,19.7
				c-1.3,0.1-2.4-0.9-2.5-2.2c-0.1-1.3,0.9-2.4,2.2-2.5c0.1,0,0.2,0,0.3,0c1.3-0.1,2.4,1,2.5,2.3c0,0,0,0.1,0,0.1
				C195.1,18.7,194,19.7,192.7,19.7C192.7,19.7,192.7,19.7,192.7,19.7z M177.6,37.5l-5.9-15.9h4.3l4,12.2l4-12.2h4.3l-5.9,15.9
				L177.6,37.5z M165.4,35.2c-1.2,1.7-3.2,2.7-5.3,2.6c-4,0-7.2-3.3-7.2-8.3s3.2-8.1,7.3-8.1c2.1-0.1,4,0.9,5.3,2.5v-2.3h4v15.9h-4
				V35.2z M161.2,24.9c-2.2,0-4.2,1.6-4.2,4.6s2,4.7,4.2,4.7c2.2,0,4.2-1.7,4.2-4.7S163.4,24.9,161.2,24.9L161.2,24.9z M146.3,29.6v7.9
				h-4V21.6h4v2.5c1-1.7,2.9-2.7,4.9-2.7v4.2h-1.1C147.7,25.6,146.3,26.5,146.3,29.6z M129.5,37.7c-5.5,0.2-10.1-4.1-10.3-9.6
				c0-0.2,0-0.4,0-0.6c-0.2-5.5,4.1-10.1,9.6-10.2c0.2,0,0.4,0,0.6,0c2.9-0.1,5.6,1,7.6,3.1l-3,3c-1-1.6-2.7-2.5-4.6-2.4
				c-3.6,0-6.1,2.6-6.1,6.5c0,4.1,2.5,6.6,6.3,6.6c2.8,0.2,5.3-1.8,5.7-4.6h-6.9v-3.1h10.9v3.5C138.3,34.5,134.2,37.8,129.5,37.7
				L129.5,37.7z M102.1,37.4c-5.9,0-7.8-4.6-7.8-10.7c0-6.1,1.8-10.6,7.8-10.6c5.9,0,7.8,4.5,7.8,10.6C109.8,32.9,108,37.4,102.1,37.4
				L102.1,37.4z M102.1,19.9c-3.4,0-3.8,3.3-3.8,6.9c0,3.7,0.4,6.9,3.8,6.9s3.8-3.2,3.8-6.9C105.9,23.2,105.5,19.9,102.1,19.9
				L102.1,19.9z M80,34.3l7-9.4H80v-3.3h11.6v3.2l-7,9.4h7.1v3.3H80V34.3z M194.6,37.5h-4V21.6h4V37.5z"></path>
			</svg>
		</a>
		 <ul class="wd-nav">
			<li><a href="<?php echo $html->url('/') ?>"><?php __("Login") ?></a></li>
			<li><a href="https://z0gravity.com" target="_blank"><?php __("Login About us") ?></a></li>
			<li><a href="https://z0gravity.com/contactez-nous/" target="_blank"><?php __("Login Contact") ?></a></li>
		</ul>
	</div>
</header>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                </div>
                <div class="wd-tab">
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <div class="wd-content">
								<div class="wd-version-action">
									<?php 				
										echo $this->Form->input('', array(
											'type'=> 'select',
											'id' => 'version',
											'required' => true,
											'rel' => 'no-history',
											'empty' => __('Select', true),
											'options' => $version_number,
											'label' => false,
											'class' => 'version',
											'div' => false,
											'selected' => $versions['Version']['id'],
										));
									?>
								</div>
								<h2 class="version-title"><?php echo __('Z0Gravity version', true)?>: <span><?php echo $versions['Version']['name']?> </span></h2>
								<div class="wd-version">
									<h3 class="version-log-title"><?php echo __('Change logs', true)?></h3>
									<div class="wd-version-content">
									
										<?php echo nl2br($versions['Version']['content']); ?>
									</div>
								</div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<?php echo $html->script(array(
	'newDesign/webfontloader',
	'newDesign/picturefill',
	'newDesign/webfonts'
)); 
?>
<script>
	$('#version').on('change', function(){
		_this = $(this);
		id = _this.val();
		_this.closest('.wd-content').addClass('loading');
		$.ajax({
			url: '<?php echo $this->Html->url('/') ?>versions/version/'+id,
			dataType: 'json',
			type: 'POST',
			success: function(data){
				if(data.data){
					versions = data.data.Version;
					$('.version-title span').html(versions.name);
					$('.wd-version .wd-version-content').html((versions.content).replace(/\n/g, "<br />"));
					_this.closest('.wd-content').removeClass('loading')
				}
			}
		});
	});
</script>