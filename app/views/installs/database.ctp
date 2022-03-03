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
    .wd-form-dt{
        color: black;
    }
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
    .wd-error-1, .wd-error-2, .wd-error-3{
        padding-left: 140px;
        padding-top: 5px;
        color: red;
        font-size: 12px;
        font-style: italic;
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
    .message{
        width: auto !important;
    }
</style>
<div id="wd-main">
    <div class="install">
        <div class="wd-left">
            <ul>
                <li><?php echo __('Installation: Welcome', true);?></li>
                <li><?php echo __('Step 1: Choose Language', true);?></li>
                <li class="wd-current-li"><?php echo __('Step 2: Database', true);?></li>
                <li><?php echo __('Step 3: Build database', true);?></li>
                <li><?php echo __('Step 4: Create Admin User', true);?></li>
                <li><?php echo __('Step 5: License', true);?></li>
                <li><?php echo __('Step 6: Mail Settings', true);?></li>
                <li style="border-bottom: none;"><?php echo __('Installation successful', true);?></li>
            </ul>
        </div>
       	<h2><?php echo $title_for_layout; ?></h2>
        <?php echo $this->Session->flash(); ?>
        <div class="wd-form-dt">
            <?php echo $this->Form->create('Database', array('url' => array('controller' => 'installs', 'action' => 'database')));?>
                <div class="wd-dt-input">
                    <label>Host Name<span>(*)</span>:</label>
                    <?php
                        echo  $this->Form->input('host', array('label' => false, 'div' => false, 'class' => 'wd-input'));
                    ?>
                    <p class="wd-error-1"></p>
                </div>
                <div class="wd-dt-input">
                    <label>Database Username<span>(*)</span>:</label>
                    <?php
                        echo  $this->Form->input('login', array('label' => false, 'div' => false, 'class' => 'wd-input'));
                    ?>
                    <p class="wd-error-2"></p>
                </div>
                <div class="wd-dt-input">
                    <label>Database Password:</label>
                    <?php
                        echo  $this->Form->input('password', array('type' => 'password', 'label' => false, 'div' => false, 'class' => 'wd-input'));
                    ?>
                </div>
                <div class="wd-dt-input">
                    <label>Database Name<span>(*)</span>:</label>
                    <?php
                        echo  $this->Form->input('database', array('label' => false, 'div' => false, 'class' => 'wd-input'));
                    ?>
                    <p class="wd-error-3"></p>
                </div>  
                <div class="wd-dt-input">
                    <label>Database Port:</label>
                    <?php
                        echo  $this->Form->input('port', array('label' => false, 'div' => false, 'class' => 'wd-input'));
                    ?>
                </div> 
                <input type="submit" value="<?php echo __('Submit', true);?>" class="bt-success"/>
                <?php echo $this->Form->end(); ?>           
        </div>
    </div>
    <div class="form-actions">
    </div>
</div>
<script>
    $('#DatabaseDatabaseForm').submit(function(){
        var host = $('#DatabaseHost').val();
        var login = $('#DatabaseLogin').val();
        var data = $('#DatabaseDatabase').val();
        if(host == ''){
            $('.wd-error-1').html('The input is not blank!');
            return false;
        }
        if(login == ''){
            $('.wd-error-2').html('The input is not blank!');
            return false;
        }
        if(data == ''){
            $('.wd-error-3').html('The input is not blank!');
            return false;
        }
    });
</script>