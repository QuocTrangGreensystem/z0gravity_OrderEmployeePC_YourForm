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
				if(typeof selects[field] != 'undefined')
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
			var gridSorter = function(arraySort) {
				var length=arraySort.length-1;
				dataView.sort(function (dataRow1, dataRow2) {
					for(j=0;j<length;j++)
					{
						/*var column = grid.getColumns()[grid.getColumnIndex(arraySort[j].columnId)];
						var type = column.datatype;
						if(comparerMs(dataRow1[arraySort[j].columnId],dataRow2[arraySort[j].columnId],arraySort[j].sortAsc,type)!=0)
							return comparerMs(dataRow1[arraySort[j].columnId],dataRow2[arraySort[j].columnId],arraySort[j].sortAsc,type);
						else
						{
							var column = grid.getColumns()[grid.getColumnIndex(arraySort[j+1].columnId)];
							var type = column.datatype;
							comparerMs(dataRow1[arraySort[j+1].columnId],dataRow2[arraySort[j+1].columnId],arraySort[j+1].sortAsc,type);
						}*/
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
            $('#onSort').click(function(){
				var arraySort=JSON.parse(jQuery('#strMultiSort').val());
				var arraySortTemp=arraySort;
				var obj={'columnId':'no.','sortAsc':1};	
				arraySort.push(obj);	
				gridSorter(arraySort);
				grid.invalidate();
				grid.render();
				grid.setSortColumns(arraySortTemp);
				showHideIt();
				sortCustomList();	
			});
			//END ADD--------