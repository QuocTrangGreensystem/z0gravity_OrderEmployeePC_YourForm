<?php echo $this->Form->create('Employee', array('id' => 'EmployeeLoginForm')); ?>
<div class="page-login__panel">
    <div class="page-login__logo">
        <a href="<?php echo $html->url('/') ?>"><img src="img_z0g/logo-big.svg" alt="zero gravity"/></a>
        <?php
        echo $session->flash("auth");
        echo $session->flash();
        ?>
    </div>
    <div class="page-login__input">
        <img src="img_z0g/person.svg" alt="" role="presentation" />
        <?php echo $this->Form->input('email', array(
                'autocomplete' => 'off',
                'tabindex' => 1,
                "class" => "placeholder required email",
                "placeholder" => __("Email", true),
                "div" => false,
                "label" => false
            ))
        ?>
    </div>
    <button onclick="javascript:fsubmit();" class="page-login__login-btn btn btn--fancy"><?php __("Send") ?></button>
    <a href="<?php echo $this->Html->url('/login'); ?>" class="page-login__lost-password"><?php __("Login") ?></a>
    <ul class="page-login__lang">
    <?php
    $langCode = Configure::read('Config.langCode');
    ?>
        <li>
            <a href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn <?php echo ($langCode == 'fr' ? 'page-login__lang-btn--active' : ''); ?>">
                <span class="btn__icon-holder"><img class="btn__icon" alt="Fran�ais" src="img_z0g/fr.png"/></span>
            </a>
        </li>
        <li>
            <a href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn <?php echo ($langCode == 'en' ? 'page-login__lang-btn--active' : ''); ?>">
                <span class="btn__icon-holder"><img class="btn__icon" alt="Anglais" src="img_z0g/en.png"/></span>
            </a>
        </li>
        <li>
            <a href="<?php echo $this->here . Router::queryString(array('hl' => 'vi') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" class="btn page-login__lang-btn <?php echo ($langCode == 'en' ? 'page-login__lang-btn--active' : ''); ?>">
                <span class="btn__icon-holder"><img class="btn__icon" alt="Anglais" src="img_z0g/vi.png"/></span>
            </a>
        </li>
    </ul>
</div>
<?php echo $this->Form->end();?>
<?php echo $html->script('jquery-1.6.2.min'); ?>
<?php echo $html->script('jquery.cookie'); ?>
<?php echo $html->script('jquery.valid'); ?>
<?php echo $html->script('jquery.md5'); ?>
<script type="text/javascript">
    function fsubmit() {
        if ($("#EmployeeLoginForm").validate({
            errorClass: 'wd-error-message',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.insertAfter('div.page-login__input');
            }
        })){
            return true;
        }
        return false;
    }
</script>
