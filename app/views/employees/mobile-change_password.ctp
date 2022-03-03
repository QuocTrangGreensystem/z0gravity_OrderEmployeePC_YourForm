<?php echo $html->css('green/tooltip');?>

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
<?php
$query = $this->params['url']['url'] . Router::queryString(array_diff_key(
                        $this->params['url'], array('url' => '', 'ext' => '')), array());
?>
<?php
echo $this->Form->create('Employee', array(
'url' => $this->Html->url('/password-change?token=' . $this->params['url']['token']),
'id' => 'EmployeeLoginForm'));
?>
<div class="row">
    <div class="absolute-center">
        <div class="box box-login effect7">
            <div class="box-header text-center">
                <h4 class="box-title"><?php echo $this->Html->image('front/logo_footer.png') ?></h4>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <?php echo $session->flash('resource'); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('password', array('type' => 'password', "class" => "form-control placeholder required minlength", 'minlength' => 4, "placeholder" => __("New Password", true), "div" => false, "label" => false)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('confirm_password', array('type' => 'password', "class" => "form-control placeholder", "placeholder" => __("Re Password", true), "div" => false, "label" => false)); ?>
                    <div class="strength_meter veryweak pconfirm"></div>
                </div>
                <div class="form-group text-right"> 
                    <a class="<?php echo ($langCode == 'en' ? 'selected' : ''); ?> lang" href="<?php echo $this->here . Router::queryString(array('hl' => 'en') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="English"><?php echo $html->image('/img/front/flag_united_kingdom.png') ?></a>
                    <a class="<?php echo ($langCode == 'fr' ? 'selected' : ''); ?> lang" href="<?php echo $this->here . Router::queryString(array('hl' => 'fr') + array_diff_key($this->params['url'], array('url' => '', 'ext' => '')), array()); ?>" title="French"><?php echo $html->image('/img/front/flag_france.png') ?></a>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <a href="<?php echo $this->Html->url('/login'); ?>" class="wd-input-link"><?php __("Login") ?></a>
                    </div>
                    <div class="col-xs-6">
                        <button type="submit" id="btnSubmit" class="btn btn-block btn-primary btn-login" onclick="javascript:return fsubmit();"><?php __("Change") ?></button>
                    </div>
                </div>
            </div>
            <?php
            echo $this->Form->hidden('first_name', array('value' => $employee['Employee']['first_name']));
            echo $this->Form->hidden('last_name', array('value' => $employee['Employee']['last_name']));
            ?>
        </div>
    </div>    
</div>
<?php echo $this->Form->end() ?>
<?php echo $html->script('jquery.cookie'); ?>
<?php echo $html->script('jquery.valid'); ?>
<?php echo $html->script('jquery.md5'); ?>
<?php echo $html->script('strength');?>
<?php echo $html->script('green/tooltip');?>
<script type="text/javascript">

    function fsubmit() {
        if ($("#EmployeeLoginForm").validate({
            errorClass: 'wd-error-message',
            errorElement: 'div'
        })){

            var valid = $('#EmployeePassword').data('plugin_strength').valid;
            if( !valid ){
                $('#EmployeePassword').focus();
                return false;
            }
            if($("#EmployeePassword").val() !== $("#EmployeeConfirmPassword").val()){
                $('.pconfirm').html('<?php __('The password does not match.'); ?>');
                $("#EmployeeConfirmPassword").focus();
                return false;
            }
            $('.pconfirm').html('');
            return true;
        }
        return false;
    }

<?php
if( $security['SecuritySetting']['complex_password'] ):
    $rules = array(
        sprintf(__('Minimum characters of %s', true), $security['SecuritySetting']['password_min_length'])
    );
    if( $security['SecuritySetting']['password_special_characters'] ){
        $messages = array_fill(0, 3, '<img src="'.$html->url('/img/test-fail-icon.png').'" alt="">');
        $messages[] = '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">';
        $rules[] = __('Uppercase letters (A-Z)', true);
        $rules[] = __('Lowercase letters (a-z)', true);
        $rules[] = __('Base 10 digits (0-9)', true);
        $rules[] = __('Non-alphanumeric characters (for example, !, $, #, %)', true);
        $valid = array(0, 0, 0, 1);
    } else {
        $messages = array_fill(0, 4, '<img src="'.$html->url('/img/test-pass-icon.png').'" alt="">');
        $valid = array(1, 1, 1, 1);
    }
?>
    //init complexify
    var caption = <?php echo json_encode(__('Password rules', true)) ?>;
    var el = $('#EmployeePassword').strength({
        minLength : <?php echo $security['SecuritySetting']['password_min_length'] ?>,
        text : <?php echo json_encode($messages) ?>,
        valid : <?php echo json_encode($valid) ?>,
        textPrefix : '',
        lengthError : '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>',
<?php
    if( $security['SecuritySetting']['password_ban_list'] ):
        $rule = h(__('Password should not contain the user\'s first or last name', true));
        $rules[] = $rule;
?>
        banListError : '<?php echo $rule ?>',
        banList : function(){
            var list = [],
                fn = $('#EmployeeFirstName').val(),
                ln = $('#EmployeeLastName').val();
            if( fn.length )list.push(fn);
            if( ln.length )list.push(ln);
            return list;
        },
<?php endif ?>
        validWhenEmpty : false,
        emptyError: '<?php printf(__('At least %s characters', true), $security['SecuritySetting']['password_min_length']) ?>'
    });

    el.tooltip({
        maxHeight : 500,
        maxWidth : 400,
        type : ['top','left'],
        content: '<div class="password-rule"><h4>' + caption + '</h4><ul><li>- ' + (<?php echo json_encode($rules) ?>).join('</li><li>- ') + '</li></ul></div>'
    });
<?php
endif;
?>


</script>