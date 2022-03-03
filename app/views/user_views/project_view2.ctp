<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('history_filter'); ?>
<script type="text/javascript">
    HistoryFilter.here =  '<?php echo $this->params['url']['url'] ?>';
    HistoryFilter.url =  '<?php echo $this->Html->url(array('controller' => 'employees', 'action' => 'history_filter')); ?>';
</script>
<style>
    table.display thead th {
        color: #FFFFFF;
        cursor: pointer;
        font-size: 13px;
        font-weight: normal;
        line-height: 16px;
        text-align: center;
    }
    .ui-state-default .ui-icon {
        float: right;
        margin-top: 0;
    }

</style>
<div id="wd-container-main" class="wd-project-index">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-title">
                    <h2 class="wd-t1"><?php echo __("Project List", true); ?></h2>
                    <a href="javascript:void(0);" id="add_project" class="wd-add-project"><span><?php __('Add Project') ?></span></a>
                    <a href="<?php echo $html->url("/user_views/exportProjectViewToExcel/" . $view_id) ?>" class="wd-add-project" style="margin-right:5px; "><span><?php __('Export Excel') ?></span></a>	
                </div>
                <?php echo $this->Session->flash(); ?>
                <div class="wd-table" id="project_view_container" style="width: 100%; height: 400px">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('dialog_projects') ?>
<script>
    $(function(){
    
        /* table*/
        
        /* table .end */
    
        $('#plannedenddate,#startdate,#enddate,#projectamrmepdateamr').datepicker({
            showOn          : 'button',
            buttonImage     : '<?php echo $html->url("/img/front/calendar.gif") ?>',
            buttonImageOnly : true,
            dateFormat      : 'dd-mm-yy'
        });
    
    });
    
</script>