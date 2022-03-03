(function($){
	if( typeof $.z0 == 'undefined' ){
		$.z0 = {};
	}
	if( typeof $.z0.data == 'undefined' ){
		$.z0.data = function(store){
			this.store = store;
			return this;
		};
		$.z0.data.prototype.get = function(key, d){
			var result;
			if ( typeof this.store[key] != 'undefined' ){
				result = this.store[key];
				//cast object to array
				if( $.isArray(d) ){
					var newResult = [];
					$.each(result, function(i, v){
						newResult.push(v);
					});
					result = newResult;
					this.store[key] = result;
				}
			} else {
				result = d;
			}
			return result;
		};
		$.z0.data.prototype.set = function(key, value){
			this.store[key] = value;
		};
		$.z0.data.prototype.unset = function(){
			var keys = arguments;
			try {
				for(var i = 0; i < keys.length; i++){
					delete this.store[keys[i]];
				}
			} catch(ex){}
		};
		$.z0.data.prototype.getStore = function(){
			return this.store;
		};
	}

	$.z0.clone = function(from, to)
	{
	    if (from == null || typeof from != "object") return from;
	    if (from.constructor != Object && from.constructor != Array) return from;
	    if (from.constructor == Date || from.constructor == RegExp || from.constructor == Function ||
	        from.constructor == String || from.constructor == Number || from.constructor == Boolean)
	        return new from.constructor(from);

	    to = to || new from.constructor();

	    for (var name in from)
	    {
	        to[name] = typeof to[name] == "undefined" ? extend(from[name], null) : to[name];
	    }

	    return to;
	}

	$.z0.substr_count = function(string, subString, allowOverlapping){
		string += "";
	    subString += "";
	    if (subString.length <= 0) return (string.length + 1);

	    var n = 0,
	        pos = 0,
	        step = allowOverlapping ? 1 : subString.length;

	    while (true) {
	        pos = string.indexOf(subString, pos);
	        if (pos >= 0) {
	            ++n;
	            pos += step;
	        } else break;
	    }
	    return n;
	}

	$.z0.History = {
		_saveUrl: '/history_filters/saveSettings',
		_getUrl: '/history_filters/getSettings',
		_store: {},

		init: function(){

		},

		load: function(path, callback){
			var me = this;
			$.ajax({
				url: me._getUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					data: {
						path: path
					}
				},
				success: function(data){
					me._store[path] = data;
					if( $.isFunction(callback) ){
						callback.call(me, new $.z0.data(data));
					}
				}
			});
		},

		get: function(path, def){
			_path = _path.split('.');
			path = _path[0];
			key = _path[1];
			return (typeof this._store[path] != 'undefined' && typeof this._store[path][key] != 'undefined' ? this._store[path][key] : def);
		},

		set: function(_path, value){
			_path = _path.split('.');
			path = _path[0];
			key = _path[1];
			this._store[path][key] = value;
		},

		save: function(path, data){
			if( data instanceof $.z0.data ){
				data = data.getStore();
			}
			var me = this;
			this._store[path] = data;
			$.ajax({
				url: me._saveUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					data: {
						path: path,
						store: data
					}
				}
			});
		}
	};
	$.zIE = function () {
	    var ua = window.navigator.userAgent;

	    var msie = ua.indexOf('MSIE ');
	    if (msie > 0) {
	        // IE 10 or older => return version number
	        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	    }

	    var trident = ua.indexOf('Trident/');
	    if (trident > 0) {
	        // IE 11 => return version number
	        var rv = ua.indexOf('rv:');
	        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	    }

	    var edge = ua.indexOf('Edge/');
	    if (edge > 0) {
	       // Edge (IE 12+) => return version number
	       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	    }

	    // other browser
	    return false;
	}
})(jQuery);


if( typeof $.follow == 'undefined' ){
	$.follow = {};
}

$.fn.follow = function () {
    var $this = this,
        $window = $(window);
    // add a placeholder
    var id = $this.prop('id') + '-placeholder',
    	placeholder = $('#' + id);
    if( !placeholder.length ){
    	placeholder = $('<div id="' + id + '" class="follow-placeholder"></div>').insertAfter($this);
    }
    placeholder.css('height', $this.height());

    var top = $this.offset().top;


    $window.scroll(function(e){
    	if( $window.scrollTop() > top ){
    		placeholder.show();
	        $this.css({
	        	'top': 0,
	        	'left': '0',
	        	'right': '0',
	        	'position': 'fixed',
	        	'z-index': 9999,
	        	'box-shadow': '0 0 15px rgba(0, 0, 0, 0.6)'
	        });
	    } else {
	    	placeholder.hide();
	    	$this.css({
	    		'position': 'inherit',
	    		'z-index': 'inherit',
	    		'box-shadow': '0 0 0'
	    	});
	    }
    });

    return this;
};