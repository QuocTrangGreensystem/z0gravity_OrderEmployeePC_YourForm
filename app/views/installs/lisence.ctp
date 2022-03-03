<style>
    .install{
        background: none repeat scroll 0 0 padding-box white;
        padding: 20px 20px 10px;
    }
    #wd-main{
        margin: 42px auto;
        max-width: 500px;
        box-shadow: 0 4px 18px #C8C8C8;
        border: 1px solid #C8C8C8;
        position:relative;
    }
    .install h2{
        color: black;
        font-size: 31.5px;
        line-height: 40px;
        font-family: inherit;
        font-weight: bold;
        line-height: 20px;
        margin: 30px 0;
        text-rendering: optimizelegibility;
    }
    .install p.success{
        background-color: #DFF0D8;
        border: 1px solid #D6E9C6;
        border-radius: 4px 4px 4px 4px;
        margin-bottom: 20px;
        padding: 8px 35px 8px 14px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        color: #468847;
    }
    .install p.error{
        background-color: #f2dede;
        border: 1px solid #fbeed5;
        border-radius: 4px 4px 4px 4px;
        margin-bottom: 20px;
        padding: 8px 35px 8px 14px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        color: #b94a48;
    }
    .form-actions{
        margin-bottom: 0;
        text-align: right;
        background-color: #F5F5F5;
        border-top: 1px solid #E5E5E5;
        padding: 19px 20px 20px;
    }
    .bt-success{
        background-color: #5BB75B;
        background-image: linear-gradient(to bottom, #62C462, #51A351);
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        border-radius: 4px 4px 4px 4px;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        line-height: 20px;
        padding: 4px 12px;
        border-style: solid;
        border-width: 1px;
        color: white;
        margin-left: 30%;
    }
    #gantt-display{
        color: black;
    }
    .radio label{display:block; padding-bottom: 10px;}
    .radio input{float:left}
    
    .wd-input{
        border: 1px solid black;
    }
    .wd-dt-input{
        display: block;
        padding-bottom: 12px;
    }
    .wd-dt-input label{
        min-width: 135px;
        display: inline-block;
    }
    .wd-dt-input label span{
        color: red
    }
    .wd-dt-input input{
        border: 1px solid rgb(209, 208, 206);
        width: 300px;
        padding: 7px;
    }
    .wd-error{
        padding-left: 140px;
        padding-top: 5px;
        color: red;
        font-size: 12px;
        font-style: italic;
    }
    .wd-submit-lisena{
        display: none;
    }
    .message{
        width: auto !important;
    }
    .wd-left{
        border: 1px solid #C8C8C8;
        width: 200px;
        position: absolute;
        left: -202px;
        top: -1px;
        border-right: none;
    }
    .wd-left ul li{
        color: black;
        padding:8px 0;
        text-indent:26px;
    }
    .wd-current-li{
        background:#dedede url(../img/test-pass-icon.png) no-repeat 5px center;
        color: rgb(14, 57, 190) !important;
    }
</style>
<div id="wd-main">
<?php if(empty($alias)){?>
    <div class="install">
    <div class="wd-left">
        <ul>
            <li><?php echo __('Installation: Welcome', true);?></li>
            <li><?php echo __('Step 1: Choose Language', true);?></li>
            <li><?php echo __('Step 2: Database', true);?></li>
            <li><?php echo __('Step 3: Build database', true);?></li>
            <li><?php echo __('Step 4: Create Admin User', true);?></li>
            <li class="wd-current-li"><?php echo __('Step 5: License', true);?></li>
            <li><?php echo __('Step 6: Mail Settings', true);?></li>
            <li style="border-bottom: none;"><?php echo __('Installation successful', true);?></li>
        </ul>
    </div>
   	<h2><?php echo $title_for_layout; ?></h2>
   	<?php
    echo $this->Form->create('Project', array('url' => array('controller' => 'installs', 'action' => 'lisence'), 'type' => 'file'));
    ?>
    <div id="gantt-display">
        <?php
        echo $this->Form->input('display', array(
            'options' => array(__('Trial', true), __('License Key', true)),
            'type' => 'radio', 'legend' => false, 'fieldset' => false, 'default' => 0
        ));
        ?>
        <hr />
        <div class="wd-group-1">
            <h1 style="font-size: 20px;">Trial</h1>
            <p style="margin-left: 30px;"> - Please contact the administrator to get key 30 day trial!</p>
        </div>
        <div class="wd-group-2" style="display: none;">
            <h1 style="font-size: 20px; padding-bottom: 10px;">License Key</h1>
            <div class="wd-dt-input">
                <label>Projects Module:</label>
                <?php
                    echo  $this->Form->input('pm', array('label' => false, 'div' => false, 'class' => 'wd-input', 'type' => 'file'));
                ?>
            </div>
            <div class="wd-dt-input">
                <label>Activity Module:</label>
                <?php
                    echo  $this->Form->input('am', array('label' => false, 'div' => false, 'class' => 'wd-input', 'type' => 'file'));
                ?>
            </div>
            <div class="wd-dt-input">
                <label>Absence Module:</label>
                <?php
                    echo  $this->Form->input('bm', array('label' => false, 'div' => false, 'class' => 'wd-input', 'type' => 'file'));
                ?>
            </div>
            <div class="wd-dt-input">
                <label>Audit Module:</label>
                <?php
                    echo  $this->Form->input('aum', array('label' => false, 'div' => false, 'class' => 'wd-input', 'type' => 'file'));
                ?>
            </div>
            <div class="wd-dt-input">
                <label>Budget Module:</label>
                <?php
                    echo  $this->Form->input('bdm', array('label' => false, 'div' => false, 'class' => 'wd-input', 'type' => 'file'));
                ?>
            </div>
            <input type="submit" value="<?php echo __('Submit', true);?>" class="bt-success" id="lisenForm"/>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    </div>
<?php
$out = $this->Html->link(__('Next', true), array(
	'action' => 'mail',
), array(
	'class' => 'bt-success',
	'tooltip' => array(
		'data-title' => __('Click here to begin installation', true),
		'data-placement' => 'left',
	),
));
echo $this->Html->div('form-actions', $out);
?>
<?php
    } else {
?>
    <div class="install">
       	<h2><?php echo $title_for_layout; ?></h2>
        <div class="install">
        <?php echo $this->Session->flash(); ?>
        </div>
    <?php
    $out = $this->Html->link(__('Next', true), array(
    	'action' => 'mail',
    ), array(
    	'class' => 'bt-success',
    	'tooltip' => array(
    		'data-title' => __('Click here to begin installation', true),
    		'data-placement' => 'left',
    	),
    ));
    echo $this->Html->div('form-actions', $out);
    ?>
    </div>
<?php }?>
</div>
<script>
    $('#lisenForm').addClass('wd-submit-lisena');
    $('#ProjectDisplay0').click(function(){
        if($('#ProjectDisplay0').is(':checked')){
            $('.wd-group-1').css('display', 'block');
            $('.wd-group-2').css('display', 'none');
            $('#lisenForm').addClass('wd-submit-lisena');
        }
    });
    $('#ProjectDisplay1').click(function(){
        if($('#ProjectDisplay1').is(':checked')){
            $('.wd-group-1').css('display', 'none');
            $('.wd-group-2').css('display', 'block');
            $('#lisenForm').removeClass('wd-submit-lisena');
        }
    });
</script>