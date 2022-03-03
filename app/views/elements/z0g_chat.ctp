
<?php

echo $html->css('preview/z0g_chat.css');
?>
<div id="z0g_chat" class="z0g_chat">
    <div class="loading">
        <img width="50" src= "<?php echo $html->url('/img/new-icon/loading.svg') ?>"/>
    </div>
    <div class="z0g_chat_body">
        <div class="z0g_chat_left">
            <div class="z0g_chat_left_search">
                <input id="input-project" type="text" onkeyup="filter()" placeholder="<?php echo __('Rechercher') ?>">
                <img class="btn-search" width="20" src= "<?php echo $html->url('/img/new-icon/searcher.svg') ?>"/>
                <a href="#" class="popup_cancel"><img width="20" src= "<?php echo $html->url('/img/new-icon/cancel.svg') ?>"/></a>
            </div>
            <div id="accordion" class="scrollbar">
                <div class="force-overflow">
                    <ul id="listProject">

                    </ul>
                </div>
            </div>
        </div>
        <div class="z0g_chat_right">
            <a href="javascript:void(0)" class="back">Back</a>
            <div class="z0g_chat_right_comment">
                <form id="form-zog_msg">
                    <textarea class="textarea-comment" name="content" rows="3" cols="40" placeholder="Votre message" style="resize: none; overflow-y: hidden"></textarea>
                    <input type="hidden" class="parent_id" id="parent_id" value="">
                    <input type="hidden" class="project_id" name="project_id" id="project_id">
                    <input type="hidden" id="max_id" name="max_id" value="">
                    <a type="button" class="send" id="submit-btn-msg"><img width="20" src= "<?php echo $html->url('/img/new-icon/send-button.svg') ?>"/></a>
					<a style="right: 55px;top: 34px;position: absolute;" target="_blank" href="<?php echo $this->Html->url('/guides/chat/_chat.htm') ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
						<defs>
						<style>
							.cls-1 {
								fill: #666;
								fill-rule: evenodd;
							}
						</style>
						</defs>
						<path id="help" class="cls-1" d="M1960,40a10,10,0,1,1,10-10A10,10,0,0,1,1960,40Zm0-18.667A8.667,8.667,0,1,0,1968.66,30,8.667,8.667,0,0,0,1960,21.333Zm2.04,8.192q-0.15.146-.39,0.374c-0.16.152-.3,0.284-0.41,0.4a2.539,2.539,0,0,0-.27.286,1.379,1.379,0,0,0-.27.909v0.66h-1.66V31.255a2.13,2.13,0,0,1,.14-0.873,3.544,3.544,0,0,1,.61-0.755l1.07-1.07a1.272,1.272,0,0,0,.34-0.909,1.255,1.255,0,0,0-.35-0.9,1.231,1.231,0,0,0-.91-0.359,1.325,1.325,0,0,0-.93.344,1.347,1.347,0,0,0-.43.917h-1.78a3.024,3.024,0,0,1,1.02-2.046,3.251,3.251,0,0,1,2.18-.741,3.1,3.1,0,0,1,2.12.711,2.488,2.488,0,0,1,.83,1.988,2.246,2.246,0,0,1-.49,1.467C1962.28,29.26,1962.13,29.427,1962.04,29.525Zm-2.15,3.71a1.14,1.14,0,0,1,.8.315,1.027,1.027,0,0,1,.34.763,1.048,1.048,0,0,1-.34.77,1.084,1.084,0,0,1-.79.323,1.136,1.136,0,0,1-.8-0.316,1.015,1.015,0,0,1-.33-0.762,1.04,1.04,0,0,1,.33-0.77A1.07,1.07,0,0,1,1959.89,33.235Z" transform="translate(-1950 -20)"/>
						</svg>
					</a>
                    <a href="#" class="popup_cancel"><img width="20" src= "<?php echo $html->url('/img/new-icon/cancel.svg') ?>"/></a>
                </form>
            </div>
            <h2 id = "wd-t3" class="wd-t3"></h2>
            <div class="time_notify">
                <div class="time"><img width="15" src= "<?php echo $html->url('/img/new-icon/Time.svg') ?>"/> <span></span></div>
                <div class="notify_email">
                    <span style=" vertical-align: top; padding-right: 10px">Notification email</span> <label class="switch"><input type="checkbox" id="subscribe"><div class="slider"></div></label>
                </div>
            </div>
            <div class="loading_cmt" style="display: block">
                <img width="50" src= "<?php echo $html->url('/img/new-icon/loading.svg') ?>"/>
            </div>
			<div class="wrap-list-comment">
				<div class="comment-ct" id="comment-ct">

				</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	
</script>