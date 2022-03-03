Ext.define('PMS.controller.ProjectTasks', {
    extend: 'Ext.app.Controller',
    models: ['ProjectTask'],
    stores: ['ProjectTasks'],
    views: [
        'TreePanel',
        'ContextMenu'
    ],
    refs: [
        {
            ref: 'pmstreepanel',
            selector: 'pmstreepanel'
        },
        {
            ref: 'contextMenu',
            selector: 'tasksContextMenu',
            xtype: 'tasksContextMenu',
            autoCreate: true
        }
    ],
    editingTasks:{},
    developerMode: false,

    init: function() {
        var me = this,
        tasksStore = me.getProjectTasksStore();
        me.control({
            '[iconCls=tasks-new-list add]': {
                click: me.handleAddTask
            },
            '[iconCls=tasks-delete-list delete]': {
                click: me.handleDeleteClick
            },
            '[iconCls=tasks-special-list add]': {
                click: me.handleAddSpecial
            },
            'pmstreepanel': {
                edit: me.updateTask,
                canceledit: me.handleCancelEdit,
                // deleteclick: me.handleDeleteIconClick,
                // selectionchange: me.filterTaskGrid,
                tasksdragdrop: me.taskDrop,
                //taskdrop: me.updateTaskList,
                // listdrop: me.reorderList,
                // itemmouseenter: me.showActions,
                // itemmouseleave: me.hideActions,
                itemcontextmenu: me.showContextMenu,
                cellclick: function(t, td, cellIndex, record, tr, rowIndex, e, eOpts){
                    if( !canModify )return false;
                    if( Azuree.isTouch ){
                        //click on taskname on mobile
                        if( t.grid.columns[cellIndex].dataIndex == 'task_title' ){
                            me.showContextMenu(null, record, null, null, e);
                        }
                        //click on workload on mobile
                        else if( t.grid.columns[cellIndex].dataIndex == 'estimated' ){
                            t.grid.checkEstimated(record.data, record);
                        }
                    }
                }
            },
            '[iconCls=project-tasks-add]': {
                //click:me.handleAddTask
            },
            '[iconCls=estimated-detail-save x-save]': {
                click: me.handleEstimated
            },
            '[iconCls=tasks-new-list cancel]': {
                click: me.handleCancel
            },
            '[id=edit-task]': {
                click: me.editTask
            },
            '[id=edit-task-name]': {
                click: me.editTaskName
            }
        });
        tasksStore.on('remove', me.handleRemoveTask, me);
        tasksStore.on('datachanged', me.handleDatachanged, me);
        tasksStore.on('write', me.syncTasksStores, me);
        tasksStore.on('load', me.handleLoad, me);
        tasksStore.load();
    },
    editTask: function(){
        var tree = this.getPmstreepanel(),
            model = tree.getSelectionModel(),
            task = model.getSelection()[0];
        tree.cellEditingPlugin.startEdit(task);
    },
    handleLoad: function(a, b, c, d, e){
        var me = this,
        pmstreepanel = me.getPmstreepanel(),
        store = me.getProjectTasksStore();
        store.root.data.task_title = "All Task";
        pmstreepanel.afterLoad();
        pmstreepanel.doAll();
    },
    handleWrite: function(a, b, c, d, e){
    },
    handleDatachanged: function(store, eOpts){
    },
    handleRemoveTask: function(parentNode, currentNode, destroy){
        var me = this,
        pmstreepanel = me.getPmstreepanel(),
        store = me.getProjectTasksStore();
    },
    // Controls' section
    taskDrop: function(){
    },
    syncTasksStores: function(listsStore, operation) {
        var me = this,
            stores = [
                me.getProjectTasksStore()
            ],
            taskToSync;
        Ext.each(operation.getRecords(), function(task) {
            Ext.each(stores, function(store) {
                if(store) {
                    taskToSync = store.getNodeById(task.getId());
                    switch(operation.action) {
                        case 'create':
                            break;
                        case 'update':
                            if(taskToSync) {
                                taskToSync.set(task.data);
                                taskToSync.commit();
                            }
                            break;
                        case 'destroy':
                            if(taskToSync) {
                                taskToSync.remove(false);
                            }
                    }
                }
            });
        });
    },
    // Tree Panel
    updateTask: function(editor, e){
            var me      = this,
            task    = e.record,
            tasksTree = me.getPmstreepanel();
        var hasEstimationChange = false;
        // Before save task, must make sure that estimate is following the logic.
        // if(me.developerMode){
        //     var estimated_detail    = task.get("estimated_detail");
        //     var is_profit_center    = task.get("is_profit_center");
        //     var task_assign_to_id   = task.get("task_assign_to_id");
        // }
        if( !task.get('is_new') ){
            tasksTree.buildWorkload(task);
        }
        var pcs = task.get("is_profit_center"),
            ids = task.get("task_assign_to_id"),
            getDuration = task.get("duration"),
            getName = task.get("task_title"),
            callKeepDuration = task.get('callKeepDuration');

        //END
        if(task.raw.is_new){
            // Create task
            if(task.raw.is_new == true){
                if(task.get('parent_id') > 999999999999){
                    var phase_id = task.parentNode.get('phase_id');
                    task.set('project_planed_phase_id', phase_id);
                    task.set('parent_id', 0);
                    task.set('parentId', 0);
                } else if(task.get('parent_id') < 999999999999 && task.get('parent_id') > 0) {
                    // do not save this task
                    var phase_id = task.parentNode.get('project_planed_phase_id');
                    task.set('project_planed_phase_id', phase_id);

                } else if(task.get('parent_id') == 0) {
                    var phase_id = task.get('project_planed_phase_id');
                    task.set('project_planed_phase_id', phase_id);
                } else {
                    //do nothing
                }
            }else{
                // do nothing
            }
        }else{
            // Update task
            // If parent_id > 999.999.999.999, this is a task within a phase. Do set parent id for this task to 0
            // Other wise, this is a sub task, leave it as normal
            var phase_id = task.parentNode.get('phase_id');
            if(task.get('parent_id') > 999999999999){
                task.set('parent_id', 0);
                task.set('parentId', 0);
            }else{
                // do not save this task
            }
        }
        if(task.get('special')==1&&(parseFloat(task.get('consumed'))>parseFloat(task.get('estimated'))))
        {
            Ext.MessageBox.show({
                title: 'ERROR',
                msg: 'Update task Failed!',
                icon: Ext.Msg.ERROR,
                buttons: Ext.Msg.OK
            });
            task.set('consumed', task.raw.consumed);
            return false;
        }
        // if(callKeepDuration && getDuration && getDuration != 0){
            // Ext.Msg.show({
                // title: '',
                // msg: i18n('Keep the duration of the task ') + getName,
                // buttons: Ext.Msg.YESNO,
                // fn: function(response) {
                    // if(response === 'yes') {
                        // task.set('keep_duration', 1);
                    // } else {
                        // task.set('keep_duration', 0);
                    // }
                    // me.handleSaveTask(me, task, tasksTree);
                // }
            // });
        // } else {
            task.set('keep_duration', 0);
            me.handleSaveTask(me, task, tasksTree);
        // }
    },
    handleSaveTask: function(me, task, tasksTree){
        me.getPmstreepanel().setLoading(i18n("Please wait"));
        task.save({
            scope: me,
            success: function(returnTask, operation) {
                var _dataJSON = JSON.parse(operation._response.responseText);
                var _json_task_return = _dataJSON.message.ProjectTask;
                if( !_dataJSON.message ){
                    location.reload();return;
                }
                if(_json_task_return.is_linked && _json_task_return.is_linked != 0){
                    Ext.Msg.show({
                        title: '',
                        width: 900,
                        msg: Ext.String.format(i18n('{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?'), _json_task_return.task_title),
                        buttons: Ext.Msg.YESNO,
                        fn: function(response) {
                            if(response === 'yes') {
                                me.handleLinked(_json_task_return.id, _json_task_return.task_end_date, _json_task_return.increase_endDate, true, _json_task_return, task);
                            } else {
                                me.handleLinked(_json_task_return.id, _json_task_return.task_end_date, _json_task_return.increase_endDate, false, _json_task_return, task);
                            }
                        }
                    });
                } else {
                    if(_json_task_return.task_assign_to_id.length > 1 && _json_task_return.estimated > 0){
                        tasksTree.checkEstimated(_json_task_return, task, 1);
                        me.getPmstreepanel().refreshSummaryNew(_json_task_return, function(callback){
                            me.getPmstreepanel().setLoading(i18n("Please wait"));
                        });
                    } else {
                        me.getPmstreepanel().refreshSummaryNew(_json_task_return, function(callback){
                            me.getPmstreepanel().setLoading(false);
                        });
                    }
                }
				$('.x-grid-cell').removeClass('x-grid-dirty-cell');
                me.getPmstreepanel().refreshStaffing(function(callback){
                    //do nothing
                });
            },
            
            failure: function(returnTask, operation) {
                var error = operation.getError(),
                msg = Ext.isObject(error) ? error.status + ' ' + error.statusText : error;
                returnTask.set('callKeepDuration', false);
                Ext.MessageBox.show({
                    title: '',
                    msg: i18n(error ? error : 'Update task Failed'),
                    icon: Ext.Msg.ERROR,
                    buttons: Ext.Msg.OK
                });
                if(typeof task.data.id == 'undefined'){
                    // destroy the tree node on the server
                    var arrTmp=new Array();
                    $.each(task.parentNode.data.children,function(key,value){
                        if(task.parentNode.data.children[key].id!=task.data.id)
                        {
                            arrTmp.push(task.parentNode.data.children[key]);
                        }
                    });
                    task.parentNode.data.children=arrTmp;
                    task.parentNode.removeChild(task, true);
                }
                me.getPmstreepanel().cellEditingPlugin.startEdit(task, 0);
                me.getPmstreepanel().setLoading(false);
            }
        });
    },
    handleCancelEdit: function(e, context, opt){
        var panel = this.getPmstreepanel(),
            task = context.record,
            parent = task.parentNode;
        if( task.get('is_new') ){
            var arrTmp = [];
            $.each(parent.data.children, function(key, value){
                if( parent.data.children[key].id != task.get('id') ){
                    arrTmp.push(parent.data.children[key]);
                }
            });
            parent.data.children = arrTmp;
            //parent.set('leaf', arrTmp.length ? false : true);
            parent.removeChild(task, true);
        } else {
            //RESET ASSIGN TO
            var ids = task.get('task_assign_to_id'),
                pc = task.get('is_profit_center');
            task.set('task_assign_to_text', panel.getAssignToText(ids, pc));
        }
        panel.applyFilters();
		// panel.getStore().rejectChanges();
    },
    // Button Add Task
    handleAddTask:function(){
        var me = this;
        var tasksTree           = this.getPmstreepanel(),
            store = tasksTree.getStore(),
            cellEditingPlugin   = tasksTree.cellEditingPlugin, // for double click
            selectionModel      = tasksTree.getSelectionModel(),
            selectedTask        = selectionModel.getSelection()[0],
            parentTask          = selectedTask;

        if(selectedTask){
            parentTask = selectedTask;

            // Check for ability to create sub task for current selected
            // - A Phase
            // - A Task with consume == 0
            if((selectedTask.get('is_phase') == "true") || (selectedTask.get('consumed') == 0) || (selectedTask.parentNode.get('is_phase') == "true")){
                // Continue add task
            }else{
                // Return right away
                return;
            }
        }else{
            selectionModel.select(0);
            parentTask = selectionModel.getSelection()[0];
        }
        var childNull=[];
        var today = new Date();
        var date = parentTask.raw.task_start_date ? parentTask.raw.task_start_date : today;
		var status_default = (listAllStatus) ? listAllStatus[0] : 0;
        if(parentTask.get('is_phase')=="true"){
            var newTask = Ext.create('PMS.model.ProjectTask', {
                task_title  : i18n('New Task'),
                loaded      : true,
                leaf        : false,
                expanded    : true,
                children    : childNull,
                parent_id   : parseInt(parentTask.get('id')),
                parent_name : parentTask.get('task_title'),
                project_id  : tasksTree.project_id,
                is_new      : true,
                special     : 0,
                task_start_date: date,
				task_status_id: status_default['id'],
				task_status_text: status_default['name'],
            });
        } else {
                var newTask = Ext.create('PMS.model.ProjectTask', {
                task_title  : i18n('New Task'),
                loaded      : true,
                leaf        : true,
                expanded    : true,
                children    : childNull,
                parent_id   : parseInt(parentTask.get('id')),
                parent_name : parentTask.get('task_title'),
                project_id  : tasksTree.project_id,
                is_new      : true,
                special     : 0,
                task_start_date: date,
				task_status_id: status_default['id'],
				task_status_text: status_default['name'],
            });
        }
        //temporary clear filter
        tasksTree.clearFilter();
        var expandAndEdit = function() {
            if(parentTask.isExpanded()) {
                selectionModel.select(newTask);
                cellEditingPlugin.startEdit(newTask, 0);
            } else {
                tasksTree.on('afteritemexpand', function startEdit(task) {
                    if(task === parentTask) {
                        selectionModel.select(newTask);
                        cellEditingPlugin.startEdit(newTask, 0);
                        tasksTree.un('afteritemexpand', startEdit);
                    }
                });
                parentTask.expand();
            }
        };
        parentTask.set('leaf', false);
        parentTask.appendChild(newTask);
        //MODIFY BY VINGUYEN 06/06/2014
        if(!parentTask.data.children||parentTask.data.children=='null')
        parentTask.data.children=[];
        parentTask.data.children.push(newTask.data);
        //END
		flag_new_task_id = newTask.data.id;
        if(tasksTree.getView().isVisible(true)) {
            expandAndEdit();
        } else {
            tasksTree.on('expand', function onExpand() {
                expandAndEdit();
                tasksTree.un('expand', onExpand);
            });
            tasksTree.expand();
        }
    },
    deleteTask: function(task){
        var me = this,
        taskTree = me.getPmstreepanel(),
        taskName = task.get('task_title'),
        selModel = taskTree.getSelectionModel(),
        tasksStore = me.getProjectTasksStore();
        if( task.get('is_new') ){
            var arrTmp=new Array();
            $.each(task.parentNode.data.children,function(key,value){
                if(task.parentNode.data.children[key].id!=task.data.id){
                    arrTmp.push(task.parentNode.data.children[key]);
                }
            });
            task.parentNode.data.children=arrTmp;
            task.parentNode.removeChild(task, true);
            return;
        }
        if(task.get('consumed') > 0 || task.get('wait') > 0){
            Ext.Msg.show({
                title: '',
                msg: i18n('This task is in used/ has consumed'),
                buttons: Ext.Msg.OK
            });
            return;
        }
        Ext.Msg.show({
            title: i18n('Warning'),
            msg: i18n('Delete task and its sub-tasks?'),
            buttons: Ext.Msg.YESNO,
            fn: function(response) {
                if(response === 'yes') {
                    // save the existing filters
                    taskTree.setLoading(i18n("Please wait"));
                    $.ajax({
                        url: '/project_tasks/destroyTaskJson/' + task.get('id'),
                        data: {
                            data:{
                                task_title: task.get('task_title'),
                                project_id: task.get('project_id')
                            }
                        },
                        async: false,
                        type: 'POST',
                        success:function(data) {
                            var data = JSON.parse(data);
                            if(data.success == false){
                                taskTree.setLoading(false);
                                Ext.MessageBox.show({
                                    title: 'ERROR',
                                    msg: data.message,
                                    icon: Ext.Msg.ERROR,
                                    buttons: Ext.Msg.OK
                                });
                            } else {
                                var arrTmp=new Array();
                                $.each(task.parentNode.data.children,function(key,value){
                                    if(task.parentNode.data.children[key].id!=task.data.id){
                                        arrTmp.push(task.parentNode.data.children[key]);
                                    }
                                });
                                task.parentNode.data.children=arrTmp;
                                task.parentNode.removeChild(task, true);
								var item_task = [];
                                taskTree.refreshSummaryNew(item_task, function(callback){
                                    taskTree.setLoading(false);
                                });
								$('.x-grid-cell').removeClass('x-grid-dirty-cell');
                                me.getPmstreepanel().refreshStaffing(function(callback){
                                    //do nothing
                                });
                            }

                        }
                   });
                   // refresh the list view so the task counts will be accurate
                   taskTree.refreshView();
                }
            }
        });
    },

    handleDeleteClick:function(component, e){
		
        this.deleteTask(this.getPmstreepanel().getSelectionModel().getSelection()[0]);
    },

    showContextMenu: function(view, task, node, rowIndex, e) {
		this.getPmstreepanel().getSelectionModel().select(rowIndex);
        if( !canModify )return false;
        var contextMenu = this.getContextMenu(),
            newListItem = Ext.getCmp('new-task-item'),
            deleteListItem = Ext.getCmp('delete-task-item'),
            specialItem = Ext.getCmp('new-special-task'),
            edit = Ext.getCmp('edit-task'),
            editName = Ext.getCmp('edit-task-name');
        specialItem.hide();
        edit.hide();
        editName.hide();
        if(task.get('special')==1) {
            newListItem.hide();
            edit.show();
            deleteListItem.hide();
            e.preventDefault();
            return;
        }
        // if is root
        if(task.get('id') == 'root'){
            if(task_no_phase){
                newListItem.show();
                if(!create_ntc_task) specialItem.show();
                deleteListItem.hide();
            }else {
                newListItem.hide();
                deleteListItem.hide();
                e.preventDefault();
                return;
            }
        }else{

            if(task.get('is_part') == 'true'){
                newListItem.hide();
                deleteListItem.hide();
                e.preventDefault();
                return;
            }
            if(task.get('is_phase') == 'true'){
                if(!create_ntc_task) specialItem.show();
                if(task.get('is_activity') == 'true'){
                    newListItem.hide();
                    deleteListItem.hide();
                    e.preventDefault();
                    return;
                }else{
                    newListItem.show();
                    deleteListItem.hide();
                }
            }else{
                if(task.parentNode){
                    if(task.parentNode.get('is_phase') == 'true'){
                        if( task.get('is_nct') == 1 ){
                            edit.show();
                            newListItem.hide();
                            if( task.get('consumed') > 0 || task.get('wait') > 0 )
                                deleteListItem.hide();
                            else deleteListItem.show();
                        }
                        else if(task.get('consumed') > 0 && task.childNodes.length == 0){
                            edit.show();
                            newListItem.hide();
                            deleteListItem.show();
                        } else if(task.get('consumed') > 0 && task.childNodes.length > 0){
                            newListItem.show();
                            deleteListItem.show();
                        }
                        else if(task.get('is_predecessor') == 'true'){
                            newListItem.show();
                            deleteListItem.hide();
                            if( task.childNodes.length == 0 ){
                                edit.show();
                            }
                        } else {
                            //thich lam gi thi lam
                            newListItem.show();
                            deleteListItem.show();
                            if( task.childNodes.length == 0 ){
                                edit.show();
                            } else {
                                editName.show();
                            }
                        }
                    } else {
                        newListItem.hide();
                        deleteListItem.show();
                        if( task.childNodes.length == 0 ){
                            edit.show();
                        }
                    }
                } else {
                    if(jQuery.fn.getIsDebuging()){
                    }
                }
            }
        }
        contextMenu.setTask(task);
        if( !newListItem.hidden || !deleteListItem.hidden || !edit.hidden )contextMenu.showAt(e.getX(), e.getY());
        e.preventDefault();
    },

    handleEstimated: function(button, index){
        var me = this;
        var _form = Ext.getCmp('formEstimated').getForm();
        if (_form.isValid()) {
            _form.submit({
                scope: me,
                url: '/project_tasks/saveEstimatedDetail/',
                success: function(_form, action) {
                    var dataJSON = JSON.parse(action.response.responseText);
                    var _references = dataJSON.message;
                    var totalEsti = parseFloat(dataJSON.total);
                    var totalRefen = 0;
                    var submited_estimated_returned = [];
                    var checkNegative = false;
                    for(var i = 0; i < _references.length; i++){
                        if(_references[i].ProjectTaskEmployeeRefer.estimated < 0){
                            checkNegative = true;
                        }
                        submited_estimated_returned.push(_references[i].ProjectTaskEmployeeRefer.estimated);
                        totalRefen += parseFloat(_references[i].ProjectTaskEmployeeRefer.estimated);
                    }
                    button.task.set("estimated_detail", submited_estimated_returned);
                    // button.task.save({
                    //     scope:me,
                    //     success: function(returnTask, operation){
                    //         me.getPmstreepanel().refreshView();
                    //         button.task = returnTask;
                    //     }
                    // });

                    if(checkNegative == true){
                        var _msg = 'The workload greater or equal to 0';
                        Ext.MessageBox.show({
                            title   : 'Save Failed',
                            msg     : _msg,
                            icon    : Ext.Msg.ERROR,
                            buttons : Ext.Msg.OK
                        });
                       return false;
                    } else {
                        totalRefen = totalRefen.toFixed(2);
                        if(totalRefen == totalEsti){
                            button.up('.window').close();
                            Ext.showMsg('Saved');
                        } else {
                            var _msg = Ext.String.format(i18n('Workload {1} M.D , Workload filled {0} M.D.'), totalRefen, totalEsti);
                            Ext.MessageBox.show({
                                title   : '',
                                msg     : _msg,
                                icon    : Ext.Msg.ERROR,
                                buttons : Ext.Msg.OK
                            });
                           return false;
                        }
                    }
                    me.getPmstreepanel().refreshStaffing(function(callback){
                        //do nothing
                    });
                },
                failure: function(_form, action) {
                    Ext.showMsg('Not saved', 'error');
                }
            });
        }
    },

    handleLinked: function(id, task_end_date, increase, change, _json_task_return, task){
        var me = this;
        var tasksTree = this.getPmstreepanel();
        me.getPmstreepanel().setLoading(i18n("Please wait"));
        Ext.Ajax.request({
            scope: me,
            url: '/project_tasks/updateLinkedTasks/',
            method: 'GET',
            params: {
                id: id,
                number: increase,
                change: change,
                endDate: task_end_date
            },
            failure: function (responsex, optionsx) {

            },
            success:function(responsex, optionsx) {
                me.getPmstreepanel().refreshView();
                me.getPmstreepanel().refreshSummary(function(callback){
                    me.getPmstreepanel().setLoading(false);
                });
                if(_json_task_return.task_assign_to_id.length > 1 && _json_task_return.estimated > 0){
                    tasksTree.checkEstimated(_json_task_return,task);
                    me.getPmstreepanel().refreshSummary(function(callback){
                        me.getPmstreepanel().setLoading(i18n("Please wait"));
                    });
                } else {
                    me.getPmstreepanel().refreshSummary(function(callback){
                        me.getPmstreepanel().setLoading(false);
                    });
                }

                me.getPmstreepanel().refreshStaffing(function(callback){
                    //do nothing
                });
            }
        });
    },
    handleAddSpecial: function(){
        var me = this.getPmstreepanel(),
            selectionModel = me.getSelectionModel(),
            selectedTask = selectionModel.getSelection()[0];
        if( selectedTask.get('is_phase') == "true" ){
            var phase = parseInt(selectedTask.get('id'))-1000000000000+1;
            var start_date = selectedTask.get('task_start_date');
            var end_date = selectedTask.get('task_end_date');
            if(start_date){
                start_date = (start_date.getDate() < 10 ? '0' +  start_date.getDate() :  start_date.getDate()) + '-' + ((start_date.getMonth() + 1) < 10 ? '0' + (start_date.getMonth() + 1) : (start_date.getMonth() + 1)) + '-' + start_date.getFullYear();
            }
            if(end_date){
                end_date = (end_date.getDate() < 10 ? '0' +  end_date.getDate() :  end_date.getDate()) + '-' + ((end_date.getMonth() + 1) < 10 ? '0' + (end_date.getMonth() + 1) : (end_date.getMonth() + 1)) + '-' + end_date.getFullYear();
            }
            $('#special-task-info').data('tree', me);
            Task = new SpecialTask({
                columns: [],
                data: {},
                task: {
                    id: 0,
                    task_title: '',
                    task_priority_id: '',
                    task_status_id: '',
                    task_start_date: start_date,
                    task_end_date: end_date,
                    project_planed_phase_id: phase
                }
            });
            $('#special-task-info').dialog('open');
        }
    },
    checkName: function(name, task){
        var parent = task.parentNode;
        var count = 0;
        if( parent ){
            //traverse each child of parent
            Ext.Array.forEach(parent.childNodes, function(child){
                if( task.get('id') != child.get('id') && child.get('task_title') == name ){
                    count++;
                }
            });
        }
        return count;
    },
    editTaskName: function(){
        var tree = this.getPmstreepanel(),
            task = this.getContextMenu().getTask(),
            me = this;
        //create a window
        var w = new Ext.window.Window({
            autoShow: true,
            title: i18n('Edit'),
            minWidth: 500,
            minHeight: 70,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    allowBlank: false,
                    value: task.get('task_title'),
                    validator: function(value){
                        var count = me.checkName(value, task),
                            msg = '<span style="color: red">' + i18n('Task name already existed') + '</span>';
                        if( count > 0 ){
                            w.down('statusbar').setText(msg);
                            return msg;
                        }
                        return true;
                    }
                }
            ],
            closable: true,
            listeners:{
                render: function(label){
                    tree.setLoading(i18n('Please wait'));
                },
                close: function(){
                    tree.setLoading(false);
                }
            },
            bbar: Ext.create('Ext.ux.StatusBar', {
                id: 'statusbar',
                text: '',
                items: [
                    '-',
                    {
                        text: i18n('Save'),
                        handler: function(){
                            var textField = w.down('[name="name"]'),
                                btn = this;
                            if( textField.isValid() ){
                                this.disable();
                                //call ajax
                                $.ajax({
                                    url: Azuree.root + 'project_tasks/saveName',
                                    type: 'POST',
                                    data: {
                                        data: {
                                            id: task.get('id'),
                                            task_title: textField.getValue()
                                        }
                                    },
                                    dataType: 'json',
                                    success: function(data){
                                        task.set('task_title', data.task_title);
                                        task.commit();
                                    },
                                    complete: function(){
                                        w.down('statusbar').setText('');
                                        btn.enable();
                                        w.close(false);
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: i18n('Reset'),
                        handler: function() {
                            var textField = w.down('[name="name"]');
                            textField.setValue(task.get('task_title'));
                        }
                    },
                    {
                        text: i18n('Cancel'),
                        handler: function() {
                            w.close();
                        }
                    }
                ]
            })
        });
    }
});
