/*!
 * strength.js
 * Original author: @aaronlumsden
 * Further changes, comments: @aaronlumsden
 * Licensed under the MIT license
 */
;(function ( $, window, document, undefined ) {

	var pluginName = "strength",
		defaults = {
			strengthClass: 'strength',
			strengthMeterClass: 'strength_meter',
			minLength : 8,
			text : ['Weak', 'Medium', 'Strong', 'Very strong'],
			valid : [true, true, true, true],
			textPrefix : 'Strength: ',
			lengthError : 'At least 8 characters',
			emptyError : 'Empty field',
			banListError : 'Contain word',
			banList : [],
			validWhenEmpty : false
		};

	   // $('<style>body { background-color: red; color: white; }</style>').appendTo('head');

	function Plugin(element, options) {
		this.element = element;
		this.$elem = $(this.element);
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.valid = true;
		this.init();
	}

	Plugin.prototype = {

		init: function() {
			var $this = this;

			var characters = 0;
			var capitalletters = 0;
			var loweletters = 0;
			var number = 0;
			var special = 0;

			var upperCase= new RegExp('[A-Z]');
			var lowerCase= new RegExp('[a-z]');
			var numbers = new RegExp('[0-9]');
			var specialchars = /\W/;

			function GetPercentage(a, b) {
				return ((b / a) * 100);
			}

			function check_strength(thisval,thisid){
				if ( thisval.match(specialchars) ) { characters = 1; } else { characters = -1; };
				if (thisval.match(upperCase)) { capitalletters = 1} else { capitalletters = 0; };
				if (thisval.match(lowerCase)) { loweletters = 1}  else { loweletters = 0; };
				if (thisval.match(numbers)) { number = 1}  else { number = 0; };

				var total = characters + capitalletters + loweletters + number + special;
				var totalpercent = GetPercentage(7, total).toFixed(0);

				if (!thisval.length) {total = -1;}

				get_total(total,thisid);
			}

			function get_total(total,thisid){
				var thismeter = $('span[data-meter="'+thisid+'"]');
				var thisval = $('#'+thisid).val(),
					banList = $this.options.banList;
				if( typeof banList == 'function' )
					banList = banList();
				thismeter.removeClass('veryweak weak medium strong');
				if( banList.length ){
					for(var i in banList){
						var word = banList[i];
						if( thisval.toLowerCase().indexOf(word.toLowerCase()) != -1 ){
							$this.valid = false;
							thismeter.addClass('veryweak').html($this.options.banListError);
							return;
						}
					}
				}
				if ( thisval.length > 0 && thisval.length < $this.options.minLength ){
				   	thismeter.addClass('veryweak').html($this.options.lengthError);
				   	$this.valid = false;
				}
				else if (total <= 1) {
					thismeter.addClass('veryweak').html($this.options.textPrefix + $this.options.text[0]);
				   	$this.valid = $this.options.valid[0];
				} else if (total == 2){
				   	thismeter.addClass('weak').html($this.options.textPrefix + $this.options.text[1]);
				   	$this.valid = $this.options.valid[1];
				} else if(total == 3){
				   	thismeter.addClass('medium').html($this.options.textPrefix + $this.options.text[2]);
				   	$this.valid = $this.options.valid[2];

				} else {
				   	thismeter.addClass('strong').html($this.options.textPrefix + $this.options.text[3]);
				   	$this.valid = $this.options.valid[3];
				}
				
				if (total == -1) {
					if( $this.options.validWhenEmpty && !thisval.length ){
						$this.valid = true;
						thismeter.html('');
					}
					else if ( !$this.options.validWhenEmpty && !thisval.length ){
						$this.valid = false;
						thismeter.addClass('veryweak').html($this.options.emptyError);
					} else {
						$this.valid = true;
						thismeter.addClass('veryweak').html('');
					}
				}
			}





			var isShown = false;


			thisid = this.$elem.attr('id');

			this.$elem.addClass(this.options.strengthClass).attr('data-password',thisid).after('<input style="display:none" class="'+this.options.strengthClass+'" data-password="'+thisid+'" type="text" name="" value=""><div class="'+this.options.strengthMeterClass+'"><span data-meter="'+thisid+'"></span></div>');
			 
			this.$elem.bind('keyup keydown', function(event) {
				thisval = $('#'+thisid).val();
				$('input[type="text"][data-password="'+thisid+'"]').val(thisval);
				check_strength(thisval,thisid);
				
			});

			$('input[type="text"][data-password="'+thisid+'"]').bind('keyup keydown', function(event) {
				thisval = $('input[type="text"][data-password="'+thisid+'"]').val();
				$('input[type="password"][data-password="'+thisid+'"]').val(thisval);
				check_strength(thisval,thisid);
			});



			// $(document.body).on('click', '.'+this.options.strengthButtonClass, function(e) {
			// 	e.preventDefault();

			//    thisclass = 'hide_'+$(this).attr('class');

			// 	if (isShown) {
			// 		$('input[type="text"][data-password="'+thisid+'"]').hide();
			// 		$('input[type="password"][data-password="'+thisid+'"]').show().focus();
			// 		isShown = false;

			// 	} else {
			// 		$('input[type="text"][data-password="'+thisid+'"]').show().focus();
			// 		$('input[type="password"][data-password="'+thisid+'"]').hide();
			// 		isShown = true;
   
			// 	}


			   
			// });


		 
			
		},

		AAAvalid: function(el, options) {
			// some logic
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( options ) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin( this, options ));
			}
		});
	};

})( jQuery, window, document );


