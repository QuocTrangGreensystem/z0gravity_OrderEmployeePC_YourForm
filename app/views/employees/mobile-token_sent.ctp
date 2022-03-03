<style type="text/css">

.message {
    border-radius: 3px 3px 3px 3px;
    position: relative;
    background-color: #fffbcc;
    border-color: #ebe174;
}

.message .close {
    display: none;
}

fieldset div.wd-input div.wd-error-message {
    background-color: #114B80;
    clear: both;
    display: block;
    width: auto;
    color: red;
    text-align: left;
    padding: 0;
}
.strength_meter {
    margin: 5px 0 0 5px;
    display: inline-block;
}
.veryweak {
    color: #f00;
}
.weak {
    color: #f60;
}
.medium, .strong {
    color: #57a957;
}
.password-rule h4 {
    padding: 0 0 10px 0;
}
.password-rule ul {
    list-style: square inside !important;
}
</style>
<div class="row">
    <div class="absolute-center">
        <div class="box box-login effect7">
            <div class="box-header text-center">
                <h4 class="box-title"><?php echo $this->Html->image('front/logo_footer.png') ?></h4>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div id="flashMessage" class="message success">
                        <?php
                        echo '<strong>' . sprintf(__('An email has been sent to %s. Please follow the instructions to change your password.', true), $employee['email']) . '</strong>';
                        ?>
                    </div>
                </div>
                <div class="form-group text-right">
                    <a class="<?php echo ($langCode == 'en' ? 'selected' : ''); ?> lang" href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="English"><?php echo $html->image('/img/front/flag_united_kingdom.png') ?></a>
                    <a class="<?php echo ($langCode == 'fr' ? 'selected' : ''); ?> lang" href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="French"><?php echo $html->image('/img/front/flag_france.png') ?></a>
                </div>
                <div class="row">
                    <div class="col-xs-6 pull-right">
                        <a href="/login" tabindex="3" class="btn btn-block btn-primary btn-login"><?php __("Login") ?></a>
                    </div>
                </div>
            </fieldset>
            <?php //echo $this->Form->end() ?>
        </div>
    </div>
</div>
