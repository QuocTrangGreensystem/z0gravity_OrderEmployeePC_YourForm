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
if(isset($typeSelect)) {
	if($typeSelect != 'year'){
	?> 
	<a href="<?php echo $currentUrl; ?>" class="validate-for-validate validation-for-validate-top"><?php echo $this->Html->image("/img/btn-expand.png"); ?></a>
	<?php }
}
else{ ?>
    <a href="<?php echo $currentUrl; ?>" class="validate-for-validate validation-for-validate-top"><?php echo $this->Html->image("/img/btn-expand.png"); ?></a>
<?php 
} ?>
