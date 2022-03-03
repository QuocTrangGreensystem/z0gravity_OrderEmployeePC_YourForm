// get Id of link current
var href_currents = window.location.pathname;
href_currents = href_currents.split("/");
var idActivity = href_currents[3];

Ext.define('PMS.model.ActivityTask', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'task_title',      type: 'string'},
        {name: 'task_priority_id'},
        {name: 'task_priority_text'},
        {name: 'task_status_id'},
        {name: 'task_status_text'},
        {name: 'task_assign_to_id'},
        {name: 'is_profit_center'},
        {name: 'estimated_detail'},
        {name: 'task_assign_to_text',  type: 'string'},
        {name: 'slider', defaultValue: 0},
        {name: 'task_start_date',     type: 'date', dateFormat: 'Y-m-d'},
        {name: 'task_end_date',       type: 'date', dateFormat: 'Y-m-d'},
        {name: 'initial_task_start_date',     type: 'date', dateFormat: 'Y-m-d'},
        {name: 'initial_task_end_date',       type: 'date', dateFormat: 'Y-m-d'},
        {name: 'initial_estimated'},
        {name: 'predecessor'},
        {name: 'duration'},
        {name: 'estimated', defaultValue: 0},
        {name: 'overload', defaultValue: 0},
        {name: 'manual_overload', defaultValue: 0},
        {name: 'manual_consumed', defaultValue: 0},
        {name: 'consumed', defaultValue: 0},
        {name: 'hasUsed', defaultValue: 0},
        {name: 'wait', defaultValue: 0},
        {name: 'completed', defaultValue: 0},
        {name: 'remain', defaultValue: 0},
        {name: 'parent_id'},
        {name: 'parent_name'},
        {name: 'activity_id'},
        {name: 'is_predecessor'},
        {name: 'phase_id'},
        {name: 'is_nct'},
        {name: 'is_phase'}, //is activities
        {name: 'is_previous'},
        {name: 'profile_id'},
        {name: 'profile_text', type: 'string'},
		{name: 'weight', defaultValue: ''},  //MODIFY BY VINGUYEN 31/05/2014
		{name: 'special', defaultValue: 0},  //MODIFY BY VINGUYEN 25/09/2014
        {name: 'amount', defaultValue: 0},
        {name: 'progress_order', defaultValue: 0},
        {name: 'progress_order_amount', defaultValue: 0},
        {name: 'text_1'},
        {name: 'attachment'},
        {name: 'text_updater'},
        {name: 'text_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'keep_duration'}
    ],

    proxy:{
		type        : 'rest',
		api         : {
			create  : '/activity_tasks/createTaskJson/' + idActivity,
			read    : '/activity_tasks/listTasksJson/' + idActivity,
			update  : '/activity_tasks/updateTaskJson/' + idActivity,
			destroy : '/activity_tasks/destroyTaskJson/' + idActivity
		},
		reader      : {
			type            : 'json',
			messageProperty : 'message'
		},
		writer		: {
			writeAllFields: true
		}
	}
});