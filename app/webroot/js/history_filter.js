var HistoryFilter = {
    here : '',
    url : '',
    timeoutID : null,
    delay : 500,
    stask : {},
    data : [],
    auto: true,
    afterLoad: null
};
// if( typeof AutoHistory != 'undefined' ){
//     HistoryFilter.auto = AutoHistory;
// }

(function($){

    var $this = HistoryFilter;
    $.ajaxPrefilter(function( options ) {
        var callback = $.noop;
        if($.isFunction(options.complete)){
            callback = options.complete;
        }
        options.complete = function(e, xhr, settings){
            $this.parse();
            callback.call(this,e,xhr,settings);
        }
    });
    $.extend($this,{
        parse : function(){
            $.each($this.data,function(key, value){
                if($this.setVal(key,value)){
                    delete $this.data[key];
                }
            });
        },
        init : function(){
            var save = {
                path : $this.here
            };
            $.ajax({
                type:'POST',
                url:$this.url,
                data:{
                    data: save
                },
                cache: false,
                success:function(content){
                    $this.data = $.parseJSON(content);
                    $this.parse();
                    if( typeof $this.afterLoad == 'function' ){
                        $this.afterLoad.call();
                    }
                    try{
                        history_reset();
                    }catch(e){

                    }
                }
            });
        },
        serialize:function (mixed_value) {
            var _utf8Size = function (str) {
                var size = 0,
                i = 0,
                l = str.length,
                code = '';
                for (i = 0; i < l; i++) {
                    code = str.charCodeAt(i);
                    if (code < 0x0080) {
                        size += 1;
                    } else if (code < 0x0800) {
                        size += 2;
                    } else {
                        size += 3;
                    }
                }
                return size;
            };
            var _getType = function (inp) {
                var type = typeof inp,
                match;
                var key;
                if (type === 'object' && !inp) {
                    return 'null';
                }
                if (type === "object") {
                    if (!inp.constructor) {
                        return 'object';
                    }
                    var cons = inp.constructor.toString();
                    match = cons.match(/(\w+)\(/);
                    if (match) {
                        cons = match[1].toLowerCase();
                    }
                    var types = ["boolean", "number", "string", "array"];
                    for (key in types) {
                        if (cons == types[key]) {
                            type = types[key];
                            break;
                        }
                    }
                }
                return type;
            };
            var type = _getType(mixed_value);
            var val, ktype = '';
            switch (type) {
                case "function":
                    val = "";
                    break;
                case "boolean":
                    val = "b:" + (mixed_value ? "1" : "0");
                    break;
                case "number":
                    val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
                    break;
                case "string":
                    val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
                    break;
                case "array":    case "object":
                    val = "a";
                    var count = 0;
                    var vals = "";
                    var okey;
                    var key;
                    for (key in mixed_value) {
                        if (mixed_value.hasOwnProperty(key)) {
                            ktype = _getType(mixed_value[key]);
                            if (ktype === "function") {
                                continue;
                            }

                            okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                            vals += $this.serialize(okey) + $this.serialize(mixed_value[key]);
                            count++;
                        }
                    }
                    val += ":" + count + ":{" + vals + "}";
                    break;
                case "undefined":
                // Fall-through
                default:
                    // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
                    val = "N";
                    break;
            }
            if (type !== "object" && type !== "array") {
                val += ";";
            }
            return val;
        },
        getVal : function($element , name , type){
            var result = null;
            if($element.is(":"+type) && name){
                result = [];
                $("input[type="+type+"][name='"+name+"']:checked").each(function(){
                    result.push($(this).val());
                });
            }
            return result;
        },
        setVal : function(name , value){
            var $data = $("[name='"+name+"']").each(function(){
                var $element = $(this);
                if($element.is(':checkbox') || $element.is(':radio')){
                    if(!$.isArray(value)){
                        value = [value];
                    }
                    $element.prop('checked', $.inArray($element.val(), value) != -1);
                }else{
                    $element.val(value);
                    $element.keypress();
                }
                $element.data('__auto_trigger' , true);
                $element.change();
            });
            return $data.length > 0;
        },
        send: function(){
            //if($.i)
            if( !$this.here )return;
            var save = {
                path : $this.here,
                params : $this.serialize($this.stask)
            };
            $this.stask = {};
            $.ajax({
                type:'POST',
                url:$this.url,
                data:{
                    data: save
                },
                cache: false,
                success:function(){
                    $this.timeoutID = null;
                    try{
                        history_reset();
                    }catch(e){

                    }
                }
            });
        },
        pushStask : function(name,val){
            clearTimeout($this.timeoutID);
            $this.stask[name] = val;
            $this.timeoutID = setTimeout(function(){
                $this.send();
            }, $this.delay);
        },
        bind: function(e){
            $(e).on('change', function(){
                var $element = $(this);
                if($element.data('__auto_trigger')){
                    $element.data('__auto_trigger', false);
                    return;
                }
                var name = $element.attr("name");
                var val = ($this.getVal($element,name,"radio") || $this.getVal($element,name,"checkbox") || $element.val());
                $this.pushStask(name ,val);
            });
        }
    });

    $(function(){
        setTimeout(function(){
            if( $this.auto ){
                $this.init();
            }
            $(':input[name][rel!="no-history"][type!="file"]').on('change', function(){
                var $element = $(this);
                if($element.hasClass('not_save_history')) return false;
                if($element.data('__auto_trigger')){
                    $element.data('__auto_trigger', false);
                    return;
                }
                var name = $element.attr("name");
                var val = ($this.getVal($element,name,"radio") || $this.getVal($element,name,"checkbox") || $element.val());
                $this.pushStask(name ,val);
            });
        }, 500);
    });

    $(window).bind('beforeunload',function() {
        if($this.timeoutID){
            alert('Data of history filter unsaved, please wait in some second. Press ok to leave.');
        }
    });


})(jQuery);
