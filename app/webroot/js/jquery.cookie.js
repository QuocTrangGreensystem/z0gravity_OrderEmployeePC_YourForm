/*!
 * jQuery Cookie Plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function($) {
    $.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = $.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ? function(s) { return s; } : decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };
    /*
        Enhancement in $.cookie
        + each namespace is a cookie with name "z0Cookie.[namespace]"
    */
   /**
   * Decimal adjustment of a number.
   *
   * @param {String}  type  The type of adjustment.
   * @param {Number}  value The number.
   * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
   * @returns {Number} The adjusted value.
   */
  function decimalAdjust(type, value, exp) {
    // If the exp is undefined or zero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // If the value is not a number or the exp is not an integer...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
    $.z0Cookie = {
        cookie: {},
        //list: ['default'],
        namespace: 'default',
        revert: function(){
            this.namespace = 'default';
        },
        init: function(){
            // var me = this;
            // var list = $.cookie('z0List');
            // if( list )this.list = JSON.parse(list);
            // $.each(this.list, function(i, namespace){
            //     var cookie = $.cookie('z0.' + namespace);
            //     if( cookie ){
            //         me.cookie[namespace] = JSON.parse(cookie);
            //     } else {
            //         me.cookie[namespace] = {};
            //     }
            //     if( namespace != me.namespace){
            //         me.list.push(namespace);
            //     }
            // });
        },
        addNamespace: function(name, set){
            if( typeof this.cookie[name] == 'undefined' ){
                this.cookie[name] = {};
            }
            if( set )this.setNamespace(name);
            return this;
        },
        setNamespace: function(name){
            if( typeof this.cookie[name] == 'undefined' )return false;
            this.namespace = name;
            return true;
        },
        get: function(key, _default){
            if( typeof this.cookie[this.namespace] == 'undefined' ){
                //get cookie
                var c = $.cookie('z0.' + this.namespace);
                if( c ){
                    this.cookie[this.namespace] = JSON.parse(c);
                } else {
                    this.cookie[this.namespace] = {};
                }
            }
            return (typeof this.cookie[this.namespace][key] != 'undefined' ? this.cookie[this.namespace][key] : _default);
        },
        set: function(key, value, options){
            if( typeof this.cookie[this.namespace] == 'undefined' ){
                //get cookie
                var c = $.cookie('z0.' + this.namespace);
                if( c ){
                    this.cookie[this.namespace] = JSON.parse(c);
                } else {
                    this.cookie[this.namespace] = {};
                }
            }
            this.cookie[this.namespace][key] = value;
            //store cookie
            this.save(options);
        },
        //save the current namespace cookie
        save: function(options){
            var def = {
                expires: 30,
                path: '/'
            };
            if( typeof options == 'object' )options = $.extend({}, def, options);
            $.cookie('z0.' + this.namespace, JSON.stringify(this.cookie[this.namespace]), options);
        },
        destroy: function(key){
            if( typeof this.cookie[this.namespace][key] == 'undefined' )return;
            delete this.cookie[this.namespace][key];
            //store cookie
            this.save();
        },
        removeNamespace: function(name){
            if( name == 'default' || typeof this.cookie[name] == 'undefined' )return;
            delete this.cookie[name];
            //delete cookie
            $.cookie('z0.' + name, '', {expires: -1});
            //revert to namespace 'default'
            this.revert();
        }
    };
    //call the init
    $.z0Cookie.init();
})(jQuery);
