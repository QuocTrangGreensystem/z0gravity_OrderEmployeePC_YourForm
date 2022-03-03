<?php
$arg = $this->passedArgs;
$arg["?"] = $this->params['url'];
unset($arg['?']['url'], $arg['?']['ext']);
$type = 'monthyear';
// echo $html->script('jquery.flexslider-min');
// echo $html->css('flexslider');
echo $this->Html->script(array(
    'dashboard/jqx-all',
    'dashboard/jqxchart',
    'dashboard/jqxcore',
    'dashboard/jqxdata',
    'dashboard/jqxcheckbox',
    'dashboard/jqxradiobutton',
    'dashboard/gettheme',
    'dashboard/jqxgauge',
    'dashboard/jqxbuttons',
    'dashboard/jqxslider',

));
echo $this->Html->css(array(
    'dashboard/jqx.base',
    'dashboard/jqx.web'
));

?>
<?php 
    echo $html->css('slick'); 
    echo $html->css('slick-theme');  
    echo $html->script('slick.min'); 
?>

<?php echo $this->element('dialog_detail_value');
//echo $this->element('dialog_projects');
?>
<script type="text/javascript">
    var showProfile = <?php echo isset($companyConfigs['activate_profile']) ? (string) $companyConfigs['activate_profile'] : '0' ?>;
    var is_manual_consumed = <?php echo isset($companyConfigs['manual_consumed']) ? (string) $companyConfigs['manual_consumed'] : '0' ?>;
    var gap_linked_task = <?php echo isset($companyConfigs['gap_linked_task']) ? (string) $companyConfigs['gap_linked_task'] : '0' ?>;
    var webroot = <?php echo json_encode($this->Html->url('/')) ?>;
    var hightlightTask = <?php echo json_encode(isset($_GET['id']) ? $_GET['id'] : '') ?>;
   
</script>
<fieldset style="display: none;">
    <?php
    echo $this->Form->create('Export', array(
        'type' => 'POST',
        'url' => array('controller' => 'project_tasks', 'action' => 'export', $projectName['Project']['id'])));
    echo $this->Form->input('list', array('type' => 'text', 'value' => '', 'id' => 'export-item-list'));
    echo $this->Form->end();
    ?>
</fieldset>
<!-- /export excel  -->
<!-- dialog_import -->
<div id="dialog_import_CSV" style="display:none" title="<?php __('Import CSV file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadForm', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_csv', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[csv_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.csv)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- dialog_import -->
<div id="dialog_import_MICRO" style="display:none" title="<?php __('Import MICRO file') ?>" class="buttons">
    <?php
    echo $this->Form->create('Import', array('id' => 'uploadFormMicro', 'type' => 'file',
        'url' => array('controller' => 'project_tasks', 'action' => 'import_task_micro_project', $projectName['Project']['id'])));
    ?>
    <div class="wd-input">
        <center>
            <label><?php echo __('File:') ?></label>
            <input type="file" name="FileField[micro_file_attachment]" />
        </center>
        <div style="clear:both; margin-left:100px; width: 220px; color: #008000; font-style:italic;">(<?php __('Allowed file type') ?>: *.xml)</div>
    </div>
    <ul class="type_buttons">
        <li><a class="cancel" href="javascript:void(0)"><?php echo __('Close') ?></a></li>
        <li><a id="import-micro-submit" class="new" onclick="return false;" href="#"><?php echo __('Submit') ?></a></li>
        <li id="error-micro"></li>
    </ul>
    <?php echo $this->Form->end(); ?>
</div>
<!-- End dialog_import -->
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-left">
                <!-- Gantt -->

                <?php 
                
                if (!empty($projectMilestones)) { ?>
                <div id="GanttChartDIV">
                    <div id="carousel" class="wd-carousel">
                        <div class="slides">
                            <?php
                            $rows = 0;
                            $start = $end = 0;
                            $data = $projectId = $conditions = array();
                            $stones = array();
                            $_milestoneColor = array();
                            
                                $i = 0;
                                $activeDate ='';
                                $currentDate = strtotime(date('d-m-Y', time()));
                //                 ob_clean();
                // debug($currentDate);
                // exit;
                                $min = abs($currentDate - $tmpProjectMilestones[0]['ProjectMilestone']['milestone_date']);
                                foreach ($projectMilestones as $p) { 
                                    $milestone_date = strtotime($p['milestone_date']);
                                    $flag = abs($currentDate - $milestone_date);
                                     
                                    if($min > $flag && $milestone_date <= $currentDate){
                                        $min = $flag;
                                        $activeDate = $p['id'];
                                    }
                                }
                                //debug($currentDate);
                                foreach ($projectMilestones as $p) { $i++; 
                                    $milestone_date = strtotime($p['milestone_date']);
                                    $nearDate = ($currentDate - $milestone_date);
                                    $item_class = '';
                                    if ($activeDate == $p['id']) {
                                        $item_class = 'active-item';
                                    }else{
                                        if($milestone_date > $currentDate){
                                            $item_class = 'last-item flag-item';
                                        }
                                    }
                                    if($p['validated']){
                                        $item_class .= ' milestone-green';
                                    }else{
                                        if ($milestone_date < $currentDate) {
                                            $item_class .= ' milestone-mi';
                                        } else if($milestone_date > $currentDate) {
                                            $item_class .= ' milestone-blue';
                                        } else {
                                            $item_class .= ' milestone-orange';
                                        }
                                    }
                                    if($milestone_date < $currentDate) { $item_class .= ' out_of_date'; }
                                    ?>
                                        <div data-num = <?php echo $i; ?> class="wd-slider-item">
                                            <span class="lnr lnr827"></span>
                                            <div class="milestones-item <?php echo $item_class; ?>" data-id="<?php echo $p['id']; ?>">
                                                <div class="date-milestones">
                                                    <span><?php echo date("d", strtotime($p['milestone_date'])); ?></span>
                                                    <span><b><?php echo date("M", strtotime($p['milestone_date'])); ?></b></span>
                                                </div>
                                                <p><?php echo $p['project_milestone']; ?></p>
                                            </div>
                                        </div>
                                    <?php 
                                }
                                if($i < 5){
                                    for( $j = $i ; $j < 5 ; $j++){
                                        echo '<li></li>';
                                    }
                                }?>
                        </div>
                    </div>

                </div>
                <?php } ?>
                <!-- Gantt.end -->
                <!-- Log comment -->
                <div class="amrs-log-comment">

                    <div class="amrs-wrap">
                        <div class="amrs-log-heading">
                            <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Participant(s)', true);?></span></h3>
                            <div class="amrs-heading-avatar">
                                <ul><?php
                                    $name = $full_name = '';
                                    if(!empty($listAssign)){
                                        foreach($listAssign as $key => $listEmAssig){
                                            $full_name = $listEmAssig['Employee']['name'];
                                            if($listEmAssig['Employee']['is_profit_center']){ ?>
                                                <li><a class="circle-name" href ="#" title ="<?php echo $full_name; ?>"><i class="icon-user"></i></a></li>
                                            <?php }else{
                                                if($checkAvatar[$listEmAssig['Employee']['id']]){
                                                    $src = $this->UserFile->avatar($listEmAssig['Employee']['id']); ?>
                                                    <li><a class="circle-name" href ="#" title ="<?php echo $full_name; ?>"><img src="<?php echo $src; ?>" /></a></li>
                                                <?php }else{
                                                    $employee_name = explode(' ', $full_name);
                                                    $name = substr( trim($employee_name[0]),  0, 1) .''.substr( trim($employee_name[1]),  0, 1);
                                                    ?>
                                                    <li><a class="circle-name" href ="#" title ="<?php echo $full_name; ?>"><?php echo $name ?></a></li>
                                                <?php }
                                                }
                                            }
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php 
                            if(!empty($project)){
                                ?>
                                <div class="amrs-project-objectives">
                                    <h3><span><?php echo __d(sprintf($_domain, 'Details'), 'Project Objectives', true);?></span></h3>
                                    <p><?php echo $project['project_objectives']; ?></p>
                                </div>
                                <?php 
                            }
                        ?>
                        <ul class="amrs-list-comment"><?php
                            $name = $full_name = '';
                            if(!empty($logSystems)){
                                $name = explode(" ", $logSystems[0]['name']);
                                if(!empty($name)){
                                    $full_name = trim($name[0]) .' '.trim($name[1]);
                                    $name = substr( trim($name[0]),  0, 1) .''.substr( trim($name[1]),  0, 1);
                                }?>

                            <li class="log-arm-field log-arm-comment"  data-log-id="<?php echo $logSystems[0]['id'] ?>">
                                <span class="log-field-name"><?php echo __d(sprintf($_domain, 'KPI'), 'Avancement', true);?></span>
                                <div style="clear: both"></div>
                                <p class="log-avatar circle-name"><?php echo $name ?></p>
                                <div class="log-body">
                                    <h4 class="log-author"><?php echo $full_name ?></h4>
                                    <em class="log-time"><?php echo date('H:i d-m-Y', $logSystems[0]['created']) ?></em>
                                    <p class="log-content"><?php echo $logSystems[0]['description'] ?></p>
                                </div>
                            </li>
                           <?php } 
                            if(!empty($todos)){
                                $name = explode(" ", $todos[0]['name']);
                                if(!empty($name)){
                                    $full_name = trim($name[0]) .' '.trim($name[1]);
                                    $name = substr( trim($name[0]),  0, 1) .''.substr( trim($name[1]),  0, 1);
                                }?>

                            <li class="log-arm-field log-arm-todo"  data-log-id="<?php echo $todos[0]['id'] ?>">
                                <span class="log-field-name"><?php echo __d(sprintf($_domain, 'KPI'), 'Réalisé', true);?></span>
                                <div style="clear: both"></div>
                                <p class="log-avatar circle-name"><?php echo $name ?></p>
                                <div class="log-body">
                                    <h4 class="log-author"><?php echo $full_name ?></h4>
                                    <em class="log-time"><?php echo date('H:i d-m-Y', $todos[0]['created']) ?></em>
                                    <p class="log-content"><?php echo $todos[0]['description'] ?></p>
                                </div>
                            </li>
                           <?php } 
                            if(!empty($dones)){
                                $name = explode(" ", $dones[0]['name']);
                                if(!empty($name)){
                                    $full_name = trim($name[0]) .' '.trim($name[1]);
                                    $name = substr( trim($name[0]),  0, 1) .''.substr( trim($name[1]),  0, 1);
                                }?>

                            <li class="log-arm-field log-arm-done" data-log-id="<?php echo $dones[0]['id'] ?>">
                                <span class="log-field-name"><?php echo __d(sprintf($_domain, 'KPI'), 'Decision', true);?></span>
                                <div style="clear: both"></div>
                                <p class="log-avatar circle-name"><?php echo $name ?></p>
                                <div class="log-body">
                                    <h4 class="log-author"><?php echo $full_name ?></h4>
                                    <em class="log-time"><?php echo date('H:i d-m-Y', $dones[0]['created']) ?></em>
                                    <p class="log-content"><?php echo $dones[0]['description'] ?></p>
                                </div>
                            </li>
                           <?php } ?>
                        </ul>

                    </div>
                </div>

                <!-- Log comment -->
                <div class="amrs-progress">
                    <div class="progress-circle progress-circle-yellow">
                        <div class="progress-circle-inner">
                            <i class="icon-question" aria-hidden="true"></i>
                            <?php 
                                $per = $real = 0;
                                if(empty($totals['inv']['budget'])){
                                    $totals['inv']['budget'] = 0;
                                }
                                if(empty($totals['inv']['avancement'])){
                                    $totals['inv']['avancement'] = 0;
                                }
                                if($totals['inv']['budget'] == 0 && $totals['inv']['avancement'] == 0) {
                                    $per = 0;
                                }else if($totals['inv']['budget'] == 0) {
                                    $per = 100;
                                }else {
                                    $real = $per = round($totals['inv']['avancement']/$totals['inv']['budget'] * 100,2);
                                    $per = ($per > 100) ? 100 : $per;
                                }
                            ?>
                            <h3><span><?php echo __d(sprintf($_domain, 'Finance'), 'Investissement', true); ?></span></h3>
                            <canvas data-real = "<?php echo $real; ?>" data-value = "<?php echo $per; ?>" id="myCanvas" width="165" height="160" style="" class="canvas-circle"></canvas>
                            <div class ="progress-value progress-validated"><p><?php echo __d(sprintf($_domain, 'KPI'), 'Budget', true);?></p><span><?php echo number_format((!empty($totals['inv']['budget']) ? $totals['inv']['budget'] : 0), 2, '.', ' '); ?> € </span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'KPI'), 'Engagé', true);?></p><span><?php echo number_format((!empty($totals['inv']['avancement']) ? $totals['inv']['avancement'] : 0), 2, '.', ' '); ?> € </span></div>
                            <div class="progress-circle-text" data-cat="investissement"><?php echo __d(sprintf($_domain, 'KPI'), 'synthèse détaillée', true);?><i></i></div>
                        </div>
                    </div>
                    <div class="progress-circle progress-circle-red" style="margin-left: 20px;">
                        <div class="progress-circle-inner">
                            <i class="icon-question" aria-hidden="true"></i>
                             <?php 
                                $per = $real = 0;
                                if(empty($totals['fon']['budget'])){
                                    $totals['fon']['budget'] = 0;
                                }
                                if(empty($totals['fon']['avancement'])){
                                    $totals['fon']['avancement'] = 0;
                                }
                                 if($totals['fon']['budget'] == 0 && $totals['fon']['avancement'] == 0) {
                                    $per = 0;
                                }else if($totals['fon']['budget'] == 0) {
                                    $per = 100;
                                } else {
                                    $real = $per = round($totals['fon']['avancement']/$totals['fon']['budget'] * 100,2);
                                    $per = ($per > 100) ? 100 : $per;
                                }
                            ?>
                            <h3><span><?php echo __d(sprintf($_domain, 'Finance'), 'Fonctionnement', true); ?></span></h3>
                            <canvas data-real = "<?php echo $real; ?>" data-value = "<?php echo $per; ?>" id="myCanvas-2" width="165" height="160" style=""></canvas>
                            <div class ="progress-value progress-validated"><p><?php echo __d(sprintf($_domain, 'KPI'), 'Budget', true);?></p><span><?php echo number_format((!empty($totals['fon']['budget']) ? $totals['fon']['budget'] : 0), 2, '.', ' '); ?> € </span></div>
                            <div class ="progress-value progress-engaged"><p><?php echo __d(sprintf($_domain, 'KPI'), 'Engagé', true);?></p><span><?php echo number_format((!empty($totals['fon']['avancement']) ? $totals['fon']['avancement'] : 0), 2, '.', ' '); ?> € </span></div>
                            <div class="progress-circle-text" data-cat="fonctionnement"><?php echo __d(sprintf($_domain, 'KPI'), 'synthèse détaillée', true);?><i></i></div>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                    <div class="progress-line">
                        <div class="amrs-wrap">
                            <i class="icon-question" aria-hidden="true"></i>
                            <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Progression', true);?></span></h3>
                            <div id ="progress-line" class="progress-line-inner">
                                <div class="progress-label">
                                    <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span><?php echo __('Consumed', true);?></span></div>
                                    <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><?php echo __('Planed', true);?></span></div>
                                </div>
                                
                                <div id='chartContainer' style="width:<?php echo ($countLine * 50); ?>px; height:240px">
                                </div>
                                
                            </div>
                            <span onclick="onPrevous()" class="scroll-progress scroll-left"></span>
                            <span onclick="onNext()" class="scroll-progress scroll-right"></span>
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="wd-right">
               <div class="wd-zog-messages">
                    <?php
                        if(!empty($zogMsgs)){
                            $currentDate = strtotime(date('d-m-Y', time()));
                            $i=0;
                            foreach ($zogMsgs as $key => $zogMsg) {
                                if($zogMsg['employee_id'] == $employee_info['Employee']['id']){
                                    $url = $this->UserFile->avatar($zogMsg['employee_id']);
                                    $avatar = '<img src="'. $url .'"/>';
                                }else{
                                    $name = substr( trim($eZogMsgs[$zogMsg['employee_id']]['first_name']),  0, 1) .''.substr( trim($eZogMsgs[$zogMsg['employee_id']]['last_name']),  0, 1);
                                    $avatar = '<p class="circle-name">'.$name.'</p>';
                                } 
                                if($currentDate == strtotime(date('d-m-Y',  strtotime($zogMsg['created'])))){
                                    if( $i == 0) { ?>
                                        <div class="zog-current">
                                            <div class="zog-current-title"><img src="<?php echo $html->url('/img/new-icon/message.jpg'); ?>"/><h3><?php echo __d(sprintf($_domain, 'KPI'), "Aujourd'hui", true);?></h3></div>
                                        <?php }?>
                                        <div class="zog-comment">
                                            <a class="menu-zog" href="#"><img src="<?php echo $html->url('/img/new-icon/menu.png'); ?>"/></a>
                                            <div class="zog-comment-heading">
                                                <?php echo $avatar; ?>
                                                <div class="zog-comment-heading-right">
                                                    <h3><?php echo $eZogMsgs[$zogMsg['employee_id']]['first_name'] ." ". $eZogMsgs[$zogMsg['employee_id']]['last_name']; ?></h3>
                                                    <p class="zog-comment-date"><?php echo $zogMsg['created']; ?></p>
                                                </div>
                                            </div>
                                            <div class="zog-comment-content"><?php echo $zogMsg['content']; ?></div>
                                            
                                        </div>
                                <?php } 
                                $i++;
                            }
                            if($i != 0) echo "</div>";?>
                            <div class="zog-message">
                            <div class="zog-title"><h3><?php echo __d(sprintf($_domain, 'KPI'), "Hier", true);?></h3></div>
                            <?php 
                            foreach ($zogMsgs as $key => $zogMsg) {
                                if($zogMsg['employee_id'] == $employee_info['Employee']['id']){
                                    $url = $this->UserFile->avatar($zogMsg['employee_id']);
                                    $avatar = '<img src="'. $url .'"/>';
                                }else{
                                    $name = substr( trim($eZogMsgs[$zogMsg['employee_id']]['first_name']),  0, 1) .''.substr( trim($eZogMsgs[$zogMsg['employee_id']]['last_name']),  0, 1);
                                    $avatar = '<p class="circle-name">'.$name.'</p>';
                                } 
                                if($currentDate != strtotime(date('d-m-Y',  strtotime($zogMsg['created'])))){ ?>
                                    <div class="zog-comment">
                                        <a class="menu-zog" href="#"><img src="<?php echo $html->url('/img/new-icon/menu.png'); ?>"/></a>
                                        <div class="zog-comment-heading">
                                            <?php echo $avatar; ?>
                                            <div class="zog-comment-heading-right">
                                                <h3><?php echo $eZogMsgs[$zogMsg['employee_id']]['first_name'] ." ". $eZogMsgs[$zogMsg['employee_id']]['last_name']; ?></h3>
                                                <p class="zog-comment-date"><?php echo $zogMsg['created']; ?></p>
                                            </div>
                                        </div>
                                        <div class="zog-comment-content"><?php echo $zogMsg['content']; ?></div>
                                        
                                    </div>
                                <?php
                                } 
                                
                            }
                            echo "</div>";
                        }
                    ?>
               </div>
            </div>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
<script>
    function draw_progress(myCanvas){
    //var id = id ? id : 'myCanvas';
    var id = myCanvas;
    var canvas = document.getElementById(id);
    var context = canvas.getContext('2d');
    /* Khởi tạo giá trị */
    var al=0; // giá trị khởi đầu
    var start= 21; // Vị trí khởi đầu
    var border = 10; // độ rộng của vòng cung
    var bgr_cl = '#d4d4d4'; // Background color
    var total_steps = 12; // chia vòng tròn làm 12 phần
    var num_steps = 10; // chạy 10 phần bỏ trống 2
    var not_fill = 0.1; // tô màu 0.9 (90%), bỏ trống 0.1 (10%)
    var max = 0; // giá trị dừng 
    if( canvas.getAttribute('data-value') ) max = canvas.getAttribute('data-value');
    if( canvas.getAttribute('data-real') ) real = canvas.getAttribute('data-real');
    var font = '15pt Verdana';
    arr_cl2 = ['#DB414F','#E0636E','#EA848E','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC']; // cái này cho nó trên 50%
    arr_cl1 = ['#75AF7E','#8FBB96','#AECDB3','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC']; // cái này cho dưới 50%
    /* End Khởi tạo giá trị */
    var width = context.canvas.width;
    var height = context.canvas.height;
    var square = Math.min(width,height); // lấy kích thước canvas
    square /= 2; // bán kính = 1/2 canvas
    square -= border/2; // trừ phần border 
    var cw=context.canvas.width/2;
    var ch=context.canvas.height/2;
    var diff; // độ dài vòng cung
    var percent;

    /*
    arc( x,y,R, start_angle, e_angle,bool counterclockwise);
    */
    function progressBar(){
        angle = Math.PI*2/total_steps; // vong cung của 1 góc
        diff=(al/10)*Math.PI*2;
        context.clearRect(0,0,400,200);
        
        // Vẽ vòng cung màu nhạt bên dưới (placeholder)
        for(i = 0; i< num_steps; i++){
          context.beginPath(); // clear vị trí con trỏ graph
          context.lineWidth = border; // độ rộng của vòng cung
          context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
          context.strokeStyle=bgr_cl;
          context.stroke();
        }
        // context.strokeStyle= al <50 ? '#F29D3A' : 'red';
        
        
        // Tô màu viền
        color = max > 90 ? arr_cl2 : arr_cl1;
        //number = parseInt(max/10);
        number = Math.floor(max/10); // làm tròn xuống
        surplus = parseInt(max%10);
        i=0;
        for(i=0; i < number;i++){
            context.beginPath(); // clear vị trí con trỏ graph
            context.lineWidth = border; // độ rộng của vòng cung
            context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
            color_index = number - i - 1; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
        if(surplus){
            context.beginPath(); // clear vị trí con trỏ graph
            context.lineWidth = border; // độ rộng của vòng cung
            surplus_angle = surplus*angle/10;
            context.arc(cw,ch,square,i*angle+start ,i*angle + start + surplus_angle ,false);
            color_index = 0; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
            // End tô màu viền
            
            // vẽ hình tròn bên trong      
            context.beginPath();
            context.arc(cw,ch,square - 2*border,0,Math.PI*2,false);
            context.fillStyle = bgr_cl;
            context.fill();
            
            // vẽ chữ
            context.beginPath();
            context.textAlign='center';
            context.lineWidth = 5;
            context.font = font;
            context.fillStyle = '#fff';
            context.fill();
            context.beginPath();
            context.stroke();
            context.fillText(real+'%',cw+2,ch+6);
            
            // Phần này để đó cho chữ chạy và thanh progres chạy
            /*percent = parseInt(al/10) * 10;
            context.fillText(percent+'%',cw+2,ch+6);
            ///al = al == 99 ? 0 : al;  
            if(al>= max){
                  clearTimeout(bar);
              }
              al++;*/
        }

        //var bar=setInterval(progressBar,50); // cái này cho chạy với delay 50
        var bar = progressBar();
    }
    var prog = draw_progress('myCanvas');
    var prog = draw_progress('myCanvas-2');


</script>
<script type="text/javascript">
(function($){
    // var _pos = $("#carousel .slides li").length;
    // var findActive = $('#carousel .active-item');
    // var startTo = 0;
    // var screenWidth = $(window).width();
    // var num = 3;
    // var itemSlider = 5;
    // if(screenWidth <= 1024) {
    //     num = 2;
    //     itemSlider = 3;
    // }
    // if(findActive.length !=0){
    //     if(_pos > 0){
    //         item_active = findActive.closest('li').data('num');
    //         if(item_active > num){
    //             startTo = item_active - num;
    //         }
    //         if(item_active < num){
    //             for($i= 0 ; $i < (num - item_active); $i++){
    //                 $("#carousel .slides").append("<li></li>");
    //             } 
    //         }
    //     }
    // }else{
    //     $("#carousel").addClass('notActive');
    // }
    // $('#carousel').flexslider({
    //     animation: "slide",
    //     controlNav: false,
    //     animationLoop: false,
    //     slideshow: false,
    //     itemMargin: 5,
    //     itemWidth: 160,
    //     minItems: itemSlider,
    //     maxItems: itemSlider,
    //     move: startTo,
    //     startAt: startTo,
    //     asNavFor: '#slider',
    //     pauseOnHover: true,
    //     prevText: '<i class="icon-arrow-left" aria-hidden="true"></i>',     
    //     nextText: '<i class="icon-arrow-right" aria-hidden="true"></i>',
    // });

    $('.progress-line-inner').on('hover','#svgChart', function(){
        var circle = $(this).find('circle');
        var color = $(this).find('circle').attr('fill');
        if(color){
            $(circle).append('<span style="background-color:'+  color +' "></span>');
        }
       
    });
    
})(jQuery);

$(document).ready(function () {
 
    // prepare jqxChart settings
    var years    = <?php echo json_encode($setYear); ?>,
        manDays    = <?php echo json_encode($manDays); ?>,
        dataSets    = <?php echo !empty($dataSets) ? json_encode($dataSets) : json_encode(array()); ?>;
    var settings = {
        title: "",
        description: years,
        padding: { left: 5, top: 0, right: 5, bottom: 5 },
        titlePadding: { left: 90, top: 20, right: 0, bottom: 20 },
        source: dataSets,
        categoryAxis:
            {
                dataField: 'date',
                description: '',
                showGridLines: false,
            },
        colorScheme: 'scheme02',
        seriesGroups:
            [
                {
                    type: 'spline',
                    showLabels: false,//default
                    valueAxis:
                    {
                        axisSize: 'auto',
                        minValue: 0,
                        maxValue: manDays,
                        unitInterval: manDays,
                        description: '',
                        displayValueAxis: false
                    },
                    series: [
                            // { dataField: 'estimation', displayText: 'Estimation', labelOffset: {x: 0, y: 10}},
                            { dataField: 'consumed', displayText: '<?php echo __('Consumed', true);?>', labelOffset: {x: 0, y: 0}, color: '#538FFA'},
                            { dataField: 'validated', displayText: '<?php echo __('Planed', true);?>', labelOffset: {x: 0, y: 0}, color: '#E44353'}

                        ]
                },
            ]
    };
    function caculate(value){
       value = value.from - value.to;
       return  Math.round(value * 100) / 100 ;

    }
    // setup the chart
    $('#chartContainer').jqxChart(settings);

    /* Edit by Dai Huynh
    * Show progress-circle popup
    */
    $('.progress-circle-text').on('click', function(){
        var _this = $(this);
        var _cat = _this.data('cat');
        _this.addClass('loading');
        var id = <?php echo $project_id; ?>;
        $.ajax({
            url : "/project_finances_preview/ajax/"+id,
            type: "GET",
            cache: false,
            data: {
                cat: _cat
            },
            success: function (html) {
                _this.removeClass('loading');
                 $('#dialogDetailValue').css({'padding-top':0,'padding-bottom':0});
                var wh= $(window).height();
                if (wh < 768) {
                     $('#contentDialog').css({'max-height':600,'width':'auto'});
                } else {
                     $('#contentDialog').css({'max-height':'none','width':'auto'});
                }
                $('.wd-layout').css('overflow','visible');
                showMe();
                $('#contentDialog').html(html);
            }
        });
    });
    $('#GanttChartDIV').on('click', '.milestones-item', function(e){
        //e.preventDefault();
        var _this = $(this);
        $('#GanttChartDIV').find('.milestones-item').removeClass('active-item');
        $('#GanttChartDIV').find('.flag-item').addClass('last-item');
        var out_date = _this.hasClass('out_of_date');
        if( ! _this.hasClass('milestone-green')){
            _this.removeClass('milestone-blue').removeClass('milestone-mi').addClass('milestone-green');
        }else{
            if( out_date){
                _this.removeClass('milestone-blue').removeClass('milestone-green').addClass('milestone-mi');
            }else{
                _this.removeClass('milestone-green').removeClass('milestone-mi').addClass('milestone-blue');
            }
        }
        $(this).removeClass('last-item');
        $(this).addClass('active-item');
        var _item_id = $(this).data('id');
        $.ajax({
           url : '<?php echo $html->url('/project_milestones/change_milestone_status/' . $project["id"]); ?>' + '/' + _item_id,
            type : 'GET',
            data : '',
            success : function(data){
                console.log(data);
                var success = true;
                var check_date = 2;
                if( success){
                    
                }
            },
            error: function(){
                location.reload();
            }
        });
    });
    $("#dialogDetailValue").draggable({
        cancel: "#wd-tab-content",
        stop: function(event, ui){
            var position = $("#dialogDetailValue").position();
            $.ajax({
                url : "/projects/savePopupPosition",
                type: "POST",
                data: {
                    top: position.top,
                    left: position.left
                }
            });
            savePosition = position;
        }
    }); 


});
function onNext() {
    var elmnt = document.getElementById("progress-line");
    elmnt.scrollLeft += 50;
}

function onPrevous() {
    var elmnt = document.getElementById("progress-line");
    elmnt.scrollLeft -= 50;
}
function milestones_slider(){
    var _slider = $('#carousel .slides');
    var active_index = 0, index=0;

    if( _slider.length == 0) return;
    var item = _slider.children('.wd-slider-item').length;
    if( item){
        _slider.children('.wd-slider-item').each(function(){
            if( $(this).find('.milestones-item').hasClass('active-item')) active_index = index;
            index++;
        });
    }
    var slider_show = Math.min(item,5);
    if(item == slider_show){
        active_index = 0;
    }
    var slick_slider = _slider.slick({
        infinite: false,
        slidesToShow: slider_show,
        slidesToScroll: 1,
        speed: 600,
        arrows: true,
        dots: false,
        centerMode: true,
        focusOnSelect: true,
        initialSlide: active_index,
        centerPadding: '0',
        prevArrow: '<button type="button" class="slick-prev"><img src="/img/new-icon/arrow-left.png"></button>',
        nextArrow: '<button type="button" class="slick-next"><img src="/img/new-icon/arrow-right.png"></button>',
        responsive:[
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: Math.max(slider_show-1, 1),
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: Math.max(slider_show-2 , 1),
                }
            },


        ]
    });
    var zindex = 50;
    _slider.find('.slick-slide').each(function(){
        $(this).css('z-index', zindex--);
    });
    _slider.on('afterChange', function(){
        var zindex = 50;
        _slider.find('.slick-slide').each(function(){
            $(this).css('z-index', zindex--);
        });
    });
}
milestones_slider();
</script>