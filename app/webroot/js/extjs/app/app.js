Ext.Loader.setConfig({enabled:true,disableCaching: true});
Ext.Loader.setPath('Ext.ux', '/js/extjs/app/ux');

Ext.define("Ext.locale.en.window.MessageBox", {
    override: "Ext.window.MessageBox",
    buttonText: i18n_text
});

Ext.define("Ext.locale.en.grid.RowEditor", {
    override: "Ext.grid.RowEditor",
    saveBtnText: i18n('Save'),
	cancelBtnText: i18n('Cancel')
});

Ext.showMsg = function(m, type){
    if( !type )type = 'success';
    $('#message-place').html('<div id="flashMessage" class="message '+type+'">' + i18n(m) + '<a href="#" class="close">x</a></div>');
}

Ext.require([
    'Ext.data.*',
    'Ext.grid.*',
    'Ext.tree.*',
    'Ext.ux.CheckColumn',
    'Ext.form.*',
    'Ext.window.Window',
    'Ext.ux.statusbar.StatusBar',
    'Ext.ux.AzureeHistoryPlugin'
]);

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

var idProject = idProjectPreview = idActivity = 0;

if(href_currents[1] == 'activity_tasks'){
    var href_check_pms = '/activity_tasks/get_pms_activity/'+href_currents[3];
    var check = checkData(href_check_pms);
    if(check == 1){
        var href_get_project_id = '/activity_tasks/get_id_project/'+href_currents[3];
        idProject = checkData(href_get_project_id);
    } else {
        idActivity = href_currents[3];
    }
}else if(href_currents[1] == 'project_tasks_preview'){
    idProjectPreview = href_currents[3];
} else {
    idProject = href_currents[3];
}

//extjs 5 dont have raw so we need this for it to work with old codes
Ext.define('App.overrides.data.Model', {
    override: 'Ext.data.Model',
    constructor: function (data) {
        this.raw = Ext.apply({}, data);
        this.callParent(arguments);
    }
});

if(idProject && idProject != 0){
    Ext.application({
        name: 'PMS',
        appFolder: '/app/webroot/js/extjs/app',
        autoCreateViewport: false,
        controllers: ['ProjectTasks'],
        projectid: idProject,
        launch: function() {
            var tableView   = Ext.create('PMS.view.TableView',{
                id : 'main',
                xtype: "tableview",
                project_id: idProject
            });
        }
    });
} else if(idProjectPreview && idProjectPreview != 0){
    Ext.application({
        name: 'PMS',
        appFolder: '/app/webroot/js/extjs/app',
        autoCreateViewport: false,
        controllers: ['ProjectTasksPreview'],
        projectid: idProject,
        launch: function() {
         	var tableView	= Ext.create('PMS.view.TableView',{
         		id : 'main',
         		xtype: "tableview",
                project_id: idProject
         	});
        }
    });
} else {
    Ext.application({
        name: 'PMS',
        appFolder: '/app/webroot/js/extjs/app',
        autoCreateViewport: false,
        controllers: ['ActivityTasks'],
        activityid: idActivity,
        launch: function() {

            if(PMS.debug){
                console.log("Application Launch");    
            }
            
        	var tableViewActivity = Ext.create('PMS.view.TableViewActivity',{
        		id : 'main',
        		xtype: "tableviewactivity",
                activity_id: idActivity
        	});
        }
    });
}