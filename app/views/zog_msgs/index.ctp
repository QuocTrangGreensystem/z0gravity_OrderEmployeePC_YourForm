<?php echo $html->script('jquery.multiSelect'); ?>
<?php echo $html->css('jquery.multiSelect'); ?>
<!-- <?php echo $html->script('history_filter'); ?> -->
<?php echo $html->css('slick_grid/slick.grid'); ?>
<?php echo $html->css('slick_grid/slick.pager'); ?>
<?php echo $html->css('slick_grid/slick.common'); ?>
<?php echo $html->css('slick_grid/slick.edit'); ?>
<style>
/*#comment-ct{
    min-height: 60px;
}
#project_container{
    width: 503px;
    height: 531px;
    padding: 20px;
    padding-left: 0px;
}
.comment-ct{
    height: 500px;
    overflow-y: scroll;
    border-bottom: 1px solid #ccc;
}
button{
    width: 50px;
    height: 50px;
    background-color: #56aaff;
    display: inline-block;
    vertical-align: top;
    border: none;
    cursor: pointer;
}
.comment-btn textarea{  
    width: calc( 100% - 55px);
    height: 48px;
    border: 1px solid #0cb0e0;
    color: #242424;
    font: normal normal 1em Arial, Helvetica, sans-serif;
    outline: 0;
    padding: 0px;
    border-left: none;
    display: inline-block;
    margin-left: -4px;
    padding-top: 5px;
    padding-left: 3px;
}
.wd-tab .wd-content {
    overflow-x: hidden;
    overflow-y: hidden;
    padding: 0;
    position: relative;
    top: 20px;
    left: 50px
}
.comment{
    min-height: 70px;
    max-width: 320px;
    background-color: #eee;
    border-bottom: 5px solid #fff;
    margin-left: 10px;
    width: auto;
    float: left;
    padding-right: 30px;
    border-radius: 10px;
    margin-right: 40px;
}
.left-comment{
    width: 50px;
    float: left;
    clear: both;
    overflow: hidden;
}
.right-comment{
    padding-top: 8px;
    max-width: 684px;
    margin-left: 10px;
    padding-bottom: 5px;
}
.right-comment b{
    color: #578cca;
}
.right-comment span {
    margin-left: 10px;
}
.input-project{
    height: 20px;
    width: 95%;
    margin-right: 10px;
    padding-left: 10px;
}
.hidden{
    display: none;
}
.date{
    font-weight: 700;
}
.subscribe{
     float: right; 
    font-size: 15px;
     font-family: "Times New Roman", Georgia, Serif; 
    margin-bottom: -29px;
    font: normal normal 100%/1.35 Arial, Helvetica, sans-serif;
    color: #363636;
}
.comment-none{
    max-width: 458px;
    min-height: 49px;
    margin-top: 20px;
}

.my-comment{
    display: inline-block;
    width: calc( 100% - 65px);
}
.my-avatar{
    display: inline-block;
    vertical-align: top;
    margin-top: 2px;
    width: 60px;

}
.avatar{
    width: 40px;
    height: 40px;
    margin: 0 10px;
    padding: 5px;
    border: 1px solid #bbb;
    border-radius: 3px;
}
.my-date{
    font-size: 10px;
    color: #888;
    display: inline-block;
    padding-left: 10px;
}
.my-content{
    clear: both;
    padding-left: 10px;
}
.btn-order{
    margin-bottom: 30px;
    border: none;
    padding: 5px;
    background-color: #fff;
    color: #fff;
    margin-right: 20px;
}
.subscribe span{
    font-size: 15px;
}
.btn-order:hover{
    cursor: pointer;
}
.btn-order img{
    height: 32px;
    margin-top: -15px;
    outline : none;
    border : 0;
    -moz-outline-style: none;
}
.btn-order:focus{
    outline:0;
}
.right-avatar{

}
.right-avatar img{
   
}
span em{
    font-size: 12px;
}
.show-hide img{
    height: 30px;
    margin-top: 24px;
    clear: both;
}
.show-hide img:hover{
    cursor: pointer;
}
.hidden-div{
    display: none;
}
.message-new-ct{
    float: right;
    background-color: red;
    color: #fff;
    border-radius: 50%;
    width: 20px;
    text-align: center;
    display: block;
    height: 20px;
    margin-top: 5px;
    margin-right: 5px;
    line-height: 20px;
}
.wd-tab .wd-panel h2.wd-t3{
    font-size: 1.8em;
    font-weight: 700;
    padding: 0;
    margin: 0;
    color: #ffb250;
}
.wd-table{
    background: transparent !important;
}*/
<?php
if(!$isMobile):
?>
.wd-tab .wd-content {
    display: block;
    /*z-index: 1002;*/
    outline: 0px;
    height: auto;
    width: 500px;
    border: 1px solid #eee;
    box-shadow: 2px 2px 2px #eee;
}
<?php
endif;
if( $isTablet ):
?>
#comment-ct{
    /*max-height: 400px;
    width: 95%;
    max-height: 77%;*/
}
.project-title{
    /*margin-top: -10px;*/
}
.right-comment{
    /*max-width: 200px;*/
}
.comment-btn textarea{
    width: 70%;
    height: 50px;
}
.submit-btn-msg{
    height: 68px;
}
.wd-tab .wd-panel h2.wd-t3 {
    font-size: 13px;
    font-weight: bold;
}
<?php endif;
if($isMobile): ?>
#project_container:not(.project-list){
    height: auto !important;
    max-width: 80%;
}
.comment-btn textarea {
    width: 65%;
    height: 46px;
}
.submit-btn-msg {
    width: 80px;
}
.project-title {
    margin-left: 0px;
    margin-top: -0px;
}

<?php endif; ?>
.project-title{
    background-color: #fff;
    color: #000;
    border: 1px solid #ccc;
    border-width: 1px !important;
    padding: 5px;
}
.user-name{
    color: #08c;
    font-size: 14px;
    display: inline-block;
    margin-right: 5px;
}
.comment-content{
    margin-bottom: 16px;
}
</style>

<script type="text/javascript">

</script>
