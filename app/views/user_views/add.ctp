<style type="text/css">
    .wd-selected-new .wd-selected{
        color: green;
    }
    .error-message{
        color: red;
        padding-top: 5px;
        font-size: 0.8em;
    }
    .wd-multi-select-new-left .wd-list-new p, .wd-selected-list p {
        color: #000000;
        cursor: default;
        padding: 5px;
        border: 1px solid #C3C3C3;
        margin-top: 2px;
        position: relative;
    }
    .wd-multi-select-new-left .wd-list-new p.wd-selected {
        color: #C3C3C3;
        cursor: default;
        padding: 5px;
        border: 1px solid #C3C3C3 ;
        margin-top: 2px;
    }
    p.wd-activated {
        color: #004686 !important;
        text-decoration: underline !important;
        cursor: default !important;
        padding: 5px !important;
        border: 1px solid #004686 !important;
        margin-top: 2px !important;
    }
    .wd-selected-list p, .wd-list-new p, .wd-tt, p {
        -moz-user-select: none;
        -khtml-user-select: none;
        user-select: none;
    }
    .overlay {
        position: absolute;
        width: 50px;
        height: 20px;
        display: none;
        top: 5px; right: 0;
        text-decoration: none;
    }
    #div_selected div.wd-selected-new p span{
        display: inline;
    }
    .wd-multi-select-new-left h3.wd-tt {
        background-color: #64a3c7;
        color: #fff;
        text-align: center;
        padding: 10px;
        margin: 0;
        margin-top: 2px;
    }
    .wd-multi-select-new-left h3.wd-tt:first-child {
        margin-top: 0px;
    }
    .wd-multi-select-new-left {
        padding: 0;
    }
    .wd-multi-select-new-left .wd-sub {
        background-color: #538dd5;
        padding: 5px 5px 5px 20px;
        text-transform: capitalize;
        margin-left: -15px;
        margin-top: 2px;
    }
    .wd-multi-select-new-left .wd-sub:first-child {
        margin-top: 0;
    }
    .wd-multi-select-new-left .wd-list-new {
        padding: 0 0 0 15px;
    }
    fieldset div.wd-submit {
        width: 300px;
    }
    #btnSave:disabled {
        filter: gray; /* IE6+ */
        filter: url("data:image/svg+xml;utf8,<svg%20xmlns='http://www.w3.org/2000/svg'><filter%20id='grayscale'><feColorMatrix%20type='matrix'%20values='0.3333%200.3333%200.3333%200%200%200.3333%200.3333%200.3333%200%200%200.3333%200.3333%200.3333%200%200%200%200%200%201%200'/></filter></svg>#grayscale"); /* Firefox 3.5+ */
        filter: grayscale(100%); /* Current draft standard */
        -webkit-filter: grayscale(100%); /* New WebKit */
        -moz-filter: grayscale(100%);
        -ms-filter: grayscale(100%);
        -o-filter: grayscale(100%);
        cursor: default;
    }
    .gantt-group-year{
        width: 150px;
        float: left;
        margin-left: 5px;
    }
    .gantt-group-year input{
        padding: 6px;
        clear: both;
        width: 50px;
    }
    .heading-back{
        display: none;
    }
    .wd-submit .btn-text{
       background: #538FFA url('/img/ui/blank-save.png') center no-repeat;
    }
    .wd-submit #reset_button{
        background: #e55c5c url('/img/ui/blank-reset.png') center no-repeat;
    }
	.wd-selected-new-right .wd-selected-list h3.wd-tt {
		color: #424242;
		font-size: 1.125em;
		font-weight: bolder;
		display: block;
		line-height: 40px;
		margin: -10px;
		padding: 0 10px;
		margin-bottom: 0;
		color: #fff;
	}
	.wd-layout .wd-main-content{
		max-width: 1920px;
		margin: auto;
	}
	.wd-multi-select-new-left{
		margin-left: 0;
	}
</style>
<div id="wd-container-main" class="wd-project-detail">
    <div class="wd-layout">
        <div class="wd-main-content wd-user-view ">
            <?php echo $this->Session->flash() ?>
            <div class="wd-list-project">
                <div class="wd-title">
                    <div class="heading-back"><a href="javascript:window.history.back();" ><i class="icon-arrow-left"></i><span><?php __('Back');?></span></a></div>
                    <h2 class="wd-t1"><?php echo __("New View", true); ?></h2>
                    <select style="margin-right:11px; width:8.8%% !important; padding: 6px;" class="wd-customs" id="FilterModel">
                        <option value="project" <?php echo $model=='project' ? 'selected="selected"': '';?> ><?php echo  __("Project", true)?></option>
                        <option value="activity" <?php echo $model=='activity' ? 'selected="selected"' : '';?> ><?php echo  __("Activity", true)?></option>
                        <option value="business" <?php echo $model=='business' ? 'selected="selected"' : '';?> ><?php echo  __("Lead", true)?></option>
                        <option value="deal" <?php echo $model=='deal' ? 'selected="selected"' : '';?> ><?php echo  __("Deal", true)?></option>
                        <option value="deal" <?php echo $model=='ticket' ? 'selected="selected"' : '';?> ><?php echo  __("Ticket", true)?></option>
                    </select>
                </div>
                <?php
                $index = 0;
                $content = array();
                $listDatas = array(
                    'workload_y' => __('Workload', true) . ' ' . date('Y', time()),
                    'workload_last_one_y' => __('Workload', true) . ' ' . (date('Y', time()) - 1),
                    'workload_last_two_y' => __('Workload', true) . ' ' . (date('Y', time()) - 2),
                    'workload_last_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) - 3),
                    'workload_next_one_y' => __('Workload', true) . ' ' . (date('Y', time()) + 1),
                    'workload_next_two_y' => __('Workload', true) . ' ' . (date('Y', time()) + 2),
                    'workload_next_thr_y' => __('Workload', true) . ' ' . (date('Y', time()) + 3),
                    'consumed_y' => __('Consumed', true) . ' ' . date('Y', time()),
                    'consumed_last_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 1),
                    'consumed_last_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 2),
                    'consumed_last_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) - 3),
                    'consumed_next_one_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 1),
                    'consumed_next_two_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 2),
                    'consumed_next_thr_y' => __('Consumed', true) . ' ' . (date('Y', time()) + 3),
                    'provisional_budget_md' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional M.D", true),
                    'provisional_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . date('Y', time()),
                    'provisional_last_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 1),
                    'provisional_last_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 2),
                    'provisional_last_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) - 3),
                    'provisional_next_one_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 1),
                    'provisional_next_two_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 2),
                    'provisional_next_thr_y' => __d(sprintf($_domain, 'Provisional'), "Budget Provisional", true) . ' ' . (date('Y', time()) + 3)
                );
                if (!empty($this->data['UserView']['content'])) {
                    $content = array_flip(unserialize($this->data['UserView']['content']));
                }
                echo $this->Form->create('UserView', array('url' => '/user_views/add?model='.$model, 'inputDefaults' => array('label' => false, 'div' => false)));

                ?>
                <div class="wd-new-list">
                    <div class="wd-multi-select-new-left" style="height: 340px; overflow: auto">
                        <?php if(!empty($showMenu['your_form']) && $showMenu['your_form']) { ?>
                        <h3 class="wd-tt new-style" rel="project_detail"><?php
                            if($model == 'project'){
                                echo __("Projects details");
                            } elseif($model == 'business'){
                                echo __("Lead Details", true);
                            } elseif($model == 'deal'){
                                echo __("Deal Details", true);
                            } elseif($model == 'ticket'){
                                echo __("Ticket Details", true);
                            } else {
                                echo __("Activity Details", true);
                            }
                        ?></h3>
                        <div class="wd-list-new" rel="project_detail" >
                            <?php
							//Thiet lap field hien thi mac dinh khi add new.
                            if($model=='project') {
                                $fieldDefauls=array('Project.project_name');
                            } elseif($model=='business') {
                                $fieldDefauls=array('Sale.sale_customer_id', 'Sale.name');
                            } elseif($model=='deal') {
                                $fieldDefauls=array('Sale.sale_customer_id', 'Sale.name');
                            } elseif($model=='ticket') {
                                $fieldDefauls=array('name');
                            } else {
                                $fieldDefauls=array('name','short_name','family_id');
                            }
							//vong lap cac field hien thi
                             foreach ($projectFields as $field => $name) : ?>
                                <?php
                                if ($field == 'id') {
                                    continue;
                                }
                                $class = '';
                                if (in_array($field,$fieldDefauls)) {
                                    $class = 'wd-selected wd-activated';
                                } elseif ($content && isset($content[$field])) {
                                    $class = 'wd-activated';
                                }
                                ?>
                                <p class="<?php echo $class ?>" rel="<?php echo $field ?>">
                                    <?php
                                    $name = !empty($listDatas) && !empty($listDatas[$field]) ? $listDatas[$field] : $name;
                                    if( $model == 'project' ){
                                        if( substr($name, 0, 1) != '*' ){
                                            echo __d(sprintf($_domain, 'Details'), $name, true);
                                        } else echo __(substr($name, 1), true);
                                    } else if( $model == 'activity' && $field == 'manual_consumed' ){
                                        echo __d(sprintf($_domain, 'Project_Task'), $name, true);
                                    } else {
                                        echo __($name, true);
                                    }
                                    echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $field, 'checked' => !empty($class), 'hiddenField' => false));
                                    echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    ?>
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <?php } if($model=='project') {
                            $words = $this->requestAction('/translations/getByPage', array('pass' => array('KPI')));
                        if(!empty($enableKPI)) {
                        ?>
                        <h3 class="wd-tt" rel="project_detail"><?php echo __("KPI", true); ?></h3>
                        <div class="wd-list-new" rel="project_detail" >
                            <?php

                             foreach ($amrFields as $field => $name) : ?>
                                <?php
                                $class = '';
                                if ($field == 'ProjectAmr.id' || $field == 'ProjectAmr.project_id') {
                                    continue;
                                } elseif ($content && isset($content[$field])) {
                                    $class = 'wd-activated';
                                }
                                ?>
                                <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                    <?php
                                    if( in_array($name, $words) ){
                                        __d(sprintf($_domain, 'KPI'), $name);
                                    }
                                    else {
                                        __($name);
                                    }
                                    echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                    echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    ?>
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <?php } if(!empty($showMenu['task'])){ ?>
                        <h3 class="wd-tt" rel="project_detail"><?php echo __("Task", true); ?></h3>
                        <div class="wd-list-new" rel="project_detail" >
                            <?php foreach ($budgetFields as $type => $fields) {
                                if($type == 'Task'){
									foreach($fields as $field => $name){
										$class = '';
										?>
										<p rel="<?php echo $field ?>" class="<?php echo $class ?>">
											<?php
											$_field = trim(str_replace('ProjectBudgetSyn.', '', $field));
											 if( $type == 'Task' ) {
												$translated = __d(sprintf($_domain, 'Project_Task'), $name, true);
											echo !empty($listDatas) && !empty($listDatas[$_field]) ? $listDatas[$_field] : $translated;
											echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
												'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
											echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
												'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
											} ?>
										</p>
									<?php }
								} ?>
                            <?php } ?>
                        </div>
                        <!-- End Budget -->
                        <?php } if($canSeeBudget){ ?>
                        <h3 class="wd-tt" rel="project_detail"><?php echo __("Budget", true); ?></h3>
                        <div class="wd-list-new" rel="project_detail" >
                            <?php foreach ($budgetFields as $type => $fields) :
                                if($type != 'Task'){
                             ?>
                                <h4 class="wd-sub">
                                    <?php
                                    if( $type == 'sales' ){
                                        echo !empty($tranBud['project_budget_sales']) ? $tranBud['project_budget_sales'] : __($type, true);
                                    } else if( $type == 'purchases' ){
                                        echo !empty($tranBud['project_budget_purchases']) ? $tranBud['project_budget_purchases'] : __($type, true);
                                    } else if( $type == 'internal' ){
                                        echo !empty($tranBud['project_budget_internals']) ? $tranBud['project_budget_internals'] : __($type, true);
                                    } else if( $type == 'external' ){
                                        echo !empty($tranBud['project_budget_externals']) ? $tranBud['project_budget_externals'] : __($type, true);
                                    } else if($type == 'provisional'){
                                        echo !empty($tranBud['project_budget_provisionals']) ? $tranBud['project_budget_provisionals'] : __($type, true);
                                    }else if($type == 'task'){
                                        
                                    } else {
                                        echo __($type, true);
                                    }
                                    ?>
                                </h4>
                                <?php
                                foreach($fields as $field => $name):
                                $class = '';
                                if ($field == 'ProjectAmr.id' || $field == 'ProjectAmr.project_id') {
                                    continue;
                                }
                                ?>
                                <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                    <?php
                                    $_field = trim(str_replace('ProjectBudgetSyn.', '', $field));
                                    if( $type == 'sales' ){
                                        $translated = __d(sprintf($_domain, 'Sales'), $name, true);
                                    } else if( $type == 'purchases' ){
                                        $translated = __d(sprintf($_domain, 'Purchase'), $name, true);
                                    } else if( $type == 'internal' ){
                                        $translated = __d(sprintf($_domain, 'Internal_Cost'), $name, true);
                                    } else if( $type == 'external' ){
                                        $translated = __d(sprintf($_domain, 'External_Cost'), $name, true);
                                    } else if( $field == 'ProjectAmr.manual_consumed' ){
                                        $translated = __d(sprintf($_domain, 'Project_Task'), $name, true);
                                    } else if( $type == 'Task' ) {
                                        $translated = __d(sprintf($_domain, 'Project_Task'), $name, true);
                                    } else {
                                        $translated = __($name, true);
                                    }
                                    if( $type != 'Task' ) {
                                        echo !empty($listDatas) && !empty($listDatas[$_field]) ? $listDatas[$_field] : $translated;
                                        echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                            'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                        echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    }
                                    ?>
                                </p>
                                <?php endforeach; ?>
                            <?php } endforeach; ?>
                        </div>
                        <!-- End Budget -->
                        <?php }
                        if(!empty($financeFields) && $canSeeBudget) { ?>
                        <h3 class="wd-tt display_lang_<?php echo $LANG?>" rel="project_detail"><?php echo __("Finance", true); ?></h3>
                        <div class="wd-list-new display_lang_<?php echo $LANG?>" rel="project_detail" >
                            <?php foreach ($financeFields as $field => $name) : ?>
                                <?php
                                $class = '';
                                $saveFieldName = explode(' ', $name);
                                if(!empty($saveFieldName[2]) && is_numeric($saveFieldName[2])){
                                    $name = $saveFieldName[0] . ' ' . $saveFieldName[1] . ' (Y)';
                                    $name = __d(sprintf($_domain, 'Finance'), $name, true);
                                    $name = str_replace('(Y)', $saveFieldName[2], $name);
                                } else {
                                    $name = $name;
                                    $name = __d(sprintf($_domain, 'Finance'), $name, true);
                                }
                                ?>
                                <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                    <?php
                                    echo __d(sprintf($_domain, 'Finance'), $name, true);
                                    echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                    echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    ?>
                                </p>
                            <?php endforeach;?>
                        </div>
                        <?php } ?>
                        <!-- finance plus -->
                        <?php
                        if(!empty($financeFieldPlus) && $canSeeBudget) { ?>
                        <h3 class="wd-tt display_lang_<?php echo $LANG?>" rel="project_detail"><?php echo __("Finance+", true); ?></h3>
                        <div class="wd-list-new display_lang_<?php echo $LANG?>" rel="project_detail" >
                            <?php foreach ($financeFieldPlus as $field => $name) : ?>
                                <?php $domain_name = 'Finance';
								$tmp_field = $field;
								$domain_key = explode('_', $tmp_field);
								switch($domain_key[0]){
									// case 'ProjectFinancePlus.inv':
										// $domain_name = 'Budget_Investment';
										// break;
									// case 'ProjectFinancePlus.fon':
										// $domain_name = 'Budget_Operation';
										// break;
									case 'ProjectFinancePlus.finaninv':
										$domain_name = 'Finance_Investment';
										break;
									case 'ProjectFinancePlus.finanfon':
										$domain_name = 'Finance_Operation';
										break;
									default: 
										$domain_name = 'Finance';
										break;
									
								}
								
                                $class = '';
                                $saveFieldName = explode('_', $field);
                                if(!empty($saveFieldName[2]) && is_numeric($saveFieldName[2])){
									$pf_year = $saveFieldName[2];
                                    $name = str_replace($pf_year, '(Y)', $name);
                                    $name = __d(sprintf($_domain, $domain_name), $name, true);
                                    $name = str_replace('(Y)', $pf_year, $name);
                                }else {
                                    $name = $name;
                                    $name = __d(sprintf($_domain, $domain_name), $name, true);
                                }
                                ?>
                                <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                    <?php
                                    echo __d(sprintf($_domain, 'Finance'), $name, true);
                                    echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                    echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    ?>
                                </p>
                            <?php endforeach;?>
                        </div>
                        <?php } ?>
                        <!-- end -->
                        <!-- finance two plus -->
                        <?php
                        if(!empty($financeFieldTwoPlus) && $canSeeBudget) { ?>
                        <h3 class="wd-tt display_lang_<?php echo $LANG?>" rel="project_detail"><?php echo __("Finance++", true); ?></h3>
                        <div class="wd-list-new display_lang_<?php echo $LANG?>" rel="project_detail" >
                            <?php foreach ($financeFieldTwoPlus as $field => $name) : ?>
                                <?php
                                $class = '';
                                $saveFieldName = explode(' (', $name);
                                if(!empty($saveFieldName[1])) $saveFieldName[1] = str_replace(')', '', $saveFieldName[1]);
                                if(!empty($saveFieldName[1]) && is_numeric($saveFieldName[1])){
                                    $name = __d(sprintf($_domain, 'Finance_2'), $saveFieldName[0], true) . ' ' . $saveFieldName[1];
                                } else {
                                    $name = __d(sprintf($_domain, 'Finance_2'), $name, true);
                                }
                                ?>
                                <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                    <?php
                                    echo __d(sprintf($_domain, 'Finance_2'), $name, true);
                                    echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                    echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                        'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                    ?>
                                </p>
                            <?php endforeach;?>
                        </div>
                        <?php } ?>
                        <!-- others -->
                        <?php
                        if(!empty($othersField)) { ?>
                        <h3 class="wd-tt display_lang_<?php echo $LANG?>" rel="project_detail"><?php echo __("Others", true); ?></h3>
                        <div class="wd-list-new display_lang_<?php echo $LANG?>" rel="project_detail" >
                            <?php
                            $finan_title = array(
								'inv' => __d(sprintf($_domain, 'Budget_Investment'), "Budget Investment", null),
								'fon' => __d(sprintf($_domain, 'Budget_Operation'), "Budget Operation", null),
								'finaninv' =>  __d(sprintf($_domain, 'Finance_Investment'), "Finance Investment", null),
								'finanfon' => __d(sprintf($_domain, 'Finance_Operation'), "Finance Operation", null),
							);
                            foreach($othersField as $field => $name):
                            $class = '';
                            if ($content && isset($content[$field])) {
                                $class = 'wd-activated';
                            }
                            ?>
                            <p rel="<?php echo $field ?>" class="<?php echo $class ?>">
                                <?php
                                $translated = !empty($finan_title[$name]) ? $finan_title[$name] : __($name, true);
                                echo $translated;
                                echo $this->Form->input('UserView.content.' . $index . '.value', array('type' => 'checkbox',
                                    'style' => 'display:none', 'value' => $field, 'checked' => false, 'hiddenField' => false));
                                echo $this->Form->input('UserView.content.' . $index . '.weight', array('type' => 'checkbox',
                                    'style' => 'display:none', 'value' => $index++, 'checked' => false, 'hiddenField' => false));
                                ?>
                            </p>
                            <?php endforeach; ?>
                        </div>
                        <?php } ?>
                        <!-- end -->
                        <?php } ?>
                    </div>
                    <div class="wd-submit-select">
                        <a href="javascript:void(0);" class="wd-next"><?php echo __("Next", true); ?></a>
                        <a href="javascript:void(0);" class="wd-prev"><?php echo __("Prev", true); ?></a>
                    </div>
                    <div class="wd-selected-new-right">
                        <div class="wd-view">
                            <?php
                            echo $this->Form->input('UserView.name', array('style' => 'width: 100%', 'type' => 'text', 'placeholder' => __('View name', true)));
                            ?>
                        </div>
                        <div class="wd-description">
                            <?php
                            echo $this->Form->input('UserView.description', array('style' => 'width: 100%', 'type' => 'text', 'placeholder' => __('View description', true)));
                            ?>
                        </div>
                        <div class="wd-selected-list" id="div_selected" style="height: 258px; overflow: auto">
                            <span class="overlay"><span class="wd-up" onclick="moveUp(this)"></span><span class="wd-down" onclick="moveDown(this)"></span></span>
                            <h3 class="wd-tt" rel="project_detail"><?php echo __("Personalized Views", true); ?></h3>
                            <div class="wd-selected-new" rel="project_detail"></div>
                        </div>
                        <div class="default-view" style="clear: both;text-align:center; font-size: 12px;">
                            <?php
                            echo $this->Form->input('UserView.model', array('value'=>$model,'type' => 'hidden'));
                            echo $this->Form->input('UserView.default', array('style' => 'float:left;', 'type' => 'checkbox'));?>
                            <p style="float:left;font-weight:bold"><?php echo __("Set it as default view", true); ?></p>
                            <br style="clear: both;"/>
                            <?php
                            if($model=='project') {
                                echo $this->Form->input('UserStatusView.progress_view', array('style' => 'float:left;', 'type' => 'checkbox', 'checked' => true));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as In progress view", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserStatusView.oppor_view', array('style' => 'float:left;', 'type' => 'checkbox', 'checked' => true));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Opportunity view", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserStatusView.archived_view', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Archived view", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserStatusView.model_view', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Model view", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserView.mobile', array('style' => 'float:left;', 'value' => '1', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold; color: #0cb0e0"><?php echo __("Mobile", true); ?></p>
                                <br style="clear: both;"/>
                            <?php
                            } elseif($model=='business'){
                                $leadStatus = array('Open', 'Closed Won', 'Closed Lose', 'All');
                                echo $this->Form->input('UserStatusViewSale.lead_status', array(
                                    'div' => false,
                                    'label' => false,
                                    'style' => 'float: left;border: 1px solid #E0E0E0;width: 140PX;padding: 6px;margin-left: 3px;',
                                    "options" => $leadStatus));
                                ?>
                                <br style="clear: both;"/>
                            <?php
                            } elseif($model=='deal'){
                                $dealStatus = array('Open', 'Archived', 'Renewal', 'All');
                                echo $this->Form->input('UserStatusViewSaleDeal.deal_status', array(
                                    'div' => false,
                                    'label' => false,
                                    'style' => 'float: left;border: 1px solid #E0E0E0;width: 140PX;padding: 6px;margin-left: 3px;',
                                    "options" => $dealStatus));
                                ?>
                                <br style="clear: both;"/>
                            <?php
                            } elseif($model=='ticket'){
                                //do nothing
                            } else {
                                echo $this->Form->input('UserStatusViewActivity.activated', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Activated", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserStatusViewActivity.not_activated', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Not activated", true); ?></p>
                                <br style="clear: both;"/>
                                <?php
                                echo $this->Form->input('UserStatusViewActivity.activated_and_not_activated', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as Activated and not activated", true); ?></p>
                                <br style="clear: both;"/>
                            <?php
                            }
							/**
							* Update hien thi cac field Gantt for PM. Updated by QuanNV 02/07/2019. Ticket #417
							*/
                            if (!empty($isAdmin)) : ?>
                                <?php
                                echo $this->Form->input('UserView.public', array('style' => 'float:left;', 'type' => 'checkbox'));
                                ?>
                                <p style="float:left;font-weight:bold"><?php echo __("Set it as public view", true); ?></p><br style="clear: both;"/>
							<?php endif; ?>	
							<?php if( $model == 'project' ): ?>
								<?php
									echo $this->Form->input('UserView.gantt_view', array('style' => 'float:left;margin-left: -15px;', 'class' => 'mobile-not-available', 'type' => 'hidden'));
								?>
								<!--p style="float:left;font-weight:bold"><?php echo __("Gantt", true); ?></p-->
								<?php
									echo $this->Form->input('UserView.initial', array('style' => 'float:left;', 'class' => 'mobile-not-available', 'type' => 'checkbox'));
								?>
									<p style="float:left;font-weight:bold"><?php echo __("Gantt Initial", true); ?></p><br style="clear: both;"/>
								<?php
									echo $this->Form->input('UserView.real', array('style' => 'float:left;', 'class' => 'mobile-not-available', 'type' => 'checkbox'));
								?>
									<p style="float:left;font-weight:bold"><?php echo __("Gantt Real Time", true); ?></p><br style="clear: both;"/>
								<?php
									echo $this->Form->input('UserView.stones', array('style' => 'float:left;', 'class' => 'mobile-not-available', 'type' => 'checkbox'));
								?>
								<p style="float:left;font-weight:bold"><?php echo __("Milestones", true); ?></p><br style="clear: both;"/>
								<div class="gantt-group-year">
									<p style="float:left;font-weight:bold"><?php echo __("From", true); ?></p>
									<?php
									echo $this->Form->input('UserView.from', array('style' => 'float:left;', 'class' => 'mobile-not-available', 'value' => date('Y')));
									?>
									<p style="font-weight:bold"><?php echo __("To", true); ?></p>
									<?php
									echo $this->Form->input('UserView.to', array('class' => 'mobile-not-available', 'value' => date('Y')));
									?>
								</div>
								<p style="display:none; clear: both; float: left; padding-left: 5px;color: red;" class="wd-error">The end date must be greater than start date</p>
							<?php endif ?>
                        </div>
                    </div>
                    <div class="error" style="clear: both;text-align:center"></div>
                    <fieldset>
                        <div class="wd-submit">
                            <button type="submit btn-green" class="btn-text" id="btnSave" style="margin-left:18px"/>
                                <!-- <img src="<?php echo $this->Html->url('/img/ui/blank-save.png') ?>" />
                                <span><?php __('Save') ?></span> -->
                            </button>
                            <a class="btn-text btn-red" id="reset_button" href="<?php echo $html->url('/user_views/add?model=' . $model); ?>">
                                <!-- <img src="<?php echo $this->Html->url('/img/ui/blank-reset.png') ?>" />
                                <span><?php echo __("Refresh", true); ?></span> -->
                            </a>
                        </div>
                    </fieldset>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo $html->script('common'); ?>
<!--[if IE]>
<?php echo $html->script('excanvas.compiled'); ?>
<![endif]-->
<script type="text/javascript">
	$(window).resize(function () {
		update_table_height();
    });
	function update_table_height(){
		var wdTable = $('.wd-multi-select-new-left');
		var heightTable = $(window).height() - wdTable.offset().top - wdTable.parent().find('.wd-submit').outerHeight(true) - 40;
        wdTable.height(heightTable);
	}
	update_table_height();
    function updateSort($element, add){
        $('.wd-list-new [rel="'+ $element.attr('rel')+ '"]').find('input:last').val($element.parent().children().index($element) + (add || 0));
    }
    function moveUp(e){
        var me = $(e);
        var $element = me.parent().parent();
        var $swaper = $element.prev();
        if ($swaper.length > 0) {
            var newMe = $element.clone();
            $swaper.before(newMe);
            updateSort($swaper.prev().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                newMe.animate({backgroundColor:'#FFF'}, 'slow');
            }));
            updateSort($swaper);
            $element.remove();
        }
    }
    function moveDown(e){
        var me = $(e);
        var $element = me.parent().parent();
        var $swaper = $element.next();
        if ($swaper.length > 0) {
            var newMe = $element.clone();
            $swaper.after(newMe);
            updateSort($swaper.next().animate({backgroundColor:'#CCFFCC'}, 'slow', '', function(){
                newMe.animate({backgroundColor:'#FFF'}, 'slow');
            }),-1);
            updateSort($swaper , -1);
            $element.remove();
        }
    }
    function canAddMore(){
        var mobile = $('#UserViewMobile').prop('checked');
        if( mobile ){
            return $('.wd-activated').length + $('.wd-selected-new p').length < 5;
        }
        return true;
    }
    var model = <?php echo json_encode($model);?>;
    function canSubmit(){
        if($('#UserViewMobile').prop('checked') && $('.wd-selected-new p').length > 5 ){
            //disable save button
            $('#btnSave').prop('disabled', true);
        } else {
            $('#btnSave').prop('disabled', false);
        }
        if(model != 'project'){
            $('#btnSave').prop('disabled', false);
        }
    }
    function bind(e){
        $(e).off('click dblclick').on("click",function(){
            var $element = $(this);
            if ($element.hasClass("wd-selected")){
                return false;
            }
            $element.toggleClass('wd-activated');
        }).on('dblclick',function(){
            var $element = $(this);
            if ($element.hasClass("wd-selected")){
                return false;
            }
            if($element.addClass('wd-activated').closest('.wd-selected-list').length){
                removeItems();
            }else{
                addItems();
            }
        });
    }
    var $overlay = $('.wd-selected-list .overlay');
    var _sales = <?php echo json_encode(__('Sales', true)) ?>,
        _internal = <?php echo json_encode(__('Internal', true)) ?>,
        _external = <?php echo json_encode(__('External', true)) ?>;

    function addItems(){
        if( !canAddMore() ){
            alert('<?php __('Maximum fields (5) exceeded for mobile view') ?>');
            return false;
        }
        var $panel = $('.wd-selected-list .wd-selected-new');
        $('.wd-list-new .wd-activated').each(function(){
            var $element = $(this);
            var $clone = $element.removeClass('wd-activated').clone();
            var field = $element.attr('rel').split('.')[1];
            var text = $clone.text();
            if( field ){
                if( field.substr(0,5) == 'sales' ){
                    text += ' <small style="color: gray">(' + _sales + ')</small>';
                } else if( field.substr(0,8) == 'internal' ){
                    text += ' <small style="color: gray">(' + _internal + ')</small>';
                } else if( field.substr(0,8) == 'external' ){
                    text += ' <small style="color: gray">(' + _external + ')</small>';
                }
            }
            $element.addClass('wd-selected').find('input').prop('checked', true);
            $panel.append($clone.html(text).append($overlay.clone().show()));
            bind($clone);
            updateSort($clone);
        });
        canSubmit();
    }
    function removeItems(){
        $('.wd-selected-new .wd-activated').each(function(){
            var $element = $(this);
            $('.wd-list-new [rel="'+ $element.attr('rel')+ '"]').removeClass('wd-selected').find('input').prop('checked', false);
            $element.remove();
        });
        canSubmit();
    }
    var isAdmin = <?php echo json_encode($isAdmin);?>;
    function validated(){
        var _start = parseInt($("#UserViewFrom").val());
        var _end = parseInt($("#UserViewTo").val());
        var result = true;
        if(_start <= _end){
            $('.wd-error').hide();
            result = true;
        } else {
            $('.wd-error').show();
            result = false;
        }
        if(model != 'project'){
            result = true;
        }
        if(isAdmin == false){
            result = true;
        }
        return result;
    }
    $(document).ready(function(){
        $('#UserViewMobile').click(function(){
            canSubmit();
            //$('.mobile-not-available').prop('disabled', $(this).prop('checked'));
        });
        $('.wd-selected-new').sortable({
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: true,
            update: function(){
                updateSortAll();
            }
        });
        addItems();
        bind('.wd-list-new p, .wd-selected-list p');
        $('.wd-submit-select .wd-next').click(function(){
            addItems();
        });
        $('.wd-submit-select .wd-prev').click(function(){
            removeItems();
        });
        $('#FilterModel').change(function(){
            $('#FilterModel option').each(function(){
                if($(this).is(':selected')){
                    var id = $('#FilterModel').val();
                    window.location = ('/user_views/add?model=' +id);
                }
            });
        });
        $('#btnSave').closest('form').submit(validated);
    });
    function updateSortAll(){
        $('.wd-selected-new p').each(function(index){
            $('.wd-list-new [rel="'+ $(this).attr('rel')+ '"]').find('input:last').val(index);
        });
    }
	$('.wd-multi-select-new-left').find('.wd-tt').on('click', function(e){
		var _this = $(this);
		var list = _this.next('.wd-list-new');
		if( list.length){
			var _time = 300;
			_time = parseInt( list.height() / 300) * 100;
			_time = _time > 1000 ? 500 : _time;
			list.slideToggle(_time);
		}
	});
</script>
