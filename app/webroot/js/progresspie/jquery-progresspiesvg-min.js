"use strict";function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function e(t){return typeof t}:function e(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(e)}
/**
 * @license 
 * Copyright (c) 2018, Immo Schulz-Gerlach, www.isg-software.de 
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are 
 * permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT 
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED 
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
!function(pe){function a(e){var t=e.valueInput;if("object"!==_typeof(t))return"string"==typeof t?pe(t):null;if("function"==typeof t.val)return t;throw new Error("option 'valueInput' is an object, but does not have a 'val' method, i.e. it's obviously not a jQuery result object.")}var t={};pe.fn.setupProgressPie=function(r,n){var e=this;pe(this).each(function(){var e=pe(this).data(pe.fn.setupProgressPie.dataKey);if(n||"object"!==_typeof(e)){var t=pe.extend({},pe.fn.progressPie.defaults,{update:!0},r);pe(this).data(pe.fn.setupProgressPie.dataKey,t)}else pe.extend(e,r)});var t=pe.extend({},pe.fn.progressPie.defaults,r),o=a(t);if(null!==o){if("string"!=typeof t.valueInputEvents)throw new Error("'valueInputEvents' has to be a string (space-separated list of event names)!");o.on(t.valueInputEvents,function(){pe(e).progressPie()})}return this},pe.fn.setupProgressPie.dataKey="$.fn.setupProgressPie",pe.fn.progressPie=function(e){function d(e,t){if("number"==typeof e)return e;if(Array.isArray(e)&&0<e.length){if(1===e.length)return e[0];for(var r=t;r>=e.length;)r-=2;return e[r]}return 0}function U(e){return void 0===t[e]&&(t[e]=0),e+ ++t[e]}function Z(e){return.02*Math.PI*e}function n(e,t){var r,n=(0,eval)(e);if("function"==typeof n)return n(t);throw new Error("The value of the colorFunctionAttr attribute is NOT a function: "+e)}function r(e){var t,r=(0,eval)(o+"."+e);if("function"==typeof r||"object"===_typeof(r)&&"function"==typeof r.draw)return r;throw new Error(e+" is not the name of a function or object in namespace "+o+"!")}function W(e){var t;if("function"==typeof e||"object"===_typeof(e)&&"function"==typeof e.draw)t=e;else{if("string"!=typeof e)throw new Error("contentPlugin option must either be a function or an object with method named 'draw' or the name of such a function or object in the namespace "+o+"!");t=r(e)}return t}function G(e){return Array.isArray(e)?pe.map(e,W):[W(e)]}function L(e,t){return null===e?null:Array.isArray(e)?e[t]:0===t&&"object"===_typeof(e)?e:null}function ee(e,t){return.02*Math.PI*e*t}function te(e,t,r,n,o,i){var a=document.createElementNS(ue,"animate");for(var s in a.setAttribute("attributeName",t),a.setAttribute("attributeType",r),a.setAttribute("from",n),a.setAttribute("to",o),a.setAttribute("fill","freeze"),i)a.setAttribute(s,i[s]);e.appendChild(a)}function re(e,t,r){var n,o;if("number"==typeof t)n=t;else{if("object"!==_typeof(t))throw new Error("illegal option: 'strokeDashes' is neither number (count) nor object!");n=t.count,o=t.length}if(void 0===n)throw new Error("illegal option: 'strokeDashes' does not specify the 'count' property!");if(void 0===o)o=r/n/2;else if("string"==typeof o){var i="%"===(o=o.trim()).substring(o.length-1);o=Number.parseInt(o,10),i&&(o=r*o/100)}if(r<=o*n)throw new Error("Illegal options: strokeDashCount * strokeDashLength >= circumference, can't set stroke-dasharray!");var a=(r-o*n)/n,s="object"===_typeof(t)&&t.centered?1*o/2:0;"object"===_typeof(t)&&t.inverted?(e.style.strokeDasharray=a+"px, "+o+"px",e.style.strokeDashoffset=a+s+"px"):(e.style.strokeDasharray=o+"px, "+a+"px",0!==s&&(e.style.strokeDashoffset=s+"px"))}function ne(e,t){if("string"==typeof t){var r=document.createElementNS(ue,"title");pe(r).text(t),e.appendChild(r)}}function Y(e,t,r,n,o,i,a,s,u,l,p,d,c,f,g,v,h,m,y,b){"number"==typeof n&&(n=Math.min(n,r)),"number"==typeof u&&(u=Math.min(u,r));var A=-1,S,C;if("number"==typeof n&&"number"==typeof u&&0<n&&0<u&&n!==u&&s&&(p===le.RingAlign.CENTER||p===le.RingAlign.INNER)){var P=Math.max(u,n),E=Math.min(u,n);A=p===le.RingAlign.CENTER?r-P/2:r-P+E/2}var w=!1;if("number"==typeof n){(C=document.createElementNS(ue,"circle")).setAttribute("cx",0),C.setAttribute("cy",0),0<A&&n<u?S=A:(S=r-n/2,s||p!==le.RingAlign.INNER||(S-=u)),C.setAttribute("r",S),C.setAttribute("transform","rotate(-90)"),i&&re(C,i,2*Math.PI*S);var k=(w="string"==typeof o)?o:v;"string"==typeof k&&(C.style.stroke=k),"string"==typeof a&&(C.style.fill=a),C.style.strokeWidth=n,C.setAttribute("class",d),ne(C,m),e.appendChild(C)}var M=u||(s||"number"!=typeof n?r:r-n);if(0<A&&u<n?S=A:(S=r-M/2,s||"number"!=typeof n||p!==le.RingAlign.OUTER||(S-=n)),100!==f||y||"string"!=typeof v){if(0<f&&f<100||(y||void 0===v)&&(0===f||100===f)){var R=document.createElementNS(ue,"path"),N=f;if(y){var x=f-g,_=ee(S,x),I,O,D;D=x<0?(N=g,O="0px",-_+"px"):(O=_+"px","0px");var j=ee(S,N);R.setAttribute("stroke-dasharray",j+"px "+j+"px"),R.setAttribute("stroke-dashoffset",O),te(R,"stroke-dashoffset","CSS",O,D,y),l&&0===f&&te(R,"stroke-linecap","CSS","round","butt",y),h&&h!==v&&(te(R,"stroke","CSS",h,v,y),!w&&C&&(C.style.stroke=h,te(C,"stroke","CSS",h,v,y)))}var B=Z(N),F,T,K,V,z,U="M0,"+-S;if(U+=" A"+S+","+S+" 0 "+(50<N?"1":"0")+","+"1"+" "+(100===N?-1e-5:Math.sin(B)*S)+","+Math.cos(B-Math.PI)*S,R.setAttribute("d",U),R.style.fill="none","string"==typeof v&&(R.style.stroke=v),R.style.strokeWidth=M,R.style.strokeLinecap=l&&0<f?"round":"butt",b){var W="progresspie-rotation-style",G="progresspie-rotate",L="@keyframes "+G+" {100% {transform: rotate(360deg);}}";if(!pe("#"+W).length){var Y=pe("head");if(Y.length){var Q=document.createElement("style");Q.id=W,pe(Q).text(L),Y.get(0).appendChild(Q)}else{var $=document.createElementNS(ue,"style");$.id=W,pe($).text(L),t.appendChild($)}}var q=!1===b.clockwise,H="string"==typeof b?b:"string"==typeof b.duration?b.duration:"1s",J="string"==typeof b.timing?b.timing:"linear";R.style.animation=G+" "+H+" "+J+(q?" reverse":"")+" infinite"}R.setAttribute("class",c),ne(R,m),e.appendChild(R)}}else{var X=document.createElementNS(ue,"circle");X.setAttribute("cx",0),X.setAttribute("cy",0),X.setAttribute("r",S),X.style.stroke=v,X.style.strokeWidth=M,X.style.fill="none",X.setAttribute("class",c),ne(X,m),e.appendChild(X)}}function s(e,t){var r,n=a(t);if(null!==n){if(r=n.val(),void 0!==t.valueData||void 0!==t.valueAttr||void 0!==t.valueSelector)throw new Error("options 'valueInput', 'valueData', 'valueAttr' and 'valueSelector' are mutually exclusive, i.e. at least three must be undefined!")}else if("string"==typeof t.valueData){if(r=e.data(t.valueData),void 0!==t.valueAttr||void 0!==t.valueSelector)throw new Error("options 'valueData', 'valueAttr' and 'valueSelector' are mutually exclusive, i.e. at least two must be undefined!")}else{if(void 0!==t.valueData)throw new Error("option 'valueData' is not of type 'string'!");if("string"==typeof t.valueAttr){if(r=e.attr(t.valueAttr),void 0!==t.valueSelector)throw new Error("options 'valueAttr' and 'valueSelector' are mutually exclusive, i.e. at least one must be undefined!")}else{if(void 0!==t.valueAttr)throw new Error("option 'valueAttr' is not of type 'string'!");void 0!==t.valueSelector&&(r=pe(t.valueSelector,e).text())}}return void 0===r&&(r=e.text()),r}function u(e,t){return Math.max(0,Math.min(100,t.valueAdapter(e)))}function Q(e,t){var r=t.mode,n=t.color;if(r===le.Mode.CSS)n=void 0;else{var o=_typeof(n);if("undefined"!==o&&"string"!==o&&"function"!==o)throw new Error("option 'color' has to be either a function or a string, but is of type '"+o+"'!");"function"===o?r=i.USER_COLOR_FUNC:("undefined"===o&&"string"==typeof t.colorAttr&&(n=e.attr(t.colorAttr)),"string"==typeof n?r=i.USER_COLOR_CONST:"string"==typeof t.colorFunctionAttr&&"string"==typeof(n=e.attr(t.colorFunctionAttr))&&(r=i.DATA_ATTR_FUNC))}return{mode:r,color:n}}function $(e,t,r){return e===i.CSS?void 0:e===i.MASK?i.MASK.color:e===i.IMASK?i.IMASK.color:e===i.GREY?i.GREY.color:e===i.GREEN?le.colorByPercent(100):e===i.RED?le.colorByPercent(0):e===i.COLOR||void 0===t?le.colorByPercent(r):e===i.USER_COLOR_CONST?t:e===i.USER_COLOR_FUNC?t(r):e===i.DATA_ATTR_FUNC?n(t,r):"black"}function q(e,t,r){return e===i.CSS?void 0:"string"==typeof t.backgroundColor?t.backgroundColor:"function"==typeof t.backgroundColor?t.backgroundColor(r):e===i.IMASK?i.MASK.color:"none"}function H(e,t){return void 0===e.ringWidth||t&&t.fullSize}function J(e,t,r,n,o,i){var a=document.createElementNS(ue,"rect");e.appendChild(a);var s=t+d(r,3),u=t+d(r,0),l=s+t+d(r,1),p=u+t+d(r,2);"number"==typeof i&&"none"!==n&&(a.setAttribute("stroke-width",i),l-=i,p-=i,s-=i/2,u-=i/2),a.setAttribute("x","-"+s),a.setAttribute("y","-"+u),a.setAttribute("width",l),a.setAttribute("height",p),a.setAttribute("stroke",n),a.setAttribute("fill",o)}function X(e,t){var r=document.createElementNS(ue,"svg"),n=e+t.getPadding(3)+t.getMargin(3),o=e+t.getPadding(0)+t.getMargin(0),i=n+e+t.getPadding(1)+t.getMargin(1),a=o+e+t.getPadding(2)+t.getMargin(2),s=i,u=a;return"number"==typeof t.scale&&(s*=t.scale,u*=t.scale),r.setAttribute("width",Math.ceil(s)),r.setAttribute("height",Math.ceil(u)),r.setAttribute("viewBox","-"+n+" -"+o+" "+i+" "+a),r}function oe(e,t,r){var n={};if(n.raw=s(e,t),"function"==typeof t.optionsByRawValue){var o=t.optionsByRawValue(n.raw);null!=o&&(pe.extend(t,o),n.raw=s(e,t))}n.p=u(n.raw,t);var i=0===r?le.prevValueDataName:le.prevInnerValueDataName;if(1<r&&(i+=r),n.prevP=e.data(i),n.isInitialValue=void 0===n.prevP,e.data(i,n.p),"number"!=typeof n.prevP&&(n.prevP=0),"function"==typeof t.optionsByPercent){var a=t.optionsByPercent(n.p);null!=a&&(pe.extend(t,a),n.raw=s(e,t),n.p=u(n.raw,t))}return n}var ie={getMargin:function e(t){return d(this.margin,t)},getPadding:function e(t){return d(this.padding,t)}},ae=pe.extend({},pe.fn.progressPie.defaults,e,ie),se=void 0===e,ue="http://www.w3.org/2000/svg",o="jQuery.fn.progressPie.contentPlugin",le=pe.fn.progressPie,i=pe.extend({USER_COLOR_CONST:{},USER_COLOR_FUNC:{},DATA_ATTR_FUNC:{}},le.Mode);return pe(this).each(function(){var e=pe(this),o=pe.extend({},ae);if(se){var t=pe(this).data(pe.fn.setupProgressPie.dataKey);"object"===_typeof(t)&&(o=pe.extend({},t,ie))}var r=pe("svg",e);if(!r.length||o.update){r.length&&o.update&&(r.remove(),o.separator="");var n=oe(e,o,0),i=Math.ceil("number"==typeof o.size?o.size:e.height());0===i&&(i=20);var a=(i*=o.sizeFactor)/2,s=a,u=Q(e,o),l=$(u.mode,u.color,n.p),p=q(u.mode,o,n.p),d;(!0===o.animateColor||void 0===o.animateColor&&!n.isInitialValue)&&(d=$(u.mode,u.color,n.prevP));var c=le.smilSupported()?!0===o.animate?le.defaultAnimationAttributes:"object"===_typeof(o.animate)?pe.extend({},le.defaultAnimationAttributes,o.animate):null:null,f=null,g=!1,v={isFullSize:function e(){return H(o,this)},isCssMode:function e(){return"string"!=typeof this.color},color:l,percentValue:n.p,rawValue:n.raw,pieOpts:o};if(o.contentPlugin){f=G(o.contentPlugin);for(var h=0;h<f.length;h++){var m=f[h],y=L(o.contentPluginOptions,h),b=v;null!==y&&"object"===_typeof(y)&&(b=pe.extend({},v,y)),"object"===_typeof(m)&&"function"==typeof m.hidesChartIfFullSize&&(g=g||o.mode!==le.Mode.MASK&&o.mode!==le.Mode.IMASK&&H(o,y)&&m.hidesChartIfFullSize(b))}}var A=X(a,o),S=document.createElementNS(ue,"defs");u.mode!==le.Mode.CSS&&(A.style.verticalAlign=o.verticalAlign),e.is(":empty")?e.append(A):o.prepend?e.prepend(A,o.separator):e.append(o.separator,A),ne(A,o.globalTitle);var C=null,P=A;if(!g){o.mode!==le.Mode.MASK&&o.mode!==le.Mode.IMASK||(P=document.createElementNS(ue,"mask"),S.appendChild(P),C=U("pie"),P.setAttribute("id",C),o.mode===le.Mode.IMASK&&J(P,a,o.padding,"none",p));var E=o.cssClassForegroundPie,w=o.cssClassBackgroundCircle;"object"===_typeof(o.inner)&&(E+=" "+o.cssClassOuter,w+=" "+o.cssClassOuter),Y(P,S,a,o.strokeWidth,o.strokeColor,o.strokeDashes,p,o.overlap,o.ringWidth,o.ringEndsRounded,o.ringAlign,w,E,n.p,n.prevP,l,d,o.title,c,o.rotation)}for(var k="number"==typeof o.ringWidth?o.ringWidth:"number"==typeof o.strokeWidth?o.strokeWidth:0,M=o.inner,R=0;"object"===_typeof(M);){R++,void 0===(M=pe.extend({},M)).valueAdapter&&(M.valueAdapter=le.defaults.valueAdapter),void 0===M.overlap&&(M.overlap=le.defaults.overlap),void 0===M.ringAlign&&(M.ringAlign=o.ringAlign);var N=oe(e,M,R),x=o.cssClassInner;1<R&&(x+=R),u=Q(e,M),a="number"==typeof M.size?M.size*o.sizeFactor/2:.6*a;var _=$(u.mode,u.color,N.p),I=null;(!0===M.animateColor||void 0===M.animateColor&&(!0===o.animateColor||void 0===o.animateColor&&N.isInitialValue))&&(I=$(u.mode,u.color,N.prevP)),!1!==M.animate&&le.smilSupported()?!0===M.animate&&null===c?c=le.defaultAnimationAttributes:"object"===_typeof(M.animate)&&(c=null===c?pe.extend({},le.defaultAnimationAttributes,M.animate):pe.extend({},c,M.animate)):c=null,g||Y(P,S,a,M.strokeWidth,M.strokeColor,M.strokeDashes,p,M.overlap,M.ringWidth,M.ringEndsRounded,M.ringAlign,o.cssClassBackgroundCircle+" "+x,o.cssClassForegroundPie+" "+x,N.p,N.prevP,_,I,M.title,c,M.rotation),k="number"==typeof M.ringWidth?M.ringWidth:0,M=M.inner}if(null!==f){var O=a;k<a&&(O-=k);for(var D=pe.extend({newSvgElement:function e(t){var r=document.createElementNS(ue,t);return T.appendChild(r),r},newSvgSubelement:function e(t,r){var n=document.createElementNS(ue,r);return t.appendChild(n),n},newDefElement:function e(t){var r=document.createElementNS(ue,t);return S.appendChild(r),r},createId:U,getBackgroundRadius:function e(t){var r=this.isFullSize()?this.totalRadius:this.radius,n;t||(r-="number"==typeof this.margin?this.margin:this.isFullSize()?this.pieOpts.defaultContentPluginBackgroundMarginFullSize:this.pieOpts.defaultContentPluginBackgroundMarginInsideRing);return r},addBackground:function e(t,r){var n="string"==typeof r;if(this.backgroundColor||n){var o=this.newSvgElement("circle");o.setAttribute("cx","0"),o.setAttribute("cy","0"),o.setAttribute("r",t),this.backgroundColor&&o.setAttribute("fill",this.backgroundColor),n&&o.setAttribute("class",r)}},addBackgroundRect:function e(t,r,n){J(T,s,o.padding,t,r,n)},getContentPlugin:W,radius:O,totalRadius:s,color:l,percentValue:n.p,rawValue:n.raw},v),j=!0,B=0;B<f.length;B++){var F=f[B],T=document.createElementNS(ue,"g"),K="function"==typeof F?F:F.draw,V=D,z=L(o.contentPluginOptions,B);null!==z&&"object"===_typeof(z)&&(V=pe.extend({},D,z)),K(V),"boolean"==typeof F.inBackground&&F.inBackground||"function"==typeof F.inBackground&&F.inBackground(V)?(pe(A).prepend(T),null!==C&&j&&(T.setAttribute("mask","url(#"+C+")"),j=!1)):pe(A).append(T)}if(null!==C&&j)throw new Error("MASK mode could not be applied since no content plug-in drew a background to be masked! You need do specify at least one content plug-in which draws into the background!")}S.hasChildNodes()&&pe(A).prepend(S)}}),this},pe.fn.progressPie.Mode={GREY:{color:"#888"},RED:{value:200},GREEN:{value:200},COLOR:{},CSS:{},MASK:{color:"white"},IMASK:{color:"black"}},pe.fn.progressPie.colorByPercent=function(e,t){var r=pe.fn.progressPie.Mode.GREEN.value,n=pe.fn.progressPie.Mode.RED.value,o=50<e?r:Math.floor(r*e/50),i,a=(e<50?n:Math.floor(n*(100-e)/50))+","+o+",0";return"number"==typeof t?"rgba("+a+","+t+")":"rgb("+a+")"},pe.fn.progressPie.smilSupported=function(){return void 0===pe.fn.progressPie.smilSupported.cache&&(pe.fn.progressPie.smilSupported.cache=/SVGAnimate/.test(document.createElementNS("http://www.w3.org/2000/svg","animate").toString())),pe.fn.progressPie.smilSupported.cache},pe.fn.progressPie.RingAlign={OUTER:{},CENTER:{},INNER:{}},pe.fn.progressPie.defaults={mode:pe.fn.progressPie.Mode.GREY,margin:0,padding:0,strokeWidth:2,overlap:!0,ringAlign:pe.fn.progressPie.RingAlign.OUTER,prepend:!0,separator:"&nbsp;",verticalAlign:"bottom",update:!1,valueAdapter:function e(t){return"string"==typeof t?parseFloat(t):"number"==typeof t?t:0},valueInputEvents:"change",ringEndsRounded:!1,sizeFactor:1,scale:1,defaultContentPluginBackgroundMarginFullSize:0,defaultContentPluginBackgroundMarginInsideRing:1,cssClassBackgroundCircle:"progresspie-background",cssClassForegroundPie:"progresspie-foreground",cssClassOuter:"progresspie-outer",cssClassInner:"progresspie-inner"},pe.fn.progressPie.defaultAnimationAttributes={dur:"1s",calcMode:"spline",keySplines:"0.23 1 0.32 1",keyTimes:"0;1"},pe.fn.progressPie.contentPlugin={},pe.fn.progressPie.prevValueDataName="_progresspieSVG_prevValue",pe.fn.progressPie.prevInnerValueDataName="_progresspieSVG_prevInnerValue"}(jQuery);
/* Add code by Z0G team
 * Huynh Le 2020-02-26
 * Add plugin for display progress text
 */

( function($) {
	var drawText = function(sValue, args) {
		var opts = $.extend({}, $.fn.progressPie.contentPlugin.progressDisplayDefaults, args);
		var text = opts.newSvgElement("text");
		text.setAttribute("x", 0);
		text.setAttribute("y", 0);
		var _id = opts.createId('text');
		text.setAttribute("id", _id);
		text.style.textAnchor = "middle";
		if( opts.singleLine){
			text.textContent = Math.round(args.rawValue);
		}
		if( opts.fontWeight ) text.style.fontWeight = opts.fontWeight;
		if( opts.fontFamily ) text.style.fontFamily = opts.fontFamily;
		var fsFactor = typeof opts.fontSize === 'number' ? opts.fontSize : 14;
		text.style.fontSize = fsFactor;
		text.setAttribute("dy", -(fsFactor/2));
		var _color = '#111';
		if (typeof opts.color === "string") { //not in CSS mode.
			_color = opts.color;
		}
		if (typeof opts.color === "function") { //not in CSS mode.
			_color = opts.color(args.rawValue);
		}
		text.setAttribute("fill", _color);
		text.setAttribute("class", opts.cssClass);
		if( $.isArray(opts.multiline) && !opts.singleLine){
			var _height = 0;
			$.each(opts.multiline, function(i, o){
				var line = opts.newSvgSubelement(text, "tspan");
				line.textContent = o.textContent.replace(/%s/g, Math.round(args.rawValue));
				line.setAttribute("dy", "1em");
				line.setAttribute("x", 0);
				var fsize = typeof o.fontSize === 'number' ? o.fontSize : fsFactor;
				_height -= fsize;
				line.style.fontSize = fsize;
				if( o.fontWeight ) line.style.fontWeight = o.fontWeight;
				if( o.fontFamily ) line.style.fontFamily = o.fontFamily;
				line.setAttribute("class", o.cssClass);
				var t_color = _color;
				if (typeof o.color === "string") { //not in CSS mode.
					t_color = o.color;
				}
				if (typeof o.color === "function") { //not in CSS mode.
					t_color = o.color(sValue);
				}
				line.setAttribute("fill", t_color);
			});
			text.setAttribute("y", _height/2);
		}
	};
	$.fn.progressPie.contentPlugin.progressDisplay = function(args) {
		drawText(Math.round(args.percentValue), args);
	};
	$.fn.progressPie.contentPlugin.rawValue = function(args) {
		drawText(args.rawValue, args);
	};
	$.fn.progressPie.contentPlugin.progressDisplayDefaults = {
		cssClass: "progresspie-progress",
		singleLine: false,
		fontFamily: 'Open Sans',
		fontSize: 36,
		fontWeight: 600,
		color: '#666',
		multiline: [
			{
				cssClass: "progresspie-progressText",
				fontSize: 11,
				textContent: 'PROGRESS',
				color: '#dddddd',
			},
			{
				cssClass: "progresspie-progressValue",
				fontSize: 28,
				textContent: '%s%' ,
				color: function(value){
					return $.fn.progressPie.colorByPercent(value);
				},
			}
		]
	};
	$.fn.progressPie.colorByPercent = function(percent, alpha) { 
		var _alpha = ( typeof alpha === "number" ? alpha : 1);
		var rgb = (percent <= 100) ? '110, 175, 121' : '233, 71, 84';
		return "rgba(" + rgb + "," + _alpha +")";
	};
} (jQuery));
/* END  Add plugin for display progress text */


/* Add code by Z0G team
 * Viet Nguyen 2020-07-25
 * Add plugin for display multi text
 */

( function($) {
	var drawText = function(sValue, innerValue, args) {
		var opts = $.extend({}, $.fn.progressPie.contentPlugin.progressDisplayDefaults, args);
		var text = opts.newSvgElement("text");
		text.setAttribute("x", 0);
		text.setAttribute("y", 0);
		var _id = opts.createId('text');
		text.setAttribute("id", _id);
		text.style.textAnchor = "middle";
		if( opts.singleLine){
			text.textContent = Math.round(args.rawValue);
		}
		if( opts.fontWeight ) text.style.fontWeight = opts.fontWeight;
		if( opts.fontFamily ) text.style.fontFamily = opts.fontFamily;
		var fsFactor = typeof opts.fontSize === 'number' ? opts.fontSize : 14;
		text.style.fontSize = fsFactor;
		text.setAttribute("dy", -(fsFactor/2));
		var _color = '#111';
		if (typeof opts.color === "string") { //not in CSS mode.
			_color = opts.color;
		}
		if (typeof opts.color === "function") { //not in CSS mode.
			_color = opts.color(args.rawValue);
		}
		text.setAttribute("fill", _color);
		text.setAttribute("class", opts.cssClass);
		if( $.isArray(opts.multiline) && !opts.singleLine){
			var _height = 0;
			$.each(opts.multiline, function(i, o){
				
				var line = opts.newSvgSubelement(text, "tspan");
				line.textContent = Math.round(o.textContent) + '%';
				line.setAttribute("dy", "1em");
				line.setAttribute("x", 0);
				var fsize = typeof o.fontSize === 'number' ? o.fontSize : fsFactor;
				_height -= fsize;
				line.style.fontSize = fsize;
				if( o.fontWeight ) line.style.fontWeight = o.fontWeight;
				if( o.fontFamily ) line.style.fontFamily = o.fontFamily;
				line.setAttribute("class", o.cssClass);
				var t_color = _color;
				if (typeof o.color === "string") { //not in CSS mode.
					t_color = o.color;
				}
				if (typeof o.color === "function") { //not in CSS mode.
					t_color = o.color(sValue);
				}
				line.setAttribute("fill", t_color);
			});
			text.setAttribute("y", _height/1.7);
		}
	};
	$.fn.progressPie.contentPlugin.progressMultiDisplay = function(args) {
		// console.log(args);
		drawText(Math.round(args.percentValue), Math.round(args.pieOpts.data), args);
	};
	$.fn.progressPie.contentPlugin.rawValue = function(args) {

		drawText(args.rawValue, args);
	};
	$.fn.progressPie.contentPlugin.progressMultiDisplayDefaults = {
		cssClass: "progresspie-progress",
		singleLine: false,
		fontFamily: 'Open Sans',
		fontSize: 36,
		fontWeight: 600,
		color: '#666',
		multiline: [
			{
				cssClass: "progresspie-progressText",
				fontSize: 11,
				textContent: 'PROGRESS',
				color: '#dddddd',
			},
			{
				cssClass: "progresspie-progressValue",
				fontSize: 28,
				textContent: '%s%' ,
				color: function(value){
					return $.fn.progressPie.colorByPercent(value);
				},
			}
		]
	};
	$.fn.progressPie.colorByPercent = function(percent, alpha) { 
		var _alpha = ( typeof alpha === "number" ? alpha : 1);
		var rgb = (percent <= 100) ? '110, 175, 121' : '233, 71, 84';
		return "rgba(" + rgb + "," + _alpha +")";
	};
} (jQuery));
/* END  Add plugin for display progress text */



