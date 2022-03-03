<style>
html *{
	box-sizing: border-box;
}
.monospaced{
	font-family: monospace;
	font-size: 12px;
}
#trace_log{
	min-height: 500px;
	overflow: auto;
	width: 100%;
	height: 100vh;
	background: #efefef;
	padding: 15px;
}
pre{
	font-size: 90%;
	color: #777;
	margin: 5px 20px 8px 20px;
	
}
.var-name{
	margin-top: 20px;
}
.error{
	color: red;
}
.trace_log .success{
	color: green;
}
.trace_log .result{
	font-weight: 600;
}
</style>
<div class="trace-container">
	<div class="monospaced trace_log loading" id="trace_log">
		<p> <?php __('Test Connection to SSO server with config');?> </p>
		<?php if( !$result){
			foreach( $message as $m){
				echo "<p class='error'>$m</p>";
			}
		}?>
	</div>
</div>
<script>
var sp_info = <?php echo json_encode($settingsInfo['sp'])?>;
var company_id = <?php echo json_encode($company_id)?>;
var settingsInfo = <?php echo json_encode($settingsInfo)?>;
var result = <?php echo json_encode($result)?>;
var IssuerUrl = <?php echo json_encode($IssuerUrl)?>;
var SamlEndPoint = <?php echo json_encode($SamlEndPoint)?>;
var SloEndPoint = <?php echo json_encode($SloEndPoint)?>;
var Certificate = <?php echo json_encode($Certificate)?>;
var ssoBuiltUrl = <?php echo json_encode($ssoBuiltUrl)?>;
var trace_log = $('#trace_log');
var popupWindow;
function trace_reset(){
	trace_log.html('<p>Trace: </p>');
}
function trace_write_value(name, value){
	trace_write(name + ': ', 'var-name');
	var tab = '   ';
	$i = 1;
	var _html = '<pre><code>';
		if( typeof value == 'undefined'){
			_html += tab + 'undefined';
		}else if( typeof value == 'object'){
			_html += JSON.stringify(value, undefined, 3);
		}else{
			_html += tab + value;
		}
	_html += '</code></pre>';
	trace_write( _html, 'variable');
}
function trace_write(t, _class="", before, after){
	if( t !== undefined){
		trace_log.append( $('<p>' + t + '</p>').addClass(_class));
	}
	
}
$(window).ready( function(){
	if( result ){
		trace_write_value('SP setting', sp_info);
		trace_write_value('IdP setting', {
			IssuerUrl : IssuerUrl,
			SamlEndPoint : SamlEndPoint,
			SloEndPoint : SloEndPoint,
			Certificate : Certificate
		});
		trace_write_value( 'Connecting to SSO', ssoBuiltUrl);
		popupWindow = window.open(ssoBuiltUrl, 'sso_testing', 'top=0');
	}
	
});
function testPopupMessage(mes, _class, type) {
	var type = type||'child';
	if( type == 'parent')
		trace_write(mes, _class);
	else{
		trace_write('<pre class="variable">' +  mes + '</pre>', _class);
	}
}	
function check_email(email) {
	$.ajax({
		type: 'get',
		async: false,
		url: '/sso_logins/test_email/' + email,
		dataType: 'json',
		success: function(res){
			if( res.result){
				trace_write(res.message, res.result);
			}else{
				
			}
		},
		error: function(){
			var message = 'You are logged out from system. Can not check employee';
			// trace_write(message, 'log'	);
		},
		complete: function(){ 
		}
		
	});
}
function finish_test() {
	trace_log.removeClass('loading');
	trace_write('Finish!', 'success');
}

</script>