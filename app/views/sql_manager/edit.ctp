
    <div>
        <?php echo $this->Form->create('SqlManager', array('type' => 'POST', 'id' => 'form_edit_sql', 'url' => array('controller' => 'sql_manager', 'action' => 'edit'))); ?>

        <div class="wd-input">
            <label for="request_name"><?php __("Request Name") ?></label>
            <?php echo $this->Form->input('request_name', array('div' => false, 'label' => false, 'id' => 'sqlName', 'class' => '', 'style' => 'color: #000','required'=>'')); ?>
        </div>
        <div class="wd-input">
            <label for="desc"><?php __("Description") ?></label>
            <?php echo $this->Form->input('desc', array('div' => false, 'label' => false, 'id' => 'sqlDesc' ,'class' => '', 'style' => 'color: #000','required'=>'')); ?>
        </div>
        <div class="wd-input">
            <label for="request_type_edit"><?php __("Type") ?></label>
            <?php
            echo $this->Form->input('type', array(
                'type' => 'select',
                'id' => 'request_type_edit',
                'div' => false,
                'label' => false,
                'rel' => 'no-history',
                'required' => true,
                'style' => 'width: 300px !important',
                'options' => $typelist,
            ));
            ?>
        </div>
		<div class="wd-input"  style="overflow: visible">
            <label for=""><?php __("Company") ?></label>
            <?php
                echo $this->Form->input('company', array(
                'type' => 'select',
//                'name' => 'company',
                'id' => 'editcompanyId',
                'div' => false,
                'label' => false,
                'multiple' => true,
                'hiddenField' => false,
                'rel' => 'no-history',
                "empty" => false,
                'style' => 'width: 300px !important',
                "options" => $companylist,
                ));
            ?>
        </div>


        <div class="wd-input" id="" style="overflow: visible">
            <label for=""><?php __("Resource") ?></label>
            <?php
            echo $this->Form->input('SqlManagerEmployee.resource', array('div' => false, 'label' => false,
                "empty" => false,
//                'name' => 'resource',
                'id' => 'editresourceId',
                'multiple' => true,
                'type' =>'select',
                'hiddenField' => false,
                'style' => 'width: 300px !important',
                "options" => '',
          
                ));
            ?>
        </div>
        <div class="wd-input" id="" style="overflow: visible">
        <?php echo $this->Form->input('request_sql', array('type' => 'textarea','div' => false, 'label' => false, 'id' => 'editcode', 'rows' => '10',"cols"=>'60')); ?>   

        </div>
        <?php  echo $this->Form->input('id', array('type' => 'hidden')); ?>
        <?php  echo $this->Form->input('create_by', array('type' => 'hidden','default'=>$employee_info['Employee']['id'])); ?>
        
        <?php echo $this->Form->end(); ?>
    </div>
    <div style="clear: both;"></div>
    <div class="wd-submit">
		<a href="javascript:void(0)" class="btn-form-action btn-cancel cancel"><?php __("Cancel") ?></a>
		<a href="javascript:void(0)" class="btn-form-action btn-right btn-ok" id="edit_save"><?php __('Save') ?></a>
		<a href="javascript:void(0)" class="btn-form-action btn-right" id="excute-edit"><?php __('Excute') ?></a>
	</div>

<script>
     var editor2 = CodeMirror.fromTextArea(document.getElementById("editcode"), {
        lineNumbers: true,
        autoRefresh :true,
        value : 'SELECT',
        mode: "text/x-mysql",
		lineWrapping: true,
    }); 
		
    var companySelected = <?php echo json_encode($companySelected); ?>;
    var resourceSelected = <?php echo json_encode($resourceSelected); ?>;
    $('#editcompanyId').multipleSelect({
            onClick: function(view){
                    getResource();
                }
        });
    $('#editresourceId').multipleSelect();
    function getResource(){
        //get result from company
        var companySelect = $("#editcompanyId").multipleSelect("getSelects");
        $.ajax({
            method: "POST",
            url: "/sql_manager/getresource",
                data: { companySelect: companySelect }
        })
        .done(function( result ) {

            $('#editresourceId').html(result);
            $('#editresourceId').multipleSelect();
            $("#editresourceId").multipleSelect("setSelects", resourceSelected);
        });
        
    }
    
     $("#edit_save").click(function(event){
		 console.log('edit_save');
        name = $("#sqlName").val();
        desc = $("#sqlDesc").val();  
        company = $("#editcompanyId").val(); 
        employee = $("#editresourceId").val(); 
        $("form#form_edit_sql input[type=text]").each(function(){
            $(this).removeClass('form-error');
            if($(this).val()==""||$(this).val()==null){
                $(this).addClass('form-error');
            }
       });
        if(name!=""&&desc!=""&&company!=null&&employee!=null){
            
        $("#form_edit_sql").submit();}
        else{
            event.preventDefault();
            return false;
        }

    });
    $("#excute-edit").click(function(event){
        var requireSql = editor2.getValue();
		var request_type = $('#request_type_edit').val();
		if( request_type == 'sql' ){
			postExcute('requireSql',requireSql);
		}else if( request_type == 'iframe'){
			postExcute('viewIframeText',requireSql);
		}else if( request_type == 'link'){
			window.open(requireSql);
		}

    });
    $(function (){
    //set select company and resouce
    $("#editcompanyId").multipleSelect("setSelects", companySelected);
    getResource();
   
    })
</script>    