var vv = '';
// Ext.override(Ext.grid.RowEditor, {
//  getToolTip: function() {
//      var me = this,
//          tip;

//      if (!me.tooltip) {

//          tip = me.tooltip = Ext.createWidget('tooltip', {
//              cls: Ext.baseCSSPrefix + 'grid-row-editor-errors',
//              title: me.errorsText,
//              autoHide: false,
//              closable: true,
//              closeAction: 'disable',
//              //width: 200,
//              anchor: 'bottom'
//          });
//      }
//      return me.tooltip;
//  }
// });

var listTaskHasLoad  = [];
var listAssignTos = {};
var debug = '';
// Add the additional 'advanced' VTypes
Ext.apply(Ext.form.field.VTypes, {
    daterange: function(val, field) {
        var date = field.parseDate(val);

        if (!date) {
            this.dateRangeMax = false;
            this.dateRangeMin = false;
            return false;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            // Set start date is note larger than end day
            var start = field.up('panel').down('#' + field.startDateField);
            start.setMaxValue(date);
            //start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            // Set end date is not larger than start day
            var end = field.up('panel').down('#' + field.endDateField);
            end.setMinValue(date);
             //end.validate();
            this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    },

    daterangeText: 'Start date must be less than end date'
});
Ext.define('PMS.view.TreePanel', {
    extend: 'Ext.tree.Panel',
    alias: 'widget.pmstreepanel',
    requires: [
        'Ext.tree.plugin.TreeViewDragDrop',
        'Ext.grid.column.Action',
        'PMS.ux.DragDrop'
    ],
    bufferedRenderer: false,
    monitorResize: true,
    rootVisible: false,
    store: 'ProjectTasks',
    forceFit: false,
    viewConfig: {
            plugins: [{
                    ptype: 'tasksdragdrop',
                    dragText: i18n('Move Here'),
                    ddGroup: 'task'
                },
                {
                    ptype: 'azureehistory'
                }
            ],
            toggleOnDblClick: false,
            listeners: {
                beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts){
                    if( !canModify )return false;
                    var temp=0;
                    var dcn=data.records[0].data; //dcn = data of current node
                    var don=overModel.data; //don = data of over node
                    if( dcn.is_nct == 1 && don.is_phase != 'true' && dropPosition == 'append'){
                        dropHandlers.cancelDrop();
                        Ext.Msg.show({
                            title: 'Moving Project Task',
                            msg: "NC-Task can only be moved under a phase",
                            buttons: Ext.Msg.OK
                        });
                        return false;
                    }
                    if( don.is_nct == 1 && dropPosition == 'append' ){
                        dropHandlers.cancelDrop();
                        Ext.Msg.show({
                            title: 'Moving Project Task',
                            msg: "Can not move this task to a NC-Task",
                            buttons: Ext.Msg.OK
                        });
                        return false;
                    }
                    if(dcn.special==1){
                        dropHandlers.cancelDrop();
                        Ext.Msg.show({
                            title: 'Moving Project Task',
                            msg: "Cannot move auto-task",
                            buttons: Ext.Msg.OK
                        });
                        return false;
                    }
                    if(don.special==1){
                        dropHandlers.cancelDrop();
                        Ext.Msg.show({
                            title: 'Moving Project Task',
                            msg: "Cannot move task to this auto-task",
                            buttons: Ext.Msg.OK
                        });
                        return false;
                    }
                    //return false;
                    if(dcn.is_part||dcn.is_phase){
                        dropHandlers.cancelDrop();
                        Ext.Msg.show({
                            title: ' ',
                            msg: i18n('Move part and phase in the screens part and phase'),
                            buttons: Ext.Msg.OK
                        });
                        return false;
                    } else {
                        if(don.is_part){
                            dropHandlers.cancelDrop();
                            Ext.Msg.show({
                                title: 'Moving Project Task',
                                msg: "Don't move task to part.",
                                buttons: Ext.Msg.OK
                            });
                            return false;
                        }
                        // else if(don.is_phase && dropPosition !== 'append')
                        // {
                        //  dropHandlers.cancelDrop();
                        //  Ext.Msg.show({
                        //      title: 'Moving Project Task',
                        //      msg: "Don't move task to part.",
                        //      buttons: Ext.Msg.OK
                        //  });
                        //  return false;
                        // }
                        else{
                            if(dcn.predecessor){
                                dropHandlers.cancelDrop();
                                Ext.Msg.show({
                                    title: 'Moving Project Task',
                                    msg: 'The task containing predecessor.',
                                    buttons: Ext.Msg.OK
                                });
                                return false;
                            } else if((dcn.consumed==''||dcn.consumed==0)||(dcn.wait==''||dcn.wait==0)){
                                if(don.is_phase){
                                    //do nothing
                                    data.records[0].set('leaf',false);
                                    data.records[0].set('children',[]);
                                    data.records[0].set('loaded',true);
                                    data.records[0].set('expanded',true);
                                    temp=1;
                                } else {
                                    if(dcn.children && dcn.children.length != 0){
                                        dropHandlers.cancelDrop();
                                        Ext.Msg.show({
                                            title: 'Moving Project Task',
                                            msg: "Task have subtasks don't move to task.",
                                            buttons: Ext.Msg.OK
                                        });
                                        return false;
                                    } else if(dropPosition === 'append'){
                                        if(don.children.length == 0 && don.consumed!=0){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: 'This task is in used/ has consumed.',
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else if(don.children.length == 0 && don.wait!=0){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: 'This task is in used/ has consumed.',
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else if((don.children.length == 0 && don.wait!=0)||(don.children.length == 0 && don.wait!='')){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: 'This task is in used/ has consumed.',
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else {
                                            if(dcn.children && dcn.children.length != 0){
                                                dropHandlers.cancelDrop();
                                                Ext.Msg.show({
                                                    title: 'Moving Project Task',
                                                    msg: "Tasks have subtasks don't move to task.",
                                                    buttons: Ext.Msg.OK
                                                });
                                                return false;
                                            } else {
                                                dcn.leaf = true;
                                            }
                                        }
                                    }
                                    else{
                                        if(don.leaf){
                                            dcn.leaf = true;
                                        } else {
                                            data.records[0].set('leaf',false);
                                            data.records[0].set('children',[]);
                                            data.records[0].set('loaded',true);
                                            data.records[0].set('expanded',true);
                                            temp=1;
                                        }
                                    }
                                }
                            }
                            else{
                                if(dcn.children && dcn.children.length != 0){
                                    if(don.is_phase){
                                        data.records[0].set('leaf',false);
                                        data.records[0].set('children',[]);
                                        data.records[0].set('loaded',true);
                                        data.records[0].set('expanded',true);
                                        temp=1;
                                    } else {
                                        dropHandlers.cancelDrop();
                                        Ext.Msg.show({
                                            title: 'Moving Project Task',
                                            msg: "Task have subtasks don't move to task.",
                                            buttons: Ext.Msg.OK
                                        });
                                        return false;
                                    }
                                } else {
                                    if(dropPosition === 'append') {
                                        if(don.children && don.children.length == 0 && don.consumed!=0){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: "This task is in used/ has consumed.",
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else if(don.children && don.children.length == 0 && don.wait!=0){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: "This task is in used/ has consumed.",
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else if((don.children.length == 0 && don.wait!=0)||(don.children.length == 0 && don.wait!='')){
                                            dropHandlers.cancelDrop();
                                            Ext.Msg.show({
                                                title: 'Moving Project Task',
                                                msg: 'This task is in used/ has consumed.',
                                                buttons: Ext.Msg.OK
                                            });
                                            return false;
                                        } else {
                                            if(don.is_phase){
                                                data.records[0].set('leaf',false);
                                                data.records[0].set('children',[]);
                                                data.records[0].set('loaded',true);
                                                data.records[0].set('expanded',true);
                                                temp=1;
                                            } else {
                                                dcn.leaf = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if(dropPosition!=='append') {
                        var pon=overModel.parentNode; //pon = parent of over node
                    } else {
                        var pon=overModel; //pon = parent of over node
                    }
                    var pcn=data.records[0].parentNode; //pcn = parent of current node
                    //check name task and reset children
                    if(pcn.internalId!=pon.internalId) {
                        if(pon.data.children&&pon.data.children.length>0) {
                            var flag=0;
                            $.each(pon.data.children, function(key,child){
                                if(child.task_title==dcn.task_title) {
                                    flag=1;
                                }
                            })
                        }
                        if(flag==1) {
                            if(temp==1) data.records[0].set('leaf',true);

                            dropHandlers.cancelDrop();
                            Ext.Msg.show({
                                title: '',
                                msg: i18n("The task name already exists"),
                                buttons: Ext.Msg.OK
                            });
                            return false;
                        } else {
                            //reset children & childNodes
                            pon.appendChild(data.records[0]);
                            if(!pon.data.children||pon.data.children=='null')
                            pon.data.children=[];
                            pon.data.children.push(dcn);
                            //pon.childNodes.push(dcn);

                            var arrTmp=new Array();
                            $.each(pcn.data.children,function(key,value){
                                if(pcn.data.children[key].id!=dcn.id)
                                {
                                    arrTmp.push(pcn.data.children[key]);
                                }
                            });
                            pcn.data.children=arrTmp;
                            pcn.removeChild(data.records[0], true);
                        }
                    }
                },
                drop: function (node, data, overModel, dropPosition) {
                    this.panel.setLoading(i18n("Please wait"));
                    var dcn=data.records[0].data; //don = data of current node
                    var don=overModel.data; //don = data of over node
                    if(don.is_phase) {
                        overNodeIsPhase = 1;
                        idOverNode=don.phase_id;
                    } else {
                        overNodeIsPhase = 0;
                        idOverNode=don.id;
                    }
                    Ext.Ajax.request({
                        scope: this,
                        url: '/project_tasks/drapDrop/',
                        method: 'POST',
                        params: {
                            idCurrentNode : dcn.id,
                            idOverNode : idOverNode,
                            type : dropPosition,
                            overNodeIsPhase : overNodeIsPhase
                        },
                        failure: function (response, opts) {
                            //console.log(response);
                        },
                        success:function(response, opts) {
							if(response){
                            var dataJSON = JSON.parse(response.responseText);
                            //*******
                            this.panel.clearFilter();
                            this.panel.handleDnD(dataJSON);
                            this.panel.applyFilters();
                            this.panel.setLoading(false);
                            return;
                            var me=this;
                            //var arrData=response.responseText;
                            // location.reload();
                            datas = [];
                            var cons = dataJSON.consumed;
                            var estimated = dataJSON.estimated;
                            var overload = dataJSON.overload;
                            var remain = dataJSON.remain;
                            var wait = dataJSON.wait;
                            var startDate = dataJSON.task_start_date;
                            var endDate = dataJSON.task_end_date;
                            var duration = dataJSON.duration;
                            var idActivity = dataJSON.id_activity;
                            var unit_price = dataJSON.unit_price;
                            var estimated_euro = dataJSON.estimated_euro;
                            var consumed_euro = dataJSON.consumed_euro;
                            var remain_euro = dataJSON.remain_euro;
                            var workload_euro = dataJSON.workload_euro;

                            /*var _totalPrvious = _totalEstimatedPr = 0;
                            if(dataJSON.children){
                                for(i = 0; i < dataJSON.children.length; i++){
                                    if(dataJSON.children[i].is_activity == 'true'){
                                        _totalPrvious = dataJSON.children[i].consumed;
                                        _totalEstimatedPr = dataJSON.children[i].estimated;
                                    }
                                }
                                cons = cons + parseFloat(_totalPrvious);
                                cons = cons.toFixed(2);
                                if(_totalEstimatedPr == null || _totalEstimatedPr == ''){
                                    _totalEstimatedPr = 0;
                                }
                            }*/

                            var completed, consume = is_manual_consumed ? dataJSON.manual_consumed : cons, over = is_manual_consumed ? overload : dataJSON.manual_overload;
                            if(estimated == 0 || consume == 0){
                                completed = 0 + '%';
                            } else {
                                var _caculate = ((consume * 100)/(estimated+over)).toFixed(2);
                                if(_caculate > 100){
                                    completed = 100 + '%';
                                } else {
                                    completed = _caculate + '%';
                                }
                            }

                            me.storeConsumed = cons;
                            me.storeEstimated = estimated;
                            me.storeOverload = overload;
                            me.storeRemain = remain;
                            me.storeWait = wait;
                            me.storeStartDate = startDate;
                            me.storeEndDate = endDate;
                            me.storeCompleted = completed;
                            me.storeDuration = duration;
                            me.storeIdActivity = idActivity;
                            me.storeUnitPrice = unit_price;
                            me.storeEstimatedEuro = estimated_euro;
                            me.storeConsumedEuro = consumed_euro;
                            me.storeRemainEuro = remain_euro;
                            me.storeWorkloadEuro = workload_euro;

                            me.node.childNodes[0].set('estimated',me.storeEstimated);
                            me.node.childNodes[0].set('overload',me.storeOverload);
                            me.node.childNodes[0].set('manual_overload',dataJSON.manual_overload);
                            me.node.childNodes[0].set('consumed',me.storeConsumed);
                            me.node.childNodes[0].set('manual_consumed',dataJSON.manual_consumed);
                            me.node.childNodes[0].set('remain',me.storeRemain);
                            me.node.childNodes[0].set('wait',me.storeWait);
                            me.node.childNodes[0].set('task_start_date',me.storeStartDate);
                            me.node.childNodes[0].set('task_end_date',me.storeEndDate);
                            me.node.childNodes[0].set('completed',me.storeCompleted);
                            me.node.childNodes[0].set('duration',me.storeDuration);
                            me.node.childNodes[0].set('id_activity',me.storeIdActivity);
                            me.node.childNodes[0].set('amount', dataJSON.amount);
                            me.node.childNodes[0].set('progress_order', dataJSON.progress_order);
                            me.node.childNodes[0].set('progress_order_amount', dataJSON.progress_order_amount);
                            me.node.childNodes[0].set('unit_price', dataJSON.unit_price);
                            me.node.childNodes[0].set('estimated_euro', dataJSON.estimated_euro);
                            me.node.childNodes[0].set('consumed_euro', dataJSON.consumed_euro);
                            me.node.childNodes[0].set('remain_euro', dataJSON.remain_euro);
                            me.node.childNodes[0].set('workload_euro', dataJSON.workload_euro);


                            me.node.set('estimated',me.storeEstimated);
                            me.node.set('overload',me.storeOverload);
                            me.node.set('consumed',me.storeConsumed);
                            me.node.set('manual_overload',dataJSON.manual_overload);
                            me.node.set('manual_consumed',dataJSON.manual_consumed);
                            me.node.set('remain',me.storeRemain);
                            me.node.set('wait',me.storeWait);
                            me.node.set('task_start_date',me.storeStartDate);
                            me.node.set('task_end_date',me.storeEndDate);
                            me.node.set('completed',me.storeCompleted);
                            me.node.set('duration',me.storeDuration);
                            me.node.set('id_activity',me.storeIdActivity);
                            me.node.set('unit_price',me.storeUnitPrice);
                            me.node.set('estimated_euro',me.storeEstimatedEuro);
                            me.node.set('consumed_euro',me.storeConsumedEuro);
                            me.node.set('remain_euro',me.storeRemainEuro);
                            me.node.set('workload_euro',me.storeWrokloadEuro);

                            var $_datas = [];
                            for (var i = 0; i < dataJSON.children.length; i++) {
                                $_datas[i] = {
                                    task_start_date:dataJSON.children[i].task_start_date,
                                    task_end_date: dataJSON.children[i].task_end_date,
                                    estimated: dataJSON.children[i].estimated,
                                    overload: dataJSON.children[i].overload,
                                    remain: dataJSON.children[i].remain,
                                    wait: dataJSON.children[i].wait,
                                    completed: dataJSON.children[i].completed,
                                    duration: dataJSON.children[i].duration,
                                    consumed: dataJSON.children[i].consumed,
                                    manual_consumed: dataJSON.children[i].manual_consumed,
                                    manual_overload: dataJSON.children[i].manual_overload,
                                    unit_price: dataJSON.children[i].unit_price,
                                    estimated_euro: dataJSON.children[i].estimated_euro,
                                    consumed_euro: dataJSON.children[i].consumed_euro,
                                    remain_euro: dataJSON.children[i].remain_euro,
                                    workload_euro: dataJSON.children[i].workload_euro,
                                };
                            }
                            for(var i = 0; i < me.node.childNodes[0].childNodes.length; i++) {
                                var _st = $_datas[i].task_start_date;
                                var _end = $_datas[i].task_end_date;
                                var _estimated =  $_datas[i].estimated;
                                var _overload =  $_datas[i].overload;
                                var _remain =  $_datas[i].remain;
                                var _wait =  $_datas[i].wait;
                                var _completed = $_datas[i].completed;
                                var _duration = $_datas[i].duration;
                                var _consumed = $_datas[i].consumed;
                                var _manual_consumed = $_datas[i].manual_consumed;
                                me.node.childNodes[0].childNodes[i].set("task_start_date", _st);
                                me.node.childNodes[0].childNodes[i].set("task_end_date", _end);
                                me.node.childNodes[0].childNodes[i].set("estimated", _estimated);
                                me.node.childNodes[0].childNodes[i].set("overload", _overload);
                                me.node.childNodes[0].childNodes[i].set("remain", _remain);
                                me.node.childNodes[0].childNodes[i].set("wait", _wait);
                                me.node.childNodes[0].childNodes[i].set("completed", _completed);
                                me.node.childNodes[0].childNodes[i].set("duration", _duration);
                                me.node.childNodes[0].childNodes[i].set("consumed", _consumed);
                                me.node.childNodes[0].childNodes[i].set("manual_consumed", _manual_consumed);
                                me.node.childNodes[0].childNodes[i].set("manual_overload", $_datas[i].manual_overload);
                                me.node.childNodes[0].childNodes[i].set('amount', $_datas[i].amount);
                                me.node.childNodes[0].childNodes[i].set('progress_order', $_datas[i].progress_order);
                                me.node.childNodes[0].childNodes[i].set('progress_order_amount', $_datas[i].progress_order_amount);
                                me.node.childNodes[0].childNodes[i].set('unit_price', $_datas[i].unit_price);
                                me.node.childNodes[0].childNodes[i].set('estimated_euro', $_datas[i].estimated_euro);
                                me.node.childNodes[0].childNodes[i].set('consumed_euro', $_datas[i].consumed_euro);
                                me.node.childNodes[0].childNodes[i].set('remain_euro', $_datas[i].remain_euro);
                                me.node.childNodes[0].childNodes[i].set('workload_euro', $_datas[i].workload_euro);
                            }
                            var _arg = [];
                            for (var i = 0; i < dataJSON.children.length; i++) {
                                if(dataJSON.children[i].children){
                                    for(var j = 0; j < dataJSON.children[i].children.length; j++) {
                                        z=j;
                                        //for(var z=0; z < me.node.childNodes[0].childNodes[i].childNodes.length; z++){
                                            //if(me.node.childNodes[0].childNodes[i].childNodes[z].internalId==dataJSON.children[i].children[j].id){
                                                //refresh task
                                                if(dataJSON.children[i].children[j].task_start_date != '0000-00-00'){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("task_start_date", dataJSON.children[i].children[j].task_start_date);
                                                }
                                                if(dataJSON.children[i].children[j].task_end_date != '0000-00-00'){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("task_end_date", dataJSON.children[i].children[j].task_end_date);
                                                }
                                                if(dataJSON.children[i].children[j].duration){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("duration", dataJSON.children[i].children[j].duration);
                                                }
                                                //if(dataJSON.children[i].children[j].remain){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("remain", dataJSON.children[i].children[j].remain);
                                                //}
                                                //if(dataJSON.children[i].children[j].wait){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("wait", dataJSON.children[i].children[j].wait);
                                                //}
                                                //if(dataJSON.children[i].children[j].consumed){
                                                me.node.childNodes[0].childNodes[i].childNodes[z].set("consumed", dataJSON.children[i].children[j].consumed);
                                                me.node.childNodes[0].childNodes[i].childNodes[z].set("manual_consumed", dataJSON.children[i].children[j].manual_consumed);
                                                me.node.childNodes[0].childNodes[i].childNodes[z].set("manual_overload", dataJSON.children[i].children[j].manual_overload);

                                                if(dataJSON.children[i].children[j].amount){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("amount", dataJSON.children[i].children[j].amount);
                                                }
                                                if(dataJSON.children[i].children[j].progress_order){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("progress_order", dataJSON.children[i].children[j].progress_order);
                                                }
                                                if(dataJSON.children[i].children[j].progress_order_amount){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("progress_order_amount", dataJSON.children[i].children[j].progress_order_amount);
                                                }
                                                if(dataJSON.children[i].children[j].unit_price){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("unit_price", dataJSON.children[i].children[j].unit_price);
                                                }
                                                if(dataJSON.children[i].children[j].estimated_euro){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("estimated_euro", dataJSON.children[i].children[j].estimated_euro);
                                                }
                                                if(dataJSON.children[i].children[j].consumed_euro){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("consumed_euro", dataJSON.children[i].children[j].consumed_euro);
                                                }
                                                if(dataJSON.children[i].children[j].remain_euro){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("remain_euro", dataJSON.children[i].children[j].remain_euro);
                                                }
                                                if(dataJSON.children[i].children[j].workload_euro){
                                                    me.node.childNodes[0].childNodes[i].childNodes[j].set("workload_euro", dataJSON.children[i].children[j].workload_euro);
                                                }
                                                //}
                                                if(dataJSON.children[i].children[j].weight){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("weight", dataJSON.children[i].children[j].weight);
                                                }
                                                //if(dataJSON.children[i].children[j].completed != '0%'){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("completed", dataJSON.children[i].children[j].completed);
                                                //}
                                                if(dataJSON.children[i].children[j].estimated){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("estimated", dataJSON.children[i].children[j].estimated);
                                                }
                                                if(dataJSON.children[i].children[j].overload){
                                                    me.node.childNodes[0].childNodes[i].childNodes[z].set("overload", dataJSON.children[i].children[j].overload);
                                                }
                                                me.node.childNodes[0].childNodes[i].childNodes[z].set("predecessor", dataJSON.children[i].children[j].predecessor);

                                                me.node.childNodes[0].childNodes[i].childNodes[z].set("is_predecessor", dataJSON.children[i].children[j].is_predecessor);
                                                // refresh sub-task
                                                if(dataJSON.children[i].children[j].children)
                                                {
                                                    for(var x = 0; x < dataJSON.children[i].children[j].children.length; x++)
                                                    {
                                                        t=x;
                                                        //for(var t=0; t < me.node.childNodes[0].childNodes[i].childNodes[j].childNodes.length; t++)
                                                        //{
                                                            //if(me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].internalId==dataJSON.children[i].children[j].children[x].id)
                                                            //{
                                                                if(dataJSON.children[i].children[j].children[x].task_start_date != '0000-00-00'){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("task_start_date", dataJSON.children[i].children[j].children[x].task_start_date);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[t].task_end_date != '0000-00-00'){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("task_end_date", dataJSON.children[i].children[j].children[x].task_end_date);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[t].duration){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("duration", dataJSON.children[i].children[j].children[x].duration);
                                                                }
                                                                //if(dataJSON.children[i].children[j].children[t].consumed){
                                                                me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("consumed", dataJSON.children[i].children[j].children[x].consumed);
                                                                me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("manual_consumed", dataJSON.children[i].children[j].children[x].manual_consumed);
                                                                me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("manual_overload", dataJSON.children[i].children[j].children[x].manual_overload);

                                                                if(dataJSON.children[i].children[j].children[x].amount){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("amount", dataJSON.children[i].children[j].children[x].amount);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].progress_order){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("progress_order", dataJSON.children[i].children[j].children[x].progress_order);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].progress_order_amount){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("progress_order_amount", dataJSON.children[i].children[j].children[x].progress_order_amount);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].unit_price){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("unit_price", dataJSON.children[i].children[j].children[x].unit_price);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].estimated_euro){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("estimated_euro", dataJSON.children[i].children[j].children[x].estimated_euro);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].consumed_euro){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("consumed_euro", dataJSON.children[i].children[j].children[x].consumed_euro);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].remain_euro){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("remain_euro", dataJSON.children[i].children[j].children[x].remain_euro);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[x].workload_euro){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("workload_euro", dataJSON.children[i].children[j].children[x].workload_euro);
                                                                }
                                                                //}
                                                                if(dataJSON.children[i].children[j].children[t].weight){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("weight", dataJSON.children[i].children[j].children[x].weight);
                                                                }
                                                                if(dataJSON.children[i].children[j].children[t].estimated){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("estimated", dataJSON.children[i].children[j].children[x].estimated);
                                                                }
                                                                //if(dataJSON.children[i].children[j].children[t].remain){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("remain", dataJSON.children[i].children[j].children[x].remain);
                                                                //}
                                                                //if(dataJSON.children[i].children[j].children[t].wait){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("wait", dataJSON.children[i].children[j].children[x].wait);
                                                                //}
                                                                //if(dataJSON.children[i].children[j].children[t].completed != '0%'){
                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("completed", dataJSON.children[i].children[j].children[x].completed);
                                                                //}
                                                                me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("predecessor", dataJSON.children[i].children[j].children[x].predecessor);
                                                                me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[t].set("is_predecessor", dataJSON.children[i].children[j].children[x].is_predecessor);
                                                                //check number order for sub-task
                                                                if(dataJSON.children[i].children[j].children[x].children)
                                                                {
                                                                    for(var k = 0; k < dataJSON.children[i].children[j].children[x].children.length; k++)
                                                                    {
                                                                        for(var h=0; h < me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes.length; h++)
                                                                        {
                                                                            if(me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[h].internalId==dataJSON.children[i].children[j].children[x].children[k].id)
                                                                            {
                                                                                if(dataJSON.children[i].children[j].children[x].children[k].weight){
                                                                                    me.node.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[h].set("weight", dataJSON.children[i].children[j].children[x].children[k].weight);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            //}
                                                        //}
                                                    }
                                                }
                                            //}
                                        //}
                                    }
                                }
                            }
							}
                            this.setLoading(false);
                        }
						
                    });
                    //var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
                }
                //END
            }
    },

    developerMode: false,
    timeouts:[],
    // start 21/10/2013 huythang: add code
    validateDateTimeFocus:function(val, field, predec, _data){

        var date = field.parseDate(val);
        if (!date) {
            field.setMaxValue('1-1-2031');
            field.setMinValue('1-1-2010');
            return false;
        }

        var dateParent = '';
        if(predec != ''){
            //part
            $.each(_data, function(index, values){
                if(values.childNodes){
                    //phase
                    $.each(values.childNodes, function(keys, value){
                        if(value.childNodes){
                            //task
                            $.each(value.childNodes, function(key, vals){
                                if(vals.childNodes){
                                    //sub-task
                                    $.each(vals.childNodes, function(k, val){
                                        if(val.data.id == predec){
                                            dateParent = val.data.task_end_date;
                                        }
                                    });
                                }
                                if(vals.data.id == predec){
                                    dateParent = vals.data.task_end_date;
                                }
                            });
                        }
                        if(value.data.id == predec){
                            dateParent = value.data.task_end_date;
                        }
                    });
                }
                if(values.data.id == predec){
                    dateParent = values.data.task_end_date;
                }
            });
        }
        if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            // Set end date is not larger than start day
            var end = field.up('panel').down('#' + field.endDateField);
            end.setMinValue(date);
            if(dateParent != ''){
                var start = field.up('panel').down('#' + field.id);

                var day = dateParent.getDate();
                var month = dateParent.getMonth();
                var year = dateParent.getFullYear();

                var d = new Date(year, month, day + 1, 0, 0, 0, 0);
                start.setMinValue(d);
            }else{
                field.setMaxValue('1-1-2031');
                field.setMinValue('1-1-2010');
            }
        } else if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            // Set start date is note larger than end day
            var end = field.up('panel').down('#enddt');

            // var day = date.getDate();
            // var month = date.getMonth();
            // var year = date.getFullYear();

            // var d = new Date(year, month, day + 1, 0, 0, 0, 0);
            end.setMinValue(date);
        }

        return true;
    },

    validateDateTime:function(val, field, predec, _data){

        var date = field.parseDate(val);
        if (!date) {
            field.setMaxValue('1-1-2031');
            field.setMinValue('1-1-2010');
            return false;
        }
        var dateParent = '';
        if(predec != ''){
            //part
            $.each(_data, function(index, values){
                if(values.childNodes){
                    //phase
                    $.each(values.childNodes, function(keys, value){
                        if(value.childNodes){
                            //task
                            $.each(value.childNodes, function(key, vals){
                                if(vals.childNodes){
                                    //sub-task
                                    $.each(vals.childNodes, function(k, val){
                                        if(val.data.id == predec){
                                            dateParent = val.data.task_end_date;
                                        }
                                    });
                                }
                                if(vals.data.id == predec){
                                    dateParent = vals.data.task_end_date;
                                }
                            });
                        }
                        if(value.data.id == predec){
                            dateParent = value.data.task_end_date;
                        }
                    });
                }
                if(values.data.id == predec){
                    dateParent = values.data.task_end_date;
                }
            });
        }


        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            // Set start date is note larger than end day
            // var start = field.up('panel').down('#' + field.startDateField);
            // start.setMaxValue(date);
            //start.validate();
            //this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            // Set end date is not larger than start day
            var end = field.up('panel').down('#' + field.endDateField);
            end.setMinValue(date);

            var start = field.up('panel').down('#startdt');
            var duration = field.up('panel').down('#txtduration');
            var numDuration = parseInt(duration.value.toString());
            if (start.getValue() > end.getValue()) {
                var myDate = date;
                // numDuration = numDuration - 1;
                if (numDuration != null) {
                    numDuration = numDuration - 1;
                    myDate.setDate(date.getDate());
                    while (numDuration > 0){
                        myDate.setDate(myDate.getDate() + 1);
                        if (myDate.getDay() == 0 || myDate.getDay() == 6) {
                            // do nothing
                        } else {
                            numDuration -= 1;
                        }
                    }
                } else {
                    myDate.setDate(date.getDate());
                }

                end.setValue(myDate);
            }

            // if(dateParent != ''){
            //     var start = field.up('panel').down('#' + field.id);
            //     start.setMinValue(dateParent);
            // }
             //end.validate();
            //this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    },

    initComponent:function(){
        var me = this;
        me.saveLoadTasks = new Array();

        me.allowReloadManDay = true;

        me.curentTaskLoadManDay = false;

        me.storePriority =  Ext.create('Ext.data.Store',{
            autoLoad    : true,
            fields      : [
                {name: 'task_priority_id'},
                {name: 'task_priority_text', type: 'string'}
            ],
            data        : [{task_priority_id: '1',    task_priority_text: 'Start'}]
        });

        me.storeStatuses =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_status_id'},
                {name: 'task_status_text', type: 'string'}
            ],
            data: [{task_status_id: '1',    task_status_text: 'Start'}]
        });

        me.storeMilestone =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'milestone_id'},
                {name: 'milestone_text', type: 'string'}
            ],
            data: [{milestone_id: '1',    milestone_text: 'Start'}]
        });

        me.storeProfiles = Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'profile_id'},
                {name: 'profile_text', type: 'string'}
            ],
            data: [{profile_id: '1',    profile_text: 'Start'}]
        });

        me.storeAssigned =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_assign_to_id'},
                {name: 'is_profit_center'},
                {name: 'capacity_on'},
                {name: 'capacity_off'},
                {name: 'capacity'},
                {name: 'listProfile'},
                {name: 'task_assign_to_text', type: 'string'},
                {name: 'is_selected'}
            ],
            data: [{task_assign_to_id: '1', is_profit_center: '1', capacity_on: '0', capacity_off: '0', capacity: '0', listProfile: 'None', task_assign_to_text: 'Start'}]
        });

        me.storeAssignedTask =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'AssignGroup'}
            ],
            data: [
                {AssignGroup: '0'}
            ]
        });

        me.storeConsumed =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'Consumed'}
            ],
            data: [{Consumed: '0'}]
        });

        me.storeWait =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'Wait'}
            ],
            data: [{Wait: '0'}]
        });

        me.storeEstimated =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'Estimated'}
            ],
            data: [{Estimated: '0'}]
        });

        me.storeManualConsumed =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'manual_consumed'}
            ],
            data: [{manual_consumed: '0'}]
        });

        me.storeOverload =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'Overload'}
            ],
            data: [{Overload: '0'}]
        });

        me.storeManualOverload =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'manual_overload'}
            ],
            data: [{manual_overload: '0'}]
        });

        me.storeIdActivity =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'id_activity'}
            ],
            data: [{id_activity: '0'}]
        });

        me.storeRemain =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'Remain'}
            ],
            data: [{Remain: '0'}]
        });

         me.storeCompleted =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'completed'}
            ],
            data: [{completed: '0'}]
        });

        me.storeDuration =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'duration'}
            ],
            data: [{duration: '0'}]
        });

        me.storeStartDate =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_start_date'}
            ],
            data: [{task_start_date: '0'}]
        });

        me.storeEndDate =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_end_date'}
            ],
            data: [{task_end_date: '0'}]
        });

        me.storeUnitPrice =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'unit_price'}
            ],
            data: [{unit_price: '0'}]
        });

        me.storeEstimatedEuro =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'estimated_euro'}
            ],
            data: [{estimated_euro: '0'}]
        });

        me.storeConsumedEuro =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'consumed_euro'}
            ],
            data: [{consumed_euro: '0'}]
        });

        me.storeRemainEuro =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'remain_euro'}
            ],
            data: [{remain_euro: '0'}]
        });


        me.storeWorkloadEuro=  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'workload_euro'}
            ],
            data: [{workload_euro: '0'}]
        });

        me.columns = me.buildTreeColumns();
        me.flag=false;
        me.plugins = [me.cellEditingPlugin = Ext.create('Ext.grid.plugin.RowEditing',{
            errorSummary : false,
            pluginId: 'rowEditing',
            listeners: {
                beforeedit: function(editor, record, opt){
                    if( !canModify )return false;
                    if(me.flag===false) {
                        editor.editor.down('#priority-editor').bindStore(me.storePriority);
                        editor.editor.down('#status-editor').bindStore(me.storeStatuses);
                        editor.editor.down('#profile-editor').bindStore(me.storeProfiles);
                        editor.editor.down('#milestone-editor').bindStore(me.storeMilestone);
                        editor.editor.down('#comboChange').bindStore(me.storeAssigned);
                        me.flag=true;
                    }
                    var dr=record.record.data; //dr=data row
                    if( record.record.get('is_part') == "true" ||  record.record.get('is_phase') == "true" || record.record.id=='root'){
                        return false; // cancel edit phase
                    }
                    // if(!record.record.isLeaf()&&dr.children&&dr.children.length!=0){
                        // return false; // cancel edit parent project
                    // }
                    //if edit a nct task
                    //open a special window
                    if( record.record.get('is_nct') == 1 ){
                        me.openNct(record.record);
                        return false;
                    }
                    me.oldWorkload = me.getWorkload(record.record);
                }
            }
        })];

        me.on('beforeedit', function(editor, record) {
            if( !canModify )return false;
            me.currentId = record.record.get('id');
            me.currentTaskName = record.record.get('task_title');
            me.currentStartDate = record.record.get('task_start_date');
            me.currentEndDate = record.record.get('task_end_date');
            me.currentWorkload = record.record.get('estimated');
            me.currentPopupSelected = false;
            me.currentPopupSelectedView = false;
            // Disable editing for consume > 0 tasks

            /* @bangvn : re-enable when finish issue with task+ */
			var dr = record.record.data; 
			var editorForm = me.getPlugin('rowEditing').editor.form.getFields().items;
			if(!record.record.isLeaf()&&dr.children&&dr.children.length!=0){
				
				$.each(editorForm, function(ind, col){
					if( col.name != 'task_title' && col.name != 'milestone_id') col.disable();
					else col.enable();
				});
				
			}else{
				if(record.record.get('special') == 1) {
					me.getPlugin('rowEditing').editor.form.findField('task_title').disable();
					me.getPlugin('rowEditing').editor.form.findField('estimated').disable();
					me.getPlugin('rowEditing').editor.form.findField('task_assign_to_id').disable();
					me.getPlugin('rowEditing').editor.form.findField('consumed').enable();
					me.getPlugin('rowEditing').editor.form.findField('profile_id').disable();
				} else {
					me.getPlugin('rowEditing').editor.form.findField('consumed').disable();
					me.getPlugin('rowEditing').editor.form.findField('profile_id').enable();
					if (record.record.get('consumed') > 0 || record.record.get('wait') > 0){
						/*
						*yeu cau moi ngay 04/02/2016: task co consume co the doi ten dc
						*/
						me.getPlugin('rowEditing').editor.form.findField('task_title').enable();
					} else {
						$.each(editorForm, function(ind, col){
							if( col.name != 'consumed'){
								col.enable();
							}else col.disable();
						});
					}
				}
			}
        });

        me.setLoading(i18n("Please wait"));

        me.listeners = {
            itemmouseenter: function(current, record, item, index, e, eOpts ){
                if( !canModify )return false;
                var me = this;
                var cell = e.getTarget(me.view.cellSelector),
                header = me.view.getHeaderByCell(cell);
                var conditionColumn = ['estimated'];
                if(me.currentPopupSelected) {
                    // do not show the pop up
                } else {
                    // show the pop up
                    if((record.data.leaf===false&&record.data.children&&record.data.children.length>0) || record.data.is_nct == 1 || record.data.task_assign_to_id  == "" || record.data.estimated == null || record.data.estimated == 0 || (jQuery.inArray(header.dataIndex,conditionColumn) == -1)){
                    } else {
                        me.currentPopupSelected = record.data;
                        var new_time_out = setTimeout(function() {
							if(me.currentPopupSelected == record.data){
								me.checkEstimated(record.data, record);
							}else{
								me.currentPopupSelected = false;
							}
						}, 3000);
                        me.new_time_out = new_time_out;
                    }
                }
            },

            itemmouseleave:function(current, record, item, index, e, eOpts ) {
                clearTimeout(me.new_time_out);
                me.currentPopupSelected = false;
            },

            beforerender: function(current, e, eOpts) {
                me.ajax_count = 0;
            },

            afterrender: function(current, e, eOpts) {
                me.requestPrioritiesDataCustom();
                var view = me.getView();
                Ext.get('loading-mask').fadeOut({remove:true});
                //if pm cannot modify then add mask
                // if(typeof canModify == 'undefined'){
                //  // ke me no
                // } else {
                //  if(!canModify){
                //          me.setLoading(i18n('Read only'));
                //          return;
                //      }
                // }
                me.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        me.cellIndex = cellIndex;
                        me.recordIndex = recordIndex;
                    }
                });
                //current.getSelectionModel().select(current.getRootNode());
                //me.doAll();
                Ext.create('Ext.tip.ToolTip', {
                    // The overall target element.
                    target: view.el,
                    // Each grid row causes its own separate show and hide.
                    delegate: '.x-icon-text',
                    // Moving within the row should not hide the tip.
                    trackMouse: true,
                    // Render immediately so that tip.body can be referenced prior to the first show.
                    renderTo: Ext.getBody(),
                    showDelay: 100,
                    dismissDelay: 0,
                    maxWidth: 300,
                    anchor: 'right',
                    listeners: {
                        // Change content dynamically depending on which element triggered the show.
                        beforeshow: function(tip){
                            if( !canModify )return false;
                            if( !Ext.isEmpty(me.cellIndex) && me.cellIndex !== -1 ){
                                header = me.headerCt.getGridColumns()[me.cellIndex];
                                if( header.dataIndex == 'text_1' ){
                                    var record = me.getStore().getAt(me.recordIndex),
                                        text = record.get(header.dataIndex);
                                    if( text ){
                                        //updater
                                        text = text.replace(/[\r\n]+/g, '<br/>');
                                        if( record.get('text_updater') ){
                                            text += '<div style="color: red; margin-top: 8px; font-size: 12[x"><strong>' + record.get('text_updater') + '</strong>, ' + Ext.Date.format(record.get('text_time'), 'H:i, d-m-Y') + '</div>';
                                        }
                                        tip.update(text);
                                        return true;
                                    }
                                }
                            }
                            return false;
                        }
                    }
                });

            }
        };
        me.callParent(arguments);

    },
    // @todo: wrapp these functions into 1
    //REFRESH STAFFING WHEN UPDATE TASK
    refreshStaffing : function(callback){
        var me = this;
        Ext.Ajax.request({
            url: '/project_tasks/staffingWhenUpdateTask/' +me.project_id,
            method: 'GET',
            disableCaching: false,
            failure: function (responsex, optionsx) {
                return callback(false);
            },
            success:function(responsex, optionsx) {
                return false;
            }
        });
        return false;
    },
    calculateTable: function(){
        if(is_manual_consumed){
            var intConsume = extConsume = intOverload = extOverload = 0;
            this.getRootNode().cascadeBy(function(node){
                if( !node.hasChildNodes() && node.get('is_part') != 'true' && node.get('is_phase') != 'true' ){
                    if( node.get('special') == '1' ){
                        extConsume += parseFloat(node.get('manual_consumed'));
                        extOverload += parseFloat(node.get('manual_overload'));
                    } else {
                        var a = parseFloat(node.get('manual_consumed'));
                        if( !isNaN(a) )intConsume += a;
                        a = parseFloat(node.get('manual_overload'));
                        if( !isNaN(a) )intOverload += a;
                    }
                }
            });
            var intWl = parseFloat($('.int-wl .display-value').text()),
                extWl = parseFloat($('.ext-wl .display-value').text()),
                intRemain = intWl - intConsume + intOverload,
                extRemain = extWl - extConsume + extOverload;
            if( intRemain < 0 )intRemain = 0;
            if( extRemain < 0 )extRemain = 0;
            $('.display-internal-consumed .display-value').text(intConsume.toFixed(2));
            $('.display-external-consumed .display-value').text(extConsume.toFixed(2));
            $('.display-external-remain .display-value').text(extRemain.toFixed(2));
            $('.display-internal-remain .display-value').text(intRemain.toFixed(2));
        }
    },
    //implement later
    reloadSummary: function(callback){
        var me = this;
        Ext.Ajax.request({
            url: '/project_tasks/listTasksJson/' +me.project_id,
            method: 'GET',
            failure: function (responsex, optionsx) {
                return callback(false);
            },
            success:function(responsex, optionsx) {
                var dataJSON = JSON.parse(responsex.responseText);
            }
        });
    },
	updateNodeData: function(node, datas){
		datas.is_new = false;
		jQuery.each(datas, function(key, value){
			node.set(key, value);
		});
	},
    refreshSummaryNew:function(item_task, callback){
		var me = this;
		var panel = Ext.getCmp('pmstreepanel');
		items = panel.getStore().getData().items;
		console.log(items);
		var datas = [];
	    if(items){
			me.clearFilter();
			var dataJSON = items[0].data;
			var cons = 0;
			var estimated = 0;
			var overload = 0;
			var manualOverload = 0;
			var manualConsumed = 0;
			var remain = 0;
			var wait = 0;
			var startDate = 0;
			var endDate = 0;
			var duration = dataJSON.duration;
			var idActivity = dataJSON.id_activity;
			var unit_price = 0;
			var estimated_euro = 0;
			var consumed_euro = 0;
			var remain_euro = 0;
			var workload_euro = 0;
			var amount = 0;
			var progress_order = 0;
			var progress_order_amount = 0;
			var $_datas = [];
			
			for (var i = 0; i < dataJSON.children.length; i++) {
				$_datas[i] = {
					task_start_date:dataJSON.children[i].task_start_date,
					task_end_date: dataJSON.children[i].task_end_date,
					duration: dataJSON.children[i].duration,
					progress_order: dataJSON.children[i].progress_order,
					progress_order_amount: dataJSON.children[i].progress_order_amount,
				};
				progress_order += dataJSON.children[i].progress_order;
				progress_order_amount += dataJSON.children[i].progress_order_amount;
				
			}
			var _arg = [];
			for (var i = 0; i < dataJSON.children.length; i++) {
				// Phases
				var i_remain = i_duration = i_wait = i_estimated = i_consumed = i_manual_overload = i_manual_consumed = i_unit_price = i_workload_euro = i_remain_euro = i_consumed_euro = i_amount = i_estimated_euro = i_overload = i_task_start_date = i_task_end_date = 0;
				if(dataJSON.children[i].children){
					for(var j = 0; j < dataJSON.children[i].children.length; j++){
						// Tasks
						
						var item = me.store.root.childNodes[0].childNodes[i].childNodes[j];
						if(item.id == flag_new_task_id || item.id == item_task.id){
							me.updateNodeData(item, item_task);
						}
						if(dataJSON.children[i].children[j].task_priority_text)
							item.set("task_priority_text", dataJSON.children[i].children[j].task_priority_text);
						if(dataJSON.children[i].children[j].task_assign_to_text)
							item.set("task_assign_to_text", dataJSON.children[i].children[j].task_assign_to_text);
						if(dataJSON.children[i].children[j].task_status_text)
							item.set("task_status_text", dataJSON.children[i].children[j].task_status_text);
						if(dataJSON.children[i].children[j].task_status_id)
							item.set("task_status_id", dataJSON.children[i].children[j].task_status_id);
						if(dataJSON.children[i].children[j].milestone_id)
							item.set("milestone_id", dataJSON.children[i].children[j].milestone_id);
						if(dataJSON.children[i].children[j].profile_text){
							item.set("profile_text", dataJSON.children[i].children[j].profile_text);
						}
						// refresh sub-task
						if(dataJSON.children[i].children[j].children){
							// Sub Tasks
							var j_remain = j_duration = j_wait = j_estimated = j_consumed = j_manual_overload = j_manual_consumed = j_unit_price = j_workload_euro = j_remain_euro = j_consumed_euro = j_amount = j_estimated_euro = j_overload = j_task_start_date = j_task_end_date = x_task_start_date = x_task_end_date = 0;
							for(var x = 0; x < dataJSON.children[i].children[j].children.length; x++){
								if(me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes.length>0)
								{
									if(dataJSON.children[i].children[j].children[x].id == flag_new_task_id || dataJSON.children[i].children[j].children[x].id == item_task.id){
										me.updateNodeData(me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x], item_task);
									}
									if(dataJSON.children[i].children[j].children[x].profile_text){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("profile_text", dataJSON.children[i].children[j].children[x].profile_text);
									}
									if(dataJSON.children[i].children[j].children[x].profile_id){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("profile_id", dataJSON.children[i].children[j].children[x].profile_id);
									}
									if(dataJSON.children[i].children[j].children[x].task_status_text){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("task_status_text", dataJSON.children[i].children[j].children[x].task_status_text);
									}
									if(dataJSON.children[i].children[j].children[x].task_status_id){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("task_status_id", dataJSON.children[i].children[j].children[x].task_status_id);
									}

									if(dataJSON.children[i].children[j].children[x].milestone_id){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("milestone_id", dataJSON.children[i].children[j].children[x].milestone_id);
									}

									if(dataJSON.children[i].children[j].children[x].task_start_date != '0000-00-00'){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("task_start_date", dataJSON.children[i].children[j].children[x].task_start_date);
										if(x_task_start_date == 0){
											x_task_start_date = dataJSON.children[i].children[j].children[x].task_start_date;
										}else if(x_task_start_date > dataJSON.children[i].children[j].children[x].task_start_date){
											x_task_start_date = dataJSON.children[i].children[j].children[x].task_start_date;
										}
										
									}
									if(dataJSON.children[i].children[j].children[x].task_end_date != '0000-00-00'){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("task_end_date", dataJSON.children[i].children[j].children[x].task_end_date);
										if(x_task_end_date == 0){
											x_task_end_date = dataJSON.children[i].children[j].children[x].task_end_date;
										}else if(x_task_end_date < dataJSON.children[i].children[j].children[x].task_end_date){
											x_task_end_date = dataJSON.children[i].children[j].children[x].task_end_date;
										}
									}
									if(item_task.id == dataJSON.children[i].children[j].children[x].id){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("duration", item_task.duration);
									}else{
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("duration", dataJSON.children[i].children[j].children[x].duration);
									}
									//if(dataJSON.children[i].children[j].children[x].remain){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("remain", dataJSON.children[i].children[j].children[x].remain);
										j_remain += parseFloat(dataJSON.children[i].children[j].children[x].remain);
									//}
									me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("consumed", dataJSON.children[i].children[j].children[x].consumed);
									j_consumed += parseFloat(dataJSON.children[i].children[j].children[x].consumed);
									//if(dataJSON.children[i].children[j].children[x].wait){
									me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("wait", dataJSON.children[i].children[j].children[x].wait);
									j_wait += parseFloat(dataJSON.children[i].children[j].children[x].wait);
									//}
									//ADD CODE BY VINGUYEN 03/06/2014
									if(dataJSON.children[i].children[j].children[x].weight){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("weight", dataJSON.children[i].children[j].children[x].weight);
									}
									if(dataJSON.children[i].children[j].children[x].task_title){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("task_title", dataJSON.children[i].children[j].children[x].task_title);
									}
									if(dataJSON.children[i].children[j].children[x].estimated){
										// console.log(dataJSON.children[i].children[j].children[x].estimated);
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("estimated", dataJSON.children[i].children[j].children[x].estimated);j_estimated += parseFloat(dataJSON.children[i].children[j].children[x].estimated);
										// console.log(j_estimated);
									}
									//END
									// if(dataJSON.children[i].children[j].children[x].completed != '0%'){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("completed", dataJSON.children[i].children[j].children[x].completed);
									// }
									me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("predecessor", dataJSON.children[i].children[j].children[x].predecessor);
									me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("is_predecessor", dataJSON.children[i].children[j].children[x].is_predecessor);

									if(dataJSON.children[i].children[j].children[x].overload){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("overload", dataJSON.children[i].children[j].children[x].overload);
										j_overload += parseFloat(dataJSON.children[i].children[j].children[x].overload);
									}
									
									if(dataJSON.children[i].children[j].children[x].manual_overload){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("manual_overload", dataJSON.children[i].children[j].children[x].manual_overload);
										j_manual_overload += parseFloat(dataJSON.children[i].children[j].children[x].manual_overload);
									}
									if(dataJSON.children[i].children[j].children[x].manual_consumed){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("manual_consumed", dataJSON.children[i].children[j].children[x].manual_consumed);
										j_manual_consumed += parseFloat(dataJSON.children[i].children[j].children[x].manual_consumed);
									}
									if(dataJSON.children[i].children[j].children[x].amount){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("amount", dataJSON.children[i].children[j].children[x].amount);
										j_amount += parseFloat(dataJSON.children[i].children[j].children[x].amount);
									}
									if(dataJSON.children[i].children[j].children[x].progress_order){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("progress_order", dataJSON.children[i].children[j].children[x].progress_order);
										
									}
									if(dataJSON.children[i].children[j].children[x].progress_order_amount){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("progress_order_amount", dataJSON.children[i].children[j].children[x].progress_order_amount);
									}
									if(dataJSON.children[i].children[j].children[x].unit_price){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("unit_price", dataJSON.children[i].children[j].children[x].unit_price);
										j_unit_price += parseFloat(dataJSON.children[i].children[j].children[x].unit_price);
									}
									if(dataJSON.children[i].children[j].children[x].estimated_euro){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("estimated_euro", dataJSON.children[i].children[j].children[x].estimated_euro);
										j_estimated_euro += parseFloat(dataJSON.children[i].children[j].children[x].estimated_euro);
									}
									if(dataJSON.children[i].children[j].children[x].consumed_euro){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("consumed_euro", dataJSON.children[i].children[j].children[x].consumed_euro);
										j_consumed_euro += parseFloat(dataJSON.children[i].children[j].children[x].consumed_euro);
									}
									if(dataJSON.children[i].children[j].children[x].remain_euro){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("remain_euro", dataJSON.children[i].children[j].children[x].remain_euro);
										j_remain_euro += parseFloat(dataJSON.children[i].children[j].children[x].remain_euro);
									}
									if(dataJSON.children[i].children[j].children[x].workload_euro){
										me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].set("workload_euro", dataJSON.children[i].children[j].children[x].workload_euro);
										j_workload_euro += parseFloat(dataJSON.children[i].children[j].children[x].workload_euro);
									}
									if(dataJSON.children[i].children[j].children[x].children) {
										for(var k = 0; k < dataJSON.children[i].children[j].children[x].children.length; k++) {
											if(dataJSON.children[i].children[j].children[x].children[k].profile_text) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("profile_text", dataJSON.children[i].children[j].children[x].children[k].profile_text);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].profile_id) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("profile_id", dataJSON.children[i].children[j].children[x].children[k].profile_id);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].task_status_id) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("task_status_id", dataJSON.children[i].children[j].children[x].children[k].task_status_id);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].milestone_id) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("milestone_id", dataJSON.children[i].children[j].children[x].children[k].milestone_id);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].task_status_text) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("task_status_text", dataJSON.children[i].children[j].children[x].children[k].task_status_text);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].weight) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("weight", dataJSON.children[i].children[j].children[x].children[k].weight);
											}
											if(dataJSON.children[i].children[j].children[x].children[k].task_title) {
												me.store.root.childNodes[0].childNodes[i].childNodes[j].childNodes[x].childNodes[k].set("task_title", dataJSON.children[i].children[j].children[x].children[k].task_title);
											}
										}
									}
									//END
								}
							}
						}
						if(dataJSON.children[i].children[j].task_start_date != '0000-00-00'){
							j_task_start_date = dataJSON.children[i].children[j].task_start_date;
							if(x_task_start_date != 0){
								j_task_start_date = x_task_start_date;
							}
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("task_start_date", j_task_start_date);
						}
						
						if(dataJSON.children[i].children[j].task_end_date != '0000-00-00'){
							// j_task_end_date = dataJSON.children[i].children[j].task_end_date;
							if(x_task_end_date != 0 && j_task_end_date < x_task_end_date){
								j_task_end_date = x_task_end_date;
							}else{
								j_task_end_date = dataJSON.children[i].children[j].task_end_date;
							}
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("task_end_date", j_task_end_date);
						}
				
						if(item_task.id == dataJSON.children[i].children[j].id){
							// Sub task
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("duration", item_task.duration);
						}else{
							// Parent task
							if(item_task && item_task['task_parent_duration'] && item_task['task_parent_duration'][dataJSON.children[i].children[j].id]){
								me.store.root.childNodes[0].childNodes[i].childNodes[j].set("duration", item_task['task_parent_duration'][dataJSON.children[i].children[j].id]);
							}else {
								// Task
								me.store.root.childNodes[0].childNodes[i].childNodes[j].set("duration", dataJSON.children[i].children[j].duration);
							}
						}
						if(j_remain == 0){
							j_remain = dataJSON.children[i].children[j].remain;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("remain", j_remain);
						if(j_consumed == 0){
							j_consumed = dataJSON.children[i].children[j].consumed;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("consumed", j_consumed);
						if(j_wait == 0){
							j_wait = dataJSON.children[i].children[j].wait;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("wait", j_wait);
						
						//ADD CODE BY VINGUYEN 03/06/2014
						if(dataJSON.children[i].children[j].weight){
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("weight", dataJSON.children[i].children[j].weight);
						}
						if(dataJSON.children[i].children[j].task_title){
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("task_title", dataJSON.children[i].children[j].task_title);
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("completed", dataJSON.children[i].children[j].completed);
						
						if(j_estimated == 0){
							j_estimated = dataJSON.children[i].children[j].estimated;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("estimated", j_estimated);
						
						if(j_overload == 0){
							j_overload = dataJSON.children[i].children[j].overload;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("overload", j_overload);
						if(j_manual_overload == 0){
							j_manual_overload = dataJSON.children[i].children[j].manual_overload;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("manual_overload", j_manual_overload);
						
						if(j_manual_consumed == 0){
							j_manual_consumed = dataJSON.children[i].children[j].manual_consumed;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("manual_consumed", j_manual_consumed);
						
						if(j_amount == 0){
							j_amount = dataJSON.children[i].children[j].amount;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("amount", j_amount);
					
						if(dataJSON.children[i].children[j].progress_order){
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("progress_order", dataJSON.children[i].children[j].progress_order);
						}
						if(dataJSON.children[i].children[j].progress_order_amount){
							me.store.root.childNodes[0].childNodes[i].childNodes[j].set("progress_order_amount", dataJSON.children[i].children[j].progress_order_amount);
						}
						
						if(j_unit_price == 0){
							j_unit_price = dataJSON.children[i].children[j].unit_price;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("unit_price", j_unit_price);
						
						if(j_estimated_euro == 0){
							j_estimated_euro = dataJSON.children[i].children[j].estimated_euro;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("estimated_euro", j_estimated_euro);
						
						if(j_consumed_euro == 0){
							j_consumed_euro = dataJSON.children[i].children[j].consumed_euro;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("consumed_euro", j_consumed_euro);
						
						if(j_remain_euro == 0){
							j_remain_euro = dataJSON.children[i].children[j].remain_euro;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("remain_euro",  j_remain_euro);
						
						if(j_workload_euro == 0){
							j_workload_euro = dataJSON.children[i].children[j].workload_euro;
						}
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("workload_euro", j_workload_euro);
					
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("predecessor", dataJSON.children[i].children[j].predecessor);
						
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("is_predecessor", dataJSON.children[i].children[j].is_predecessor);
						me.store.root.childNodes[0].childNodes[i].childNodes[j].set("dirty", false);
						
						i_remain += parseFloat(j_remain);
						i_wait += parseFloat(j_wait);
						i_estimated += parseFloat(j_estimated);
						i_consumed += parseFloat(j_consumed);
						i_manual_overload += parseFloat(j_manual_overload);
						i_manual_consumed += parseFloat(j_manual_consumed);
						i_unit_price += parseFloat(j_unit_price);
						i_workload_euro += parseFloat(j_workload_euro);
						i_remain_euro += parseFloat(j_remain_euro);
						i_consumed_euro += parseFloat(j_consumed_euro);
						i_amount += parseFloat(j_amount);
						i_estimated_euro += parseFloat(j_estimated_euro);
						i_overload += parseFloat(j_overload);
						i_task_end_date = (i_task_end_date !=0) ? ((i_task_end_date > j_task_end_date) ? i_task_end_date : j_task_end_date) : j_task_end_date;
						i_task_start_date = (i_task_start_date !=0) ? ((i_task_start_date > j_task_start_date) ? j_task_start_date : i_task_start_date) : j_task_start_date;
					}
					
				}
				me.store.root.childNodes[0].childNodes[i].set("estimated", i_estimated);
				me.store.root.childNodes[0].childNodes[i].set("overload", i_overload);
				me.store.root.childNodes[0].childNodes[i].set("consumed", i_consumed);
				me.store.root.childNodes[0].childNodes[i].set("manual_overload", i_manual_overload);
				me.store.root.childNodes[0].childNodes[i].set("manual_consumed", i_manual_consumed);
				me.store.root.childNodes[0].childNodes[i].set("remain", i_remain);
				me.store.root.childNodes[0].childNodes[i].set("wait", i_wait);
				me.store.root.childNodes[0].childNodes[i].set("dirty", false);
				
				
				// Duration phase
				if(item_task && item_task['phase_duration'] && item_task['phase_duration'][dataJSON.children[i].id]){
					me.store.root.childNodes[0].childNodes[i].set("duration", item_task['phase_duration'][dataJSON.children[i].id]);
				}else{
					me.store.root.childNodes[0].childNodes[i].set("duration", dataJSON.children[i].duration);
				}
				me.store.root.childNodes[0].childNodes[i].set('amount', i_amount);
				me.store.root.childNodes[0].childNodes[i].set('progress_order', dataJSON.children[i].progress_order);
				me.store.root.childNodes[0].childNodes[i].set('progress_order_amount', dataJSON.children[i].progress_order_amount);
				me.store.root.childNodes[0].childNodes[i].set('unit_price', i_unit_price);
				me.store.root.childNodes[0].childNodes[i].set('estimated_euro', i_estimated_euro);
				me.store.root.childNodes[0].childNodes[i].set('consumed_euro', i_consumed_euro);
				me.store.root.childNodes[0].childNodes[i].set('remain_euro', i_remain_euro);
				me.store.root.childNodes[0].childNodes[i].set('workload_euro', i_workload_euro);
				me.store.root.childNodes[0].childNodes[i].set("task_start_date", i_task_start_date);
				me.store.root.childNodes[0].childNodes[i].set("task_end_date", i_task_end_date);
				
				cons += i_consumed;
				manualConsumed += i_manual_consumed;
				estimated += i_estimated;
				manualOverload += i_manual_overload;
				remain += i_remain;
				wait += i_wait;
				unit_price += i_unit_price;
				estimated_euro += i_estimated_euro;
				consumed_euro += i_consumed_euro;
				remain_euro += i_remain_euro;
				workload_euro += i_workload_euro;
				overload += i_overload;
				amount += i_amount;
				
				endDate = (endDate !=0) ? ((i_task_end_date > endDate) ? i_task_end_date : endDate) : i_task_end_date;
				startDate = (startDate !=0) ? ((i_task_start_date > startDate) ? startDate : i_task_start_date) : i_task_start_date;
				
			}
			var completed, consume = is_manual_consumed ? manualConsumed : cons, over = is_manual_consumed ? manualOverload : overload;
			if(estimated == 0 || cons == 0){
				completed = '0%';
			} else {
				var _caculate = ((consume * 100)/(estimated+over)).toFixed(2);
				if(_caculate > 100){
					completed = '100%';
				} else {
					completed = _caculate + '%';
				}
			}
			me.storeConsumed = cons;
			me.storeManualConsumed = manualConsumed;
			me.storeEstimated = estimated;
			me.storeOverload = overload;
			me.storeManualOverload = manualOverload;
			me.storeRemain = remain;
			me.storeWait = wait;
			me.storeStartDate = startDate;
			me.storeEndDate = endDate;
			me.storeCompleted = completed;
			me.storeDuration = duration;
			me.storeIdActivity = idActivity;
			me.storeUnitPrice = unit_price;
			me.storeEstimatedEuro = estimated_euro;
			me.storeConsumedEuro = consumed_euro;
			me.storeRemainEuro = remain_euro;
			me.storeWorkloadEuro = workload_euro;
			me.storeAmount = amount;

			me.store.root.childNodes[0].set('estimated',me.storeEstimated);
			me.store.root.childNodes[0].set('overload',me.storeOverload);
			me.store.root.childNodes[0].set('manual_overload',me.storeManualOverload);
			me.store.root.childNodes[0].set('consumed',me.storeConsumed);
			me.store.root.childNodes[0].set('manual_consumed',me.storeManualConsumed);
			me.store.root.childNodes[0].set('remain',me.storeRemain);
			me.store.root.childNodes[0].set('wait',me.storeWait);
			me.store.root.childNodes[0].set('task_start_date',me.storeStartDate);
			me.store.root.childNodes[0].set('task_end_date',me.storeEndDate);
			me.store.root.childNodes[0].set('completed',me.storeCompleted);
			me.store.root.childNodes[0].set("dirty", false);
			
			if(item_task.project_duration){
				me.store.root.childNodes[0].set('duration', item_task.project_duration);
			}else{
				me.store.root.childNodes[0].set('duration',me.storeDuration);
			}
			me.store.root.childNodes[0].set('id_activity',me.storeIdActivity);
			me.store.root.childNodes[0].set('amount', me.storeAmount);
			me.store.root.childNodes[0].set('progress_order', me.progress_order);
			me.store.root.childNodes[0].set('progress_order_amount', me.progress_order_amount);
			me.store.root.childNodes[0].set('unit_price', me.storeUnitPrice);
			me.store.root.childNodes[0].set('estimated_euro', me.storeEstimatedEuro);
			me.store.root.childNodes[0].set('consumed_euro', me.storeConsumedEuro);
			me.store.root.childNodes[0].set('remain_euro', me.storeRemainEuro);
			me.store.root.childNodes[0].set('workload_euro', me.storeWorkloadEuro);

			me.store.root.set('estimated',me.storeEstimated);
			me.store.root.set('overload',me.storeOverload);
			me.store.root.set('consumed',me.storeConsumed);
			me.store.root.set('manual_overload',me.storeManualOverload);
			me.store.root.set('manual_consumed',me.storeManualConsumed);
			me.store.root.set('remain',me.storeRemain);
			me.store.root.set('wait',me.storeWait);
			me.store.root.set('task_start_date',me.storeStartDate);
			me.store.root.set('task_end_date',me.storeEndDate);
			me.store.root.set('completed',me.storeCompleted);
			me.store.root.set('duration',me.storeDuration);
			me.store.root.set('id_activity',me.storeIdActivity);
			me.store.root.set('unit_price',me.storeUnitPrice);
			me.store.root.set('estimated_euro',me.storeEstimatedEuro);
			me.store.root.set('consumed_euro',me.storeConsumedEuro);
			me.store.root.set('remain_euro',me.storeRemainEuro);
			me.store.root.set('workload_euro',me.storeWorkloadEuro);
			me.store.root.set('amount',me.storeAmount);
			
			var root = me.store.root;
			me.refreshView();
			me.applyFilters();
			me.calculateTable();
			return callback(true);
		}
	},
    refreshSummary:function(callback){
        var me = this;
        Ext.Ajax.request({
            url: '/project_tasks/listTasksJson/' +me.project_id,
            method: 'GET',
            failure: function (responsex, optionsx) {
                return callback(false);
            },
            success:function(responsex, optionsx) {
                var dataJSON = JSON.parse(responsex.responseText);
                me.clearFilter();
                me.handleDnD(dataJSON);
                me.applyFilters();
                me.calculateTable();
                return callback(true);
            }
        });
    },
    // @todo: wrapp these functions into 1
    requestSummary: function(callback1){
        var me = this;
        Ext.Ajax.request({
            url: '/project_tasks/listTasksJson/' +me.project_id,
            method: 'GET',
            failure: function (responsex, optionsx) {
                return callback1(false);
            },
            success:function(responsex, optionsx) {
                var dataJSON = JSON.parse(responsex.responseText);
                var estimated = dataJSON.estimated;
                /*$('.internal-line .display-workload span.display-value').text(estimated);
                var wInterNal=parseFloat(estimated).toFixed(2);
                var budget=$('.internal-line .display-budget span.display-value').text();
                budget=parseFloat(budget).toFixed(2);
                var bVar=(wInterNal/budget)-1;
                bVar=parseFloat(bVar).toFixed(2);
                bVar=parseFloat(bVar*100).toFixed(2);
                $('.internal-line .display-var span.display-value').text(bVar+' %');

                if(bVar>0){
                    $('.internal-line .display-var span.display-value').removeClass('var-green');
                    $('.internal-line .display-var span.display-value').addClass('var-red');
                }else{
                    $('.internal-line .display-var span.display-value').removeClass('var-red');
                    $('.internal-line .display-var span.display-value').addClass('var-green');
                }*/
                /*$.post("/project_tasks/budget_var/"+me.project_id, function(data) {
                        $(".internal-line .display-var span.display-value" ).html(data);
                        data = parseFloat(data);
                        if(data>0){
                            $('.internal-line .display-var span.display-value').removeClass('var-green');
                            $('.internal-line .display-var span.display-value').addClass('var-red');
                        }else{
                            $('.internal-line .display-var span.display-value').removeClass('var-red');
                            $('.internal-line .display-var span.display-value').addClass('var-green');
                        }
                });*/
                var overload = dataJSON.overload;
                var consumed = dataJSON.consumed;
                var remain = dataJSON.remain;
                var wait = dataJSON.wait;
                var startDate = dataJSON.task_start_date;
                var endDate = dataJSON.task_end_date;
                var duration = dataJSON.duration;
                var idActivity = dataJSON.id_activity;
                var unit_price = dataJSON.unit_price;
                var estimated_euro = dataJSON.estimated_euro;
                var consumed_euro = dataJSON.consumed_euro;
                var remain_euro = dataJSON.remain_euro;
                var workload_euro = dataJSON.workload_euro;
                var manual_overload = dataJSON.manual_overload,
                    manual_consumed = dataJSON.manual_consumed;

                var _totalPrvious = _totalEstimatedPr = 0;
                if(dataJSON.children){
                    var countChildren=dataJSON.children.length;
                    for(i = 0; i < countChildren; i++){
                    if(dataJSON.children[i].is_activity == 'true'){
                            _totalPrvious = dataJSON.children[i].consumed;
                            _totalEstimatedPr = dataJSON.children[i].estimated;
                        }
                    }
                    consumed = consumed + parseFloat(_totalPrvious);
                    consumed = consumed.toFixed(2);
                    if(_totalEstimatedPr == null || _totalEstimatedPr == ''){
                        _totalEstimatedPr = 0;
                    }
                }
                var completed;
                if(estimated == 0 || consumed == 0){
                    completed = 0 + '%';
                } else {
                    completed = ((consumed * 100)/(estimated+overload)).toFixed(2) + '%';
                }
                if(startDate != 0){
                    startDate = startDate.split('-');
                    startDate = startDate[1] + '-' + startDate[2] + '-' + startDate[0];
                }

                if(endDate != 0){
                    endDate = endDate.split('-');
                    endDate = endDate[1] + '-' + endDate[2] + '-' + endDate[0];
                }

                me.storeEstimated = estimated;
                me.storeOverload = overload;
                me.storeConsumed = consumed;
                me.storeRemain = remain;
                me.storeWait = wait;
                me.storeStartDate = startDate;
                me.storeEndDate = endDate;
                me.storeCompleted = completed;
                me.storeDuration = duration;
                me.storeIdActivity = idActivity;
                me.storeUnitPrice = unit_price;
                me.storeEstimatedEuro = estimated_euro;
                me.storeConsumedEuro= consumed_euro;
                me.storeRemainEuro = remain_euro;
                me.storeWrokloadEuro = workload_euro;

                return callback1(true);
            }
        });
    },
    // @todo: wrapp these functions into 1
    requestStatusesData: function(requestStatusesDataCallback){
        var me = this;
        Ext.Ajax.request({
            scope: me,
            url: '/project_tasks/listStatusesJson/',
            method: 'GET',
            failure: function (responsex, optionsx) {

            },
            success:function(responsex, optionsx) {
                var dataJSON = JSON.parse(responsex.responseText),
                    datas = [];
                for (var i = 0; i < dataJSON.length; i++) {
                    datas[i] = {
                        task_status_id:dataJSON[i].ProjectStatus.id,
                        task_status_text: dataJSON[i].ProjectStatus.status};
                    // me.listStatuses[dataJSON[i].ProjectStatus.id] = dataJSON[i].ProjectStatus.status;
						me.listStatuses[i] = {};
						me.listStatuses[i]['id'] = dataJSON.Statuses[i].ProjectStatus.id;
						me.listStatuses[i]['name'] = dataJSON.Statuses[i].ProjectStatus.name;
                }

                var newStoreStatuses =  Ext.create('Ext.data.Store',{
                    autoLoad: true,
                    fields: [
                        {name: 'task_status_id'},
                        {name: 'task_status_text', type: 'string'}
                    ],
                    data: datas
                });

                return requestStatusesDataCallback(true);
            }
        });
    },
    listStatuses: {},
    listMilestone: {},
    listPriority: {},
    listProfile: {},
    requestPrioritiesDataCustom: function(requestPrioritiesDataCallback1){
        var me = this;
        var dataJSON = JSON.parse(jQuery('#priorityJson').html()),
            datasPriorities = [],
            datasStatuses = [],
            datasMilestone = [],
            datasEmployees = [];
            datasProfile = [];
        var countPriorities=dataJSON.Priorities.length
        for (var i = 0;  i < countPriorities; i++) {
            datasPriorities[i] = {
                task_priority_id:dataJSON.Priorities[i].ProjectPriority.id,
                task_priority_text: dataJSON.Priorities[i].ProjectPriority.priority
            };
            me.listPriority[dataJSON.Priorities[i].ProjectPriority.id] = dataJSON.Priorities[i].ProjectPriority.priority;
        }
        var newStorePriority =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_priority_id'},
                {name: 'task_priority_text', type: 'string'}
            ],
            data: datasPriorities
        });
        var countStatuses=dataJSON.Statuses.length;
        for (var i = 0 ;i < countStatuses; i++) {
            datasStatuses[i] = {
                task_status_id:dataJSON.Statuses[i].ProjectStatus.id,
                task_status_text: dataJSON.Statuses[i].ProjectStatus.name
            };
			me.listStatuses[i] = {};
            me.listStatuses[i]['id'] = dataJSON.Statuses[i].ProjectStatus.id;
            me.listStatuses[i]['name'] = dataJSON.Statuses[i].ProjectStatus.name;
        }
        var newStoreStatuses =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_status_id'},
                {name: 'task_status_text', type: 'string'}
            ],
            data: datasStatuses
        });

        var countMilestone=dataJSON.Milestone.length;
        for (var i = 0 ;i < countMilestone; i++) {
            datasMilestone[i] = {
                milestone_id:dataJSON.Milestone[i].ProjectMilestone.id,
                milestone_text: dataJSON.Milestone[i].ProjectMilestone.project_milestone
            };
            me.listMilestone[dataJSON.Milestone[i].ProjectMilestone.id] = dataJSON.Milestone[i].ProjectMilestone.project_milestone;
        }
        var newStoreMilestone =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'milestone_id'},
                {name: 'milestone_text', type: 'string'}
            ],
            data: datasMilestone
        });

        var countProfile=dataJSON.Profiles.length;
        for (var i = 0 ;i < countProfile; i++) {
            datasProfile[i] = {
                milestone_id:dataJSON.Profiles[i].Profile.id,
                milestone_text: dataJSON.Profiles[i].Profile.name
            };
            me.listProfile[dataJSON.Profiles[i].Profile.id] = dataJSON.Profiles[i].Profile.name;
        }
        var newStoreProfile =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'profile_id'},
                {name: 'profile_text', type: 'string'}
            ],
            data: datasProfile
        });
        var countEmployees=dataJSON.Employees.length;
        me.references = {};
        for (var i = 0; i < dataJSON.Employees.length; i++) {
            var d = dataJSON.Employees[i].Employee;
            datasEmployees[i] = {
                task_assign_to_id: d.id.toString(),
                is_profit_center: d.is_profit_center ? d.is_profit_center : 0,
                task_assign_to_text: d.name
            };
            me.references[d.id + '-' + d.is_profit_center] = d.name;
        }
        listAssignTos = datasEmployees;
        var newStoreAssigned =  Ext.create('Ext.data.Store',{
            autoLoad: true,
            fields: [
                {name: 'task_assign_to_id'},
                {name: 'is_profit_center'},
                {name: 'actif'},
                {name: 'task_assign_to_text', type: 'string'}
            ],
            data: datasEmployees
        });

        me.datasPriorities = datasPriorities;
        me.datasMilestone = datasMilestone;
        me.datasStatuses = datasStatuses;
        me.datasEmployees = datasEmployees;
        me.storeEstimated = newStoreAssigned;
        me.storeAssigned = newStoreAssigned;
        me.storeStatuses = newStoreStatuses;
        me.storePriority = newStorePriority;
        me.storeMilestone = newStoreMilestone;
        me.storeProfiles = newStoreProfile;

        //todo: create profile store
        var profiles = [];
        for(var i = 0; i < dataJSON.Profiles.length; i++){
            profiles.push({
                profile_id: dataJSON.Profiles[i].Profile.id,
                profile_text: dataJSON.Profiles[i].Profile.name
            });
        }
        me.storeProfiles = Ext.create('Ext.data.Store', {
            autoLoad: true,
            fields: [
                {name: 'profile_id'},
                {name: 'profile_text', type: 'string'}
            ],
            data: profiles
        });
    },

    requestAssignWhenClick: function(curentTask,action,$employee){
        var eOpts = Ext.getCmp("comboChange");
        if(typeof curentTask.start =='undefined' || !curentTask.start)
        {
            return false;
        }
        if(typeof curentTask.end =='undefined' || !curentTask.end)
        {
            return false;
        }
        var startMonth = curentTask.start.getMonth()+1;
        startMonth = (startMonth < 10) ? '0'+startMonth : startMonth;
        var _dayStart = curentTask.start.getDate() < 10 ? '0'+curentTask.start.getDate() : curentTask.start.getDate();
        var _startDate = curentTask.start.getFullYear() + '-' + startMonth + '-' + _dayStart;

        var endMonth = curentTask.end.getMonth()+1;
        endMonth = (endMonth < 10) ? '0'+endMonth : endMonth;
        var _dayEnd = curentTask.end.getDate() < 10 ? '0'+curentTask.end.getDate() : curentTask.end.getDate();
        var _endDate = curentTask.end.getFullYear() + '-' + endMonth + '-' + _dayEnd;

        var task_id = curentTask.id ? curentTask.id : 999999999;
        var me = this;
        Ext.Ajax.on("beforerequest", function(){
            eOpts.setLoading(i18n('Please Wait'));
        });
        Ext.Ajax.on('requestcomplete', function(){
            eOpts.setLoading(false);
        });
         Ext.Ajax.on('requestexception', function(){
            eOpts.setLoading(false);
        });
        if(action === 'show'){
            //eOpts.select(curentTask.assign);
            if(typeof listTaskHasLoad[task_id] != 'undefined' && listTaskHasLoad[task_id] != null){
                var assignTask = listTaskHasLoad[task_id] ? listTaskHasLoad[task_id] : listAssignTos;
                Ext.onReady(function() {
                    $.each(assignTask, function(index, values){
                        var _idAssign = values.task_assign_to_id;
                        var _isProfit = values.is_profit_center;
                        var manDay = values.capacity_on ? values.capacity_on : 'wait';
                        var idLi = '#li-'+_idAssign+'-'+_isProfit;
                        $(idLi).find('img').remove();
                        $(idLi).find('em').remove();
                        if(manDay === 'wait'){

                        } else {
                            var newLi = $(idLi).html();
                            if(_isProfit == 0){
                                $('<a class="loadmd" id="' +_idAssign+ '" ><img src="/img/extjs/viewprojecttask.png" ></a>').insertBefore(idLi);
                            }
                            if(manDay == 0){
                                newLi += '<em class="noMD" >' +manDay+ ' M.D</em>';
                            }
                            else{
                                newLi += '<em >' +manDay+ ' M.D</em>';
                            }
                            $(idLi).html(newLi);
                        }
                    });
                });
            }
        } else if(action === 'loadmd'){
            Ext.onReady(function()
            {
                $value=$employee.split('-');
                var _idAssign = $value[0];
                var _isProfit = $value[1];
                //var manDay = 'wait';
                $('#'+_idAssign).remove();
                var idLi = '#li-'+$employee;
                $(idLi).find('img').remove();
                $(idLi).find('em').remove();

                $(idLi).append('<img src="/img/slick_grid/ajax-loader-small.gif" alt="Loading..." style="float:right" />');

                //clearInterval(flag);
                var flag=setTimeout(function(){
                $.ajax({
                    url: '/project_tasks/getManDayOfEmployee/project/' + me.project_id + '/' + task_id + '/' + _startDate + '/' + _endDate,
                    data: {
                        employee: $value
                    },
                    //async: false,
                    type:'POST',
                    success:function(datas) {
                        var datas = JSON.parse(datas);
                        var manDay = datas.manDays.val ? datas.manDays.val : 0;
                        var idLi = datas.manDays.id ? datas.manDays.id : 0;
                        var receiveds = datas.listAssignReceiveds ? datas.listAssignReceiveds : '';
                        $(idLi).find('img').remove();
                        $(idLi).find('em').remove();
                        var newLi = $(idLi).html();
                        if(_isProfit == 0){
                            $('<a class="loadmd" id="' +_idAssign+ '" ><img src="/img/extjs/viewprojecttask.png" ></a>').insertBefore(idLi);
                        }
                        if(manDay == 0) {
                            newLi += '<em class="noMD">' +manDay+ ' M.D</em>';
                        } else {
                            newLi += '<em>' +manDay+ ' M.D</em>';
                        }
                        //clearInterval(flag);
                        $(idLi).html(newLi);
                        var datasEmployees = [];
                        var listAssigned = [];
                        var listIdSelects = [];
                        var k = 0;
                        for (var i = 0; i < receiveds.length; i++) {
                            if(receiveds[i].Employee.id==_idAssign){
                                $('#'+_idAssign).attr('rel',receiveds[i].Employee.name);
                            }
                            if(me.storeAssigned.data.items[i] && me.storeAssigned.data.items[i].data.capacity_on) {
                                if(receiveds[i].Employee.capacity_on) {
                                    datasEmployees[i] = {
                                        task_assign_to_id: receiveds[i].Employee.id.toString(),
                                        is_profit_center: receiveds[i].Employee.is_profit_center ? receiveds[i].Employee.is_profit_center : 0,
                                        task_assign_to_text: receiveds[i].Employee.name,
                                        actif: receiveds[i].Employee.actif,
                                        is_selected : receiveds[i].Employee.is_selected,
                                        capacity_on: receiveds[i].Employee.capacity_on.toString()
                                    };
                                } else {
                                    datasEmployees[i] = {
                                        task_assign_to_id: receiveds[i].Employee.id.toString(),
                                        is_profit_center: receiveds[i].Employee.is_profit_center ? receiveds[i].Employee.is_profit_center : 0,
                                        task_assign_to_text: receiveds[i].Employee.name,
                                        actif: receiveds[i].Employee.actif,
                                        is_selected : receiveds[i].Employee.is_selected
                                    };
                                }
                            }
                            var liIdCurrent = '#li-'+receiveds[i].Employee.id+'-'+receiveds[i].Employee.is_profit_center;
                            if(receiveds[i].Employee.is_selected == 1) {
                                listIdSelects[k] = receiveds[i].Employee.id.toString();
                                listAssigned[k] = receiveds[i].Employee.name;
                                k++;
                            }
                        }
                        listTaskHasLoad[task_id] = datasEmployees;
                        me.saveLoadTasks[task_id] = listIdSelects;
                        var newStoreAssigned =  Ext.create('Ext.data.Store',{
                            autoLoad: true,
                            fields: [
                                {name: 'task_assign_to_id'},
                                {name: 'is_profit_center'},
                                {name: 'capacity_on'},
                                {name: 'actif'},
                                {name: 'task_assign_to_text', type: 'string'},
                                {name: 'is_selected'}
                            ],
                            data: datasEmployees
                        });
                        me.storeAssigned = newStoreAssigned;
                        me.valueAssignAfterLoadMD = listAssigned.join(',');
                        // eOpts.select(listAssigned.join(','));
                        // eOpts.setValue(listIdSelects);
                        // eOpts.setRawValue(listAssigned.join(','));
                    }
                });},100);

            });
        }
    },

    requestAfterClick : function(task_id, callback){
        var me = this;
        var eOpts = Ext.getCmp("comboChange");
        var _eOpts = Ext.getCmp("comboChangeMain");
        var _start = _end = -1;
        if(me.currentStartDate && me.currentEndDate){
            _start = me.currentStartDate;
            _end = me.currentEndDate;
            var _startMonth = (_start.getMonth()+1 < 10) ? '0'+(_start.getMonth()+1 ) : (_start.getMonth()+1 );
            var _startDate = (_start.getDate() < 10) ? '0'+(_start.getDate()) : _start.getDate();
            var _endMonth = (_end.getMonth()+1 < 10) ? '0'+(_end.getMonth()+1 ) : (_end.getMonth()+1 );
            var _endDate = (_end.getDate() < 10) ? '0'+(_end.getDate()) : _end.getDate();
            _start = _start.getFullYear() + '-' + _startMonth + '-' + _startDate;
            _end = _end.getFullYear() + '-' + _endMonth + '-' + _endDate;
        }
        Ext.Ajax.request({
            scope: me,
            url: '/project_tasks/listEmployeesJson/' + me.project_id +'/'+ task_id + '/' + _start + '/' +_end,
            method: 'GET',
            failure: function (response, options) {
                if( typeof callback == 'function' )callback.call(me, false);
                return requestAssignWhenClickCallback(false);
            },
            success:function(responsex, optionsx) {
                var dataJson = JSON.parse(responsex.responseText);
                var datasEmployees = [];

                var listAssigned = [];
                var listIdSelects = [];
                var k = 0;
                for (var i = 0; i < dataJson.length; i++) {
                    datasEmployees[i] = {
                        task_assign_to_id: dataJson[i].Employee.id.toString(),
                        is_profit_center: dataJson[i].Employee.is_profit_center ? dataJson[i].Employee.is_profit_center : 0 ,
                        task_assign_to_text: dataJson[i].Employee.name,
                        actif: dataJson[i].Employee.actif,
                        is_selected: dataJson[i].Employee.is_selected,
                        capacity_on: '',
                        capacity_off: '',
                        capacity: '',
                        listProfile:'None',
                    };
                    if(dataJson[i].Employee.is_selected == 1) {
                        listIdSelects[k] = dataJson[i].Employee.id.toString();
                        listAssigned[k] = dataJson[i].Employee.name;
                        k++;
                    }
                }
                var newStoreAssigned =  Ext.create('Ext.data.Store',{
                    autoLoad: true,
                    fields: [
                        {name: 'task_assign_to_id'},
                        {name: 'is_profit_center'},
                        {name: 'actif'},
                        {name: 'capacity_on'},
                        {name: 'capacity_off'},
                        {name: 'capacity'},
                        {name: 'listProfile'},
                        {name: 'task_assign_to_text', type: 'string'},
                        {name: 'is_selected'}
                    ],
                    data: datasEmployees
                });

                //var rawvl  = eOpts.getRawValue() ;
                me.storeAssigned = newStoreAssigned;
                //eOpts.up().setStore(newStoreAssigned);
                //eOpts.setStore(newStoreAssigned);
                //eOpts.bindStore(newStoreAssigned);
                me.plugins[0].editor.down('#comboChange').bindStore(me.storeAssigned);
                me.valueAssignAfterLoadMD = listAssigned.join(',');
                // eOpts.select(listAssigned.join(','));
                // eOpts.setValue(listIdSelects);
                if( typeof callback == 'function' )callback.call(me);
            }
        });
    },
    showDetail : function(id_employee, id_task, employee_name, inforTasks){
        var me = this;
        var eOpts = Ext.getCmp("comboChange");
        var capacity_on = 0;
        var capacity_off = 0;
        eOpts.getStore().each(function(rec) {
            if(rec.raw.task_assign_to_id == id_employee){
                capacity_on = rec.raw.capacity_on;
                capacity_off = rec.raw.capacity_off;
            }
        });
        // ten employee
        $(".gs-name-header span").html(employee_name);
        // chen title cua avai
        var startDate = inforTasks.start.getDate() + '-' + (inforTasks.start.getMonth()+1) + '-' + inforTasks.start.getFullYear();
        var endDate = inforTasks.end.getDate() + '-' + (inforTasks.end.getMonth()+1) + '-' + inforTasks.end.getFullYear();
        $(".gs-header-content-name span").html(inforTasks.name);
        $(".gs-header-content-start span").html(startDate);
        $(".gs-header-content-end span").html(endDate);
        $(".gs-header-content-work span").html(inforTasks.workload);
        // phan content
        function getVocationDetail(){
            var result = [];
            var startMonth = inforTasks.start.getMonth()+1;
            startMonth = (startMonth < 10) ? '0'+startMonth : startMonth;
            var endMonth = inforTasks.end.getMonth()+1;
            endMonth = (endMonth < 10) ? '0'+endMonth : endMonth;
            var _dayStart = inforTasks.start.getDate() < 10 ? '0'+inforTasks.start.getDate() : inforTasks.start.getDate();
            var _startDate = inforTasks.start.getFullYear() + '-' + startMonth + '-' + _dayStart;
            var _dayEnd = inforTasks.end.getDate() < 10 ? '0'+inforTasks.end.getDate() : inforTasks.end.getDate();
            var _endDate = inforTasks.end.getFullYear() + '-' + endMonth + '-' + _dayEnd;
            $.ajax({
                url : '/project_tasks/getVocationDetail/' + id_employee + '/' + _startDate + '/' + _endDate,
                async: false,
                dataTye: 'json',
                success: function(data){
                    data = JSON.parse(data);
                    result = data;
                },
                error: function(message){
                }
            });
            return result;
        }
        var datas = getVocationDetail();

        var widthDivRight = 0;
        function init(){
            $('#filter_year').removeClass('ch-current');
            $('#filter_month').removeClass('ch-current');
            $('#filter_date').addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = 0;
            if(datas.vocation){
                headers += '<tr>';
                $.each(datas.vocation, function(index, values){
                    var count = 0;
                    $.each(values, function(ind, val){
                        count++;
                        totalCount++;
                        totalVacation += parseFloat(val);
                    });
                    headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                });
                headers += '</tr><tr>';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocation, function(index, values){
                    $.each(values, function(ind, val){
                        ind = ind.split('-');
                        var keyWl = index+'-'+ind[1]+'-'+ind[0];
                        ind = ind[0]+'-'+datas.dayMaps[ind[1]];
                        widthDivRight += 50;
                        headers += '<td><div class="text-center">' + ind + '</div></td>';
                        var _vais = '';
                        if(val == 1 ){
                            _vais = 0;
                        }
                        var _wokingday=1;
                        _capacity=parseFloat(_wokingday)-parseFloat(val);
                        avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                        vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                        work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                        over += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                        capacity += '<td><div id="capacity-' + keyWl + '">' + _capacity + '</div></td>';
                        working += '<td><div id="working-' + keyWl + '">' + _wokingday + '</div></td>';
                    });
                });
                headers += '</tr>';
                avais += '</tr>';
                vocs += '</tr>';
                work += '</tr>';
                over += '</tr>';
                capacity += '</tr>';
                working += '</tr>';
            }
            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);

            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
            if(datas.listDateDatas){
                $.each(datas.listDateDatas, function(idFamily, values){
                    var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                    listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                    valTaskDisplay += '<tr class="family-group">';
                    $.each(datas.vocation, function(index, values){
                        $.each(values, function(ind, val){
                            ind = ind.split('-');
                            ind = index+'-'+ind[1]+'-'+ind[0];
                            valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
                        });
                    });
                    valTaskDisplay += '</tr>';
                    var sttActivity = 0;
                    $.each(values, function(idGlobal, value){
                        sttActivity++;
                        idGlobal = idGlobal.split('-');
                        if(idGlobal[0] === 'ac'){
                            var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : 0;
                                var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
                                var activityTaskName = datas.groupNameTasks.activity[idTask] ? datas.groupNameTasks.activity[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocation, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = ind.split('-');
                                        ind = index+'-'+ind[1]+'-'+ind[0];
                                        var _value = valTask[ind] ? valTask[ind] : 0;
                                        if(val == 1){
                                            _value = 0;
                                        }
                                        valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                        if(!totalWorkload[ind]){
                                            totalWorkload[ind] = 0;
                                        }
                                        totalWorkload[ind] += _value;
                                        if(!listSumFamily[idFamily+'-'+ind]){
                                            listSumFamily[idFamily+'-'+ind] = 0;
                                        }
                                        listSumFamily[idFamily+'-'+ind] += _value;
                                        if(!totalFamily[idFamily]){
                                            totalFamily[idFamily] = 0;
                                        }
                                        totalFamily[idFamily] += _value;
                                    });
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else if(idGlobal[0] === 'pr'){
                            var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
                                var priorities =  datas.priority.project[idTask] ?  datas.priority.project[idTask] : '';
                                var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocation, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = ind.split('-');
                                        ind = index+'-'+ind[1]+'-'+ind[0];
                                        var _value = valTask[ind] ? valTask[ind] : 0;
                                        if(val == 1){
                                            _value = 0;
                                        }
                                        valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                        if(!totalWorkload[ind]){
                                            totalWorkload[ind] = 0;
                                        }
                                        totalWorkload[ind] += _value;
                                        if(!listSumFamily[idFamily+'-'+ind]){
                                            listSumFamily[idFamily+'-'+ind] = 0;
                                        }
                                        listSumFamily[idFamily+'-'+ind] += _value;
                                        if(!totalFamily[idFamily]){
                                            totalFamily[idFamily] = 0;
                                        }
                                        totalFamily[idFamily] += _value;
                                    });
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            //do nothing
                        }
                    });
                });
            }
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);

                var getAvais=0;
                var vocs = $('#total-vocs-popup').find('#vocs-'+getId).html();
                if(vocs == 1){
                    getAvais = 0;
                }else if(vocs == 0.5){
                    getAvais = 0.5 - getTotalWl;
                }else{
                    getAvais = 1 - getTotalWl;
                }
                if (!isNaN(getAvais) && getAvais.toString().indexOf('.') != -1){
                    getAvais = getAvais.toFixed(2);
                }
                totalAvais += parseFloat(getAvais);
                if(getAvais < 0 ) {
                    getOvers = -1*parseFloat(getAvais);
                    getAvais=0;
                } else {
                    getOvers=0;
                }
                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver=parseFloat(totalAvais) * (-1);
                totalAvais = 0;
            } else {
                totalOver=0;
            }
            $('#total-availability, .gs-header-content-avai span').html(totalAvais);
            $('#total-overload, .gs-header-content-over span').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);

            $(".popup-task-detail").html(listTaskDisplay);
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
        }
        function initMonth(){
            $('#filter_month').addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = totalCapacity = totalWorking = 0;
            widthDivRight = 0;
            if(datas.vocationMonth){
                headers += '<tr>';
                $.each(datas.vocationMonth, function(index, values){
                    var count = 0;
                    $.each(values, function(ind, val){
                        count++;
                        totalCount++;
                        totalVacation += parseFloat(val);
                        totalWorking += parseFloat(datas.working[index][ind]);
                    });
                    headers += '<td colspan="' + count + '" class="text-center">' + index + '</td>';
                });
                headers += '</tr><tr>';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocationMonth, function(index, values){
                    if(values){
                        $.each(values, function(ind, val){
                            var keyWl = index+'-'+ind;
                            widthDivRight += 50;
                            headers += '<td><div class="text-center">' + ind + '</div></td>';
                            var _vais = '';
                            if(val == 1){
                                _vais = 0;
                            }
                            var _wokingday=datas.working[index][ind];
                            var _capacity=parseFloat(_wokingday)-parseFloat(val);
                            avais += '<td><div id="avai-' + keyWl + '">' + _vais + '</div></td>';
                            vocs += '<td><div id="vocs-' + keyWl + '">' + val + '</div></td>';
                            work += '<td><div id="' + keyWl + '">' + 0 + '</div></td>';
                            over += '<td><div id="over-' + keyWl + '">' + 0 + '</div></td>';
                            capacity += '<td><div id="capacity-' + keyWl + '">' + _capacity + '</div></td>';
                            working += '<td><div id="working-' + keyWl + '">' + _wokingday + '</div></td>';
                        });
                    }
                });
                headers += '</tr>';
                avais += '</tr>';
                vocs += '</tr>';
                work += '</tr>';
                over += '</tr>';
                capacity += '</tr>';
                working += '</tr>';
            }
            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);


            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
            if(datas.listMonthDatas){
                $.each(datas.listMonthDatas, function(idFamily, values){
                    var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                    listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                    valTaskDisplay += '<tr class="family-group">';
                    $.each(datas.vocationMonth, function(index, values){
                        $.each(values, function(ind, val){
                            ind = index+'-'+ind;
                            valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+ind+'">&nbsp;</div></td>';
                        });
                    });
                    valTaskDisplay += '</tr>';
                    var sttActivity = 0;
                    $.each(values, function(idGlobal, value){
                        sttActivity++;
                        idGlobal = idGlobal.split('-');
                        if(idGlobal[0] === 'ac'){
                            var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.activity[idTask] ? datas.PriorityActivityTasks[idTask] : 0;
                                var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
                                var activityTaskName =  datas.groupNameTasks.activity[idTask] ?  datas.groupNameTasks.activity[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocationMonth, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = index+'-'+ind;
                                        var _value = valTask[ind] ? valTask[ind] : 0;
                                        valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                        if(!totalWorkload[ind]){
                                            totalWorkload[ind] = 0;
                                        }
                                        totalWorkload[ind] += _value;
                                        if(!listSumFamily[idFamily+'-'+ind]){
                                            listSumFamily[idFamily+'-'+ind] = 0;
                                        }
                                        listSumFamily[idFamily+'-'+ind] += _value;
                                        if(!totalFamily[idFamily]){
                                            totalFamily[idFamily] = 0;
                                        }
                                        totalFamily[idFamily] += _value;
                                    });
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else if(idGlobal[0] === 'pr'){
                            var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
                                var priorities = datas.priority.project[idTask] ? datas.priority.project[idTask] : '';
                                var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocationMonth, function(index, values){
                                    $.each(values, function(ind, val){
                                        ind = index+'-'+ind;
                                        var _value = valTask[ind] ? valTask[ind] : 0;
                                        valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                        if(!totalWorkload[ind]){
                                            totalWorkload[ind] = 0;
                                        }
                                        totalWorkload[ind] += _value;
                                        if(!listSumFamily[idFamily+'-'+ind]){
                                            listSumFamily[idFamily+'-'+ind] = 0;
                                        }
                                        listSumFamily[idFamily+'-'+ind] += _value;
                                        if(!totalFamily[idFamily]){
                                            totalFamily[idFamily] = 0;
                                        }
                                        totalFamily[idFamily] += _value;
                                    });
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            // do no thing
                        }
                    });
                });
            }
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);

                var getAvais = datas.avaiTotalMonth[getId] ? datas.avaiTotalMonth[getId] : 0;
                totalAvais += parseFloat(getAvais);
                if(parseFloat(getAvais) < 0 ) {
                    getOvers = parseFloat(getAvais) * (-1);
                    getAvais=0;
                } else {
                    getOvers=0;
                }

                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver = parseFloat(totalAvais) * (-1);
                totalAvais = 0;
            } else {
                totalOver = 0;
            }
            totalCapacity=totalWorking-totalVacation;
            $('#total-availability, .gs-header-content-avai span').html(totalAvais);
            $('#total-overload, .gs-header-content-over span').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);
            $('#total-capacity').html(totalCapacity);
            $('#total-working').html(totalWorking);

            $(".popup-task-detail").html(listTaskDisplay);
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
        }
        //init();
        initMonth();
        //filter
        $("#filter_year").click(function(e){
            $('#filter_date').removeClass('ch-current');
            $('#filter_month').removeClass('ch-current');
            $(this).addClass('ch-current');
            var headers = avais = vocs = work = over = working = capacity = '';
            var totalCount = totalVacation = 0;
            widthDivRight = 0;
            if(datas.vocationYear){
                headers += '<tr class="popup-header">';
                avais += '<tr id="total-avai-popup">';
                vocs += '<tr id="total-vocs-popup">';
                work += '<tr id="total-workload-popup">';
                over += '<tr id="total-over-popup">';
                capacity += '<tr id="total-capacity-popup">';
                working += '<tr id="total-working-popup">';
                $.each(datas.vocationYear, function(index, values){
                    totalCount++;
                    totalVacation += parseFloat(values);
                    var _wokingday = 0 ;
                    $.each(datas.working[index], function(ind, values){
                        _wokingday+=datas.working[index][ind];
                    });
                    var _capacity=parseFloat(_wokingday)-parseFloat(values);
                    headers += '<td class="text-center">' + index + '</td>';
                    avais += '<td><div id="avai-' + index + '">' + 0 + '</div></td>';
                    vocs += '<td><div id="vocs-' + index + '">' + values + '</div></td>';
                    work += '<td><div id="' + index + '">' + 0 + '</div></td>';
                    over += '<td><div id="over-' + index + '">' + 0 + '</div></td>';
                    capacity += '<td><div id="capacity-' + index + '">' + 0 + '</div></td>';
                    working += '<td><div id="working-' + index + '">' + 0 + '</div></td>';
                    widthDivRight += 50;
                });
            }
            headers += '</tr>';
            avais += '</tr>';
            vocs += '</tr>';
            work += '</tr>';
            over += '</tr>';
            capacity += '</tr>';
            working += '</tr>';

            $(".popup-header-2").html(headers);
            $(".popup-availa-2").html(avais);
            $(".popup-over-2").html(over);
            $(".popup-vaication-2").html(vocs);
            $(".popup-workload-2").html(work);
            $(".popup-capacity-2").html(capacity);
            $(".popup-working-2").html(working);

            // phan detail cua task
            var listTaskDisplay = '';
            var valTaskDisplay = '';
            var totalWorkload = [];
            var listSumFamily = [];
            var totalFamily = [];
            if(datas.listYearDatas){
                $.each(datas.listYearDatas, function(idFamily, values){
                    var familyName = datas.families[idFamily] ? datas.families[idFamily] : '';
                    listTaskDisplay += '<tr class="family-group"><td><div style="font-weight: bold;">&nbsp;' + familyName + '</div></td><td><div>&nbsp;</div></td><td class="ch-fam"><div id="total-fam-'+idFamily+'">&nbsp;</div></td></tr>';
                    valTaskDisplay += '<tr class="family-group">';
                    $.each(datas.vocationYear, function(index, values){
                        valTaskDisplay += '<td><div id="fam-'+idFamily+'-'+index+'">&nbsp;</div></td>';
                    });
                    valTaskDisplay += '</tr>';
                    var sttActivity = 0;
                    $.each(values, function(idGlobal, value){
                        sttActivity++;
                        idGlobal = idGlobal.split('-');
                        if(idGlobal[0] === 'ac'){
                            var activityName = datas.groupNames.activity[idGlobal[1]] ? datas.groupNames.activity[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + activityName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : 0;
                                var priorities = datas.priority.activity[idTask] ? datas.priority.activity[idTask] : '';
                                var activityTaskName = datas.groupNameTasks.activity[idTask] ? datas.groupNameTasks.activity[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + activityTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocationYear, function(index, values){
                                    var _value = valTask[index] ? valTask[index] : 0;
                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                    if(!totalWorkload[index]){
                                        totalWorkload[index] = 0;
                                    }
                                    totalWorkload[index] += _value;
                                    if(!listSumFamily[idFamily+'-'+index]){
                                        listSumFamily[idFamily+'-'+index] = 0;
                                    }
                                    listSumFamily[idFamily+'-'+index] += _value;

                                    if(!totalFamily[idFamily]){
                                        totalFamily[idFamily] = 0;
                                    }
                                    totalFamily[idFamily] += _value;
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else if(idGlobal[0] === 'pr'){
                            var projectName = datas.groupNames.project[idGlobal[1]] ? datas.groupNames.project[idGlobal[1]] : '';
                            listTaskDisplay += '<tr class="project-activity-group"><td><div style="font-weight: bold;">&nbsp;'+ sttActivity +'. ' + projectName + '</div></td><td><div>&nbsp;</div></td><td><div>&nbsp;</div></td></tr>';
                            valTaskDisplay += '<tr class="project-activity-group"><td colspan="' + totalCount + '"><div>&nbsp;</div></td></tr>';
                            var sttTask = 0;
                            $.each(value, function(idTask, valTask){
                                sttTask++;
                                valTaskDisplay += '<tr>';
                                //var idPriority = datas.priority.project[idTask] ? datas.priority.project[idTask] : 0;
                                var priorities = datas.priority.project[idTask] ? datas.priority.project[idTask] : '';
                                var projectTaskName = datas.groupNameTasks.project[idTask] ? datas.groupNameTasks.project[idTask] : '';
                                listTaskDisplay += '<tr><td class="list-task"><div>&nbsp;'+ sttActivity +'.'+ sttTask +'. ' + projectTaskName + '</div></td><td><div>' + priorities + '</div></td><td><div>&nbsp;</div></td></tr>';
                                $.each(datas.vocationYear, function(index, values){
                                    var _value = valTask[index] ? valTask[index] : 0;
                                    valTaskDisplay += '<td><div>' + _value + '</div></td>';
                                    if(!totalWorkload[index]){
                                        totalWorkload[index] = 0;
                                    }
                                    totalWorkload[index] += _value;
                                    if(!listSumFamily[idFamily+'-'+index]){
                                        listSumFamily[idFamily+'-'+index] = 0;
                                    }
                                    listSumFamily[idFamily+'-'+index] += _value;

                                    if(!totalFamily[idFamily]){
                                        totalFamily[idFamily] = 0;
                                    }
                                    totalFamily[idFamily] += _value;
                                });
                                valTaskDisplay += '</tr>';
                            });
                        } else {
                            // do nothing
                        }
                    });
                });
            }
            var totalWorkloads = totalAvais = 0;
            $('#total-workload-popup').find('td div').each(function(){
                var getId = $(this).attr('id');
                var getTotalWl = totalWorkload[getId] ? totalWorkload[getId].toFixed(2) : 0;
                totalWorkloads += parseFloat(getTotalWl);
                var getAvais = datas.avaiTotalYear[getId] ? datas.avaiTotalYear[getId] : 0;
                totalAvais += parseFloat(getAvais);
                if(getAvais < 0 ) {
                    getOvers = parseFloat(getAvais) * (-1);
                    getAvais=0;
                } else {
                    getOvers=0;
                }
                $('#total-avai-popup').find('#avai-'+getId).html(getAvais);
                $('#total-over-popup').find('#over-'+getId).html(getOvers);
                $('#'+getId).html(getTotalWl);
            });
            totalWorkloads = totalWorkloads.toFixed(2);
            totalAvais = totalAvais.toFixed(2);
            if(totalAvais < 0){
                totalOver = totalAvais*(-1);
                totalAvais = 0;
            } else {
                totalOver = 0;
            }
            $('#total-availability, .gs-header-content-avai span').html(totalAvais);
            $('#total-overload, .gs-header-content-over span').html(totalOver);
            $('#total-vacation').html(totalVacation);
            $('#total-workload').html(totalWorkloads);


            $(".popup-task-detail").html(listTaskDisplay);
            $(".popup-task-detail-2").html(valTaskDisplay);

            $('.popup-task-detail-2').find('.family-group td div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('fam-', '');
                var valSumFam = listSumFamily[idCheck] ? listSumFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).html(valSumFam);
            });
            $('.popup-task-detail').find('td.ch-fam div').each(function(){
                var idDivOfFamily = $(this).attr('id');
                var idCheck = idDivOfFamily.replace('total-fam-', '');
                var valSumFam = totalFamily[idCheck] ? totalFamily[idCheck].toFixed(2) : 0;
                $('#'+idDivOfFamily).css('text-align', 'right');
                $('#'+idDivOfFamily).html(valSumFam);
            });
            configPopup(widthDivRight);
            return false;
        });
        $("#filter_month").click(function(e){
            $('#filter_date').removeClass('ch-current');
            $('#filter_year').removeClass('ch-current');
            //filter year
            initMonth();
            configPopup(widthDivRight);
            return false;
        });
        $("#filter_week").click(function(e){
            //filter year
            return false;
        });
        var flag=0;
        $("#filter_date").click(function(e){
            $('#filter_month').removeClass('ch-current');
            $('#filter_year').removeClass('ch-current');
            //filter year
            init();
            if(flag==0) {
                configPopup(widthDivRight);
                flag=1;
            }
            return false;
        });

        // config cho phan hien thi popup
        function configPopup(withRight){
            var lWidth = $(window).width();
            var DialogFull = Math.round((95*lWidth)/100);
            var header = Math.round((93*lWidth)/100);
            var marginTile = Math.round((22*lWidth)/100);
            var tableLeft = Math.round((35*lWidth)/100);
            var tableRight = Math.round((56.7*lWidth)/100);
            var tableRightContent = Math.round((70*lWidth)/100);
            if(withRight <= tableRightContent){
                tableRightContent = withRight;
            }
            $('#gs-popup-header, #gs-popup-content').width(header);
            $('.gs-name-header').css('margin-left', marginTile);
            $('.table-left').width(tableLeft);
            $('.table-right').width(tableRight);
            $('#tb-popup-content-2').width(tableRightContent);
            //

            var lHeight =  $(window).height();
            var DialogFullHeight = Math.round((80*lHeight)/100);
            var heightDetail = Math.round((40*lHeight)/100);
            $('#gs-popup-content').height(heightDetail);
            $( "#showdetail" ).dialog({
                modal: true,
                width: DialogFull,
                height: DialogFullHeight,
                zIndex: 9999999
            });
        }
        configPopup(widthDivRight);
    },
    /*END*/
    requestPhasesData: function(requestPhasesDataCallback){
        var me = this;
        Ext.Ajax.request({
            scope: me,
            url: '/project_tasks/listPhasesJson/'+me.project_id,
            method: 'GET',
            failure: function (responsex, optionsx) {

            },
            success:function(responsex, optionsx) {
                var dataJSON = JSON.parse(responsex.responseText),
                    datas = [];

                for (var i = 0; i < dataJSON.length; i++) {
                    datas[i] = {
                        project_planed_phase_id:dataJSON[i].ProjectPhase.id,
                        project_planed_phase_text: dataJSON[i].ProjectPhase.name};
                }

                var newStorePhases =  Ext.create('Ext.data.Store',{
                    autoLoad: true,
                    fields: [
                        {name: 'project_planed_phase_id'},
                        {name: 'project_planed_phase_text', type: 'string'}
                    ],
                    data: datas
                });

                me.storePhases = newStorePhases;
                return requestPhasesDataCallback(true);
            }
        });
    },
    /**
     * Set time paris
     */
    setAndGetTimeOfParis: function(){
        //var _date = new Date().toLocaleString('en-US', {timeZone: 'Europe/Paris'}); // khong dung dc tren IE
        var _date = new Date(); // Lay Ngay Gio Thang Nam Hien Tai
        /**
         * Lay Ngay Gio Chuan Cua Quoc Te
         */
        var _day = _date.getUTCDate();
        var _month = _date.getUTCMonth() + 1;
        var _year = _date.getUTCFullYear();
        var _hours = _date.getUTCHours();
        var _minutes = _date.getUTCMinutes();
        var _seconds = _date.getUTCSeconds();
        var _miniSeconds = _date.getUTCMilliseconds();
        /**
         * Tinh gio cua nuoc Phap
         * Nuoc Phap nhanh hon 2 gio so voi gio Quoc te.
         */
        _hours = _hours + 2;
        if(_hours > 24){
            _day = _day + 1;
            if(_day > daysInMonth(_month, _year)){
                _month = _month + 1;
                if(_month > 12){
                    _year = _year + 1;
                }
            }
        }
        _day = _day < 10 ? '0'+_day : _day;
        _month = _month < 10 ? '0'+_month : _month;
        return _hours + ':' + _minutes + ' ' + _day + '/' + _month + '/' + _year;
    },
    getCurrentDate: function(){
        var date = new Date();
        date.setHours(0);
        date.setMinutes(0);
        date.setSeconds(0);
        date.setMilliseconds(0);
        return date;
    },
    buildTreeColumns:function(){
        var me = this;
        function render_weight(a,b,c,d,e){
            var weight;
            if((c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('is_part') && (c.get('is_part') == "true"))) {
                return "";
            }
            weight = c.get('weight');
            if(c.get('leaf')){
                return '<span class="x-tree-elbow" style="display: block; padding-left:20px; padding-top:4px; margin-left:5px;">'+weight+'</span>';
            } else {
                return '<span style="display: block; padding-left:10px;">'+weight+'</span>';
            }
        }

        function number_format (number, decimals, dec_point, thousands_sep) {
          number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
          var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
              var k = Math.pow(10, prec);
              return '' + Math.round(n * k) / k;
            };
          // Fix for IE parseFloat(0.55).toFixed(0) = 0;
          s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
          if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
          }
          if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
          }
          return s.join(dec);
        }
        function render_start_date(val,b,c) {
            var yeal = new Date(val).getFullYear();
            val = String(Ext.util.Format.date(val, 'd-m-y'));
            if(val == '01-01-70'){
                return '';
            }
            var curDate = me.getCurrentDate().getTime();
            var sDate = val.split('-');
            sDate = new Date(yeal + '-' + sDate[1] + '-' + sDate[0]).getTime();

            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id')=='root') || (c.childNodes.length)){
                return '<span class="task_none">' + val + '</span>';
            } else {
                var _classDate = 'task_blue';
                var _title = '';
                var consume = parseFloat(is_manual_consumed ? c.get('manual_consumed') : c.get('consumed')),
                    workload = parseFloat( c.get('estimated') );
                if( isNaN(consume) )consume = 0;
                if( isNaN(workload) )workload = 0;
                if(consume == 0 && sDate < curDate && workload > 0 && c.get('task_status_st') != 'CL'){
                    _classDate = 'task_red';
                    _title = i18n("No consumed and the current date > start date");
                }
                return '<span class="' + _classDate + '" title="' + _title + '">' + val + '</span>';
            }

        }

        function render_end_date(val,b,c) {
            var yeal = new Date(val).getFullYear();
            val = String(Ext.util.Format.date(val, 'd-m-y'));
            if(val == '01-01-70'){
                return '';
            }
            var _date = me.getCurrentDate().getTime();
            var sDate = val.split('-');
            sDate = new Date(yeal + '-' + sDate[1] + '-' + sDate[0]).getTime();
            //task_status_st
            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id')=='root') || (c.childNodes.length)){
                return '<span class="task_none">' + val + '</span>';
            } else {
             var _classDate = 'task_blue';
                var _title = '';
                if(c.get('task_status_st') && c.get('task_status_st') != 'CL' && _date > sDate){
                    _classDate = 'task_red';
                    _title = i18n("Task not closed and the current date > end date");
                }
                return '<span class="' + _classDate + '" title="' + _title + '">' + val + '</span>';
            }

        }
        function render_initial_start_date(a,b,c,d,e){
            var val = c.get('initial_task_start_date');
            val = String(Ext.util.Format.date(val, 'd-m-y'));
            if(val == '01-01-70'){
                val = '';
            }
            return val;
        }
        function render_initial_end_date(a,b,c,d,e){
            var val = c.get('initial_task_end_date');
            val = String(Ext.util.Format.date(val, 'd-m-y'));
            if(val == '01-01-70'){
                val = '';
            }
            return val;
        }
        function render_initial_start_date_none(a,b,c,d,e){
            var val = '';
            return val;
        }
        function render_initial_end_date_none(a,b,c,d,e){
            var val = '';
            return val;
        }
        function render_phase(a,b,c,d,e) {
            if(!c.isLeaf()){
                return " ";
            }
            return c.get('project_planed_phase_text');
        }

        function render_priority(a,b,c,d,e) {
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return " ";
            }
            if(!c.isLeaf()&&c.childNodes.length>0){
                return " ";
            }
            return c.get('task_priority_text');
        }

        function render_status(a,b,c,d,e) {
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return " ";
            }
            if(!c.isLeaf()&&c.childNodes.length>0){
                return " ";
            }
            return c.get('task_status_text');
        }

        function render_milestone(a,b,c,d,e) {
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return " ";
            }
            // if(!c.isLeaf()&&c.childNodes.length>0){
                // return " ";
            // }
            var val = c.get('milestone_text') && c.get('milestone_text') !== null ? c.get('milestone_text') : '';
            var m_id = c.get('milestone_id') && c.get('milestone_id') !== null ? c.get('milestone_id') : 0;
            var cls = '';
            if( m_id != 0){
                cls = (_milestoneColor !== undefined && _milestoneColor[m_id] !== undefined) ? _milestoneColor[m_id] : 'milestone-mi';
            }
            return '<span class="' + cls + '">' + val + '</span>';
        }

        function render_profile(a,b,c,d,e) {
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return " ";
            }
            if(!c.isLeaf()&&c.childNodes.length>0){
                return " ";
            }
            return c.get('profile_text');
        }

        function render_assign(a,b,c,d,e) {
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return " ";
            }
            if(!c.isLeaf()&&c.childNodes.length>0){
                return " ";
            }
            return c.get('task_assign_to_text');
        }

        function render_estimated(a,b,c,d,e){
            var val = c.get('estimated')===null||c.get('estimated')==0?0:c.get('estimated');
            val=parseFloat(val);
            var saveVal = val;
            val = number_format(val, 2, ',', ' ');
            if(c.get('id')=='root'){
                var wExternal=$('.external-line .display-workload span.display-value').text();
                var budget=$('.internal-line .display-budget span.display-value').text();
                //wExternal=wExternal.replace(/ /g, '');
                wExternal=parseFloat(wExternal).toFixed(2);
                budget=parseFloat(budget).toFixed(2);
                var wInterNal=parseFloat(saveVal-wExternal).toFixed(2);
                var bVar=(budget!='0.00')?((wInterNal/budget)-1):0;
                bVar=parseFloat(bVar).toFixed(2);
                bVar=parseFloat(bVar*100).toFixed(2);
                $('.internal-line .display-workload span.display-value').text(wInterNal);
                $('.internal-line .display-var span.display-value').text(bVar+' %');
                if(bVar>0){
                    $('.internal-line .display-var span.display-value').removeClass('var-green');
                    $('.internal-line .display-var span.display-value').addClass('var-red');
                }else{
                    $('.internal-line .display-var span.display-value').removeClass('var-red');
                    $('.internal-line .display-var span.display-value').addClass('var-green');
                }
            }
            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id')=='root') || (c.childNodes.length)){
                return '<span class="task_none" style="float: right;">' + val + ' J.H</span>';
            } else {
                var _classDate = 'task_blue';
                var _title = '';
                var consume = is_manual_consumed ? c.get('manual_consumed') : c.get('consumed');
                consume = parseFloat(consume);
                consume = number_format(consume, 2, ',', ' ');
                if( parseFloat(consume) > parseFloat(val)){
                    _classDate = 'task_red';
                    _title = i18n("Consumed > Workload");
                }
                return '<span class="' + _classDate + '" title="' + _title + '"  style="text-align: right;">' + val + ' J.H</span>';
            }
        }
        function render_eac(a,b,c,d,e){
			var val = c.get('eac')===null||c.get('eac')==0?0:c.get('eac');
            val = parseFloat(val);
			val = number_format(val, 2, ',', ' ');
			 return '<span  style="text-align: right;">' + val + ' J.H</span>';
           
        }
        function render_initial_estimated(a,b,c,d,e){
            var val = c.get('initial_estimated')===null||c.get('initial_estimated')==0?0:c.get('initial_estimated');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            //if(val=='0.00'){val='';}
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0} J.H</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0} J.H</span>',
                        val
                    );
        }
        function render_overload(a,b,c,d,e){
            var val = c.get('overload')===null||c.get('overload')==0?0:c.get('overload');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('overload') > 0){
                return Ext.String.format(
                        '<span style="background-color: red;display: block;text-align: right; color: white;">{0}</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
        }
        function render_manual_overload(a,b,c,d,e){
            var val = c.get('manual_overload')===null||c.get('manual_overload')==0?0:c.get('manual_overload');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('manual_overload') > 0){
                return Ext.String.format(
                        '<span style="background-color: red;display: block;text-align: right; color: white;">{0}</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
        }

        function render_duration(a,b,c,d,e){
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        c.get('duration')
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        c.get('duration')
                    );
        }

        function render_predecessor(a,b,c,d,e){
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return '';
            }
			outText = '';
			title = (listTaskName[c.get('predecessor')]) ? listTaskName[c.get('predecessor')] : '';
			if(c.get('predecessor')){
				title = (listTaskName[c.get('predecessor')]) ? listTaskName[c.get('predecessor')] : '';
				outText = '<a class="predecessor-task" onmouseover="predecessor_hightlightTask(' + c.get('predecessor') + ')" onmouseout="predecessor_unhightlightTask()" data-predecessor="' + c.get('predecessor') + '" title="' + title + '" style="text-align: right;">' + c.get('predecessor') +'</a>';
			}
			return outText;

        }

        function render_unit_price(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            return Ext.String.format(
                        '<span style="float: right;">{0} '+ budgetCurrency +'</span>',
                        val
                    );
        }

        function render_estimated_euro(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            return Ext.String.format(
                        '<span style="float: right;">{0} '+ budgetCurrency +'</span>',
                        val
                    );
        }

        function render_consume_euro(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            // return Ext.String.format(
            //     '<span style="float: right;">{0} €</span>',
            //     val
            // );
            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id')=='root') || (c.childNodes.length)){
                return '<span class="task_none" style="float: right;">' + val + ' '+ budgetCurrency +'</span>';
            } else {
             var _classDate = 'task_blue';
                var _title = '';
                if( val > c.get('workload_euro') ){
                    _classDate = 'task_red';
                    _title = i18n("Consumed € > Workload €");
                }
                return '<span class="' + _classDate + '" title="' + _title + '" style="text-align: right;">' + val + ' '+ budgetCurrency +'</span>';
            }
        }

        function render_remain_euro(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            return Ext.String.format(
                '<span style="float: right;">{0} '+ budgetCurrency +'</span>',
                val
            );
        }

        function render_workload_euro(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            // return Ext.String.format(
            //     '<span style="float: right;">{0} €</span>',
            //     val
            // );
            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id')=='root') || (c.childNodes.length)){
                return '<span class="task_none" style="float: right;">' + val + ' '+ budgetCurrency +'</span>';
            } else {
             var _classDate = 'task_blue';
                var _title = '';
                if( val > c.get('estimated') ){
                    _classDate = 'task_red';
                    _title = i18n("Workload € > Workload");
                }
                return '<span class="' + _classDate + '" title="' + _title + '" style="text-align: right;">' + val + ' '+ budgetCurrency +'</span>';
            }
        }

        function render_completed(a,b,c,d,e){
            var val = c.get('completed')===null||c.get('completed')==0?0:c.get('completed');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0} %</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0} %</span>',
                        val
                    );
        }

        function render_id(a,b,c,d,e){

            var id = c.get('id');    
            if( (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('is_part') && (c.get('is_part') == "true")) || c.get('id') == 'root') {
                return "";
            }
            return '<span id="x-task-' + id + '">' + id + '</span>';
        }

        function render_remain(a,b,c,d,e){
            var val = c.get('remain')===null||c.get('remain')==0?0:c.get('remain');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
        }
        var _link = '/project_tasks/detail/';
        var _link_activitiesDetail = '/activities/details/';
        var _link_activitiesDetailTotal = '/activities/detail/';
        function render_consumed(a,b,c,d,e){
            var val = c.get('consumed')===null||c.get('consumed')==0?0:c.get('consumed');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('special')==1){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            if(c.get('is_part') && (c.get('is_part') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                if(c.get('is_activity') && (c.get('is_activity') == "true")){
                    return Ext.String.format(
                        '<b style="float: right;"><a target="_blank" href="' +_link_activitiesDetail+ '{1}">{0}</a></b>',
                        val,
                        c.get('id_activity')
                    );
                } else {
                    return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
                }
            }
            if(c.get('id') == 'root'){
                if(c.get('consumed') != 0){
                    return Ext.String.format(
                        '<b style="float: right;"><a target="_blank" href="' +_link_activitiesDetailTotal+ '{1}">{0}</a></b>',
                        val,
                        c.get('id_activity')
                    );
                } else {
                     return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
                }

            }
            if(c.get('consumed') != 0){
                return Ext.String.format(
                    '<b style="float: right;"><a target="_blank" href="' +_link+ '{1}">{0}</a></b>',
                    val,
                    c.get('id')
                );
            } else {
                 return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
        }
        function render_manual_consumed(a,b,c,d,e){
            var val = c.get('manual_consumed')===null||c.get('manual_consumed')==0?0:c.get('manual_consumed');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
        }

        function render_wait(a,b,c,d,e){
            var val = c.get('wait')===null||c.get('wait')==0?0:c.get('wait');
            val=parseFloat(val);
            val = number_format(val, 2, ',', ' ');
            if(c.get('special')==1){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            if(c.get('is_part') && (c.get('is_part') == "true")){
                return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                if(c.get('is_activity') && (c.get('is_activity') == "true")){
                    return Ext.String.format(
                        '<b style="float: right;"><a target="_blank" href="' +_link_activitiesDetail+ '{1}">{0}</a></b>',
                        val,
                        c.get('id_activity')
                    );
                } else {
                    return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
                }
            }
            if(c.get('id') == 'root'){
                if(c.get('wait') != 0){
                    return Ext.String.format(
                        '<b style="float: right;"><a target="_blank" href="' +_link_activitiesDetailTotal+ '{1}">{0}</a></b>',
                        val,
                        c.get('id_activity')
                    );
                } else {
                     return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
                }

            }
            if(c.get('wait') != 0){
                return Ext.String.format(
                    '<b style="float: right;"><a target="_blank" href="' +_link+ '{1}">{0}</a></b>',
                    val,
                    c.get('id')
                );
            } else {
                 return Ext.String.format(
                        '<span style="float: right;">{0}</span>',
                        val
                    );
            }
        }

        function render_cls(a,b,c,d,e){
            if( c.get('is_nct') != null && c.get('is_nct') == 1 ){
                c.set('iconCls', 'x-tree-icon-special');
            } else if( c.get('is_part') && c.get('is_part') == 'true' ){
                c.set('iconCls', 'x-tree-icon-part');
            }
            var val = c.get('task_title')===null||c.get('task_title')==''?'':c.get('task_title');
            if(c.get('special') == 1 ){
                return Ext.String.format(
                    '<span style="color:#800080">{0}</span>',
                    val
                );
            }
            return Ext.String.format(
                '<span class="task-'+ c.get('id')+'">{0}</span>',
                val
            );
        }

        function render_euro(a,b,c,d,e){
           val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = val.toFixed(2);
            return Ext.String.format(
                '<span style="float: right;">{0} &euro;</span>',
                val
            );
        }

        function render_percent(a,b,c,d,e){
            val = parseFloat(a);
            if( isNaN(val) )val = 0.0;
            val = number_format(val, 2, ',', ' ');
            return Ext.String.format(
                '<span style="float: right;">{0} %</span>',
                val
            );
        }

        function render_text(a,b,c,d,e){
            if((c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id') == 'root') || c.get('is_previous')){
                return '';
            }
            var html = '';
			debug = c;
			if( c.get('text_1') ){
				if( c.get('read_status')){
					html += '<img src="' + webroot + 'img/extjs/icon-text-has-msg.png" alt="' + c.get('id') + '" onclick="openCommentDialog.call(this)" class="img read img-dialog img-comment-' + c.get('id') + '">';					
				}else{
					html += '<img src="' + webroot + 'img/extjs/icon-text-unread.png" alt="' + c.get('id') + '" onclick="openCommentDialog.call(this)" class="img unread img-dialog img-comment-' + c.get('id') + '">';
				}
			}else{
				html += '<img src="' + webroot + 'img/extjs/icon-text-no-msg.png" alt="' + c.get('id') + '" onclick="openCommentDialog.call(this)" class="img img-dialog img-comment-' + c.get('id') + '">';
			}
            return html;
        }

        function render_file(a, b, c, d, e){
			// console.log( a);
            if( (c.get('is_part') && (c.get('is_part') == "true")) || (c.get('is_phase') && (c.get('is_phase') == "true")) || (c.get('id') == 'root') || c.get('is_previous') ){
                return '';
            }
            // var html = '', cls = a ? 'x-hide' : '', ncls = !a ? 'x-hide' : '';
            // html += '<img src="' + webroot + 'img/extjs/icon-task-folder.png" alt="' + c.get('id') + '" onclick="openAttachmentDialog.call(this)" class="img img-dialog ' + cls + '">';
            // if( a ) {
                // aa = a.split(':');
                // if( aa[0] == 'file' ) {
                    // html += '<img src="' + webroot + 'img/download.png" alt="' + c.get('id') + '" onclick="openAttachment.call(this)" class="img img-file ' + ncls + '">';
                // } else {
                    // html += '<img src="' + webroot + 'img/extjs/icon-url.png" alt="' + c.get('id') + '" onclick="openAttachment.call(this)" class="img img-url ' + ncls + '">';
                // }
            // }
            // html += '<img src="' + webroot + 'img/delete.png" alt="' + c.get('id') + '" onclick="deleteAttachment.call(this)" class="img img-delete ' + ncls + '">';
            // return html;
			var html = '';
			if(a){
				if( c.get('attach_read_status')){ // read
					html += '<img src="' + webroot + 'img/extjs/attachment-read.png" alt="' + c.get('id') + '" onclick="openAttachmentDialog.call(this)" class="img img-dialog xxx">';
				}else{ // unread
					html += '<img src="' + webroot + 'img/extjs/attachment-unread.png" alt="' + c.get('id') + '" onclick="openAttachmentDialog.call(this)" class="img img-dialog yyy">';
				}
			}else{ // No attachment
				html += '<img src="' + webroot + 'img/extjs/attachment-nofile.png" alt="' + c.get('id') + '" onclick="openAttachmentDialog.call(this)" class="img img-dialog no-attachment zzz">';
			}
			return html;
			
        }

        function render_slider(a , b, c, d, e){
            if(c.get('is_phase') && (c.get('is_phase') == "true")){
                return '';
            }
            return Ext.String.format(
                '<span style="float: right;">{0}</span>',
                c.get('slider') ? c.get('slider') : ''
            );
        }

        var taskload = [];
        var $confConsumed = null;
        if(jQuery('#pm_acl').val()=="pm"||jQuery('#pm_acl').val()=="admin"){
            var $confConsumed = {
                xtype: 'textfield',
                selectOnFocus: true
            };
        }
        var columns = {
            ID: {
                text: i18n('ID'),
                width: 60,
                dataIndex: 'id',
                renderer: render_id,
                sortable: false,
                menuDisabled: true,
                mobileHidden: Azuree.isMobile ? true : false,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Task: {
                xtype: 'treecolumn', //this is so we know which column will show the tree
                text: i18n('Task'),
                cls: 'x-icon',
                width: 250,
                sortable: false,
                renderer: render_cls,
                // menuDisabled: true,
                id: 'wd-task-title',
                dataIndex: 'task_title',
                editor: {
                    allowBlank: false,
                    xtype: 'textfield',
                    selectOnFocus: true,
                    validator: function(value){
                        value = Ext.String.trim(value);
                        return value.length < 1 ? this.blankText : true;
                    }
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    },
                    headerclick: {
                        fn: function(panel, col, e, el){
                            me.togglePhase(e);
                        },
                        scope: this
                    }
                }
            },
            Order: {
                text: i18n('Order'),
                //flex: 1,
                width: 50,
                dataIndex: 'weight',
                renderer: render_weight,
                sortable: false,
                menuDisabled: true,
                mobileHidden: Azuree.isMobile ? true : false
            },
            Priority: {
                text: i18n('Priority'),
                width: 80,
                dataIndex: 'task_priority_id',
                cls: 'x-icon x-icon-filter',
                sortable: false,
                menuDisabled: true,
                renderer: render_priority,
                editor   : {
                    id: 'priority-editor',
                    xtype:'combo',
                    store: me.storePriority,
                    displayField:'task_priority_text',
                    valueField: 'task_priority_id',
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    emptyText: i18n('Priority'),
                    listeners:{
                        select: function(combo, record, index) {
                            combo.ownerCt.context.record.set('task_priority_text',record.get('task_priority_text'));
                        }
                    }
                },
                listeners: {
                    headerclick: {
                        fn: function(panel, col, e, el){
                            me.openPriorityFilter(col, e, el);
                        },
                        scope: this
                    },
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Status: {
                text: i18n('Status'),
                width: 80,
                dataIndex: 'task_status_id',
                cls: 'x-icon x-icon-filter',
                renderer: render_status,
                sortable: false,
                menuDisabled: true,
                editor   : {
                    xtype:'combo',
                    id: 'status-editor',
                    //allowBlank: false,
                    store: me.storeStatuses,
                    displayField:'task_status_text',
                    valueField: 'task_status_id',
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    emptyText: i18n('Status'),
                    listeners:{
                        select: function(combo, record, index) {
                            combo.ownerCt.context.record.set('task_status_text',record.get('task_status_text'));

                        }
                    }
                },
                listeners: {
                    headerclick: {
                        fn: function(panel, col, e, el){
                            me.openStatusFilter(col, e, el);
                        },
                        scope: this
                    },
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Milestone: {
                text: i18n('Milestone'),
                width: 100,
                dataIndex: 'milestone_id',
                cls: 'x-icon x-icon-filter',
                renderer: render_milestone,
                sortable: false,
                menuDisabled: true,
                editor   : {
                    xtype:'combo',
                    id: 'milestone-editor',
                    //allowBlank: false,
                    store: me.storeMilestone,
                    displayField:'milestone_text',
                    valueField: 'milestone_id',
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    emptyText: i18n('Milestone'),
                    listeners:{
                        select: function(combo, record, index) {
                            combo.ownerCt.context.record.set('milestone_text',record.get('milestone_text'));

                        }
                    }
                },
                listeners: {
                    headerclick: {
                        fn: function(panel, col, e, el){
                            me.openMilestoneFilter(col, e, el);
                        },
                        scope: this
                    },
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Profile: {
                text: i18n('Profile'),
                //flex: 5,
                width: 120,
                dataIndex: 'profile_id',
                cls: 'x-icon x-icon-filter',
                renderer: render_profile,
                sortable: false,
                menuDisabled: true,
                mobileHidden: Azuree.isMobile || (!Azuree.isMobile && !showProfile) ? true : false,
                editor   : {
                    xtype:'combo',
                    id: 'profile-editor',
                    //allowBlank: false,
                    store: me.storeProfiles,
                    displayField: 'profile_text',
                    valueField: 'profile_id',
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    emptyText: '',
                    listeners:{
                        select: function(combo, record, index) {
                            combo.ownerCt.context.record.set('profile_text',record.get('profile_text'));
                        }
                    }
                },
                listeners: {
                    headerclick: {
                        fn: function(panel, col, e, el){
                            me.openProfileFilter(col, e, el);
                        },
                        scope: this
                    },
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            AssignedTo: {
                text: i18n('AssignedTo'),
                //flex: 7,
                width: 400,
                dataIndex: 'task_assign_to_id',
                renderer: render_assign,
                sortable: false,
                typeAhead: false,
                editable: true,
                itemId : 'comboChangeMain',
                id : 'comboChangeMain',
                menuDisabled: false,
                editor   : {
                    xtype:'combo',
                    id: 'comboChange',
                    itemId : 'comboChange',
                    editable: false,
                    store: me.storeAssigned,
                    displayField:'task_assign_to_text',
                    valueField: 'task_assign_to_id',
                    multiSelect: true,
                    typeAhead: false,
                    /*autoSelect: true,
                    triggerAction: 'all',
                    disableKeyFilter : false,
                    enableKeyEvents: true,
                    lastQuery: '',
                    mode: 'local',*/
                    //lazyRender: true,
                    emptyText: i18n('Resources'),
                    tpl:
                    '<ul class="x-list-plain wd-list-assign-to">' +
                        '<li style="background:white ; height: 25px ;border: none; clear: both;" class="x-grid-group-hd x-grid-group-title">'+
                            '<div class="context-menu-filter" style="display: block;"><span><input type="text" onclick="return false;" onkeyup="return filterEmployee(this.value,event)" rel="no-history" class="txtsearch"></span></div>'+
                        '</li>' +
                        '<tpl for=".">' +
                            '<tpl if="is_selected &gt; 0">' +
                                '<li id="li-{task_assign_to_id}-{is_profit_center}" class="x-boundlist-item item-actif-{actif} x-boundlist-selected" rel="li-employee">' +
                            '<tpl else>' +
                                '<li id="li-{task_assign_to_id}-{is_profit_center}" class="x-boundlist-item item-actif-{actif}" rel="li-employee">' +
                            '</tpl>' +
                                '{task_assign_to_text}' +
                            '</li>' +
                            '<tpl if="actif &gt; 0">' +
                                '<div style="float: right" rel="li-{task_assign_to_id}-{is_profit_center}" title="{task_assign_to_id}-{is_profit_center}" class="loadmd viewloadmd item-actif-{actif}" ><a></a></div>' +
                            '</tpl>' +
                        '</tpl>' +
                    '</ul>',
                    listConfig: {
                        listeners: {
                            containerClick:function(){
                                return false;
                            },
                            beforeshow: function(eOpts){
                                /*me.allowReloadManDay = true;
                                if(typeof listTaskHasLoad[me.currentId] == 'undefined' && listTaskHasLoad[me.currentId] == null){
                                    me.requestAfterClick(me.currentId);
                                }else{
                                    var curentTask = {
                                        id:  me.currentId,
                                        start: me.currentStartDate,
                                        end: me.currentEndDate
                                    };
                                    me.requestAssignWhenClick(curentTask, 'show','');
                                }    */
                                // if(me.currentId==me.curentTaskLoadManDay)
                                // {
                                //  var curentTask = {
                                //      id:  me.currentId,
                                //      start: me.currentStartDate,
                                //      end: me.currentEndDate,
                                //      assign : me.valueAssignAfterLoadMD
                                //  };
                                //  me.requestAssignWhenClick(curentTask, 'show','');
                                // }
                                // else
                                // {
                                this.disable(true);
                                var t = this;
                                setTimeout(me.requestAfterClick(me.currentId, function(){
                                    t.enable();
                                }), 500);
                                    //me.requestAfterClick(me.currentId);
                                // }
                            },
                            show: function(eOpts){
                            },
                            hide: function(){
                                me.allowReloadManDay = false;
                                me.curentTaskLoadManDay = me.currentId;
                            },
                            el:  {
                                delegate: '.loadmd',
                                click: function(cmp, a ) {
                                    if(a.id){
                                        var currentDate = new Date();
                                        var _end = me.currentEndDate ? me.currentEndDate : currentDate;
                                        var _start = me.currentStartDate ? me.currentStartDate : currentDate;
                                        var inforTasks = {
                                            'name': me.currentTaskName,
                                            'start': _start,
                                            'end': _end,
                                            'workload': me.currentWorkload
                                        };
                                        me.showDetail(a.id , me.currentId , a.rel , inforTasks);
                                        var curentTask = {
                                            id:  me.currentId,
                                            start: me.currentStartDate,
                                            end: me.currentEndDate,
                                            assign : me.valueAssignAfterLoadMD
                                        };
                                        me.requestAssignWhenClick(curentTask, 'show', a.id);
                                    }else{
                                        var curentTask = {
                                            id:  me.currentId,
                                            start: me.currentStartDate,
                                            end: me.currentEndDate
                                        };
                                        me.requestAssignWhenClick(curentTask, 'loadmd', a.title);
                                    }
                                }
                            }
                        }
                    },
                    listeners:{
                        select: function(combo, record, index) {  //Fire when an item is selected
                            var is_profit = [];
                            var id_refer_flag = [];
                            for(var i = 0; i < record.length; i++){
                                is_profit.push(record[i].data.is_profit_center);
                                if(id_refer_flag[record[i].data.task_assign_to_id]) {
                                    //do nothing
                                } else {
                                    id_refer_flag[record[i].data.task_assign_to_id] = record[i].data.is_profit_center;
                                }
                            };
                            //INSERT FLAG (PC OR NO) FOR REFERENCE ID
                            combo.ownerCt.context.record.set('id_refer_flag',id_refer_flag);
                            combo.ownerCt.context.record.set('task_assign_to_text', combo.rawValue);
                            combo.ownerCt.context.record.set('is_profit_center', is_profit);
                        },
                        beforeselect: function( combo, record, index, eOpts ){
                            //if(record.data.actif == 0)
                            //{
                            //  return false;
                            //}
                            //combo.setValue(index);
                            //combo.setRawValue(record.raw.task_assign_to_text);
                        },
                        resize: function(a, width, height, oldWidth, oldHeight, eOpts){
                            //var currenwidth = width -280;
                        },
                    }
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Startdate: {
                text: i18n('Startdate'),
                width: 100,
                minWidth: 100,
                //flex: 1.5,
                dataIndex: 'task_start_date',
                sortable: false,
                // menuDisabled: true,
                renderer: render_start_date,
                editor: {
                    xtype: 'datefield',
                    id: 'startdt',
                    // vtype: 'daterange',
                    endDateField: 'enddt',
                    format: 'd-m-y',
                    listeners: {
                        change: function(thisfield, newValue, oldValue, eOpts){
                            var _datas = me.store.root.childNodes[0].childNodes;
                            var predec = me.valuePredecessor;
                            me.validateDateTime(newValue, thisfield, predec, _datas);
                            if(newValue != oldValue){
                                var _node = me.store.getNodeById(me.currentId);
                                _node.set('callKeepDuration', true);
                            }
                        },
                        focus: function(thisfield){
                            var endDateFieldVal = (Ext.getCmp('startdt').rawValue.length);
                            var startDateFieldVal = (Ext.getCmp('enddt').rawValue.length);
                            me.valuePredecessor = Ext.getCmp('predecessor').value;
                            var _datas = me.store.root.childNodes[0].childNodes;
                            var predec = me.valuePredecessor;
                            me.validateDateTimeFocus(Ext.getCmp('startdt').rawValue, thisfield, predec, _datas);
                        }

                    }
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Enddate: {
                text: i18n('Enddate'),
                width: 100,
                minWidth: 100,
                //flex: 1.5,
                dataIndex: 'task_end_date',
                sortable: false,
                // menuDisabled: true,
                renderer: render_end_date,
                editor: {
                    xtype: 'datefield',
                    id: 'enddt',
                    // vtype: 'daterange',
                    startDateField: 'startdt',
                    format: 'd-m-y',
                    listeners: {
                        change: function(thisfield, newValue, oldValue, eOpts){
                            me.validateDateTime(newValue, thisfield, '', '');
                            if(newValue != oldValue){
                                var _node = me.store.getNodeById(me.currentId);
                                _node.set('callKeepDuration', true);
                            }
                        },
                        focus: function(thisfield){
                            var endDateFieldVal = (Ext.getCmp('startdt').rawValue.length);
                            // var startDateFieldVal = (Ext.getCmp('enddt').rawValue.length);

                            // //startDateFieldVal <> 0 =>
                            // //      enddt exist
                            // if(startDateFieldVal){
                            //     if(endDateFieldVal){
                            //         me.validateDateTimeFocus(Ext.getCmp('startdt').rawValue, thisfield, '', '');
                            //     }else{
                            //         console.log(Ext.getCmp('startdt').rawValue);
                            //         me.validateDateTimeFocus(Ext.getCmp('startdt').rawValue, thisfield, '', '');
                            //     }
                            // }else{
                            //     if(endDateFieldVal){
                            //         me.validateDateTimeFocus(Ext.getCmp('startdt').rawValue, thisfield, '', '');
                            //     }else{
                            //         me.validateDateTime(false, thisfield, '', '');
                            //     }
                            // }

                            if (endDateFieldVal) {
                                me.validateDateTimeFocus(Ext.getCmp('startdt').rawValue, thisfield, '', '');
                            }else{
                                me.validateDateTime(false, thisfield, '', '');
                            }
                        }
                    }
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Duration: {
                text: i18n('Duration'),
                //flex: 1.5,
                width: 80,
                dataIndex: 'duration',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_duration,
                editor: {
                    id: 'txtduration',
                    xtype: 'textfield',
                    listeners: {
                        change: function(thisfield, newValue, oldValue, eOpts){
                            if(newValue != oldValue){
                                var _node = me.store.getNodeById(me.currentId);
                                _node.set('callKeepDuration', true);
                            }
                        }
                    }
                },
                mobileHidden: Azuree.isMobile ? true : false,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Predecessor: {
                text: i18n('Predecessor') ,
                //flex: 1.5,
                width: 80,
                dataIndex: 'predecessor',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_predecessor,
                editor: {
                    xtype: 'textfield',
                    id: 'predecessor',
                    listeners: {
                        change: function(thisfield, newValue, oldValue, eOpts){
                            var _node = me.store.getNodeById(newValue);
                            if(_node){
								var _endDateParent = _node.get('task_end_date');
								if(_endDateParent){
									var start = thisfield.up('panel').down('#startdt');
									var childStartDate = new Date(_endDateParent.getFullYear(), _endDateParent.getMonth(), _endDateParent.getDate() + 1);
									start.setValue(childStartDate);
								}
                            }else{
								var _startDateCurrent = me.store.getNodeById(me.currentId).get('task_start_date');
								var start = thisfield.up('panel').down('#startdt');
								start.setValue(_startDateCurrent);
								if(newValue){
									// Nhap sai task_id (task_id khong ton tai)
									// Chua lam dc
								}
								
							}
                        },
						focus: function(thisfield, newValue, oldValue, eOpts){     
							var _node = me.store.getNodeById(me.currentId);
							_record = thisfield.ownerCt.context.rowIdx;
							var recordPrevous = me.getStore().getAt(_record - 1);
							
							if(_node && !thisfield.getValue()){
								 var _predecessor = _node.get('predecessor');
								 if(!_predecessor){
									 if(!(recordPrevous.get('is_part') == "true" ||  recordPrevous.get('is_phase') == "true" )){
										thisfield.setValue(recordPrevous.get('id'));
									}
								 }
							}
                        },
                    }
                },
                mobileHidden: Azuree.isMobile ? true : false
            },
            Workload: {
                text: i18n('Workload') ,
                width: 80,
                minWidth: 80,
                dataIndex: 'estimated',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_estimated,
                editor: {
                    xtype: 'textfield',
                    selectOnFocus: true
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Overload: {
                text:i18n('Overload') ,
                //flex: 1.5,
                width: 80,
                dataIndex: 'overload',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_overload,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            ManualOverload: {
                text:i18n('Overload') ,
                //flex: 1.5,
                width: 80,
                dataIndex: 'manual_overload',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_manual_overload,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Consumed: {
                text: i18n('Consumed'),
                width: 70,
                dataIndex: 'consumed',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_consumed,
                editor: $confConsumed,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            ManualConsumed: {
                text: i18n('ManualConsumed'),
                //flex: 1.5,
                width: 100,
                dataIndex: 'manual_consumed',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_manual_consumed,
                editor: $confConsumed,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        $.cookie('w_' + e.dataIndex, newWidth, {secure: true, expires: 30});
                    },
                    render: function(e){
                        var w = parseInt($.cookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            InUsed: {
                text: i18n('InUsed'),
                width: 80,
                dataIndex: 'wait',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_wait,
                mobileHidden: Azuree.isMobile ? true : false,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Completed: {
                text: i18n('Completed'),
                width: 80,
                dataIndex: 'completed',
                renderer: render_completed,
                sortable: false,
                menuDisabled: true,
                mobileHidden: Azuree.isMobile ? true : false,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Remain: {
                text: i18n('Remain'),
                width: 80,
                dataIndex: 'remain',
                renderer: render_remain,
                sortable: false,
                menuDisabled: true,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Initialworkload: {
                text: i18n('Initialworkload') ,
                width: 80,
                dataIndex: 'initial_estimated',
                summaryType: 'sum',
                sortable: false,
                menuDisabled: true,
                renderer: render_initial_estimated,
                mobileHidden: Azuree.mobileEnabled ? true : false,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            Initialstartdate: {
                text: i18n('Initialstartdate'),
                width: 80,
                //flex: 1.5,
                dataIndex: 'initial_task_start_date',
                sortable: false,
                menuDisabled: true,
                renderer: render_initial_start_date,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                },
                mobileHidden: Azuree.mobileEnabled ? true : false
            },
            Initialenddate: {
                text: i18n('Initialenddate'),
                width: 80,
                //flex: 1.5,
                dataIndex: 'initial_task_end_date',
                sortable: false,
                menuDisabled: true,
                renderer: render_initial_end_date,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                },
                mobileHidden: Azuree.mobileEnabled ? true : false
            },
            'Amount€': {
                text: i18n('Amount€'),
                width: 70,
                dataIndex: 'amount',
                sortable: false,
                menuDisabled: true,
                renderer: render_euro,
                editor: $confConsumed,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            '%progressorder': {
                text: i18n('%progressorder'),
                width: 70,
                dataIndex: 'progress_order',
                sortable: false,
                menuDisabled: true,
                renderer: render_percent,
                editor: $confConsumed,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            '%progressorder€': {
                text: i18n('%progressorder€'),
                width: 70,
                dataIndex: 'progress_order_amount',
                sortable: false,
                menuDisabled: true,
                renderer: render_euro,
                editor: null,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Text': {
                text: i18n('Text'),
                xtype: 'actioncolumn',
                width: 70,
                dataIndex: 'text_1',
                sortable: false,
                renderer: render_text,
                menuDisabled: true,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Attachment': {
                text: i18n('Attachment'),
                width: 70,
                dataIndex: 'attachment',
                sortable: false,
                menuDisabled: true,
                renderer: render_file,
                editor: null,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            '+/-': {
                text: i18n('+/-'),
                width: 70,
                dataIndex: 'slider',
                sortable: false,
                menuDisabled: true,
                renderer: render_slider,
                editor: {
                    xtype: 'numberfield',
                    selectOnFocus: true
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'UnitPrice': {
                text: i18n('UnitPrice'),
                width: 70,
                dataIndex: 'unit_price',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_unit_price,
                editor: {
                    xtype: 'textfield',
                    selectOnFocus: true
                },
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Consumed€': {
                text: i18n('Consumed€'),
                width: 70,
                dataIndex: 'consumed_euro',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_consume_euro,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Remain€': {
                text: i18n('Remain€'),
                width: 70,
                dataIndex: 'remain_euro',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_remain_euro,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Workload€': {
                text: i18n('Workload€'),
                width: 70,
                dataIndex: 'workload_euro',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_workload_euro,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'Estimated€': {
                text: i18n('Estimated€'),
                width: 70,
                dataIndex: 'estimated_euro',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_estimated_euro,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
            'EAC': {
                text: i18n('EAC'),
                width: 70,
                dataIndex: 'eac',
                sortable: false,
                menuDisabled: true,
                summaryType: 'sum',
                renderer: render_eac,
                listeners: {
                    resize: function(e, newWidth, newHeight, oldWidth, oldHeight, eOpts) {
                        me.setCookie('w_' + e.dataIndex, newWidth, {expires: 30, path : '/'});
                    },
                    render: function(e){
                        var w = parseInt(me.getCookie('w_' + e.dataIndex));
                        if( !isNaN(w) )e.setWidth(w);
                    }
                }
            },
        };
        var realColumns = [];
        Ext.Array.each(columns_order, function(v){
            v = v.split('|');
            var key = v[0], hidden = !parseInt(v[1]);
            //special fields
            if( is_manual_consumed && key == 'Overload' ){
                key = 'ManualOverload';
            }
            if( key == 'ManualConsumed' ){
                 if(is_manual_consumed){
                     if(parseInt(v[1]) == 0){
                         columns[key].hidden = true;
                     }
                     // yes la theo dieu kien o v 1
                 } else {
                     columns[key].hidden = true;
                 }
            } else if( key == 'Initialworkload' || key == 'Initialenddate' || key == 'Initialstartdate' ) {
                if( !(jQuery('#is_show_freeze').val()==1 && jQuery('#off_freeze').val()==1) ){
                    columns[key].hidden = true;
                } else {
                    columns[key].hidden = hidden;
                }
            } else if(key == '+/-'){
                columns[key].hidden = gap_linked_task ? false : true;
            } else {
                columns[key].hidden = hidden;
            }
            //if is mobile
            if( typeof columns[key].mobileHidden && columns[key].mobileHidden )columns[key].hidden = true;
            realColumns.push(columns[key]);
        });
        // var columnFreeze = columns;
        // if((jQuery('#show_freeze').val()==1)&&(jQuery('#is_show_freeze').val()==1)&&(jQuery('#off_freeze').val()==1)){
        //  var columnFreeze = $.merge($.merge( [],columns),columnUF);
        // }
        // if((jQuery('#show_freeze').val()==0)&&(jQuery('#is_show_freeze').val()==1)&&(jQuery('#off_freeze').val()==1)){
        //  var columnFreeze = $.merge($.merge( [],columns),columnF);
        // }
        return realColumns;
    },
	
    refreshView: function() {
        this.getView().refresh();
    },

    checkEstimated: function(assign, task, type){
        var me = this;
        var _fields = [];
        var _es;
        var sumEstimated = 0;
        $employees = me.getAssignToTextArray(task);
        if(assign.estimated_detail && assign.estimated_detail != ''){
            $estiamtedDetail = assign.estimated_detail;
            var countEmployee=assign.task_assign_to_id.length;
            for(var i = 0; i < countEmployee; i++){
                var estimatedForI=$estiamtedDetail[i];
                if(countEmployee==1) {
                    var estimatedForI=assign.estimated;
                }
                sumEstimated += parseFloat(estimatedForI);
                 _fields[i] = {
                    fieldLabel: $employees[i],
                    id: 'text'+i,
                    cls: 'abc',
                    name: "Reference[" +assign.task_assign_to_id[i]+ "_" +assign.is_profit_center[i]+ "]",
                    anchor:'100%',
                    allowBlank: false,
                    value: estimatedForI,
                    listeners:{
                        change: function(btn, newValue, oldValue, eOpts){
                            var form= Ext.getCmp("formEstimated");
                            var items = form.getForm().monitor.items.items;
                            var _lengths = items.length-3;
                            var consumedWorload = 0;
                            for(var j = 1;j <= _lengths; j++){
                                if(j != _lengths){
                                    var _workload = items[j].value ? parseFloat(items[j].value) : 0;
                                    consumedWorload += _workload;
                                }
                            }
							var setValEnd = (parseFloat(assign.estimated)).toFixed(2) - (parseFloat(consumedWorload)).toFixed(2);
							Ext.getCmp(items[_lengths].id).setValue((parseFloat(setValEnd)).toFixed(2));
                        }
                    }
                };
            }
        } else {
            if(assign.estimated == 0 || assign.estimated == '' || assign.estimated == null){
                _es = 0;
            } else {
                _es = assign.estimated;
            }

            for(var i = 0; i < assign.task_assign_to_id.length; i++){
                var _val = 0;
                if(i == 0){
                    _val = _es;
                }
                 _fields[i] = {
                    fieldLabel: $employees[i],
                    id: 'text'+i,
                    name: "Reference[" +assign.task_assign_to_id[i]+ "_" +assign.is_profit_center[i]+ "]",
                    anchor:'100%',
                    allowBlank: false,
                    value: _val,
                    listeners:{
                        change: function(btn, newValue, oldValue, eOpts){
                            var form= Ext.getCmp("formEstimated");
                            var items = form.getForm().monitor.items.items;
                            var _lengths = items.length-3;
                            var consumedWorload = 0;
                            for(var j = 1;j <= _lengths; j++){
                                if(j != _lengths){
                                    var _workload = items[j].value ? parseFloat(items[j].value) : 0;
                                    consumedWorload += _workload;
                                }
                            }
                            var setValEnd = (parseFloat(assign.estimated)).toFixed(2) - (parseFloat(consumedWorload)).toFixed(2);
							Ext.getCmp(items[_lengths].id).setValue((parseFloat(setValEnd)).toFixed(2));
                        }
                    }
                };
            }

        }

        var _label = {
            fieldLabel: i18n('Total workload'),
            name: 'total_estimated',
            anchor:'100%',
            disabled: true,
            value: assign.estimated
        };
        var _hiddenField = {
            name: "ProjectTaskId",
            value: assign.id,
            hidden: true
        };
        var _hiddenTotalEstimated = {
            name: "totalEstimated",
            value: assign.estimated,
            hidden: true
        };
        //CHECK CANCEL BUTTON
        sumEstimated = sumEstimated.toFixed(2);
        var checkValid = assign.estimated - sumEstimated;
        var cancelButton = cancelIcon = false;
        if(checkValid != 0)
        {
            cancelButton = true;
            _label.fieldCls = 'invalid';
        }
        else
        {
            cancelIcon = true;
        }
        //END
        _fields = Ext.Array.merge(_label, _fields, _hiddenField, _hiddenTotalEstimated);

        var field = new Ext.form.field.Text({renderTo: document.body}),
            fieldHeight = field.getHeight(),
            padding = 5,
            remainingHeight;

        field.destroy();

        remainingHeight = padding + fieldHeight * 2;

        var form = new Ext.form.Panel({
            id: 'formEstimated',
            border: false,
            fieldDefaults: {
                labelWidth: 200
            },
            defaultType: 'textfield',
            bodyPadding: padding,
            items: _fields
        });
        new Ext.window.Window({
            autoShow: true,
            title: i18n('Workload Detail For Employee And Profit Center'),
            width: 500,
            minWidth: 400,
            minHeight: 200,
            autoScroll: true,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: form,
            closable: cancelIcon,
            task: task,
            listeners:{
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                },
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                }
            },
            buttons: [
                {
                    text: i18n('Save'),
                    iconCls: 'estimated-detail-save x-save',
                    task: task
                },
                {
                    text: i18n('Cancel'),
                    disabled : cancelButton,
                    iconCls: 'estimated-detail-cancel x-cancel',
                    handler: function() {
                        //me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },
    viewEstimated: function(record){
        var me = this;
        var _fields = [];
        var _es;
        var texts = '';
        if(record.data.capacity_off > 0){
            texts = '<span style = "color: rgb(29, 209, 29); float: right;">' + record.data.capacity_off + ' M.D</span>';
        } else {
            texts = '<span style = "color: red; float: right;">' + record.data.capacity_off + ' M.D</span>';
        }

        _fields = {
            fieldLabel: record.data.task_assign_to_text + texts,
            id: "Reference[" +record.data.task_assign_to_id+ "_" +record.data.is_profit_center+ "]",
            name: "Reference[" +record.data.task_assign_to_id+ "_" +record.data.is_profit_center+ "]",
            anchor:'100%',
            allowBlank: false,
            value: record.data.capacity
        };
        var _label = {
            fieldLabel: i18n('Total workload'),
            name: 'total_estimated',
            anchor:'100%',
            disabled: true,
            value: record.data.capacity
        };
        var tools = '';
        $.each(record.data.listProfile, function(index, value){
            tools += value + '\n';
        });
        var _tools = [];
        _tools = {
                fieldLabel: i18n('List Project And Activiy'),
                labelWidth: 150,
                width: 500,
                height: 350,
                anchor:'100%',
                allowBlank: false,
                xtype: 'textareafield',
                value: tools
            };

        _fields = Ext.Array.merge(_label, _fields, _tools);

        var field = new Ext.form.field.Text({renderTo: document.body}),
            fieldHeight = field.getHeight(),
            padding = 5,
            remainingHeight;

        field.destroy();

        remainingHeight = padding + fieldHeight * 2;

        var form = new Ext.form.Panel({
            id: 'formEstimated',
            border: false,
            fieldDefaults: {
                labelWidth: 400
            },
            defaultType: 'textfield',
            bodyPadding: padding,
            items: _fields
        });

        new Ext.window.Window({
            autoShow: true,
            title: i18n('Workload Detail For Employee And Profit Center'),
            width: 800,
            height: 500,
            minWidth: 400,
            minHeight: 200,
            autoScroll: true,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: form,
            closable: true,
            //task: task,
            listeners:{
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                },
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                }
            },
            buttons: [
                {
                    text: i18n('Cancel'),
                    iconCls: 'estimated-detail-cancel-view x-cancel',
                    handler: function() {
                        me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },

    openStatusFilter: function(){
        var me = this;
        //build checkbox data
        var data = [];
        //cookie store as id => 1 | 0
        var settings = me.getSetting('status_filter', {});
        Ext.Object.each(me.listStatuses, function(id, v){
            data.push({
                boxLabel: v.name,
                name: 'azuree_status',
                inputValue: v.id,
                checked: typeof settings[v.id] != 'undefined' ? settings[v.id] : true,
                checkedCls: 'x-form-cb-checked x-checked'
            });
        });
        if( !data ) return;
        var checkboxGroup = Ext.create('Ext.form.CheckboxGroup', {
            //id: 'status-filter',
            columns: 2,
            vertical: true,
            items: data
        });
        new Ext.window.Window({
            autoShow: true,
            title: i18n('Filter'),
            minWidth: 600,
            minHeight: 150,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: checkboxGroup,
            closable: true,
            listeners:{
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                },
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                }
            },
            buttons: [
                {
                    text: i18n('Save'),
                    iconCls: 'save-filter x-save',
                    handler: function(){
                        //luu settings
                        var settings = {};
                        Ext.Array.each(checkboxGroup.items.items, function(checkbox){
                            settings[checkbox.inputValue] = checkbox.checked;
                            //settings[checkbox.boxLabel] = 1;
                        });
                        me.saveSetting('status_filter', settings);
                        //thuc hien filter
                        me.addStatusFilter(settings, true);
                        // me.filterByStatus(settings, me.getSetting('show_type', null));
                        //dong window
                        $('body').find('.btn-reset-filter').removeClass('hidden');
                        this.ownerCt.ownerCt.close();
                    }
                },
                {
                    text: i18n('Cancel'),
                    iconCls: 'x-cancel',
                    handler: function() {
                        //me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },
    openPriorityFilter: function(){
        var me = this;
        //build checkbox data
        var data = [];
        //cookie store as id => 1 | 0
        var settings = me.getSetting('priority_filter', {});

        Ext.Object.each(me.listPriority, function(id, v){
            data.push({
                boxLabel: v,
                name: 'azuree_priority',
                inputValue: id,
                checked: typeof settings[id] != 'undefined' ? settings[id] : true,
                checkedCls: 'x-form-cb-checked x-checked'
            });
        });
        if( !data ) return;
        var checkboxGroup = Ext.create('Ext.form.CheckboxGroup', {
            columns: 2,
            vertical: true,
            items: data
        });
        new Ext.window.Window({
            autoShow: true,
            title: i18n('Filter'),
            minWidth: 600,
            minHeight: 150,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: checkboxGroup,
            closable: true,
            listeners:{
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                },
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                }
            },
            buttons: [
                {
                    text: i18n('Save'),
                    iconCls: 'save-filter x-save',
                    handler: function(){
                        //luu settings
                        var settings = {};
                        Ext.Array.each(checkboxGroup.items.items, function(checkbox){
                            settings[checkbox.inputValue] = checkbox.checked;
                            //settings[checkbox.boxLabel] = 1;
                        });
                        me.saveSetting('priority_filter', settings);
                        //thuc hien filter
                        me.addPriorityFilter(settings, true);
                        //dong window
                        $('body').find('.btn-reset-filter').removeClass('hidden');
                        this.ownerCt.ownerCt.close();
                    }
                },
                {
                    text: i18n('Cancel'),
                    iconCls: 'x-cancel',
                    handler: function() {
                        //me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },
    openMilestoneFilter: function(){
        var me = this;
        //build checkbox data
        var data = [];
        //cookie store as id => 1 | 0
        var settings = me.getSetting('milestone_filter', {});

        Ext.Object.each(me.listMilestone, function(id, v){
            data.push({
                boxLabel: v,
                name: 'azuree_milestone',
                inputValue: id,
                checked: typeof settings[id] != 'undefined' ? settings[id] : true,
                checkedCls: 'x-form-cb-checked x-checked'
            });
        });
        if( !data ) return;
        var checkboxGroup = Ext.create('Ext.form.CheckboxGroup', {
            columns: 2,
            vertical: true,
            items: data
        });
        new Ext.window.Window({
            autoShow: true,
            title: i18n('Filter'),
            minWidth: 600,
            minHeight: 150,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: checkboxGroup,
            closable: true,
            listeners:{
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                },
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                }
            },
            buttons: [
                {
                    text: i18n('Save'),
                    iconCls: 'save-filter x-save',
                    handler: function(){
                        //luu settings
                        var settings = {};
                        Ext.Array.each(checkboxGroup.items.items, function(checkbox){
                            settings[checkbox.inputValue] = checkbox.checked;
                            //settings[checkbox.boxLabel] = 1;
                        });
                        me.saveSetting('milestone_filter', settings);
                        //thuc hien filter
                        me.addMilestoneFilter(settings, true);
                        // add filter
                        $('body').find('.btn-reset-filter').removeClass('hidden');
                        //dong window
                        this.ownerCt.ownerCt.close();
                    }
                },
                {
                    text: i18n('Cancel'),
                    iconCls: 'x-cancel',
                    handler: function() {
                        //me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },
    openProfileFilter: function(){
        var me = this;
        //build checkbox data
        var data = [];
        //cookie store as id => 1 | 0
        var settings = me.getSetting('profile_filter', {});

        Ext.Object.each(me.listProfile, function(id, v){
            data.push({
                boxLabel: v,
                name: 'azuree_profile',
                inputValue: id,
                checked: typeof settings[id] != 'undefined' ? settings[id] : true,
                checkedCls: 'x-form-cb-checked x-checked'
            });
        });
        if( !data ) return;
        var checkboxGroup = Ext.create('Ext.form.CheckboxGroup', {
            columns: 2,
            vertical: true,
            items: data
        });
        new Ext.window.Window({
            autoShow: true,
            title: i18n('Filter'),
            minWidth: 600,
            minHeight: 150,
            autoHeight: true,
            layout: 'fit',
            plain: true,
            items: checkboxGroup,
            closable: true,
            listeners:{
                render: function(label){
                    me.setLoading(i18n('Please wait'));
                },
                close: function(){
                    me.setLoading(false);
                    me.currentPopupSelected = false;
                }
            },
            buttons: [
                {
                    text: i18n('Save'),
                    iconCls: 'save-filter x-save',
                    handler: function(){
                        //luu settings
                        var settings = {};
                        Ext.Array.each(checkboxGroup.items.items, function(checkbox){
                            settings[checkbox.inputValue] = checkbox.checked;
                            //settings[checkbox.boxLabel] = 1;
                        });
                        me.saveSetting('profile_filter', settings);
                        //thuc hien filter
                        me.addProfileFilter(settings, true);
                         $('body').find('.btn-reset-filter').removeClass('hidden');
                        //dong window
                        this.ownerCt.ownerCt.close();
                    }
                },
                {
                    text: i18n('Cancel'),
                    iconCls: 'x-cancel',
                    handler: function() {
                        //me.currentPopupSelected = false;
                        this.ownerCt.ownerCt.close();
                    }
                }
            ]
        });
    },
    collapseOnClear: false,
    allowParentFolders: false,

    clearFilter: function () {
        this.store.clearFilter();
    },
    cleanFilters: function(){
        this._filters = {};
        this._setups = {};
        // save settings
        this.removeSetting([
            'status_filter',
            'show_type',
            'name_filter',
            'assign_filter',
            'assign_selection',
            'sd_filter',
            'ed_filter',
            'milestone_filter',
            'profile_filter',
            'priority_filter'
        ]);

        this.saveSetting();
        var list = [
            'task_assign_to_id',
            'task_start_date',
            'task_end_date',
            'task_title',
            'task_status_id',
            'milestone_id',
            'task_priority_id',
            'profile_id'
        ];
        for(var i = 0 ; i < list.length; i++){
            var index = list[i];
            if( index == 'task_status_id' || index == 'milestone_id' || index == 'task_priority_id' || index == 'profile_id'){
                this.toggleHeaderStyle(index, false, 'x-icon-filtered');
            } else {
                this.toggleHeaderStyle(index, false);
            }
        }
        this.clearFilter();
    },
    _filters: {},
    _setups: {},
    addFilter: function(key, callback, setup, run){
        this._filters[key] = callback;
        this._setups[key] = setup;
        if( run )this.applyFilters();
    },
    removeFilter: function(key, run){
        try {
            delete this._filters[key];
            delete this._setups[key];
        } catch(ex){}
        if( run )this.applyFilters();
    },
    applyFilters: function(){
        var me = this,
            matches = [],
            visibleNodes = [],
            settings = {};
        Ext.Object.each(this._setups, function(key, callback, self){
            if( typeof callback == 'function' )
            callback.call(me, key, settings);
        });

        me.store.clearFilter();
        me.getRootNode().cascadeBy(function(node){
            var push = true;
            Ext.Object.each(me._filters, function(key, callback, self){
                push = callback.call(me, node, settings, key, (node.get('is_phase') == 'true' || node.get('is_part') == 'true') || node.get('is_previous') == 'true');
                if( !push )return false;
            });
            if( push ){
                matches.push(node);
            }
        });

        visibleNodes = me.getParentsFromNodes(matches);	
        me.store.filterBy(function(record){
            return Ext.Array.contains(visibleNodes, record.get('id'));
        });
        // setTimeout(function(){
        //     cleanFilter();
        // }, 500);
    },
    addMilestoneFilter: function(list, run){
        var me = this;
        if( Ext.Object.isEmpty(list) ){
            this.toggleHeaderStyle('milestone_id', false, 'x-icon-filtered');
            this.removeFilter('milestone_filter', run);
        } else {
            this.addFilter('milestone_filter', function(node, s, k, p){
                var t = true;
                Ext.Object.each(list, function(k, v){
                    if( !v ){
                        t = false;
                        return false;
                    }
                });
                var miles_id = parseInt(node.get('milestone_id')),
                    text = node.get('milestone_text');
                if( (miles_id && typeof list[miles_id] != 'undefined' && list[miles_id] == true) || t || p ){
                    return true;
                }
                return false;
            }, null, run);
            var has_filter = false;
            Ext.Object.each(list, function(k, v){
                if( !v ){
                    has_filter = true;
                    return false;
                }
            });
            if( has_filter ){
                me.toggleHeaderStyle('milestone_id', true, 'x-icon-filtered');
            } else {
                me.toggleHeaderStyle('milestone_id', false, 'x-icon-filtered');
            }
        }
    },
    addProfileFilter: function(list, run){
        var me = this;
        if( Ext.Object.isEmpty(list) ){
            this.toggleHeaderStyle('profile_id', false, 'x-icon-filtered');
            this.removeFilter('profile_filter', run);
        } else {
            this.addFilter('profile_filter', function(node, s, k, p){
                var t = true;
                Ext.Object.each(list, function(k, v){
                    if( !v ){
                        t = false;
                        return false;
                    }
                });
                var miles_id = parseInt(node.get('profile_id')),
                    text = node.get('profile_text');
                if( (miles_id && typeof list[miles_id] != 'undefined' && list[miles_id] == true) || t || p ){
                    return true;
                }
                return false;
            }, null, run);
            var has_filter = false;
            Ext.Object.each(list, function(k, v){
                if( !v ){
                    has_filter = true;
                    return false;
                }
            });
            if( has_filter ){

                me.toggleHeaderStyle('profile_id', true, 'x-icon-filtered');
            } else {
                me.toggleHeaderStyle('profile_id', false, 'x-icon-filtered');
            }
        }
    },
    addStatusFilter: function(list, run){
        var me = this;
        if( Ext.Object.isEmpty(list) ){
            this.toggleHeaderStyle('task_status_id', false, 'x-icon-filtered');
            this.removeFilter('status_filter', run);
        } else {
            this.addFilter('status_filter', function(node, s, k, p){
                var status_id = parseInt(node.get('task_status_id')),
                    text = node.get('task_status_text');
                if( (status_id && typeof list[status_id] != 'undefined' && list[status_id] == true) || p ){
                    return true;
                }
                return false;
            }, null, run);
            var has_filter = false;
            Ext.Object.each(list, function(k, v){
                if( !v ){
                    has_filter = true;
                    return false;
                }
            });
            if( has_filter ){
                me.toggleHeaderStyle('task_status_id', true, 'x-icon-filtered');
            } else {
                me.toggleHeaderStyle('task_status_id', false, 'x-icon-filtered');
            }
        }
    },
    addPriorityFilter: function(list, run){
        var me = this;
        if( Ext.Object.isEmpty(list) ){
            this.toggleHeaderStyle('task_priority_id', false, 'x-icon-filtered');
            this.removeFilter('priority_filter', run);
        } else {
            this.addFilter('priority_filter', function(node, s, k, p){
                var t = true;
                Ext.Object.each(list, function(k, v){
                    if( !v ){
                        t = false;
                        return false;
                    }
                });
                var miles_id = parseInt(node.get('task_priority_id')),
                    text = node.get('task_priority_text');
                if( (miles_id && typeof list[miles_id] != 'undefined' && list[miles_id] == true) || t || p ){
                    return true;
                }
                return false;
            }, null, run);
            var has_filter = false;
            Ext.Object.each(list, function(k, v){
                if( !v ){
                    has_filter = true;
                    return false;
                }
            });
            if( has_filter ){
                me.toggleHeaderStyle('task_priority_id', true, 'x-icon-filtered');
            } else {
                me.toggleHeaderStyle('task_priority_id', false, 'x-icon-filtered');
            }
        }
    },
    addTypeFilter: function(icon, run){
        if( !icon ){
            this.removeFilter('type_filter', run);
        } else {
            this.addFilter('type_filter', function(record, settings, key, isPhase){
                var curDate = settings[key];
                try {
                    var startDate = record.get('task_start_date').getTime(),
                        endDate = record.get('task_end_date').getTime();
                } catch(ex){
                    var startDate = new Date().getTime(), endDate = new Date().getTime();
                }
                var consume = parseFloat(is_manual_consumed ? record.get('manual_consumed') : record.get('consumed'));
                if( isNaN(consume) ) consume = 0;
                var workload = parseFloat(record.get('estimated'));
                if( isNaN(workload) ) workload = 0;
                var conds = (consume == 0 && curDate > startDate && workload > 0 && record.get('task_status_st') && record.get('task_status_st') != 'CL') || (record.get('task_status_st') && record.get('task_status_st') != 'CL' && curDate > endDate) || (consume > workload),
                    phasePart = isPhase;
                switch(icon){
                    case 'green':
                        //all must be green
                        if( phasePart || !conds ){
                            return true;
                        }
                        return false;
                    break;
                    case 'red':
                        //at least one red
                        if( phasePart || conds ){
                            return true;
                        }
                        return false;
                    break;
                    default:
                        //clear all
                        return true;
                    break;
                }
            }, function(key, settings){
                settings[key] = this.getCurrentDate().getTime();
            }, run);
        }
    },
    getParentsFromNodes: function(matches){
        var visibleNodes = [],
            me = this,
            root = me.getRootNode();
        Ext.Array.each(matches, function (item) { // loop through all matching leaf nodes
            root.cascadeBy(function (node) { // find each parent node containing the node from the matches array
                if (node.contains(item) == true) {
                    visibleNodes.push(node.get('id')) // if it's an ancestor of the evaluated node add it to the visibleNodes  array
                }
            });
            visibleNodes.push(item.get('id')) // also add the evaluated node itself to the visibleNodes array
        });
        return visibleNodes;
    },
    togglePhase: function(eOpts){
        // var me = this;
        // var open = null;
        // var settings = {};
        // var root = me.getRootNode();
        // root.cascadeBy(function(node) {
        //  if( node.get('is_phase') == 'true' ){
        //      if( null === open ){
        //          open = node.isExpanded();
        //      }
        //      if( open ){
        //          //do collapse
        //          node.set('update_me', 0);
        //          node.collapse();
        //          //store settings
        //          settings[node.getId()] = false;
        //      } else {
        //          node.set('update_me', 0);
        //          node.expand();
        //          settings[node.getId()] = true;
        //      }
        //  }
        // });
        // me.saveSetting('rows', settings);
        // //filter (required)
        // var list = this.getSetting('status_filter', {}),
        //  type = this.getSetting('show_type', null);
        // this.filterByStatus(list, type);
    },
    doAll: function(){
        var view = this.getView(),
            me = this,
            store = this.getStore();
        //filter by status
        this.calculateTable();
        //select task
        var record;
        if( hightlightTask ){
            record = store.getNodeById(hightlightTask);
        }
        if( record ){
            view.setSelection(record);
            view.scrollRowIntoView(record);
            //edit
            if( record.get('is_nct') == 1 ){
                setTimeout(function(){
                    me.openNct(record);
                }, 500);
            } else {
                setTimeout(function(){
                    me.cellEditingPlugin.startEdit(record);
                }, 500);
            }
        } else {
            var list = me.getSetting('status_filter', {}),
                type = me.getSetting('show_type', null),
                milestone = me.getSetting('milestone_filter', {}),
                profile = me.getSetting('profile_filter', {}),
                priority = me.getSetting('priority_filter', {});
            // me.filterByStatus(list, type);
            me.addTypeFilter(type);
            me.addStatusFilter(list);
            me.addMilestoneFilter(milestone);
            me.addProfileFilter(profile);
            me.addPriorityFilter(priority);
            me.applyFilters();
        }
    },
    selectAndEdit: function(id){
        var view = this.getView(),
            me = this,
            store = this.getStore();
        //select task
        var record;
        if( hightlightTask ){
            record = store.getNodeById(hightlightTask);
        }
        if( record ){
            me.getSelectionModel().select(record);
            view.scrollRowIntoView(record);
            //edit
            if( record.get('is_nct') == 1 ){
                setTimeout(function(){
                    me.openNct(record);
                }, 500);
            } else {
                setTimeout(function(){
                    me.cellEditingPlugin.startEdit(record);
                }, 500);
            }
        }
    },
    listeners: {
        afteritemexpand: function(node, eOpts){
			this.refreshView();
			this.saveDataExpand();
        },
        afteritemcollapse: function(node, eOpts){
			 this.saveDataExpand();
        }
    },
    openNct: function(record){
        if( record.get('is_nct') == 1 ){
            var me = this;
            var id = record.get('id');
            me.setLoading(i18n('Please wait'));
            $.ajax({
                url: '/project_tasks/getNcTask',
                data: {data : {id : id}},
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    $('#special-task-info').data('tree', me);
                    Task = new SpecialTask({
                        columns: data.columns,
                        data: data.data,
                        id: id,
                        task: data.task,
                        consume: data.consumeResult,
                        request: data.request,
						employees_actif: data.employees_actif,
                    });
                    $('#special-task-info').dialog('open');
					setLimitedDate('#nct-start-date', '#nct-end-date');
                    me.setLoading(false);
                },
                failure: function(){
                    alert('Problem loading task data. Please reload this page.');
                    me.setLoading(false);
                }
            });
            return false;
        }
    },
    getAssignToTextArray: function(record){
        var ids = record.get('task_assign_to_id'),
            pc = record.get('is_profit_center'),
            result = [],
            me = this;
        Ext.Array.each(ids, function(v, i){
            id = v + '-' + pc[i];
            result.push(me.references[id]);
        });
        return result;
    },
    getAssignToText: function(list, pc){
        var result = [], me = this;
        Ext.Array.each(list, function(v, i){
            id = v + '-' + pc[i];
            result.push(me.references[id]);
        });
        return result.join(', ');
    },
    getWorkload: function(record){
        var ids = record.get('task_assign_to_id'),
            pc = record.get('is_profit_center'),
            detail = record.get('estimated_detail'),
            result = {};
        Ext.Array.each(ids, function(id, i){
            id = id + '-' + pc[i];
            result[id] = detail && detail[i] ? detail[i] : record.get('estimated'); //single resource assignment
        });
        return result;
    },
    buildWorkload: function(record){
        var ids = record.get('task_assign_to_id'),
            pc = record.get('is_profit_center'),
            result = [],
            me = this;
        Ext.Array.each(ids, function(id, i){
            id = id + '-' + pc[i];
            if( me.oldWorkload[id] ){
                result.push(me.oldWorkload[id]);
            } else {
                result.push('0.00');
            }
        });
        record.set('estimated_detail', result);
    },
    addNameFilter: function(val, run){
        var me = this;
        if( val ){
            val = val.toLowerCase();
            me.addFilter('name_filter', function(node, s, k, isPhase){
                var ed = node.get('task_title').toLowerCase();
                if( ed.indexOf(val) != -1 || isPhase ){
                    return true;
                }
                return false;
            });
            this.toggleHeaderStyle('task_title', true);
        } else {
            me.removeFilter('name_filter');
            this.toggleHeaderStyle('task_title', false);
        }
        if( run )me.applyFilters();
    },
    addSDFilter: function(val, run){
        var me = this;
        if( val ){
            me.addFilter('sd_filter', function(node, s, k, isPhase){
                var ed = node.get('task_start_date');
                if( ed ){
                    ed = Ext.Date.format(ed, 'd-m-y');
                } else {
                    ed = '';
                }
                if( ed.indexOf(val) != -1 || isPhase ){
                    return true;
                }
                return false;
            });
            this.toggleHeaderStyle('task_start_date', true);
        } else {
            me.removeFilter('sd_filter');
            this.toggleHeaderStyle('task_start_date', false);
        }
        if(run )me.applyFilters();
    },
    addEDFilter: function(val, run){
        var me = this;
        if( val ){
            me.addFilter('ed_filter', function(node, s, k, isPhase){
                var ed = node.get('task_end_date');
                if( ed ){
                    ed = Ext.Date.format(ed, 'd-m-y');
                } else {
                    ed = '';
                }
                if( ed.indexOf(val) != -1 || isPhase ){
                    return true;
                }
                return false;
            });
            this.toggleHeaderStyle('task_end_date', true);
        } else {
            me.removeFilter('ed_filter');
            this.toggleHeaderStyle('task_end_date', false);
        }
        if(run )me.applyFilters();
    },
    addAssignFilter: function(list, run){
        if( !list.length ){
            var l = this.getSetting('assign_filter', {});
            if( Ext.Object.isEmpty(l) ){
                this.toggleHeaderStyle('task_assign_to_id', false);
                this.removeFilter('assign_filter', run);
                return;
            }
        }
        this.addFilter('assign_filter', function(node, s, k, isPhase){
            var textArray = this.getAssignToTextArray(node);
            var result = Ext.Array.intersect(textArray, list);
            return result.length > 0 || isPhase;
        }, null, run);
        var has_filter = false;
        var reallist = this.getSetting('assign_filter', {});
        Ext.Object.each(reallist, function(k, v){
            if( !v ){
                has_filter = true;
                return false;
            }
        });
        if( has_filter ){
            this.toggleHeaderStyle('task_assign_to_id', true);
        } else {
            this.toggleHeaderStyle('task_assign_to_id', false);
        }
    },
    _headers: {},
    toggleHeaderStyle: function(dataIndex, onoff, custom){
        var header = this.getHeaderByDataIndex(dataIndex);
        var css = custom ? custom : 'has-filter';
        if( header ){
            if( onoff ){
                header.addCls(css);
            } else {
                header.removeCls(css);
            }
        }
    },
    getHeaderByDataIndex: function(dataIndex){
        if( typeof this._headers[dataIndex] != 'undefined' ){
            return this._headers[dataIndex];
        }
        var index,
            columns = this.headerCt.getGridColumns();
        for (index = 0; index < columns.length; ++index) {
            if (columns[index].dataIndex == dataIndex) {
                break;
            }
        }
        index = index == columns.length ? -1 : index;
        this._headers[dataIndex] = columns[index];
        return columns[index];
    },
    afterLoad: function(){
        var me = this;
        var menu = me.headerCt.getMenu(),
            hideItems = {
                ascItem: 1,
                descItem: 1,
                columnItem: 1,
                columnItemSeparator: 1
            };

        var list = me.getSetting('assign_selection', []);
        me.addAssignFilter(list);

        me.form_assign_filter = Ext.create('Ext.form.Panel', {
            id: 'assign-filter',
            bodyPadding: 0,
            width: 400,
            hidden: true,

            // Fields will be arranged vertically, stretched to full width
            layout: 'anchor',
            defaults: {
                anchor: '100%'
            },

            // The fields
            defaultType: 'textfield',
            items: [],
            listeners: {
                beforeshow: function(t){
                    t.removeAll();

                    var assign_filter = me.getSetting('assign_filter', {});
                    var list = [], assigns = [];

                    me.getRootNode().cascadeBy(function(node){
                        if( !node.childNodes.length ){
                            assigns = Ext.Array.merge(assigns, me.getAssignToTextArray(node));
                        }
                    });
                    Ext.Array.each(assigns, function(text, i){
                        if( !text )return;
                        list.push({
                            boxLabel: text,
                            name: 'assign_filter',
                            inputValue: text,
                            checked: typeof assign_filter[text] != 'undefined' ? assign_filter[text] : true,
                            checkedCls: 'x-form-cb-checked x-checked'
                        });
                    });

                    var chk = Ext.create('Ext.form.CheckboxGroup', {
                        //id: 'assign-filter',
                        columns: 2,
                        vertical: true,
                        items: list
                    });

                    t.add(chk);
                },
            },
            // Reset and Submit buttons
            buttons: [{
                text: i18n('Uncheck all'),
                iconCls: 'x-uncheck',
                handler: function() {
                    var chk = me.form_assign_filter.items.items[0];
                    Ext.Array.each(chk.items.items, function(checkbox){
                        checkbox.setValue(false);
                    });
                }
            }, {
                text: i18n('Check all'),
                iconCls: 'x-check',
                handler: function() {
                    var chk = me.form_assign_filter.items.items[0];
                    Ext.Array.each(chk.items.items, function(checkbox){
                        checkbox.setValue(true);
                    });
                }
            }, {
                text: i18n('Save'),
                iconCls: 'x-save',
                formBind: true, //only enabled once the form is valid
                handler: function() {
                    var chk = me.form_assign_filter.items.items[0];
                    //luu settings
                    var settings = {}, selectedFilter = [];
                    Ext.Array.each(chk.items.items, function(checkbox){
                        settings[checkbox.inputValue] = checkbox.checked;
                        if( checkbox.checked ){
                            selectedFilter.push(checkbox.inputValue);
                        }
                    });
                    me.saveSetting({
                        'assign_filter': settings,
                        'assign_selection': selectedFilter
                    });
                    // me.saveSetting('assign_selection', selectedFilter);

                    $('body').find('.btn-reset-filter').removeClass('hidden');

                    //thuc hien filter
                    me.addAssignFilter(selectedFilter, true);

                    menu.hide();
                }
            }]
        });

        var val = me.getSetting('name_filter', '');
        me.addNameFilter(val);

        me.form_name_filter = Ext.create('Ext.form.Panel', {
            id: 'name-filter',
            bodyPadding: 10,
            save: function() {
                var input = this.down('#name_filter'),
                    val = Ext.String.trim(input.getRawValue());

                me.addNameFilter(val, true);

                me.saveSetting('name_filter', val);

                menu.hide();
            },
            layout: 'hbox',
            // The fields
            defaultType: 'textfield',
            items: [
                {
                    xtype: 'textfield',
                    id: 'name_filter',
                    margin: '0 5 0 0',
                    flex: 7
                },
                {
                    xtype: 'button',
                    text: i18n('Save'),
                    iconCls: 'x-save',
                    handler: function(t) {
                        t.up('form').save();
                  //    var input = t.up('form').down('#name_filter'),
                  //        val = Ext.String.trim(input.getRawValue());
                         $('body').find('.btn-reset-filter').removeClass('hidden');
                  //    me.addNameFilter(val, true);

                  //    me.saveSetting('name_filter', val);

                        // menu.hide();
                    },
                    flex: 3
                }
            ],
            listeners: {
                beforeshow: function(t){
                    var v = me.getSetting('name_filter', '');
                    var inp = t.down('#name_filter');
                    inp.setRawValue(v);
                    inp.focus();
                },
                afterrender: function(thisForm, options){
                    this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
                        enter: function(){
                            thisForm.save();
                        },
                        scope: this
                    });
                }
            },
        });

        var val = me.getSetting('sd_filter', '');
        me.addSDFilter(val);
        var val = me.getSetting('ed_filter', '');
        me.addEDFilter(val);

        me.form_sd_filter = Ext.create('Ext.form.Panel', {
            id: 'sd-filter',
            bodyPadding: 10,
            save: function(){
                var input = this.down('#sd_filter'),
                    val = Ext.String.trim(input.getRawValue());
                me.addSDFilter(val, true);
                me.saveSetting('sd_filter', val);
                menu.hide();
            },
            layout: 'hbox',
            // The fields
            defaultType: 'textfield',
            items: [
                {
                    xtype: 'textfield',
                    id: 'sd_filter',
                    margin: '0 5 0 0',
                    width: 80
                },
                {
                    xtype: 'button',
                    text: i18n('Save'),
                    iconCls: 'x-save',
                    handler: function(t) {
                        $('body').find('.btn-reset-filter').removeClass('hidden');
                        t.up('form').save();
                    }
                }
            ],
            listeners: {
                beforeshow: function(t){
                    var v = me.getSetting('sd_filter', '');
                    var inp = t.down('#sd_filter');
                    inp.setRawValue(v);
                    inp.focus();
                },
                afterrender: function(thisForm, options){
                    this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
                        enter: function(){
                            thisForm.save();
                        },
                        scope: this
                    });
                }
            },
        });

        // endDate filter
        me.form_ed_filter = Ext.create('Ext.form.Panel', {
            id: 'ed-filter',
            bodyPadding: 10,
            save: function(){
                var input = this.down('#ed_filter'),
                    val = Ext.String.trim(input.getRawValue());
                me.addEDFilter(val, true);
                me.applyFilters();
                me.saveSetting('ed_filter', val);
                menu.hide();
            },
            layout: 'hbox',
            // The fields
            defaultType: 'textfield',
            items: [
                {
                    xtype: 'textfield',
                    id: 'ed_filter',
                    margin: '0 5 0 0',
                    width: 80
                },
                {
                    xtype: 'button',
                    text: i18n('Save'),
                    iconCls: 'x-save',
                    handler: function(t) {
                        t.up('form').save();
                        $('body').find('.btn-reset-filter').removeClass('hidden');
                    }
                }
            ],
            listeners: {
                beforeshow: function(t){
                    var v = me.getSetting('ed_filter', '');
                    var inp = t.down('#ed_filter');
                    inp.setRawValue(v);
                    inp.focus();
                },
                afterrender: function(thisForm, options){
                    this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
                        enter: function(){
                            thisForm.save();
                        },
                        scope: this
                    });
                }
            },
        });

        menu.on({
            beforeshow: function(m){
                // hide default item
                for(var i = 0, items = m.items.items, l = items.length; i < l ; i++){
                    var item = items[i];
                    if( hideItems[item.itemId] ){
                        item.hide();
                    }
                }
                switch(menu.activeHeader.dataIndex){
                    case 'task_assign_to_id':
                        me.form_assign_filter.show();
                        me.form_name_filter.hide();
                        me.form_sd_filter.hide();
                        me.form_ed_filter.hide();
                    break;
                    case 'task_title':
                        me.form_assign_filter.hide();
                        me.form_name_filter.show();
                        me.form_sd_filter.hide();
                        me.form_ed_filter.hide();
                    break;
                    case 'task_start_date':
                        me.form_assign_filter.hide();
                        me.form_name_filter.hide();
                        me.form_sd_filter.show();
                        me.form_ed_filter.hide();
                    break;
                    case 'task_end_date':
                        me.form_assign_filter.hide();
                        me.form_name_filter.hide();
                        me.form_sd_filter.hide();
                        me.form_ed_filter.show();
                    break;
                }
            },
            hide: function(m){
                me.form_assign_filter.hide();
                me.form_name_filter.hide();
            }
        });
        menu.insert(0, [
            // assign filter
            me.form_assign_filter,
            me.form_name_filter,
            me.form_sd_filter,
            me.form_ed_filter
        ]);
        $('#loading').remove();
    },
    handleDnD: function (data) {
        var newRoot = {
            id: 'super-root',
            children: data,
            isExpanded: true
        };
        // this.u(this.getSetting('status_filter', {}), this.getSetting('show_type', null));
        this.getStore().setRootNode(newRoot);
//         var nodes = [];
//         this.getRootNode().cascadeBy(function(node){

//             var cond = node.get('is_phase') == 'true' || node.get('is_part') == 'true' || node.get('is_previous') == 'true';
//             if (!cond) {
//                 nodes.push(node.data);
//             }
//         });
// console.log(nodes);

        this.refreshView();
        //
    },
	saveDataExpand: function(){
		var data = this.getStore().data.items;
		var settings = this.getSetting('expanded_setting', {});
		for(n=0; n<data.length;n++){
		  settings[data[n].get('id')] = data[n].get('expanded');
		}
		this.saveSetting({
			'expanded_setting': settings,
		});
	}
});
