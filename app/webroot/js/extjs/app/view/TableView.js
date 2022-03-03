Ext.define('PMS.view.TableView', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.tableview',
	requires: [
//		'PMS.view.TreePanel',
	],
	renderTo : 'project_container',
	layout: 'border',
	width: '100%',
	height: '100%',
	// hideHeaders: true,

	initComponent: function() {

		this.items= [
			{
				region: 'center',
				xtype: 'pmstreepanel',
				id: 'pmstreepanel',
				project_id: this.project_id
			}

		];
       // Ext.create('Ext.Button', {
//            text: 'Click me',
//            renderTo: 'demo_export',        
//            handler: function() {
//                var me = this;
//                //linkButton.getEl().child('a', true).href = 'data:application/vnd.ms-excel;base64,' + Base64.encode(grid.getExcelXml());
//                console.log(me);
//            }
//        });
        
		this.callParent();
	}
});