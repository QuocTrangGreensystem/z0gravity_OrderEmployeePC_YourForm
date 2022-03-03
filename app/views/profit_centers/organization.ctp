<?php echo $html->css('jquery.jOrgChart.css'); ?>
<?php echo $html->css('custom_chart'); ?>
<?php echo $html->script('jquery.jOrgChart'); ?>
<!--[if lt IE 9]>
<?php echo $html->script('flash_canvas/flashcanvas'); ?>
<script type="text/javascript">
    var _createElement = document.createElement;
    document.createElement = function(n){
        var element = _createElement.call(this,n);
        if(n=="canvas"){
            document.getElementById("target").appendChild(element);
            FlashCanvas.initElement(element);
        }
        return element;
    };
</script>
<div id="target" style="position: absolute; top: -10000px;left: -999999px;"></div>
<![endif]-->
<?php echo $html->script(array('html2canvas', 'jquery.html2canvas.organization')); ?>
<style>
    .wd-layout{}
    #overlay-container{
        display: none;
    }
    #overlay-wrapper{
        position: fixed;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 10000;
        background: #000;
        opacity: 0.5;
        filter:alpha(opacity=50);
    }
    #overlay-box{
        position: fixed;
        top: 50%;
        left: 50%;
        z-index: 10001;
        height: 100px;
        margin-left: -100px;
        margin-top: -50px;
        padding-top: 70px;
        width: 200px;
        text-align: center;
        font-weight: bold;
        background: url("../img/loader.gif") top center no-repeat;
    }

    .wmd-view{
        overflow-x: auto;
        width: 100%;
    }
    .wmd-view-topscroll{
        height: 36px;
    }
    .dynamic-div{
        display: inline-block;
    }
    .exportGantt{
        overflow-x: visible;
        position: absolute;
        top: 100px;
        left: 0;
        background: #fff;
        z-index: 99999;
    }
    .pc-name:hover{
        cursor: pointer;
    }
    .hidden-avatar{
        display: none;
    }
</style>
<?php if(!empty($link) && !empty($title)){
    $link = explode('/', $link);
}else{
    $link = array('projects','index');
    $title = 'Project List';
}
?>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout" style="padding-bottom: 0px; background: white !important">
        <div class="wd-main-content">
            <div class="wd-section" >
                <div class="wd-container-created-value">
                    <div class="wd-title">
                        <h2 class="wd-t1"><?php echo __('Organization chart') ?></h2>
                        <a href="<?php echo $html->url(array('controller' => $link[0], 'action' => $link[1])) ?>" class="btn btn-back"><span>&nbsp;</span></a>
                        <a href="#" onclick="SubmitDataExport();return false;" class="export-excel-icon-all" title="<?php __('Export Excel')?>"><span><?php echo __('Export organization chart') ?></span></a>
                    </div>
                    <div id="overlay-container" style="display: none; color:white">
                        <div id="overlay-wrapper"></div>
                        <div id="overlay-box" >
                            Please wait, Preparing export ...
                        </div>
                    </div>
                    <?php if ($is_sas == 1) { ?>
                        <div style="float: left; color: white">
                            <form accept-charset="utf-8" action="<?php echo $html->url(array('controller' => 'profit_centers', 'action' => 'organization')) ?>" method="get"><?php echo __("Choose company:") ?>
                                <?php
                                echo $this->Form->input('company_id', array('name' => 'limit',
                                    'type' => 'select',
                                    'div' => false,
                                    'label' => false,
                                    'selected' => $limit,
                                    'style' => "border:1px solid #ccc;padding:3px;",
                                    'onchange' => "this.form.submit();",
                                    "options" => $companies));
                                ?>
                            </form>
                        </div>
                    <?php } ?>
                    <div>
                        <ul id="org" style="display: none">
                            <li><?php echo $company_name ?>
                                <ul>
                                    <?php
                                    echo $this->Organization->getOrganization($tree, $companyConfigs);
                                    ?>
                                </ul>
                            </li>
                        </ul>

                    </div>
                </div>

            </div>
            <div style="margin-top: 40px;"></div>
            <div class="wmd-view-topscroll" style="width: 100%; overflow-x: auto;">
                <div class="scroll-div">
                    &nbsp;
                </div>
            </div>
            <div class="wmd-view">
                <div class="dynamic-div" id="chart-wrapper">
                    <div id="chart" class="orgChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Form->create('Export', array('url' => array('controller' => 'profit_centers', 'action' => 'export_organization'), 'type' => 'file'));
echo $this->Form->hidden('canvas', array('id' => 'canvasData'));
echo $this->Form->hidden('height', array('id' => 'canvasHeight'));
echo $this->Form->hidden('width', array('id' => 'canvasWidth'));
//echo $this->Form->hidden('rows', array('value' => //$rows));
echo $this->Form->end();
?>
<script typ="text/javascript">
    jQuery(document).ready(function() {
        $("#org").jOrgChart({
            chartElement : '#chart',
            dragAndDrop  : false
        });
<?php if (empty($company_name)) { ?>
            $("span#company_name").html($("#company_id option:selected").text()+"<?php echo __(" company") ?>");
<?php } ?>

    });

    function SubmitDataExport(){
        $(".wmd-view-topscroll").scrollLeft(0);
        $('.wmd-view-topscroll').removeAttr('style');
        $('.wmd-view-topscroll').css('width', '100%');
        $('.jOrgChart').html2canvas();
    }

    $(function () {

        $(".wmd-view-topscroll").scroll(function () {
            $(".wmd-view")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
        });

        $(".wmd-view").scroll(function () {
            $(".wmd-view-topscroll")
            .scrollLeft($(".wmd-view").scrollLeft());
        });

    });

    $(window).load(function () {
        $('.scroll-div').css('width', $('.dynamic-div').outerWidth() );
    });
    function myFunction(_this) {
        var _this = $(_this);
        var div = _this.parent();
        var img = div.find('.employee-avatar');
        $.each(img, function(key, _img){
            var _img = $(_img);
            if( _img.hasClass('hidden-avatar') ){
                _img.removeClass('hidden-avatar');
            } else {
                _img.addClass('hidden-avatar');
            }
        });
}
</script>
<div id="overlay-container">
    <div id="overlay-wrapper"></div>
    <div id="overlay-box">
        Please wait, Preparing export ...
    </div>
</div>
