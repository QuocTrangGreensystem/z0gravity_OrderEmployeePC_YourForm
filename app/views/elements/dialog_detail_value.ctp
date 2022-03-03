<div id="dialogDetailValue">
    <img id="closePopup" alt="Close Popup" src="<?php echo $this->Html->url('/img/close.png') ?>" onClick="hideMe();" />
    <div class="dialog-detail-inner">
		<div id="contentDialog"></div>
	</div>
</div>
<style>
#closePopup{
    cursor:pointer;
    position:absolute;
    z-index:99999999999;
    background-color: #ddd;
    opacity: 0.8;
    transition: opacity 0.2s;
    float: right;
    right: 0;
    top: 0;
}
#closePopup:hover {
    opacity: 1;
}
#dialogDetailValue{
    position:fixed;
    top:50%;
    left:50%;
	min-width:960px;
    min-height:200px;
    /*overflow:auto;*/
    background-color:#FFF;
    z-index:100;
    padding:0px 0px;
    display:none;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5);
    color:#333;
    overflow: auto;
}
#contentDialog{
    padding: 10px;
}
.dialog-detail-inner{
	width: 100%;
    display: block;
    position: relative;
    overflow: auto;
}
</style>
<script>
function hideMe() {
    jQuery('#dialogDetailValue').hide();
    jQuery(".light-popup").removeClass('show');
	jQuery('#contentDialog').empty();
}
function showMe() {
    var hp=jQuery('#dialogDetailValue').height();
    var mtp=parseInt(hp)/2;
    var wp=jQuery('#dialogDetailValue').width();
    var mlp=parseInt(wp)/2;
    // jQuery('#dialogDetailValue').css( {marginTop: -mtp});
    // jQuery('#dialogDetailValue').css( {marginLeft: -mlp});
    jQuery('#dialogDetailValue').show('fast');
    //jQuery('#closePopup').css( {marginLeft: mlp*2 - 35});
}
</script>
