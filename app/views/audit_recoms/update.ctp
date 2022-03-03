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
    echo $validation->bind('AuditMission', array('form' => '#AuditRecomUpdateForm'));
?>
<div id="wd-container-main" class="wd-project-detail"> 
    <?php echo $this->element("project_top_menu"); ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-title">
                <fieldset style="float: left;">
                    <div class="wd-submit" style="overflow: hidden;margin: 0; width: 400px;">
                        <input onclick="if(validateForm()){jQuery('#wd-fragment-1 form:first').submit();};" class="wd-save"/>
                        <?php if(!empty($checkAction)){?>
                            <a href="<?php echo $this->Html->url(array('controller' => 'audit_recoms', 'action' => 'index_follow_employ'));?>" class="wd-back" style="float:left; margin-left: 10px; margin-top: 2px; "><span><?php echo __('Back To Recommendation') ?></span></a>
                        <?php } else {?>
                            <a href="<?php echo $this->Html->url(array('controller' => 'audit_recoms', 'action' => 'index', $company_id, $audit_mission_id));?>" class="wd-back" style="float:left; margin-left: 10px; margin-top: 2px; "><span><?php echo __('Back To Recommendation') ?></span></a>
                        <?php }?>
                        <!--a href="" class="wd-reset"><?php //__('Reset') ?></a-->
                    </div>   
                </fieldset>
            </div>
            <div class="wd-tab">
                <div class="wd-panel">
                    <div class="wd-section" id="wd-fragment-1">
                        <h2 class="wd-t2"><?php __("RECOMMENDATION") ?></h2>
                        <?php echo $this->Session->flash(); ?>
                        <?php
                        echo $this->Form->create('AuditRecom', array(
                            'enctype' => 'multipart/form-data',
                            'url' => array('controller' => 'audit_recoms', 'action' => 'update', $company_id, $audit_mission_id, $id, $checkAction),
                            'id' => 'AuditRecomUpdateForm'
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
                                        <label for="mission_title"><?php __("Mission Title") ?></label>
                                        <?php
                                        echo $this->Form->input('mission_title', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => 'disabled',
                                            'value' => !empty($auditMissions['mission_title']) ? $auditMissions['mission_title'] : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="mission_number"><?php __("Mission Number") ?></label>
                                        <?php
                                        echo $this->Form->input('mission_number', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => 'disabled',
                                            'value' => !empty($auditMissions['mission_number']) ? $auditMissions['mission_number'] : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_mission_status"><?php __("Mission Status") ?></label>
                                        <?php
                                        echo $this->Form->input('audit_setting_mission_status', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => 'disabled',
                                            'value' => !empty($auditSettings[1]) && !empty($auditSettings[1][$auditMissions['audit_setting_mission_status']]) ? $auditSettings[1][$auditMissions['audit_setting_mission_status']] : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <label for="auditor_company"><?php __("Auditor Company") ?></label>
                                        <?php
                                        echo $this->Form->input('audit_setting_auditor_company', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'disabled' => 'disabled',
                                            'value' => !empty($auditSettings[0]) && !empty($auditSettings[0][$auditMissions['audit_setting_auditor_company']]) ? $auditSettings[0][$auditMissions['audit_setting_auditor_company']] : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="mission_manager"><?php __("Mission Manager") ?></label>
                                        <?php
                                        $missionManagers = '';
                                        if(!empty($auditMissionEmployeeRefers[1])){
                                            foreach($auditMissionEmployeeRefers[1] as $employ => $backup){
                                                $missionManagers .= !empty($employees[$employ]) ? $employees[$employ] : '';
                                                if($backup == 1){
                                                    $missionManagers .= '(B), ';
                                                } else {
                                                    $missionManagers .= ', ';
                                                }
                                            }
                                            echo $this->Form->input('mission_manager', array('div' => false, 'label' => false,
                                                'maxlength' => 255,
                                                'disabled' => 'disabled',
                                                'value' => $missionManagers,
                                                "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        }
                                        ?>
                                    </div>
                                </div>
                                <hr style="clear: both;" />
                                <h4 style="color: #7EB7D3; font-size: 13px;"><?php __('Mission Manager Follow Up');?></h4>
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="id_recommendation" style="color: #F00808; width: 162px !important; padding-right: 0px; line-height: 16px;"><?php __("ID Recommendation") ?></label>
                                        <?php
                                        $disableRecom = '';
                                        $styleRecom = 'padding: 6px 2px; width: 62%;';
                                        if($disabledIDRecom == true){
                                            $disableRecom = 'disabled';
                                            $styleRecom = 'padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);';
                                        }
                                        echo $this->Form->input('id_recommendation', array('div' => false, 'label' => false,
                                            'maxlength' => 255,
                                            'type' => 'text',
                                            'disabled' => $disableRecom,
                                            "style" => $styleRecom));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_recom_priority" style="color: #F00808; line-height: 16px;"><?php __("Priority Recommendation") ?></label>
                                        <?php 
                                        $styleTextMission = 'padding: 6px 2px; width: 62%;';
                                        $styleSelectMission = $styleSelectMissionRecomManager = '';
                                        if($editMission == false){
                                            $styleTextMission = 'padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);';
                                            $styleSelectMission = 'background-color: rgb(218, 221, 226);';
                                            $styleSelectMissionRecomManager = 'background-color: rgb(218, 221, 226); background-image: none;';
                                        }
                                        echo $this->Form->input('audit_setting_recom_priority', array('div' => false, 'label' => false,
                                            'disabled' => ($editMission == false) ? 'disabled' : '', 
                                            'style' => $styleSelectMission,
                                            "options" => !empty($auditSettings[4]) ? $auditSettings[4] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="recom_theme" style="line-height: 16px;"><?php __("Theme Recommendation") ?></label>
                                        <?php
                                        echo $this->Form->input('recom_theme', array('div' => false, 'label' => false,
                                            'type' => 'text',
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleTextMission));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="comment_manager" style="line-height: 16px;"><?php __("Mission Manager Comments") ?></label>
                                        <?php
                                        echo $this->Form->input('comment_manager', array(
                                            'type' => 'textarea', 
                                            'class' => 'textHeightExpand',
                                            'div' => false, 'label' => false,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleTextMission . ' height:40px;'));
                                        //echo $this->Form->input('comment_manager', array('div' => false, 'label' => false,
//                                            'type' => 'text',
//                                            'disabled' => ($editMission == false) ? 'disabled' : '',
//                                            "style" => "padding: 6px 2px; width: 62%;"));
                                        ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="audit_setting_recom_status_mission" style="line-height: 16px; color: #F00808;"><?php __("Recommendation Status (Mission Manager)") ?></label>
                                        <?php 
                                            echo $this->Form->input('audit_setting_recom_status_mission', array('div' => false, 'label' => false, 
                                                'disabled' => ($editMission == false) ? 'disabled' : '',
                                                'style' => $styleSelectMission,
                                                "options" => !empty($auditSettings[3]) ? $auditSettings[3] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="date_change_status_mission" style="line-height: 16px;"><?php __("Date Change Status (Mission Manager)") ?></label>
                                        <?php
                                        echo $this->Form->input('date_change_status_mission', array('div' => false, 'label' => false,
                                            'disabled' => 'disabled',
                                            'type' => 'text',
                                            'value' => !empty($id) && !empty($this->data['AuditRecom']['date_change_status_mission']) ? date('d/m/Y', $this->data['AuditRecom']['date_change_status_mission']) : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input">
                                        <label for="contact"><?php __("Statement") // Statement = Contact ?></label>
                                        <?php
                                        echo $this->Form->input('contact', array('div' => false, 'label' => false,
                                            'type' => 'textarea',
											'class' => 'textHeightExpand',
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleTextMission . ' height:90px;'));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-area wd-none">
                                        <label style="color: #F00808;"><?php __("Recommendation") ?></label>
                                        <?php 
                                        echo $this->Form->input('recommendation', array(
                                            'type' => 'textarea', 
                                            'class' => 'textHeightExpand',
                                            'div' => false, 'label' => false,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleTextMission . ' height:40px;')); ?>
                                    </div>
                                    <div class="wd-input">
                                        <label for="recommendation_manager" style="color: #F00808; line-height: 16px;"><?php __("Recommendation Manager") ?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox" style="<?php echo $styleSelectMissionRecomManager;?>"></a>
                                            <div id="wd-data-project" class="recom_manager">
                                                <?php foreach($employees as $employId => $employName):?>
                                                <div class="recom_manager wd-data-manager wd-group-<?php echo $employId;?>">
                                                    <p class="recom_manager wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            echo $this->Form->input('audit_recom_manager', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'class' => 'recom_manager',
                                                                'name' => 'data[audit_recom_manager][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="recom_manager" style="padding-left: 5px;"><?php echo $employName;?></span>
                                                    </p>
                                                    <p class="recom_manager wd-backup" style="float: right; margin: -27px 0; padding-right: 5px;">
                                                        <?php
                                                            echo $this->Form->input('is_backup', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'type' => 'checkbox',
                                                                'disabled' => 'disabled',
                                                                'class' => 'recom_manager',
                                                                'name' => 'data[is_backup][]',
                                                                'value' => $employId));
                                                        ?>
                                                        <span class="recom_manager" style="padding-left: 5px;">Backup</span>
                                                    </p>
                                                </div>
                                                <?php endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wd-input wd-area wd-none">
                                        <label style="line-height: 16px;"><?php __("Initial Response Recommendation Manager") ?></label>
                                        <?php 
                                        echo $this->Form->input('response_recom_manager', array(
                                            'type' => 'textarea', 
                                            'class' => 'textHeightExpand',
                                            'div' => false, 'label' => false,
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            "style" => $styleTextMission . ' height:40px;')); ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="implement_date" style="color: #F00808; line-height: 16px;"><?php __("Initial Implementation Date") ?></label>
                                        <?php
                                        echo $this->Form->input('implement_date', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            'value' => !empty($id) && !empty($this->data['AuditRecom']['implement_date']) ? date('d/m/Y', $this->data['AuditRecom']['implement_date']) : '',
                                            "style" => $styleTextMission));
                                        ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="implement_revised" style="line-height: 16px;"><?php __("Date Of Implementation Revised") ?></label>
                                        <?php
                                        echo $this->Form->input('implement_revised', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => ($editMission == false) ? 'disabled' : '',
                                            'value' => !empty($id) && !empty($this->data['AuditRecom']['implement_revised']) ? date('d/m/Y', $this->data['AuditRecom']['implement_revised']) : '',
                                            "style" => $styleTextMission));
                                        ?>
                                    </div>
                                </div>
                                <hr style="clear: both;" />
                                <h4 style="color: #7EB7D3; font-size: 13px;"><?php __('Recommendation Manager Follow Up');?></h4>
                                <div class="wd-left-content">
                                    <div class="wd-input">
                                        <label for="audit_setting_recom_status_recom" style="line-height: 16px;"><?php __("Recommendation Status (Recommendation Manager)") ?></label>
                                        <?php 
                                            $styleTextRecom = 'padding: 6px 2px; width: 62.5%;';
                                            $styleSelectRecom = '';
                                            if($editRecom == false){
                                                $styleTextRecom = 'padding: 6px 2px; width: 62.5%; background-color: rgb(218, 221, 226);';
                                                $styleSelectRecom = 'background-color: rgb(218, 221, 226);';
                                            }
                                            echo $this->Form->input('audit_setting_recom_status_recom', array('div' => false, 'label' => false, 
                                                'disabled' => ($editRecom == false) ? 'disabled' : '',
                                                'style' => $styleSelectRecom,
                                                "options" => !empty($auditSettings[5]) ? $auditSettings[5] : array(), 'empty' => __("--Select--", true))); ?>
                                    </div>
                                    <div class="wd-input wd-calendar">
                                        <label for="date_change_status_recom" style="line-height: 16px;"><?php __("Date Change Status (Recommendation Manager)") ?></label>
                                        <?php
                                        echo $this->Form->input('date_change_status_recom', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => 'disabled',
                                            'value' => !empty($id) && !empty($this->data['AuditRecom']['date_change_status_recom']) ? date('d/m/Y', $this->data['AuditRecom']['date_change_status_recom']) : '',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);"));
                                        ?>
                                    </div>
                                </div>
                                <div class="wd-right-content">
                                    <div class="wd-input wd-calendar">
                                        <label for="author_modify" style="line-height: 16px;"><?php __("Operator Modification") ?></label>
                                        <?php
                                        echo $this->Form->input('author_modify', array('div' => false,
                                            'label' => false,
                                            'type' => 'text',
                                            'disabled' => 'disabled',
                                            "style" => "padding: 6px 2px; width: 62%; background-color: rgb(218, 221, 226);color:red;"));
                                        ?>
                                    </div>
                                    <!--div class="wd-input">
                                        <label for="author_modify" style="text-transform: none; line-height: 16px;"><?php //__("Operator Modification") // Operator Modification = Author Modification?></label>
                                        <div class="multiselect">
                                            <a href="" class="wd-combobox-2"></a>
                                            <div id="wd-data-project-2" class="author_modify">
                                                <?php //foreach($employees as $employId => $employName):?>
                                                <div class="author_modify wd-data-manager wd-group-<?php //echo $employId;?>">
                                                    <p class="author_modify wd-data" style="width: 200px; margin: 10px 5px;">
                                                        <?php
                                                            //echo $this->Form->input('author_modify', array(
//                                                                'label' => false,
//                                                                'div' => false,
//                                                                'type' => 'checkbox',
//                                                                'class' => 'author_modify',
//                                                                'name' => 'data[author_modify][]',
//                                                                'value' => $employId));
                                                        ?>
                                                        <span class="author_modify" style="padding-left: 5px;"><?php //echo $employName;?></span>
                                                    </p>
                                                </div>
                                                <?php //endforeach;?>
                                            </div>
                                        </div>
                                    </div-->
                                    <div class="wd-input">
                                        <label for="comment_recom" style="line-height: 16px;"><?php __("Recommendation Manager Comments") ?></label>
                                        <?php
                                        echo $this->Form->input('comment_recom', array(
                                            'type' => 'textarea', 
                                            'class' => 'textHeightExpand',
                                            'div' => false, 'label' => false,
                                            'disabled' => ($editRecom == false) ? 'disabled' : '',
                                            "style" => $styleTextRecom . ' height:40px;'));
                                        //echo $this->Form->input('comment_recom', array('div' => false,
//                                            'label' => false,
//                                            'type' => 'text',
//                                            'disabled' => ($editRecom == false) ? 'disabled' : '',
//                                            "style" => "padding: 6px 2px; width: 62%"));
                                        ?>
                                    </div>
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
    var auditRecomEmployeeRefers = <?php echo !empty($auditRecomEmployeeRefers[0]) ? json_encode($auditRecomEmployeeRefers[0]) : json_encode(array()); ?>,
    auditRecomEmployeeOperators = <?php echo !empty($auditRecomEmployeeRefers[1]) ? json_encode($auditRecomEmployeeRefers[1]) : json_encode(array()); ?>,
    idOfAudit = <?php echo json_encode($id); ?>,
    company_id = <?php echo json_encode($company_id); ?>,
    audit_mission_id = <?php echo json_encode($audit_mission_id); ?>,
    auditRecomFiles = <?php echo json_encode($auditRecomFiles); ?>,
    editMission = <?php echo json_encode($editMission); ?>,
    editRecom = <?php echo json_encode($editRecom); ?>,
    missionManager = $('#wd-data-project').find('.wd-data-manager'),
    dataMissionManager = $(missionManager).find('.wd-data'),
    backupMissionManager = $(missionManager).find('.wd-backup'),
    operatorModification = $('#wd-data-project-2').find('.wd-data-manager');
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
        if(editRecom == false){
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
        ($(e.target).attr('class').split(' ')[0] && ($(e.target).attr('class').split(' ')[0] == 'author_modify' || $(e.target).attr('class').split(' ')[0] == 'recom_manager')) ||
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
        var valList = $(data).find('#AuditRecomAuditRecomManager').val();
        var valListBackup = $(backup).find('#AuditRecomIsBackup').val();
        if(auditRecomEmployeeRefers){
            $.each(auditRecomEmployeeRefers, function(employId, isBackup){
                isBackup = (isBackup == 1) ? employId : 0;
                if(valList == employId){
                    $(data).find('#AuditRecomAuditRecomManager').attr('checked', 'checked');
                    $(backup).find('#AuditRecomIsBackup').removeAttr('disabled');
                    $('a.wd-combobox').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                }
                if(valListBackup == isBackup){
                    $(backup).find('#AuditRecomIsBackup').attr('checked', 'checked');
                    $('a.wd-combobox .wd-bk-'+valListBackup).append('(B)');
                }
                $ids.push(employId);
            });
        }
        /**
         * When click in checkbox
         */
        $(data).find('#AuditRecomAuditRecomManager').click(function(){
            var _datas = $(this).val();
            if($(this).is(':checked')){
                $(backup).find('#AuditRecomIsBackup').removeAttr('disabled');
                $ids.push(_datas);
                $('a.wd-combobox').append('<span class="wd-dt-'+_datas+'">' + $(data).find('span').html() + '<span class="wd-bk-'+_datas+'"></span></span><span class="wd-em-'+_datas+'">, </span>');
            } else {
                $ids = jQuery.removeFromArray(_datas, $ids);
                $(backup).find('#AuditRecomIsBackup').attr('disabled', 'disabled');
                $('a.wd-combobox').find('.wd-dt-' +_datas).remove();
                $('a.wd-combobox').find('.wd-em-' +_datas).remove();
                $(backup).find('#AuditRecomIsBackup').removeAttr('checked');
            }
            if($ids.length > 1){
                for(var i = 0; i < $ids.length; i++){
                    var _bkup = $(backup).find('#AuditRecomIsBackup').val();
                    if($ids[i] != $ids[0] && $ids[i] == _bkup){
                        $(backup).find('#AuditRecomIsBackup').attr('checked', 'checked');
                        $('a.wd-combobox .wd-bk-'+_bkup).append('(B)');
                    }
                }
            }
        });
        /**
         * When click in checkbox BACKUP
         */
        $(backup).find('#AuditRecomIsBackup').click(function(){
            var _bkup = $(backup).find('#AuditRecomIsBackup').val();
            if($(this).is(':checked')) {
                $('a.wd-combobox .wd-bk-'+_bkup).append('(B)'); 
            } else {
                $('a.wd-combobox').find('.wd-bk-' +_bkup).remove();
                $('a.wd-combobox .wd-dt-' +_bkup).append('<span class="wd-bk-'+_bkup+'"></span>');
            }
        });
    });
    operatorModification.each(function(){
        var data = $(this).find('.wd-data');
        var backup = $(this).find('.wd-backup');
        /**
         * When click in checkbox
         */
        $(data).find('#AuditRecomAuthorModify').click(function(){
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
        var valList = $(data).find('#AuditRecomAuthorModify').val();
        if(auditRecomEmployeeOperators){
            $.each(auditRecomEmployeeOperators, function(employId, isBackup){
                if(valList == employId){
                    $(data).find('#AuditRecomAuthorModify').attr('checked', 'checked');
                    $('a.wd-combobox-2').append('<span class="wd-dt-'+valList+'">' + $('.wd-group-' + valList).find('span').html() + '<span class="wd-bk-'+valList+'"></span></span><span class="wd-em-'+valList+'">, </span>');
                }
            });
        }
    });
    /**
     * DatePicker
     */
    $("#AuditRecomImplementDate, #AuditRecomImplementRevised").datepicker({
        //showOn          : 'button',
        buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
        buttonImageOnly : true,
        dateFormat      : 'dd/mm/yy'
    }); 
    /**
     * Validation Form Update Audit Mission
     */
    function validateForm(){
        var flag = true;
        $("#flashMessage").hide();
        $('div.error-message').remove();
        $("div.wd-input input, select").removeClass("form-error");	
        if($('#AuditRecomIdRecommendation').val() == ''){
            var element = $("#AuditRecomIdRecommendation");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The ID Recommendation is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#AuditRecomImplementDate').val() == ''){
            var element = $("#AuditRecomImplementDate");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Initial Implementation Date is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#AuditRecomRecommendation').val() == ''){
            var element = $("#AuditRecomRecommendation");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Recommendation is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#AuditRecomAuditSettingRecomPriority').val() == '' || $('#AuditRecomAuditSettingRecomPriority').val() == '--Select--'){
            var element = $("#AuditRecomAuditSettingRecomPriority");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Priority Recommendation is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('#AuditRecomAuditSettingRecomStatusMission').val() == '' || $('#AuditRecomAuditSettingRecomStatusMission').val() == '--Select--'){
            var element = $("#AuditRecomAuditSettingRecomStatusMission");
            element.addClass("form-error");
            var parentElem = element.parent();
            parentElem.addClass("error");
            parentElem.append('<div class="error-message">'+"<?php __("The Recommendation Status (Mission Manager) is not Blank!") ?>"+'</div>');
            flag = false;
        }
        if($('.wd-combobox').html() == ''){
            var element = $(".wd-combobox");
            element.addClass("form-error");
            var parentElem = element.parent();
            element.addClass("error");
            parentElem.append('<div class="error-message" style="padding-left: 0px !important; margin-left: -1px;">'+"<?php __("The Recommendation Manager is not Blank!") ?>"+'</div>');
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
    var checkAction = <?php echo !empty($checkAction) ? json_encode($checkAction) : json_encode('');?>;
    var uploader = $("#uploader").pluploadQueue({
        runtimes : 'html5, html4',
        url : "/audit_recoms/upload/"+company_id+"/"+idOfAudit+"/"+audit_mission_id+"/"+checkAction,
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
                up.linkedAction = '/audit_recoms/attachment/';
                up.auditMissionId = audit_mission_id;
                var hideLinkDelete = linkDownMargin = '';
                if(editRecom == false){
                    up.disableBrowse(true);
                    hideLinkDelete = 'style="display:none;"';
                    linkDownMargin = 'style="margin-right:25px;"';
                }
  		        if(auditRecomFiles && auditRecomFiles.length > 0){
  		            up.auditFiles = auditRecomFiles;
  		            var tmpHtml = '';
                    $.each(auditRecomFiles, function(ind, val){
                        var hrefDownload = '/audit_recoms/attachment/'+company_id+'/'+idOfAudit+'/'+val.id+'/download/'+audit_mission_id;
                        var hrefDelete = '/audit_recoms/attachment/'+company_id+'/'+idOfAudit+'/'+val.id+'/delete/'+audit_mission_id;
                        
                        tmpHtml += 
                        '<li id="' + val.id + '" class="plupload_done">' +
    						'<div class="plupload_file_name"><span>' + val.file_attachment + '</span></div>' +
                            '<div class="plupload_file_action_modify">' +
                            '<a class="download-attachment" ' +linkDownMargin+ ' href="' +hrefDownload+ '" rels=' + val.id + '>Download</a>' +
                            '<a class="delete-attachment" ' +hideLinkDelete+ ' href="' +hrefDelete+ '" rels=' + val.id + '>Delete</a></div>' +
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
    $('.textHeightExpand').focus(function(){
		changedSizeForTextField(this.id,'focus');
	});
	$('.textHeightExpand').blur(function(){
		changedSizeForTextField(this.id,'blur');
	});
	function changedSizeForTextField(id,type){
	   $elm=$('#'+id);
		if(type=='focus'){
			$elm.width($elm.width());
			$elm.addClass('heightExpand');
		} else{
			$elm.removeClass('heightExpand');
		}
	}
</script>