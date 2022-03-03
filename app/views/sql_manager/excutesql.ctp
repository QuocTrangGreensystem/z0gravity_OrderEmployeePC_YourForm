<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
    .table-result {
        margin:0px;padding:0px;
        width:100%;
        max-height: 800px;
        border:1px solid #000000;
        border-radius: 0px;
        overflow: auto;
    }
    .table-result table{
        border-collapse: collapse;
        border-spacing: 0;
        width:100%;
        height:100%;
        margin:0px;
        padding:0px;
    }
    .table-result tr:last-child td:last-child {
        -moz-border-radius-bottomright:0px;
        -webkit-border-bottom-right-radius:0px;
        border-bottom-right-radius:0px;
    }
    .table-result table thead td:first-child {
        -moz-border-radius-topleft:0px;
        -webkit-border-top-left-radius:0px;
        border-top-left-radius:0px;
    }
    .table-result table thead td:last-child {
        -moz-border-radius-topright:0px;
        -webkit-border-top-right-radius:0px;
        border-top-right-radius:0px;
    }
    .table-result tr:last-child td:first-child{
        -moz-border-radius-bottomleft:0px;
        -webkit-border-bottom-left-radius:0px;
        border-bottom-left-radius:0px;
    }
    .table-result tr:hover td{

    }
    .table-result tr:nth-child(odd){ background-color:#CEE3F6; }
    .table-result tr:nth-child(even)    { background-color:#ffffff; }.table-result td{
        vertical-align:middle;
        border:1px solid #000000;
        border-width:0px 1px 1px 0px;
        text-align:left;
        padding:4px 6px;
        font-size:12px;
        font-family:Arial;
        font-weight:normal;
        color:#000000;
    }.table-result tr:last-child td{
        border-width:0px 1px 0px 0px;
    }.table-result tr td:last-child{
        border-width:0px 0px 1px 0px;
    }.table-result tr:last-child td:last-child{
        border-width:0px 0px 0px 0px;
    }
    .table-result thead th{
        /*background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );*/
        background: #f7f7f7 none repeat scroll 0 0;
        /*filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);*/

        background-color:#f7f7f7;
        border:0px solid #000000;
        text-align:center;
        border-width:0px 0px 1px 1px;
        font-size:14px;
        font-family:Arial;
        font-weight:bold;
        color:#000000;
        padding: 4px 6px;
    }
    .table-result thead:hover td{
        background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
        background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);

        background-color:#cccccc;
    }
    .table-result thead td:first-child{
        border-width:0px 0px 1px 0px;
    }
    .table-result thead td:last-child{
        border-width:0px 0px 1px 1px;
    }
    .light-table-filter{

        background-color: #fff;
        border: 1px solid #d4d4d4;
        padding: 6px 5px;
        width: 200px;
    }
    .btn-menu{
        margin-left: 100px;
        display: inline;
    }
	.wd-tab .wd-panel.pm-logged{
		border: none;
	}
</style>
<link rel="stylesheet" type="text/css" href="/css/jquery.dynatable.css">
<script src="/js/jquery.dynatable.min.js" type="text/javascript"></script>

<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">

                </div>
                <div id="message-place">
                    <?php
                    App::import("vendor", "str_utility");
                    $str_utility = new str_utility();
                    echo $this->Session->flash();
                    ?>
                </div>
                <div class="wd-tab">
                    <?php
					$extra_class = 'pm-logged';
					$is_sas = !empty($employee_info['Employee']['is_sas']);
					$is_admin = !empty($employee_info['Role']['name']) && $employee_info['Role']['name'] == 'admin';
					if( $isAjax){
						$extra_class = '';
					}else{
						if ($is_sas || $is_admin){
							$extra_class = '';
							echo $this->element("admin_sub_top_menu"); 
							
						}
					}
					?>
                     <div class="wd-panel <?php echo $extra_class ?>">
						<?php if( !empty($show_iframe)){
							$iframeText = trim(str_replace( PHP_EOL, ' ', $iframeText));
							preg_match('/(.*)<iframe(.*)<\/iframe>$/', $iframeText, $matches);
							if( !empty( $matches[0])) $iframeText = $matches[0];
							else{
								$iframeText = '<iframe width="100%" height="100%" src="' . $iframeText . '"></iframe>';
							}


						?>
							<div class="wd-section">
								<div class="wd-content">
									<?php echo $iframeText;?>
								</div>
							</div>
						<?php }else{ ?>
							<div class="wd-section" id="wd-fragment-1">
								<div class="wd-content" >
									<div class="input-result" style="margin-bottom: 10px">
									<a id="btn_back"   class="btn-text btn-blue" href="<?php echo $html->url('/sql_manager/') ?>"><img src="/img/ui/icon-back.png"> <span><?php __('Back') ?> </span> </a>
									<div class="btn-menu">
									<a id="export-submit" class="btn btn-excel" title="Export Excel" href="javascript:void(0);">
									<span><?php __('Export Excel') ?></span>
									</a>
									</div>
									</div>
									<?php if($status==""){ ?>
									<div class="table-result">
										<table class="order-table table" id="tb-result">
											<thead>
												<tr>
													<?php if( !empty( $columns)){
														foreach ($columns as $col) {
															$namecol = strtolower($col);
														   echo "<th data-dynatable-column='$namecol'> $col </th>"; 
														} 
													}else{
														 echo "<td>". __('Data Empty', true) ."</td>"; 
													} ?>
												</tr>
											</thead>
											<tbody>
												
											</tbody>
										</table>
									</div>
									<?php }else{ ?>

									<div id="flashMessage" class="message">
										<h2 class="error-message"> <?php echo $status; ?></h2>
									</div>
									<?php }?>
								</div>

							</div>
						<?php } ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if( empty($show_iframe)){ ?>
	<form id="form-export" action="/sql_manager/export_excel" method="post">
			<textarea style="display:none" type="hidden" name="requireSql"><?php echo $requireSql ?></textarea>
			<input type="hidden" name="export" value="1" />
	</form>
<?php } ?> 
<script>
<?php if( !empty($show_iframe) ){ ?>
	<?php if( empty($isAjax) ){ ?>
		var wdPanel = $('.wd-panel .wd-content:first');
		function set_height_panel(){
			var heightPanel = $(window).height() - wdPanel.offset().top - 40;
			wdPanel.css({
				height: heightPanel,
			});
		}
		set_height_panel();
		$(window).on('resize', set_height_panel);
	<?php } ?> 
<?php }else{ ?>
	(function(document) {
		'use strict';
		var myRecords = <?php echo json_encode($datas); ?>; 
	//    myRecords =  $.parseJSON(records);

	//    console.log(myRecords);
		var LightTableFilter = (function(Arr) {

			var _input;

			function _onInputEvent(e) {
				_input = e.target;
				var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
				Arr.forEach.call(tables, function(table) {
					Arr.forEach.call(table.tBodies, function(tbody) {
						Arr.forEach.call(tbody.rows, _filter);
					});
				});
			}

			function _filter(row) {
				var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
				row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
			}

			return {
				init: function() {
					var inputs = document.getElementsByClassName('light-table-filter');
					Arr.forEach.call(inputs, function(input) {
						input.oninput = _onInputEvent;
					});
				}
			};
		})(Array.prototype);

		document.addEventListener('readystatechange', function() {
			if (document.readyState === 'complete') {
				LightTableFilter.init();
			}
		});

		$("#export-submit").click(function (){
			<?php if($status==""){ ?>
			$("#form-export").submit();
			<?php } ?>
		});
		$('#tb-result').dynatable({
			dataset: {
			  records: myRecords,
			  perPageDefault: 20
			}
		});   

	})(document);
<?php } ?> 
</script>
