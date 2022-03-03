BigNumber.config({ FORMAT: {
	prefix: '',
	decimalSeparator: '.',
	groupSeparator: ' ',
	groupSize: 3,
	secondaryGroupSize: 0,
	fractionGroupSeparator: '',
	fractionGroupSize: 0,
	suffix: ''
} });
BigNumber.config({ ROUNDING_MODE: BigNumber.ROUND_HALF_CEIL })
String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  for (var i = 0; i < find.length; i++) {
    replaceString = replaceString.replace(find[i], replace[i]);
  }
  return replaceString;
};
function log(o){
	console.log(o);
}
$('body').on('click', function(e){
	var _multisels = $('.wd-multiselect');
	$.each( _multisels, function( ind, _multisel){
		_target = $(e.target);
		_multisel = $(_multisel);
		if( _multisel.find( e.target).length  || (_multisel.length == _target.length && _multisel.length == _multisel.filter(_target).length) ) return;
		else{
			_multisel.find('.wd-combobox-content').fadeOut('300');
		}
	});
})
if( $('#addProjectTemplate').length ){
	$('#addProjectTemplate').appendTo($('body'));
}
function set_layout_height(){
	var _layout = $('#layout');
	if( !_layout.length) return;
	var height_layout = $(window).height() - _layout.offset().top;
	_layout.css('min-height', height_layout);
}
$(document).ready(function(){
	set_layout_height();
});
$(window).resize(function(){
	set_layout_height();
});

$('body').on('click', function(e){
	if( !($('.wd-dropdown.open').find(e.target).length ) ){
		$('.wd-dropdown.open').removeClass('open');
	}
});
function numberCount(s) {
    return encodeURI(s).split(/%..|./).length - 1;
}
function number_format_old(number, decimals, dec_point, thousands_sep) {
  // Strip all characters but numerical ones.
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
	  // var k = Math.pow(10, prec);
	  // return '' + Math.round(n * k) / k;
	  n = new BigNumber(n);
	  return n.toFixed(prec);
	};
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
	s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
	s[1] = s[1] || '';
	s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}
function wd_isArray(x) {
  return x.constructor.toString().indexOf("Array") > -1;
}
function number_format(number, decimals, dec_point, thousands_sep) {
	// console.log( number);
	if( !number) {  number = "0";}
	var n = new BigNumber(number);
	fmt = {
		prefix: '',
		decimalSeparator: dec_point || '.',
		groupSeparator: thousands_sep|| ' ',
		groupSize: 3,
		secondaryGroupSize: 0,
		fractionGroupSeparator: '',
		fractionGroupSize: 0,
		suffix: ''
	};
	decimals = decimals ||0;
	return n.toFormat(decimals, BigNumber.ROUND_UP, fmt);
}
function cancel_dropdown(elm){
	if ( $(elm).length){
		$(elm).closest('.wd-dropdown').removeClass('open');
		var cont = $(elm).closest('.wd-dropdown').data('container');
		cont = $(elm).closest(cont);
		if( cont.length) cont.removeClass('active');
	}else{
		$('.wd-dropdown.open').removeClass('open');
		$.each( $('.wd-dropdown.open'), function(i, item){
			item.removeClass('open');
			var cont = $(item).data('container');
			cont = $(item).closest(cont);
			if( cont.length) cont.removeClass('active');
		});
	}
}
jQuery.removeFromObjectbyKey = function (obj, key) {
	// array
	if( wd_isArray( obj)){
		console.log( obj);
		var new_array = [];
		$.each(obj, function( k, v){
			console.log( k, v);
			if( k != key) new_array.push(v);
		});
		console.log( new_array);
		return new_array;
	}
	//object
	var new_obj = {};
	$.each(obj, function( k, v){
		if( k != key) new_obj[k] = v;
	});
	console.log( new_obj);
	return new_obj;
};
$('.wd-widget .slick-grid-action').on('click', '.slick-control', function(){
	var _slider = $(this).closest('.wd-widget').find('.slides.slick-slider');
	if( $(this).hasClass('slick-next')) _slider.slick('slickNext');
	if( $(this).hasClass('slick-prev')) _slider.slick('slickPrev');
});
$('.wd-input input, .wd-input select').on('change', function(){
	var _has_placeholder = $(this).attr('placeholder');
	_has_placeholder = (typeof _has_placeholder !== 'undefined' && _has_placeholder !== false);
	if( ($(this).val() !== '') || (_has_placeholder) ) $(this).closest('.wd-input').addClass('has-val').removeClass('placeholder');
	else $(this).closest('.wd-input').addClass('placeholder').removeClass('has-val');
	
}); 
$('.wd-input input, .wd-input select').on('focus', function(){
	$(this).closest('.wd-input').addClass('focus');
}); 
$('.wd-input input, .wd-input select').on('focusout', function(){
	$(this).closest('.wd-input').removeClass('focus');
}); 
/* Function thay thế confirm dialog 
* Use: wdConfirmIt(options, functionIfYes, functionIfNo);
* Button mode: WD_ONE_BUTTON, WD_TWO_BUTTON, WD_THREE_BUTTON
*/
function wdConfirmIt(options, functionIfYes, functionIfNo){
	var confirm_dialog = $('<div id="wdConfimDialog" class="alert-popup">' + (options.content ? '<p class="alert-message text-center form-message request-message" autofocus >' + options.content + '</p>' : '') + '</div>');
	def_options = {
		width: 380,
		// height: 150,
		title: '',
		modal : true,
		closeOnEscape: true,
		close: function(ev, ui){
		  if( functionIfNo ) functionIfNo.call(this);
		  confirm_dialog.dialog( "destroy" );
		  $('#wdConfimDialog').remove();
		},
		buttonModel: 'WD_TWO_BUTTON',
		buttonText: ['Yes', 'No', 'Cancel']
	};
	opts = $.extend(def_options, options);
	switch(opts.buttonModel){
		case 'WD_ONE_BUTTON':
			opts.buttons = [{
				text: opts.buttonText[0] ? opts.buttonText[0] : 'OK',
				class: 'btn-form-action btn-ok btn-right',
				click: function() {
					$( this ).dialog( "close" );
					if(functionIfYes) functionIfYes.call(this);
				}
				
			}];
			// opts.classes['ui-dialog-buttonpane'] = 'wd-btn-center';
			break;
		case 'WD_THREE_BUTTON':
			opts.buttons = [
				{
					text: opts.buttonText[2] ? opts.buttonText[2] : 'Cancel',
					class: 'btn-form-action btn-cancel',
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					text: opts.buttonText[1] ? opts.buttonText[1] : 'No',
					classes: 'btn-form-action ',
					click: function() {
						$( this ).dialog( "close" );
						if(functionIfNo) functionIfNo.call(this);
					}
				},
				{
					text: opts.buttonText[0] ? opts.buttonText[0] : 'Yes',
					class: 'btn-form-action btn-ok btn-right',
					click: function() {
						$( this ).dialog( "close" );
						if(functionIfYes) functionIfYes.call(this);
					}
				},
				
			];
			break;
		case 'WD_TWO_BUTTON':
		default: 
			opts.buttonModel = 'WD_TWO_BUTTON';
			opts.buttons = [
				{
					text: opts.buttonText[0] ? opts.buttonText[0] : 'Yes',
					class: 'btn-form-action btn-ok btn-right',
					click: function() {
						$( this ).dialog( "close" );
						if(functionIfYes) functionIfYes.call(this);
					}
				},
				{
					text: opts.buttonText[1] ? opts.buttonText[1] : 'No',
					class: 'btn-form-action btn-cancel',
					click: function() {
						$( this ).dialog( "close" );
						if(functionIfNo) functionIfNo.call(this);
					}
				},				
			];
	}
	opts.create = function( event, ui ) {
		var _dialog = $('#wdConfimDialog').closest('.ui-dialog');
		_dialog.addClass('wd-dialog-2019');
		_dialog.find('.ui-dialog-titlebar').addClass('popup-title');
		_dialog.find('.ui-dialog-content').addClass('alert-message');
		_dialog.find('.ui-dialog-buttonpane').addClass('wd-submit');
		if( opts.buttonModel == 'WD_ONE_BUTTON'){
			_dialog.find('.ui-dialog-buttonpane').addClass(' center');
		}
		setTimeout( function(){
			$('#wdConfimDialog').click();
		}, 500);
	};
	confirm_dialog = confirm_dialog.dialog(opts);
}
var flashtimeout=0;
$(window).ready(function(){
	flashtimeout = setTimeout(function(){
		$('#flashMessage').slideUp(300, function(){
			$(window).trigger('resize');
		});
	}, 3000);
	$('.wd-dropdown').unbind('click').on('click', '.selected', function(e){
		if( $(this).closest('.wd-dropdown').hasClass('disabled')) return;
		var _container = $(this).closest('.wd-dropdown');
		if( !_container.hasClass('open')){
			var beforeopen = _container.attr('onBeforeOpen');
			// console.log(beforeopen);
			if( typeof window[beforeopen] == 'function') window[beforeopen](_container[0]);
		}
		_container.toggleClass('open');
	});
	$('.wd-dropdown').on( 'click',  'ul li a', function(e){
		if( $(this).closest('.wd-dropdown').hasClass('disabled')) return;
		$(this).closest('li').addClass('active').siblings().removeClass('active').children('a').removeClass('active');
		$(this).addClass('active').siblings().removeClass('active');
		var _class = $(this).closest('.wd-dropdown').find('.selected > span');
		$(this).closest('.wd-dropdown').removeClass('open').find('.selected').text($(this).data('text'));
		if( _class.length) _class.prop('class', $(this).data('class'));
		var _input = $(this).closest('.wd-dropdown').find('.wd-dropdown-seleted');
		if( _input.length){
			_input.val($(this).data('value')).trigger('change');
		}
	});	
});
function wd_dropdown_setvalue(elm, val, is_trigger){
	if( elm.is('input')) elm = elm.closest('.wd-dropdown');
// console.log( elm);
	if( (!elm.hasClass( 'wd-dropdown')) || elm.hasClass('disabled')) {
		console.log('Not allow');
		return;
	}
	var new_selected = elm.find( 'li a[data-value="' + val + '"');
	if( new_selected.length){
		elm.find('ul li a').removeClass('active');
		elm.find('ul li').removeClass('active');
		new_selected.addClass('active');
		new_selected.parent().addClass('active');
		$(this).closest('.wd-dropdown').removeClass('open').find('.selected').text($(this).data('text'));
		var _class = elm.find('.selected > span');
		if( _class.length) _class.prop('class', new_selected.data('class'));
		elm.removeClass('open').find('.selected').text(new_selected.data('text'));
		var _input = $(this).closest('.wd-dropdown').find('.wd-dropdown-seleted');
		if( _input.length){
			_input.val(new_selected.data('value'));
			if(is_trigger) _input.trigger('change');
		}
	}else{
		console.log('New value not found', val);
	}
}
(function( $ ){
	$.fn.gotoDate = function(date){
		if( typeof $(this).datepicker !== 'function') return false;
		var _date = new Date();
		if( date) _date = new Date(date);
		//console.log( _date);
		$.each( $(this), function(i, el){
			
			var inst = $.datepicker._getInst(el);
			inst.drawMonth = inst.selectedMonth = _date.getMonth();
			inst.drawYear = inst.selectedYear = _date.getFullYear();
			$.datepicker._notifyChange(inst);
			$.datepicker._adjustDate(el);
		});
	}
})( jQuery );
var wdCreateDialog = function(elm, width, height){
	elm.dialog({
		position    :'center',
		autoOpen    : false,
		height      : height ? height : Math.min( 740, $(window).height() - 80),
		modal       : true,
		width       : width ? width : ( (isTablet || isMobile) ?  320 : 520) ,
		minHeight   : 50,
		open : function(e){
			var $dialog = $(e.target);
			$dialog.dialog({open: $.noop});
		}
	});
	createDialog = $.noop;
}
function show_full_popup(elm, args, trigger){
	var isTablet = isTablet || false;
	var isMobile = isMobile || false;
	if( typeof trigger == 'undefined') trigger = true;
	if( $(elm).hasClass('wd-full-popup')) {
		var _popup = $(elm);
		if(typeof args != 'object') args = {};
		if( 'title' in args) _popup.find('.wd-popup-head h4').html(args.title);			
		if( 'width' in args) _popup.find('.wd-popup-container').width( Math.min(args.width, $(window).width() - 30 ) );
		else _popup.find('.wd-popup-container').width( (isTablet || isMobile) ?  320 : 580 );
		if( 'height' in args) _popup.find('.wd-popup-content').height(args.height);
		else _popup.find('.wd-popup-content:first').css( 'max-height', ( $(window).height() - 70 - 80 ) ); // wd-popup-head cao 70
		$('#layout').addClass('wd-popup-ontop');
		$(elm).fadeIn(100);
		if( trigger ) setTimeout( function(){$(elm).find('input, select, textarea').trigger('change')}, 120);
		var id = $(elm).prop('id');
		if( id ){
			var _id = id.replace(/-/g, '_');
			var _function = _id + '_showed' ;
			// console.log( _function);
			if( typeof window[_function] == 'function'){
				setTimeout( function(){ window[_function](id); }, 120); 
			}
		}		
	}
}
function cancel_popup(elm, trigger=true){
	if( typeof trigger == 'undefined') trigger = true;
	$(elm).closest('.wd-full-popup').fadeOut(300);
	var _forms = $(elm).closest('.wd-full-popup').find('.form-style-2019');
	if( _forms.length) $.each( _forms, function( ind, _form){
		$(_form)[0].reset();
	});
	$(elm).closest('.wd-full-popup').find('.loading-mark').removeClass('loading');
	$('#layout').removeClass('wd-popup-ontop');
	var id = $(elm).closest('.wd-full-popup').prop('id');
	if( id ){
		var _id = id.replace(/-/g, '_');
		var _function = 'cancel_popup_' + _id ;
		// console.log(_id, id, _function);
		if( typeof window[_function] == 'function'){
			window[_function](id);
		}
	}
}
function cancel_dialog(elm){
	$(elm).closest('.ui-dialog-content').dialog("close");
}
var wd_full_popup = $('.wd-full-popup');
if( wd_full_popup.length){
	$('.wd-full-popup').on('click', '.wd-close-popup, .wd-popup-close', function(){
		cancel_popup(this);
	});
}
function wd_radio_button(){
	var radio_btns = $('.wd-input.wd-radio-button input[type="radio"]');
	if( radio_btns.length) {
		$.each(radio_btns, function( index, r_btn){
			var _this = $(r_btn);
			var _id =  _this.prop('id');
			var checked = _this.is(':checked');
			if( _id.length ){
				_this.hide();
				var _label = $('label[for="' + _id + '"]');
				if( !_label.length) $('<label for="' + _id + '"></label>').insertAfter( _this );
				_label = $('label[for="' + _id + '"]');
				_label_txt = _label.text();
				if( !_label.find( '.wd-btn-switch').length){
					var _html = '<span class="wd-btn-switch"><span></span></span>' + _label_txt;
					_label.html( _html);
				}
			}
			
		});
	}
}
wd_radio_button();
function wd_checkbox_switch(){
	var checkbox_btns = $('.wd-input.wd-checkbox-switch :checkbox');
	if( checkbox_btns.length) {
		$.each(checkbox_btns, function( index, c_btn){
			var _this = $(c_btn);
			var _id =  _this.prop('id');
			var checked = _this.is(':checked');
			if( _id.length ){
				_this.hide();
				var _label = $('label[for="' + _id + '"]');
				if( !_label.length) $('<label for="' + _id + '"></label>').insertAfter( _this );
				_label = $('label[for="' + _id + '"]');
				_label_txt = _label.text();
				if( !_label.find( '.wd-btn-switch').length){
					var _html = '<span class="wd-btn-switch"><span></span></span>' + _label_txt;
					_label.html( _html);
				}
			}
			
		});
	}
}
wd_checkbox_switch();
function re_init_afterload(){
	wd_radio_button();	
	$('.wd-input input, .wd-input select').on('change', function(){
		if( $(this).val() != '') $(this).closest('.wd-input').addClass('has-val').removeClass('placeholder');
		else $(this).closest('.wd-input').addClass('placeholder').removeClass('has-val');	
	}); 
	$('.wd-input input, .wd-input select').on('focus', function(){
		$(this).closest('.wd-input').addClass('focus');
	}); 
	$('.wd-input input, .wd-input select').on('focusout', function(){
		$(this).closest('.wd-input').removeClass('focus');
	});
	$('.wd-full-popup').find('select, input').trigger('change');
}
function wd_remove_tag(originalString){
	var strippedString = originalString.replace(/(<([^>]+)>)/gi, "");
	return strippedString;
}
var wdDropzone = {};
if(jQuery) (function($) {
    // function check(ele_start, ele_end){
        // return 1
    // }
	var options = {};
	var def_options = {
		// acceptedFiles: ".jpg,.jpeg,.bmp,.gif,.png,.txt,.doc,.xls,.pdf,.docx,.xlsx,.ppt,.pps,.pptx,.csv,.xlsm,.msg",
		imageSrc: '/img/new-icon/draganddrop.png',
		dictDefaultMessage: 'Drag and Drop your picture 300*200',
		// autoProcessQueue: true,
		addRemoveLinks: true,  
		// maxFiles: 1,
		dictRemoveFile: 'Remove file',
		dz_queuecomplete: function(_Dropzone){
			console.log(_Dropzone);
		},
		dz_success: function(file, _Dropzone){
			console.log( file);
			// console.log( this);
			console.log( _Dropzone);
		},
		dz_sending: function(file, xhr, formData){
			console.log(file, xhr, formData);
			// console.log( this);
		}
	};
    $.fn.wdDropzone = function(args) {
		_elements = this;
		if( !_elements.length) return false;
		options = $.extend(def_options, Azuree.dropzone_option, args);
		return _elements.each(function() {
			// Do something to each element here.
			var _Dropzone = new Dropzone(this, options);
			// console.log( _Dropzone);
			_Dropzone.on("queuecomplete", function() {
				options.dz_queuecomplete.call( );
			});
			_Dropzone.on("success", function(file, responseText, e) {
				console.log(file, responseText, e);
				options.dz_success( file, _Dropzone );
			});
			_Dropzone.on("sending", function(file, xhr, formData) {
				options.dz_sending(file, xhr, formData);
			});
		});
		
    }
})(jQuery);

$(document).ready(function() {
//	$(".wd-tab").tabs();
	/*$('.wd-tab').each(function(){
		$(this).find('.wd-section').hide();

		var current = $(this).find('.wd-item').children('.wd-current');
		if (current.length == 0){
			$(this).find('.wd-item').children(':first-child').addClass('wd-current');
			$($(this).find('.wd-item').children(':first-child').find('a').attr('href')).show();
		}

		$(this).find('.wd-item').find('a').click(function(){
			var current = $(this).parent().hasClass('wd-current');
			if (current == false){
				$(this).parent()
					.addClass('wd-current')
					.siblings().each(function(){
						$(this).removeClass('wd-current');
						$($(this).find('a').attr('href')).hide();
					});
				$($(this).attr('href')).fadeIn();
			}
			return false;
		});
	});	*/
//placeholded input
	if(!Modernizr.input.placeholder){
		$("input").each(function(){
				if($(this).val()=="" && $(this).attr("placeholder")!=""){
					$(this).val($(this).attr("placeholder"));
					$(this).focus(function(){
					if($(this).val()==$(this).attr("placeholder")) $(this).val("");
					});
					$(this).blur(function(){
						if($(this).val()=="") $(this).val($(this).attr("placeholder"));
					});
				}
		});
	}
//placeholded input .end
/*	
	$('#start-date,#end-date,#startdate,#enddate,#originaldate').datepicker({
		showOn          : 'button',
		buttonImage     : 'img/front/calendar.gif',
		buttonImageOnly : true
    });
*/
	//project manager
	$(".wd-pro-manager").click(function(){
		$(".wd-pro-manager-s").slideToggle("slow");
		$(".wd-pro-manager-l").slideToggle("slow");
	});
	$(".wd-pro-manager-s a.wd-close").click(function(){
		$(".wd-pro-manager-s").hide("slow");
		$(".wd-pro-manager-l").show();
	});
	//analyer
	$(".wd-analyer").click(function(){
		$(".wd-analyer-s").slideToggle();
		$(".wd-analyer-l").slideToggle();
	});
	$(".wd-analyer-s a.wd-close").click(function(){
		$(".wd-analyer-s").hide("slow");
		$(".wd-analyer-l").show();
	});
	//developer
	$(".wd-developer").click(function(){
		$(".wd-developer-s").slideToggle();
		$(".wd-developer-l").slideToggle();
	});
	$(".wd-developer-s a.wd-close").click(function(){
		$(".wd-developer-s").hide("slow");
		$(".wd-developer-l").show();
	});

	$("#ui-datepicker-div").hide();
    
    $(".close").click(function(){
       $(this).parent().hide("slow");
       return false; 
    });
    
    /* 27-02-2012 */
    $('.wd-hover-tooltip').bt({width:60,positions: 'top'});
    $(window).scrollTop(0);

    var _first = $('.ui-state-default.slick-headerrow-column.l0.r0:not(.wd-row-custom)');
    if( _first.length && _first.is(':empty') ){
    	_first.addClass('filterer');
    }
});