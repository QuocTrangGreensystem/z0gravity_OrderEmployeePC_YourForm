<style>
.btnSave {
    background: url(<?php echo $this->Html->url('/img/validation.png') ?>) no-repeat;
    display: inline-block;
    width: 32px;
    height: 32px;
}
#td-comment {
    padding: 0;
}
#td-comment textarea {
    margin: 0;
    border: 0;
    background: #fff;
    width: 99%;
    height: 100px;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <?php if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1) echo $this->element("secondary_menu_preview"); ?>

            <div class="wd-tab"> <div class="wd-panel">
            <div class="wd-list-project" style="overflow: auto">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo !empty($projectName) ? $projectName['Project']['project_name'] : '' ?></h2>
                </div>
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                $tpl = '<tr class="row_data" rel="%id%" id=row_%id%><td class="colValue name"><input type="text" class="editorRowPlan" onblur="changePartnerName(this.value,%id%,this);" id="name_%id%" value="%partner%"  /></td><td class="colValue"><input type="text" onkeypress="return validateKeypress(event,this.value,1);" onblur="changePartnerPercent(this.value,%id%,this);" class="editorRowPlan editorRowPlanPercent" id="percent_%id%" value="%percent%"  />%</td><td class="colValue"><span>'. $bg_currency .'</span><span class="amount" id="amount_%id%">%amount%</span></td><td class="colAction"><a href="javascript:;" class="btnDel" onclick="deletePartner(%id%);"></td></tr>';
                if((!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite)){
                    $disabled = '';
                } else {
                    $disabled = 'disabled';
                }
                ?>
                <div class="wd-tab1">
                    <div class="wd-panel">
                        <div class="wd-section">
                            <div class="floatLeft">
                            <table class="tableViN">
                                <tr>
                                    <td class="colTitle" ><?php echo __("Budget Total", true); ?></td>
                                    <td class="colValue"><input type="text" <?php echo $disabled ?> class="editorRow" id="finance_total_budget" value="<?php echo $data['finance_total_budget'] ?>"  /><?php echo ' '.$bg_currency; ?></td>
                                </tr>
                                <tr>
                                    <td class="colText"><?php echo __("BP Investment City", true); ?></td>
                                    <td class="colValue"><input type="text" <?php echo $disabled ?> class="editorRow" id="bp_investment_city" value="<?php echo $data['bp_investment_city'] ?>"  /><?php echo ' '.$bg_currency; ?></td>
                                    <td class="colText"><?php echo __("Available Investment", true); ?></td>
                                    <td class="colValue"><input type="text" <?php echo $disabled ?> class="editorRow" id="available_investment" value="<?php echo $data['available_investment'] ?>"  /><?php echo ' '.$bg_currency; ?></td>
                                </tr>
                                <tr>
                                    <td class="colText"><?php echo __("BP Operation City", true); ?></td>
                                    <td class="colValue"><input type="text" <?php echo $disabled ?> class="editorRow" id="bp_operation_city" value="<?php echo $data['bp_operation_city'] ?>"  /><?php echo ' '.$bg_currency; ?></td>
                                    <td class="colText"><?php echo __("Available Operation", true); ?></td>
                                    <td class="colValue"><input type="text" <?php echo $disabled ?> class="editorRow" id="available_operation" value="<?php echo $data['available_operation'] ?>"  /><?php echo ' '.$bg_currency; ?></td>

                                </tr>
                            </table>
                            </div>
                            <div class="floatRight">
                            <table class="tableViN" id="finance_plan_table">
                                <tr>
                                    <td class="colTitle" width="450"><?php echo __("Financing Plan", true); ?></td>
                                    <td class="colValue"><input type="text" readonly="readonly" <?php echo $disabled ?> class="editorRow" id="finance_plan" value="<?php echo formatNumber($data['finance_plan']) ?>"  /><?php echo ' '.$bg_currency; ?></td>
                                    <?php if((!empty($canModified) && !$_isProfile )|| ($_isProfile && $_canWrite)){ ?>
                                    <td style="border:none !important"><a href="javascript:;" class="btnAdd" onclick="addRow();"></a><span class="ViNTmp"></span> </td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="colTitle"><?php echo __("Partner", true); ?></td>
                                    <td class="colTitle val"><?php echo __("Percent", true); ?></td>
                                    <td class="colTitle val"><?php echo __("Amount", true); ?></td>
                                    <td class="colTitle colAction"><?php echo __("Action", true); ?></td>
                                </tr>
                                <?php
                                if(!empty($dataPartner))
                                {
                                    foreach($dataPartner as $val)
                                    {
                                        $_val = $val['ProjectFinancePartner'];
                                        $amount = round(($_val['finance_percent'] * $data['finance_plan'])/100,2);
                                        $amount = formatNumber($amount);
                                        $tmp = str_replace('%id%',$_val['id'],$tpl);
                                        $tmp = str_replace('%partner%',$_val['finance_partner'],$tmp);
                                        $tmp = str_replace('%percent%',$_val['finance_percent'],$tmp);
                                        $tmp = str_replace('%amount%',$amount,$tmp);
                                        echo $tmp;
                                    }
                                }
                                ?>
                            </table>
                            </div>
                            <div class="floatRight">
                            <?php echo $this->Form->create('Finance') ?>
                            <table class="tableViN" id="finance_plan_table">
                                <tr>
                                    <td class="colTitle" width="450"><?php echo __("Comment", true); ?></td>
                                    <?php if((!empty($canModified) && !$_isProfile ) || ($_isProfile && $_canWrite)){ ?>
                                    <td style="border:none !important"><a href="javascript:;" class="btn btn-save" onclick="saveComment();"></a> <span class="f-status"></span></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <?php if((!empty($canModified) && !$_isProfile )  || ($_isProfile && $_canWrite)){ ?>
                                    <td colspan="2" id="td-comment"><?php echo $this->Form->textarea('comment', array('value' => $data['comment'])) ?></td>
                                    <?php } else { ?>
                                        <td colspan="2" id="td-comment"><?php echo $this->Form->textarea('comment', array('disabled' => true, 'value' => $data['comment'])) ?></td>
                                    <?php } ?>
                                </tr>
                            </table>
                            <?php echo $this->Form->end() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             </div></div>
        </div>
    </div>
</div>
<div class="tmp"></div>
<div class="loadingDiv"></div>
<?php
function formatNumber(&$number)
{
    if(isset($number)&&is_numeric($number))
    {
        $number=number_format($number,2, '.', '');
    }
    else
    {
        $number=number_format(0.00,2, '.', '');
    }
    return $number;
}
?>
<script>
var f = function(v){
    return number_format(v, 2, '.', ' ');
}
String.prototype.s = function(){
    return parseFloat(this.replace(/\s+/g, ''));
}
var project = <?php echo $project_id ?> ;
var finance = <?php echo $finance_id ?> ;
$('.editorRow').bind('keypress', function(e) {
    var currentVal = this.value;
    return validateKeypress(e,currentVal,0);
});
function saveComment(){
    $('.f-status').text('Saving...');
    $.ajax({
        url: '/project_finances/update/'+project,
        type: 'POST',
        data: {updateMe: 1, field: 'comment', value: $('#FinanceComment').val()},
    }).always(function(){
        $('.f-status').text('OK');
    });
}
$('.editorRow').change( function(){
    var field = this.id;
    var value = this.value;
    value = parseFloat(value);
    $('#'+field).val(formatNumber(value));
    $('#'+field).addClass('acti');
    if(field == 'finance_plan')
    {
        $('.amount').addClass('acti');
    }
    $('.loadingDiv').show();
    var data = 'updateMe=1&field='+field+'&value='+value;
    $.ajax({
        url: '/project_finances/update/'+project,
        data: data,
        type:'POST',
        success:function(datas) {

            if(datas == 1)
            {
                $('#'+field).addClass('ok');
                if(field != 'available_investment' && field != 'available_operation')
                {
                    var $total = $('#finance_total_budget').val().s();
                    var $bp_invest = $('#bp_investment_city').val().s();
                    var $bp_operation = $('#bp_operation_city').val().s();
                    var $finance_plan = $total - $bp_invest - $bp_operation;
                    $('#finance_plan').val( f($finance_plan) );
                    $('#finance_plan').addClass('ok');
                    $('.row_data').each(function(index, element) {
                        var $id = $(this).attr('rel');
                        $('#amount_'+$id).addClass('acti');
                        var $percent = $('#percent_'+$id).val().s();
                        var amount = f(($percent*$finance_plan)/100);
                        $('#amount_'+$id).text( amount );
                        $('#amount_'+$id).addClass('ok');
                        //setTimeout(function(){
                            $('#amount_'+$id).removeClass('acti');
                        //},1000);
                    });
                }
            }
            else
            {
                $('#'+field).addClass('ko');
            }
            setTimeout(function(){
                $('.loadingDiv').hide();
                $('#'+field).removeClass('acti');
            },1000);
        }
    });
})
.blur(function(){
    value = $(this).val();
    $(this).val(f(value));
})
.focus(function(){
    value = $(this).val();
    $(this).val(value.replace(/\s+/g, ''));
})
.each(function(){
    value = $(this).val();
    $(this).val(f(value));
});

$('.amount').each(function(){
    value = $(this).text();
    $(this).text(f(value));
});

function validateKeypress(e,currentVal,check)
{
    if (window.event)//lấy giá ASCII kí tự mới nhập vào với trình duyệt IE
    {
        var value = window.event.keyCode;
    }
    else
        var value=e.which;
    if(value != 8 && value != 46)
    {
        if(value < 48 || value > 57)
        {
            return false;
        }
        /*else
        {
            if(check == 1)
            {

            }
        }*/
    }
    else if(value == 46)
    {
        if(currentVal.indexOf(".") != -1)
        {
            return false;
        }
    }
}
function addRow()
{
    $('.ViNTmp').addClass('acti');
    var data = 'add=1' ;
    var tmp = '<?php echo $tpl ?>';
    $.ajax({
        url: '/project_finances/add_partner/'+finance,
        data: data,
        type:'POST',
        success:function(datas) {
            var partner_id = datas ;
            tmp = tmp.replace(/%id%/g, partner_id);
            tmp = tmp.replace(/%partner%/g, '');
            tmp = tmp.replace(/%percent%/g, '0.00');
            tmp = tmp.replace(/%amount%/g, '0.00');
            $('#finance_plan_table').append(tmp);
            $('#name_'+partner_id).focus();
            $('.btnAdd').hide();
            //setTimeout(function(){
                $('.ViNTmp').removeClass('acti');
            //},1000);
        }
    });


}
function deletePartner(id)
{
    $('.ViNTmp').addClass('acti');
    var data = 'del=1' ;
    $.ajax({
        url: '/project_finances/delete_partner/'+id,
        data: data,
        type:'POST',
        success:function(datas) {
            //console.log(datas);
            $('#row_'+id).remove();
            //setTimeout(function(){
                $('.ViNTmp').removeClass('acti');
            //},1000);
        }
    });
}
function changePartnerName(name,id,$this)
{
    if($this.value == $this.defaultValue)
    {
        if(name == '')
        {
            $('#name_'+id).focus();
            $('.btnAdd').hide();
            return false;
        }
        return false;
    }

    if(name == '')
    {
        $('#name_'+id).focus();
        $('.btnAdd').hide();
        return false;
    }
    $('#name_'+id).addClass('acti');
    $('.loadingDiv').show();
    var data = 'updateMe=1&field=finance_partner&value='+name;
    $.ajax({
        url: '/project_finances/update_partner/'+id,
        data: data,
        type:'POST',
        success:function(datas) {
            $this.defaultValue = name ;
            $('.btnAdd').show();
            $('#name_'+id).addClass('ok');
            //setTimeout(function(){
                $('#name_'+id).removeClass('acti');
                $('.loadingDiv').hide();
            //},1000);
        }
    });
}
function changePartnerPercent(value,id,$this)
{
    if($this.value == $this.defaultValue)
    {
        return false;
    }
    var finance_plan = $('#finance_plan').val().s();
    value = parseFloat(value);
    if( isNaN(value) ){
        $this.value = $this.defaultValue;
        return false;
    }
    var $sum = value;
    $id = 'percent_'+id;
    $('.editorRowPlanPercent').each(function(index, element) {

        if($id != this.id)
        {
            $sum = $sum + parseFloat(this.value);
        }
    });
    if($sum > 100)
    {
        $('#percent_'+id).focus();
        $('#percent_'+id).addClass('ko');
        return false;
    }
    $('#percent_'+id).removeClass('ko');
    $('#percent_'+id).val(f(value));
    $('#percent_'+id).addClass('acti');
    $('#amount_'+id).addClass('acti');
    var data = 'updateMe=1&field=finance_percent&value='+value;
    $('.loadingDiv').show();
    $.ajax({
        url: '/project_finances/update_partner/'+id,
        data: data,
        type:'POST',
        success:function(datas) {
            if(datas == 1)
            {
                $this.defaultValue = formatNumber(value);
                $('#percent_'+id).addClass('ok');
                $('#amount_'+id).addClass('ok');
                var amount = f((finance_plan*value)/100);
                $('#amount_'+id).text(amount);
            }
            else
            {
                $('#percent_'+id).addClass('ko');
                $('#amount_'+id).addClass('ko');
            }
            //setTimeout(function(){
                $('#percent_'+id).removeClass('acti');
                $('#amount_'+id).removeClass('acti');
                $('.loadingDiv').hide();
            //},1000);

        }
    });
}
function formatNumber(number)
{
    number = parseFloat(number);
    return number.toFixed(2);
}
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '';
    toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
