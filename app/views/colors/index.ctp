<?php /*
NOTE
If you want to change color, you sould change in view/elements/style/cystom_css.ctp

*/
?>

<?php echo $html->script('jquery.validation.min'); ?>
<?php echo $html->script('jquery.dataTables'); ?>
<?php echo $html->css('jquery.dataTables'); ?>
<?php echo $html->css('jquery.ui.custom'); ?>
<?php echo $html->script('jscolor'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<?php $employee_info = $this->Session->read("Auth.employee_info"); 
if(!empty($employee_info['Color']['is_new_design']) && $employee_info['Color']['is_new_design'] == 1){
    echo $html->css('preview/color-admin');
} ?>
<style>
   input.jscolor{
        max-width: 125px;
   }
   .wd-tab .form-jscolor .wd-right-content{
        width: inherit; 
        float: none;
   }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <?php echo $this->element("project_top_menu") ?>
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <?php
                App::import("vendor", "str_utility");
                $str_utility = new str_utility();
                ?>
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <?php echo $this->Form->create('Color', array("action" => "update")); ?>
                                <fieldset>
                                    <div class="wd-scroll-form-min form-jscolor">
                                        <div class="wd-content wd-left-content">
                                            <div class="wd-input1">
                                                <input name="data[Color][page_color]" class="jscolor page_color" value="#2362B8">
                                                <label for="last-name"><?php __("Page") ?></label>
                                            </div>

                                            <div class="wd-input1" style="display: none">
                                                <input name="data[Color][color]" class="jscolor color" value="">
                                                <label for="last-name"><?php __("Color") ?></label>
                                            </div>
                                            <div class="wd-input1">
                                                <input name="data[Color][header_color]" class="jscolor header_color" value="#2362B8">
                                                <label for="last-name"><?php __("Header") ?></label>
                                            </div>
                                            <div class="wd-input1">
                                                <input name="data[Color][line_color]" class="jscolor line_color" value="#C1C8C6">
                                                <label for="last-name"><?php __("Header Line") ?></label>
                                            </div>
                                            <div class="wd-input1">
                                                <input name="data[Color][table_color]" class="jscolor table_color" value="#2362B8">
                                                <label for="last-name"><?php __("Header of data table") ?></label>
                                            </div>
                                            <div class="wd-input1">
                                                <input name="data[Color][popup_color]" class="jscolor popup_color" value="#2362B8">
                                                <label for="last-name"><?php __("Header pop-up") ?></label>
                                            </div>
                                        </div>
                                        <div class="wd-content wd-right-content">
                                            <div class="wd-input1">
                                                <input name="data[Color][kpi_color]" class="jscolor kpi_color" value="#2362B8">
                                                <label for="last-name"><?php __("Header KPI - Your form") ?></label>
                                            </div>
                                            <div class="wd-input1">
                                                <input name="data[Color][tab_color]" class="jscolor tab_color" value="#2362B8">
                                                <label for="last-name"><?php __("Tab") ?></label>
                                            </div>
                                            
                                            <div class="wd-input1">
                                                <input name="data[Color][tab_selected]" class="jscolor tab_selected" value="#B1B8B6">
                                                <label for="last-name"><?php __("Tab selected") ?></label>
                                            </div>
                                            
                                            <div class="wd-input1">
                                                <input name="data[Color][tab_hover]" class="jscolor tab_hover" value="#2362B8">
                                                <label for="last-name"><?php __("Tab hover") ?></label>
                                            </div>
                                            
                                            <div class="wd-input1">
                                                <input name="data[Color][button_color]" class="jscolor button_color" value="#2362B8">
                                                <label for="last-name"><?php __("Button") ?></label>
                                            </div>
                                        </div>
                                        <?php echo $this->Form->input('id', array('name' => 'data[Color][id]', 'type' => 'hidden')); ?>
                                    </div>

                                    <div class="wd-submit">
                                        <button type="submit" class="wd-button-f wd-save-project" id="btnSave" />
                                            <span><?php __('Save') ?></span>
                                        </button>
                                        <?php
                                        if (($is_sas == 0) && (($employee_info["Role"]["name"] == "admin") && (!empty($employee_info["Company"]["parent_id"]))))
                                            $reset = 'reset_form_1'; else
                                            $reset = 'reset_form';
                                        ?>
                                        <a href="javascript:void(0)" id="<?php echo $reset ?>" class="wd-reset"><?php __('Reset') ?></a>
                                    </div>

                                </fieldset>

                                <?php echo $this->Form->end(); ?>
                                
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script>
    $("#btnSave").click(function(){
        $("#flashMessage").hide();
    });
    $(document).ready(function(){
        var color = <?php echo json_encode($colors['Color']['color']);?>,
        kpi_color = <?php echo json_encode($colors['Color']['kpi_color']);?>,
        popup_color = <?php echo json_encode($colors['Color']['popup_color']);?>,
        table_color = <?php echo json_encode($colors['Color']['table_color']);?>,
        line_color = <?php echo json_encode($colors['Color']['line_color']);?>,
        header_color = <?php echo json_encode($colors['Color']['header_color']);?>,
        tab_selected = <?php echo json_encode($colors['Color']['tab_selected']);?>,
        tab_color = <?php echo json_encode($colors['Color']['tab_color']);?>;
        page_color = <?php echo json_encode($colors['Color']['page_color']);?>;
        button_color = <?php echo json_encode($colors['Color']['button_color']);?>;
        tab_hover = <?php echo json_encode($colors['Color']['tab_hover']);?>;

        if(!header_color) header_color = '#2362B8'
        if(!kpi_color) kpi_color = '#2362B8'
        if(!popup_color) popup_color = '#2362B8'
        if(!table_color) table_color = '#2362B8'
        if(!line_color) line_color = '#C1C8C6'
        if(!tab_selected) tab_selected = '#B1B8B6'
        if(!tab_color) tab_color = '#2362B8'
        if(!page_color) page_color = '#0B4578'
        if(!button_color) button_color = '#2362B8'
        if(!tab_hover) tab_hover = '#2362B8'

        $(".color").val(color);
        $(".header_color").val(header_color);
        $(".line_color").val(line_color);
        $(".table_color").val(table_color);
        $(".popup_color").val(popup_color);
        $(".kpi_color").val(kpi_color);
        $(".tab_selected").val(tab_selected);
        $(".tab_color").val(tab_color);
        $(".page_color").val(page_color);
        $(".button_color").val(button_color);
        $(".tab_hover").val(tab_hover);
        
        $("#reset_form").click(function(){
            header_color = '2362B8';
            kpi_color = '2362B8';
            popup_color = '2362B8';
            table_color = '2362B8';
            line_color = 'C1C8C6';
            tab_selected = 'B1B8B6';
            tab_color = '2362B8';
            page_color = '2362B8';
            button_color = '2362B8';
            tab_hover = '2362B8';
                
            $(".color").val(color);
            $(".header_color").val(header_color);
            $(".line_color").val(line_color);
            $(".table_color").val(table_color);
            $(".popup_color").val(popup_color);
            $(".kpi_color").val(kpi_color);
            $(".tab_selected").val(tab_selected);
            $(".tab_color").val(tab_color);
            $(".page_color").val(page_color);
            $(".button_color").val(button_color);
            $(".tab_hover").val(tab_hover);

            $( "#btnSave" ).trigger( "click" );
        });
    });
</script>
