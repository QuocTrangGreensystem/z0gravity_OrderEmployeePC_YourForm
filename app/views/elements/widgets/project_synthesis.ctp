<?php 
$widget_title = !empty( $widget_title) ? $widget_title : __('Synthesis', true);
$employee_info = $this->Session->read('Auth.employee_info');
$read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0; 
$options = array(
    // 'ProjectAmr'  => __('Synthesis Comment', true),
    'Done'        => __d(sprintf($_domain, 'KPI'), 'Done', true),
    'ProjectRisk' => __d(sprintf($_domain, 'KPI'), 'Risk', true),
    'ProjectIssue' => __d(sprintf($_domain, 'KPI'), 'Issue', true),
    'ProjectAmr'  => __d(sprintf($_domain, 'KPI'), 'Comment', true),
);
$icons_comment = array(
	'edit' => '<svg id="icon-modify" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
  <rect id="box" width="24" height="24" fill="none"/>
  <path id="icon" d="M67.066,288.393a.674.674,0,0,1-.648-.86l1.192-4.172a.669.669,0,0,1,.172-.291l8.047-8.047a2.153,2.153,0,0,1,3.04,0l.894.895a2.15,2.15,0,0,1,0,3.04L71.715,287a.671.671,0,0,1-.291.171l-4.173,1.192A.689.689,0,0,1,67.066,288.393Zm.982-1.656,2.837-.811,6.254-6.254-2.027-2.026L68.859,283.9Zm10.046-8.019.715-.715a.8.8,0,0,0,0-1.132l-.895-.9a.8.8,0,0,0-1.133,0l-.715.716Z" transform="translate(-61.392 -269.395)" fill="#7b7b7b"/>
</svg>',
    'delete' => '<svg id="icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
  <rect id="blank" width="24" height="24" fill="none"/>
  <path id="icon" d="M6,13V8H1A1,1,0,0,1,1,6H6V1A1,1,0,0,1,8,1V6h5a1,1,0,0,1,0,2H8v5a1,1,0,0,1-2,0Z" transform="translate(12.001 2.1) rotate(45)" fill="#7b7b7b"/>
</svg>'
);
if( empty( $model_display )) $model_display = $options;
$count_model = count($model_display);
$synth_width = round(100 / $synthesis_column , 1); 
$history_synthesis_height = !empty( $filter_render['indicator_synthesis_comment_height']) ? $filter_render['indicator_synthesis_comment_height'] : '';
?>
<div class="wd-widget project-synthesis-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
            <div class="widget-action">
				<div class="wd-column-action"> 
					<a href="javascript:void(0);" class="wd-synth-column wd-synth-column-2 <?php echo $synthesis_column == 2 ? 'active' : ''; ?>" data-column= 2 >
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
						  <g id="icon-display-2columns">
							<rect id="blank" width="24" height="24" fill="none"/>
							<path id="icon" d="M11,20V0h5a4,4,0,0,1,4,4V16a4,4,0,0,1-4,4Zm2-2h3a2,2,0,0,0,2-2V4a2,2,0,0,0-2-2H13ZM4,20a4,4,0,0,1-4-4V4A4,4,0,0,1,4,0H9V20ZM2,4V16a2,2,0,0,0,2,2H7V2H4A2,2,0,0,0,2,4Z" transform="translate(2 2)" fill="#fff"/>
						  </g>
						</svg>
					</a>
					<a href="javascript:void(0);" class="wd-synth-column wd-synth-column-4 <?php echo $synthesis_column == 4 ? 'active' : ''; ?>" data-column= 4 >
						<svg id="icon-display-4columns" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
						  <rect id="blank" width="24" height="24" fill="none"/>
						  <path id="icon" d="M11,20V11h9v5a4,4,0,0,1-4,4Zm2-2h3a2,2,0,0,0,2-2V13H13ZM4,20a4,4,0,0,1-4-4V11H9v9ZM2,16a2,2,0,0,0,2,2H7V13H2Zm9-7V0h5a4,4,0,0,1,4,4V9Zm2-2h5V4a2,2,0,0,0-2-2H13ZM0,9V4A4,4,0,0,1,4,0H9V9ZM2,4V7H7V2H4A2,2,0,0,0,2,4Z" transform="translate(2 2)" fill="#fff"/>
						</svg>
					</a>
				</div>
                <a href="javascript:void(0);" onclick="wd_synthesis_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
                <a href="javascript:void(0);" onclick="wd_synthesis_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
            </div>
        </div>
        <div class="widget_content">
            <div class="synthesis-logs <?php echo 'log-column-'.$count_model; ?> <?php echo 'col-synth-'.$synthesis_column; ?>" style="height: <?php echo $history_synthesis_height;?>px">
            <?php foreach ($model_display as $key => $is_display) {
                $log_title = $options[$key];
				?>
                <div class="log-content" style="width: <?php if($count_model > 2 ) echo $synth_width; ?>%">
                    <h4 class="log-title"> <?php echo $log_title ?> </h4>
                    <ul data-type="<?php echo $key; ?>"  class="<?php echo $key; ?>">
    					<?php if(!empty($logGroups[$key])){ 
    						$i = 1;
    						foreach ($logGroups[$key] as $data) {
                                $url = $this->UserFile->avatar($data['employee_id']);
								$_e_name = !empty($listEmployeeNames[$data['employee_id']]) ? $listEmployeeNames[$data['employee_id']] : '';
                                $avatar = '<p class="circle-name" title="'. $_e_name.'"><img src="'. $url .'"/></p>';
								$show_class = ($i <=3) ? 'show' : 'hidden';
                           
                        ?>
                        <li class="cmt-<?php echo $data['id']; ?> <?php echo $show_class; ?>" data-id="<?php echo $data['id'] ?>" style="display:<?php echo ($synthesis_column == 2 && $i > 1) ? 'none' : ''; ?>">
                            <div class="log-item">
                                <?php echo $avatar; ?>
								<div class="log-info">
									<span class="log-time"><?php echo date('d ', $data['updated']). __(date('M', $data['updated']),true).date(' Y', $data['updated']) ?></span>
									<?php if($data['employee_id']== $employee_info['Employee']['id']){ ?>
										<a href="javascript:void(0)" onclick="open_edit_popup(this, '<?=$data['model']?>')" data-log-id = "<?php echo $data['id']; ?>" class="log-action log-field-edit"><?php echo $icons_comment['edit']?></a>
										<a href="javascript:void(0)" onclick="delete_comment(this, '<?=$data['model']?>')" data-log-id = "<?php echo $data['id']; ?>" class="log-action log-field-delete"><?php echo $icons_comment['delete']?></a>
										
									<?php } ?>
								 </div>
								 <?php 
									$cmt_desc = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a target="_blank" href="$1">$1</a>', nl2br($data['description']));
								 ?>
                                <div class="cont_cmt" data-log-id="<?php echo $data['id'] ?>"><?php echo $cmt_desc; ?></div>
                           
                            </div>
                        </li>
                        <?php
                            // if($i == 3) break;
                            $i++;  
                            }
                        } ?>
                    </ul>
                    <?php if(!$read_only){ ?>
                    <a href="javascript:void(0);" onclick="show_comment_popup(this, '<?php echo $key; ?>');" class="log-addnew"></a>
                    
                    <?php } ?>
                </div>
                <?php }  ?>
            </div>
			<div style="display:none;">
				<!-- This input is used for activating HistoryFilter -->
				<input type="hidden" id="indicator_synthesis_comment_height" name="indicator_synthesis_comment_height" value="<?php echo $history_synthesis_height;?>"/>
			</div>
        </div>
    </div>
</div>
<?php echo $this->element("comment_popup") ?>
<style>

    .project-synthesis-widget .widget_content .log-content{
        width: 25%;
        display: inline-block;
        vertical-align: top;
        position: relative;
        float: left;
		padding-right: 15px;
		-webkit-transition: all 0.4s ease 0s;
		-moz-transition: all 0.4s ease 0s;
		-o-transition: all 0.4s ease 0s;
		transition: all 0.4s ease 0s;
		padding-top: 20px;			
    }
    /*.project-synthesis-widget .widget_content .log-content:not(:last-child){
        width: calc(25% - 4px);
    }*/
    .project-synthesis-widget .widget_content .log-title{
        color: #33678b;
		font-family: "Open Sans";
		font-size: 12px;
		font-weight: bold;
		line-height: 40px;
        text-transform: uppercase;
        margin-bottom: 10px;	
		box-sizing: border-box;
		padding-left: 10px;
		background: #F6FAFD 0% 0% no-repeat padding-box;
		border: 1px solid #DDEBF6;
		border-radius: 8px;
		opacity: 1;
		margin-right: 14px;
    }
    .widget_content .log-content textarea{
        display: block;
        width: 100%;
        border: none;
        margin-top: 6px;
        resize: none;
        color: #424242; font-family: "Open Sans";   font-size: 14px;    line-height: 18px;
    }
    .widget_content .log-content > ul{
        position: relative;
		overflow: auto;
		padding-right: 15px;
    }
    /* .widget_content .log-content ul li:not(:last-child){ */
    .widget_content .log-content .circle-name{
        width: 40px;
        line-height: 40px;
        height: 40px;
        font-size: 14px;
        top: 0;
        margin-right: 0;
    }
    .widget_content .log-content .circle-name img{
        width: 100%;
        height: auto;
    }
    .widget_content .log-content .log-addnew{
        position: absolute;
        width: 24px;
        height: 24px;
        /* bottom: -15px; */
        top: 29px;
        right: 39px;
        border-radius: 50%;
        transition: all 0.2s ease;
        z-index: 3;
		background: #217FC2 0% 0% no-repeat padding-box;
		opacity: 1;
    }
    .widget_content .log-content .log-addnew:before,
    .widget_content .log-content .log-addnew:after{
        content: '';
        width: 2px;
        height: 10px;
        margin-top: 7px;
        margin-left: 11px;
		background-color: #fff;
        position: absolute;
    }
    .widget_content .log-content .log-addnew:after{
        width: 10px;
        height: 2px;
        margin-top: 11px;
        margin-left: 7px;
    }
    .widget_content .log-content .log-addnew:hover{
        opacity: 0.8;
    }
    .widget_content .log-content ul{
        position: relative;

    }
    
    .add-comment .add-comment-submit{
        line-height: 20px;
        padding: 10px 20px;
        min-width: 80px;
        border-radius: 20px;
        background-color: #538fFa;
        color: #fff;
        transition: all ease 0.2s;
        display: inline-block;
        text-align: center;
        text-transform: uppercase;
    }
    .add-comment .submit-row{
        text-align: right;
    }
    .add-comment .add-comment-submit:hover {
        opacity: 0.9;
        text-decoration: none;
    }
    ul.loading{
        position: relative;
    }
    ul.loading:before{
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        left: 0;
        top: 0;
        z-index: 2;
    }
    ul.loading:after{
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: url(/img/business/wait-1.gif) no-repeat center center;
        z-index: 3;
        display: block;
        background-size: cover;
        width: 50px;
        height: 50px;
    }

    .project-synthesis-widget .ui-resizable-s {
        width: 100%;
        height: 8px;
        background-color: red;
        display: block;
        cursor: s-resize;
        position: absolute;
        bottom: -4px;
        z-index: 3;
        background: url(/img/new-icon/resizable-handle.png) center no-repeat;
    }
    .synthesis-logs{
        overflow-y: auto;
    }
    .project-synthesis-widget .synthesis-logs{
        height: 280px;
    }
    .content-logs .comment{
        height: auto;
        font-size: 14px;
        color: #424242;
    }
    .synthesis-logs.log-column-2 .log-content:nth-child(3),
	.synthesis-logs.log-column-4.col-synth-2 .log-content:nth-child(3){
		clear: left;
	}
	.widget_content .log-content .circle-name{
		display: inline-block;
	}
	.widget_content .log-content .log-info{
		display: inline-block;
		width: calc(100% - 44px);
		position: relative;
		top: 13px;
	}
	.widget_content .log-content .log-time{
		padding-left: 10px;
	}
    .project-synthesis-widget .widget_content .log-column-1 .log-content{
        width: 100%;
    }
    .project-synthesis-widget .widget_content .log-column-2 .log-content{
        width: 50%;
    }
    .project-synthesis-widget .widget_content .log-column-3 .log-content{
        width: 33.3%;
    }

	@media print{
		.widget-expand .project-synthesis-widget .synthesis-logs{
			height: auto !important;
		}
	}
	.project-synthesis-widget .log-content .log-item p{
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.synthesis-logs .log-content div.cont_cmt {
		padding: 10px;
		box-sizing: border-box;
		background: #FCFCFC 0% 0% no-repeat padding-box;
		border: 1px solid #E8E8E8;
		border-radius: 8px;
		opacity: 1;
	}
    .widget_content .log-content .circle-name{
        top: 6px;
    }
	.widget_content .log-content .log-time	{
		height: 20px;
		line-height: 20px;
		color: #242424;
		font-size: 12px;
		font-weight: 600;
		position: relative;
		top: 0px;
		width: calc(100% - 103px);
		display: inline-block;
		vertical-align: top;
	}
	.widget_content .log-content .log-time{
		width: calc(100% - 56px);
	}
	a.cmt_edit {
		margin-right: 5px;
	}
	.comment {
		background: none;
	}
	.content {
		margin-top: 5px;
	}
	#template_logs .comment-lists .content .comment {
		margin-bottom: 0px;
	}
	.wd-comment-dialog textarea{
		background-color: #f0f2fc;
		box-shadow: none;
		border-radius: 10px;
	}
	.wd-column-action .wd-synth-column svg{
		opacity: 0.3;
	}
	.wd-column-action .wd-synth-column.active svg{
		opacity: 1;
	}
	.synthesis-logs{
		padding-top: 5px;
	}
	.synthesis-logs .log-content .log-item .cont_cmt a{
		color: #217FC2;
		font-weight: 600;
	}
	.synthesis-logs .log-content .log-item p{
		font-size: 14px;
		color: #424242;
	}

</style>