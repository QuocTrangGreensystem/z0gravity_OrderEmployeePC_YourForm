// bootstrap
window.z0 || (window.z0 = {});
window.Painter = {};
window.Toolbar = {};
window.ZFactory = {};

z0.STATE_READY = 0;
z0.STATE_START = 1;
z0.STATE_END = 2;

z0.t = function(text){
	return z0.i18n && z0.i18n[text] ? z0.i18n[text] : text;
}
// filters

fabric.Image.filters.ColorMe = fabric.util.createClass({

	type: 'ColorMe',
	initialize: function(color){
		this.color = new fabric.Color(color).toRgb().replace(/[^\d\,]+/g, '').split(',');
	},
	applyTo: function(canvasEl) {
		var context = canvasEl.getContext('2d'),
			imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
			data = imageData.data;

		for (var i = 0, len = data.length; i < len; i += 4) {
			if( data[i + 3] > 0 ){
				data[i] = parseInt(this.color[0]);
				data[i + 1] = parseInt(this.color[1]);
				data[i + 2] = parseInt(this.color[2]);
			}
		}

		context.putImageData(imageData, 0, 0);
	}
});



ZFactory.create = function(strClass) {
    var args = Array.prototype.slice.call(arguments, 1);
    var clsClass = eval(strClass);
    function F() {
        return clsClass.apply(this, args);
    }
    F.prototype = clsClass.prototype;
    return new F();
};

z0.Toolbar = fabric.util.createClass({
	initialize: function(painter, settings) {
		var parent = document.getElementById(painter.id);
		var el = document.getElementById(settings.toolbar_id);
		if( !el ){
			el = document.createElement('ul');
			el.id = settings.toolbar_id;
			el.className = 'z0painter-toolbar';
			parent.insertBefore(el, parent.firstChild);
		}
		this.painter = painter;
		this.el = el;
		this._tools = {};
		this.activeTool = null;
		this.id = settings.toolbar_id;

		// build html
		settings.toolbars || (settings.toolbars = 'z0.Rect z0.Circle z0.Arrow z0.Line z0.Pencil z0.Text z0.FontStyle z0.ColorSelect z0.StrokeWidth z0.ClearAll');
		if( typeof settings.toolbars == 'string' ){
			settings.toolbars = settings.toolbars.split(/\s+/);
		}
		for(var i = 0, length = settings.toolbars.length; i < length; i++){
			this.addTool(settings.toolbars[i]);
		}
		this.toHTML();

		window.Toolbar[this.id] = this;
	},
	addTool: function(name){
		var tool = ZFactory.create(name);
		this.add(name, tool);
	},
	add: function(name, tool){
		this._tools[name] = tool;
		tool._onAdd.call(tool, this);
	},
	toHTML: function(){
		for(var name in this._tools ){
			var tool = this._tools[name];
			var el = tool._toHTML();
			this.el.appendChild(el);
			if( tool.onBuild ){
				tool.onBuild.call(tool, el);
			}
		}
	},
	setToolActive: function(name, onoff){
		// off active tool
		if( typeof this._tools[name] == 'undefined' )return false;
		if( onoff ){
			this.activeTool = name;
		} else {
			this.activeTool = null;
		}
	},
	getActiveTool: function(){
		return this.find(this.activeTool);
	},
	find: function(name){
		return this._tools[name];
	}
});

z0.Toolbar.get = function(id){
	return window.Toolbar[id];
}

// Abstract Tool
z0.Tool = fabric.util.createClass({
	initialize: function(def) {
		this.name = 'z0.Tool';
		this.definition = def || {};
	},
	_onAdd: function(toolbar){
		this.painter = toolbar.painter;
		this.toolbar = toolbar;
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
	_toHTML: function(){
		var li = document.createElement('li');
		var span = document.createElement('span');
		span.className = this.definition.icon;
		li.id = this.getDOMId();
		li.dataset.toolbar_id = this.toolbar.id;
		li.dataset.name = this.name;
		li.className = 'z0-tool ' + this.name.replace(/\.+/g, '_');
		li.appendChild(span);
		var me = this;
		li.onclick = function(e){
			var t = $(e.target);
			if( t.hasClass('z0-dropdown') || t.closest('.z0-dropdown').length ){
				return;
			}
			me.fn.call(me, this);
		}
		li.title = this.definition.title;
		return li;
	},
	onBuild: function(){
		this.preload();
	},
	resources: [],
	// need extend
	setActive: function(){},
	fn: function(){},
	preload: function(){
		var t = this;
		for(var i = 0, l = this.resources.length; i < l; i++){
			var r = this.resources[i];
			var x = new Image();
			x.onload = function(){
				t.resources.splice(i, 1);
			};
			x.src = r;
		}
	}

});

z0.DrawTool = fabric.util.createClass(z0.Tool, {
	initialize: function(def) {
		this.name = 'z0.DrawTool';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-square-o';
		}
	},
	// _toHTML: function(){
	// 	var li = document.createElement('li');
	// 	var span = document.createElement('span');
	// 	span.className = this.definition.icon;
	// 	li.id = this.getDOMId();
	// 	li.dataset.toolbar_id = this.toolbar.id;
	// 	li.dataset.name = this.name;
	// 	li.className = 'z0-tool ' + this.name.replace(/\.+/g, '_');
	// 	li.appendChild(span);
	// 	li.onclick = this.fn;
	// 	li.title = this.definition.title;
	// 	return li;
	// },
	setActive: function(onoff){
		var el = this.getElement();
		$(this.toolbar.el).find('> li').removeClass('active');
		if( onoff ){
			// deactivate current tool
			var tool = this.toolbar.getActiveTool();
			if( tool ){
				tool.setActive(false);
			}

			this.toolbar.setToolActive(this.name, onoff);

			el.className += ' active';
			// tool are ready for mouse drawing
			this.ready();
		} else {
			el.className = el.className.replace(/\s+active(\s|$)/g, '$1');
			this.toolbar.setToolActive(this.name, onoff);
			this.end();
		}
	},
	fn: function(el){
		if( this.isActive() ){
			this.setActive(false);
		} else {
			this.setActive(true);
		}
	},
	// need implementation
	draw: function(){},
	drawComplete: function(shape){
		this.setActive(false);
	},
	end: function(){
		// unset event
		painter.canvas.off('mouse:down');
		painter.canvas.off('mouse:move');
		painter.canvas.off('mouse:up');
		painter.enableSelection(true);
	},
	// ready will call when this tool is active and user interact with the canvas
	ready: function(){
		var painter = this.painter,
			tool = this;
		painter._draw = {
			state: z0.STATE_READY,
			start: {x: 0, y: 0},
			end: {x: 0, y: 0}
		};
		painter.enableSelection(false);
		painter.canvas.on('mouse:down', function(opts){
			painter._draw.state = z0.STATE_START;
			painter._draw.start = {
				x: opts.e.layerX,
				y: opts.e.layerY
			};
		});
		painter.canvas.on('mouse:move', function(opts){
			if( painter._draw.state == z0.STATE_START ){
				// start fake draw
			}
		});
		painter.canvas.on('mouse:up', function(opts){
			if( painter._draw.state == z0.STATE_START ){
				// paint it
				painter._draw.end = {
					x: opts.e.layerX,
					y: opts.e.layerY
				};
				painter._draw.box = painter.parseBox(painter._draw);
				var shape = tool.draw(painter, painter._draw);
				if( tool.drawComplete ){
					tool.drawComplete.call(tool, shape);
				}
				// reset state
				painter._draw.state = z0.STATE_READY;
			}
		});

	}
});

// style tools
z0.StyleTool = fabric.util.createClass(z0.Tool, {
	initialize: function(def) {
		this.name = 'z0.StyleTool';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-square-o';
		}
	},
	// _toHTML: function(){
	// 	var li = document.createElement('li');
	// 	var span = document.createElement('span');
	// 	span.className = this.definition.icon;
	// 	li.id = this.getDOMId();
	// 	li.dataset.toolbar_id = this.toolbar.id;
	// 	li.dataset.name = this.name;
	// 	li.className = 'z0-tool';
	// 	li.appendChild(span);
	// 	li.onclick = this.fn;
	// 	li.title = this.definition.title;
	// 	return li;
	// },
	setActive: function(){},
	fn: function(){},
	draw: null,
	drawComplete: null,
	end: null,
	ready: null
});

// basic tools

z0.Rect = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Rect';
		this.definition.icon = 'icon-square-o';
		this.definition.title = z0.t('Rectangle');
	},
	draw: function(painter, info){
		var box = info.box;
		if( !box.width || !box.height ){
			return;
		}
		var rect = new fabric.Rect({
			left: box.x,	// implement later (using mouse drag)
			top: box.y,
			width: box.width,
			height: box.height,
			fill: 'rgba(0,0,0,0)',
			tag: 'path',
			strokeWidth: painter.strokeWidth,
			stroke: painter.color
		});
		painter.canvas.add(rect);
	}
});

z0.Circle = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Circle';
		this.definition.icon = 'icon-circle-thin';
		this.definition.title = z0.t('Circle');
	},
	draw: function(painter, info){
		var params = this.parseCircle(info);
		// calculate the center and scale
		var circle = new fabric.Circle({
			left: params.x,	// implement later (using mouse drag)
			top: params.y,	//
			radius: params.radius,
			scaleX: params.scaleX,
			scaleY: params.scaleY,
			fill: 'rgba(0,0,0,0)',
			tag: 'path',
			strokeWidth: painter.strokeWidth,
			stroke: painter.color
		});
		painter.canvas.add(circle);
		return circle;
	},
	// input: info
	// output: {x, y, radius, scaleX, scaleY}
	parseCircle: function(info){
		var result = {x: 0, y: 0, radius: 0, scaleX: 0, scaleY: 0};
		result.radius = Math.max(info.box.width, info.box.height) / 2;
		result.x = info.box.x;
		result.y = info.box.y;
		// result.x = (info.start.x + info.end.x) / 2;
		// result.y = (info.start.y + info.end.y) / 2;
		result.scaleX = (info.box.width / 2) / result.radius;
		result.scaleY = (info.box.height / 2) / result.radius;
		return result;
	},
});

z0.Arrow = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Arrow';
		this.definition.icon = 'icon-long-arrow-right';
		this.definition.title = z0.t('Arrow');
		// wrap resource here for preload
		this.img = z0.Painter.path + 'resources/arrow.png';
		this.resources.push(this.img);

	},
	ready: function(){
		var me = this;
		this.callSuper('ready');
		this.painter.addListener('colorchange', function(shapes, color){
			for(var i = 0; i < shapes.length; i++){
				var shape = shapes[i];
				if( shape && shape.get('tag') == 'image' ){
					me.applyColor(shape);
					// me.painter.canvas.renderAll();
				}
			}
		});
	},
	draw: function(painter, info){
		var box = info.box, me = this;
		if( !box.width || !box.height ){
			return;
		}
		var shape;
		fabric.Image.fromURL(this.img, function(img) {
			img.set({
				left: box.x,
			 	top: box.y,
			 	tag: 'image',
			});
			img.scaleToWidth(box.width);
			img.scaleToHeight(box.height);
			// apply color
			me.applyColor(img);
			painter.canvas.add(img);
			shape = img;
		});
		return shape;
	},
	applyColor: function(shape){
		var painter = this.painter;
		shape.filters.push(
			new fabric.Image.filters.ColorMe(painter.color)
		);

		shape.applyFilters(painter.canvas.renderAll.bind(painter.canvas));
	}
});

// text tool

z0.Text = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Text';
		//this.definition.icon = 'icon-font';
		this.definition.title = z0.t('Text');
	},
	_toHTML: function(){
		// create main text
		var li = $('<li />'),
			me = this;
		li.prop({
			'class': 'z0-tool',
			title: me.definition.title,
			id: me.getDOMId()
		});
		li.data({
			toolbar_id: me.toolbar_id,
			name: me.name
		});
		li.on('click', function(){
			me.fn.call(me, this);
		});
		// span
		var span = $('<span />');
		span.prop({
			'class': 'icon-font'
		});
		li.append(span);
		return li[0];
	},
	ready: function(){
		this.callSuper('ready');
		var painter = this.painter;
		painter.addListener('colorchange', function(shapes, color){
			for(var i = 0; i < shapes.length; i++){
				var shape = shapes[i];
				if( shape && shape.get('tag') == 'text' ){
					shape.setFill(color);
					painter.canvas.renderAll();
				}
			}
		});
	},
	draw: function(painter, info){
		var box = info.box;
		if( !box.width || !box.height ){
			return;
		}
		var text = new fabric.IText(z0.t('your text'), {
			left: box.x,	// implement later (using mouse drag)
			top: box.y,
			fontFamily: 'arial',
			fill: painter.color,
			fontSize: 24,	// default
			tag: 'text'
		});
		painter.canvas.add(text);
	}
});

z0.FontStyle = fabric.util.createClass(z0.StyleTool, {
	initialize: function(def) {
		this.name = 'z0.FontStyle';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-italic';
		}
		this.definition.title = z0.t('Font style');
		this.styles = {
			fontFamily: [
				'arial', 'helvetica', 'verdana', 'comic sans ms'
			],
			fontSize: [10, 12, 13, 14, 16, 18, 20, 24, 30, 36, 48],
			alignment: ['left', 'center', 'right', 'justify']
		};
	},
	_toHTML: function(){
		var li = this.callSuper('_toHTML');
		var list = $('<table class="font-style z0-dropdown"></table>'),
			me = this;
		var tr = $('<tr />');
		tr.append('<td>' + z0.t('Font') + '</td>');
		var select = $('<select class="select-fontfamily"></select>');
		for(var i = 0, length = this.styles.fontFamily.length; i < length; i++){
			var font = this.styles.fontFamily[i];
			select.append('<option value="' + font + '" style="font-family: ' + font + '">' + font + '</option>');
		}
		tr.append($('<td />').append(select));
		list.append(tr);


		var tr = $('<tr />');
		tr.append('<td>' + z0.t('Size') + '</td>');
		var select = $('<select class="select-fontsize"></select>');
		for(var i = 0, length = this.styles.fontSize.length; i < length; i++){
			var size = this.styles.fontSize[i];
			select.append('<option value="' + size + '" style="font-size: ' + size + 'px">' + size + 'px</option>');
		}
		tr.append($('<td />').append(select));
		list.append(tr);

		var tr = $('<tr />');
		tr.append('<td>' + z0.t('Alignment') + '</td>');
		var select = $('<select class="select-alignment"></select>');
		for(var i = 0, length = this.styles.alignment.length; i < length; i++){
			var alignment = this.styles.alignment[i];
			select.append('<option value="' + alignment + '">' + alignment + '</option>');
		}
		tr.append($('<td />').append(select));
		list.append(tr);

		var btn = $('<button>');
		btn.html(z0.t('Apply'));

		btn.on('click', function(){
			var font = list.find('.select-fontfamily').val(),
				size = list.find('.select-fontsize').val(),
				alignment = list.find('.select-alignment').val()
				;
			me.applyStyle(font, size, alignment);
		});
		var handler = $('<tr><td colspan="2" class="z0-action"></td></tr>');
		handler.find('td').append(btn);

		list.append(handler);

		this.list = list;
		$(li).append(list);
		return li;
	},
	// _onAdd: function(toolbar){
	// 	this.callSuper('_onAdd', toolbar);
	// 	console.log('added');
	// },
	onBuild: function(el){
		this.callSuper('onBuild');
		$(el).hide();
		var me = this;
		this.painter.canvas.on('object:selected', function(o){
			var shape = o.target;
			if( shape && shape.get('tag') == 'text' ){
				// get style from shape
				var style = {
					font: shape.get('fontFamily'),
					size: shape.get('fontSize'),
					alignment: shape.get('textAlign')
				};
				me.show(style);
			}
		});
		this.painter.canvas.on('selection:cleared', function(){
			me.hide();
		});
	},
	fn: function(el){
		this.toggle();
	},
	applyStyle: function(font, size, alignment){
		var shape = this.painter.canvas.getActiveObject();
		if( shape && shape.get('tag') == 'text' ){
			shape.set({
				fontFamily: font,
				fontSize: size,
				textAlign: alignment
			});
			this.painter.canvas.renderAll();
		}
		this.painter.trigger('fontstylechange', shape, font, size, alignment);
		// close the picker
		this.close();
	},
	toggle: function(){
		if( this.list.is(':hidden') ){
			this.open();
		} else {
			this.close();
		}
	},
	close: function(){
		this.list.hide();
	},
	open: function(){
		$(this.toolbar.el).find('.z0-dropdown').hide();
		this.list.show();
	},
	show: function(style){
		// this is jQuery object
		this.list.find('.select-fontfamily').val(style.font);
		this.list.find('.select-fontsize').val(style.size);
		this.list.find('.select-alignment').val(style.alignment);
		$(this.getElement()).show();
	},
	hide: function(){
		$(this.getElement()).hide();
	}
});

// pencil, utilize the free drawing mode

z0.Pencil = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Pencil';
		this.definition.icon = 'icon-pencil';
		this.definition.title = z0.t('Pencil');
	},
	ready: function(){
		var canvas = this.painter.canvas;
		var el = this.getElement(),
			me = this;
		canvas.on('path:created', function(e){
			var path = e.path;
			path.set('tag', 'path');
			// path.set('stroke', me.painter.color);
			// canvas.renderAll();
		});
		this.painter.addListener('colorchange', function(shapes, color){
			if( canvas.isDrawingMode ){
				// ignore the shapes
				canvas.freeDrawingBrush.color = color;
			}
		});
		canvas.isDrawingMode = true;
		this.painter.enableSelection(false);
		canvas.freeDrawingBrush = new fabric['PencilBrush'](canvas);
		canvas.freeDrawingBrush.color = this.painter.color;
		canvas.freeDrawingBrush.width = this.painter.strokeWidth;
	},
	end: function(){
		this.painter.canvas.isDrawingMode = false;
		this.painter.enableSelection(true);
	}
});

// line tool

z0.Line = fabric.util.createClass(z0.DrawTool, {
	initialize: function(def){
		this.callSuper('initialize', def);
		this.name = 'z0.Line';
		this.definition.icon = 'icon-minus';
		this.definition.title = z0.t('Line');
	},
	draw: function(painter, info){
		var box = info.box;
		if( !box.width || !box.height ){
			return;
		}
		var shape = new fabric.Line([info.start.x, info.start.y, info.end.x, info.end.y], {
			fill: painter.color,
			stroke: painter.color,
			tag: 'path',
			strokeWidth: painter.strokeWidth
		});
		painter.canvas.add(shape);
	}
});

// color tool
z0.ColorSelect = fabric.util.createClass(z0.StyleTool, {
	initialize: function(def) {
		this.name = 'z0.ColorSelect';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-th-large';
		}
		this.definition.title = z0.t('Color');
		this.colorMap = [
			"000000", "Black",
			"993300", "Burnt orange",
			"333300", "Dark olive",
			"003300", "Dark green",
			"003366", "Dark azure",
			"000080", "Navy Blue",
			"333399", "Indigo",
			"333333", "Very dark gray",
			"800000", "Maroon",
			"FF6600", "Orange",
			"808000", "Olive",
			"008000", "Green",
			"008080", "Teal",
			"0000FF", "Blue",
			"666699", "Grayish blue",
			"808080", "Gray",
			"FF0000", "Red",
			"FF9900", "Amber",
			"99CC00", "Yellow green",
			"339966", "Sea green",
			"33CCCC", "Turquoise",
			"3366FF", "Royal blue",
			"800080", "Purple",
			"999999", "Medium gray",
			"FF00FF", "Magenta",
			"FFCC00", "Gold",
			"FFFF00", "Yellow",
			"00FF00", "Lime",
			"00FFFF", "Aqua",
			"00CCFF", "Sky blue",
			"993366", "Red violet",
			"FFFFFF", "White",
			"FF99CC", "Pink",
			"FFCC99", "Peach",
			"FFFF99", "Light yellow",
			"CCFFCC", "Pale green",
			"CCFFFF", "Pale cyan",
			// "99CCFF", "Light sky blue",
			// "CC99FF", "Plum"
		];
	},
	_toHTML: function(){
		var li = this.callSuper('_toHTML');
		var list = $('<table class="color-select z0-dropdown"></table>'),
			tr = $('<tr />'),
			me = this;
		var cols = 9, j = 1;
		for(var i = 0, l = this.colorMap.length; i < l; i += 2){
			var color = '#' + this.colorMap[i],
				name = z0.t(this.colorMap[i+1]);
			var c = $('<td data-color="' + color + '" title="' + name + '"><span style="background-color: ' + color + '"></span></td>');
			c.on('click', function(){
				me.choose.call(me, $(this).data('color'), this, li);
				event.stopPropagation();
			});
			tr.append(c);
			if( j % cols == 0 ){
				list.append(tr);
				tr = $('<tr />');
			}
			j++;
		}
		this.list = list;
		$(li).append(list);
		return li;
	},
	fn: function(el){
		this.toggle();
	},
	choose: function(color, el, li){
		this.painter.color = color;
		this.list.find('td').removeClass('l-active');
		$(el).addClass('l-active');
		// apply color to current object
		var shapes = [],
			shape = this.painter.canvas.getActiveObject(),
			group = this.painter.canvas.getActiveGroup();
		if( shape ){
			shapes.push(shape);
		} else if( group ){
			shapes = shapes.concat(group.getObjects());
		}
		// apply color
		for(var i = 0; i < shapes.length; i++){
			var shape = shapes[i];
			if( shape.get('tag') == 'path' ){
				shape.set('stroke', color);
			}
		}
		this.painter.trigger('colorchange', shapes, color);
		this.painter.canvas.renderAll();
		$(li).find('> span').css('color', color);
		// close the picker
		this.close();
	},
	toggle: function(){
		if( this.list.is(':hidden') ){
			this.open();
		} else {
			this.close();
		}
	},
	close: function(){
		this.list.hide();
	},
	open: function(){
		$(this.toolbar.el).find('.z0-dropdown').hide();
		this.list.show();
	}
});

// stroke size tool
z0.StrokeWidth = fabric.util.createClass(z0.StyleTool, {
	initialize: function(def) {
		this.name = 'z0.StrokeWidth';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-barcode';
		}
		this.definition.title = 'Stroke width';
		this.sizes = [1, 3, 5, 8];
	},
	_toHTML: function(){
		var li = this.callSuper('_toHTML');
		var list = $('<ul class="size-select z0-dropdown"></ul>'),
			me = this;
		for(var i = 0, l = this.sizes.length; i < l; i++){
			var size = this.sizes[i],
				name = size + 'px';
			var c = $('<li data-size="' + size + '" title="' + name + '" class="' + (this.painter.strokeWidth == size ? 'l-active' : '') + '"><span style="height: ' + name + '"></span></td>');
			c.on('click', function(){
				me.choose.call(me, $(this).data('size'), this);
				event.stopPropagation();
			});
			list.append(c);
		}
		this.list = list;
		$(li).append(list);
		return li;
	},
	fn: function(el){
		this.toggle();
	},
	choose: function(size, el){
		this.painter.strokeWidth = size;
		this.list.find('li').removeClass('l-active');
		$(el).addClass('l-active');
		// apply color to current object
		var shapes = [],
			shape = this.painter.canvas.getActiveObject(),
			group = this.painter.canvas.getActiveGroup();
		if( shape ){
			shapes.push(shape);
		} else if( group ){
			shapes = shapes.concat(group.getObjects());
		}
		// apply color
		for(var i = 0; i < shapes.length; i++){
			var shape = shapes[i];
			if( shape.get('tag') == 'path' ){
				shape.set('strokeWidth', size);
			}
		}
		this.painter.trigger('strokewidthchange', shapes, size);
		this.painter.canvas.renderAll();
		// close the picker
		this.close();
	},
	toggle: function(){
		if( this.list.is(':hidden') ){
			this.open();
		} else {
			this.close();
		}
	},
	close: function(){
		this.list.hide();
	},
	open: function(){
		$(this.toolbar.el).find('.z0-dropdown').hide();
		this.list.show();
	}
});

// reset canvas
z0.ClearAll = fabric.util.createClass(z0.StyleTool, {
	initialize: function(def) {
		this.name = 'z0.ClearAll';
		this.definition = def || {};
		if( !this.definition.icon ){
			this.definition.icon = 'icon-close';
		}
		this.definition.title = 'Clear all';
	},
	fn: function(el){
		if( confirm(z0.t('Clear all?')) ){
			this.clear();
		}
	},
	_toHTML: function(){
		var li = this.callSuper('_toHTML');
		$(li).find('span').css('color', 'red');
		return li;
	},
	clear: function(){
		var canvas = this.painter.canvas;
		canvas.clear().renderAll();
	}
});


// our paper
z0.Painter = fabric.util.createClass({
	initialize: function(id, settings) {
		var me = this;
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
		this.strokeWidth = 3;

		this.wrapper = document.createElement('div');
		// this.wrapper.id = id + '-wrapper';
		this.wrapper.className = 'z0-wrapper';
		this.wrapper.appendChild(this._canvas);

		container.appendChild(this.wrapper);

		// init paper: fabric.Canvas
		
		this.canvas = new fabric.Canvas(this._canvas.id, {
			width: 700,
			height: 500,
			isDrawingMode: false
		});

		window.Painter[id] = this;

		// init toolbar
		this.toolbar = new z0.Toolbar(this, settings);

		// init wrapper
		$(window).resize(function(){
			var w = $(document).width() - 10,
				h = $(document).height() - 10 - $(me.toolbar.el).height();
			$(me.wrapper).css({
				'max-width': w + 'px',
				'max-height': h + 'px'
			});
		}).trigger('resize');

		// init keyboard event
		this.wrapper.tabIndex = 1000;
		// this.wrapper.style.outline = 0;
		$(this.wrapper).keydown(function(evt){
			var canvas = me.canvas;

			// delete object
			if( evt.which == 46 ){
				var shape = canvas.getActiveObject(),
					group = canvas.getActiveGroup();
				me.trigger('beforeshapesremove', shape, group);
				if( shape ){
					me.canvas.remove(shape);
				} else if( group ){
					var objectsInGroup = group.getObjects();
					canvas.discardActiveGroup();
					objectsInGroup.forEach(function(object) {
						canvas.remove(object);
					});
				}
			}

			// move object

			var movementDelta = 1;

		    var activeObject = canvas.getActiveObject();
		    var activeGroup = canvas.getActiveGroup();

		    if (evt.keyCode === 37) {
		        evt.preventDefault(); // Prevent the default action
		        if (activeObject) {
		            var a = activeObject.get('left') - movementDelta;
		            activeObject.set('left', a);
		        }
		        else if (activeGroup) {
		            var a = activeGroup.get('left') - movementDelta;
		            activeGroup.set('left', a);
		        }

		    } else if (evt.keyCode === 39) {
		        evt.preventDefault(); // Prevent the default action
		        if (activeObject) {
		            var a = activeObject.get('left') + movementDelta;
		            activeObject.set('left', a);
		        }
		        else if (activeGroup) {
		            var a = activeGroup.get('left') + movementDelta;
		            activeGroup.set('left', a);
		        }

		    } else if (evt.keyCode === 38) {
		        evt.preventDefault(); // Prevent the default action
		        if (activeObject) {
		            var a = activeObject.get('top') - movementDelta;
		            activeObject.set('top', a);
		        }
		        else if (activeGroup) {
		            var a = activeGroup.get('top') - movementDelta;
		            activeGroup.set('top', a);
		        }

		    } else if (evt.keyCode === 40) {
		        evt.preventDefault(); // Prevent the default action
		        if (activeObject) {
		            var a = activeObject.get('top') + movementDelta;
		            activeObject.set('top', a);
		        }
		        else if (activeGroup) {
		            var a = activeGroup.get('top') + movementDelta;
		            activeGroup.set('top', a);
		        }
		    }

		    if (activeObject) {
		        activeObject.setCoords();
		        canvas.renderAll();
		    } else if (activeGroup) {
		        activeGroup.setCoords();
		        canvas.renderAll();
		    }

		});

		// init background
		if( settings.backgroundImage ){
			this.setBackgroundImage(settings.backgroundImage);
		}

	},
	// utils
	// input: {start:{x,y}, end:{x,y}}
	// output: {x,y, width, height}
	parseBox: function(box){
		var result = {x: 0, y: 0, width: 0, height: 0};
		result.x = Math.min(box.start.x, box.end.x);
		result.y = Math.min(box.start.y, box.end.y);
		result.width = Math.abs(box.start.x - box.end.x);
		result.height = Math.abs(box.start.y - box.end.y);
		return result;
	},
	enableSelection: function(onoff){
		this.canvas.forEachObject(function(o) {
			o.selectable = onoff;
		});
		this.canvas.renderAll();
	},
	draw: function(shape){
		this.canvas.add(shape);
	},
	_events: {},
	addListener: function(name, handler){
		if( typeof this._events[name] == 'undefined' ){
			this._events[name] = [];
		}
		this._events[name].push(handler);
	},
	trigger: function(name){
		var args = Array.prototype.slice.call(arguments, 1);
		var handlers = this._events[name] || [];
		for(var i = 0; i < handlers.length; i++){
			handlers[i].apply(this, args);
		}
	},
	setBackgroundImage: function(url){
		var canvas = this.canvas;
		canvas.setBackgroundImage(url, function(image){
			// set size
			canvas.setWidth(image.width);
			canvas.setHeight(image.height);
			canvas.renderAll.bind(canvas).call(canvas, image);
		});
	},
	toImage: function(type){
		this.canvas.deactivateAll().renderAll();
		return this.canvas.toDataURL(type);
	}
});

z0.Painter.get = function(id){
	return window.Painter[id];
}

z0.Painter.path = '';