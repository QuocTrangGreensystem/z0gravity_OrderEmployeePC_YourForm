<?php 
    echo $html->script(array(
        'jquery.validation.min',
        'jquery.dataTables',
        'validateDate',
        'jquery.multiSelect',
        'jshashtable-2.1',
        'jquery.numberformatter-1.2.3',
        'jquery.maxlength-min',
        'multipleUpload/plupload.full.min',
        'multipleUpload/jquery.plupload.queue'
    )); 
    echo $html->css(array(
        'jquery.multiSelect',
        'jquery.dataTables',
        'multipleUpload/jquery.plupload.queue',
        'audit',
    ));
    echo $this->element('dialog_projects');
    echo $validation->bind('AuditMission', array('form' => '#AuditMissionUpdateForm'));
?>
<div id="wd-container-main" class="wd-project-detail"> 
    <?php echo $this->element("project_top_menu"); ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
                <fieldset style="float: left; width: 500px;">
                    <div class="wd-submit" style="overflow: hidden;margin: 0; width: 500px;">
                        <input onclick="if(validateForm()){jQuery('#wd-fragment-1 form:first').submit();};" class="wd-save"/>
                        <a href="<?php echo $this->Html->url(array('controller' => 'audit_missions', 'action' => 'index'));?>" class="wd-back" style="float:left; margin-left: 10px; margin-top: 2px; "><span><?php echo __('Back To Mission') ?></span></a>
                        <?php if(!empty($id)):?>
                        <a href="<?php echo $this->Html->url(array('controller' => 'audit_recoms', 'action' => 'index', $company_id, $id));?>" class="wd-next" style="float:left; margin-left: 10px; margin-top: 2px; "><span><?php echo __('Go To Recommendation') ?></span></a>
                        <?php endif;?>
                        <!--a href="" class="wd-reset"><?php //__('Reset') ?></a-->
                    </div>   
                </fieldset>
            </div>
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php __("Mission details") ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('AuditMission', array(
                            'enctype' => 'multipart/form-data',
                            'url' => array('controller' => 'audit_missions', 'action' => 'update', $company_id, $id),
                            'id' => 'AuditMissionUpdateForm'
                        ));
                        echo $this->Form->input('id');
                        App::import("vendor", "str_utility");
                        $str_utility = new str_utility();
                        echo $this->Form->input('tmp_activity', array('div' => false, 'label' => false, 'type' => 'hidden'));
                        ?>
                        <fieldset>
                            <div class="wd-scroll-form" style="height:auto;">
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="mission_title" style="color: #F00808;"><?php __("Mission Title") ?></label>
                                        <?php
                                        $styleText = 'padding: 6px 2px; width: 62%;';
                                        $styleTextArea = 'padding: 6px 2px; width: 81.5%';
                                        $styleSelectBox = $styleSelectMulti = '';
                                        if($editMission == false){
                                            $styleText = 'padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);';
                                            $styleTextArea = 'padding: 6px 2px; width: 81.5%; background-color: rgb(218, 221, 226);';
                                            $styleSelectBox = 'background-color: rgb(218, 221, 226);';
                                            $styleSelectMulti = 'background-color: rgb(218, 221, 226); background-image: none;';
                                        }
                                        echo $this->Form->input('mission_title', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleText));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="mission_number"><?php __("Mission Number") ?></label>
                                        <?php
                                        echo $this->Form->input('mission_number', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleText));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_mission_status"><?php __("Mission Status") ?></label>
                                        <?php 
                                        echo $this->Form->input('audit_setting_mission_status', array('div' => false, 'label' => false, 
                                            'style' => $styleSelectBox,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "options" => !empty($auditSettings[1]) ? $auditSettings[1] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_auditor_company"><?php __("Auditor Company") ?></label>
                                        <?php 
                                        echo $this->Form->input('audit_setting_auditor_company', array('div' => false, 'label' => false, 
                                            'style' => $styleSelectBox,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "options" => !empty($auditSettings[0]) ? $auditSettings[0] : array(), 'empty' => __("--Select--", true))); ?>  
                                    </div>
                                    <div class="wd-input">
                                        <label for="auditor"><?php __("Auditor") ?></label>
                                        <?php
                                        echo $this->Form->input('auditor', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleText));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audited_company"><?php __("Audited Company") ?></label>
                                        <?php
                                        echo $this->Form->input('audited_company', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleText));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <label for="mission_manager" style="color: #F00808;"><?php __("Mission Manager") ?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox" style="<?php echo $styleSelectMulti;?>"></a>
                                            <div id="wd-data-project" class="mission_manager">
                                                <?php foreach($employees as $employId => $employName):?>
                                                <div class="mission_manager wd-data-manager wd-group-<?php echo $employId;?>">
                                                    <p class="mission_manager wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('audit_mission_manager', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'mission_manager',
                                                                'name' => 'data[audit_mission_manager][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="mission_manager" style="padding-left: 5px;"><?php echo $employName;?></span>
                                                    </p>
                                                    <p class="mission_manager wd-backup" style="float: right; margin: -27px 0; padding-right: 5px;">
                                                        <?php
                                                            echo $this->Form->input('is_backup', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'disabled' => 'disabled',
                                                                'class' => 'mission_manager',
                                                                'name' => 'data[is_backup][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="mission_manager" style="padding-left: 5px;">Backup</span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_mission_type"><?php __("Mission Type") ?></label>
                                        <?php 
                                        echo $this->Form->input('audit_setting_mission_type', array('div' => false, 'label' => false, 
                                            'style' => $styleSelectBox,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "options" => !empty($auditSettings[2]) ? $auditSettings[2] : array(), 'empty' => __("--Select--", true))); ?>  
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="mission_validation_date"><?php __("Mission Validation Date") ?></label>
                                        <?php
                                        echo $this->Form->input('mission_validation_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            'value' => !empty($id) && !empty($this->data['AuditMission']['mission_validation_date']) ? date('d/m/Y', $this->data['AuditMission']['mission_validation_date']) : '',
                                            "style" => $styleText));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="mission_closing_date"><?php __("Mission Closing Date") ?></label>
                                        <?php
                                        echo $this->Form->input('mission_closing_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => 'disabled',
                                            'value' => !empty($id) && !empty($this->data['AuditMission']['mission_closing_date']) ? date('d/m/Y', $this->data['AuditMission']['mission_closing_date']) : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="readable_by"><?php __("Readable by") ?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox-2" style="<?php echo $styleSelectMulti;?>"></a>
                                            <div id="wd-data-project-2" class="readable_by">
                                                <?php foreach($employees as $employId => $employName):?>
                                                <div class="readable_by wd-data-manager wd-group-<?php echo $employId;?>">
                                                    <p class="readable_by wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('audit_readable_by', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'readable_by',
                                                                'name' => 'data[audit_readable_by][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="readable_by" style="padding-left: 5px;"><?php echo $employName;?></span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="updated" style="text-transform: none;"><?php __("Date Update") ?></label>
                                        <?php
                                        $lastUpdates = '';
                                        if(!empty($id) && !empty($this->data['AuditMission']['updated'])){
                                            $lastUpdates =  date('H:i:s A d/m/Y', $this->data['AuditMission']['updated']) . __(' by ', true);
                                            $lastUpdates .= !empty($this->data['AuditMission']['update_by_employee']) ? $this->data['AuditMission']['update_by_employee'] : '';
                                        }
                                        echo $this->Form->input('updated', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => 'disabled',
                                            'value' => $lastUpdates,
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226); color: red;"));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-input wd-area wd-none">
                                    <label><?php __("Comments") ?></label>
                                    <?php 
                                    echo $this->Form->input('comment', array('type' => 'textarea', 
                                        'div' => false, 'label' => false,
                                        'disabled' => ($editMission == false) ? 'disabled' : '',
                                        "style" => $styleTextArea)); ?>
                                </div>
                                <?php if(!empty($id) && !empty($this->data)):?>
                                <div class="wd-input wd-calendar" rels="<?php echo Configure::read('Config.language'); ?>" id="languageTranslationAudit">
                                    <label for="attachments"><?php __("Attachments") ?></label>
                                    <p style="padding-top: 5px;color: #F00808; font-size: 13px; font-style: italic;"><?php echo __('"Add files" and then "Start Upload"', true);?></p>
                                </div>
                                <div id="uploader" class="wd-input wd-calendar" style="margin-top: -10px;">
                                    <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                </div>
                                <?php endif;?>
                            </div>
                        </fieldset>
                        </form>
                    </div>					
                </div>
            </div>
        </div>
    </div>	
</div>	
<script>
    /**
     * Khai bao bien
     */
    var auditMissionEmployeeRefers = <?php echo json_encode($auditMissionEmployeeRefers);?>,
    managerHasSelects = <?php echo !empty($auditMissionEmployeeRefers[1]) ? json_encode(array_keys($auditMissionEmployeeRefers[1])) : json_encode(array());?>,
    company_id = <?php echo  json_encode($company_id); ?>,
    idOfAudit = <?php echo  json_encode($id); ?>,
    auditMissionFiles = <?php echo json_encode($auditMissionFiles); ?>,
    editMission = <?php echo json_encode($editMission); ?>,
    missionManager = $('#wd-data-project').find('.wd-data-manager'),
    dataMissionManager = $(missionManager).find('.wd-data'),
    backupMissionManager = $(missionManager).find('.wd-backup'),
    readableBy = $('#wd-data-project-2').find('.wd-data-manager');
    /**
     * Phan filter menu cua Mission Manager Va Readable By.
     * Ket hop voi phan mutiple select
     */
    var initMenuFilter = function($menu, $check){
        if($check === true){
            var $filter = $('<div class="context-menu-filter"><span><input type="text" rel="no-history"></span></div>');  
        } else {
            var $filter = $('<div class="context-menu-filter-2"><span><input type="text" rel="no-history"></span></div>');
        }
        $menu.before($filter);

        var timeoutID = null, searchHandler = function(){
            var val = $(this).val();
            var te = $($menu).find('.wd-data-manager .wd-data span').html();
            
            $($menu).find('.wd-data-manager .wd-data span').each(function(){
                var $label = $(this).html();
                $label = $label.toLowerCase();
                val = val.toLowerCase();
                if(!val.length || $label.indexOf(val) != -1 || !val){
                    $(this).parent().css('display', 'block');
                    $(this).parent().next().css('display', 'block');
                } else{
                    $(this).parent().css('display', 'none');
                    $(this).parent().next().css('display', 'none');
                }
            });
        };

        $filter.find('input').click(function(e){
            e.stopImmediatePropagation();
        }).keyup(function(){
            var self = this;
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function(){
                searchHandler.call(self);
            } , 200);
        });

    };
    initMenuFilter($('#wd-data-project'), true);
    initMenuFilter($('#wd-data-project-2'), false);
    $('.context-menu-filter, .context-menu-filter-2').css('display', 'none');
    $('.wd-combobox').click(function(){
        if(editMission == false){
            return false;
        }
        var checked = $(this).attr('checked');
        $('#wd-data-project-2').css('display', 'none');
        $('.context-menu-filter-2').css('display', 'none');
        $('.wd-combobox-2').removeAttr('checked');
        if(checked){
            $('#wd-data-project').css('display', 'none');
            $(this).removeAttr('checked');
            $('.context-menu-filter').css('display', 'none');
        } else {
            $('#wd-data-project').css('display', 'block');
            $(this).attr('checked', 'checked');
            $('.context-menu-filter').css({
                'display': 'block',
                'position': 'absolute',
                'width': '27.3%',
                'z-index': 2
            });
            $('#wd-data-project div:first-child').css('padding-top', '20px');
        }
        return false;
    });
    $('.wd-combobox-2').click(function(){
        if(editMission == false){
            return false;
        }
        var checked = $(this).attr('checked');
        $('#wd-data-project').css('display', 'none');
        $('.context-menu-filter').css('display', 'none');
        $('.wd-combobox').removeAttr('checked');
        if(checked){
            $('#wd-data-project-2').css('display', 'none');
            $(this).removeAttr('checked');
            $('.context-menu-filter-2').css('display', 'none');
        } else {
            $('#wd-data-project-2').css('display', 'block');
            $(this).attr('checked', 'checked');
            $('.context-menu-filter-2').css({
                'display': 'block',
                'position': 'absolute',
                'width': '27.3%',
                'z-index': 2
            });
            $('#wd-data-project-2 div:first-child').css('padding-top', '20px');
        }
        return false;
    });
    $('html').click(function(e){
        if($(e.target).attr('class') && (
        ($(e.target).attr('class').split(' ')[0] && ($(e.target).attr('class').split(' ')[0] == 'mission_manager' || $(e.target).attr('class').split(' ')[0] == 'readable_by')) ||
        $(e.target).attr('class') == 'context-menu-filter-2' ||
        $(e.target).attr('class') == 'context-menu-filter'
        )){ 
            //do nothing
        } else {
            $('.context-menu-filter, .context-menu-filter-2').css('display', 'none');
            $('#wd-data-project, #wd-data-project-2').css('display', 'none');
            $('.wd-combobox, .wd-combobox-2').removeAttr('checked');
        }
    });
    /**
     * Remove element to array
     */
    jQuery.removeFromArray = function(value, arr) {
        return jQuery.grep(arr, function(elem, index) {
            return elem !== value;
        });
    };
    /**
     * Phan chon cac phan tu trong combobox
     */
    var $ids = [];
    missionManager.each(function(){
        var data = $(this).find('.wd-data');
        var backup = $(this).find('.wd-backup');
        /**
         * When load data
         */
        var valList = $(data).find('#AuditMissionAuditMissionManager').val();
        var valListBackup = $(backup).find('#AuditMissionIsBackup').val();
        if(auditMissionEmployeeRefers[1]){
            $.each(auditMissionEmployeeRefers[1], function(employId, isBackup){
                isBackup = (isBackup == 1) ? employId : 0;
                if(valList == employId){
                    $(data).find('#AuditMissionAuditMissionManager').attr('checked', 'checked');
                    $(backup).find('#AuditMissionIsBackup').removeAttr('disabled');
                    $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                }
                if(valListBackup == isBackup){
                    $(backup).find('#AuditMissionIsBackup').attr('checked', 'checked');
                    $('a.wd-combobox .wd-bk-'+valListBackup).append('(B)');
                }
                $ids.push(employId);
            });
        }
        /**
         * When click in checkbox
         */
        $(data).find('#AuditMissionAuditMissionManager').click(function(){
            var _datas = $(this).val();
            if($(this).is(':checked')){
                $(backup).find('#AuditMissionIsBackup').removeAttr('disabled');
                $ids.push(_datas);
                $('#wd-data-project-2').find('.wd-group-'+_datas).css('display', 'none');
                $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
            } else {
                $ids = jQuery.removeFromArray(_datas, $ids);
                $('#wd-data-project-2').find('.wd-group-'+_datas).css('display', 'block');
                $(backup).find('#AuditMissionIsBackup').attr('disabled', 'disabled');
                $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
                $('a.wd-combobox').find('.wd-em-' +_datas).remove();
                $(backup).find('#AuditMissionIsBackup').removeAttr('checked');
            }
            if($ids.length > 1){
                for(var i = 0; i < $ids.length; i++){
                    var _bkup = $(backup).find('#AuditMissionIsBackup').val();
                    if($ids[i] != $ids[0] && $ids[i] == _bkup){
                        $(backup).find('#AuditMissionIsBackup').attr('checked', 'checked');
                        $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                    }
                }
            }
        });
        /**
         * When click in checkbox BACKUP
         */
        $(backup).find('#AuditMissionIsBackup').click(function(){
            var _bkup = $(backup).find('#AuditMissionIsBackup').val();
            if($(this).is(':checked')) {
                $('a.wd-combobox .wd-bk-'+_bkup).append('(B)'); 
            } else {
                $('a.wd-combobox').find('.wd-bk-' +_bkup).remove();
                $('a.wd-combobox .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
            }
        });
    });
    readableBy.each(function(){
        var data = $(this).find('.wd-data');
        var backup = $(this).find('.wd-backup');
        /**
         * When click in checkbox
         */
        $(data).find('#AuditMissionAuditReadableBy').click(function(){
            var _datas = $(this).val();
            if($(this).is(':checked')){
                $('a.wd-combobox-2').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
            } else {
                $('a.wd-combobox-2').find('.wd-dt-' +_datas).remove();
                $('a.wd-combobox-2').find('.wd-em-' +_datas).remove();
            }
        });
        /**
         * When load data
         */
        var valList = $(data).find('#AuditMissionAuditReadableBy').val();
        if(auditMissionEmployeeRefers[0]){
            $.each(auditMissionEmployeeRefers[0], function(employId, isBackup){
                if(valList == employId){
                    $(data).find('#AuditMissionAuditReadableBy').attr('checked', 'checked');
                    $('a.wd-combobox-2').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                }
            });
        }
        /**
         * Hide value have select in mission manager
         */
        if(GetObjectValueIndex(managerHasSelects, valList) != null){
            $('#wd-data-project-2').find('.wd-group-'+valList).css('display', 'none');
        }
    });
    /**
     * DatePicker
     */
    $("#AuditMissionMissionValidationDate").datepicker({
        //showOn          : 'button',
        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
        buttonImageOnly : true,
        dateFormat      : 'dd/mm/yy'
    }); 
    /**
     * Validation Form Update Audit Mission
     */
    function validateForm(){
        if(editMission == false){
            return false;
        }
        var flag = true;
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");
        if($('#AuditMissionMissionTitle').val() == ''){
            var element = $("#AuditMissionMissionTitle");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Mission Title is not Blank!") ?>"+'</div>');
            flag = false;
        }	
        if($('.wd-combobox').html() == ''){
            var element = $(".wd-combobox");
            element.addClass("form-error");
            var parentElem = element.parent();
            element.addClass("error");
            parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">'+"<?php __("The Mission Manager is not Blank!") ?>"+'</div>');
            flag = false;
        }
        return flag;
    }
    /**
     * Tim 1 phan tu trong mang
     */
    function GetObjectValueIndex(obj, keyToFind) {
        var i = 0, key;
        for (key in obj) {
            var val = obj[key] ? obj[key] : 0;
            if (val == keyToFind) {
                return i;
            }
            i++;
        }
        return null;
    };
    /**
     * Multiple Upload
     */
    
    var uploader = $("#uploader").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/audit_missions/upload/"+company_id+"/"+idOfAudit,
        chunk_size : '10mb',
        rename : true,
        dragdrop: true,
        filters : {
            max_file_size : '10mb',
            mime_types: [
                {title : "Files", extensions : "jpg,jpeg,bmp,gif,png,swf,txt,zip,rar,doc,xls,pdf,docx,xlsx,ppt,pps,pptx,csv,xlsm"}
            ]
        },
        init: {
    		PostInit: function(up) {                  		  
                up.idOfAudit = idOfAudit;
                up.company_id = company_id;
                up.linkedAction = '/audit_missions/attachment/';
                var hideLinkDelete = linkDownMargin = '';
                if(editMission == false){
                    up.disableBrowse(true);
                    hideLinkDelete = 'style="display:none;"';
                    linkDownMargin = 'style="margin-right:25px;"';
                }
  		        if(auditMissionFiles && auditMissionFiles.length > 0){
  		            up.auditFiles = auditMissionFiles;
  		            var tmpHtml = '';
                    $.each(auditMissionFiles, function(ind, val){
                        var hrefDownload = '/audit_missions/attachment/'+company_id+'/'+idOfAudit+'/'+val.id+'/download';
                        var hrefDelete = '/audit_missions/attachment/'+company_id+'/'+idOfAudit+'/'+val.id+'/delete';
                        tmpHtml += 
                        '<li id="' + val.id + '" class="plupload_done">' +
    						'<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" ' +linkDownMargin+ ' href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" ' +hideLinkDelete+ ' href="' +hrefDelete+ '" rels=' + val.id + '>Download</a></div>' +
    						'<div class="plupload_file_action"><a href="#" style="display: block;"></a></div>' +
    						'<div class="plupload_file_status">' + 100 + '%</div>' +
    						'<div class="plupload_file_size">' + plupload.formatSize(val.size) + '</div>' +
    						'<div class="plupload_clearer">&nbsp;</div>' +
    					'</li>';
                    });
                    $('#uploader_filelist').html(tmpHtml);
                }
    		}
    	}
    });
    uploader.init();
    /**
     * Download And Remove File Attachment
     */
    //uploader.on('click', 'div.plupload_file_action_modify a.download-attachment', function(e) {
//    });
    //if ($.browser.mozilla && $.browser.version >= "2.0" ){ // firefox
//        alert('Mozilla above 1.9');
//    }
//    if( $.browser.safari ){ //safari
//        alert('Safari');
//    }
//    if( $.browser.opera){ //opera
//        alert('Opera');
//    }
    //var Browser = navigator.userAgent;
//    if (Browser.indexOf('MSIE') >= 0){
//        Browser = 'MSIE';
//    } else if (Browser.indexOf('Firefox') >= 0){
//        Browser = 'Firefox';
//    } else if (Browser.indexOf('Chrome') >= 0){
//        Browser = 'Chrome';
//    }else if (Browser.indexOf('Safari') >= 0){
//        Browser = 'Safari';
//    }else if (Browser.indexOf('Opera') >= 0){
//      Browser = 'Opera';
//    }else{
//        Browser = 'UNKNOWN';
//    }
    //function browserVersion(){
//       var index;
//       var version = 0;
//       var name = browserName();
//       var info = navigator.userAgent;
//       index = info.indexOf(name) + name.length + 1;
//       version = parseFloat(info.substring(index,index + 3));
//       return version;
//    }
    /**
     * Tat chuc nang drag & drop tren version ie < 9 or document mode of ie <= 9
     */
    if (($.browser.msie && $.browser.version <= 9) || document.documentMode <= 9){
        $(document).bind({
           dragenter: function (e) {
              e.stopPropagation();
              e.preventDefault();
              var dt = e.originalEvent.dataTransfer;
             dt.effectAllowed = dt.dropEffect = 'none';
           },
           dragover: function (e) {
              e.stopPropagation();
              e.preventDefault();
              var dt = e.originalEvent.dataTransfer;
              dt.effectAllowed = dt.dropEffect = 'none';
           }
        });
    }
</script>