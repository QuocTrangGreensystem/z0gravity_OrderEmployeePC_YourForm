// This file is copied from Slick_grid_custom.js
// Edit for milestons screen 
// Synchronize Table and Slider
var SlickGridCustom = {},BaseSlickEditor = function(){};
if( typeof employeeAvatar_link == 'undefined' ) employeeAvatar_link = '/img/avatar/%ID%.png';																							 
(function($){
	var $this = SlickGridCustom,grid;    
	// add ui datepicker create event
	var _updateDatepicker = $.datepicker._updateDatepicker;
	$.datepicker._updateDatepicker = function(a){
		_updateDatepicker.apply(this,arguments);
		if(a.settings && $.isFunction(a.settings.create)){
			a.settings.create(a);
		}
	};
 
	// class SlickGridCustom
	BaseSlickEditor = function(args){	   
		this.tip = '';
		this.input = '';
		this.defaultValue = '';
		this.isCreate = $.isEmptyObject(args.item) || !args.item.id;
        
		this.destroy = function () {
			this.tooltip();
			this.input.remove();
		};
		
		this.getArgs = function(){
			return args;
		}
        
		this.tooltip = function(message , callback){
			switch($.type(message)){
				case 'object' : {
					this.tip = message;
					break;
				}
				case 'undefined':{
					this.tip && this.tip.tooltip('destroy'); 
					break;
				}
				default : {
					this.tip = this.tip || this.input;
					var self = this;
					var div = $('<div />').html(message).append($('<span class="editor-reset" title="'
						+ $this.t('Click to reset.') + '" />').click(function(){
						self.reset();
					}));
					this.tip.tooltip({
						openEvent : 'focus.tooltip',
						closeEvent :'blur.tooltip',
						content : div,
						cssClass : 'editor-error'
					});
					callback && callback.call(this);
				}
			}
		}
        
		this.focus = function () {
            var t = this;
            try {
	            t.input.focus(function(){
	            	var f = t.input.data('focused');
	            	if( f )return;
	            	t.input.data('focused', true);
	            	setTimeout(function(){
	            		t.input.select();
	            	}, 50);
	            }).blur(function(){
	            	t.input.data('focused', false);
	            });
	        } catch (ex){}
			this.input.focus();
		};
        
		this.reset = function () {
			this.setValue(this.defaultValue);
			this.tooltip();
			$(args.container).removeClass('invalid');
		};
        
		this.getValue = function () {
			return this.input.val();
		};

		this.setValue = function (val) {
			this.input.val(val);
		};

		this.loadValue = function (item) {		  
			this.defaultValue = item[args.column.field] || '';
			this.setValue(this.defaultValue);
			this.input[0].defaultValue = this.defaultValue;
		};

		this.serializeValue = function () {
			return this.getValue();
		};
        
		this.applyValue = function (item, state) {		  
			if($.isEmptyObject(item)){
				$.extend(item,{
					id : null,
					'no.' : Number(grid.getDataLength()) +1,
					DataSet : {},
					MetaData :{
						cssClasses : 'pendding'
					},
					'action.' : ''
				});
				$.each($this.fields , function(field,rule){
					item[field] = rule.defaulValue;
				});
				$this.onApplyValue(item,args);
			}
			item[args.column.field] = state;
		};
        
		this.isValueChanged = function () {
			return (this.getValue() != this.defaultValue);
		};
        
		this.validate = function () {
			var option = $this.fields[args.column.id] || {};
			var result = {
				valid: true,
				message: typeof option.message != 'undefined' ? option.message : $this.t('This information is not blank!')
			},val = this.getValue();
			if(option.allowEmpty === false && !val.length && !this.isCreate){
				result.valid = false;
			}
			if(result.valid && val.length){
				if(option.maxLength && val.length > option.maxLength){
					result = {
						valid: false,
						message: $this.t('Please enter must be no larger than %s characters long.' , option.maxLength)
					};
				}else if($.isFunction(args.column.validator)){
					result = args.column.validator.call(this, val, args);
				}
			}
			if(!result.valid && result.message){
				this.tooltip(result.message , result.callback);
			}
			return result;
		};
	}
	$.extend(Slick.Formatters,{
		normal : function(row, cell, value, columnDef, dataContext){
			if (value == null || value == 'null' || 
				(typeof $this.selectMaps[columnDef.field] != 'undefined' && !value)) {
				return "";
			} else {
				return value.toString().replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
			}
		},
		selectBox : function(row, cell, value, columnDef, dataContext){
			var _value = [];
			value && value != '0' && value != 'null' && $.each( (value = value || []) && $.isArray(value) ? value : [value], function(i,val){
				_value.push($this.selectMaps[columnDef.id][val] || val);
			});
			return Slick.Formatters.HTMLData(row, cell, _value.join(', '), columnDef, dataContext);
		},
		percentValue : function(row, cell, value, columnDef, dataContext){
			return Slick.Formatters.HTMLData(row, cell, value + '%', columnDef, dataContext);
		}
	});
	$.extend(Slick.Editors,{
		textBox : function(args){
			$.extend(this, new BaseSlickEditor(args));
			this.input = $("<input type='text' />")
			.appendTo(args.container).attr('rel','no-history').addClass('editor-text');
			this.focus();
		},
		textArea : function(args){
			$.extend(this, new BaseSlickEditor(args));
			this.input = $("<textarea class='textarea-editor' rows='5' />")
			.appendTo(args.container).attr('rel','no-history');
			this.input.closest('.slick-cell').css('overflow' , 
				'visible').closest('.slick-row').css('zIndex' , 10);
			this.focus();
			this.input.bind('keydown keypress',function(e){
				e.stopImmediatePropagation();
			});
			var destroy = this.destroy;
			this.destroy = function(){
				this.input.closest('.slick-cell').css('overflow' ,
					'').closest('.slick-row').css('zIndex' , '');
				destroy.apply(this, $.makeArray(arguments));
			}
		},
		selectBox : function(args){
			this.isCreated = false;
			$.extend(this, new BaseSlickEditor(args));
			this.input = $($this.createSelect($this.selectMaps[args.column.id] ,$this.t('-- Any --')))
			.appendTo(args.container).attr('rel','no-history').addClass('editor-select');
            
			var serializeValue = this.serializeValue;
			this.serializeValue = function(){
				if(!this.isCreated){
					this.input.combobox();
					this.tooltip(this.input.next().find('input'));
					this.isCreated = true;
				}
				return serializeValue.apply(this,$.makeArray(arguments));
			}
			var reset = this.reset;
			this.reset = function(){
				this.input.autocomplete('search', '');
				this.input.next().find('input').val($this.selectMaps[args.column.id][this.defaultValue]);
				reset.apply(this, $.makeArray(arguments));
			}
			this.destroy = function(){
				this.tooltip();
				this.input.combobox('destroy');
				this.input.remove();
			}
			this.focus();
			this.focus = function(){
				this.input.next().find('input').focus();
			}
			if($.isEmptyObject(args.item) && $this.fields[args.column.field] && typeof $this.fields[args.column.field].defaulValue != 'undefined'){
				this.setValue($this.fields[args.column.field].defaulValue);
			}
		},
		mselectBox : function(args){
			var multiSelect;
			$.extend(this, new BaseSlickEditor(args));
			this.input = $($this.createSelect($this.selectMaps[args.column.id] ,$this.t('-- Any --')))
			.appendTo(args.container).attr('multiple','multiple').addClass('editor-select');
                
			!$.isEmptyObject(args.item) && this.loadValue(args.item);
               
			this.input.multiSelect({
				noneSelected: $this.t('-- Any --'), 
				appendTo : $('body'),
				oneOrMoreSelected: '*',
				selectAll: false
			});
			this.tooltip(multiSelect = $(args.container).find('a'));
			multiSelect.data("multiSelectOptions").find('input').attr('rel' , 'no-history');
                
			var destroy = this.destroy;
			this.destroy = function(){
				multiSelect.multiSelectDestroy();
				destroy.apply(this , $.makeArray(arguments));
			}
            
			this.isValueChanged = function(){
				return this.getValue().join(',') != (this.defaultValue || []).join(',');
			}
                
			this.getValue = function(){
				return multiSelect.data("multiSelectOptions").find('input:checked').map(function(){
					return $(this).val();
				}).get();
			}
                
			this.focus();
			this.focus = function(){
				multiSelect.focus();
			}
                
		},
		datePicker : function(args){
			var self = this;
			$.extend(this, new BaseSlickEditor(args));
			this.isCreated = false;
			var item = args.item,
				column = args.column.id;
			this.input = $("<input type='text' class='editor-text editor-datepicker' />").appendTo(args.container);
			var serializeValue = this.serializeValue;
			this.serializeValue = function(){
				if(!this.isCreated){
					this.input.datepicker({
						dateFormat : $this.dateFormat,
						showButtonPanel : true,
						// create : function(ui){
						// 	ui.dpDiv.find('.ui-datepicker-buttonpane').append($('<button>'+$this.t('Clear')+'</button>').button().click(function(){
						// 		self.input.val('');
						// 	}));
						// },
						onSelect : function(){
							self.focus();
						},
						beforeShowDay: function(date){
							//if is editing phase end date
							if( item.phase_planed_start_date && column == 'phase_planed_end_date' ){
								var start = $.datepicker.parseDate('dd-mm-yy', item.phase_planed_start_date);
								return [date >= start, '', ''];
							}
							return [true, '', ''];
						}
					});
					this.isCreated = true;
					this.focus();
				}
				return serializeValue.apply(this,$.makeArray(arguments));
			}
            
			var getValue =  this.getValue;
			this.getValue = function () {
				var val = '';
				try{
					val = $.datepicker.formatDate($this.dateFormat,
						$.datepicker.parseDate($this.dateFormat,getValue.call(this)));
				}catch(e){
					this.setValue('');
				}
				return val;
			};
            
			var destroy =  this.destroy;
			this.destroy = function(){
				this.input.datepicker("hide");
				this.input.datepicker("destroy");
				destroy.call(this);
			}
		},
		dateAbsencePicker : function(args){
			var self = this;
			$.extend(this, new BaseSlickEditor(args));
			this.isCreated = false;
			this.input = $("<input type='text' class='editor-text editor-datepicker' />").appendTo(args.container);
			var serializeValue = this.serializeValue;
			this.serializeValue = function(){
				if(!this.isCreated){
					this.input.datepicker({
						dateFormat : $this.dateAbsenceFormat,
						showButtonPanel : true,
						create : function(ui){
							ui.dpDiv.find('.ui-datepicker-buttonpane').html($('<button>'+$this.t('Clear')+'</button>').button().click(function(){
								self.input.val('');
							}));
						},
						onSelect : function(){
							self.focus();
						}
					});
					this.isCreated = true;
					this.focus();
				}
				return serializeValue.apply(this,$.makeArray(arguments));
			}
            
			var getValue =  this.getValue;
			this.getValue = function () {
				var val = '';
				try{
					val = $.datepicker.formatDate($this.dateAbsenceFormat,
						$.datepicker.parseDate($this.dateAbsenceFormat,getValue.call(this)));
				}catch(e){
					this.setValue('');
				}
				return val;
			};
            
			var destroy =  this.destroy;
			this.destroy = function(){
				this.input.datepicker("hide");
				this.input.datepicker("destroy");
				destroy.call(this);
			}
		},
		percentValue : function(args){
			var self = this;
			$.extend(this, new Slick.Editors.textBox(args));
            
			var serializeValue =  this.serializeValue;
			this.serializeValue = function(){
				self.setValue(Math.max( Math.min(parseInt(self.getValue(), 10) || 0, 100) ,0));
				return serializeValue.call(this);
			}
			this.focus();
		},
		numericValue : function(args){
			$.extend(this, new Slick.Editors.textBox(args));
			this.input.attr('maxlength' , 10).keypress(function(e){
				var key = e.keyCode ? e.keyCode : e.which;
				if(!key || key == 8 || key == 13){
					return;
				}
				var val = $(e.currentTarget).replaceSelection(String.fromCharCode(key));
				if(val == '0' || !/^([1-9]|[1-9][0-9]*)$/.test(val)){
					e.preventDefault();
					return false;
				}
			});
			this.focus();
		},
		SlickLabel: function(args){
			$.extend(this, new BaseSlickEditor(args));
			this.input = $('<div class="slick-label" />').appendTo(args.container);
		}
	});
    
    
	$.extend($this, {
		url : '',
		canModified : false,
		isExporting : false,
		isExpand: false,
		currency: '&euro;',		  
		i18n : {},
		dateFormat : 'dd-mm-yy',
		dateAbsenceFormat : 'dd-M',
		fields : {
		// example phase_real_start_date : ['phase_planed_start_date']
		},
		selectMaps : {},
		/**
         * Replace placeholders with sanitized values in a string. supported %s or %s1$s
         */
		format : function(str,args) {
			var regex = /%(\d+\$)?(s)/g,
			i = 0;
			return str.replace(regex, function (substring, valueIndex, type) {
				var value = valueIndex ? args[valueIndex.slice(0, -1)-1] : args[i++];
				switch (type) {
					case 's':
						return String(value);
					default:
						return substring;
				}
			});
		},
		/**
         * Translate strings to the page language or a given language.
         */
		t : function (str,args) {
			if ($this.i18n[str]) {
				str = $this.i18n[str];
			}
			if(args === undefined){
				return str;
			}
			if (!$.isArray(args)) {
				args = $.makeArray(arguments);
				args.shift();
			}
			return $this.format(str, args);
		},
		onBeforeEdit : function(args){
			return true;
		},
		onAddNewRow : function(args){
			return true;
		},
		onColumnsResized : function(args){
			return true;
		},							
		onCellChange : function(args){
			return true;
		},
        onContextMenu : function(args){
            return true;
        },
		onBeforeSave: function (args){
			return true;
		},
		onAfterSave : function(result,args){},
		onApplyValue : function(item,args){},
		onSort : function(args){},
		createSelect :  function(data,empty){
			var o = '';
			if(empty){
				o+= '<option selected="selected" value="">' + empty + '</option>';
			}
			$.each(data , function(i,v){
				o += '<option value="'+i+'">' + v + '</option>';
			});
			return '<select>'+ o + '</select>';
		},
        createSelectSort :  function(data,empty){
			var o = '';
			if(empty){
				o+= '<option selected="selected" value="">' + empty + '</option>';
			}
            var listDatas = new Array();
            //$.each(data, function(i, v){
//                listDatas[i] = v;
//            });
            if($this.accessible_profit_sort){
                $.each($this.accessible_profit_sort, function(ind, val){
                    var _key = val['key'] ? val['key'] : 0;
                    var _val = val['value'] ? val['value'] : '';
                    //if($.inArray(_val, listDatas) != -1){
                        o += '<option value="'+_key+'">' + _val + '</option>';
                    //}
                });
            }
			return '<select>'+ o + '</select>';
		},
		/**
         * Convert string date to unix timestamp
         */
		getTime : function(value){
			value = value.split("-");
			return (new Date(parseInt(value[2] ,10), parseInt(value[1], 10) - 1, parseInt(value[0], 10))).getTime();
		},
		/**
         * Convert timestamp date to number day
         */
		toDay : function(value){
			return parseInt(value ,10) / 86400000;
		},
		/**
         * Initalize
         */
		save : function(){
		//            args.grid._args =  args;
		//            args.grid.eval('trigger(self.onCellChange, {row: activeRow,cell: activeCell,item: self._args.item});');
		//            delete args.grid._args;  
		},
        /**
         * Format number
         */
        number_format : function(number, decimals, dec_point, thousands_sep) {
          number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
          var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
              var k = Math.pow(10, prec);
              return '' + Math.round(n * k) / k;
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
        },
        getInstance: function(){
        	return grid;
        },
        bindHeaderRow: function(){
        	var g = this;
        	$(grid.getHeaderRow()).find(':input').on("change keyup", function () {
				var $element = $(this);
				g.syncFilter($element , $element.data('column') , 500);
			});
        },
		init : function(container,dataSet,columns,options){
			options = $.extend({
				enableCellNavigation: true,
				enableColumnReorder: false,
				asyncEditorLoading: false,
				showHeaderRow: true,
				headerRowHeight: 36,
				rowHeight: 33,
				editable: $this.canModified,
				enableAddRow: $this.canModified,
				defaultFormatter: Slick.Formatters.normal,
				autoEdit: true
			},options || {});
            
			if(!$this.canModified){
				columns.pop();
			}
            
			(function(){
				for (var c in $this.selectMaps){
					if($.isArray($this.selectMaps[c])){
						$this.selectMaps[c] = {};
					}
				}
			})();
            
			var $container = $(container),historySort = false,$sortOrder,$sortColumn;
			var dataView = new Slick.Data.DataView();
			grid = new Slick.Grid($container, dataView, columns, options);

			grid.getDataView = function(){
				return dataView;
			};
        
			dataView.onRowCountChanged.subscribe(function (e, args) {
				grid.updateRowCount();
				grid.render();
			});
			dataView.onRowsChanged.subscribe(function (e, args) {
				grid.invalidateRows(args.rows);
				grid.render();
			});
			//1111
			dataView.getItemMetadata = function(row){
				var _itemData = dataView.getItem(row)||{};
				var _metaData = (dataView.getItem(row)||{}).MetaData || [];
				if(_itemData.milestone_date){
					var ms_date = _itemData.milestone_date.split("-");
					ms_date = new Date(ms_date[2]+'-'+ms_date[1]+'-'+ms_date[0]);
					var curr_date = new Date();
					var color_class = '';
					if(_itemData.validated=='yes'){
	                    color_class = "milestone-row-green";
	                }else {
	                    if (ms_date < curr_date) {
	                        color_class ="milestone-row-mi";
	                    } else if(ms_date > curr_date) {
	                        color_class ="milestone-row-blue";
	                    } else {
	                        color_class = "milestone-row-orange";
	                    }
	                }

	                _metaData.cssClasses = (( _metaData.cssClasses != undefined ) ?  _metaData.cssClasses + ' ' : '' )  + color_class;
	            }

				return  _metaData;
			}
            
			//grid.setSelectionModel(new Slick.CellSelectionModel());
			grid.onSort.subscribe(function(e, args) {
				var callback = $this.onSort(args);
				if(callback === false){
					return;
				}
				if(!$.isFunction(callback)){
					callback = function(a,b){
						//MODIFY BY VINGUYEN 12/11/2014
						//var x = a[args.sortCol.id], y = b[args.sortCol.id];	
						//return (x == y ? 0 : (x > y ? 1 : -1));
						if(typeof $this.selectMaps[args.sortCol.id] != 'undefined')
						{
							var x = $this.selectMaps[args.sortCol.id][a[args.sortCol.id]] ? $this.selectMaps[args.sortCol.id][a[args.sortCol.id]] : '';
							var y = $this.selectMaps[args.sortCol.id][b[args.sortCol.id]] ? $this.selectMaps[args.sortCol.id][b[args.sortCol.id]] : '';
						}
						else
						{
							var x = a[args.sortCol.id], y = b[args.sortCol.id];	
						}
						return comparerMs(x, y, 1, args.sortCol.datatype ? args.sortCol.datatype : '');
						
						//END
					}
				}
				dataView.sort(callback, args.sortAsc);
				if(historySort){
					historySort = false;
					return;
				}
				$sortOrder.val(args.sortAsc ? 'asc' : 'desc').change();
				$sortColumn.val(args.sortCol.id).change();
			});
            
			grid.onColumnsResized.subscribe(function (e, args) {			 
				for (var i = 0; i < columns.length; i++) {
					if(columns[i].previousWidth != columns[i].width){
						$('input[name="' + columns[i].id + '.Resize"]').val(columns[i].width).change();
						break;
					}
				}
			});
            
            grid.onContextMenu.subscribe(function(e, args) {
                if($this.onContextMenu(args) === false){
					return;
				}
                e.preventDefault();
			});
            
			grid.onAddNewRow.subscribe(function (e, args) {
				if($this.onAddNewRow(args) === false){
					return;
				}
				grid.invalidateRow(dataView.getItems().length);
				dataView.addItem(args.item);
				grid.updateRowCount();
				grid.render();
				args.grid._args =  args;
				args.grid.eval('trigger(self.onCellChange, {row: activeRow,cell: activeCell,item: self._args.item});');
			});
            
			grid.onBeforeEditCell.subscribe(function (e, args) {
				if(!$this.onBeforeEdit(args) || !required(args.item,args.column, true)){
					return false;
				}
				return !$(args.grid.getCellNode(args.row,args.cell)).parent().hasClass('disabled');
			});
			var validate = function (item, column){
				var result = true;
				$.each($this.fields , function(field , rule){
					if(rule.allowEmpty === false && (!item || !item[field])){
						return result = false;
					}
					return result = required(item,column);
				});
				return result;
			};
			var required = function (item, column , strict){
				var result = true, rule = $this.fields[column.id] || {};
				$.each(rule.required || [] , function(undefined , field){
					if(!item || (!item[field] && !(strict && column.id == field))){
						return result = false;
					}
				});
				return result;
			};
            
			var _filter = function (v,d,is){
                //var d = d.toString();
				if($.isArray(d)){
					if($.inArray(v, d) != -1){
						return true;
					}
				}else if ( d != null && ((is && d == v) || (!is && d.toString().toLowerCase().indexOf(v) != -1)) ) {
					return true;
				}
				return false;
			};
			var filter = function(item) {
				var result = true;
				$.each(columnFilters , function(i,data){
					var c = grid.getColumns()[i].id;
					var d = item[c];
                                        
					result = true;
					if(data.length > 0){
						if($.isArray(data)){
							result = false;
							$.each(data,function(undefined,v){
								if((result = _filter(v,d,selectFilters[c]))){
									return false;
								}
							});
						}else{
							result = !_filter(v,d,selectFilters[c]);
						}
					}
					if(!result){
						return false;
					}
				});
				return result;
			}
			var columnFilters = {}, selectFilters = {}, timeOutId = null, syncFilter;
			this.syncFilter = syncFilter = function($input,column, delay){
				var result = [];
				$input.each(function(){
					result.push($.trim(this.value).toLowerCase());
				});
				columnFilters[grid.getColumnIndex(column)] = result;
				clearTimeout(timeOutId);
				timeOutId = setTimeout(function(){
					dataView.refresh();
                    // tinh consumed of top, after filter and refresh grid
                    var length = dataView.getLength();
                    var listSumTop = new Array();
                    for(var i = 0; i < length ; i++){
                        $.each(dataView.getItem(i), function(key, val){
                            if($.inArray(key, $this.columnNotCalculationConsumed) != -1){
                                // do nothing
                            } else {
                                if(!listSumTop[key]){
                                    listSumTop[key] = 0;
                                }
                                val = val ? $this.number_format(val, 2, '.', '') : 0;
                                listSumTop[key] += parseFloat(val);
                            }
                        });
                    }
                    for (var i = 0; i < columns.length; i++){
                        var column = columns[i];
                        var idOfHeader = column.id;
                        var valOfHeader = (listSumTop[column.id] || listSumTop[column.id] == 0) ? listSumTop[column.id] : '';
                        if($.inArray(idOfHeader, $this.columnAlignRightAndManDay) != -1){
                            valOfHeader = $this.number_format(valOfHeader, 2, ',', ' ') + ' ' + $this.t('M.D');
                        } else if($.inArray(idOfHeader, $this.columnAlignRightAndEuro) != -1){
                            valOfHeader = $this.number_format(valOfHeader, 2, ',', ' ') + ' &euro;';
                        } else {
                            if(valOfHeader){
                                valOfHeader = $this.number_format(valOfHeader, 2, ',', ' ');
                            }
                        }
                        idOfHeader = idOfHeader.toString().replace('.', '_');
                        if($this.moduleAction){
                            idOfHeader = $this.moduleAction + idOfHeader;
                        }
                        
                        $('#'+idOfHeader+' p').html(valOfHeader);
                    }
                    // end tinh consumed of top, after filter and refresh grid
                    /**
                     * Add class cho mot so screen co ton tai
                     */
                    $('.row-parent').parent().addClass('row-parent-custom');
                    $('.row-disabled').parent().addClass('row-disabled-custom');
                    $('.row-number').parent().addClass('row-number-custom');
				},delay || 200);
			};
			var updateFilter;
			this.updateFilter = updateFilter = function(args){
				var cols = args ? [args.column] : columns,$input;
				if((args && $this.selectMaps[args.column.id]) || $.isEmptyObject(selectFilters)){
					selectFilters = {};
					$.each(dataView.getItems(), function(undefined,data){
						$.each(cols , function(undefined, column){
							if($this.selectMaps[column.id]){
								if(!selectFilters[column.id]){
									selectFilters[column.id] = {};
								}
								if(typeof $this.selectMaps[column.id][data[column.id]] != 'undefined'){
									selectFilters[column.id][data[column.id]]  =  $this.selectMaps[column.id][data[column.id]];
                                } else {
                                    if(data[column.id]){
                                        $.each(data[column.id], function(ind, val){
                                            if(typeof $this.selectMaps[column.id][val] != 'undefined'){
                                                selectFilters[column.id][val]  =  $this.selectMaps[column.id][val];
                                            }
                                        });
                                    }
                                }
							}
						});
					});
				}
				if(args && !selectFilters[args.column.id]){
					return;
				}
				$.each( cols , function(undefined,column){
					if(!args && $this.selectMaps[column.id] && !column.formatter){
						column.formatter = Slick.Formatters.selectBox
					}
					if(!column.noFilter){ 
						var $header = $(grid.getHeaderRowColumn(column.id));
						if(!($input = $header.find(".input-filter,.multiSelect")).length){
							if(selectFilters[column.id]){
                                if(column.id === 'accessible_profit' || column.id === 'linked_profit'){
                                    $input = $($this.createSelectSort(selectFilters[column.id] , true));
                                } else {
                                    $input = $($this.createSelect(selectFilters[column.id] , true));
                                }
							}else{
								$input = $("<input type=\"text\" />");
							}
							$('<div class="multiselect-filter"></div>').append($input.addClass("input-filter").attr("id",column.id)
								.attr("name",column.id).data("column",column.id)).appendTo($header);
						}else{
							if(selectFilters[column.id]){
								$input.multiSelectOptionsUpdate(selectFilters[column.id]);
							}
							return;
						}
						if($this.selectMaps[column.id]){
							$input.multiSelect({
								column : column.id,
								noneSelected: $this.t('-- Any --'), 
								appendTo : $('body'),
								oneOrMoreSelected: '*',
								selectAll: false,
								cssClass: 'slickgrid-select'
							},function(){
								var o = this.data("config");
								syncFilter(this.data("multiSelectOptions").find('input:checked'),o.column);
							});
						}
						$("<input type=\"text\" style=\"display:none\" name=\""+ column.id +".Resize\" />")
						.data('columnIndex',column.id).appendTo($container).change(function(){
							var $element = $(this), index = grid.getColumnIndex($element.data('columnIndex'));
							columns[index].width = Number($element.val());
							grid.eval('applyColumnHeaderWidths();updateCanvasWidth(true);');
						});
					}
				});
			};
        
			grid.onCellChange.subscribe(function (e, args) {
				args.column = columns[args.cell];
				// console.log(args);
				updateFilter(args);
				if($this.onCellChange(args) === false){
					return false;
				}
				if(validate(args.item,args.column) === false){
					return false;
				}
				if($this.onBeforeSave(args) === false){
					return false;
				}
				// add class Color
				var ms_date = args.item.milestone_date.split("-");
				ms_date = new Date(ms_date[2]+'-'+ms_date[1]+'-'+ms_date[0]);
				var ms_date_timestame = ms_date/1000;
				var curr_date = new Date();
				var color_class = '';
				if(args.item.validated=='yes'){
                    color_class = "milestone-row-green";
                }else {
                    if (ms_date < curr_date) {
                        color_class ="milestone-row-mi";
                    } else if(ms_date > curr_date) {
                        color_class ="milestone-row-blue";
                    } else {
                        color_class = "milestone-row-orange";
                    }
                }
				// console.log( color_class);

				//End Add Class color
				
				var submit = {};
				$.each(args.item,function(i,v){
					var h = false;
					$.each($this.fields,function(f){
						if(f == i){
							return !(h = true);
						}
					});
					h && (submit[i] = v);
				});
				var $cell = $(args.grid.getCellNode(args.row,args.cell));
				var setCss = function(klass){
					if(!args.item.MetaData){
						args.item.MetaData = {};
					}
					args.item.MetaData.cssClasses = klass + ' ' + color_class;
					$cell.parent().removeClass('error pendding success disabled').addClass(klass);
					$cell.parent().removeClass('milestone-row-orange milestone-row-blue milestone-row-mi milestone-row-green').addClass(color_class);
				}
				var parseResult = function(data){
					if(data.result == false){
						setCss('error');
					}else{
						setCss('success');
					}
					$cell.removeClass('loading');
					$.extend(args.item ,data.data);
                    
					if(args.item.id){
						var dt = args.grid.getData();
						dt.eval('updateIdxById(0)');
					}
                    
					args.grid.updateRow(args.row);
					$('#message-place').html(data.message);
					$this.onAfterSave(data.result , args);
					setTimeout(function(){
						$('#message-place .message').fadeOut('slow');
					} , 5000);
				};
				var ms_sld_it = $('#milestone-slider .milestones-item[data-id="' + submit.id + '"]');
				// console.log( ms_sld_it);
				$.ajax({
					url : $this.url,
					cache : false,
					type : 'POST',
					dataType : 'json',
					data : {
						data : submit
					},
					beforeSend : function(){
						setCss('disabled');
						$cell.addClass('loading');
						ms_sld_it.addClass('loading');

					},
					success : function(data){
						parseResult(data);
						var list_month = ["Jan", "Feb", "Mar", "Ap", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
						var ms_date = data.data.milestone_date.split("-");
						ms_date = new Date(ms_date[2]+'-'+ms_date[1]+'-'+ms_date[0]);
						
						// console.log(ms_date.getTime());
						if( ms_sld_it.length == 0) {
							if( typeof reDrawMilestoneSlider == 'function') {
								reDrawMilestoneSlider();
								return;
							}
							else location.reload();
						}
						ms_sld_it.find('span b').html(ms_date.getDate());
						ms_sld_it.find('span:last').html(list_month[ms_date.getMonth()]);
						
						var curr_date = new Date();
						var color_class = '';
						if(data.data.validated=='yes'){
		                    color_class = "milestone-green milestone-validated";
		                }else {
		                    if (ms_date < curr_date) {
		                        color_class ="milestone-mi";
		                    } else if(ms_date > curr_date) {
		                        color_class ="milestone-blue";
		                    } else {
		                        color_class = "milestone-orange";
		                    }
		                }
		                ms_sld_it.removeClass('milestone-orange milestone-blue milestone-mi milestone-green milestone-validated').addClass(color_class);
						ms_sld_it.find('p').html(data.data.project_milestone);
						if( args.column.field=='milestone_date') {
							ms_sld_it.closest('.wd-slider-item').attr('data-time', Math.round(ms_date.getTime()/1000));
							if( typeof reDrawMilestoneSlider == 'function') reDrawMilestoneSlider();
						}
						ms_sld_it.removeClass('loading');
					},
					error : function(){
						parseResult({
							result : false,
							message : $this.defaultMessage,
							data : { }
						});
					}
				});
				return true;
			});
            
            //ADD CODE BY VINGUYEN 16/05/2014---------
			var comparerMs = function(x,y,isAsc,type) {
				x = typeof x == 'string' ? x.toLowerCase() : x ;
				y = typeof y == 'string' ? y.toLowerCase() : y ;
				if(isAsc==1)	var isAsc=1;
				else	var isAsc=-1;
				if(type=='datetime')
				{
					var arr;
					if (typeof(x) === "undefined" || x==""){
						c = "1/1/1970";
					}         
					else{
						arr = x.split("-");
						c = arr[1]+"/"+arr[0]+"/"+arr[2];
					}
					if (typeof(y) === "undefined" || y==""){
						d  = "1/1/1970";
					}else{
						arr = y.split("-");
						d = arr[1]+"/"+arr[0]+"/"+arr[2];
					}   
					var c = new Date(c),
					d = new Date(d);
					if((c-d)==0) result = 0;
					result = (c.getTime() - d.getTime());
				}
				else
				{
					if(x==y) result = 0;
					else result = (x > y ? 1 : -1);
				}
				return result*isAsc;
			}		
			var dataFieldSort = function(r1,r2,field)
			{
				var value = []
				if(typeof $this.selectMaps[field] != 'undefined')
				{
					value[0] = $this.selectMaps[field][r1] ? $this.selectMaps[field][r1] : '';
					value[1] = $this.selectMaps[field][r2] ? $this.selectMaps[field][r2] : '';
				}
				else
				{
					value[0] = r1;
					value[1] = r2;
				}
				return value;
			}
			var gridSorter = function(columnField,isAsc,arraySort) {
				var length=arraySort.length-1;
				var sign = isAsc ? 1 : -1;
				var field = columnField;
				dataView.sort(function (dataRow1, dataRow2) {
					for(j=0;j<length;j++)
					{
						var column = grid.getColumns()[grid.getColumnIndex(arraySort[j].columnId)];
						var type = column.datatype;
						var $val = dataFieldSort(dataRow1[arraySort[j].columnId],dataRow2[arraySort[j].columnId],arraySort[j].columnId);
						var checkComparer = comparerMs($val[0],$val[1],arraySort[j].sortAsc,type);
						if( checkComparer )
							return checkComparer;
						else
						{
							var column = grid.getColumns()[grid.getColumnIndex(arraySort[j+1].columnId)];
							var type = column.datatype;
							var $val = dataFieldSort(dataRow1[arraySort[j+1].columnId],dataRow2[arraySort[j+1].columnId],arraySort[j+1].columnId);
							comparerMs($val[0],$val[1],arraySort[j+1].sortAsc,type);
						}
					}
				});   
			};
			grid.sort = function(sortCols){
				// cast object to array
				var arr = sortCols;
				if( $.isArray(sortCols) ){
					var newResult = [];
					$.each(sortCols, function(i, v){
						v.sortAsc = v.sortAsc == 'true' || v.sortAsc == '1' ? 1 : -1;
						newResult.push(v);
					});
					arr = newResult;
				}
				arr2 = arr.slice(0);
				if( arr.length ){
					arr.push({'columnId':'no.','sortAsc':1});
					gridSorter(null, null, arr);
					grid.invalidate();
					grid.render();
					grid.setSortColumns(arr2);
				}
			};
            $('#onSort').click(function(){
				var arraySort=JSON.parse(jQuery('#strMultiSort').val());
				var arraySortTemp = arraySort.slice(0);
				var obj={'columnId':'no.','sortAsc':1};
				arraySort.push(obj);
				args1=arraySort[0];
				gridSorter(args1.columnId,args1.sortAsc,arraySort);
				grid.invalidate();
				grid.render();
				grid.setSortColumns(arraySortTemp);
				if( typeof sortHandler == 'function' ){
					sortHandler(arraySortTemp);
				}
				showHideIt();	
			});
			//END ADD--------
            
			dataView.beginUpdate();
			dataView.setItems(dataSet);
			dataView.setFilter(filter);
			updateFilter();
			dataView.endUpdate();
        
			$sortOrder = $("<input type=\"text\" style=\"display:none\" name=\""+ $container.attr('id') +".SortOrder\" />")
			.appendTo($container);
			$sortColumn = $("<input type=\"text\" style=\"display:none\" name=\""+ $container.attr('id') +".SortColumn\" />")
			.appendTo($container).change(function(){
				historySort = true;
				var index = grid.getColumnIndex($sortColumn.val());
				grid.setSortColumns([{
					sortAsc : $sortOrder.val() != 'asc',
					columnId : $sortColumn.val()
				}]);
				$container.find('.slick-header-columns').children().eq(index)
				.find('.slick-sort-indicator').click();
			});
			// Export excel ----------------------
			$(function(){
				if(!$this.canModified){
				//$('#export-submit').remove();
				}
				$('#export-submit').click(function(){
					var length = dataView.getLength();
					var list = [];
					for(var i = 0; i < length ; i++){
						list.push(dataView.getItem(i).id);
					}
					$('#export-item-list').val(list.join(',')).closest('form').submit();
				});
                $('#export-submitplus').click(function(){
					var length = dataView.getLength();
					var list = [];
					for(var i = 0; i < length ; i++){
						list.push(dataView.getItem(i).id);
					}
					$('#export-item-listplus').val(list.join(',')).closest('form').submit();
				}); 
                addActivities = function(){
                    var length = dataView.getLength();
                    ControlGrid.gotoCell(length, 1, true);
                }               
			});

			this.bindHeaderRow();
			return grid;
		}
	});

})(jQuery);