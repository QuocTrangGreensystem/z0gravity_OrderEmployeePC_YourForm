(function($){
    $.extend(true, window, {
        "Slick": {
            "DataExporter": DataExporter
        }
    });

    function DataExporter(url, beforeSubmit, afterSubmit){
        var grid, form;
        var self = this;

        function init(_grid){
            grid = _grid;
            //
            if( !$('#slick-data-exporter').length ){
                form = $('<form id="slick-data-exporter" style="display: none" method="post"><input type="hidden" name="data[data]"></form>').appendTo('body');
                form.prop({
                    action: url,
                    method: 'post'
                });
            } else {
                form = $('#slick-data-exporter');
            }
        }

        function destroy(){
        }

        function parse(json){
            var length = grid.getDataLength(),
                columns = grid.getColumns(),
                data = [];
            for(var i = 0; i < length; i++){
                var item = grid.getDataItem(i),
                    cols = [];
                // column
                for(var j = 0; j < columns.length; j++){
                    var column = columns[j],
                        field = column.field,
                        value;
                    if( typeof column.ignoreExport != 'undefined' && column.ignoreExport ){
                        continue;
                    }
                    if( typeof column.exportFormatter == 'function' ){
                        value = column.exportFormatter.call(grid, item[field], item);
                    } else if ( typeof column.isSelected != 'undefined' && column.isSelected ){
                        var f = grid.getFormatter(i, column);
                        value = $.trim(
                            $('<div />').append(
                                f(i, j, item[field], column, item)
                            ).find(':selected').html()
                        );
                    } else if( typeof column.isImage != 'undefined' && column.isImage ){
                        var f = grid.getFormatter(i, column);
                        var img = $.trim(
                            $('<div />').append(
                                f(i, j, item[field], column, item)
                            ).find('.change-image').data('image')
                        );
                        value = 'image:' + img;
                    } else {
                        var f = grid.getFormatter(i, column);
                        value = $.trim(
                            $('<div />').append(
                                f(i, j, item[field], column, item)
                            ).text()
                        );
                    }
                    // remove j.H, MD,... in projects.
                    if(typeof controller != 'undefined' && controller == 'projects'){
                        value = value.replace(/ J.H/g, "").replace(/ M.D/g, "").replace(/ €/g, "");
                    }
                    // check markup
                    if( typeof column.type != 'undefined' ){
                        cols.push({
                            type: column.type,
                            value: value,
                            define: column
                        });
                    } else {
                        // append to cols
                        cols.push(value);
                    }
                }
                // append to data
                data.push(cols);
            }
            var header = [];
            for( var i = 0; i < columns.length; i++){
                var column = columns[i];
                if( typeof column.ignoreExport != 'undefined' && column.ignoreExport ){
                    continue;
                }
				var headerName = column.nameExport ? column.nameExport : column.name;
                header.push(headerName);
            }
            var ret = {
                header: header,
                body: data
            };
            if( json ){
                return JSON.stringify(ret);
            }
            return ret;
        }

        function submit(){
            var data = parse(true);
            form.find('input').val(data);
            if( typeof beforeSubmit == 'function' ){
                beforeSubmit.call(form);
            }
            form.submit();
            if( typeof afterSubmit == 'function' ){
                afterSubmit.call(form);
            }
        }

        $.extend(this, {
            "init": init,
            "destroy": destroy,
            'parse': parse,
            'submit': submit
        });
    }
})(jQuery);
