Ext.define('PMS.view.TableViewActivity', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.tableviewactivity',
	requires: [
//		'PMS.view.TreePanel',
		'PMS.view.TreePanelActivity'
	],
	renderTo : 'project_container',
	layout: 'border',
	width: '100%',
	height: '100%',

	// hideHeaders: true,

	initComponent: function() {
		Ext.QuickTips.init();
		this.items= [
			{
				region: 'center',
				xtype: 'pmstreepanelactivity',
				id: 'pmstreepanelactivity',
				activity_id: this.activity_id
			}

		];
		
		this.callParent();
	}
});