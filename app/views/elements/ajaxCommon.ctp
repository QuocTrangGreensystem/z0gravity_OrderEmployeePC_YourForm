<script type="text/javascript">
var loading = "<span id='loadingElm'><img src='<?php echo $this->Html->webroot('img/ajax-loader.gif'); ?>' alt='Loading' /></span>";
function editMe(field,value)
{
	$(loading).insertAfter('#'+field);
	var data = field+'/'+value;
	$.ajax({
		url: '/company_configs/editMe/',
		data: {
			data : { value : value, field : field }
		},
		type:'POST',
		success:function(data) {
			$('#'+field).removeClass('KO');
			$('#loadingElm').remove();
		}
	});
}
</script>