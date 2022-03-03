<?php
    echo $this->Html->css(array(
        'slick_grid/slick.grid.activity',
        'jquery.multiSelect',
        'slick_grid/slick.grid_v2',
        'slick_grid/slick.pager',
        'slick_grid/slick.common_v2',
        'slick_grid/slick.edit',
		'preview/tab-admin',
		'layout_admin_2019'
    ));
    echo $this->Html->script(array(
        'slick_grid/slick.core',
        'slick_grid/slick.dataview',
        'slick_grid/controls/slick.pager',
        'slick_grid/slick.formatters',
        'slick_grid/plugins/slick.cellrangedecorator',
        'slick_grid/plugins/slick.cellrangeselector',
        'slick_grid/plugins/slick.cellselectionmodel',
        'slick_grid/slick.editors',
        'slick_grid_custom',
        'slick_grid/lib/jquery-ui-1.8.16.custom.min',
        'slick_grid/lib/jquery.event.drop-2.0.min',
        'slick_grid/plugins/slick.rowselectionmodel',
        'slick_grid/plugins/slick.rowmovemanager',
        'slick_grid/lib/jquery.event.drag-2.2',
        'slick_grid/slick.grid.activity',
    ));
?>
<style>
    .slick-cell .multiSelect {width: auto; display: block;overflow: hidden; text-overflow: ellipsis;}
    .wd-tab .wd-aside-left{width: 300px !important;}
    .slick-cell-move-handler {
        cursor: move;
    }
    .slick-cell-move-handler:empty {
        cursor: default;
    }
    p {
        margin-bottom: 10px;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div class="wd-tab">
                    <?php echo $this->element("admin_sub_top_menu");?>
                    <div class="wd-panel">
                        <div class="wd-section" id="wd-fragment-1">
                            <?php echo $this->element('administrator_left_menu') ?>
                            <div class="wd-content">
                                <div id="message-place">
                                    <?php
                                    App::import("vendor", "str_utility");
                                    $str_utility = new str_utility();
                                    echo $this->Session->flash();
                                    ?>
                                </div>
                                <div class="wd-table" id="project_container" style="width:100%;height:400px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
function jsonParseOptions($options, $safeKeys = array()) {
    $output = array();
    $safeKeys = array_flip($safeKeys);
    foreach ($options as $option) {
        $out = array();
        foreach ($option as $key => $value) {
            if (!is_int($value) && !isset($safeKeys[$key])) {
                $value = json_encode($value);
            }
            $out[] = $key . ':' . $value;
        }
        $output[] = implode(', ', $out);
    }
    return '[{' . implode('},{ ', $output) . '}]';
}

$columns = array(
    array(
        'id' => 'no.',
        'field' => 'no.',
        'name' => '#',
        'width' => 40,
        'sortable' => false,
        'resizable' => false,
        'noFilter' => 1,
        'behavior' => 'selectAndMove',
        // 'cssClass' => 'slick-cell-move-handler'
    ),
    array(
        'id' => 'original_text',
        'field' => 'original_text',
        'name' => __('Original text', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
    ),
    array(
        'id' => 'english',
        'field' => 'english',
        'name' => __('English', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
    ),
    array(
        'id' => 'france',
        'field' => 'france',
        'name' => __('French', true),
        'width' => 150,
        'sortable' => false,
        'resizable' => true,
        'editor' => 'Slick.Editors.textBox',
    ),
);
$i = 1;
$dataView = array();
$selectMaps = array();

if(!empty($fields)){
    foreach ($fields as $_field) {
        $field = $_field['ExpectationTranslation'];
        $data = array(
            'id' => $field['id'],
            'company_id' => $company_id,
            'no.' => $i++,
            'MetaData' => array()
        );
        $data['original_text'] = (string) $field['original_text'];
        $data['english'] = (string) $field['eng'];
        $data['france'] = (string) $field['fre'];
        $data['action.'] = '';
        $dataView[] = $data;
    }
}
$i18n = array(
    '-- Any --' => __('-- Any --', true),
    'This information is not blank!' => __('This information is not blank!', true),
    'The code is avaiable, please enter another!' => __('The code is avaiable, please enter another!', true),
    'Clear' => __('Clear', true)
);
?>
<script type="text/javascript">
    var DataValidator = {};
    (function($){

        $(function(){
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

            var $this = SlickGridCustom;
            $this.i18n = <?php echo json_encode($i18n); ?>;
            $this.canModified =  true;
            // For validate date

            $this.onCellChange = function(args){
                return true;
            };
            $.extend(Slick.Formatters,{
            });
            $this.onBeforeEdit = function(args){
                return true;
            }
            var  data = <?php echo json_encode($dataView); ?>;
            var  columns = <?php echo jsonParseOptions($columns, array('editor', 'formatter', 'validator')); ?>;
            $this.selectMaps = <?php echo json_encode($selectMaps); ?>;
            $this.fields = {
                id : {defaulValue : 0},
                company_id : {defaulValue : '<?php echo $company_id; ?>', allowEmpty : false},
                english : {defaulValue : '', maxLength: 125},
                france : {defaulValue : '', maxLength: 125},
            };
            $this.url =  '<?php echo $html->url(array('action' => 'update')); ?>';
            var dataGrid = $this.init($('#project_container'),data,columns , {
                enableAddRow : false,
                showHeaderRow : false,
                frozenColumn: 1
            });

        });

    })(jQuery);
</script>
