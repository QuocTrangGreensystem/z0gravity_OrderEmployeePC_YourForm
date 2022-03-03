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
</style>
<div class="trace-container">
	<div class="monospaced trace_log loading" id="trace_log"></div>
</div>
<script>
var message = <?php echo json_encode($message)?>;
var attributes = <?php echo json_encode($attributes)?>;
var trace_log = $('#trace_log');
var opener = window.opener;
var has_openner = false;
if( typeof opener == 'object') {
	if( typeof opener.testPopupMessage == 'function')
		has_openner = true;
}
function trace_reset(){
	trace_log.html('<p>Trace: </p>');
}
function trace_write_value(name, value, type){
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
	trace_write( _html, 'variable', type);
}
function trace_write(t, _class="", type){
	console.log( t);
	if( t !== undefined){
		trace_log.append( $('<p>' + t + '</p>').addClass(_class));
	}
	if( has_openner )opener.testPopupMessage(t, _class, type);
	
}
$(window).ready( function(){
	if( typeof message == 'object'){
		trace_write('Data return from IdP', 'var-name', 'parent');
		$.each(message, function(i,v){
			var _class = '';
			var msg = v;
			if( typeof v == 'object'){
				_class= v['class'];
				msg = v['msg'];
			}
			trace_write(msg, _class);
		});
	}
	if( (typeof attributes == 'object') && (!$.isEmptyObject(attributes))){
		trace_write('Attributes return from IdP', 'var-name', 'parent');
		var email = '';
		$.each(attributes, function(k,v){
			var _class = 'variable';
			var msg = k;
			if( k == 'email') email = v[0];
			if( typeof v == 'object'){
				$.each( v, function(i,t){
					msg = msg + ' / ' + t;
				});
			}else{
				msg = v;
			}
			trace_write(msg, _class);
		});
		if( has_openner && email) opener.check_email(email);
	}
	if( has_openner) {
		opener.finish_test();
		window.close();
	}
});

</script>