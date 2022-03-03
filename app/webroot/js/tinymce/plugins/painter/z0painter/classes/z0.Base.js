z0.Toolbar = fabric.util.createClass({
	initialize: function(painter, settings) {
		var parent = document.getElementById(settings.id);
		var el = document.getElementById(settings.toolbar_id);
		if( !el ){
			el = document.createElement('ul');
			el.id = settings.toolbar_id;
			el.className = 'z0painter-toolbar';
			parent.appendChild(el);
		}
		this.painter = painter;
		this.el = el;
		this._tools = {};
		this.activeTool = null;
	},
	addTool: function(name){
		var tool = new z0.Tool(name);
		this.add(name, tool);
	},
	add: function(name, tool){
		this._tools[name] = tool;
		tool._onAdd.call(this);
	},
	toHTML: function(){
		for(var name in this._tools ){
			var el = this._tools[name]._toHTML();
			this.el.appendChild(el);
		}
	},
	setToolActive: function(name, onoff){
		if( typeof this._tools[name] == 'undefined' )return false;
		if( onoff ){
			this.activeTool = name;
		} else {
			this.activeTool = null;
		}
	}
});

z0.Tool = fabric.util.createClass({
	initialize: function(name, def) {
		this.name = name;
		this.definition = def;
	},
	_onAdd: function(toolbar){
		this.painter = toolbar.painter;
		this.toolbar = toolbar;
	},
	_toHTML: function(){
		var li = document.createElement('li');
		li.id = this.getDOMId();
		li.className = 'z0-tool';
		li.onclick = this._fn;
		return li;
	},
	getDOMId: function(){
		return this.painter.id + '-tool_' + this.name;
	},
	getElement: function(){
		return document.getElementById(this.getDOMId());
	},
	isActive: function(){
		return this.toolbar.activeTool == this.name;
	},
	setActive: function(onoff){
		this.toolbar.setToolActive(this.name, onoff);
	},
	_fn: function(){
		this.setActive(true);
	},
	// need implementation
	draw: function(){}
});

// basic tools

window.z0.Rect = fabric.util.createClass('Rect', {
	initialize: function(name, def){
		this.callSuper('initialize', name, def);
	},
	draw: function(painter){
		var rect = new fabric.Rect({
			left: 100,	// implement later (using mouse drag)
			top: 100,	//
			//fill: 'red',
			width: 50,
			height: 50,
			angle: 45
		});
		rect.set({ strokeWidth: 1, stroke: painter.color });
		painter.canvas.add(rect);
	}
});

window.Painter = {};
z0.Painter = fabric.util.createClass({
	initialize: function(id, settings) {
		settings = settings || {};
		var container = document.getElementById(id);
		if( !settings.toolbar_id ){
			settings.toolbar_id = id + '-toolbar';
		}
		// create canvas element
		this._canvas = document.createElement('canvas');
		this._canvas.id = id + '-paper';
		this.id = id;
		this.color = '#000000';
		container.appendChild(this._canvas);

		// init toolbar
		this.toolbar = new z0.Toolbar(this, settings.toolbar_id, settings);

		// init paper: fabric.Canvas
		
		this.canvas = new fabric.Canvas(this._canvas.id);
		window.Painter[id] = this;
	}
});

z0.Painter.get = function(id){
	return window.Painter[id];
}