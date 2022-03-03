/* function sortObjectByValue
* input 
	data = {
		362: "Pilotage"
		365: "Planning"
		366: "Finance"
		368: "Stratégie/Risque"
		369: "Plan de charge"
		668: "Gain 2019"
		672: "vue des droits"
	}
* output
	sortable = {
		0: (2) ["366", "Finance"]
		1: (2) ["668", "Gain 2019"]
		2: (2) ["362", "Pilotage"]
		3: (2) ["369", "Plan de charge"]
		4: (2) ["365", "Planning"]
		5: (2) ["368", "Stratégie/Risque"]
		6: (2) ["672", "vue des droits"]
	}
*/
jQuery.sortObjectByValue = function(data){
	if(typeof data == "object"){
		var sortable = [];
		for (var key in data) {
			sortable.push([key, data[key]]);
		}
		sortable.sort(function(a, b) {
			if(a[1] && b[1]){
				var x = a[1].toLowerCase();		
				var y = b[1].toLowerCase();		
				return x < y ? -1 : x > y ? 1 : 0;
			}
			if( a[1] ) return 1;
			if( b[1] ) return -1;
			return 0;
		});

		return sortable;
	}
	return data;
}
jQuery.orderObjectBySpecialKey = function(data, key, type, isASC){
	var key = key || '';
	var type = type||'number';
	if( typeof isASC == 'undefined') isASC = true;
	var asc = isASC ? 1 : -1;
	function get_valuebykey(arr,key){
		key = key.split('.');
		var res = arr;
		$.each( key, function(i,k){
			if( k in res) res = res[k];
			else res = '';
		});
		return res;
	}
	if(typeof data == "object"){
		var type = type||'number';
		var sortable = [];
		for (var k in data) {
			sortable.push(data[k]);
		}
		sortable.sort(function(a, b) {
			if( typeof a != "object") return -1;
			if( typeof b != "object") return 1;
			var x = get_valuebykey( a, key);
			var y = get_valuebykey( b, key);
			if( type == 'string'){
				x = x.toLowerCase();		
				y = y.toLowerCase();
			}
			if( type == 'number'){
				x = parseFloat(x);
				y = parseFloat(y);
			}		
			var res =  x < y ? -1 : x > y ? 1 : 0;
			return res*asc;
		});
		return sortable;
	}
	return data;
}
jQuery.removeFromArray = function(value, arr) {
	return jQuery.grep(arr, function(elem, index) {
		return elem !== value;
	});
};
jQuery.mergeArrayUnique = function(value, arr) {
	var newArr = [];
	$.each( arguments, function( i, arg){
		if( typeof arg == 'object'){
			$.each( arg, function( i, elm){
				if( $.inArray(elm, newArr) == -1){
					newArr.push(elm);
				}
			});
		}else if(arg){
			newArr.push(arg);
		}
	});
	return newArr;
};
jQuery.isFalse = function ( val, is_strict) {
	if( typeof is_strict == 'undefined' ) var is_strict = false;
	if( typeof val == 'undefined' ) return true;
	if( typeof val == 'object' ) return $.isEmptyObject(val);
	return (!val || ( !is_strict && (val == 'false' || val == '0' || val =="")));
};
function show_form_alert(form, msg) {
	$(form).find('.alert-message').empty().append(msg);
	clearTimeout(alert_timeout);
	alert_timeout = setTimeout(function () {
		$(form).find('.alert-message').empty();
	}, 3000);
}
/* For muulti select */
function init_multiselect(elm, args){
	var listMultiSelect;
	if( $(elm).hasClass('wd-multiselect') ){
		listMultiSelect = $(elm);
	}else{
		console.warn( $(elm), 'Element does not have "wd-multiselect" class');
		return false;
	}
	if( !args) args = {};
	if( args.empty == undefined ) args.empty = 1;
	if( args.callback == undefined ) args.callback = 0;
	if( args.checked == undefined ) args.checked = {};
	if( !$.isEmptyObject(args.checked)){
		args.empty = 1;
	}
	var empty = args.empty;
	var callback = args.callback;
	var checked = args.checked;
	listMultiSelect.each(function(){
		var _mulsel = $(this);
		_mulsel.addClass('waiting');
		var wddata = _mulsel.find('.wd-data');
		var _form = _mulsel.closest('.form-style-2019');
		var _this_id = _mulsel.prop('id');
		var area_append = _mulsel.find('.wd-combobox');
		var icon_selected = area_append.find('.circle-name');
		var _input_search = _mulsel.find('.wd-input-search');
		var data_value = [];
		if( _mulsel.closest('.wd-input').hasClass('required') &&  (_mulsel.find('.multiselect_required').length == 0) ){
			$('<input type="text" value="" class="multiselect_required" required="required"/>').insertAfter(area_append);
		}
		var multiselect_required = _mulsel.find('.multiselect_required:first');
		
		// empty data
		if( empty){
			_mulsel.find('.wd-data.checked').removeClass('checked');
			_mulsel.removeClass('has-val');
			area_append.find('a.circle-name').remove();
			_mulsel.find('.wd-combobox-content :checkbox').prop('checked', false);
			multiselect_required.val('');
			_input_search.val('');
		}
		if( !$.isEmptyObject(args.checked)){
			multiselect_setval(_mulsel,checked);
		}
		area_append.unbind('click').on('click', function(e){
			var target = $( e.target);
			if( target.hasClass('circle-name') || target.is('span') || target.is('img')) return;
			if( !_mulsel.hasClass('disabled') && !_mulsel.hasClass('waiting')){
				$(this).closest('.wd-multiselect').find('.wd-combobox-content').toggle();
			}
		});
		if( _this_id.length &&  !_mulsel.hasClass('disabled') && !_mulsel.hasClass('waiting') ){
			var _function = _this_id.replace(/-/g, '_') + 'onChange';
			if( typeof window[_function] == 'function'){
				window[_function](_this_id);
			}
		}
		if( callback ) {
			callback.call($(this));
		}
		
		var wd_mul_checked = function(_mulsel){
			var checkboxs = _mulsel.find('input[type="checkbox"]');
			$.each(checkboxs, function( ind, checkbox){
				var check_box = $(checkbox);
				if(check_box.is(':checked')){
					check_box.closest('.wd-data').addClass('checked');
				}else{
					check_box.closest('.wd-data').removeClass('checked');
				}
			});				
		}
		wddata.unbind('click').on('click',function(e){
			if( _mulsel.hasClass('disabled')  || _mulsel.hasClass('waiting')){
				return;
			}
			var _this = $(this);
			var _checkbox = _this.find('input[type="checkbox"]');
			var checked = _checkbox.is(':checked');
			_checkbox.prop("checked",!checked);
			var _datas = _checkbox.val();
			checked = _checkbox.is(':checked');
			if(checked){
				var title = '';
				var circle_name = '';
				if( _this.find('.circle-name').length){
					title = _this.find('.circle-name').attr('title');
					circle_name = _this.find('.circle-name').html();
					
				}else{
					title = _this.find('.option-name').text();
					var _val = _datas.split('-');
					var is_pc = _val[1]==1 ? _val[1] : 0;
					var _e_id = _val[0];
					var _avt = is_pc ? '<i class="icon-people"></i>' : ('<img width = 35 height = 35 src="'+  js_avatar( _e_id ) +'" title = "'+ title +'" />');
					circle_name = '<span data-id="' + _datas + '">' + _avt + '</span></a>';
				}
				data_value.push(_datas);
				_mulsel.addClass('has-val');
				multiselect_required.val('1');
				area_append.append('<a class="circle-name" title="' + title + '">' + circle_name + '</a>');
			}else{
				data_value = jQuery.removeFromArray(_datas, data_value);
				area_append.find('span[data-id = "'+ _datas + '"]').closest('.circle-name').remove();
				if(!(area_append.find('.circle-name').length)){  
					_mulsel.removeClass('has-val'); multiselect_required.val('');
				}
			}
			wd_mul_checked(_mulsel);
			if( _this_id.length){
				var _function = _this_id.replace(/-/g, '_') + 'onChange';
				// console.log( _function, typeof eval(_function));
				if( typeof eval(_function) == 'function'){
					eval(_function)(_this_id);
				}
			}
			if( callback ) {
				callback.call(_mulsel[0], this);
			}
		});
		area_append.on('click', '.circle-name', function(e){
			// console.log( 'click', $(e.target));
			var _this = $(this);
			var eid = _this.children('span').data('id');
			_this.remove();
			wddata.find('input[type="checkbox"][value = "'+ eid +'"]').prop("checked", false);
			data_value = jQuery.removeFromArray(eid, data_value);
			if(!(area_append.find('.circle-name').length)) {
				_mulsel.removeClass('has-val'); 
				multiselect_required.val('');
			}
			wd_mul_checked(_mulsel);
			if( _this_id.length){
				var _function = _this_id.replace(/-/g, '_') + 'onChange';
				// console.log( _function, typeof eval(_function));
				if( typeof eval(_function) == 'function'){
					eval(_function)(_this_id);
				}
			}
			if( callback ) {
				callback.call(_mulsel[0], this);
			}
		});
		
		multiselect_required.on('keydown', function(){ $(this).val('')});
		multiselect_required.on('focusout', function(){ $(this).val('')});
		var timeoutID;
		_input_search.keyup(function(e){
			var _this = $(this);
			clearTimeout(timeoutID);
			timeoutID = setTimeout(function(){
				var val = $.trim(_this.val()).toLowerCase();
				_this.closest('.wd-combobox-content').find('.wd-data').each(function(){
					var label = $(this).find('.option-name').html().toLowerCase();
					if(!val.length || label.indexOf(val) != -1 || !val){
						$(this).closest('.wd-data-manager').css('display', 'block');
					} else{
						$(this).closest('.wd-data-manager').css('display', 'none');
					}
				});
			} , 200);
		});
		_mulsel.removeClass('waiting');
		wddata.find(':checkbox:first').trigger('change');
	});
}
function multiselect_setval(elm, $list_ids, callback){
	if(elm.hasClass('wd-multiselect') ){
		var wddata = elm.find('.wd-data');
		var area_append = elm.find('.wd-combobox');
		area_append.find('.circle-name').remove();
		$.each(wddata, function(i, _t){
			var _this = $(_t);
			var check_box = _this.find(':checkbox');
			var val = check_box.val();
			if( $.inArray(val, $list_ids) != -1){
				_this.addClass('checked');
				check_box.prop('checked', true);
				var circle_name = '';
				var title = '';
				if( _this.find('.circle-name').length){
					title = _this.find('.circle-name').attr('title');
					circle_name = _this.find('.circle-name').html();
					
				}else{
					title = _this.find('.option-name').text();
					var _val = val.split('-');
					var is_pc = _val[1]==1 ? _val[1] : 0;
					var _e_id = _val[0];
					var _avt = is_pc ? '<i class="icon-people"></i>' : ('<img width = 35 height = 35 src="'+  js_avatar( _e_id ) +'" title = "'+ title +'" />');
					circle_name = '<span data-id="' + val + '">' + _avt + '</span></a>';
				}
				var multiselect_required = elm.find('.multiselect_required:first');
				elm.addClass('has-val');
				multiselect_required.val('1');
				area_append.append('<a class="circle-name" title="' + title + '">' + circle_name + '</a>');
			}else{
				_this.removeClass('checked');
				check_box.prop('checked', false);
			}
		});
		if( callback ) {
			callback.call(elm);
		}
	}
}
/* END For muulti select */

function js_avatar($id, $size){
	var $id = $id||'%ID%';
	var $size = $size||'small';
	$size =  ($size == 'small') ? '' : '_avatar';
	
	if( (typeof listEmployeeName != 'undefined') && ($id in listEmployeeName)) return  '/img/avatar/'+$id+$size+'.png?ver='+ listEmployeeName[$id]['updated'];
	return  '/img/avatar/'+$id+$size+'.png';
}
function employee_name($id){
	if( (typeof listEmployeeName != 'undefined') && ($id in listEmployeeName) ){
		if(listEmployeeName[$id]['is_pc'] == 1){
			return listEmployeeName[$id]['name'];
		}else{
			return listEmployeeName[$id]['fullname'];
		}
	}
}
function avatar_html($id){
	if( (typeof listEmployeeName != 'undefined') && ($id in listEmployeeName) ){
		if(listEmployeeName[$id]['is_pc'] == 1){
			return '<a class="circle-name" title="'+ employee_name($id) +'"><span data-id="'+$id+'"><i class="icon-people"></i></span></a>';
		}else return '<a class="circle-name" title="'+ employee_name($id)+'"><span data-id="'+$id+'"><img width=35 height=35 src="'+js_avatar($id)+'"/></span></a>';
	};
	return '';
}
