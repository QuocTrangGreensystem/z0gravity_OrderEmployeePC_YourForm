<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">

        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <ul class="wd-item">
                        <li class="wd-current"><a href="<?php echo $html->url('/cities/') ?>"><?php echo __('Employees', true) ?></a></li>
                        <li><a href="<?php echo $html->url('/project_phases/') ?>"><?php __('Projects') ?></a></li>
                    </ul>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <style type="text/css">
                                    #list-companies li{
                                        margin-left: 30px;
                                        list-style: decimal;
                                    }
                                </style>
                                <h2 class="wd-t3"><?php __('Select a company to managements') ?></h2>
                                <ol id="list-companies">
                                    <?php foreach ($companies as $id => $company) : ?>
                                        <li>
                                            <?php echo $this->Html->link($company, array('action' => 'index', $id)); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>