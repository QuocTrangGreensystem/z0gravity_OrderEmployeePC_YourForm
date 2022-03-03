Ext.define('PMS.view.ContextMenu', {
    extend: 'Ext.menu.Menu',
    xtype: 'tasksContextMenu',
    items: [
        {
            text: Azuree.editTask,
            iconCls: 'ext-edit-task',
            id: 'edit-task'
        },
        {
            text: Azuree.editTask,
            iconCls: 'ext-edit-task',
            id: 'edit-task-name'
        },
        {
            text: i18n('New Task'),
            iconCls: 'tasks-new-list add',
            id: 'new-task-item'
        },
        {
            text: i18n('Delete'),
            iconCls: 'tasks-delete-list delete',
            id: 'delete-task-item'
        },
        {
            text: i18n('Create not-continuous task'),
            iconCls: 'tasks-special-list add',
            id: 'new-special-task'
        },
        {
            text: i18n('Batch update tasks'),
            iconCls: 'batch-edit-tasks update ext-edit-task',
            id: 'batch-update-tasks'
        },
        {
            text: i18n('Batch delete tasks'),
            iconCls: 'batch-edit-tasks delete tasks-delete-list',
            id: 'batch-delete-tasks'
        },
        {
            text: i18n('Adjusted the workload with consumed'),
            iconCls: 'ext-edit-task batch-edit-tasks update-workload-from-consumed',
            id: 'batch-update-workload-from-consumed'
        }
    ],

    setTask: function(task) {
        this.task = task;
    },
    
    getTask: function() {
        return this.task;
    }

});