Ext.define('PMS.controller.ActivityTasks', {
	extend: 'Ext.app.Controller',
	models: ['ActivityTask'],
	stores: ['ActivityTasks'],
    views: [
		'TreePanelActivity',
        'ContextMenu'
	],
	refs: [
		{
			ref: 'pmstreepanelactivity',
			selector: 'pmstreepanelactivity'
		},
        {
            ref: 'contextMenu',
            selector: 'tasksContextMenu',
            xtype: 'tasksContextMenu',
            autoCreate: true
        }
	],
    editingTasks:{},
	init: function() {     
        var me = this;
        var activityTaskStore = this.getActivityTasksStore();

        //
        me.control({
            '[iconCls=tasks-new-list add]': {
                click: me.handleAddTask
            },
            '[iconCls=tasks-special-list add]': {
                click: me.handleAddSpecial
            },
            '[iconCls=tasks-delete-list delete]': {
                click: me.handleDeleteClick
            },
			'pmstreepanelactivity': {
				// afterrender: me.handleAfterListTreeRender,
                edit: me.updateTask,
                canceledit: me.handleCancelEdit,
                // deleteclick: me.handleDeleteIconClick,
                // selectionchange: me.filterTaskGrid,
                taskdrop: me.taskDrop,
                //taskdrop: me.updateTaskList,
                // listdrop: me.reorderList,
                // itemmouseenter: me.showActions,
                // itemmouseleave: me.hideActions,
                itemcontextmenu: me.showContextMenu,

                cellclick: function(t, td, cellIndex, record, tr, rowIndex, e, eOpts){
                    //click on taskname on mobile
                    if( Azuree.isTouch && cellIndex == 1 ){
                        me.showContextMenu(null, record, null, null, e);
                    }
                }
			},
			'[iconCls=project-tasks-add]': {
				//click:me.handleAddTask
			},
            '[iconCls=estimated-detail-save]': {
                click: me.handleEstimated
            },
            '[iconCls=ext-edit-task]': {
                click: me.editTask
            },
            '[id=edit-task-name]': {
                click: me.editTaskName
            }
		});

        activityTaskStore.on('remove', me.handleRemoveTask, me);
        activityTaskStore.on('datachanged', me.handleDatachanged, me);
        activityTaskStore.on('write', me.syncTasksStores, me);
		activityTaskStore.on('load', me.handleLoad, me);
        //
        activityTaskStore.load();
	},
    editTask: function(){
        var tree = this.getPmstreepanelactivity(),
            model = tree.getSelectionModel(),
            task = model.getSelection()[0];
        tree.cellEditingPlugin.startEdit(task);
    },
    handleLoad: function(a, b, c, d, e){
        this.getPmstreepanelactivity().afterLoad();
        this.getPmstreepanelactivity().doAll();
    },

    handleWrite: function(a, b, c, d, e){
        
    },

    handleDatachanged: function(store, eOpts){
         
    },

    handleRemoveTask: function(parentNode, currentNode, destroy){  
        var me = this,
            pmstreepanel = me.getTreePanelActivityView(),
            store = me.getActivityTasksStore()
    },
	taskDrop: function(){

	},

	syncTasksStores: function(listsStore, operation) {
	   
        var me = this,
            stores = [
                me.getActivityTasksStore()
            ], 
            taskToSync;

        Ext.each(operation.getRecords(), function(task) {
            Ext.each(stores, function(store) {
                if(store) {
                    taskToSync = store.getNodeById(task.getId());
                    switch(operation.action) {
                        case 'create':  
                            //(store.getNodeById(task.parentNode.getId()) || task.getRootNode()).appendChild(task.copy());
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
	    var me  	= this,
        	task  	= e.record,
            tasksTree = me.getPmstreepanelactivity();
         var getDuration = task.get("duration"),
            getName = task.get("task_title"),
            callKeepDuration = task.get('callKeepDuration');
        // console.log(task);
		//RESET LIST ASSIGN AND VALUES REFER WHEN ADD OR REMOVE RESOURCE
		//FIX BUG ESTIMATED DETAIL ABSOLUTE, BUG INCONVENIENT
		if($.isNumeric(task.get("id")))
		{
			var oldAssignId = task.raw.task_assign_to_id ? task.raw.task_assign_to_id : [];
			var newAssignId = task.get("task_assign_to_id") ? task.get("task_assign_to_id") : [];

			var oldAssignText;
            if( typeof task.raw.task_assign_to_text == 'string' ){
                oldAssignText = task.raw.task_assign_to_text.split(',');
            } else if( typeof task.raw.task_assign_to_text == 'object' ){
                oldAssignText = task.raw.task_assign_to_text;
            } else {
                oldAssignText = [];
            }
			var newAssignText = task.get("task_assign_to_text") ? task.get("task_assign_to_text").split(",") : [];
			var estimatedDetail = task.get("estimated_detail");
			var listAssignAfterCompare = [];
			var listAssignTextAfterCompare = [];
			var temp = 0;
			$.each(oldAssignId, function(index, val){
				if(val == newAssignId[index])
				{
					//LIST ASSIGNED ORDER BY
					listAssignAfterCompare.push(val);
					listAssignTextAfterCompare.push(oldAssignText[index]);
				}
				else
				{
					if(jQuery.inArray(val,newAssignId) !== -1)
					{  
					   //LIST ASSIGNED CHANGED ORDER
					   listAssignAfterCompare.push(val);
					   listAssignTextAfterCompare.push(oldAssignText[index]);
					}
					else
					{
						//LIST ASSIGNED DELECTED
						indexTemp = index - temp;
						if(estimatedDetail[indexTemp])
							estimatedDetail.splice(indexTemp,1);
						temp++;
					}
				}
			});
			$.each(newAssignId, function(index, val){
				if(jQuery.inArray(val,listAssignAfterCompare) !== -1)
				{  
				   //do nothing
				}
				else
				{
					//LIST NEW ASSIGNED
					text = jQuery.trim(newAssignText[index]);
					listAssignAfterCompare.push(val);
					listAssignTextAfterCompare.push(text);
				}
			});
			if(task.get("id_refer_flag"))
			{
				var id_refer_flag = task.get("id_refer_flag");
				var is_profit_center_after = [];
				$.each(listAssignAfterCompare, function(index, val){
					if(is_profit_center_after[val])
					is_profit_center_after.push(1)
					else
					is_profit_center_after.push(id_refer_flag[val]);
				});
				task.set("is_profit_center",is_profit_center_after);
				task.raw.is_profit_center = is_profit_center_after;
			}
			task.raw.task_assign_to_id = listAssignAfterCompare;
			task.raw.task_assign_to_text = listAssignTextAfterCompare;
			task.set("estimated_detail",estimatedDetail);
			task.set("task_assign_to_id",listAssignAfterCompare);
			task.set("task_assign_to_text",listAssignTextAfterCompare.join(","));
		}
		else
		{
			task.raw.task_assign_to_id = task.get("task_assign_to_id");
			task.raw.task_assign_to_text = task.get("task_assign_to_text").split(",");
		}
		//END
        if(task.raw.is_new){
            // Create task
            if(task.raw.is_new == true){
                if(task.get('parent_id') > 999999999999){
                    task.set('parent_id', 0);
                    task.set('parentId', 0);
                } 
            }else{
                // do nothing
            }
        }else{
            // Update task
            // If parent_id > 999.999.999.999, this is a task within a phase. Do set parent id for this task to 0
            // Other wise, this is a sub task, leave it as normal
            if(task.get('parent_id') > 999999999999){
                task.set('parent_id', 0);
                task.set('parentId', 0);
            }else{
                
            }
        }
		if(task.get('special')==1&&(parseFloat(task.get('consumed'))>parseFloat(task.get('estimated'))))
		{
			//console.log(editor);
			//console.log(task);
			Ext.MessageBox.show({
				title: 'ERROR',
				msg: 'Update task Failed!',
				icon: Ext.Msg.ERROR,
				buttons: Ext.Msg.OK
			});
			task.set('consumed', task.raw.consumed);
			return false;
		}
        if(callKeepDuration && getDuration && getDuration != 0){
            Ext.Msg.show({
                title: '',
                msg: i18n('Keep the duration of the task ') + getName,
                buttons: Ext.Msg.YESNO,
                fn: function(response) {
                    if(response === 'yes') {
                        task.set('keep_duration', 1);
                    } else {
                        task.set('keep_duration', 0);
                    }
                    me.handleSaveTask(me, task, tasksTree);
                }
            });
        } else {
            task.set('keep_duration', 0);
            me.handleSaveTask(me, task, tasksTree);
        }
	},
    handleSaveTask: function(me, task, tasksTree){
        me.getPmstreepanelactivity().setLoading("Please Wait");
        task.save({
            success: function(returnTask, operation) {
                var _dataJSON = JSON.parse(operation._response.responseText);
                var _json_task_return = _dataJSON.message.ActivityTask;
                if(_json_task_return.task_assign_to_id.length > 1 && _json_task_return.estimated > 0){
                    tasksTree.checkEstimated(_json_task_return, task);
                    me.getPmstreepanelactivity().refreshSummary(function(callback){
                        //me.getPmstreepanelactivity().setLoading(false);
                    });
                } else {
                    me.getPmstreepanelactivity().refreshSummary(function(callback){
                        me.getPmstreepanelactivity().setLoading(false);
                    });
                }
                if (operation.action == "create") {
                    var dataJSON = JSON.parse(operation._response.responseText);
                    var new_id = dataJSON.data.id;
                    returnTask.set('id', new_id);
                    returnTask.set('callKeepDuration', false);
                    returnTask.set('is_new', false);
                    returnTask.commit();
					me.getPmstreepanelactivity().refreshStaffing(function(callback){
						//do nothing
					});
                } else {
                    if(operation.action == "update"){
                        var dataJSON = JSON.parse(operation._response.responseText);
                        var json_task_return = dataJSON.message.ActivityTask;
                        returnTask.set(json_task_return);
                        returnTask.set('callKeepDuration', false);
                        returnTask.set('is_new', false);
                        returnTask.commit();
						if(json_task_return.task_assign_to_id.length < 2)
						{
							me.getPmstreepanelactivity().refreshStaffing(function(callback){
								//do nothing
							});
						}
                    }
                }
            },
            failure: function(returnTask, operation) {
                var error = operation.getError(),
                msg = Ext.isObject(error) ? error.status + ' ' + error.statusText : error;
                me.getPmstreepanelactivity().setLoading(false);
                returnTask.set('callKeepDuration', false);
                Ext.MessageBox.show({
                    title: 'ERROR',
                    msg: error ? error : 'Update task failed',
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
					
					
					if(task.parentNode.data.children){
						if(task.parentNode.data.children.length <= 0){
							task.parentNode.set('leaf',true);
						}
					}
					else{
						task.parentNode.set('leaf',true);
					}
					
					task.parentNode.removeChild(task, true);
        		}		
				me.getPmstreepanelactivity().cellEditingPlugin.startEdit(task, 0);	
				me.getPmstreepanelactivity().setLoading(false);
            }
        });
    },
    // Button Add Task
	handleCancelEdit: function(e, context, opt){
		var task = context.record,
            parent = task.parentNode;
        if( task.get('is_new') ){
            var arrTmp = [];
            $.each(parent.data.children, function(key, value){
                if( parent.data.children[key].id != task.get('id') ){
                    arrTmp.push(parent.data.children[key]);
                }
            });
            parent.data.children = arrTmp;
            parent.removeChild(task, true);
            //parent.set('leaf', arrTmp.length ? false : true);
        } else {
            //RESET ASSIGN TO
            listAssignText = task.raw.task_assign_to_text;
            if( typeof listAssignText == 'string' )
                task.set('task_assign_to_text', listAssignText);
            else task.set('task_assign_to_text', listAssignText.join(','));
            task.set('is_profit_center', task.raw.is_profit_center);
        }
	},
	// Button Add Task
	handleAddTask:function(){
		var me = this;        
		var tasksTree 			= this.getPmstreepanelactivity(),
			cellEditingPlugin   = tasksTree.cellEditingPlugin, // for double click
			selectionModel 		= tasksTree.getSelectionModel(),
			selectedTask 		= selectionModel.getSelection()[0],
			parentTask 			= selectedTask;
            
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
        var today = new Date();
		var newTask = Ext.create('PMS.model.ActivityTask', {
			task_title 	: i18n("New Task"),
			loaded 		: true,
            expanded    : true,
			leaf 		: true,
			parent_id 	: parseInt(parentTask.get('id')),
            parent_name : parentTask.get('task_title'),
			activity_id : tasksTree.activity_id,
            is_new      : true,
            special     : 0,
            task_start_date: today
		});
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

        if(!parentTask.data.children||parentTask.data.children=='null'){
            parentTask.data.children = [];
        }

        //find the task with id = 999999999999 (previous tasks)
        if( (prev = parentTask.findChild('id', '999999999999')) != null ){
            //dont use appendChild, use insertBefore
            parentTask.insertBefore(newTask, prev);
            //insert before the last node (prev)
            var len = parentTask.data.children.length - 1; //len-1 = last node
            parentTask.data.children.splice(len, 0, newTask.data);
        } else {
            parentTask.appendChild(newTask);
            parentTask.data.children.push(newTask.data);
        }
		//END

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
            taskTree = me.getPmstreepanelactivity(),
            taskName = task.get('task_title'),
            selModel = taskTree.getSelectionModel(),
            tasksStore = me.getActivityTasksStore();
        if(task.get('consumed') > 0){  
            Ext.Msg.show({
                title: 'ERROR',
                msg: 'This task is in used/ has consumed',
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
                    taskTree.setLoading("Please Wait");
                    $.ajax({
                        url: '/activity_tasks/destroyTaskJson/' + task.get('id'),
                        data: {
                            data:{
                                task_title: task.get('task_title'),
                                activity_id: task.get('activity_id')
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
									if(task.parentNode.data.children[key].id!=task.data.id)
									{
										arrTmp.push(task.parentNode.data.children[key]);
									}
								});
								task.parentNode.data.children=arrTmp;
								
								
								if(task.parentNode.data.children){
									if(task.parentNode.data.children.length <= 0){
										task.parentNode.set('leaf',true);
									}
								}
								else{
									task.parentNode.set('leaf',true);
								}
								
								task.parentNode.removeChild(task, true);
                                taskTree.refreshSummary(function(callback){
                                    taskTree.setLoading(false);
                                });
                            }
							me.getPmstreepanelactivity().refreshStaffing(function(callback){
								//do nothing
							});
                        }
                   });
                   // refresh the list view so the task counts will be accurate
                   taskTree.refreshView();
                }
            }
        });
    },

    handleDeleteClick:function(component, e){
        this.deleteTask(this.getPmstreepanelactivity().getSelectionModel().getSelection()[0]);

    },

    showContextMenu: function(view, task, node, rowIndex, e) {  
		this.getPmstreepanel().getSelectionModel().select(rowIndex);
        var contextMenu = this.getContextMenu(),
            newListItem = Ext.getCmp('new-task-item'),
            deleteListItem = Ext.getCmp('delete-task-item'),
            specialItem = Ext.getCmp('new-special-task'),
            editName = Ext.getCmp('edit-task-name'),
            edit = Ext.getCmp('edit-task');
            specialItem.hide();
            edit.hide();
            editName.hide();
		if(task.get('special')==1)
		{
			newListItem.hide();
            specialItem.hide();
			e.preventDefault();
            edit.show();
			return;
		}
        // if is root
        if(task.get('id') == 'root'){
            newListItem.hide();
            deleteListItem.hide();
            e.preventDefault();
            return;
        }else{
            if(task.get('is_previous') == "true"){
                newListItem.hide();
                deleteListItem.hide();
                e.preventDefault();
                return;
            }
            if(task.get('is_phase') == "true"){
                
                if(task.get('is_activity') == "true"){
                    newListItem.hide();
                    deleteListItem.hide();
                    e.preventDefault();
                    return;
                }else{
                    newListItem.show();
                    specialItem.show();
                    deleteListItem.hide();
                }
                
            } else {
                if(task.parentNode){
                    if(task.parentNode.get('is_phase') == "true"){
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
                        } else if(task.get('is_predecessor') == "true"){
                            // huu them vao 30/10/2013
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
                }else{
                    // nothing to do 
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
                url: '/activity_tasks/saveEstimatedDetail/',
                success: function(_form, action) {
                    var dataJSON = JSON.parse(action.response.responseText);
                    var _references = dataJSON.message;
                    var totalEsti = parseFloat(dataJSON.total);
                    var totalRefen = 0;
                    var submited_estimated_returned = [];
                    var checkNegative = false;
                    for(var i = 0; i < _references.length; i++){
                        if(_references[i].ActivityTaskEmployeeRefer.estimated < 0){
                            checkNegative = true;
                        }
                        submited_estimated_returned.push(_references[i].ActivityTaskEmployeeRefer.estimated);
                        totalRefen += parseFloat(_references[i].ActivityTaskEmployeeRefer.estimated);
                    }
                    button.task.set("estimated_detail", submited_estimated_returned);
                    button.task.save({
                        scope:me,
                        success: function(returnTask, operation){
                            me.getPmstreepanelactivity().refreshView();
                            button.task = returnTask;
                        }
                    });
                    
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
                            Ext.Msg.alert('Success', 'Estimated saved successfully.');
                            button.up('.window').close();  
                        } else {
                            var _msg = 'The total values input ' + totalRefen + ' not equal to the total values estimated ' + totalEsti;
                            Ext.MessageBox.show({
                                title   : 'Save Failed',
                                msg     : _msg,
                                icon    : Ext.Msg.ERROR,
                                buttons : Ext.Msg.OK
                            });
                           return false;
                        }
                    }
					me.getPmstreepanelactivity().refreshStaffing(function(callback){
						//do nothing
					});
                },
                failure: function(_form, action) {
                    Ext.Msg.alert('Failed', 'Estimated saved not successfully.');
                }
            });
        }
    },
    handleAddSpecial: function(){
        var me = this.getPmstreepanelactivity(),
            selectionModel = me.getSelectionModel(),
            selectedTask = selectionModel.getSelection()[0];
        if( selectedTask.get('is_phase') == "true" ){
            $('#special-task-info').data('tree', me);
            Task = new SpecialTask({
                columns: [],
                data: {},
                task: {
                    id: 0,
                    task_title: '',
                    task_priority_id: '',
                    task_status_id: '',
                    task_start_date: '',
                    task_end_date: '',
                    activity_id : me.activity_id
                },
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
        var tree = this.getPmstreepanelactivity(),
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
                                    url: Azuree.root + 'activity_tasks/saveName',
                                    type: 'POST',
                                    data: {
                                        data: {
                                            id: task.get('id'),
                                            task_title: textField.getValue()
                                        }
                                    },
                                    dataType: 'json',
                                    success: function(data){
                                        task.set('task_title', data.name);
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