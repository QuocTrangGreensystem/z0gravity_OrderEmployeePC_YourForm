Ext.define('Ext.ux.AzureeHistoryPlugin', {
	extend: 'Ext.plugin.Abstract',
	alias: 'plugin.azureehistory',
	//get: '/history_filters/getSetting',
	saveUrl: '/history_filters/saveSettings',
	getUrl: '/history_filters/getSettings',
	store: {},
	cookie: {},
	init: function(tree){
		this.setCmp(tree);
		var panel = tree.panel;	//pmstreepanel or pmstreepanelactivity
		this.panel = panel;
		//this.load();
		var data = Ext.JSON.decode(jQuery('#priorityJson').text())['history'];
		//a small hack if server return '[]' which will cause settings not saved (method "save")
		if( Ext.isArray(data) && !data.length )data = {};
		this.store = data;
		panel.getCookieSetting = Ext.Function.bind(this.getCookieSetting, this);
		panel.setCookieSetting = Ext.Function.bind(this.setCookieSetting, this);
		panel.getCookie = Ext.Function.bind(this.getCookie, this);
		panel.setCookie = Ext.Function.bind(this.setCookie, this);
		panel.reloadSettings = Ext.Function.bind(this.load, this);
		panel.saveSetting = Ext.Function.bind(this.save, this);
		panel.getSetting = Ext.Function.bind(this.get, this);
		panel.removeSetting = Ext.Function.bind(this.remove, this);
		//load cookies
		var cookie = Ext.util.Cookies.get('z0.Task');
		if( cookie ){
			this.cookie = Ext.JSON.decode(cookie);
		} else {
			this.cookie = {};
		}
	},
	//will be removed
	load: function(){
		var me = this;
		var data = {
		};
		if( me.panel.id == 'pmstreepanel' )data.path = 'ProjectTaskSettings-' + me.panel.project_id;
		else data.path = 'ActivityTaskSettings-' + me.panel.activity_id;
		Ext.Ajax.request({
			url: me.getUrl,
			jsonData: data,
			method: 'post',
			success: function(response){
				me.store = Ext.JSON.decode(response.responseText);
			}
		});
	},
	//get: only local
	get: function(key, d){
		var me = this;
		return me.store[key] ? me.store[key] : d;
	},
	save: function(key, value){
		var me = this;
		if( typeof key != 'undefined' ){
			if( typeof key == 'object' ){
				Ext.Object.each(key, function(k, v){
					me.store[k] = v;
				});
			} else {
				me.store[key] = value;
			}
		}
		var data = {
		};
		if( me.panel.id == 'pmstreepanel' )data.path = 'ProjectTaskSettings-' + me.panel.project_id;
		else data.path = 'ActivityTaskSettings-' + me.panel.activity_id;
		data.store = me.store;
		//ajax save
		Ext.Ajax.request({
			url: me.saveUrl,
			jsonData: data,
			method: 'post',
			success: function(response){
				me.store = Ext.JSON.decode(response.responseText);
			}
		});
	},
	remove: function(key){
		var me = this;
		if( key.length ){
			for(var i = 0; i < key.length; i++){
				var k = key[i];
				try {
					delete me.store[k];
				} catch( ex ){}
			}
		} else {
			try {
				delete me.store[key];
			} catch( ex ){}
		}
	},
	//new way of cookie - shorten number of cookies
	getCookie: function(name){
		return (typeof this.cookie[name] != 'undefined' ? this.cookie[name] : null);
	},
	setCookie: function(name, value, opt){
		this.cookie[name] = value;
		var today = new Date();
		var opts = Ext.Object.merge({expires: 30, path: '/'}, opt);
		today.setDate(today.getDate() + (opts.expires ? opts.expires : 30));

		Ext.util.Cookies.set('z0.Task', Ext.JSON.encode(this.cookie), today, opts.path ? opts.path : '/', null, true);
	},
	//cookie are global settings
	getCookieSetting: function(name, isJSON){
		var settings = Ext.util.Cookies.get('azuree.task.' + name);
		if( isJSON ){
			if( !settings )settings = {};
			else settings = Ext.JSON.decode(settings);
		}
		return settings;
	},

	setCookieSetting: function(name, value, opt){
		if( typeof value == 'object' )value = Ext.JSON.encode(value);
		var today = new Date();
		var opts = Ext.Object.merge({expires: 30, path: '/'}, opt);
		today.setDate(today.getDate() + (opts.expires ? opts.expires : 30));
		Ext.util.Cookies.set('azuree.task.' + name, value, today, opts.path ? opts.path : '/', null, true);
	},
});