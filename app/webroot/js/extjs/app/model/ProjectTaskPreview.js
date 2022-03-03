// get Id of link current
var href_currents = window.location.pathname;
href_currents = href_currents.split("/");
//fucntion ajax check data
function checkData(href) {
    var result = '';
    $.ajax({
      url: href,
      async: false,
      success:function(data) {
         result = data;
      }
   });
   return result;
}

var idProject = idActivity = 0;
if(href_currents[1] == 'activity_tasks'){
    var href_check_pms = '/activity_tasks/get_pms_activity/'+href_currents[3];
    var check = checkData(href_check_pms);
    if(check == 1){
        var href_get_project_id = '/activity_tasks/get_id_project/'+href_currents[3];
        idProject = checkData(href_get_project_id);
    } else {
        idActivity = href_currents[3];
    }
} else {
    idProject = href_currents[3];
}

Ext.define('PMS.model.ProjectTaskPreview', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'task_title',      type: 'string'},
        {name: 'project_planed_phase_id'},
        {name: 'project_planed_phase_text',     type: 'string'},
        {name: 'task_priority_id'},
        {name: 'task_priority_text'},
        {name: 'task_status_id'},
        {name: 'task_status_text'},
        {name: 'task_assign_to_id'},
        {name: 'is_profit_center'},
        {name: 'capacity_on'},
        {name: 'capacity_off'},
        {name: 'capacity'},
        {name: 'listProfile'},
        {name: 'estimated_detail'},
        {name: 'task_assign_to_text',  type: 'string'},
        {name: 'slider', defaultValue: 0},
        {name: 'task_start_date',     type: 'date', dateFormat: 'Y-m-d'},
        {name: 'task_end_date',       type: 'date', dateFormat: 'Y-m-d'},
        {name: 'initial_task_start_date',     type: 'date', dateFormat: 'Y-m-d'},
        {name: 'initial_task_end_date',       type: 'date', dateFormat: 'Y-m-d'},
        {name: 'estimated', defaultValue: 0},
        {name: 'initial_estimated', defaultValue: 0},
        {name: 'overload', defaultValue: 0},
        {name: 'consumed', defaultValue: 0},
        {name: 'manual_consumed', defaultValue: 0},
        {name: 'manual_overload', defaultValue: 0},
        {name: 'hasUsed', defaultValue: 0},
        {name: 'wait', defaultValue: 0},
        {name: 'completed', defaultValue: 0},
        {name: 'remain', defaultValue: 0},
        {name: 'eac', defaultValue: 0},
        {name: 'parent_id'},
        {name: 'parent_name'},
        {name: 'project_id'},
        {name: 'phase_id'},
        {name: 'has_children'},
        {name: 'is_nct'},
        {name: 'is_phase'},
        {name: 'is_part'},
        {name: 'is_activity'},
        {name: 'is_predecessor'},
        {name: 'id_activity'},
        {name: 'duration'},
        {name: 'predecessor'},
        {name: 'profile_id'},
        {name: 'profile_text', type: 'string'},
        {name: 'expanded', type: 'boolean', defaultValue: true, persist: false },
        {name: 'weight', defaultValue: ''},  //MODIFY BY VINGUYEN 31/05/2014
        {name: 'special', defaultValue: 0},  //MODIFY BY VINGUYEN 25/09/2014
        {name: 'amount', defaultValue: 0},
        {name: 'progress_order', defaultValue: 0},
        {name: 'progress_order_amount', defaultValue: 0},
        {name: 'text_1'},
        {name: 'attachment'},
        {name: 'text_updater'},
        {name: 'text_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'keep_duration'},
        {name: 'unit_price', defaultValue: 0},
        {name: 'milestone_id'},
        {name: 'milestone_text'},
    ],
    
    proxy:{
        type        : 'rest',
        api         : {
            create  : '/project_tasks/createTaskJson/' + idProject,
            read    : '/project_tasks/listTasksJson/' + idProject,
            update  : '/project_tasks/updateTaskJson/' + idProject,
            destroy : '/project_tasks/destroyTaskJson/'
        },
        reader      : {
            type            : 'json',
            messageProperty : 'message'
        },
        writer      : {
            writeAllFields: true
        }
    }
});
