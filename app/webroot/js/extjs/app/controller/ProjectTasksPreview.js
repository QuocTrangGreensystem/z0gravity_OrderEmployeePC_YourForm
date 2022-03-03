// Ext.define('EXTJS_23846.Element', {
    // override: 'Ext.dom.Element'
// }, function(Element) {
    // var supports = Ext.supports,
        // proto = Element.prototype,
        // eventMap = proto.eventMap,
        // additiveEvents = proto.additiveEvents;

    // if (Ext.os.is.Desktop && supports.TouchEvents && !supports.PointerEvents) {
        // eventMap.touchstart = 'mousedown';
        // eventMap.touchmove = 'mousemove';
        // eventMap.touchend = 'mouseup';
        // eventMap.touchcancel = 'mouseup';

        // additiveEvents.mousedown = 'mousedown';
        // additiveEvents.mousemove = 'mousemove';
        // additiveEvents.mouseup = 'mouseup';
        // additiveEvents.touchstart = 'touchstart';
        // additiveEvents.touchmove = 'touchmove';
        // additiveEvents.touchend = 'touchend';
        // additiveEvents.touchcancel = 'touchcancel';

        // additiveEvents.pointerdown = 'mousedown';
        // additiveEvents.pointermove = 'mousemove';
        // additiveEvents.pointerup = 'mouseup';
        // additiveEvents.pointercancel = 'mouseup';
    // }
// });
// Ext.define('EXTJS_23846.Gesture', {
    // override: 'Ext.event.publisher.Gesture'
// }, function(Gesture) {
    // var me = Gesture.instance;

    // if (Ext.supports.TouchEvents && !Ext.isWebKit && Ext.os.is.Desktop) {
        // me.handledDomEvents.push('mousedown', 'mousemove', 'mouseup');
        // me.registerEvents();
    // }
// });
Ext.define('PMS.controller.ProjectTasksPreview', {
    extend: 'Ext.app.Controller',
    models: ['ProjectTaskPreview'],
    stores: ['ProjectTasksPreview'],
    views: [
        'TreePanelPreview',
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
        tasksStore = me.getProjectTasksPreviewStore();
		// console.log(me);
        me.control({
            '[id=new-task-item]': {
                click: me.handleAddTask
            },
            '[id=delete-task-item]': {
                click: me.handleDeleteClick
            },
            '[id=new-special-task]': {
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
				// rowclick: function(){
					// console.log('row click');
				// },
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
					if( treepanel.getColumns()[cellIndex].dataIndex == 'task_title'){
						var _this = $(e.target);
						if( _this.hasClass('x-tree-elbow') || _this.hasClass('x-tree-elbow-end') ){
							if (!( record.hasChildNodes() || record.get('is_phase') || record.get('is_part') || record.isRoot() )){
								me.selectTaskOnly();
								// key 16, 17 : Ctrl, Shift
								if( _selectIconClicked.clicked && _selectIconClicked.isSelected && !_keyPressed[16] && !_keyPressed[17] && _selectIconClicked.old_status){
									var selectionModel = treepanel.getSelectionModel();
									// console.log(new Date().getTime(), 'deselect', _selectIconClicked.old_status);
									selectionModel.deselect(record, true, false); //select ( records, keepExisting, suppressEvent )
									return false;
								}else{
									me.multiSelectClass();
								}
							}
						}
					}
				},
				celldblclick: function(t, td, cellIndex, record, tr, rowIndex, e, eOpts){
					if( _selectIconClicked.clicked ) return false;
					var columns = t.grid.columns[cellIndex].dataIndex;
					var open_popup_columns = ['estimated','task_assign_to_id','task_start_date','task_end_date','task_status_id'];
					if(($.isEmptyObject(record.get('children'))) && show_workload && ( record.get('is_nct') == 0 ) && ($.inArray( columns, open_popup_columns) != -1)){
						t.grid.showEditNormalTask(record.data, record);
						return false;
					}
				},
            },
            '[iconCls=project-tasks-add]': {
                //click:me.handleAddTask
            },
            '[iconCls=estimated-detail-save x-save]': {
                click: me.handleEstimated
            },
            '[iconCls=save-estimated-detail]': {
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
            },
            '[id=batch-update-tasks]': {
                click: me.batchUpdateTasks
            },
            '[id=batch-delete-tasks]': {
                click: me.batchDeleteTasks
            },
            '[id=batch-update-workload-from-consumed]': {
                click: me.updateWorkloadFromConsumed
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
        treepanel = me.getPmstreepanel(),
        store = me.getProjectTasksPreviewStore();
        store.root.data.task_title = "All Task";
        treepanel.afterLoad();
		treepanel.getSelectionModel().allowDeselect = true;
		treepanel.getSelectionModel().selectionMode = "MULTI";
        treepanel.doAll();
		var selectionModel = treepanel.getSelectionModel();
        selectionModel.on('beforedeselect', function(model, record, index, eOpts){
			var _continue = true;
			if( _selectIconClicked.clicked && !_keyPressed[16] && !_keyPressed[17]){
				// Loai tru truong hop nhan Ctrl va Shift
				var _t = treepanel.getStore().getAt(_selectIconClicked.index);
				if (!(_t.hasChildNodes() || _t.get('is_phase') || _t.get('is_part') || _t.isRoot() )){
					if( _selectIconClicked.isSelected ){
					}else{
						var _selected_items = me.getPmstreepanel().getSelection();
						var keepExisting = (_selectIconClicked.old_status) || (_selected_items.length > 1);
						selectionModel.select(_t, keepExisting, true); //select ( records, keepExisting, suppressEvent )
					}
					me.selectTaskOnly();
					treepanel.refreshView();
					me.multiSelectClass();
					return false;
				}
			}
			return  _continue;
			
		});
		selectionModel.on('selectionchange', function(model, items, scope){
			if( items.length > 1) {
				me.selectTaskOnly();
				treepanel.refreshView();
			}else{
				if( items.length) {
					var _it = items[0];
				}
			}
			me.multiSelectClass();
		});
		
    },
	// Allow task only 
	selectTaskOnly: function(){
		var me = this,
        selectionModel = me.getPmstreepanel().getSelectionModel();
		var _selected = selectionModel.getSelected();
		var _rm = _selected.items.map( function(i){
			if (i.hasChildNodes() || i.get('is_phase') || i.get('is_part') || i.isRoot() ) return i.get('id');
			return false;
		});
		_rm.filter( function(v){ return v;})
		$.each( _rm, function(i,v){ _selected.removeByKey(v) });
		selectionModel.setLocked(true);
		selectionModel.select(0);
		selectionModel.select(_selected.items);
		selectionModel.setLocked(false);
    },
	multiSelectClass: function(){
		// Khong biet add class cho panel the nao nen add vao container
		var me = this,
        items = me.getPmstreepanel().getSelection();
		var _container = $('#project_container');
		if( _container.length) _container.toggleClass('multi-selection', ((items.length > 1) || _selectIconClicked.clicked));
	},
    handleWrite: function(a, b, c, d, e){
    },
    handleDatachanged: function(store, eOpts){
		var me = this,
        pmstreepanel = me.getPmstreepanel();
		pmstreepanel.dataChanged();
    },
    handleRemoveTask: function(parentNode, currentNode, destroy){
        var me = this,
        pmstreepanel = me.getPmstreepanel(),
        store = me.getProjectTasksPreviewStore();
    },
    // Controls' section
    taskDrop: function(){
    },
    syncTasksStores: function(listsStore, operation) {
        var me = this,
            stores = [
                me.getProjectTasksPreviewStore()
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
								console.error('remove task');
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
		// console.log( editor, e, task);
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
		var _orgVals = e.originalValues;
		if( !me.isFalse( _orgVals.predecessor)){
			me.updateIsPredecessor(_orgVals.predecessor);
		}
		if( !me.isFalse( task.get('predecessor'))){
			var _p_task = tasksTree.getStore().getById(task.get('predecessor')).set('is_predecessor', "true");
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
            // var _duration_msg = '<p class="wd_msg">' + i18n('Keep the duration of the task ') + getName + '</p>'
            // new Ext.window.Window({
                // autoShow: true,
                // componentCls: 'wd_mgs_box wd_ext2019form',
                // title: '',
                // width: 350,
                // minWidth: 200,
                // minHeight: 200,
                // autoScroll: true,
                // autoHeight: true,
                // layout: 'fit',
                // plain: true,
                // html: _duration_msg,
                // closable: false,
                // buttons: [
                    // {
                        // text: i18n('no'),
                        // cls: 'btn-form-action btn-cancel btn-left',
                        // handler: function() {
                            // task.set('keep_duration', 0);
                            // this.ownerCt.ownerCt.close();
                            // me.handleSaveTask(me, task, tasksTree);
                        // }
                    // },
                    // {
                        // text: i18n('yes'),
                        // cls: 'btn-form-action btn-ok btn-right',
                        // handler: function() {
                            // task.set('keep_duration', 1);
                            // this.ownerCt.ownerCt.close();
                            // me.handleSaveTask(me, task, tasksTree);
                        // }
                    // },
                    
                // ],
                
            // });

        // } else {
            task.set('keep_duration', 0);
            me.handleSaveTask(me, task, tasksTree);
        // }
    },
    updateIsPredecessor: function(task_id){
		var me = this,
		treepanel = me.getPmstreepanel(),
		store = treepanel.getStore()
		tasks = store.byIdMap,
		task = store.getById(task_id);
		if( me.isFalse(task)) return false;
		var _has_predecessor = 'false';
		$.each(tasks, function(_id, _t){
			if( _t.get('predecessor') == task_id){
				_has_predecessor = 'true';
				return false; // return true: continue / return false: break
			}
		});
		// console.log(task_id, _has_predecessor);
		task.set('is_predecessor', _has_predecessor);
		return _has_predecessor;
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
                    var _linked_msg = '<p class="wd_msg">' + Ext.String.format(i18n('{0} is linked to other(s) task(s). If you modified the end date the tasks(s) linked will be modified. However the duration of the tasks linked will not be modified?'), _json_task_return.task_title) + '</p>';
                    new Ext.window.Window({
                        autoShow: true,
                        componentCls: 'wd_mgs_box wd_ext2019form',
                        title: '',
                        width: 500,
                        minWidth: 200,
                        minHeight: 200,
                        autoScroll: true,
                        autoHeight: true,
                        layout: 'fit',
                        plain: true,
                        html: _linked_msg,
                        closable: false,
                        // buttonAlign: 'center',
                        buttons: [
                            {
                                text: i18n('no'),
                                cls: 'btn-form-action btn-cancel btn-left',
                                handler: function() {
                                    me.handleLinked(_json_task_return.id, _json_task_return.task_end_date, _json_task_return.increase_endDate, false, _json_task_return, task);
                                    this.ownerCt.ownerCt.close();
                                }
                            },
                            {
                                text: i18n('yes'),
                                cls: 'btn-form-action btn-ok btn-right',
                                handler: function() {
                                    me.handleLinked(_json_task_return.id, _json_task_return.task_end_date, _json_task_return.increase_endDate, true, _json_task_return, task);
                                    this.ownerCt.ownerCt.close();
                                }
                            },
                            
                        ],
                        
                    });

                } else {
                    /* Edit by Huynh 05-08-2019 Ticket #390 
                    Chi hien thi popup workload khi co su thay doi ve:
                        + Workload
                        + Assign: ( employeeid, is_profit_center)
                        * Effect: file  TreePanelPreview.js:  ctaskBefore
                    */
                    var checkEstimated = true;
                    if( ctaskBefore){
                        if( tasksTree.isEqual( ctaskBefore.estimated, task.get('estimated') ) && tasksTree.isEqual( ctaskBefore.is_profit_center, task.get('is_profit_center')) && tasksTree.isEqual( ctaskBefore.task_assign_to_id, task.get('task_assign_to_id') ) ) checkEstimated = false;
                    }
                    /* End Huynh */

                    if(_json_task_return.task_assign_to_id.length > 1 && _json_task_return.estimated > 0 &&  checkEstimated){
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

                me.getPmstreepanel().refreshStaffing(function(callback){
                    //do nothing
                });
				if( typeof z_save_task_callback == 'function' )  z_save_task_callback(task, ctaskBefore);
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
		var status_text_default = (status_default) ? listAllStatus[status_default] : 0;
        if(parentTask.get('is_phase')=="true"){
            var newTask = Ext.create('PMS.model.ProjectTaskPreview', {
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
                var newTask = Ext.create('PMS.model.ProjectTaskPreview', {
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
        // console.log(newTask);
        parentTask.appendChild(newTask);
        // console.log(parentTask);
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
		var tree = Ext.getCmp('pmstreepanel');
        var me = this,
        taskTree = me.getPmstreepanel(),
        taskName = task.get('task_title'),
        selModel = taskTree.getSelectionModel(),
        tasksStore = me.getProjectTasksPreviewStore();
		var rowSelected = tree.store.indexOf(task);
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
                title: i18n('Error'),
				cls: 'wd_ext2019form',
                msg: i18n('This task is in used/ has consumed'),
                buttons: Ext.Msg.OK
            });
            return;
        }
        Ext.Msg.show({
            title: i18n('Warning'),
			cls: 'wd_ext2019form',
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
								selModel.select(rowSelected - 1);
                                taskTree.refreshSummary(function(callback){
                                    taskTree.setLoading(false);
                                });
                                me.getPmstreepanel().refreshStaffing(function(callback){
                                    //do nothing
                                });
								if( typeof z_delete_task_callback == 'function' )  z_delete_task_callback();
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
		if(!rowIndex) return false;
		var me = this;
		var selectionModel = me.getPmstreepanel().getSelectionModel();
		var _selected = selectionModel.getSelected();
		if( !_selected.getByKey(task.get('id')) ){
			selectionModel.select(rowIndex);
		}
        if( !canModify )return false;
        var contextMenu = this.getContextMenu(),
            newListItem = Ext.getCmp('new-task-item'),
            deleteListItem = Ext.getCmp('delete-task-item'),
            specialItem = Ext.getCmp('new-special-task'),
            edit = Ext.getCmp('edit-task'),
            editName = Ext.getCmp('edit-task-name');
            batchUpdate = Ext.getCmp('batch-update-tasks');
            batchDelete = Ext.getCmp('batch-delete-tasks');
            batchUpdateWorloadFromConsumed = Ext.getCmp('batch-update-workload-from-consumed');
       
		batchUpdateWorloadFromConsumed.hide();
		if( _selected.items.length > 1){
			specialItem.hide();
			edit.hide();
			editName.hide();
			newListItem.hide();
			deleteListItem.hide();
			batchUpdate.show();
			batchDelete.show();
			if(  !is_manual_consumed && adminTaskSetting && adminTaskSetting['Consumed'] == '1' ){
				var _hasNct = 0;
				$.each( _selected.items, function(i, _t){
					var _is_nct = _t.get('is_nct');
					if(!me.isFalse( _is_nct)){
						_hasNct = true;
					}
				});
				if(!_hasNct ) batchUpdateWorloadFromConsumed.show();
			}
		}else{
			specialItem.hide();
			edit.hide();
			editName.hide();
			batchUpdate.hide();
			batchDelete.hide();
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
								edit.show();
							   
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
		}
        contextMenu.setTask(task);
        if( !newListItem.hidden || !deleteListItem.hidden || !edit.hidden || !batchUpdate.hidden || !batchDelete.hidden )contextMenu.showAt(e.getX(), e.getY());
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
                            var _msg = '<p class="wd_msg">' + Ext.String.format(i18n('Workload {1} M.D , Workload filled {0} M.D.'), totalRefen, totalEsti) + '</p>';
                           //  Ext.MessageBox.show({
                           //      title: i18n('Error'),
                           //      cls: 'wd_ext2019form error',
                           //      msg     : _msg,
                           //      // icon    : Ext.Msg.ERROR,
                           //      buttons : Ext.Msg.OK
                           //  });
                           // return false;
                           new Ext.window.Window({
                                autoShow: true,
                                componentCls: 'wd_mgs_box wd_ext2019form error',
                                title: i18n('Error'),
                                width: 350,
                                minWidth: 200,
                                minHeight: 200,
                                autoScroll: true,
                                autoHeight: true,
                                layout: 'fit',
                                plain: true,
                                html: _msg,
                                buttonAlign: 'center',
                                closable: true,
                                buttons: [
                                    {
                                        text: i18n('ok'),
                                        disabled : false,
                                        cls: 'btn-form-action btn-ok btn-center',
                                        handler: function() {
                                            this.ownerCt.ownerCt.close();
                                        }
                                    },
                                    
                                ],
                                
                            });
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
            // console.log(selectedTask);
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
            $('#special-task-info-2').data('tree', me);
            // console.log( $('#special-task-info-2').length );
            if( $('#special-task-info-2').length) show_full_popup('#special-task-info-2', {width: 'inherit'}, false);
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
			componentCls: 'wd_ext2019form',
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
						cls: 'x-btn btn-form-action',
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
						cls: 'x-btn btn-form-action',
                        handler: function() {
                            var textField = w.down('[name="name"]');
                            textField.setValue(task.get('task_title'));
                        }
                    },
                    {
                        text: i18n('Cancel'),
						cls: 'x-btn btn-form-action',
                        handler: function() {
                            w.close();
                        }
                    }
                ]
            })
        });
    },
	batchUpdateTasks: function(){
		var _popup = $('#template_batch_edit_task');
		var _hasNct = _has_predecessor = false;
		if( _popup.length){
			show_full_popup(_popup);
			var me = this,
			taskTree = me.getPmstreepanel(),
			selModel = taskTree.getSelectionModel();
			var _selected = selModel.getSelected();
			if( ! _selected.items.length) return;
			
			var _task_id_cont = _popup.find('.batch-edit-task-list-id');
			_task_id_cont.empty();
			$.each( _selected.items, function(i, _t){
				$('<input>', {
					type: 'hidden',
					id: 'batch-edit-task-id-' + _t.get('id'),
					name: 'data[ProjectTask][task_id][]',
					value: _t.get('id'),
					'data-name': _t.get('task_title')
					//'data-name': JSON.stringify(_t.get('task_title'))
				}).appendTo(_task_id_cont);
				var _is_predecessor = _t.get('is_predecessor');
				var _predecessor = _t.get('predecessor');
				var _is_nct = _t.get('is_nct');
				if( !me.isFalse( _is_predecessor) || !me.isFalse(_predecessor)){
					_has_predecessor = true;
				}
				if(!me.isFalse( _is_nct)){
					_hasNct = true;
				}
				
				// console.log( _is_predecessor, _predecessor, _has_predecessor);
			});
			if( typeof before_batch_edit_tasks == 'function') before_batch_edit_tasks(_selected.items);
			_popup.find('.wd-date').prop('disabled', false);
			_popup.find('.wd-multiselect').toggleClass('disabled', false);
			_popup.find('#batchAddAssignedto').prop('disabled', false);
			_popup.find('#batchReplaceAssignedto').prop('disabled', false);
			_popup.find('.workload-group').show();
			var _field_message = _popup.find('.form-field-message.for-wd-date');
			if( _hasNct ){
				// console.log( 'Disabled'); 
				_popup.find('.wd-date').prop('disabled', true);
				_popup.find('.wd-multiselect').toggleClass('disabled', true);
				_popup.find('#batchAddAssignedto').prop('disabled', true);
				_popup.find('#batchReplaceAssignedto').prop('disabled', true);
				_field_message.text( i18n('Dates cannot be updated, there is an NCT task') );
				_popup.find('.workload-group').hide();
			}else if( _has_predecessor ){
				// console.log( 'Disabled 2', _has_predecessor); 
				_popup.find('input.wd-date').prop('disabled', true);
				_field_message.text( i18n('Dates cannot be updated, a task has a predecessor') );
				
			}
			console.log(_hasNct, _has_predecessor,  (_hasNct || _has_predecessor));
			_field_message.toggleClass('show', (_hasNct || _has_predecessor));
		}
	},
	batchDeleteTasks: function(){
		var tree = Ext.getCmp('pmstreepanel');
        var me = this,
        taskTree = me.getPmstreepanel(),
        selModel = taskTree.getSelectionModel(),
        tasksStore = me.getProjectTasksPreviewStore();
		var _selected = selModel.getSelected();
		var taskIds = [];
		if( ! _selected.items.length) return;
		var project_id = _selected.items[0].get('project_id');
		var _canDelete = true;
        $.each( _selected.items, function(i, _t){
			if(_t.get('consumed') > 0 || _t.get('wait') > 0){
				Ext.Msg.show({
					title: i18n('Warning'),
					cls: 'wd_ext2019form',
					msg: i18n('This task is in used/ has consumed') + '<br><strong>' +  _t.get('task_title')  + '</strong>',
					buttons: Ext.Msg.OK
				});
				_canDelete = false;
				return false;
			}
			var _is_predecessor = _t.get('is_predecessor');
			// console.log( _is_predecessor, me.isFalse(_is_predecessor));
			if( !me.isFalse(_is_predecessor)){
				Ext.Msg.show({
					title: i18n('Warning'),
					cls: 'wd_ext2019form',
					msg: i18n('Cannot be deleted task linked') + '<br><strong>' +  _t.get('task_title')  + '</strong>',
					buttons: Ext.Msg.OK
				});
				_canDelete = false;
				return false;
			}
			taskIds.push(_t.get('id'));
		});
		if( ! _canDelete) return false;
		var _list_task = $('<ul>', {class: 'list-task'});
		$.each ( _selected.items, function( i, task){
			_list_task.append( $('<li>', {
				class: 'delete-task-title task-' + task.getId(),
				id: 'delete-task-id-' + task.getId(),
				title: task.get('task_title'),
			}).html('<p><strong>' + task.get('task_title') + '</strong></p>') );
		});
		console.log( _list_task);
        Ext.Msg.show({
            title: i18n('Delete tasks and its sub-tasks?'),
			cls: 'wd_ext2019form',
            msg: _list_task[0].outerHTML,
            buttons: Ext.Msg.YESNO,

            fn: function(response) {
                if(response === 'yes') {
                    // save the existing filters
                    taskTree.setLoading(i18n("Please wait"));
					var rowSelected = tree.store.indexOf(_selected.items[0]);
					selModel.setLocked(true);
					var _taskIds = [];
					$.each ( _selected.items, function( i, task){
						_taskIds.push( task.getId());
						task.parentNode.removeChild(task, false);
					});
					me.doDeleteTasks(_taskIds);
					selModel.setLocked(false);
					selModel.select(rowSelected - 1);
					taskTree.refreshSummary(function(callback){
						taskTree.setLoading(false);
					});
					me.getPmstreepanel().refreshStaffing(function(callback){
						// do nothing
					});
					if( typeof z_delete_task_callback == 'function' )  z_delete_task_callback();
                   // refresh the list view so the task counts will be accurate
                   taskTree.refreshView();
                }
            }
        });
    },
	doDeleteTasks: function( listID){
		if( listID == undefined ) return false;
		var me = this;
        var tasksTree = this.getPmstreepanel();
		var result = false;
		$.ajax({
			url: '/project_tasks/batchDeleteTasks/',
			data: {
				data:{
					task_id: listID,
					project_id: tasksTree.project_id
				}
			},
			async: false,
			type: 'POST',
			dataType: 'json',
			success:function(data) {
				// var data = JSON.parse(data);
				console.log( data);
				if(data.success == false){
					taskTree.setLoading(false);
					Ext.MessageBox.show({
						title: 'ERROR',
						msg: data.message,
						icon: Ext.Msg.ERROR,
						buttons: Ext.Msg.OK
					});
				} else {
					result = true;
				}
			}
		});
		return result;
	},
	// default var = undefined, is_strict = false
	isFalse: function( val, is_strict){
		if( typeof is_strict == 'undefined' ) var is_strict = false;
		if( typeof val == 'undefined' ) return true;
		if( typeof val == 'object' ) return $.isEmptyObject(val);
		return (!val || ( !is_strict && (val == 'false' || val == '0' || val =="")));
	},
	updateWorkloadFromConsumed: function(){
		var tree = Ext.getCmp('pmstreepanel');
        var me = this,
        taskTree = me.getPmstreepanel(),
        selModel = taskTree.getSelectionModel(),
        tasksStore = me.getProjectTasksPreviewStore();
		var _selected = selModel.getSelected();
		var taskIds = [];
		if( ! _selected.items.length) return;
		var project_id = _selected.items[0].get('project_id');
		Ext.Msg.show({
            title: i18n('Warning'),
			cls: 'wd_ext2019form',
            msg: i18n('Adjusted the workload with consumed') + '<br>' + i18n('All current workloads will be overwritten'),
            buttons: Ext.Msg.YESNO,
            fn: function(response) {
                if(response === 'yes') {
                    // save the existing filters
                    taskTree.setLoading(i18n("Please wait"));
					// var rowSelected = tree.store.indexOf(_selected.items[0]);
					// selModel.setLocked(true);
					var _taskIds = [];
					$.each ( _selected.items, function( i, task){
						_taskIds.push( task.getId());
					});
					if( !$.isEmptyObject(_taskIds)) me.doUpdateWorkloadFromConsumed(_taskIds);
					// selModel.setLocked(false);
					// selModel.select(rowSelected - 1);
					taskTree.refreshSummary(function(callback){
						taskTree.setLoading(false);
					});
					me.getPmstreepanel().refreshStaffing(function(callback){
						// do nothing
					});
                   // refresh the list view so the task counts will be accurate
                   taskTree.refreshView();
                }
            }
        });
	},
	doUpdateWorkloadFromConsumed: function(listID){
		if( listID == undefined ) return false;
		var me = this;
        var tasksTree = this.getPmstreepanel();
		var result = false;
		$.ajax({
			url: '/project_tasks/batchUpdateWorkloadByConsumed/',
			data: {
				data:{
					task_id: listID,
					project_id: tasksTree.project_id
				}
			},
			async: false,
			type: 'POST',
			dataType: 'json',
			success:function(data) {
				// var data = JSON.parse(data);
				console.log( data);
				if(data.success == false){
					taskTree.setLoading(false);
					Ext.MessageBox.show({
						title: 'ERROR',
						msg: data.message,
						icon: Ext.Msg.ERROR,
						buttons: Ext.Msg.OK
					});
				} else {
					result = true;
				}
			}
		});
		return result;
	}
});
