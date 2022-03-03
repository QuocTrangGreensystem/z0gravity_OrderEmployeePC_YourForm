<?php 
if($current==$limit){
    echo "<div class='ticket--component alerts__box-warning'>";
} elseif ($current>$limit) {
    echo "<div class='ticket--component alerts__box-error'>";
} else {
    echo "<div class='ticket--component alerts__box-ok'>";
}

?>

    <p class='ticket--content__title'><?php echo $content_title?></p>
    <span class='ticket--content__desc'><?php echo $content_description; ?></span>
</div>