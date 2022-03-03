/**
 * @class SimpleTasks.ux.DragDrop
 * @extends Ext.grid.plugin.DragDrop
 * 
 * This plugin modifies the behavior of Ext.tree.plugin.TreeViewDragDrop. to allow the DropZone to handle
 * multiple types of records (Tasks and Lists)
 */
Ext.define('PMS.ux.DragDrop', {
    extend: 'Ext.tree.plugin.TreeViewDragDrop',
    alias: 'plugin.tasksdragdrop',
    requires: [
        'Ext.view.DragZone',
        'PMS.ux.DropZone'
    ], 

    /**
     * @event taskdrop
     * **This event is fired through the GridView. Add listeners to the GridView object**
     * 
     * Fires when a task record is dropped on the group view
     * @param {SimpleTasks.model.Task} task       The task record
     * @param {SimpleTasks.model.Group} group     The group that the task was dropped on
     */

    /**
     * @event groupdrop
     * **This event is fired through the GridView. Add listeners to the GridView object**
     * 
     * Fires when a group record is dropped on the group view
     * @param {SimpleTasks.model.Group} group         The group that was dropped
     * @param {SimpleTasks.model.Group} overGroup     The group that the group was dropped on
     * @param {String} position                 `"before"` or `"after"` depending on whether the mouse is above or below the midline of the node.
     */
	
    
    onViewRender : function(view) {
        var me = this;
        if (me.enableDrag) {
            me.dragZone = Ext.create('Ext.tree.ViewDragZone', {
                view: view,
                ddGroup: me.dragGroup || me.ddGroup,
                dragText: me.dragText,
                repairHighlightColor: me.nodeHighlightColor,
                repairHighlight: me.nodeHighlightOnRepair,
                beforeDragEnter: me.beforeDragEnter,
                beforeDragOut: me.beforeDragOut,
                getDragData: me.getDragData,
                listeners:
                {
                    'onBeforeDrag': me.onBeforeDrag
                }
            });
        }
        if (me.enableDrop) {
			
            me.dropZone = Ext.create('PMS.ux.DropZone', {
                view: view,
                ddGroup: me.dropGroup || me.ddGroup,
                allowContainerDrops: me.allowContainerDrops,
                appendOnly: me.appendOnly,
                allowParentInserts: me.allowParentInserts,
                expandDelay: me.expandDelay,
                dropHighlightColor: me.nodeHighlightColor,
                dropHighlight: me.nodeHighlightOnDrop
            });
        }

    },

    beforeDragEnter: function(target, e, id){
        // var sourceData = this.dragData.records[0].data;
        // if ((sourceData.text == "Add New Article") || (sourceData.text == "Manage Articles")){
        //     return;
        // }
    },

    beforeDragOut: function(target, e, id){
        // var sourceData = this.dragData.records[0].data;
        // if ((sourceData.text == "Add New Article") || (sourceData.text == "Manage Articles")){
        //     return;
        // }

    },

    getDragData: function(e, a, b){
        var view = this.view,
            item = e.getTarget(view.getItemSelector());

        if (item) {
            return {
                copy: view.copy || (view.allowCopy && e.ctrlKey),
                event: new Ext.EventObjectImpl(e),
                view: view,
                ddel: this.ddel,
                item: item,
                records: view.getSelectionModel().getSelection(),
                fromPosition: Ext.fly(item).getXY()
            };
        }
    },

    onBeforeDrag: function(data, e) {

		
    },
    beforedrop : function(){

	}
});