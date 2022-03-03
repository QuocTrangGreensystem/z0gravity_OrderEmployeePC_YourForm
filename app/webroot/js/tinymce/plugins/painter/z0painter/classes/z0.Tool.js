z0.Toolbar = fabric.util.createClass({
	initialize: function(parent_id, settings) {
		var parent = document.getElementById(parent_id);
		var el = document.getElementById(settings.toolbar_id);
		if( !el ){
			el = document.createElement('ul');
			el.id = settings.toolbar_id;
			el.className = 'z0painter-toolbar';
			parent.appendChild(el);
		}
	}
});