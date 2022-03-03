<?php
$currentUrl=$_SERVER["REQUEST_URI"];
$myParams = $this->params['url'];

$del = '';
if(isset($myParams['get_path']))
{
	$del .= '&get_path='.$myParams['get_path'];
}
$currentUrl = str_replace($del,'',$currentUrl);
$currentUrl=$currentUrl.'&get_path=1';
$add_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16.002 16.002"><g transform="translate(-120 -231.999)"><rect class="a" width="16" height="16" transform="translate(120 231.999)"/><path class="b" d="M21284,8418v-6h-6a1,1,0,0,1,0-2h6v-6a1,1,0,1,1,2,0v6h6a1,1,0,0,1,0,2h-6v6a1,1,0,1,1-2,0Z" transform="translate(-21157 -8171)"/></g></svg>';
if(isset($typeSelect)) {
	if($typeSelect != 'year'){
	?> 
	<a href="<?php echo $currentUrl; ?>" id="expand-pc-btn" class="btn btn-plus"><?php echo $add_icon; ?></a>
	<?php }
} else{ ?>
    <a href="<?php echo $currentUrl; ?>" id="expand-pc-btn" class="btn btn-plus"><?php echo $add_icon; ?></a>
<?php 
} 
?>
