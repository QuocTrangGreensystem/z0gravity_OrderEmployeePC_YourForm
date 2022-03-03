<style type="text/css">.error-info li{margin-left:20px;list-style:decimal;}.buttons ul.type_buttons{padding-right:2px!important;}table tbody tr:hover td{}center{color:red;font-weight:700;}.ui-dialog{font-size:11px;}#dialog_import_CSV label{color:#000;}.type_buttons .error-message{background-color:#FFF;clear:both;color:#D52424;display:block;width:212px;padding:5px 0 0;}.form-error{border:1px solid #D52424;color:#D52424;}.import-fieldset{border:1px solid #D4D0C8;margin-bottom:20px;padding:5px;}.import-legend{font-size:14px;font-weight:700;padding:0 4px;}.table-info{min-width:200px;}.table-col{min-width:150px;white-space:nowrap;overflow:hidden;}.display{min-width:1165px;}.dataImport{overflow:auto;width:99%;max-height:300px;}.error,.error td,.error1{background:#FF6F6F;}
#wd-container-footer{margin: 0px !important;}#wd-container-main{padding-bottom: 0px !important;}
.buttons ul.type_buttons {
    height: 33px;
}
.buttons ul.type_buttons li{
    float: right;
    margin-left: 0px;
    margin-top: 3px;
}
.buttons ul.type_buttons li a {
    text-indent: -9999px;
    height: 33px;
}
.buttons ul.type_buttons li a.export-excel-icon-all{
    margin-top: -5px;
}
.wd-header th {
    vertical-align: middle;
}
.milestone td {
    background: #f0fff1;
}
.fields {
    padding: 10px 0;
}
.fields select, .fields input {
    padding: 5px;
}
#submit-task {
    background: url(/img/validation.png) no-repeat !important;
    width: 32px;
    height: 32px;
    padding: 0 !important;
    text-indent: -9999px;
    display: block;
    float: right;
    margin-left: 10px;
}

.no-scroll {
    overflow: visible;
    height: auto;
    max-height: none;
}
.btn-white {
    background: rgb(238,238,238);
    background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2VlZWVlZSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNjY2NjY2MiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
    background: -moz-linear-gradient(top, rgba(238,238,238,1) 0%, rgba(204,204,204,1) 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(238,238,238,1)), color-stop(100%,rgba(204,204,204,1)));
    background: -webkit-linear-gradient(top, rgba(238,238,238,1) 0%,rgba(204,204,204,1) 100%);
    background: -o-linear-gradient(top, rgba(238,238,238,1) 0%,rgba(204,204,204,1) 100%);
    background: -ms-linear-gradient(top, rgba(238,238,238,1) 0%,rgba(204,204,204,1) 100%);
    background: linear-gradient(to bottom, rgba(238,238,238,1) 0%,rgba(204,204,204,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#cccccc',GradientType=0 );
    border: 1px solid #bbb;
    color: #666;
}
.btn-white:hover, .btn-white:focus {
    border-color: #999;
    color: #444;
}
tr.exist td {
    background-color: #ffd;
}
</style>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<div id="wd-container-main">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <!--div class="wd-title">
                    <h2 class="wd-t1"><?php //echo __("Import CSV File Review", true); ?></h2>
                </div-->
                <?php
                echo $this->Session->flash();
                echo $this->Form->create('Import', array('id' => 'import-form', 'url' => array('controller' => 'project_tasks', 'action' => 'save_file_import_micro', $project_id)));
                ?>
                <?php 
                foreach ($records as $type => $record):
                    $no = 1;
                    if( $type == 'Phase' ){
                        foreach ($record as $data):
                            echo $this->Form->hidden($type . '.' . $no . '.phase_id', array('value' => $data['phase_id']));
                            echo $this->Form->hidden($type . '.' . $no . '.part_id', array('value' => $data['part_id']));
                            echo $this->Form->hidden($type . '.' . $no . '.progress', array('value' => $data['percent_complete']));
                            echo $this->Form->hidden($type . '.' . $no . '.start', array('value' => $data['StartDate']));
                            echo $this->Form->hidden($type . '.' . $no . '.end', array('value' => $data['EndDate']));
                            $no++;
                        endforeach;
                        continue;
                    }
                ?>
                <h2 class="wd-title"><?php __(Inflector::humanize(Inflector::underscore($type))); ?></h2>
                    <div class="import-fieldset">
                        <div class="dataImport <?php echo $type == 'SingleTask' ? 'no-scroll' : '' ?>">
                        <table border="0" cellspacing="1" cellpadding="3" class="display">
                            <thead>

                                <tr class="wd-header">
                                <th class="table-no"><?php __('No.'); ?></th>
                                <th class="table-col"><?php __('Task Name'); ?></th>
                                <th class="table-col"><?php __('Parent Task Name'); ?></th>
                                <th class="table-col"><?php __('Assign To'); ?></th>
                                <th class="table-col"><?php __('Start Date'); ?></th>
                                <th class="table-col"><?php __('End Date'); ?></th>
                                <th class="table-col"><?php __('Phase Name'); ?></th>
                                <th class="table-col"><?php __('Part Name'); ?></th>
                                <th class="table-info"><?php __('Description'); ?></th>
                                </tr>

                            </thead>

                            <tbody>
                            <?php foreach ($record as $id => $data) : ?> 
                                <tr id="<?php echo $id ?>" data-path="<?php echo $type == 'Milestone' ? 'milestone-' . $data['TaskName'] : implode('|', array($data['PartName'], $data['PhaseName'], $data['ParentName'], $data['TaskName'])) ?>" <?php echo ($type == 'SingleTask' ) ? 'class="single-task"' : '' ?>>
                                    <?php if($type == 'SingleTask' ): ?>
                                    <td class="table-no" align="center">
                                        <input type="checkbox" class="checkbox" value="<?php echo $id ?>" id="select-<?php echo $id ?>">
                                    </td>
                                        <?php else: ?>
                                    <td class="table-no">
                                        <?php echo $no; ?>
                                    </td>
                                        <?php endif ?>
                                    <td class="table-col"  ><?php echo $data['TaskName']; ?></td>
                                    <td class="table-col"  ><?php echo $data['ParentName']; ?></td>
                                    <td class="table-col"  ><?php echo $data['AssignedTo']; ?></td>
                                    <td class="table-col"  ><?php echo $data['StartDate']; ?></td>
                                    <td class="table-col"  ><?php echo $data['EndDate']; ?></td>
                                    <td class="table-col" id="phase-<?php echo $id ?>" ><?php echo $data['PhaseName']; ?></td>
                                    <td class="table-col"  ><?php echo $data['PartName']; ?></td>
                                    <td class="table-info">
                                        <?php echo isset($data['error'])?$data['error']:''; ?>
                                        <?php
                                        if(!empty($data['data'])){
                                            foreach($data['data'] as $key_1 => $value_1):
                                                echo $this->Form->hidden($type . '.' . $no . '.' . $key_1, array('value' => $value_1, 'id' => $key_1 . '-' . $id));
                                            endforeach;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php $no++; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                        <div class="ui-buttons" style="margin-bottom: 4px;">
                        <?php if ($type != 'SingleTask') : ?>
                            <ul class="type_buttons">
                                <li><a class="new" href="javascript:void(0)" onclick="submitForm('<?php echo $type ?>', 'do')"></a></li>
                            </ul>
                        <?php else: ?>
                            <ul class="type_buttons" style="float: right">
                                <li><a class="new" href="javascript:void(0)" onclick="submitForm('<?php echo $type ?>', 'do')"></a></li>
                            </ul>
                            <div class="fields" style="float: right">
                                <button class="btn-text" id="select-all" type="button">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-plus.png') ?>" alt="" />
                                    <span><?php __('Select all') ?></span>
                                </button>
                                <button class="btn-text" id="deselect-all" type="button">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-minus.png') ?>" alt="" />
                                    <span><?php __('Deselect all') ?></span>
                                </button>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="data[Import][plan_id]" id="ImportPlanId">
                                    <option><?php __('-Empty-') ?></option>
                                    <optgroup label="<?php __('Project phases') ?>">
                                    <?php foreach($plans as $id => $name): ?>
                                        <option value="<?php echo $id ?>-0"><?php echo $name ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="<?php __('All phases') ?>">
                                    <?php foreach($phases as $id => $name): ?>
                                        <option value="<?php echo $id ?>-1"><?php echo $name ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <button class="btn-text" id="set-phase" type="button">
                                    <img src="<?php echo $this->Html->url('/img/ui/blank-ok.png') ?>" alt="" />
                                    <span><?php __('Set Phase') ?></span>
                                </button>
                            </div>
                        <?php endif ?>
                        </div>
                    </div>
                <?php
                endforeach;
                ?>
                <?php
                //echo $this->Form->hidden('task', array('value' => '', 'name' => 'data[task]', 'id' => 'import-task'));
                echo $this->Form->hidden('type', array('value' => '', 'name' => 'data[type]', 'id' => 'import-type'));
                echo $this->Form->end();
                ?>
                <div class="wd-title">
                    <a class="btn-text" id="submit-export-all" href="javascript:void(0)">
                        <img src="<?php echo $this->Html->url('/img/ui/blank-ok.png') ?>" alt="" />
                        <span><?php __('Import all') ?></span>
                    </a>
                    <!--<a class="wd-add-project" id="submit-export-exclude" href="javascript:void(0)" style="margin-right:5px;"><span><?php __('Import Task and Milestone') ?></span></a>-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function submitForm(type){
        $('#import-type').val(type);
        $('#import-form').submit();
    }
    var tasks = <?php echo json_encode($tasks) ?>;
    function checkTask(tr){
        var path = tr.data('path');
        if( typeof tasks[path] != 'undefined' ){
            tr.addClass('exist');
        } else {
            tr.removeClass('exist');
        }
    }
    $(document).ready(function(){
        //update task
        $('tbody tr').each(function(){
            checkTask($(this));
        });
        $('#submit-task').click(function(){
            submitForm('SingleTask');
        });
        $('#submit-create-do').click(function(){
            submitForm('Task');
        });
        $('#submit-export-all').click(function(){
            submitForm('Task,SingleTask,Milestone,Update');
        });
        $('#select-all').click(function(){
            $('.checkbox').prop('checked', true);
        });
        $('#deselect-all').click(function(){
            $('.checkbox').prop('checked', false);
        });
        $('.checkbox').click(function(e){
            e.stopPropagation();
        });
        $('.single-task').click(function(e){
            var cb = $(this).find('.checkbox');
            cb.prop('checked', !cb.prop('checked'));
        });
        $('#set-phase').click(function(){
            $('.checkbox:checked').each(function(){
                var tr = $(this).parent().parent().parent();
                var id = $(this).val();
                var plan = $('#ImportPlanId option:selected').val();
                var text = $('#ImportPlanId option:selected').text();
                $('#plan_id-' + id).val(plan);
                if( plan )$('#phase-' + id).text(text);
                else $('#phase-' + id).text('');
                //modify data
                var taskName = tr.children('td:eq(1)').text();
                if( plan ){
                    var path = text + '||' + taskName;
                    if( text.indexOf('|') == -1 )
                        path = '|' + path;
                    tr.data('path', path);
                } else tr.data('path', '|||' + taskName);
                checkTask(tr);
            });
        });
    });
</script>