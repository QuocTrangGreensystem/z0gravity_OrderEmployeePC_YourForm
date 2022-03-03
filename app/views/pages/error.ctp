<style>
    #wd-error-message {
        background: white;
        border: 1px solid #8195D6;
        border-radius: 3px 3px 3px 3px;
        margin: 20px auto;
        padding: 8px 15px 8px 45px;
        position: absolute;
        width: 500px;
        height: 350px;
        top: 20%;
        left: 28%;
        z-index: 1000;
    }
    #wd-error-message h1{
        color: rgb(223, 37, 74);
        font-size: 20px;
        text-align: center;
        padding-top: 25%;
    }
    #layout {
        background: none !important;
    }
    .wd-link-ad{
        text-align: center;
        padding-top: 10px;
    }
    .wd-link-ad a{
        color: blue;
    }
</style>
<div id="wd-error-message">
    <?php
        $employee_info = $this->Session->read('Auth.employee_info');
        if($employee_info['Role']['name'] == 'admin' || $employee_info['Employee']['is_sas'] == 1){
    ?>
        <h1><?php __("Your licence has expired. <br />To continue to use this app please renew it!") ?></h1>
        <p class="wd-link-ad"><a href="<?php echo $html->url('/liscenses/') ?>"><?php __("Go to License Management!") ?></a></p>
    <?php } else {?>
        <h1><?php __("The license has expired. <br />Please contact administrator for further information!") ?></h1>
    <?php }?>
</div>
<div id="wd-container-main" class="wd-index">
    <div class="wd-layout">
        <div id="wd-nav">
            <ul class="first-menu">
                <li><a href="#"><span class="wd-image wd-project">Projects</span><span class="wd-name">Projects</span></a></li>
                <li><a href="#"><span class="wd-image wd-employees">Employees</span><span class="wd-name">Employees</span></a></li>
                <li><a href="#"><span class="wd-image wd-document">Personalized</span><span class="wd-name">Personalized</span></a></li>
                <li><a href="#"><span class="wd-image wd-admin">Administration</span><span class="wd-name">Administration</span></a></li>
            </ul>
            <ul class="last-menu">
                <li><a href="#"><span class="wd-image wd-activity">Activity</span><span class="wd-name">Activity</span></a></li>
                <li><a href="#"><span class="wd-image wd-absence">Absence</span><span class="wd-name">Absence</span></a></li>
            </ul>
        </div>
    </div>
</div>
