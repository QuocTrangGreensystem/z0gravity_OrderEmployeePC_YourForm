<?php
$url = array();
foreach ($this->passedArgs as $key => $value) {
	if ($key == "page") {
		$url[] = @urlencode($value);
	}
}
$query = array();
foreach ($this->params['url'] as $key => $value) {
	
	if ($key !== "url") {
		$query[] = "{$key}=".@urlencode($value);
	}
}
?>
<script language='javascript'>

var paging_url = '<?php echo implode("/", $url)?>';

var query = '<?php echo implode("&", $query)?>';
$(document).ready(function () {
	$('#goBtn').click(function() {
		if(paging_url!=""){
			url = "<?php echo $html->url('/'.$this->params['controller']."/".$this->params['action']."/");?>"+paging_url+"/page:"+$('#page').val()+"?"+query;
		}else{
			url = "<?php echo $html->url('/'.$this->params['controller']."/".$this->params['action']."/");?>"+"page:"+$('#page').val()+"?"+query;
		}
			window.location = url;
	});
});
</script>
<?php if ($paginator){?>
<div class="paging-wrapper">
<div class="paging" id="pagination">
	<!--<span class="page">
		<?php
		echo $this->Paginator->counter(array(
		'format' => __('Trang <strong>%page%</strong>/%current% Tổng:<strong> %pages%</strong>', true)
		));
		?>
	</span>-->
    <span class="page">
	<?php	
		echo $this->Paginator->counter(array('format' => '&nbsp; Trang <strong>%page%</strong>/%pages% - Tổng: <strong>%count%</strong>'));
	?>
	</span>
	<?php
		if (isset($this->params['url'])) {
			$this->passedArgs["?"] = $this->params['url'];
			if (isset($this->passedArgs["?"]['url'])) {
				unset($this->passedArgs["?"]['url']);
			}
		}
		
		$paginator->options(array('url'=>$this->passedArgs));
		echo $paginator->first("prev",array('class'=>'prev'));
		echo $paginator->numbers(array('separator'=>'&nbsp;'));
		echo $paginator->last("next",array('class'=>'next'));	  
	?>
</div>
</div>					
<?php } ?>