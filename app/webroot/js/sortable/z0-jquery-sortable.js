/* Update 
 * Origin file: jquery-sortable.js
 * Change: sortable to z0sortable
 * Reaason: conflict with jquery UI sortable */
(function (factory) {
	"use strict";
	var z0sortable,
		jq,
		_this = this
	;

	if (typeof define === "function" && define.amd) {
		try {
			define(["sortablejs", "jquery"], function(Sortable, $) {
				z0sortable = Sortable;
				jq = $;
				checkErrors();
				factory(Sortable, $);
			});
		} catch(err) {
			checkErrors();
		}
		return;
	} else if (typeof exports === 'object') {
		try {
			z0sortable = require('sortablejs');
			jq = require('jquery');
		} catch(err) { }
	}

	if (typeof jQuery === 'function' || typeof $ === 'function') {
		jq = jQuery || $;
	}

	if (typeof Sortable !== 'undefined') {
		z0sortable = Sortable;
	}

	function checkErrors() {
		if (!jq) {
			throw new Error('jQuery is required for z0-jquery-sortablejs');
		}

		if (!z0sortable) {
			throw new Error('SortableJS is required for z0-jquery-sortablejs (https://github.com/SortableJS/Sortable)');
		}
	}
	checkErrors();
	factory(z0sortable, jq);
})(function (Sortable, $) {
	"use strict";

	$.fn.z0sortable = function (options) {
		var retVal,
			args = arguments;

		this.each(function () {
			var $el = $(this),
				z0sortable = $el.data('z0sortable');

			if (!z0sortable && (options instanceof Object || !options)) {
				z0sortable = new Sortable(this, options);
				$el.data('z0sortable', z0sortable);
			} else if (z0sortable) {
				if (options === 'destroy') {
					z0sortable.destroy();
					$el.removeData('z0sortable');
				} else if (options === 'widget') {
					retVal = z0sortable;
				} else if (typeof z0sortable[options] === 'function') {
					retVal = z0sortable[options].apply(z0sortable, [].slice.call(args, 1));
				} else if (options in z0sortable.options) {
					retVal = z0sortable.option.apply(z0sortable, args);
				}
			}
		});

		return (retVal === void 0) ? this : retVal;
	};
});
