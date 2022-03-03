
/**
 * @author Tien Van Vo<vantienvnn@gmail.com>
 *
 * Green CMS Tooltip Module
 *
 * Example
 *
 * {{{
 *   $('selector').tooltip(array('content' =>'text tip')) to create a tooltip for selector
 *   $('selector').tooltip(method , arg1, arg2...) trigger a tooltip method
 *   $('selector').tooltip('option',key) to get tooltip option
 *   $('selector').tooltip('option',key , value) to set tooltip option
 *   ...
 *   $('selector').tooltip('open') to open tooltip
 *   $('selector').tooltip('close') to close tooltip
 *   $('selector').tooltip('destroy') to destroy tooltip
 * }}}
 */
;
(function($){

    var template = '<div class="tooltip">';
    template += '      <div class="tooltip-wrapper">';
    template += '              <div class="tooltip-corner">';
    template += '                        <div><div></div></div>';
    template += '              </div>';
    template += '              <div class="tooltip-left">';
    template += '                        <div class="tooltip-right">';
    template += '                            <div class="tooltip-container"></div>';
    template += '                        </div>';
    template += '              </div>';
    template += '              <div class="tooltip-corner tooltip-reverse">';
    template += '                        <div><div></div></div>';
    template += '              </div>';
    template += '              <div class="tooltip-arrow">=></div>';
    template += '      </div>';
    template += '  </div>';


    var Tooltip = function(options){
        this.options = $.extend({
            type : ['top','left'],
            cssClass : '',
            disabled : false,
            distance : 10,
            time : 250,
            position : {
                top : 0,
                left : 0
            },
            width : '',
            height : '',
            minHeight : 20,
            maxHeight : 100,
            minWidth : 90,
            maxWidth : 300,
            delay : 500,
            hold : 10,
            destroy : function(ui){

            },
            open : function(ui){

            },
            content : '',
            target : null
        } , options || {});

        var $widget = $(template).css({
            opacity : 0
        }).appendTo('body'),
        $container = $widget.find('.tooltip-container'),
        delayTimer = null, beingShown = false, shown = false;

        this._setOption = function(key , value){
            this.options[ key ] = value;
            switch(key){
                case 'position': {
                    $widget.css(value);
                    break;
                }
                case 'cssClass': {
                    $widget.addClass(value);
                    break;
                }
                case 'width':
                case 'minWidth':
                case 'maxWidth': {
                    $widget.css(key , value);
                    break;
                }
                case 'height':
                case 'minHeight':
                case 'maxHeight': {
                    $container.css(key , value);
                    break;
                }
                case 'content': {
                    value = $.isFunction(value) ? value.call(this,this.options.target) : (value || '');
                    $container.html(value);
                    break;
                }
            }
        };

        $.extend(this, {
            open: function(){
                var self = this;
                this.clear();
                delayTimer = setTimeout(function(){
                    if (!$container.html() || self.options.disabled || beingShown || shown) {
                        return false;
                    } else {
                        self.options.open(self);
                        beingShown = true;
                        if(self.options.target){
                            $(self.options.target).attr("aria-describedby", $widget.attr("id"));
                        }
                        $widget.attr("aria-hidden", "false").show().stop().animate({
                            top: '-=' + self.options.distance + 'px',
                            opacity: 1
                        }, self.options.time, 'swing', function() {
                            beingShown = false;
                            shown = true;
                        });
                    }
                },this.options.hold);
                return true;
            },
            close: function(){
                var self = this;
                this.clear();
                delayTimer = setTimeout(function () {
                    if(self.options.target){
                        $(self.options.target).removeAttr("aria-describedby");
                    }
                    $widget.attr("aria-hidden", "true").stop().animate({
                        top: '-=' + self.options.distance + 'px',
                        opacity: 0
                    }, self.options.time, 'swing', function () {
                        shown = false;
                        $widget.hide();
                    });
                }, this.options.delay);
                return true;
            },
            enable : function(){
                this.option('disabled' , false);
            },
            disable : function(){
                this.option('disabled' , true);
            },
            widget : function(){
                return $widget;
            },
            container : function(){
                return $container;
            },
            destroy : function(){
                clearTimeout(delayTimer);
                $widget.stop().remove();
                this.options.destroy(this);
            },
            option: function(key , value){
                var options = key,
                self = this;
                if  (typeof key === "string" ) {
                    if ( value === undefined ) {
                        return this.options[ key ];
                    }
                    options = {};
                    options[ key ] = value;
                }
                $.each( options, function( key, value ) {
                    self._setOption( key, value );
                });
                return self;
            },
            clear: function(){
                clearTimeout(delayTimer);
            }
        });
        this.option(this.options);
    };

    $.fn.tooltip = function(fn, option){
        var expando = 'tooltip-instance',$elements = $(this),reself = true,result,
        args = $.makeArray(arguments);
        args.shift();
        $elements.each(function () {

            var $element = $(this);
            var instance = $element.data(expando);

            if(instance){
                if(typeof fn === "string"){
                    if($.isFunction(instance[fn])){
                        result = instance[fn].apply(instance,args);
                    }else{
                        result = instance[fn];
                    }
                    return (reself = false);
                }
                instance.destroy();
            }else{
                if(typeof fn === "string"){
                    return true;
                }
            }

            instance = new Tooltip($.extend({
                openEvent : 'mouseenter.tooltip focus.tooltip',
                closeEvent : 'mouseleave.tooltip blur.tooltip',
                target  : this
            },fn));
            $element.data(expando, instance);

            var openHandler = function(){
                var offset = $element.offset(),position = {};
                if(instance.options.type[0] == 'top'){
                    instance.widget().removeClass('tooltip-bottom');
                    position.top = (offset.top - instance.widget().height()) + (instance.options.distance);
                }else{
                    instance.widget().addClass('tooltip-bottom');
                    position.top = (offset.top) + (instance.options.distance + 20);
                }
                if(instance.options.type[1] == 'left'){
                    position.left = offset.left + 10;
                }else{
                    position.left = offset.left - instance.widget().width() + 10;
                }
                instance.option({
                    position : position
                });
                instance.open();
                return false;
            }
            var closeHandler = function(){
                instance.close();
                return false;
            }
            $element.bind(instance.options.openEvent,openHandler).bind(instance.options.closeEvent ,closeHandler);
            instance.options.destroy = function(){
                $element.unbind(instance.options.openEvent,openHandler).unbind(instance.options.closeEvent ,closeHandler);
            }
            return true;
        });
        return reself ? $elements : result;
    }


    $.widget( "ui.combobox", {
        _create: function() {
            var input,
            self = this,
            select = this.element.hide(),
            selected = select.children( ":selected" ),
            value = selected.val() ? selected.text() : "",
            wrapper = this.wrapper = $( "<span>" )
            .addClass( "ui-combobox" )
            .insertAfter( select );

            input = $( "<input>" )
            .appendTo( $('<span>').appendTo(wrapper) )
            .val( value )
            .keypress(function(e){
                if((e.keyCode ? e.keyCode : e.which) == 13){
                    return false;
                }
            })
            .addClass( "ui-state-default ui-combobox-input" )
            .autocomplete({
                delay: 0,
                minLength: 0,
                open: function(event, ui) {
                    ui = $(input.autocomplete('widget'));
                    ui.css({
                        width : wrapper.width() - 2,
                        left : wrapper.offset().left
                    });
                    $( 'a' , wrapper).addClass('hover');
                },
                close : function(){
                    $( 'a' , wrapper).removeClass('hover');
                },
                source: function( request, response ) {
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                    response( select.children( "option" ).map(function() {
                        var text = $( this ).text();
                        if ( this.value && ( !request.term || matcher.test(text) ) ){
                            return {
                                label: text.replace(
                                    new RegExp(
                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                        $.ui.autocomplete.escapeRegex(request.term) +
                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>" ),
                                value: text,
                                option: this
                            };

                        }
                    }) );
                },
                select: function( event, ui ) {
                    var onlySale = $('#employee-place').data('onlysale');
                    if(onlySale){
                        updateContact(onlySale, ui.item.option.value);
                    }
                    ui.item.option.selected = true;
                    self._trigger( "selected", event, {
                        item: ui.item.option
                    });
                },
                search : function(event,ui){
                    if(!$(this).val().length){
                        input.autocomplete('option', 'change').call(input , event , {});
                    }
                },
                change: function( event, ui ) {
                    if ( !ui.item ) {
                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                        valid = false;
                        select.children( "option" ).each(function() {
                            if ( $( this ).text().match( matcher ) ) {
                                this.selected = valid = true;
                                return false;
                            }
                        });
                        if ( !valid ) {
                            // remove invalid value, as it didn't match anything
                            $( this ).val( "" );
                            select.val( "" );
                            (input.data( "autocomplete" ) || {}).term = "";
                            return false;
                        }
                    }
                    select.change();
                }
            })
            .addClass( "ui-widget ui-widget-content ui-corner-left" );

            input.data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.label + "</a>" )
                .appendTo( ul );
            };

            $( "<a>ok</a>" )
            .attr( "tabIndex", -1 )
            .attr( "title", "Show All Items" )
            .appendTo( wrapper )
            .click(function() {
                // close if already visible
                if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                    input.autocomplete( "close" );
                    return;
                }
                // work around a bug (likely same cause as #5265)
                $( this ).blur();

                // pass empty string as value to search for, displaying all results
                input.autocomplete( "search", "" );
                input.focus();
                var windowH = $(window).height();
                var uiTop = $('.ui-autocomplete').position().top;
                var uiHeight = $('.ui-autocomplete').height();
                var divHeight = input.parent().parent().parent().parent().height();
                if(windowH < uiTop + uiHeight){
                    $('.ui-autocomplete').css('top', uiTop - uiHeight - divHeight);
                }
            });
        },

        destroy: function() {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );
        }
    });
    function updateContact(company_id, customerId){
        setTimeout(function(){
            $.ajax({
                url: '/sale_leads/update_contact/',
                async: false,
                type : 'POST',
                dataType : 'json',
                data: {
                    sale_customer_id: customerId,
                    company_id: company_id
                },
                success:function(data) {
                    if(data){
                        $('#SaleLeadSaleCustomerContactId').html(data);
                    }
                }
            });
        }, 100);
    }
    $(function(){
        $('select.ui-combobox').combobox();
    });



    $.fn.getSelection = function(){
        var el = this[0],start = 0, end = 0, normalizedValue, range,
        textInputRange, len, endRange;
        if (el && typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
            start = el.selectionStart;
            end = el.selectionEnd;
        } else if(el) {
            range = document.selection.createRange();

            if (range && range.parentElement() == el) {
                len = el.value.length;
                normalizedValue = el.value.replace(/\r\n/g, "\n");
                textInputRange = el.createTextRange();
                textInputRange.moveToBookmark(range.getBookmark());
                endRange = el.createTextRange();
                endRange.collapse(false);
                if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                    start = end = len;
                } else {
                    start = -textInputRange.moveStart("character", -len);
                    start += normalizedValue.slice(0, start).split("\n").length - 1;
                    if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                        end = len;
                    } else {
                        end = -textInputRange.moveEnd("character", -len);
                        end += normalizedValue.slice(0, end).split("\n").length - 1;
                    }
                }
            }
        }
        return {
            start: start,
            end: end
        };
    };
    $.fn.replaceSelection = function(text){
        var element = this[0];
        if(!element){
            return text;
        }
        var p = $.fn.getSelection.call([element]);

        return element.value.substr(0, p.start) + text
        + element.value.substr(p.end, element.value.length);
    };
    var a = new Date().getTime();
    $.debugStart = function(){
        a = new Date().getTime();
    };
    $.debugEnd = function(){
        var r = (new Date().getTime() - a);
        $.debugStart();
        return r;
    };

})(jQuery);
