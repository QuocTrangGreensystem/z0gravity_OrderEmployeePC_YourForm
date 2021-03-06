/*
jQWidgets v2.9.3 (2013-July-11)
Copyright (c) 2011-2013 jQWidgets.
License: http://jqwidgets.com/license/
*/
(function (a) {
    a.jqx.jqxWidget("jqxChart", "", {});
    a.extend(a.jqx._jqxChart.prototype, {
        defineInstance: function () {
            this.title = "Title";
            this.description = "Description";
            this.source = [];
            this.seriesGroups = [];
            this.categoryAxis = {};
            this.renderEngine = undefined;
            this.enableAnimations = true;
            this.backgroundImage = this.background = undefined;
            this.padding = {
                left: 5,
                top: 5,
                right: 5,
                bottom: 5
            };
            this.backgroundColor = "#FFFFFF";
            this.showBorderLine = true;
            this.borderLineWidth = 1;
            this.titlePadding = {
                left: 2,
                top: 2,
                right: 2,
                bottom: 2
            };
            this.showLegend = true;
            this.legendLayout = undefined;
            this.enabled = true;
            this.colorScheme = "scheme01";
            this.animationDuration = 500;
            this.showToolTips = true;
            this.toolTipShowDelay = this.toolTipDelay = 500;
            this.toolTipHideDelay = 4000;
            this.toolTipFormatFunction = undefined;
            this.columnSeriesOverlap = false;
            this.rtl = false;
            this.legendPosition = null;
            this.borderLineColor = null;
            this.borderColor = null;
            this.greyScale = false;
            this.axisPadding = 5;
            this.enableCrosshairs = false;
            this.crosshairsColor = "#888888";
            this.crosshairsDashStyle = "2,2";
            this.crosshairsLineWidth = 1
        },
        createInstance: function (e) {
            if (!a.jqx.dataAdapter) {
                throw "jqxdata.js is not loaded";
                return
            }
            this._refreshOnDownloadComlete();
            var c = this;
            this.host.on("mousemove", function (g) {
                if (c.enabled == false) {
                    return
                }
                g.preventDefault();
                var f = g.pageX || g.clientX || g.screenX;
                var i = g.pageY || g.clientY || g.screenY;
                var h = c.host.offset();
                f -= h.left;
                i -= h.top;
                c.onmousemove(f, i)
            });
            this.addHandler(this.host, "mouseleave", function (f) {
                if (c.enabled == false) {
                    return
                }
                c._cancelTooltipTimer();
                c._hideToolTip(0)
            });
            var d = a.jqx.mobile.isTouchDevice();
            this.addHandler(this.host, "click", function (g) {
                if (c.enabled == false) {
                    return
                }
                if (!d) {
                    c._cancelTooltipTimer();
                    c._hideToolTip()
                }
                if (c._pointMarker && c._pointMarker.element) {
                    var h = c.seriesGroups[c._pointMarker.gidx];
                    var f = h.series[c._pointMarker.sidx];
                    c._raiseEvent("click", h, f, c._pointMarker.iidx)
                }
            });
            if (this.element.style) {
                var b = false;
                if (this.element.style.width != null) {
                    b |= this.element.style.width.toString().indexOf("%") != -1
                }
                if (this.element.style.height != null) {
                    b |= this.element.style.height.toString().indexOf("%") != -1
                }
                if (b) {
                    a(window).resize(function () {
                        if (c.timer) {
                            clearTimeout(c.timer)
                        }
                        var f = a.jqx.browser.msie ? 200 : 1;
                        c.timer = setTimeout(function () {
                            var g = c.enableAnimations;
                            c.enableAnimations = false;
                            c.refresh();
                            c.enableAnimations = g
                        }, f)
                    })
                }
            }
        },
        _refreshOnDownloadComlete: function () {
            if (this.source instanceof a.jqx.dataAdapter) {
                var c = this;
                var d = this.source._options;
                if (d == undefined || (d != undefined && !d.autoBind)) {
                    this.source.autoSync = false;
                    this.source.dataBind()
                }
                if (this.source.records.length == 0) {
                    var b = function () {
                        if (c.ready) {
                            c.ready()
                        }
                        c.refresh()
                    };
                    this.source.unbindDownloadComplete(this.element.id);
                    this.source.bindDownloadComplete(this.element.id, b)
                } else {
                    if (c.ready) {
                        c.ready()
                    }
                }
                this.source.unbindBindingUpdate(this.element.id);
                this.source.bindBindingUpdate(this.element.id, function () {
                    c.refresh()
                })
            }
        },
        propertyChangedHandler: function (b, c, e, d) {
            if (this.isInitialized == undefined || this.isInitialized == false) {
                return
            }
            if (c == "source") {
                this._refreshOnDownloadComlete()
            }
            this.refresh()
        },
        _internalRefresh: function () {
            if (a.jqx.isHidden(this.host)) {
                return
            }
            this._stopAnimations();
            this.host.empty();
            this._renderData = new Array();
            var c = null;
            if (document.createElementNS && (this.renderEngine == "SVG" || this.renderEngine == undefined)) {
                c = new a.jqx.svgRenderer();
                if (!c.init(this.host)) {
                    if (this.renderEngine == "SVG") {
                        throw "Your browser does not support SVG"
                    }
                    return
                }
            }
            if (c == null && this.renderEngine != "HTML5") {
                c = new a.jqx.vmlRenderer();
                if (!c.init(this.host)) {
                    if (this.renderEngine == "VML") {
                        throw "Your browser does not support VML"
                    }
                    return
                }
                this._isVML = true
            }
            if (c == null && (this.renderEngine == "HTML5" || this.renderEngine == undefined)) {
                c = new a.jqx.HTML5Renderer();
                if (!c.init(this.host)) {
                    throw "Your browser does not support HTML5 Canvas"
                }
            }
            this.renderer = c;
            var b = this.renderer.getRect();
            this._render({
                x: 1,
                y: 1,
                width: b.width,
                height: b.height
            });
            if (this.renderer instanceof a.jqx.HTML5Renderer) {
                this.renderer.refresh()
            }
        },
        saveAsPNG: function (c, b) {
            return this._saveAsImage("png", c, b)
        },
        saveAsJPEG: function (c, b) {
            return this._saveAsImage("jpeg", c, b)
        },
        _saveAsImage: function (j, g, l) {
            if (g == undefined || g == "") {
                g = "chart." + j
            }
            if (l == undefined || l == "") {
                l = "http://www.jqwidgets.com/export_server/export.php"
            }
            var k = this.rendererEngine;
            var f = this.enableAnimations;
            this.enableAnimations = false;
            this.renderEngine = "HTML5";
            if (this.renderEngine != k) {
                try {
                    this.refresh()
                } catch (i) {
                    this.renderEngine = k;
                    this.refresh();
                    this.enableAnimations = f
                }
            }
            try {
                var d = this.renderer.getContainer()[0];
                if (d) {
                    var h = d.toDataURL("image/" + j);
                    h = h.replace("data:image/" + j + ";base64,", "");
                    var c = document.createElement("form");
                    c.method = "POST";
                    c.action = l;
                    c.style.display = "none";
                    document.body.appendChild(c);
                    var m = document.createElement("input");
                    m.name = "fname";
                    m.value = g;
                    m.style.display = "none";
                    var b = document.createElement("input");
                    b.name = "content";
                    b.value = h;
                    b.style.display = "none";
                    c.appendChild(m);
                    c.appendChild(b);
                    c.submit();
                    document.body.removeChild(c)
                }
            } catch (i) {}
            if (this.renderEngine != k) {
                this.renderEngine = k;
                this.refresh();
                this.enableAnimations = f
            }
            return true
        },
        refresh: function () {
            this._internalRefresh()
        },
        _seriesTypes: ["line", "stackedline", "stackedline100", "spline", "stackedspline", "stackedspline100", "stepline", "stackedstepline", "stackedstepline100", "area", "stackedarea", "stackedarea100", "splinearea", "stackedsplinearea", "stackedsplinearea100", "steparea", "stackedsteparea", "stackedsteparea100", "rangearea", "splinerangearea", "steprangearea", "column", "stackedcolumn", "stackedcolumn100", "rangecolumn", "pie", "donut", "scatter", "bubble"],
        _render: function (z) {
            this.renderer.clear();
            var l = this.backgroundImage;
            if (l == undefined || l == "") {
                this.host.css({
                    "background-image": ""
                })
            } else {
                this.host.css({
                    "background-image": (l.indexOf("(") != -1 ? l : "url('" + l + "')")
                })
            }
            this._buildStats();
            var P = this.padding || {
                left: 5,
                top: 5,
                right: 5,
                bottom: 5
            };
            //edit by thach 2013-11-18
            var X = this.renderer.rect(z.x, z.y, z.width - 2, z.height - 2);
            // if(_seriesTypes["rangecolumn"]){
            //       var X = this.renderer.rect(z.x, z.y, 10, z.height - 2);
            // }
            var E = this.renderer.beginGroup();
            var o = this.renderer.createClipRect(z);
            this.renderer.setClip(E, o);
            if (l == undefined || l == "") {
                this.renderer.attr(X, {
                    fill: this.background || this.backgroundColor || "white"
                })
            } else {
                this.renderer.attr(X, {
                    fill: "transparent"
                })
            } if (this.showBorderLine != false) {
                var B = this.borderLineColor == undefined ? this.borderColor : this.borderLineColor;
                if (B == undefined) {
                    B = "#888888"
                }
                var m = this.borderLineWidth;
                if (isNaN(m) || m < 0 || m > 10) {
                    m = 1
                }
                this.renderer.attr(X, {
                    "stroke-width": m,
                    stroke: B
                })
            }
            var M = {
                x: P.left,
                y: P.top,
                width: z.width - P.left - P.right,
                height: z.height - P.top - P.bottom
            };
            this._paddedRect = M;
            var e = this.titlePadding || {
                left: 2,
                top: 2,
                right: 2,
                bottom: 2
            };
            if (this.title && this.title.length > 0) {
                var J = this.toThemeProperty("jqx-chart-title-text", null);
                var k = this.renderer.measureText(this.title, 0, {
                    "class": J
                });
                this.renderer.text(this.title, M.x + e.left, M.y + e.top, M.width - (e.left + e.right), k.height, 0, {
                    "class": J
                }, true, "center", "center");
                M.y += k.height;
                M.height -= k.height
            }
            if (this.description && this.description.length > 0) {
                var K = this.toThemeProperty("jqx-chart-title-description", null);
                var k = this.renderer.measureText(this.description, 0, {
                    "class": K
                });
                this.renderer.text(this.description, M.x + e.left, M.y + e.top, M.width - (e.left + e.right), k.height, 0, {
                    "class": K
                }, true, "center", "center");
                M.y += k.height;
                M.height -= k.height
            }
            if (this.title || this.description) {
                M.y += (e.bottom + e.top);
                M.height -= (e.bottom + e.top)
            }
            var b = {
                x: M.x,
                y: M.y,
                width: M.width,
                height: M.height
            };
            var C = this._isPieOnlySeries();
            var t = {};
            for (var Q = 0; Q < this.seriesGroups.length && !C; Q++) {
                if (this.seriesGroups[Q].type == "pie" || this.seriesGroups[Q].type == "donut") {
                    continue
                }
                var A = this.seriesGroups[Q].orientation == "horizontal";
                var u = this.seriesGroups[Q].valueAxis;
                if (!u) {
                    throw "seriesGroup[" + Q + "] is missing " + (A ? "categoryAxis" : "valueAxis") + " definition"
                }
                var d = this._getCategoryAxis(Q);
                if (!d) {
                    throw "seriesGroup[" + Q + "] is missing " + (!A ? "categoryAxis" : "valueAxis") + " definition"
                }
                var s = d == this.categoryAxis ? -1 : Q;
                t[s] = 0
            }
            var L = this.axisPadding;
            if (isNaN(L)) {
                L = 5
            }
            var p = {
                left: 0,
                right: 0,
                leftCount: 0,
                rightCount: 0
            };
            var n = [];
            for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                if (this.seriesGroups[Q].type == "pie" || this.seriesGroups[Q].type == "donut") {
                    n.push(0);
                    continue
                }
                var A = this.seriesGroups[Q].orientation == "horizontal";
                var s = this._getCategoryAxis(Q) == this.categoryAxis ? -1 : Q;
                var I = u.axisSize;
                var f = {
                    x: 0,
                    y: b.y,
                    width: b.width,
                    height: b.height
                };
                var H = undefined;
                if (!I || I == "auto") {
                    if (A) {
                        I = this._renderCategoryAxis(Q, f, true, b).width;
                        if ((t[s] & 1) == 1) {
                            I = 0
                        } else {
                            t[s] |= 1
                        }
                        H = this._getCategoryAxis(Q).position
                    } else {
                        I = this._renderValueAxis(Q, f, true, b).width;
                        if (this.seriesGroups[Q].valueAxis) {
                            H = this.seriesGroups[Q].valueAxis.position
                        }
                    }
                }
                if (H != "left" && this.rtl == true) {
                    H = "right"
                }
                if (H != "right") {
                    H = "left"
                }
                if (p[H + "Count"] > 0 && p[H] > 0 && I > 0) {
                    p[H] += L
                }
                n.push({
                    width: I,
                    position: H,
                    xRel: p[H]
                });
                p[H] += I;
                p[H + "Count"]++
            }
            var T = {
                top: 0,
                bottom: 0,
                topCount: 0,
                bottomCount: 0
            };
            var N = [];
            for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                if (this.seriesGroups[Q].type == "pie" || this.seriesGroups[Q].type == "donut") {
                    N.push(0);
                    continue
                }
                var A = this.seriesGroups[Q].orientation == "horizontal";
                var d = this._getCategoryAxis(Q);
                var s = d == this.categoryAxis ? -1 : Q;
                H = undefined;
                var S = d.axisSize;
                if (!S || S == "auto") {
                    if (A) {
                        S = this._renderValueAxis(Q, {
                            x: 0,
                            y: 0,
                            width: 10000000,
                            height: 0
                        }, true, b).height;
                        if (this.seriesGroups[Q].valueAxis) {
                            H = this.seriesGroups[Q].valueAxis.position
                        }
                    } else {
                        S = this._renderCategoryAxis(Q, {
                            x: 0,
                            y: 0,
                            width: 10000000,
                            height: 0
                        }, true).height;
                        if ((t[s] & 2) == 2) {
                            S = 0
                        } else {
                            t[s] |= 2
                        }
                        H = this._getCategoryAxis(Q).position
                    }
                }
                if (H != "top") {
                    H = "bottom"
                }
                if (T[H + "Count"] > 0 && T[H] > 0 && S > 0) {
                    T[H] += L
                }
                N.push({
                    height: S,
                    position: H,
                    yRel: T[H]
                });
                T[H] += S;
                T[H + "Count"]++
            }
            this._plotRect = b;
            var r = (this.showLegend != false);
            var v = !r || this.legendLayout ? {
                width: 0,
                height: 0
            } : this._renderLegend(M, true);
            if (M.height < T.top + T.bottom + v.height || M.width < p.left + p.right) {
                return
            }
            b.height -= T.top + T.bottom + v.height;
            b.x += p.left;
            b.width -= p.left + p.right;
            b.y += T.top;
            if (!C) {
                var V = this.categoryAxis.tickMarksColor || "#888888";
                for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                    var A = this.seriesGroups[Q].orientation == "horizontal";
                    var s = this._getCategoryAxis(Q) == this.categoryAxis ? -1 : Q;
                    var f = {
                        x: b.x,
                        y: 0,
                        width: b.width,
                        height: N[Q].height
                    };
                    if (N[Q].position != "top") {
                        f.y = b.y + b.height + N[Q].yRel
                    } else {
                        f.y = b.y - N[Q].yRel - N[Q].height
                    } if (A) {
                        this._renderValueAxis(Q, f, false, b)
                    } else {
                        if ((t[s] & 4) == 4) {
                            continue
                        }
                        this._renderCategoryAxis(Q, f, false, b);
                        t[s] |= 4
                    }
                }
            }
            if (r) {
                var G = b.x + a.jqx._ptrnd((b.width - v.width) / 2);
                var F = b.y + b.height + T.bottom;
                var I = b.width;
                var S = v.height;
                if (this.legendLayout) {
                    G = this.legendLayout.left || G;
                    F = this.legendLayout.top || F;
                    I = this.legendLayout.width || I;
                    S = this.legendLayout.height || S
                }
                if (G + I > z.x + z.width) {
                    I = z.x + z.width - G
                }
                if (F + S > z.y + z.height) {
                    S = z.y + z.height - F
                }
                this._renderLegend({
                    x: G,
                    y: F,
                    width: I,
                    height: S
                })
            }
            this._hasHorizontalLines = false;
            if (!C) {
                for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                    var A = this.seriesGroups[Q].orientation == "horizontal";
                    var f = {
                        x: b.x - n[Q].xRel - n[Q].width,
                        y: b.y,
                        width: n[Q].width,
                        height: b.height
                    };
                    if (n[Q].position != "left") {
                        f.x = b.x + b.width + n[Q].xRel
                    }
                    if (A) {
                        if ((t[this._getCategoryAxis(Q)] & 8) == 8) {
                            continue
                        }
                        this._renderCategoryAxis(Q, f, false, b);
                        t[this._getCategoryAxis(Q)] |= 8
                    } else {
                        this._renderValueAxis(Q, f, false, b)
                    }
                }
            }
            if (b.width <= 0 || b.height <= 0) {
                return
            }
            this._plotRect = {
                x: b.x,
                y: b.y,
                width: b.width,
                height: b.height
            };
            var U = this.renderer.beginGroup();
            var D = this.renderer.createClipRect({
                x: b.x,
                y: b.y,
                width: b.width,
                height: b.height
            });
            this.renderer.setClip(U, D);
            this._createAnimationGroup("series");
            for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                var q = this.seriesGroups[Q];
                var c = false;
                for (var W in this._seriesTypes) {
                    if (this._seriesTypes[W] == q.type) {
                        c = true;
                        break
                    }
                }
                if (!c) {
                    throw 'jqxChart: invalid series type "' + q.type + '"';
                    continue
                }
                if (q.bands) {
                    for (var O = 0; O < q.bands.length; O++) {
                        this._renderBand(Q, O, b)
                    }
                }
                if (q.type.indexOf("column") != -1) {
                    this._renderColumnSeries(Q, b)
                } else {
                    if (q.type.indexOf("pie") != -1 || q.type.indexOf("donut") != -1) {
                        this._renderPieSeries(Q, b)
                    } else {
                        if (q.type.indexOf("line") != -1 || q.type.indexOf("area") != -1) {
                            this._renderLineSeries(Q, b)
                        } else {
                            if (q.type == "scatter" || q.type == "bubble") {
                                this._renderScatterSeries(Q, b)
                            }
                        }
                    }
                }
            }
            this._startAnimation("series");
            this.renderer.endGroup();
            if (this.enabled == false) {
                var R = this.renderer.rect(z.x, z.y, z.width, z.height);
                this.renderer.attr(R, {
                    fill: "#777777",
                    opacity: 0.5,
                    stroke: "#00FFFFFF"
                })
            }
            this.renderer.endGroup()
        },
        _isPieOnlySeries: function () {
            if (this.seriesGroups.length == 0) {
                return false
            }
            for (var b = 0; b < this.seriesGroups.length; b++) {
                if (this.seriesGroups[b].type != "pie" && this.seriesGroups[b].type != "donut") {
                    return false
                }
            }
            return true
        },
        _renderChartLegend: function (v, c, d, e) {
            var n = {
                x: c.x + 3,
                y: c.y + 3,
                width: c.width - 6,
                height: c.height - 6
            };
            var j = {
                width: n.width,
                height: 0
            };
            var h = 0,
                g = 0;
            var f = 20;
            var b = 0;
            var o = 10;
            var u = 10;
            var s = 0;
            for (var q = 0; q < v.length; q++) {
                var k = v[q].css;
                if (!k) {
                    k = this.toThemeProperty("jqx-chart-legend-text", null)
                }
                var l = v[q].text;
                var m = this.renderer.measureText(l, 0, {
                    "class": k
                });
                if (m.height > f) {
                    f = m.height
                }
                if (m.width > s) {
                    s = m.width
                }
                if (e) {
                    if (q != 0) {
                        g += f
                    }
                    if (g > n.height) {
                        g = 0;
                        h += s + u;
                        s = m.width;
                        j.width = h + s
                    }
                } else {
                    if (h != 0) {
                        h += u
                    }
                    if (h + 2 * o + m.width > n.width && m.width < n.width) {
                        h = 0;
                        g += f;
                        f = 20;
                        b = n.width;
                        j.heigh = g + f
                    }
                } if (!d && n.x + h + m.width < c.x + c.width && n.y + g + m.height < c.y + c.height) {
                    var p = v[q].color;
                    var t = this.renderer.rect(n.x + h, n.y + g + o / 2, o, o);
                    this.renderer.attr(t, {
                        fill: p,
                        "fill-opacity": v[q].opacity,
                        stroke: p,
                        "stroke-width": 1
                    });
                    this.renderer.text(l, n.x + h + 1.5 * o, n.y + g, m.width, f, 0, {
                        "class": k
                    }, false, "center", "center")
                }
                if (e) {} else {
                    h += m.width + 2 * o;
                    if (b < h) {
                        b = h
                    }
                }
            }
            if (d) {
                j.height = a.jqx._ptrnd(g + f);
                j.width = a.jqx._ptrnd(b);
                return j
            }
        },
        _renderLegend: function (o, m) {
            var b = [];
            for (var t = 0; t < this.seriesGroups.length; t++) {
                var k = this.seriesGroups[t];
                if (k.showLegend == false) {
                    continue
                }
                var j = this._getCategoryAxis(t);
                var q = j.toolTipFormatSettings || j.formatSettings;
                var f = j.toolTipFormatFunction || j.formatFunction;
                for (var p = 0; p < k.series.length; p++) {
                    var u = k.series[p];
                    if (u.showLegend == false) {
                        continue
                    }
                    var d = this._getSerieSettings(t, p);
                    if (k.type == "pie" || k.type == "donut") {
                        var n = u.colorScheme || k.colorScheme || this.colorScheme;
                        var c = this._getDataLen(t);
                        for (var h = 0; h < c; h++) {
                            var l = this._getDataValue(h, u.displayText, t);
                            l = this._formatValue(l, q, f);
                            var e = this._getColor(n, p * c + h, t, p);
                            b.push({
                                groupIndex: t,
                                seriesIndex: p,
                                itemIndex: h,
                                text: l,
                                css: u.displayTextClass,
                                color: e,
                                opacity: d.opacity
                            })
                        }
                        continue
                    }
                    var r = u.displayText || u.dataField || "";
                    var e = this._getSeriesColor(t, p);
                    b.push({
                        groupIndex: t,
                        seriesIndex: p,
                        text: r,
                        css: u.displayTextClass,
                        color: e,
                        opacity: d.opacity
                    })
                }
            }
            return this._renderChartLegend(b, o, m, (this.legendLayout && this.legendLayout.flow == "vertical"))
        },
        _renderCategoryAxis: function (k, g, j, l) {
            var h = this._getCategoryAxis(k);
            var e = this.seriesGroups[k].orientation == "horizontal";
            var t = {
                width: 0,
                height: 0
            };
            if (!h || h.visible == false) {
                return t
            }
            if (this.rtl) {
                h.flip = true
            }
            var v = h.text;
            var E = {
                visible: (h.showGridLines != false),
                color: (h.gridLinesColor || "#888888"),
                unitInterval: (h.gridLinesInterval || h.unitInterval),
                dashStyle: h.gridLinesDashStyle
            };
            var r = {
                visible: (h.showTickMarks != false),
                color: (h.tickMarksColor || "#888888"),
                unitInterval: (h.tickMarksInterval || h.unitInterval),
                dashStyle: h.tickMarksDashStyle
            };
            var F = h.textRotationAngle || 0;
            var H = g;
            if (e) {
                H = {
                    x: g.x,
                    y: g.y,
                    width: g.height,
                    height: g.width
                }
            }
            var o = this._calculateXOffsets(k, H);
            var A = h.unitInterval;
            if (isNaN(A)) {
                A = 1
            }
            var G = h.horizontalTextAlignment;
            var q = this._alignValuesWithTicks(k);
            var n = this.renderer.getRect();
            var d = n.width - g.x - g.width;
            var D = this._getDataLen(k);
            var z = [];
            if (h.type != "date") {
                var B = o.customRange != false;
                var p = A;
                for (var C = o.min; C <= o.max; C += p) {
                    if (B || h.dataField == undefined || h.dataField == "") {
                        value = C
                    } else {
                        var w = Math.round(C);
                        value = this._getDataValue(w, h.dataField)
                    }
                    var v = this._formatValue(value, h.formatSettings, h.formatFunction, undefined, undefined, w);
                    if (v == undefined) {
                        v = !B ? value.toString() : (C).toString()
                    }
                    z.push(v);
                    if (C + p > o.max) {
                        p = o.max - C;
                        if (p <= A / 2) {
                            break
                        }
                    }
                }
            } else {
                var f = this._getDateTimeArray(o.min, o.max, h.baseUnit, q, A);
                for (var C = 0; C < f.length; C += A) {
                    z.push(this._formatValue(f[C], h.formatSettings, h.formatFunction))
                }
            } if (h.flip == true || this.rtl) {
                z.reverse()
            }
            var m = h.descriptionClass;
            if (!m) {
                m = this.toThemeProperty("jqx-chart-axis-description", null)
            }
            var u = h["class"];
            if (!u) {
                u = this.toThemeProperty("jqx-chart-axis-text", null)
            }
            if (e) {
                F -= 90
            }
            var s = {
                text: h.description,
                style: m,
                halign: h.horizontalDescriptionAlignment || "center",
                valign: h.verticalDescriptionAlignment || "center",
                textRotationAngle: e ? -90 : 0
            };
            var c = {
                textRotationAngle: F,
                style: u,
                halign: G,
                valign: h.verticalTextAlignment || "center",
                textRotationPoint: h.textRotationPoint || "auto",
                textOffset: h.textOffset
            };
            var b = (e && h.position == "right") || (!e && h.position == "top");
            return this._renderAxis(e, b, s, c, {
                x: g.x,
                y: g.y,
                width: g.width,
                height: g.height
            }, l, A, false, q, z, o, E, r, j)
        },
        _renderAxis: function (J, F, U, m, z, c, H, l, V, s, u, d, A, T) {
            var n = A.visible ? 4 : 0;
            var Q = 2;
            var I = {
                width: 0,
                height: 0
            };
            var q = {
                width: 0,
                height: 0
            };
            if (J) {
                I.height = q.height = z.height
            } else {
                I.width = q.width = z.width
            } if (!T && F) {
                if (J) {
                    z.x -= z.width
                }
            }
            if (U.text != undefined && U != "") {
                var r = U.textRotationAngle;
                var f = this.renderer.measureText(U.text, r, {
                    "class": U.style
                });
                q.width = f.width;
                q.height = f.height;
                if (!T) {
                    this.renderer.text(U.text, z.x + (J ? (!F ? Q : -Q + 2 * z.width - q.width) : 0), z.y + (!J ? (!F ? z.height - Q - q.height : Q) : 0), J ? q.width : z.width, !J ? q.height : z.height, r, {
                        "class": U.style
                    }, true, U.halign, U.valign)
                }
            }
            var N = 0;
            var t = V ? -u.itemWidth / 2 : 0;
            if (V && !J) {
                m.halign = "center"
            }
            var P = z.x;
            var O = z.y;
            var G = m.textOffset;
            if (G) {
                if (!isNaN(G.x)) {
                    P += G.x
                }
                if (!isNaN(G.y)) {
                    O += G.y
                }
            }
            if (!J) {
                P += t;
                if (F) {
                    O += q.height > 0 ? q.height + 3 * Q : 2 * Q;
                    O += n - (V ? n : n / 4)
                } else {
                    O += V ? n : n / 4
                }
            } else {
                P += Q + (q.width > 0 ? (q.width + Q) : 0) + (F ? z.width - q.width : 0);
                O += t
            }
            var S = 0;
            var M = 0;
            var b = u.itemWidth;
            for (var R = 0; R < s.length; R++, N += b) {
                var v = s[R];
                var B = m.textRotationAngle;
                var f = this.renderer.measureText(v, B, {
                    "class": m.style
                });
                if (f.width > M) {
                    M = f.width
                }
                if (f.height > S) {
                    S = f.height
                }
                if (!T) {
                    if ((J && N > z.height + 2) || (!J && N > z.width + 2)) {
                        break
                    }
                    if (!l || (l && (R % H) == 0)) {
                        this.renderer.text(v, J ? P : P + N, J ? O + N : O, !J ? b : z.width - 2 * Q - n - ((q.width > 0) ? q.width + Q : 0), J ? b : z.height - 2 * Q - n - ((q.height > 0) ? q.height + Q : 0), B, {
                            "class": m.style
                        }, false, m.halign, m.valign, m.textRotationPoint)
                    }
                }
            }
            I.width += 2 * Q + n + q.width + M + (J && q.width > 0 ? Q : 0);
            I.height += 2 * Q + n + q.height + S + (!J && q.height > 0 ? Q : 0);
            var D = {};
            var j = {
                stroke: d.color,
                "stroke-width": 1,
                "stroke-dasharray": d.dashStyle || ""
            };
            if (!T) {
                var K = a.jqx._ptrnd(z.y + (F ? z.height : 0));
                if (J) {
                    this.renderer.line(a.jqx._ptrnd(z.x + z.width), z.y, a.jqx._ptrnd(z.x + z.width), z.y + z.height, j)
                } else {
                    this.renderer.line(a.jqx._ptrnd(z.x), K, a.jqx._ptrnd(z.x + z.width + 1), K, j)
                }
            }
            var p = 0.5;
            if (!T && d.visible != false) {
                var k = d.unitInterval;
                if (isNaN(k) || k <= 0) {
                    k = H
                }
                var o = l ? s.length : u.rangeLength;
                var C = l ? 1 : k;
                var E = l ? b : (J ? z.height : z.width) / u.rangeLength;
                var R = 0;
                while (R <= o) {
                    if (l && (R % k) != 0) {
                        R += C;
                        continue
                    }
                    var g = 0;
                    if (J) {
                        g = a.jqx._ptrnd(z.y + R * E);
                        if (g > z.y + z.height + p) {
                            break
                        }
                    } else {
                        g = a.jqx._ptrnd(z.x + R * E);
                        if (g > z.x + z.width + p) {
                            break
                        }
                    } if (J) {
                        this.renderer.line(a.jqx._ptrnd(c.x), g, a.jqx._ptrnd(c.x + c.width), g, j)
                    } else {
                        this.renderer.line(g, a.jqx._ptrnd(c.y), g, a.jqx._ptrnd(c.y + c.height), j)
                    }
                    D[g] = true;
                    R += C;
                    if (R > o && R != o + C) {
                        R = o
                    }
                }
            }
            var j = {
                stroke: A.color,
                "stroke-width": 1,
                "stroke-dasharray": A.dashStyle || ""
            };
            if (!T && A.visible) {
                var L = A.unitInterval;
                if (isNaN(L) || L <= 0) {
                    L = H
                }
                var o = l ? s.length : u.rangeLength + L;
                var C = l ? 1 : L;
                var E = l ? b : (J ? z.height : z.width) / u.rangeLength;
                for (var R = 0; R <= o; R += C) {
                    if (l && (R % L / H) != 0) {
                        continue
                    }
                    var g = a.jqx._ptrnd((J ? z.y : z.x) + R * E);
                    if (D[g - 1]) {
                        g--
                    } else {
                        if (D[g + 1]) {
                            g++
                        }
                    } if (J) {
                        if (g > z.y + z.height + p) {
                            break
                        }
                    } else {
                        if (g > z.x + z.width + p) {
                            break
                        }
                    }
                    var e = !F ? -n : n;
                    if (J) {
                        this.renderer.line(z.x + z.width, g, z.x + z.width + e, g, j)
                    } else {
                        var K = a.jqx._ptrnd(z.y + (F ? z.height : 0));
                        this.renderer.line(g, K, g, K - e, j)
                    }
                }
            }
            I.width = a.jqx._rup(I.width);
            I.height = a.jqx._rup(I.height);
            return I
        },
        _renderValueAxis: function (n, h, l, o) {
            var K = this.seriesGroups[n];
            var d = K.orientation == "horizontal";
            var k = K.valueAxis;
            if (!k) {
                throw "SeriesGroup " + n + " is missing valueAxis definition"
            }
            var z = {
                width: 0,
                height: 0
            };
            if (this._isPieOnlySeries()) {
                if (l) {
                    return z
                }
                return
            }
            var v = this._stats.seriesGroups[n];
            if (!v || !v.isValid || false == k.displayValueAxis || false == k.visible) {
                if (l) {
                    return z
                }
                return
            }
            var p = k.descriptionClass;
            if (!p) {
                p = this.toThemeProperty("jqx-chart-axis-description", null)
            }
            var w = {
                text: k.description,
                style: p,
                halign: k.horizontalDescriptionAlignment || "center",
                valign: k.verticalDescriptionAlignment || "center",
                textRotationAngle: d ? 0 : (!this.rtl ? -90 : 90)
            };
            var A = k.itemsClass;
            if (!A) {
                A = this.toThemeProperty("jqx-chart-axis-text", null)
            }
            var c = {
                style: A,
                halign: k.horizontalTextAlignment || "center",
                valign: k.verticalTextAlignment || "center",
                textRotationAngle: k.textRotationAngle || 0,
                textRotationPoint: k.textRotationPoint || "auto",
                textOffset: k.textOffset
            };
            var r = k.valuesOnTicks != false;
            var f = k.dataField;
            var C = v.intervals;
            var H = v.min;
            var F = v.mu;
            var J = k.formatSettings;
            var j = K.type.indexOf("stacked") != -1 && K.type.indexOf("100") != -1;
            if (j && !J) {
                J = {
                    sufix: "%"
                }
            }
            if (!r) {
                C = Math.max(C - 1, 1)
            }
            var e = k.logarithmicScale == true;
            var m = k.logarithmicScaleBase || 10;
            if (e) {
                F = !isNaN(k.unitInterval) ? k.unitInterval : 1
            }
            var E = (d ? h.width : h.height) / C;
            var u = h.y + h.height - E;
            var D = [];
            var q = {};
            q.data = [];
            q.itemWidth = E;
            for (var I = 0; I <= C; I++) {
                var G = 0;
                if (e) {
                    if (j) {
                        G = v.max / Math.pow(m, C - I)
                    } else {
                        G = H * Math.pow(m, I)
                    }
                } else {
                    G = r ? H + I * F : H + (I + 0.5) * F
                }
                var B = (k.formatFunction) ? k.formatFunction(G) : this._formatNumber(G, J);
                D.push(B);
                q.data.push(u + E / 2);
                u -= E
            }
            q.rangeLength = e && !j ? v.intervals : (v.intervals) * F;
            if (K.valueAxis.flip != true) {
                q.data = q.data.reverse();
                D = D.reverse()
            }
            var M = k.gridLinesInterval || k.unitInterval;
            if (isNaN(M) || (e && M < F)) {
                M = F
            }
            var L = {
                visible: (k.showGridLines != false),
                color: (k.gridLinesColor || "#888888"),
                unitInterval: M,
                dashStyle: k.gridLinesDashStyle
            };
            var t = k.tickMarksInterval || k.unitInterval;
            if (isNaN(t) || (e && t < F)) {
                t = F
            }
            var s = {
                visible: (k.showTickMarks != false),
                color: (k.tickMarksColor || "#888888"),
                unitInterval: t,
                dashStyle: k.tickMarksDashStyle
            };
            var b = (d && k.position == "top") || (!d && k.position == "right") || (!d && this.rtl && k.position != "left");
            return this._renderAxis(!d, b, w, c, h, o, F, e, r, D, q, L, s, l)
        },
        _buildStats: function () {
            var P = {
                seriesGroups: new Array()
            };
            this._stats = P;
            for (var Q = 0; Q < this.seriesGroups.length; Q++) {
                var z = this.seriesGroups[Q];
                P.seriesGroups[Q] = {};
                var u = P.seriesGroups[Q];
                u.isValid = true;
                var M = z.valueAxis != undefined;
                var H = false;
                var G = 10;
                if (M) {
                    H = z.valueAxis.logarithmicScale == true;
                    G = z.valueAxis.logarithmicScaleBase;
                    if (isNaN(G)) {
                        G = 10
                    }
                }
                var C = -1 != z.type.indexOf("stacked");
                var d = C && -1 != z.type.indexOf("100");
                var F = -1 != z.type.indexOf("range");
                if (d) {
                    u.psums = new Array();
                    u.nsums = new Array()
                }
                var m = NaN,
                    J = NaN;
                var c = NaN,
                    e = NaN;
                var k = z.baselineValue;
                if (isNaN(k)) {
                    k = H && !d ? 1 : 0
                }
                var w = this._getDataLen(Q);
                var b = 0;
                var R = NaN;
                for (var O = 0; O < w && u.isValid; O++) {
                    var T = M ? z.valueAxis.minValue : Infinity;
                    var B = M ? z.valueAxis.maxValue : -Infinity;
                    var n = 0,
                        q = 0;
                    for (var K = 0; K < z.series.length; K++) {
                        var N = undefined,
                            v = undefined;
                        if (F) {
                            var U = this._getDataValueAsNumber(O, z.series[K].dataFieldFrom, Q);
                            var A = this._getDataValueAsNumber(O, z.series[K].dataFieldTo, Q);
                            N = Math.max(U, A);
                            v = Math.min(U, A)
                        } else {
                            var E = this._getDataValueAsNumber(O, z.series[K].dataField, Q);
                            if (isNaN(E) || (H && E <= 0)) {
                                continue
                            }
                            v = N = E
                        } if ((isNaN(B) || N > B) && ((!M || isNaN(z.valueAxis.maxValue)) ? true : N <= z.valueAxis.maxValue)) {
                            B = N
                        }
                        if ((isNaN(T) || v < T) && ((!M || isNaN(z.valueAxis.minValue)) ? true : v >= z.valueAxis.minValue)) {
                            T = v
                        }
                        if (E > k) {
                            n += E
                        } else {
                            if (E < k) {
                                q += E
                            }
                        }
                    }
                    if (H && d) {
                        for (var K = 0; K < z.series.length; K++) {
                            var E = this._getDataValueAsNumber(O, z.series[K].dataField, Q);
                            if (isNaN(E) || E <= 0) {
                                continue
                            }
                            var L = n == 0 ? 0 : E / n;
                            if (isNaN(R) || L < R) {
                                R = L
                            }
                        }
                    }
                    var j = n - q;
                    if (b < j) {
                        b = j
                    }
                    if (d) {
                        u.psums[O] = n;
                        u.nsums[O] = q
                    }
                    if (B > J || isNaN(J)) {
                        J = B
                    }
                    if (T < m || isNaN(m)) {
                        m = T
                    }
                    if (n > c || isNaN(c)) {
                        c = n
                    }
                    if (q < e || isNaN(e)) {
                        e = q
                    }
                }
                if (d) {
                    c = c == 0 ? 0 : Math.max(c, -e);
                    e = e == 0 ? 0 : Math.min(e, -c)
                }
                var h = M ? z.valueAxis.unitInterval : 0;
                if (!h) {
                    h = C ? (c - e) / 10 : (J - m) / 10
                }
                var t = NaN;
                var S = 0;
                var l = 0;
                if (H) {
                    if (d) {
                        t = 0;
                        var L = 1;
                        S = l = a.jqx.log(100, G);
                        while (L > R) {
                            L /= G;
                            S--;
                            t++
                        }
                        m = Math.pow(G, S)
                    } else {
                        if (C) {
                            J = Math.max(J, c)
                        }
                        l = a.jqx._rnd(a.jqx.log(J, G), 1, true);
                        J = Math.pow(G, l);
                        S = a.jqx._rnd(a.jqx.log(m, G), 1, false);
                        m = Math.pow(G, S)
                    }
                    h = G
                }
                var I = M ? z.valueAxis.tickMarksInterval || h : 0;
                var r = M ? z.valueAxis.gridLinesInterval || h : 0;
                if (m < e) {
                    e = m
                }
                if (J > c) {
                    c = J
                }
                var o = H ? m : a.jqx._rnd(C ? e : m, h, false);
                var f = H ? J : a.jqx._rnd(C ? c : J, h, true);
                if (d && f > 100) {
                    f = 100
                }
                if (d && !H) {
                    f = (f > 0) ? 100 : 0;
                    o = (o < 0) ? -100 : 0;
                    h = M ? z.valueAxis.unitInterval : 10;
                    if (isNaN(h) || h <= 0 || h >= 100) {
                        h = 10
                    }
                    if (I <= 0 || I >= 100) {
                        I = 10
                    }
                    if (r <= 0 || r >= 100) {
                        r = 10
                    }
                }
                if (isNaN(f) || isNaN(o) || isNaN(h)) {
                    continue
                }
                if (isNaN(t)) {
                    t = (f - o) / (h == 0 ? 1 : h)
                }
                if (H && !d) {
                    t = l - S;
                    b = Math.pow(G, t)
                }
                if (t < 1) {
                    continue
                }
                var D = f - o;
                u.rmax = C ? c : J;
                u.rmin = C ? e : m;
                u.min = o;
                u.max = f;
                u.minPow = S;
                u.maxPow = l;
                u.mu = h;
                u.maxRange = b;
                u.intervals = t;
                u.tickMarksInterval = I;
                u.tickMarksIntervals = I == 0 ? 0 : D / I;
                u.gridLinesInterval = r;
                u.gridLinesIntervals = r == 0 ? 0 : D / r;
                if (D == 0) {
                    D = 1
                }
                u.scale = C ? (c - e) / D : (J - m) / D
            }
        },
        _getDataLen: function (c) {
            var b = this.source;
            if (c != undefined && c != -1 && this.seriesGroups[c].source) {
                b = this.seriesGroups[c].source
            }
            if (b instanceof a.jqx.dataAdapter) {
                b = b.records
            }
            if (b) {
                return b.length
            }
            return 0
        },
        _getDataValue: function (b, e, d) {
            var c = this.source;
            if (d != undefined && d != -1) {
                c = this.seriesGroups[d].source || c
            }
            if (c instanceof a.jqx.dataAdapter) {
                c = c.records
            }
            if (!c || b < 0 || b > c.length - 1) {
                return NaN
            }
            return (e && e != "") ? c[b][e] : c[b]
        },
        _getDataValueAsNumber: function (b, e, c) {
            var d = this._getDataValue(b, e, c);
            if (this._isDate(d)) {
                return d.valueOf()
            }
            if (typeof (d) != "number") {
                d = parseFloat(d)
            }
            if (typeof (d) != "number") {
                d = undefined
            }
            return d
        },
        _renderPieSeries: function (d, D) {
            var n = this._getDataLen(d);
            var r = this.seriesGroups[d];
            while (this._renderData.length < d + 1) {
                this._renderData.push(null)
            }
            this._renderData[d] = [];
            for (var g = 0; g < r.series.length; g++) {
                var J = r.series[g];
                var R = this._getSerieSettings(d, g);
                var q = J.colorScheme || r.colorScheme || this.colorScheme;
                var h = J.initialAngle || 0;
                var O = h;
                var L = J.radius || Math.min(D.width, D.height) * 0.4;
                if (isNaN(L)) {
                    L = 1
                }
                var b = J.innerRadius || 0;
                if (isNaN(b) || b >= L) {
                    b = 0
                }
                var e = J.centerOffset || 0;
                var I = a.jqx.getNum([J.offsetX, r.offsetX, D.width / 2]);
                var G = a.jqx.getNum([J.offsetY, r.offsetY, D.height / 2]);
                var E = this._getAnimProps(d, g);
                var w = E.enabled && n < 5000 && this._isVML != true ? E.duration : 0;
                if (a.jqx.mobile.isMobileBrowser() && (this.renderer instanceof a.jqx.HTML5Renderer)) {
                    w = 0
                }
                this._renderData[d].push([]);
                var m = 0;
                var o = 0;
                for (var Q = 0; Q < n; Q++) {
                    var C = this._getDataValueAsNumber(Q, J.dataField, d);
                    if (isNaN(C)) {
                        continue
                    }
                    if (C > 0) {
                        m += C
                    } else {
                        o += C
                    }
                }
                var l = m - o;
                if (l == 0) {
                    l = 1
                }
                for (var Q = 0; Q < n; Q++) {
                    var C = this._getDataValueAsNumber(Q, J.dataField, d);
                    if (isNaN(C)) {
                        continue
                    }
                    var p = Math.round(Math.abs(C) / l * 360);
                    if (Q + 1 == n) {
                        p = 360 + h - O
                    }
                    var H = D.x + I;
                    var F = D.y + G;
                    var M = {
                        x1: H,
                        y1: F,
                        innerRadius: b,
                        outerRadius: L,
                        key: d + "_" + g + "_" + Q
                    };
                    this._renderData[d][g].push(M);
                    var N = this.renderer.pieslice(H, F, b, L, O, w == 0 ? O + p : O, e);
                    var T = e;
                    if (a.isFunction(e)) {
                        T = e({
                            seriesIndex: g,
                            seriesGroupIndex: d,
                            itemIndex: Q
                        })
                    }
                    if (isNaN(T)) {
                        T = 0
                    }
                    var t = {
                        x: H,
                        y: F,
                        innerRadius: b,
                        outerRadius: L,
                        fromAngle: O,
                        toAngle: O + p,
                        centerOffset: T
                    };
                    var k = this;
                    this._enqueueAnimation("series", N, undefined, w, function (U, i, V) {
                        var s = i.fromAngle + V * (i.toAngle - i.fromAngle);
                        var W = k.renderer.pieSlicePath(i.x, i.y, i.innerRadius, i.outerRadius, i.fromAngle, s, i.centerOffset);
                        k.renderer.attr(U, {
                            d: W
                        })
                    }, t);
                    var K = this._getColors(d, g, Q, "radialGradient", L);
                    this.renderer.attr(N, {
                        fill: K.fillColor,
                        stroke: K.lineColor,
                        "stroke-width": R.stroke,
                        "fill-opacity": R.opacity,
                        "stroke-dasharray": R.dashStyle
                    });
                    var u = O,
                        P = O + p;
                    var A = Math.abs(u - P);
                    var S = A > 180 ? 1 : 0;
                    if (A > 360) {
                        u = 0;
                        P = 360
                    }
                    var f = u * Math.PI * 2 / 360;
                    var v = P * Math.PI * 2 / 360;
                    var B = A / 2 + u;
                    var c = B * Math.PI * 2 / 360;
                    var j = this._showLabel(d, g, Q, {
                        x: 0,
                        y: 0,
                        width: 0,
                        height: 0
                    }, "left", "top", true);
                    var z = J.labelRadius || L + Math.max(j.width, j.height);
                    z += e;
                    var H = a.jqx._ptrnd(D.x + I + z * Math.cos(c) - j.width / 2);
                    var F = a.jqx._ptrnd(D.y + G - z * Math.sin(c) - j.height / 2);
                    this._showLabel(d, g, Q, {
                        x: H,
                        y: F,
                        width: j.width,
                        height: j.height
                    }, "left", "top");
                    this._installHandlers(N, d, g, Q);
                    O += p
                }
            }
        },
        _getColumnGroupsCount: function (c) {
            var e = 0;
            c = c || "vertical";
            var f = this.seriesGroups;
            for (var d = 0; d < f.length; d++) {
                var b = f[d].orientation || "vertical";
                if (f[d].type.indexOf("column") != -1 && b == c) {
                    e++
                }
            }
            return e
        },
        _getColumnGroupIndex: function (g) {
            var b = 0;
            var c = this.seriesGroups[g].orientation || "vertical";
            for (var e = 0; e < g; e++) {
                var f = this.seriesGroups[e];
                var d = f.orientation || "vertical";
                if (f.type.indexOf("column") != -1 && d == c) {
                    b++
                }
            }
            return b
        },
        _renderBand: function (p, l, j) {
            var o = this.seriesGroups[p];
            if (!o.bands || o.bands.length <= l) {
                return
            }
            var c = j;
            if (o.orientation == "horizontal") {
                c = {
                    x: j.y,
                    y: j.x,
                    width: j.height,
                    height: j.width
                }
            }
            var q = this._calcGroupOffsets(p, c);
            if (!q || q.length <= p) {
                return
            }
            var r = o.bands[l];
            var g = q.bands[l];
            var n = g.from;
            var m = g.to;
            var e = Math.abs(n - m);
            var i = {
                x: c.x,
                y: Math.min(n, m),
                width: c.width,
                height: e
            };
            if (o.orientation == "horizontal") {
                var d = i.x;
                i.x = i.y;
                i.y = d;
                d = i.width;
                i.width = i.height;
                i.height = d
            }
            var k = this.renderer.rect(i.x, i.y, i.width, i.height);
            var b = r.color || "#AAAAAA";
            var f = r.opacity;
            if (isNaN(f) || f < 0 || f > 1) {
                f = 0.5
            }
            this.renderer.attr(k, {
                fill: b,
                "fill-opacity": f,
                stroke: b,
                "stroke-width": 0
            })
        },
        _renderColumnSeries: function (g, I) {

            var u = this.seriesGroups[g];
            if (!u.series || u.series.length == 0) {
                return
            }
            var C = u.type.indexOf("stacked") != -1;
            var d = C && u.type.indexOf("100") != -1;
            var G = u.type.indexOf("range") != -1;
            var o = this._getDataLen(g);
            var P = u.columnsGapPercent;
            if (isNaN(P) || P < 0 || P > 100) {
                P = 25
            }
            var Q = u.seriesGapPercent;
            if (isNaN(Q) || Q < 0 || Q > 100) {
                Q = 10
            }
            var v = u.orientation == "horizontal";
            var m = I;
            if (v) {
                m = {
                    x: I.y,
                    y: I.x,
                    width: I.height,
                    height: I.width
                }
            }
            var p = this._calcGroupOffsets(g, m);
            if (!p || p.xoffsets.length == 0) {
                return
            }
            var f = this._getColumnGroupsCount(u.orientation);
            var c = this._getColumnGroupIndex(g);
            if (this.columnSeriesOverlap == true) {
                f = 1;
                c = 0
            }
            for (var j = 0; j < u.series.length; j++) {
                var O = u.series[j];
                //var K = O.columnsMaxWidth || u.columnsMaxWidth;
                //edit by Thach
                var K = 2; // set width coulums
                var B = O.dataField;
                var M = this._getAnimProps(g, j);
                var D = M.enabled && p.xoffsets.length < 100 ? M.duration : 0;
                var T = this._alignValuesWithTicks(g);
                var A = [];
                for (var R = p.xoffsets.first; R <= p.xoffsets.last; R++) {
                    var E = this._getDataValueAsNumber(R, B, g);
                    if (typeof (E) != "number") {
                        continue
                    }
                    var t = p.xoffsets.data[R];
                    var e = p.xoffsets.itemWidth;
                    if (T) {
                        t -= e / 2
                    }
                    t += e * (c / f);
                    e /= f;
                    var r = t + e;
                    var H = (r - t + 1);
                    var J = (r - t + 1) / (1 + P / 100);
                    var n = (!C && u.series.length > 1) ? (J * Q / 100) / (u.series.length - 1) : 0;
                    var F = (J - n * (u.series.length - 1));
                    if (J < 1) {
                        J = 1
                    }
                    var k = 0;
                    if (!C && u.series.length > 1) {
                        F /= u.series.length;
                        k = j
                    }
                    var N = t + (H - J) / 2 + k * (n + F);
                    if (k == u.series.length) {
                        F = H - t + J - N
                    }
                    if (!isNaN(K)) {
                        var L = Math.min(F, K);
                        N = N + (F - L) / 2;
                        F = L
                    }
                    var U = p.offsets[j][R].to;
                    var q = p.offsets[j][R].from;
                    var w = p.baseOffset;
                    var S = q - U;
                    var b = {
                        x: I.x + N,
                        y: Math.min(U, q),
                        width: F,
                        height: Math.abs(S)
                    };
                    if (v) {
                        b = {
                            height: F,
                            y: I.y + N
                        };
                        b.x = q;
                        b.width = Math.abs(S);
                        if (S > 0) {
                            b.x -= S
                        }
                    }
                    A.push({
                        itemIndex: R,
                        rect: b,
                        size: S,
                        vertical: !v
                    })
                }
                var z = {
                    groupIndex: g,
                    seriesIndex: j,
                    items: A
                };
                this._animateColumns(z, D == 0 ? 1 : 0);
                var l = this;
                this._enqueueAnimation("series", undefined, undefined, D, function (i, h, s) {
                    l._animateColumns(h, s)
                }, z)
            }
        },
        _calcStackedItemSize: function (o, m, f, h) {
            var e = this._renderData[o];
            var l = Math.abs(e.offsets[m][f].to - e.offsets[m][f].from);
            if (isNaN(l)) {
                return 0
            }
            var g = 0,
                n = 0;
            for (var c = 0; c < e.offsets.length; c++) {
                var i = Math.abs(e.offsets[c][f].to - e.offsets[c][f].from);
                if (isNaN(i)) {
                    continue
                }
                if (e.offsets[c][f].to < e.baseOffset) {
                    g += i
                } else {
                    n += i
                }
            }
            var b = n * h;
            var k = g * h;
            g = 0;
            n = 0;
            var j = 0;
            for (var c = 0; c <= m; c++) {
                j = Math.abs(e.offsets[c][f].to - e.offsets[c][f].from);
                if (isNaN(j)) {
                    continue
                }
                if (e.offsets[c][f].to < e.baseOffset) {
                    g += j
                } else {
                    n += j
                }
            }
            if (e.offsets[m][f].to >= e.baseOffset) {
                g = n;
                k = b
            }
            if (k < g - l) {
                return 0
            }
            if (k >= g) {
                return l
            }
            return k - (g - l)
        },
        _animateColumns: function (c, f) {
            var n = c.groupIndex;
            var l = c.seriesIndex;
            var m = this.seriesGroups[n];
            var p = m.series[l];
            var d = this._getSerieSettings(n, l);
            var b = d.colors;
            var k = m.type.indexOf("stacked") != -1;
            var g = c.items;
            for (var e = 0; e < g.length; e++) {
                var h = g[e].rect;
                var o = a.jqx._ptrnd(g[e].size * f);
                if (k) {
                    o = this._calcStackedItemSize(n, l, e, f);
                    if (g[e].size < 0) {
                        o *= -1
                    }
                }
                if (isNaN(o)) {
                    continue
                }
                if (g[e].element == undefined) {
                    g[e].element = this.renderer.rect(h.x, h.y, g[e].vertical ? h.width : 0, g[e].vertical ? 0 : h.height);
                    this.renderer.attr(g[e].element, {
                        fill: b.fillColor,
                        "fill-opacity": d.opacity,
                        stroke: b.lineColor,
                        "stroke-width": d.stroke,
                        "stroke-dasharray": d.dashStyle
                    })
                }
                o = Math.abs(o);
                if (o == 0) {
                    this.renderer.attr(g[e].element, {
                        display: "none"
                    })
                } else {
                    this.renderer.attr(g[e].element, {
                        display: "block"
                    })
                } if (g[e].vertical == true) {
                    if (g[e].size < 0) {
                        this.renderer.attr(g[e].element, {
                            height: o
                        })
                    } else {
                        this.renderer.attr(g[e].element, {
                            y: h.y + h.height - o,
                            height: o
                        })
                    }
                } else {
                    if (g[e].size < 0) {
                        this.renderer.attr(g[e].element, {
                            width: o
                        })
                    } else {
                        this.renderer.attr(g[e].element, {
                            x: h.x + h.width - o,
                            width: o
                        })
                    }
                } if (f == 1) {
                    this._installHandlers(g[e].element, n, l, g[e].itemIndex);
                    var j = this._showLabel(n, l, g[e].itemIndex, h)
                }
            }
        },
        _renderScatterSeries: function (f, d) {
            var l = this.seriesGroups[f];
            if (!l.series || l.series.length == 0) {
                return
            }
            var c = l.type == "bubble";
            var e = l.orientation == "horizontal";
            var G = d;
            if (e) {
                G = {
                    x: d.y,
                    y: d.x,
                    width: d.height,
                    height: d.width
                }
            }
            var u = this._calcGroupOffsets(f, G);
            if (!u || u.xoffsets.length == 0) {
                return
            }
            var j = this._alignValuesWithTicks(f);
            for (var w = 0; w < l.series.length; w++) {
                var F = this._getSerieSettings(f, w);
                var n = F.colors;
                var p = l.series[w];
                var B = p.dataField;
                var z = NaN,
                    C = NaN;
                if (c) {
                    for (var D = u.xoffsets.first; D <= u.xoffsets.last; D++) {
                        var J = this._getDataValueAsNumber(D, p.radiusDataField, f);
                        if (typeof (J) != "number") {
                            throw "Invalid radiusDataField value at [" + D + "]"
                        }
                        if (isNaN(z) || J < z) {
                            z = J
                        }
                        if (isNaN(C) || J > C) {
                            C = J
                        }
                    }
                }
                var k = p.minRadius;
                if (isNaN(k)) {
                    k = d.width / 50
                }
                var h = p.maxRadius;
                if (isNaN(h)) {
                    h = d.width / 25
                }
                if (k > h) {
                    throw "Invalid settings: minRadius must be less than or equal to maxRadius"
                }
                var g = p.radius || 5;
                var A = this._getAnimProps(f, w);
                var b = A.enabled && u.xoffsets.length < 5000 ? A.duration : 0;
                for (var D = u.xoffsets.first; D <= u.xoffsets.last; D++) {
                    var J = this._getDataValueAsNumber(D, B, f);
                    if (typeof (J) != "number") {
                        continue
                    }
                    var o = u.xoffsets.data[D];
                    var m = u.offsets[w][D].to;
                    if (isNaN(o) || isNaN(m)) {
                        continue
                    }
                    if (e) {
                        var H = o;
                        o = m;
                        m = H + d.y
                    } else {
                        o += d.x
                    }
                    o = a.jqx._ptrnd(o);
                    m = a.jqx._ptrnd(m);
                    var q = g;
                    if (c) {
                        var I = this._getDataValueAsNumber(D, p.radiusDataField, f);
                        if (typeof (I) != "number") {
                            continue
                        }
                        q = k + (h - k) * (I - z) / Math.max(1, C - z);
                        if (isNaN(q)) {
                            q = k
                        }
                    }
                    var E = this.renderer.circle(o, m, b == 0 ? q : 0);
                    this.renderer.attr(E, {
                        fill: n.fillColor,
                        "fill-opacity": F.opacity,
                        stroke: n.lineColor,
                        "stroke-width": F.stroke,
                        "stroke-dasharray": F.dashStyle
                    });
                    var v = {
                        from: 0,
                        to: q,
                        groupIndex: f,
                        seriesIndex: w,
                        itemIndex: D,
                        x: o,
                        y: m
                    };
                    var t = this;
                    this._enqueueAnimation("series", E, undefined, b, function (s, i, L) {
                        t._animR(s, i, L);
                        if (L >= 1) {
                            var K = c ? i.to : 0;
                            t._showLabel(i.groupIndex, i.seriesIndex, i.itemIndex, {
                                x: i.x - K,
                                y: i.y - K,
                                width: 2 * K,
                                height: 2 * K
                            })
                        }
                    }, v);
                    this._installHandlers(E, f, w, D)
                }
            }
        },
        _animR: function (c, b, e) {
            var d = Math.round((b.to - b.from) * e + b.from);
            if (this._isVML) {
                this.renderer.updateCircle(c, undefined, undefined, d)
            } else {
                this.renderer.attr(c, {
                    r: d
                })
            }
        },
        _showToolTip: function (m, k, D, w, c) {
            var u = this._getCategoryAxis(D);
            if (this._toolTipElement && D == this._toolTipElement.gidx && w == this._toolTipElement.sidx && c == this._toolTipElement.iidx) {
                return
            }
            var g = this.enableCrosshairs;
            if (this._pointMarker) {
                m = parseInt(this._pointMarker.x + 5);
                k = parseInt(this._pointMarker.y - 5)
            } else {
                g = false
            }
            var i = g && this.showToolTips == false;
            m = a.jqx._ptrnd(m);
            k = a.jqx._ptrnd(k);
            var E = this._toolTipElement == undefined;
            var j = this.seriesGroups[D];
            var n = j.series[w];
            if (j.showToolTips == false || n.showToolTips == false) {
                return
            }
            var f = n.toolTipFormatSettings || j.toolTipFormatSettings;
            var t = n.toolTipFormatFunction || j.toolTipFormatFunction || this.toolTipFormatFunction;
            var l = this._getColors(D, w, c);
            var b = this._getDataValue(c, u.dataField, D);
            if (u.dataField == undefined || u.dataField == "") {
                b = c
            }
            if (u.type == "date") {
                b = this._castAsDate(b)
            }
            var q = "";
            if (a.isFunction(t)) {
                var v = {};
                if (j.type.indexOf("range") == -1) {
                    v = this._getDataValue(c, n.dataField, D)
                } else {
                    v.from = this._getDataValue(c, n.dataFieldFrom, D);
                    v.to = this._getDataValue(c, n.dataFieldTo, D)
                }
                q = t(v, c, n, j, b, u)
            } else {
                q = this._getFormattedValue(D, w, c, f, t);
                var I = u.toolTipFormatSettings || u.formatSettings;
                var d = u.toolTipFormatFunction || u.formatFunction;
                var H = this._formatValue(b, I, d);
                if (j.type != "pie" && j.type != "donut") {
                    q = (n.displayText || n.dataField || "") + ", " + H + ": " + q
                } else {
                    b = this._getDataValue(c, n.displayText || n.dataField, D);
                    H = this._formatValue(b, I, d);
                    q = H + ": " + q
                }
            }
            var C = n.toolTipClass || j.toolTipClass || this.toThemeProperty("jqx-chart-tooltip-text", null);
            var F = n.toolTipBackground || j.toolTipBackground || "#FFFFFF";
            var G = n.toolTipLineColor || j.toolTipLineColor || l.lineColor;
            if (!this._toolTipElement) {
                this._toolTipElement = {}
            }
            this._toolTipElement.sidx = w;
            this._toolTipElement.gidx = D;
            this._toolTipElement.iidx = c;
            rect = this.renderer.getRect();
            if (g) {
                var B = a.jqx._ptrnd(this._pointMarker.x);
                var A = a.jqx._ptrnd(this._pointMarker.y);
                if (this._toolTipElement.vLine && this._toolTipElement.hLine) {
                    this.renderer.attr(this._toolTipElement.vLine, {
                        x1: B,
                        x2: B
                    });
                    this.renderer.attr(this._toolTipElement.hLine, {
                        y1: A,
                        y2: A
                    })
                } else {
                    var z = this.crosshairsColor || "#888888";
                    this._toolTipElement.vLine = this.renderer.line(B, this._plotRect.y, B, this._plotRect.y + this._plotRect.height, {
                        stroke: z,
                        "stroke-width": this.crosshairsLineWidth || 1,
                        "stroke-dasharray": this.crosshairsDashStyle || ""
                    });
                    this._toolTipElement.hLine = this.renderer.line(this._plotRect.x, A, this._plotRect.x + this._plotRect.width, A, {
                        stroke: z,
                        "stroke-width": this.crosshairsLineWidth || 1,
                        "stroke-dasharray": this.crosshairsDashStyle || ""
                    })
                }
            }
            if (!i && this.showToolTips != false) {
                var s = !E ? this._toolTipElement.box : document.createElement("div");
                var e = {
                    left: 0,
                    top: 0
                };
                if (E) {
                    s.style.position = "absolute";
                    s.style.cursor = "default";
                    s.style.overflow = "hidden";
                    a(s).addClass("jqx-rc-all jqx-button");
                    a(document.body).append(s)
                }
                s.style.backgroundColor = F;
                s.style.borderColor = G;
                this._toolTipElement.box = s;
                this._toolTipElement.txt = q;
                var o = "<span class='" + C + "'>" + q + "<span>";
                var h = this._toolTipElement.tmp;
                if (E) {
                    this._toolTipElement.tmp = h = document.createElement("div");
                    h.style.position = "absolute";
                    h.style.cursor = "default";
                    h.style.overflow = "hidden";
                    h.style.display = "none";
                    h.style.zIndex = 999999;
                    h.style.backgroundColor = F;
                    h.style.borderColor = G;
                    a(h).addClass("jqx-rc-all jqx-button");
                    this.host.append(h)
                }
                a(h).html(o);
                var r = {
                    width: a(h).width(),
                    height: a(h).height()
                };
                r.width = r.width + 5;
                r.height = r.height + 6;
                m = Math.max(m, rect.x);
                k = Math.max(k - r.height, rect.y);
                if (r.width > rect.width || r.height > rect.height) {
                    return
                }
                if (m + e.left + r.width > rect.x + rect.width - 5) {
                    m = rect.x + rect.width - r.width - e.left - 5;
                    s.style.left = e.left + m + "px"
                }
                if (k + e.top + r.height > rect.y + rect.height - 5) {
                    k = rect.y + rect.height - r.height - 5;
                    s.style.top = e.top + k + "px"
                }
                var p = this.host.coord();
                if (E) {
                    a(s).fadeOut(0, 0);
                    s.style.left = e.left + m + p.left + "px";
                    s.style.top = e.top + k + p.top + "px"
                }
                a(s).html(o);
                a(s).clearQueue();
                a(s).fadeTo(400, 1);
                a(s).animate({
                    left: e.left + m + p.left,
                    top: e.top + k + p.top,
                    opacity: 1
                }, 200, "easeInOutCirc")
            }
        },
        _hideToolTip: function (b) {
            if (!this._toolTipElement) {
                return
            }
            if (this._toolTipElement.box) {
                if (b == 0) {
                    a(this._toolTipElement.box).hide()
                } else {
                    a(this._toolTipElement.box).fadeOut()
                }
            }
            this._hideCrosshairs();
            this._toolTipElement.gidx = undefined
        },
        _hideCrosshairs: function () {
            if (!this._toolTipElement) {
                return
            }
            if (this._toolTipElement.vLine) {
                this.renderer.removeElement(this._toolTipElement.vLine);
                this._toolTipElement.vLine = undefined
            }
            if (this._toolTipElement.hLine) {
                this.renderer.removeElement(this._toolTipElement.hLine);
                this._toolTipElement.hLine = undefined
            }
        },
        _showLabel: function (u, r, d, b, m, f, c) {
            var g = this.seriesGroups[u];
            var k = g.series[r];
            var p = {
                width: 0,
                height: 0
            };
            if (k.showLabels == false || (!k.showLabels && !g.showLabels)) {
                return p
            }
            if (b.width < 0 || b.height < 0) {
                return p
            }
            var e = k.labelAngle || k.labelsAngle || g.labelAngle || g.labelsAngle || 0;
            var s = k.labelOffset || g.labelOffset || {
                x: 0,
                y: 0
            };
            var q = k.labelClass || g.labelClass || this.toThemeProperty("jqx-chart-label-text", null);
            m = m || "center";
            f = f || "center";
            var o = this._getFormattedValue(u, r, d);
            var l = b.width;
            var t = b.height;
            p = this.renderer.measureText(o, e, {
                "class": q
            });
            if (c) {
                return p
            }
            var j = 0;
            if (m == "" || m == "center") {
                j += (l - p.width) / 2
            } else {
                if (m == "right") {
                    j += (l - p.width)
                }
            }
            var i = 0;
            if (f == "" || f == "center") {
                i += (t - p.height) / 2
            } else {
                if (f == "bottom") {
                    i += (t - p.height)
                }
            }
            var n = this.renderer.text(o, j + b.x + s.x, i + b.y + s.y, p.width, p.height, e, {}, e != 0, "center", "center");
            this.renderer.attr(n, {
                "class": q
            });
            if (this._isVML) {
                this.renderer.removeElement(n);
                this.renderer.getContainer()[0].appendChild(n)
            }
            return n
        },
        _getAnimProps: function (j, f) {
            var e = this.seriesGroups[j];
            var c = e.series[f];
            var b = this.enableAnimations == true;
            if (e.enableAnimations) {
                b = e.enableAnimations == true
            }
            if (c.enableAnimations) {
                b = c.enableAnimations == true
            }
            var i = this.animationDuration;
            if (isNaN(i)) {
                i = 1000
            }
            var d = e.animationDuration;
            if (!isNaN(d)) {
                i = d
            }
            var h = c.animationDuration;
            if (!isNaN(h)) {
                i = h
            }
            if (i > 5000) {
                i = 1000
            }
            return {
                enabled: b,
                duration: i
            }
        },
        _renderLineSeries: function (d, F) {
            var w = this.seriesGroups[d];
            if (!w.series || w.series.length == 0) {
                return
            }
            var k = w.type.indexOf("area") != -1;
            var B = w.type.indexOf("stacked") != -1;
            var b = B && w.type.indexOf("100") != -1;
            var U = w.type.indexOf("spline") != -1;
            var l = w.type.indexOf("step") != -1;
            var D = w.type.indexOf("range") != -1;
            if (l && U) {
                return
            }
            var p = this._getDataLen(d);
            var S = F.width / p;
            var X = w.orientation == "horizontal";
            var r = this._getCategoryAxis(d).flip == true;
            var o = F;
            if (X) {
                o = {
                    x: F.y,
                    y: F.x,
                    width: F.height,
                    height: F.width
                }
            }
            var t = this._calcGroupOffsets(d, o);
            if (!t || t.xoffsets.length == 0) {
                return
            }
            var H = this._alignValuesWithTicks(d);
            for (var N = w.series.length - 1; N >= 0; N--) {
                var V = this._getSerieSettings(d, N);
                var K = t.xoffsets.first;
                var v = K;
                do {
                    var L = [];
                    var J = [];
                    var E = -1;
                    var h = 0;
                    var G = NaN;
                    var u = NaN;
                    var W = NaN;
                    if (t.xoffsets.length < 1) {
                        continue
                    }
                    var I = this._getAnimProps(d, N);
                    var C = I.enabled && t.xoffsets.length < 10000 && this._isVML != true ? I.duration : 0;
                    var n = K;
                    var m = false;
                    for (var T = K; T <= t.xoffsets.last; T++) {
                        K = T;
                        var M = t.xoffsets.data[T];
                        if (M == undefined) {
                            continue
                        }
                        var g = t.offsets[N][T].to;
                        var R = t.offsets[N][T].from;
                        if (isNaN(g) || isNaN(R)) {
                            K++;
                            m = true;
                            break
                        }
                        v = T;
                        if (!k && b) {
                            if (g <= o.y) {
                                g = o.y + 1
                            }
                            if (g >= o.y + o.height) {
                                g = o.y + o.height - 1
                            }
                            if (R <= o.y) {
                                R = o.y + 1
                            }
                            if (R >= o.y + o.height) {
                                R = o.y + o.h
                            }
                        }
                        M = Math.max(M, 1);
                        h = M;
                        if (l && !isNaN(G) && !isNaN(u)) {
                            if (u != g) {
                                L.push(X ? {
                                    y: o.x + h,
                                    x: a.jqx._ptrnd(u)
                                } : {
                                    x: o.x + h,
                                    y: a.jqx._ptrnd(u)
                                })
                            }
                        }
                        L.push(X ? {
                            y: o.x + h,
                            x: a.jqx._ptrnd(g),
                            index: T
                        } : {
                            x: o.x + h,
                            y: a.jqx._ptrnd(g),
                            index: T
                        });
                        J.push(X ? {
                            y: o.x + h,
                            x: a.jqx._ptrnd(R),
                            index: T
                        } : {
                            x: o.x + h,
                            y: a.jqx._ptrnd(R),
                            index: T
                        });
                        G = h;
                        u = g;
                        if (isNaN(W)) {
                            W = g
                        }
                    }
                    var e = o.x + t.xoffsets.data[n];
                    var P = o.x + t.xoffsets.data[v];
                    if (k && w.alignEndPointsWithIntervals == true) {
                        var q = r ? -1 : 1;
                        if (e > o.x) {
                            e = o.x
                        }
                        if (P < o.x + o.width) {
                            P = o.x + o.width
                        }
                        if (r) {
                            var O = e;
                            e = P;
                            P = O
                        }
                    }
                    P = a.jqx._ptrnd(P);
                    e = a.jqx._ptrnd(e);
                    var f = t.baseOffset;
                    W = a.jqx._ptrnd(W);
                    var c = a.jqx._ptrnd(g) || f;
                    if (D) {
                        L = L.concat(J.reverse())
                    }
                    var A = this._calculateLine(L, f, C == 0 ? 1 : 0, k, X);
                    if (A != "") {
                        A = this._buildLineCmd(A, D, e, P, W, c, f, k, U && L.length > 3, X)
                    } else {
                        A = "M 0 0"
                    }
                    var Q = this.renderer.path(A, {
                        "stroke-width": V.stroke,
                        stroke: V.colors.lineColor,
                        "fill-opacity": V.opacity,
                        "stroke-dasharray": V.dashStyle,
                        fill: k ? V.colors.fillColor : "none"
                    });
                    this._installHandlers(Q, d, N);
                    var z = {
                        groupIndex: d,
                        seriesIndex: N,
                        pointsArray: L,
                        left: e,
                        right: P,
                        pyStart: W,
                        pyEnd: c,
                        yBase: f,
                        isArea: k,
                        isSpline: U
                    };
                    var j = this;
                    this._enqueueAnimation("series", Q, undefined, C, function (aa, s, ab) {
                        var ac = j._calculateLine(s.pointsArray, s.yBase, ab, s.isArea, X);
                        if (ac == "") {
                            return
                        }
                        var Z = s.pointsArray.length;
                        if (!s.isArea) {
                            Z = Math.round(Z * ab)
                        }
                        ac = j._buildLineCmd(ac, D, s.left, s.right, s.pyStart, s.pyEnd, s.yBase, s.isArea, Z > 3 && s.isSpline, X);
                        j.renderer.attr(aa, {
                            d: ac
                        });
                        if (ab == 1) {
                            var ad = j._getSerieSettings(s.groupIndex, s.seriesIndex);
                            for (var Y = 0; Y < s.pointsArray.length; Y++) {
                                j._showLabel(s.groupIndex, s.seriesIndex, s.pointsArray[Y].index, {
                                    x: s.pointsArray[Y].x,
                                    y: s.pointsArray[Y].y,
                                    width: 0,
                                    height: 0
                                });
                                j._drawSymbol(j._getSymbol(s.groupIndex, s.seriesIndex), s.pointsArray[Y].x, s.pointsArray[Y].y, ad.colors.fillColor, ad.colors.lineColor, 1, ad.opacity)
                            }
                        }
                    }, z)
                } while (K < t.xoffsets.length - 1 || m)
            }
        },
        _calculateLine: function (f, l, g, h, e) {
            var c = "";
            var b = f.length;
            if (!h) {
                b = Math.round(b * g)
            }
            for (var d = 0; d < b; d++) {
                if (d > 0) {
                    c += " "
                }
                var j = f[d].y;
                var k = f[d].x;
                if (h) {
                    if (e) {
                        k = a.jqx._ptrnd((k - l) * g + l)
                    } else {
                        j = a.jqx._ptrnd((j - l) * g + l)
                    }
                }
                c += k + "," + j;
                if (b == 1) {
                    c += " " + (k + 2) + "," + (j + 2)
                }
            }
            return c
        },
        _buildLineCmd: function (m, k, g, q, p, b, r, o, d, l) {
            var f = m;
            if (d) {
                f = this._getBezierPoints(m)
            }
            var n = f.split(" ");
            var j = n[0].replace("C", "");
            if (o) {
                if (!k) {
                    var e = l ? p + "," + g : g + "," + p;
                    var h = l ? b + "," + q : q + "," + b;
                    var c = l ? r + "," + g : g + "," + r;
                    var i = l ? r + "," + q : q + "," + r;
                    f = "M " + c + " L " + j + (d ? "" : (" L " + j + " ")) + f + (d ? (" L" + i + " M " + i) : (" " + i + " " + c)) + " Z"
                } else {
                    f = "M " + j + " L " + j + (d ? "" : (" L " + j + " ")) + f + " Z"
                }
            } else {
                if (d) {
                    f = "M " + j + " " + f
                } else {
                    f = "M " + j + " L " + j + " " + f
                }
            }
            return f
        },
        _getSerieSettings: function (i, c) {
            var h = this.seriesGroups[i];
            var g = h.type.indexOf("area") != -1;
            var f = h.type.indexOf("line") != -1;
            var b = this._getColors(i, c, undefined, this._getGroupGradientType(i));
            var d = h.series[c];
            var k = d.dashStyle || h.dashStyle || "";
            var e = d.opacity || h.opacity;
            if (isNaN(e) || e < 0 || e > 1) {
                e = 1
            }
            var j = d.lineWidth;
            if (isNaN(j) && j != "auto") {
                j = h.lineWidth
            }
            if (j == "auto" || isNaN(j) || j < 0 || j > 15) {
                if (g) {
                    j = 2
                } else {
                    if (f) {
                        j = 3
                    } else {
                        j = 1
                    }
                }
            }
            return {
                colors: b,
                stroke: j,
                opacity: e,
                dashStyle: k
            }
        },
        getItemColor: function (f, d, c) {
            var g = -1;
            for (var b = 0; b < this.seriesGroups.length; b++) {
                if (this.seriesGroups[b] == f) {
                    g = b;
                    break
                }
            }
            if (g == -1) {
                return "#000000"
            }
            var e = -1;
            for (var b = 0; b < this.seriesGroups[g].series.length; b++) {
                if (this.seriesGroups[g].series[b] == d) {
                    e = b;
                    break
                }
            }
            if (e == -1) {
                return "#000000"
            }
            return this._getColors(g, e, c)
        },
        _getColors: function (s, p, d, e) {
            var l = this.seriesGroups[s];
            if (l.type != "pie" && l.type != "donut") {
                d = undefined
            }
            var c = l.series[p].useGradient || l.useGradient;
            if (c == undefined) {
                c = true
            }
            var q;
            if (!isNaN(d)) {
                var k = this._getDataLen(s);
                q = this._getColor(l.series[p].colorScheme || l.colorScheme || this.colorScheme, p * k + d, s, p)
            } else {
                q = this._getSeriesColor(s, p)
            }
            var f = a.jqx._adjustColor(q, 1.1);
            var b = a.jqx._adjustColor(q, 0.9);
            var m = a.jqx._adjustColor(f, 0.9);
            var h = q;
            var n = f;
            var i = [
                [0, 1.5],
                [100, 1]
            ];
            var g = [
                [0, 1],
                [25, 1.1],
                [50, 1.5],
                [100, 1]
            ];
            var o = [
                [0, 1.3],
                [90, 1.2],
                [100, 1]
            ];
            if (c) {
                if (e == "verticalLinearGradient") {
                    h = this.renderer._toLinearGradient(q, true, i);
                    n = this.renderer._toLinearGradient(f, true, i)
                } else {
                    if (e == "horizontalLinearGradient") {
                        h = this.renderer._toLinearGradient(q, false, g);
                        n = this.renderer._toLinearGradient(f, false, g)
                    } else {
                        if (e == "radialGradient") {
                            var r = undefined;
                            var j = i;
                            if ((l.type == "pie" || l.type == "donut") && d != undefined && this._renderData[s] && this._renderData[s][p]) {
                                r = this._renderData[s][p][d];
                                j = o
                            }
                            h = this.renderer._toRadialGradient(q, j, r);
                            n = this.renderer._toRadialGradient(f, j, r)
                        }
                    }
                }
            }
            return {
                baseColor: q,
                fillColor: h,
                lineColor: b,
                fillSelected: n,
                lineSelected: m
            }
        },
        _installHandlers: function (d, j, i, c) {
            var b = this;
            var h = this.seriesGroups[j];
            var e = this.seriesGroups[j].series[i];
            var f = h.type.indexOf("line") != -1 || h.type.indexOf("area") != -1;
            if (!f) {
                this.renderer.addHandler(d, "mousemove", function (k) {
                    k.preventDefault();
                    var g = k.pageX || k.clientX || k.screenX;
                    var m = k.pageY || k.clientY || k.screenY;
                    var l = b.host.offset();
                    g -= l.left;
                    m -= l.top;
                    if (b._mouseX == g && b._mouseY == m) {
                        return
                    }
                    if (b._toolTipElement) {
                        if (b._toolTipElement.gidx == j && b._toolTipElement.sidx == i && b._toolTipElement.iidx == c) {
                            return
                        }
                    }
                    b._startTooltipTimer(j, i, c)
                })
            }
            this.renderer.addHandler(d, "mouseover", function (g) {
                g.preventDefault();
                b._select(d, j, i, c);
                if (f) {
                    return
                }
                if (isNaN(c)) {
                    return
                }
                b._raiseEvent("mouseover", h, e, c)
            });
            this.renderer.addHandler(d, "mouseout", function (g) {
                g.preventDefault();
                if (c != undefined) {
                    b._cancelTooltipTimer()
                }
                if (f) {
                    return
                }
                b._unselect();
                if (isNaN(c)) {
                    return
                }
                b._raiseEvent("mouseout", h, e, c)
            });
            this.renderer.addHandler(d, "click", function (g) {
                g.preventDefault();
                if (f) {
                    return
                }
                if (h.type.indexOf("column") != -1) {
                    b._unselect()
                }
                if (isNaN(c)) {
                    return
                }
                b._raiseEvent("click", h, e, c)
            })
        },
        _getHorizontalOffset: function (u, p, j, h) {
            var c = this._plotRect;
            var f = this._getDataLen(u);
            if (f == 0) {
                return {
                    index: undefined,
                    value: j
                }
            }
            var n = this._calcGroupOffsets(u, this._plotRect);
            if (n.xoffsets.length == 0) {
                return {
                    index: undefined,
                    value: undefined
                }
            }
            var l = j - c.x;
            var k = h - c.y;
            var r = this.seriesGroups[u];
            if (r.orientation == "horizontal") {
                var t = l;
                l = k;
                k = t
            }
            var e = this._getCategoryAxis(u).flip == true;
            var b = undefined;
            var m = undefined;
            for (var q = n.xoffsets.first; q <= n.xoffsets.last; q++) {
                var s = n.xoffsets.data[q];
                var d = n.offsets[p][q].to;
                var o = Math.abs(l - s);
                if (isNaN(b) || b > o) {
                    b = o;
                    m = q
                }
            }
            return {
                index: m,
                value: n.xoffsets.data[m]
            }
        },
        onmousemove: function (l, k) {
            if (this._mouseX == l && this._mouseY == k) {
                return
            }
            this._mouseX = l;
            this._mouseY = k;
            if (!this._selected) {
                return
            }
            var b = this._plotRect;
            var j = this._paddedRect;
            if (l < j.x || l > j.x + j.width || k < j.y || k > j.y + j.height) {
                this._unselect();
                return
            }
            var A = this._selected.group;
            var v = this.seriesGroups[A];
            var o = v.series[this._selected.series];
            var c = v.orientation == "horizontal";
            var f = this.seriesGroups[A].type;
            var b = this._plotRect;
            if (f.indexOf("line") != -1 || f.indexOf("area") != -1) {
                var h = this._getHorizontalOffset(A, this._selected.series, l, k);
                var u = h.index;
                if (u == undefined) {
                    return
                }
                if (this._selected.item != u) {
                    if (this._selected.item) {
                        this._raiseEvent("mouseout", v, o, this._selected.item)
                    }
                    this._selected.item = u;
                    this._raiseEvent("mouseover", v, o, u)
                }
                var n = this._getSymbol(this._selected.group, this._selected.series);
                if (n == "none") {
                    n = "circle"
                }
                var p = this._calcGroupOffsets(A, b);
                var e = p.offsets[this._selected.series][u].to;
                var q = e;
                if (v.type.indexOf("range") != -1) {
                    q = p.offsets[this._selected.series][u].from
                }
                var m = c ? l : k;
                if (!isNaN(q) && Math.abs(m - q) < Math.abs(m - e)) {
                    k = q
                } else {
                    k = e
                } if (isNaN(k)) {
                    return
                }
                l = h.value;
                if (c) {
                    var w = l;
                    l = k;
                    k = w + b.y
                } else {
                    l += b.x
                }
                k = a.jqx._ptrnd(k);
                l = a.jqx._ptrnd(l);
                if (this._pointMarker) {
                    this.renderer.removeElement(this._pointMarker.element)
                }
                if (isNaN(l) || isNaN(k)) {
                    return
                }
                var t = this._getSeriesColor(this._selected.group, this._selected.series);
                var r = a.jqx._adjustColor(t, 0.5);
                var d = o.opacity;
                if (isNaN(d) || d < 0 || d > 1) {
                    d = v.opacity
                }
                if (isNaN(d) || d < 0 || d > 1) {
                    d = 1
                }
                var z = o.symbolSize;
                if (isNaN(z) || z > 10 || z < 0) {
                    z = v.symbolSize
                }
                if (isNaN(z) || z > 10 || z < 0) {
                    z = 8
                }
                this._pointMarker = {
                    type: n,
                    x: l,
                    y: k,
                    gidx: A,
                    sidx: this._selected.series,
                    iidx: u
                };
                this._pointMarker.element = this._drawSymbol(n, l, k, t, r, 1, d, z);
                this._startTooltipTimer(A, this._selected.series, u)
            }
        },
        _drawSymbol: function (g, i, h, j, k, d, e, m) {
            var c;
            var f = m || 6;
            var b = f / 2;
            switch (g) {
            case "none":
                return undefined;
            case "circle":
                c = this.renderer.circle(i, h, f / 2);
                break;
            case "square":
                f = f - 1;
                b = f / 2;
                c = this.renderer.rect(i - b, h - b, f, f);
                break;
            case "diamond":
                var l = "M " + (i - b) + "," + (h) + " L " + (i) + "," + (h + b) + " L " + (i + b) + "," + (h) + " L " + (i) + "," + (h - b) + " Z";
                c = this.renderer.path(l);
                break;
            case "triangle_up":
                var l = "M " + (i - b) + "," + (h + b) + " L " + (i + b) + "," + (h + b) + " L " + (i) + "," + (h - b) + " Z";
                c = this.renderer.path(l);
                break;
            case "triangle_down":
                var l = "M " + (i - b) + "," + (h - b) + " L " + (i) + "," + (h + b) + " L " + (i + b) + "," + (h - b) + " Z";
                c = this.renderer.path(l);
                break;
            case "triangle_left":
                var l = "M " + (i - b) + "," + (h) + " L " + (i + b) + "," + (h + b) + " L " + (i + b) + "," + (h - b) + " Z";
                c = this.renderer.path(l);
                break;
            case "triangle_right":
                var l = "M " + (i - b) + "," + (h - b) + " L " + (i - b) + "," + (h + b) + " L " + (i + b) + "," + (h) + " Z";
                c = this.renderer.path(l);
                break;
            default:
                c = this.renderer.circle(i, h, f)
            }
            this.renderer.attr(c, {
                fill: j,
                stroke: k,
                "stroke-width": d,
                "fill-opacity": e
            });
            return c
        },
        _getSymbol: function (f, b) {
            var c = ["circle", "square", "diamond", "triangle_up", "triangle_down", "triangle_left", "triangle_right"];
            var e = this.seriesGroups[f];
            var d = e.series[b];
            var h = undefined;
            if (d.symbolType != undefined) {
                h = d.symbolType
            }
            if (h == undefined) {
                h = e.symbolType
            }
            if (h == "default") {
                return c[b % c.length]
            } else {
                if (h != undefined) {
                    return h
                }
            }
            return "none"
        },
        _startTooltipTimer: function (h, f, d) {
            this._cancelTooltipTimer();
            var b = this;
            var e = b.seriesGroups[h];
            var c = this.toolTipShowDelay || this.toolTipDelay;
            if (isNaN(c) || c > 10000 || c < 0) {
                c = 500
            }
            if (this._toolTipElement || (true == this.enableCrosshairs && false == this.showToolTips)) {
                c = 0
            }
            clearTimeout(this._tttimerHide);
            this._tttimer = setTimeout(function () {
                b._showToolTip(b._mouseX, b._mouseY - 3, h, f, d);
                var g = b.toolTipHideDelay;
                if (isNaN(g)) {
                    g = 4000
                }
                b._tttimerHide = setTimeout(function () {
                    b._hideToolTip()
                }, g)
            }, c)
        },
        _cancelTooltipTimer: function () {
            clearTimeout(this._tttimer)
        },
        _getGroupGradientType: function (c) {
            var b = this.seriesGroups[c];
            if (b.type.indexOf("area") != -1) {
                return b.orientation == "horizontal" ? "horizontalLinearGradient" : "verticalLinearGradient"
            } else {
                if (b.type.indexOf("column") != -1) {
                    return b.orientation == "horizontal" ? "verticalLinearGradient" : "horizontalLinearGradient"
                } else {
                    if (b.type.indexOf("scatter") != -1 || b.type.indexOf("bubble") != -1 || b.type.indexOf("pie") != -1 || b.type.indexOf("donut") != -1) {
                        return "radialGradient"
                    }
                }
            }
            return undefined
        },
        _select: function (d, h, f, c) {
            if (this._selected && this._selected.element != d) {
                this._unselect()
            }
            this._selected = {
                element: d,
                group: h,
                series: f,
                item: c
            };
            var e = this.seriesGroups[h];
            var b = this._getColors(h, f, c, this._getGroupGradientType(h));
            if (e.type.indexOf("line") != -1 && e.type.indexOf("area") == -1) {
                b.fillSelected = "none"
            }
            this.renderer.attr(d, {
                stroke: b.lineSelected,
                fill: b.fillSelected
            })
        },
        _unselect: function () {
            if (this._selected) {
                var h = this._selected.group;
                var f = this._selected.series;
                var c = this._selected.item;
                var e = this.seriesGroups[h];
                var d = e.series[f];
                var b = this._getColors(h, f, c, this._getGroupGradientType(h));
                if (e.type.indexOf("line") != -1 && e.type.indexOf("area") == -1) {
                    b.fillColor = "none"
                }
                this.renderer.attr(this._selected.element, {
                    stroke: b.lineColor,
                    fill: b.fillColor
                });
                if (e.type.indexOf("line") != -1 || e.type.indexOf("area") != -1 && !isNaN(c)) {
                    this._raiseEvent("mouseout", e, d, c)
                }
                this._selected = undefined
            }
            if (this._pointMarker) {
                this.renderer.removeElement(this._pointMarker.element);
                this._pointMarker = undefined;
                this._hideCrosshairs()
            }
        },
        _raiseEvent: function (e, f, d, b) {
            var c = d[e] || f[e];
            var g = 0;
            for (; g < this.seriesGroups.length; g++) {
                if (this.seriesGroups[g] == f) {
                    break
                }
            }
            if (g == this.seriesGroups.length) {
                return
            }
            if (c && a.isFunction(c)) {
                c({
                    event: e,
                    seriesGroup: f,
                    serie: d,
                    elementIndex: b,
                    elementValue: this._getDataValue(b, d.dataField, g)
                })
            }
        },
        _calcGroupOffsets: function (d, E) {
            var q = this.seriesGroups[d];
            if (!q.series || q.series.length == 0) {
                return
            }
            var r = q.valueAxis.flip == true;
            var H = q.valueAxis.logarithmicScale == true;
            var G = q.valueAxis.logarithmicScaleBase || 10;
            if (!this._renderData) {
                this._renderData = new Array()
            }
            while (this._renderData.length < d + 1) {
                this._renderData.push(null)
            }
            if (this._renderData[d] != null) {
                return this._renderData[d]
            }
            var L = new Array();
            var w = q.type.indexOf("stacked") != -1;
            var c = w && q.type.indexOf("100") != -1;
            var D = q.type.indexOf("range") != -1;
            var l = this._getDataLen(d);
            var g = q.baselineValue || q.valueAxis.baselineValue || 0;
            if (D) {
                g = 0
            }
            var S = this._stats.seriesGroups[d];
            if (!S || !S.isValid) {
                return
            }
            if (g > S.max) {
                g = S.max
            }
            if (g < S.min) {
                g = S.min
            }
            var f = (c || H) ? S.maxRange : S.max - S.min;
            var W = S.min;
            var u = S.max;
            var F = E.height / (H ? S.intervals : f);
            var U = 0;
            if (c) {
                if (W * u < 0) {
                    f /= 2;
                    U = -(f + g) * F
                } else {
                    U = -g * F
                }
            } else {
                U = -(g - W) * F
            } if (r) {
                U = E.y - U
            } else {
                U += E.y + E.height
            }
            var T = new Array();
            var Q = new Array();
            var V, A;
            if (H) {
                V = a.jqx.log(u, G) - a.jqx.log(g, G);
                if (w) {
                    V = S.intervals;
                    g = c ? 0 : W
                }
                A = S.intervals - V;
                if (!r) {
                    U = E.y + V / S.intervals * E.height
                }
            }
            U = a.jqx._ptrnd(U);
            var b = (W * u < 0) ? E.height / 2 : E.height;
            var e = [];
            var t = [];
            if (q.bands) {
                for (var O = 0; O < q.bands.length; O++) {
                    var n = q.bands[O].minValue;
                    var Z = q.bands[O].maxValue;
                    var ab = 0;
                    var aa = 0;
                    if (H) {
                        ab = (a.jqx.log(n, G) - a.jqx.log(g, G)) * F;
                        aa = (a.jqx.log(Z, G) - a.jqx.log(g, G)) * F
                    } else {
                        ab = (n - g) * F;
                        aa = (Z - g) * F
                    } if (this._isVML) {
                        ab = Math.round(ab);
                        aa = Math.round(aa)
                    } else {
                        ab = a.jqx._ptrnd(ab) - 1;
                        aa = a.jqx._ptrnd(aa) - 1
                    } if (r) {
                        t.push({
                            from: U + aa,
                            to: U + ab
                        })
                    } else {
                        t.push({
                            from: U - aa,
                            to: U - ab
                        })
                    }
                }
            }
            for (var O = 0; O < q.series.length; O++) {
                if (!w && H) {
                    e = []
                }
                var v = q.series[O].dataField;
                var X = q.series[O].dataFieldFrom;
                var I = q.series[O].dataFieldTo;
                L.push(new Array());
                for (var P = 0; P < l; P++) {
                    var Y = NaN;
                    if (D) {
                        Y = this._getDataValueAsNumber(P, X, d);
                        if (isNaN(Y)) {
                            Y = g
                        }
                    }
                    var C = NaN;
                    if (D) {
                        C = this._getDataValueAsNumber(P, I, d)
                    } else {
                        C = this._getDataValueAsNumber(P, v, d)
                    } if (isNaN(C) || (H && C <= 0)) {
                        L[O].push({
                            from: undefined,
                            to: undefined
                        });
                        continue
                    }
                    if (C > S.rmax) {
                        C = S.rmax
                    }
                    if (C < S.rmin) {
                        C = S.rmin
                    }
                    var B = (C > g) ? T : Q;
                    var R = F * (C - g);
                    if (D) {
                        R = F * (C - Y)
                    }
                    if (H) {
                        while (e.length <= P) {
                            e.push({
                                p: {
                                    value: 0,
                                    height: 0
                                },
                                n: {
                                    value: 0,
                                    height: 0
                                }
                            })
                        }
                        var s = D ? Y : g;
                        var N = C > s ? e[P].p : e[P].n;
                        N.value += C;
                        if (c) {
                            C = N.value / (S.psums[P] + S.nsums[P]) * 100;
                            R = (a.jqx.log(C, G) - S.minPow) * F
                        } else {
                            R = a.jqx.log(N.value, G) - a.jqx.log(s, G);
                            R *= F
                        }
                        R -= N.height;
                        N.height += R
                    }
                    var K = U;
                    if (D) {
                        var m = 0;
                        if (H) {
                            m = (a.jqx.log(Y, G) - a.jqx.log(g, G)) * F
                        } else {
                            m = (Y - g) * F
                        }
                        K += r ? m : -m
                    }
                    if (w) {
                        if (c && !H) {
                            var p = (S.psums[P] - S.nsums[P]);
                            if (C > g) {
                                R = (S.psums[P] / p) * b;
                                if (S.psums[P] != 0) {
                                    R *= C / S.psums[P]
                                }
                            } else {
                                R = (S.nsums[P] / p) * b;
                                if (S.nsums[P] != 0) {
                                    R *= C / S.nsums[P]
                                }
                            }
                        }
                        if (isNaN(B[P])) {
                            B[P] = K
                        }
                        K = B[P]
                    }
                    R = Math.abs(R);
                    h_new = this._isVML ? Math.round(R) : a.jqx._ptrnd(R) - 1;
                    if (Math.abs(R - h_new) > 0.5) {
                        R = Math.round(R)
                    } else {
                        R = h_new
                    } if (O == q.series.length - 1 && c) {
                        var o = 0;
                        for (var M = 0; M < O; M++) {
                            o += Math.abs(L[M][P].to - L[M][P].from)
                        }
                        o += R;
                        if (o < b) {
                            if (R > 0.5) {
                                R = a.jqx._ptrnd(R + b - o)
                            } else {
                                var M = O - 1;
                                while (M >= 0) {
                                    var z = Math.abs(L[M][P].to - L[M][P].from);
                                    if (z > 1) {
                                        if (L[M][P].from > L[M][P].to) {
                                            L[M][P].from += b - o
                                        }
                                        break
                                    }
                                    M--
                                }
                            }
                        }
                    }
                    if (r) {
                        R *= -1
                    }
                    var J = C < g;
                    if (D) {
                        J = Y > C
                    }
                    if (J) {
                        B[P] += R;
                        L[O].push({
                            from: K,
                            to: K + R
                        })
                    } else {
                        B[P] -= R;
                        L[O].push({
                            from: K,
                            to: K - R
                        })
                    }
                }
            }
            this._renderData[d] = {
                baseOffset: U,
                offsets: L,
                bands: t
            };
            this._renderData[d].xoffsets = this._calculateXOffsets(d, E);
            return this._renderData[d]
        },
        _isPointSeriesOnly: function () {
            for (var b = 0; b < this.seriesGroups.length; b++) {
                var c = this.seriesGroups[b];
                if (c.type.indexOf("line") == -1 && c.type.indexOf("area") == -1 && c.type.indexOf("scatter") == -1 && c.type.indexOf("bubble") == -1) {
                    return false
                }
            }
            return true
        },
        _alignValuesWithTicks: function (f) {
            var b = this._isPointSeriesOnly();
            var e = this._getCategoryAxis(f);
            var d = e.valuesOnTicks == undefined ? b : e.valuesOnTicks != false;
            if (f == undefined) {
                return d
            }
            var c = this.seriesGroups[f];
            if (c.valuesOnTicks == undefined) {
                return d
            }
            return c.valuesOnTicks
        },
        _getYearsDiff: function (c, b) {
            return b.getFullYear() - c.getFullYear()
        },
        _getMonthsDiff: function (c, b) {
            return 12 * (b.getFullYear() - c.getFullYear()) + b.getMonth() - c.getMonth()
        },
        _getDateDiff: function (e, d, c) {
            var b = 0;
            if (c != "year" && c != "month") {
                b = d.valueOf() - e.valueOf()
            }
            switch (c) {
            case "year":
                b = this._getYearsDiff(e, d);
                break;
            case "month":
                b = this._getMonthsDiff(e, d);
                break;
            case "day":
                b /= (24 * 3600 * 1000);
                break;
            case "hour":
                b /= (3600 * 1000);
                break;
            case "minute":
                b /= (60 * 1000);
                break;
            case "second":
                b /= (1000);
                break;
            case "millisecond":
                break
            }
            if (c != "year" && c != "month") {
                b = a.jqx._rnd(b, 1, true)
            }
            return b
        },
        _getDateTimeArray: function (f, m, o, n, b) {
            var h = [];
            var j = this._getDateDiff(f, m, o) + 1;
            if (n) {
                j += b
            }
            if (o == "year") {
                var d = f.getFullYear();
                for (var g = 0; g < j; g++) {
                    h.push(new Date(d, 0, 1, 0, 0, 0, 0));
                    d++
                }
            } else {
                if (o == "month") {
                    var k = f.getMonth();
                    var l = f.getFullYear();
                    for (var g = 0; g < j; g++) {
                        h.push(new Date(l, k, 1, 0, 0, 0, 0));
                        k++;
                        if (k > 11) {
                            l++;
                            k = 0
                        }
                    }
                } else {
                    if (o == "day") {
                        for (var g = 0; g < j; g++) {
                            var e = new Date(f.valueOf() + g * 1000 * 3600 * 24);
                            h.push(e)
                        }
                    } else {
                        var c = 0;
                        switch (o) {
                        case "millisecond":
                            c = 1;
                            break;
                        case "second":
                            c = 1000;
                            break;
                        case "minute":
                            c = 60 * 1000;
                            break;
                        case "hour":
                            c = 3600 * 1000;
                            break
                        }
                        for (var g = 0; g < j; g++) {
                            var e = new Date(f.valueOf() + g * c);
                            h.push(e)
                        }
                    }
                }
            }
            return h
        },
        _getAsDate: function (b, c) {
            b = this._castAsDate(b);
            if (c == "month") {
                return new Date(b.getFullYear(), b.getMonth(), 1)
            }
            if (c == "year") {
                return new Date(b.getFullYear(), 0, 1)
            }
            if (c == "day") {
                return new Date(b.getFullYear(), b.getMonth(), b.getDate())
            }
            return b
        },
        _calculateXOffsets: function (d, c) {
            var k = this._getCategoryAxis(d);
            var z = new Array();
            var j = this._getDataLen(d);
            var G = k.type == "date";
            var n = G ? this._castAsDate(k.minValue) : this._castAsNumber(k.minValue);
            var p = G ? this._castAsDate(k.maxValue) : this._castAsNumber(k.maxValue);
            var v = n,
                A = p;
            if (isNaN(v) || isNaN(A)) {
                for (var B = 0; B < j; B++) {
                    var s = this._getDataValue(B, k.dataField, d);
                    s = G ? this._castAsDate(s) : this._castAsNumber(s);
                    if (isNaN(s)) {
                        continue
                    }
                    if (s < v || isNaN(v)) {
                        v = s
                    }
                    if (s > A || isNaN(A)) {
                        A = s
                    }
                }
            }
            if (G) {
                v = new Date(v);
                A = new Date(A)
            }
            v = n || v;
            A = p || A;
            if (G && !(this._isDate(v) && this._isDate(A))) {
                throw "Invalid Date values"
            }
            var t = (k.maxValue != undefined) || (k.minValue != undefined);
            if (t && (isNaN(A) || isNaN(v))) {
                t = false;
                throw "Invalid min/max category values"
            }
            if (!t && !G) {
                v = 0;
                A = j - 1
            }
            var q = k.baseUnit;
            var F = q == "hour" || q == "minute" || q == "second" || q == "millisecond";
            var D = k.unitInterval;
            if (isNaN(D) || D <= 0) {
                D = 1
            }
            if (F) {
                if (q == "second") {
                    D *= 1000
                } else {
                    if (q == "minute") {
                        D *= 60 * 1000
                    } else {
                        if (q == "hour") {
                            D *= 3600 * 1000
                        }
                    }
                }
            }
            var H = NaN;
            var h = this._alignValuesWithTicks(d);
            if (t) {
                if (h) {
                    H = A - v
                } else {
                    H = A - v + D
                }
            } else {
                H = j - 1;
                if (!h) {
                    H++
                }
            } if (H == 0) {
                H = D
            }
            var u = 0;
            var E = A;
            var C = v;
            if (G) {
                var E = this._getAsDate(A, q);
                var C = this._getAsDate(v, q);
                if (!F && !h) {
                    if (q == "month") {
                        E.setMonth(E.getMonth() + 1)
                    } else {
                        if (q == "year") {
                            E.setYear(E.getFullYear() + 1)
                        } else {
                            E.setDate(E.getDate() + 1)
                        }
                    }
                }
                H = this._getDateDiff(C, E, F ? "millisecond" : k.baseUnit);
                while (E <= A) {
                    H = a.jqx._rnd(H, D, true);
                    if (k.baseUnit == "month") {
                        C = new Date(C.getFullYear(), C.getMonth(), 1);
                        E = new Date(C);
                        E.setMonth(E.getMonth() + H)
                    } else {
                        if (k.baseUnit == "year") {
                            C = new Date(C.getFullYear(), 0, 1);
                            E = new Date(C);
                            E.setYear(E.getFullYear() + H)
                        }
                    } if (E < A) {
                        H++
                    } else {
                        break
                    }
                }
                u = a.jqx._rnd(this._getDateDiff(C, E, "day"), 1, false)
            }
            var g = Math.max(1, H / D);
            var e = c.width / g;
            var r = d != undefined && this.seriesGroups[d].type.indexOf("column") != -1;
            var m = 0;
            if (!h && (!G || q == "day") && !r) {
                m = e / 2
            }
            var b = q;
            if (q == "day" || q == "month" || q == "year") {
                q = "day"
            } else {
                q = "millisecond"
            }
            var f = -1,
                l = -1;
            for (var B = 0; B < j; B++) {
                if (!t && !G) {
                    z.push(a.jqx._ptrnd(m + (B - C) / H * c.width));
                    if (f == -1) {
                        f = B
                    }
                    if (l == -1 || l < B) {
                        l = B
                    }
                    continue
                }
                var s = this._getDataValue(B, k.dataField, d);
                s = G ? this._castAsDate(s) : this._castAsNumber(s);
                if (isNaN(s) || s < v || s > A) {
                    z.push(-1);
                    continue
                }
                var o = 0;
                if (!G || (G && q != "day")) {
                    diffFromMin = s - C;
                    o = (s - C) * c.width / H
                } else {
                    o = this._getDateDiff(C, s, b) * e / D;
                    var w = this._getDateDiff(this._getAsDate(s, b), s, q);
                    o += w / u * c.width
                }
                o = a.jqx._ptrnd(m + o);
                z.push(o);
                if (f == -1) {
                    f = B
                }
                if (l == -1 || l < B) {
                    l = B
                }
            }
            if (k.flip == true) {
                z.reverse()
            }
            if (F) {
                H = this._getDateDiff(C, E, k.baseUnit);
                H = a.jqx._rnd(H, 1, false)
            }
            return {
                data: z,
                first: f,
                last: l,
                length: l == -1 ? 0 : l - f + 1,
                itemWidth: e,
                rangeLength: H,
                min: v,
                max: A,
                customRange: t
            }
        },
        _getCategoryAxis: function (b) {
            if (b == undefined || this.seriesGroups.length <= b) {
                return this.categoryAxis
            }
            return this.seriesGroups[b].categoryAxis || this.categoryAxis
        },
        _isGreyScale: function (e, b) {
            var d = this.seriesGroups[e];
            var c = d.series[b];
            if (c.greyScale == true) {
                return true
            } else {
                if (c.greyScale == false) {
                    return false
                }
            } if (d.greyScale == true) {
                return true
            } else {
                if (d.greyScale == false) {
                    return false
                }
            }
            return this.greyScale == true
        },
        _getSeriesColor: function (d, c) {
            var b = this._getSeriesColorInternal(d, c);
            if (this._isGreyScale(d, c) && b.indexOf("#") == 0) {
                b = a.jqx.toGreyScale(b)
            }
            return b
        },
        _getSeriesColorInternal: function (l, c) {
            var e = this.seriesGroups[l];
            var m = e.series[c];
            if (m.color) {
                return m.color
            }
            var k = 0;
            for (var d = 0; d <= l; d++) {
                for (var b in this.seriesGroups[d].series) {
                    if (d == l && b == c) {
                        break
                    } else {
                        k++
                    }
                }
            }
            var h = this.colorScheme;
            if (e.colorScheme) {
                h = e.colorScheme;
                sidex = c
            }
            if (h == undefined || h == "") {
                h = this.colorSchemes[0].name
            }
            if (h) {
                for (var d = 0; d < this.colorSchemes.length; d++) {
                    var f = this.colorSchemes[d];
                    if (f.name == h) {
                        while (k > f.colors.length) {
                            k -= f.colors.length;
                            if (++d >= this.colorSchemes.length) {
                                d = 0
                            }
                            f = this.colorSchemes[d]
                        }
                        return f.colors[k % f.colors.length]
                    }
                }
            }
            return "#222222"
        },
        _getColor: function (d, f, k, h) {
            if (d == undefined || d == "") {
                d = this.colorSchemes[0].name
            }
            for (var g = 0; g < this.colorSchemes.length; g++) {
                if (d == this.colorSchemes[g].name) {
                    break
                }
            }
            var e = 0;
            while (e <= f) {
                if (g == this.colorSchemes.length) {
                    g = 0
                }
                var b = this.colorSchemes[g].colors.length;
                if (e + b <= f) {
                    e += b;
                    g++
                } else {
                    var c = this.colorSchemes[g].colors[f - e];
                    if (this._isGreyScale(k, h) && c.indexOf("#") == 0) {
                        c = a.jqx.toGreyScale(c)
                    }
                    return c
                }
            }
        },
        getColorScheme: function (b) {
            for (var c in this.colorSchemes) {
                if (this.colorSchemes[c].name == b) {
                    return this.colorSchemes[c].colors
                }
            }
            return undefined
        },
        addColorScheme: function (c, b) {
            for (var d in this.colorSchemes) {
                if (this.colorSchemes[d].name == c) {
                    this.colorSchemes[d].colors = b;
                    return
                }
            }
            this.colorSchemes.push({
                name: c,
                colors: b
            })
        },
        removeColorScheme: function (b) {
            for (var c in this.colorSchemes) {
                if (this.colorSchemes[c].name == b) {
                    this.colorSchemes.splice(c, 1);
                    break
                }
            }
        },
        colorSchemes: [{
            name: "scheme01",
            colors: ["#4572A7", "#AA4643", "#89A54E", "#71588F", "#4198AF"]
        }, {
            name: "scheme02",
            colors: ["#7FD13B", "#EA157A", "#FEB80A", "#00ADDC", "#738AC8"]
        }, {
            name: "scheme03",
            colors: ["#E8601A", "#FF9639", "#F5BD6A", "#599994", "#115D6E"]
        }, {
            name: "scheme04",
            colors: ["#D02841", "#FF7C41", "#FFC051", "#5B5F4D", "#364651"]
        }, {
            name: "scheme05",
            colors: ["#25A0DA", "#309B46", "#8EBC00", "#FF7515", "#FFAE00"]
        }, {
            name: "scheme06",
            colors: ["#0A3A4A", "#196674", "#33A6B2", "#9AC836", "#D0E64B"]
        }, {
            name: "scheme07",
            colors: ["#CC6B32", "#FFAB48", "#FFE7AD", "#A7C9AE", "#888A63"]
        }, {
            name: "scheme08",
            colors: ["#2F2933", "#01A2A6", "#29D9C2", "#BDF271", "#FFFFA6"]
        }, {
            name: "scheme09",
            colors: ["#1B2B32", "#37646F", "#A3ABAF", "#E1E7E8", "#B22E2F"]
        }, {
            name: "scheme10",
            colors: ["#5A4B53", "#9C3C58", "#DE2B5B", "#D86A41", "#D2A825"]
        }, {
            name: "scheme11",
            colors: ["#993144", "#FFA257", "#CCA56A", "#ADA072", "#949681"]
        }],
        _formatValue: function (f, i, b, h, d, c) {
            if (f == undefined) {
                return ""
            }
            if (this._isObject(f) && !b) {
                return ""
            }
            if (b) {
                if (!a.isFunction(b)) {
                    return f.toString()
                }
                try {
                    return b(f, c, d, h)
                } catch (g) {
                    return g.message
                }
            }
            if (this._isNumber(f)) {
                return this._formatNumber(f, i)
            }
            if (this._isDate(f)) {
                return this._formatDate(f, i)
            }
            if (i) {
                return (i.prefix || "") + f.toString() + (i.sufix || "")
            }
            return f.toString()
        },
        _getFormattedValue: function (o, f, b, c, d) {
            var j = this.seriesGroups[o];
            var q = j.series[f];
            var p = "";
            var i = c,
                l = d;
            if (!l) {
                l = q.formatFunction || j.formatFunction
            }
            if (!i) {
                i = q.formatSettings || j.formatSettings
            }
            if (!q.formatFunction && q.formatSettings) {
                l = undefined
            }
            if (j.type.indexOf("range") != -1) {
                var h = this._getDataValue(b, q.dataFieldFrom, o);
                var n = this._getDataValue(b, q.dataFieldTo, o);
                if (l && a.isFunction(l)) {
                    try {
                        return l({
                            from: h,
                            to: n
                        }, b, q, j)
                    } catch (k) {
                        return k.message
                    }
                }
                if (h) {
                    p = this._formatValue(h, i, l, j, q, b)
                }
                if (n) {
                    p += ", " + this._formatValue(n, i, l, j, q, b)
                }
            } else {
                var m = this._getDataValue(b, q.dataField, o);
                if (m) {
                    p = this._formatValue(m, i, l, j, q, b)
                }
            }
            return p || ""
        },
        _isNumberAsString: function (d) {
            if (typeof (d) != "string") {
                return false
            }
            d = a.trim(d);
            for (var b = 0; b < d.length; b++) {
                var c = d.charAt(b);
                if ((c >= "0" && c <= "9") || c == "," || c == ".") {
                    continue
                }
                if (c == "-" && b == 0) {
                    continue
                }
                if ((c == "(" && b == 0) || (c == ")" && b == d.length - 1)) {
                    continue
                }
                return false
            }
            return true
        },
        _castAsDate: function (c) {
            if (c instanceof Date && !isNaN(c)) {
                return c
            }
            if (typeof (c) == "string") {
                var b = new Date(c);
                if (isNaN(b)) {
                    b = this._parseISO8601Date(c)
                }
                if (b != undefined && !isNaN(b)) {
                    return b
                }
            }
            return undefined
        },
        _parseISO8601Date: function (g) {
            var k = g.split(" ");
            if (k.length < 0) {
                return NaN
            }
            var b = k[0].split("-");
            var c = k.length == 2 ? k[1].split(":") : "";
            var f = b[0];
            var h = b.length > 1 ? b[1] - 1 : 0;
            var i = b.length > 2 ? b[2] : 1;
            var d = c[1];
            var e = c.length > 1 ? c[1] : 0;
            var d = c.length > 2 ? c[2] : 0;
            var j = c.length > 3 ? c[3] : 0;
            return new Date(f, h, i, d, e, j)
        },
        _castAsNumber: function (c) {
            if (c instanceof Date && !isNaN(c)) {
                return c.valueOf()
            }
            if (typeof (c) == "string") {
                if (this._isNumber(c)) {
                    c = parseFloat(c)
                } else {
                    var b = new Date(c);
                    if (b != undefined) {
                        c = b.valueOf()
                    }
                }
            }
            return c
        },
        _isNumber: function (b) {
            if (typeof (b) == "string") {
                if (this._isNumberAsString(b)) {
                    b = parseFloat(b)
                }
            }
            return typeof b === "number" && isFinite(b)
        },
        _isDate: function (b) {
            return b instanceof Date
        },
        _isBoolean: function (b) {
            return typeof b === "boolean"
        },
        _isObject: function (b) {
            return (b && (typeof b === "object" || a.isFunction(b))) || false
        },
        _formatDate: function (c, b) {
            return c.toString()
        },
        _formatNumber: function (n, e) {
            if (!this._isNumber(n)) {
                return n
            }
            e = e || {};
            var q = e.decimalSeparator || ".";
            var o = e.thousandsSeparator || "";
            var m = e.prefix || "";
            var p = e.sufix || "";
            var h = e.decimalPlaces || ((n * 100 != parseInt(n) * 100) ? 2 : 0);
            var l = e.negativeWithBrackets || false;
            var g = (n < 0);
            if (g && l) {
                n *= -1
            }
            var d = n.toString();
            var b;
            var k = Math.pow(10, h);
            d = (Math.round(n * k) / k).toString();
            if (isNaN(d)) {
                d = ""
            }
            b = d.lastIndexOf(".");
            if (h > 0) {
                if (b < 0) {
                    d += q;
                    b = d.length - 1
                } else {
                    if (q !== ".") {
                        d = d.replace(".", q)
                    }
                }
                while ((d.length - 1 - b) < h) {
                    d += "0"
                }
            }
            b = d.lastIndexOf(q);
            b = (b > -1) ? b : d.length;
            var f = d.substring(b);
            var c = 0;
            for (var j = b; j > 0; j--, c++) {
                if ((c % 3 === 0) && (j !== b) && (!g || (j > 1) || (g && l))) {
                    f = o + f
                }
                f = d.charAt(j - 1) + f
            }
            d = f;
            if (g && l) {
                d = "(" + d + ")"
            }
            return m + d + p
        },
        _defaultNumberFormat: {
            prefix: "",
            sufix: "",
            decimalSeparator: ".",
            thousandsSeparator: ",",
            decimalPlaces: 2,
            negativeWithBrackets: false
        },
        _getBezierPoints: function (g) {
            var k = [];
            var h = g.split(" ");
            for (var f = 0; f < h.length; f++) {
                var l = h[f].split(",");
                k.push({
                    x: parseFloat(l[0]),
                    y: parseFloat(l[1])
                })
            }
            var m = "";
            for (var f = 0; f < k.length - 1; f++) {
                var b = [];
                if (0 == f) {
                    b.push(k[f]);
                    b.push(k[f]);
                    b.push(k[f + 1]);
                    b.push(k[f + 2])
                } else {
                    if (k.length - 2 == f) {
                        b.push(k[f - 1]);
                        b.push(k[f]);
                        b.push(k[f + 1]);
                        b.push(k[f + 1])
                    } else {
                        b.push(k[f - 1]);
                        b.push(k[f]);
                        b.push(k[f + 1]);
                        b.push(k[f + 2])
                    }
                }
                var d = [];
                var j = f == 0 ? 81 : 9;
                var e = {
                    x: ((-b[0].x + j * b[1].x + b[2].x) / j),
                    y: ((-b[0].y + j * b[1].y + b[2].y) / j)
                };
                var c = {
                    x: ((b[1].x + j * b[2].x - b[3].x) / j),
                    y: ((b[1].y + j * b[2].y - b[3].y) / j)
                };
                d.push({
                    x: b[1].x,
                    y: b[1].y
                });
                d.push(e);
                d.push(c);
                d.push({
                    x: b[2].x,
                    y: b[2].y
                });
                if (f == 0) {
                    d[1].x++;
                    d[1].y++
                }
                m += "C" + a.jqx._ptrnd(d[1].x) + "," + a.jqx._ptrnd(d[1].y) + " " + a.jqx._ptrnd(d[2].x) + "," + a.jqx._ptrnd(d[2].y) + " " + a.jqx._ptrnd(d[3].x) + "," + a.jqx._ptrnd(d[3].y) + " "
            }
            return m
        },
        _animTickInt: 50,
        _createAnimationGroup: function (b) {
            if (!this._animGroups) {
                this._animGroups = {}
            }
            this._animGroups[b] = {
                animations: [],
                startTick: NaN
            }
        },
        _startAnimation: function (c) {
            var e = new Date();
            var b = e.getTime();
            this._animGroups[c].startTick = b;
            this._enableAnimTimer()
        },
        _enqueueAnimation: function (e, d, c, g, f, b, h) {
            if (g == 0) {
                g = 1
            }
            if (h == undefined) {
                h = "easeInOutSine"
            }
            this._animGroups[e].animations.push({
                key: d,
                properties: c,
                duration: g,
                fn: f,
                context: b,
                easing: h
            })
        },
        _stopAnimations: function () {
            clearTimeout(this._animtimer);
            this._animtimer = undefined;
            this._animGroups = undefined
        },
        _enableAnimTimer: function () {
            if (!this._animtimer) {
                var b = this;
                this._animtimer = setTimeout(function () {
                    b._runAnimation()
                }, this._animTickInt)
            }
        },
        _runAnimation: function () {
            if (this._animGroups) {
                var s = new Date();
                var k = s.getTime();
                var o = {};
                for (var l in this._animGroups) {
                    var r = this._animGroups[l].animations;
                    var m = this._animGroups[l].startTick;
                    var h = 0;
                    for (var n = 0; n < r.length; n++) {
                        var t = r[n];
                        var b = (k - m);
                        if (t.duration > h) {
                            h = t.duration
                        }
                        var q = t.duration > 0 ? b / t.duration : 0;
                        var e = q;
                        if (t.easing) {
                            e = jQuery.easing[t.easing](q, b, 0, 1, t.duration)
                        }
                        if (q > 1) {
                            q = 1;
                            e = 1
                        }
                        if (t.fn) {
                            t.fn(t.key, t.context, e);
                            continue
                        }
                        var g = {};
                        for (var l = 0; l < t.properties.length; l++) {
                            var c = t.properties[l];
                            var f = 0;
                            if (q == 1) {
                                f = c.to
                            } else {
                                f = e * (c.to - c.from) + c.from
                            }
                            g[c.key] = f
                        }
                        this.renderer.attr(t.key, g)
                    }
                    if (m + h > k) {
                        o[l] = ({
                            startTick: m,
                            animations: r
                        })
                    }
                }
                this._animGroups = o;
                if (this.renderer instanceof a.jqx.HTML5Renderer) {
                    this.renderer.refresh()
                }
            }
            this._animtimer = null;
            for (var l in this._animGroups) {
                this._enableAnimTimer();
                break
            }
        }
    });
    a.jqx.toGreyScale = function (b) {
        var c = a.jqx.cssToRgb(b);
        c[0] = c[1] = c[2] = Math.round(0.3 * c[0] + 0.59 * c[1] + 0.11 * c[2]);
        var d = a.jqx.rgbToHex(c[0], c[1], c[2]);
        return "#" + d[0] + d[1] + d[2]
    }, a.jqx._adjustColor = function (d, b) {
        var e = a.jqx.cssToRgb(d);
        var d = "#";
        for (var f = 0; f < 3; f++) {
            var g = Math.round(b * e[f]);
            if (g > 255) {
                g = 255
            } else {
                if (g <= 0) {
                    g = 0
                }
            }
            g = a.jqx.decToHex(g);
            if (g.toString().length == 1) {
                d += "0"
            }
            d += g
        }
        return d.toUpperCase()
    };
    a.jqx.decToHex = function (b) {
        return b.toString(16)
    }, a.jqx.hexToDec = function (b) {
        return parseInt(b, 16)
    };
    a.jqx.rgbToHex = function (e, d, c) {
        return [a.jqx.decToHex(e), a.jqx.decToHex(d), a.jqx.decToHex(c)]
    };
    a.jqx.hexToRgb = function (c, d, b) {
        return [a.jqx.hexToDec(c), a.jqx.hexToDec(d), a.jqx.hexToDec(b)]
    };
    a.jqx.cssToRgb = function (b) {
        if (b.indexOf("rgb") <= -1) {
            return a.jqx.hexToRgb(b.substring(1, 3), b.substring(3, 5), b.substring(5, 7))
        }
        return b.substring(4, b.length - 1).split(",")
    };
    a.jqx.swap = function (b, d) {
        var c = b;
        b = d;
        d = c
    };
    a.jqx.getNum = function (b) {
        if (!a.isArray(b)) {
            if (isNaN(b)) {
                return 0
            }
        } else {
            for (var c = 0; c < b.length; c++) {
                if (!isNaN(b[c])) {
                    return b[c]
                }
            }
        }
        return 0
    };
    a.jqx._ptrnd = function (c) {
        if (!document.createElementNS) {
            if (Math.round(c) == c) {
                return c
            }
            return a.jqx._rnd(c, 1, false)
        }
        var b = a.jqx._rnd(c, 0.5, false);
        if (Math.abs(b - Math.round(b)) != 0.5) {
            return b > c ? b - 0.5 : b + 0.5
        }
        return b
    };
    a.jqx._rup = function (c) {
        var b = Math.round(c);
        if (c > b) {
            b++
        }
        return b
    };
    a.jqx.log = function (c, b) {
        return Math.log(c) / (b ? Math.log(b) : 1)
    };
    a.jqx._rnd = function (c, e, d) {
        if (isNaN(c)) {
            return c
        }
        var b = c - c % e;
        if (c == b) {
            return b
        }
        if (d) {
            if (c > b) {
                b += e
            }
        } else {
            if (b > c) {
                b -= e
            }
        }
        return b
    };
    a.jqx.commonRenderer = {
        pieSlicePath: function (j, i, g, q, z, A, d) {
            if (!q) {
                q = 1
            }
            var l = Math.abs(z - A);
            var o = l > 180 ? 1 : 0;
            if (l >= 360) {
                A = z + 359.99
            }
            var p = z * Math.PI * 2 / 360;
            var h = A * Math.PI * 2 / 360;
            var v = j,
                u = j,
                f = i,
                e = i;
            var m = !isNaN(g) && g > 0;
            if (m) {
                d = 0
            }
            if (d + g > 0) {
                if (d > 0) {
                    var k = l / 2 + z;
                    var w = k * Math.PI * 2 / 360;
                    j += d * Math.cos(w);
                    i -= d * Math.sin(w)
                }
                if (m) {
                    var t = g;
                    v = j + t * Math.cos(p);
                    f = i - t * Math.sin(p);
                    u = j + t * Math.cos(h);
                    e = i - t * Math.sin(h)
                }
            }
            var s = j + q * Math.cos(p);
            var r = j + q * Math.cos(h);
            var c = i - q * Math.sin(p);
            var b = i - q * Math.sin(h);
            var n = "";
            if (m) {
                n = "M " + u + "," + e;
                n += " a" + g + "," + g;
                n += " 0 " + o + ",1 " + (v - u) + "," + (f - e);
                n += " L" + s + "," + c;
                n += " a" + q + "," + q;
                n += " 0 " + o + ",0 " + (r - s) + "," + (b - c)
            } else {
                n = "M " + r + "," + b;
                n += " a" + q + "," + q;
                n += " 0 " + o + ",1 " + (s - r) + "," + (c - b);
                n += " L" + j + "," + i + " Z"
            }
            return n
        }
    };
    a.jqx.svgRenderer = function () {};
    a.jqx.svgRenderer.prototype = {
        _svgns: "http://www.w3.org/2000/svg",
        init: function (f) {
            var d = "<table id=tblChart cellspacing='0' cellpadding='0' border='0' align='left' valign='top'><tr><td colspan=2 id=tdTop></td></tr><tr><td id=tdLeft></td><td class='chartContainer'></td></tr></table>";
            f.append(d);
            this.host = f;
            var b = f.find(".chartContainer");
            b[0].style.width = f.width() + "px";
            b[0].style.height = f.height() + "px";
            var h;
            try {
                var c = document.createElementNS(this._svgns, "svg");
                c.setAttribute("id", "svgChart");
                c.setAttribute("version", "1.1");
                c.setAttribute("width", "100%");
                c.setAttribute("height", "100%");
                c.setAttribute("overflow", "hidden");
                b[0].appendChild(c);
                this.canvas = c
            } catch (g) {
                return false
            }
            this._id = new Date().getTime();
            this.clear();
            this._layout();
            this._runLayoutFix();
            return true
        },
        _runLayoutFix: function () {
            var b = this;
            this._fixLayout()
        },
        _fixLayout: function () {
            var g = a(this.canvas).position();
            var d = (parseFloat(g.left) == parseInt(g.left));
            var b = (parseFloat(g.top) == parseInt(g.top));
            if (a.jqx.browser.msie) {
                var d = true,
                    b = true;
                var e = this.host;
                var c = 0,
                    f = 0;
                while (e && e.position && e[0].parentNode) {
                    var h = e.position();
                    c += parseFloat(h.left) - parseInt(h.left);
                    f += parseFloat(h.top) - parseInt(h.top);
                    e = e.parent()
                }
                d = parseFloat(c) == parseInt(c);
                b = parseFloat(f) == parseInt(f)
            }
            if (!d) {
                this.host.find("#tdLeft")[0].style.width = "0.5px"
            }
            if (!b) {
                this.host.find("#tdTop")[0].style.height = "0.5px"
            }
        },
        _layout: function () {
            var c = a(this.canvas).offset();
            var b = this.host.find(".chartContainer");
            this._width = Math.max(a.jqx._rup(this.host.width()) - 1, 0);
            this._height = Math.max(a.jqx._rup(this.host.height()) - 1, 0);
            b[0].style.width = this._width;
            b[0].style.height = this._height;
            this._fixLayout()
        },
        getRect: function () {
            return {
                x: 0,
                y: 0,
                width: this._width,
                height: this._height
            }
        },
        getContainer: function () {
            var b = this.host.find(".chartContainer");
            return b
        },
        clear: function () {
            while (this.canvas.childElementCount > 0) {
                this.canvas.removeChild(this.canvas.firstElementChild)
            }
            this._defs = document.createElementNS(this._svgns, "defs");
            this._gradients = {};
            this.canvas.appendChild(this._defs)
        },
        removeElement: function (c) {
            if (c != undefined) {
                try {
                    if (c.parentNode) {
                        c.parentNode.removeChild(c)
                    } else {
                        this.canvas.removeChild(c)
                    }
                } catch (b) {}
            }
        },
        _openGroups: [],
        beginGroup: function () {
            var b = this._activeParent();
            var c = document.createElementNS(this._svgns, "g");
            b.appendChild(c);
            this._openGroups.push(c);
            return c
        },
        endGroup: function () {
            if (this._openGroups.length == 0) {
                return
            }
            this._openGroups.pop()
        },
        _activeParent: function () {
            return this._openGroups.length == 0 ? this.canvas : this._openGroups[this._openGroups.length - 1]
        },
        createClipRect: function (d) {
            var e = document.createElementNS(this._svgns, "clipPath");
            var b = document.createElementNS(this._svgns, "rect");
            this.attr(b, {
                x: d.x,
                y: d.y,
                width: d.width,
                height: d.height
            });
            this._clipId = this._clipId || 0;
            e.id = "cl" + this._id + "_" + (++this._clipId).toString();
            e.appendChild(b);
            this._defs.appendChild(e);
            return e
        },
        setClip: function (c, b) {
            return this.attr(c, {
                "clip-path": "url(#" + b.id + ")"
            })
        },
        _clipId: 0,
        addHandler: function (b, d, c) {
            b["on" + d] = c
        },
        shape: function (b, e) {
            var c = document.createElementNS(this._svgns, b);
            if (!c) {
                return undefined
            }
            for (var d in e) {
                c.setAttribute(d, e[d])
            }
            this._activeParent().appendChild(c);
            return c
        },
        measureText: function (o, d, f) {
            var h = document.createElementNS(this._svgns, "text");
            this.attr(h, f);
            h.appendChild(h.ownerDocument.createTextNode(o));
            var n = this._activeParent();
            n.appendChild(h);
            var p;
            try {
                p = h.getBBox()
            } catch (l) {
                if (console && console.log) {
                    console.log(l)
                }
            }
            if (p == undefined || isNaN(p.width) || isNaN(p.height) || Math.abs(p.width) == Infinity || Math.abs(p.height) == Infinity) {
                return {
                    width: 0,
                    height: 0
                }
            }
            var i = a.jqx._rup(p.width);
            var b = a.jqx._rup(p.height);
            n.removeChild(h);
            if (d == 0) {
                return {
                    width: i,
                    height: b
                }
            }
            var k = d * Math.PI * 2 / 360;
            var c = Math.abs(Math.sin(k));
            var j = Math.abs(Math.cos(k));
            var g = Math.abs(i * c + b * j);
            var m = Math.abs(i * j + b * c);
            return {
                width: a.jqx._rup(m),
                height: a.jqx._rup(g)
            }
        },
        text: function (u, r, p, B, z, G, J, I, t, l, d) {
            var A;
            if (!t) {
                t = "center"
            }
            if (!l) {
                l = "center"
            }
            if (I) {
                A = this.beginGroup();
                var i = this.createClipRect({
                    x: a.jqx._rup(r) - 1,
                    y: a.jqx._rup(p) - 1,
                    width: a.jqx._rup(B) + 2,
                    height: a.jqx._rup(z) + 2
                });
                this.setClip(A, i)
            }
            var v = document.createElementNS(this._svgns, "text");
            this.attr(v, J);
            this.attr(v, {
                cursor: "default"
            });
            v.appendChild(v.ownerDocument.createTextNode(u));
            var o = this._activeParent();
            o.appendChild(v);
            var c;
            try {
                c = v.getBBox()
            } catch (H) {
                if (console && console.log) {
                    console.log(H)
                }
            }
            if (c == undefined) {
                return
            }
            o.removeChild(v);
            var K = c.width;
            var m = c.height * 0.6;
            var s = B || 0;
            var E = z || 0;
            if (!G || G == 0) {
                if (t == "center") {
                    r += (s - K) / 2
                } else {
                    if (t == "right") {
                        r += (s - K)
                    }
                }
                p += m;
                if (l == "center") {
                    p += (E - m) / 2
                } else {
                    if (l == "bottom") {
                        p += E - m
                    }
                } if (!B) {
                    B = K
                }
                if (!z) {
                    z = m
                }
                this.attr(v, {
                    x: a.jqx._rup(r),
                    y: a.jqx._rup(p),
                    width: a.jqx._rup(B),
                    height: a.jqx._rup(z)
                });
                o.appendChild(v);
                this.endGroup();
                return v
            }
            var j = G * Math.PI * 2 / 360;
            var F = Math.sin(j);
            var k = Math.cos(j);
            var n = K * F;
            var q = K * k;
            var C = p;
            var g = r;
            if (t == "center" || t == "" || t == "undefined") {
                r = r + B / 2
            } else {
                if (t == "right") {
                    r = r + B
                }
            } if (l == "center" || l == "" || l == "undefined") {
                p += z / 2
            } else {
                if (l == "bottom") {
                    p += z - m / 2
                } else {
                    if (l == "top") {
                        p += m / 2
                    }
                }
            }
            d = d || "";
            var L = "middle";
            if (d.indexOf("top") != -1) {
                L = "top"
            } else {
                if (d.indexOf("bottom") != -1) {
                    L = "bottom"
                }
            }
            var b = "center";
            if (d.indexOf("left") != -1) {
                b = "left"
            } else {
                if (d.indexOf("right") != -1) {
                    b = "right"
                }
            } if (b == "center") {
                r -= q / 2;
                p -= n / 2
            } else {
                if (b == "right") {
                    r -= q;
                    p -= n
                }
            } if (L == "top") {
                r -= m * F
            } else {
                if (L == "middle") {
                    r -= m * F / 2
                }
            }
            r = a.jqx._rup(r);
            p = a.jqx._rup(p);
            var D = this.shape("g", {
                transform: "translate(" + r + "," + p + ")"
            });
            var f = this.shape("g", {
                transform: "rotate(" + G + ")"
            });
            D.appendChild(f);
            f.appendChild(v);
            o.appendChild(D);
            this.endGroup();
            return D
        },
        line: function (d, f, c, e, g) {
            var b = this.shape("line", {
                x1: d,
                y1: f,
                x2: c,
                y2: e
            });
            this.attr(b, g);
            return b
        },
        path: function (c, d) {
            var b = this.shape("path");
            b.setAttribute("d", c);
            if (d) {
                this.attr(b, d)
            }
            return b
        },
        rect: function (b, g, c, e, f) {
            b = a.jqx._ptrnd(b);
            g = a.jqx._ptrnd(g);
            c = a.jqx._rup(c);
            e = a.jqx._rup(e);
            var d = this.shape("rect", {
                x: b,
                y: g,
                width: c,
                height: e
            });
            if (f) {
                this.attr(d, f)
            }
            return d
        },
        circle: function (b, d, c) {
            return this.shape("circle", {
                cx: b,
                cy: d,
                r: c
            })
        },
        pieSlicePath: function (c, h, g, e, f, d, b) {
            return a.jqx.commonRenderer.pieSlicePath(c, h, g, e, f, d, b)
        },
        pieslice: function (j, h, g, d, f, b, i, c) {
            var e = this.pieSlicePath(j, h, g, d, f, b, i);
            var k = this.shape("path");
            k.setAttribute("d", e);
            if (c) {
                this.attr(k, c)
            }
            return k
        },
        attr: function (b, d) {
            if (!b || !d) {
                return
            }
            for (var c in d) {
                if (c == "textContent") {
                    b.textContent = d[c]
                } else {
                    b.setAttribute(c, d[c])
                }
            }
        },
        getAttr: function (c, b) {
            return c.getAttribute(b)
        },
        _gradients: {},
        _toLinearGradient: function (e, g, h) {
            var c = "grd" + this._id + e.replace("#", "") + (g ? "v" : "h");
            var b = "url(#" + c + ")";
            if (this._gradients[b]) {
                return b
            }
            var d = document.createElementNS(this._svgns, "linearGradient");
            this.attr(d, {
                x1: "0%",
                y1: "0%",
                x2: g ? "0%" : "100%",
                y2: g ? "100%" : "0%",
                id: c
            });
            for (var f in h) {
                var j = document.createElementNS(this._svgns, "stop");
                var i = "stop-color:" + a.jqx._adjustColor(e, h[f][1]);
                this.attr(j, {
                    offset: h[f][0] + "%",
                    style: i
                });
                d.appendChild(j)
            }
            this._defs.appendChild(d);
            this._gradients[b] = true;
            return b
        },
        _toRadialGradient: function (e, h, g) {
            var c = "grd" + this._id + e.replace("#", "") + "r" + (g != undefined ? g.key : "");
            var b = "url(#" + c + ")";
            if (this._gradients[b]) {
                return b
            }
            var d = document.createElementNS(this._svgns, "radialGradient");
            if (g == undefined) {
                this.attr(d, {
                    cx: "50%",
                    cy: "50%",
                    r: "100%",
                    fx: "50%",
                    fy: "50%",
                    id: c
                })
            } else {
                this.attr(d, {
                    cx: g.x1,
                    cy: g.y1,
                    r: g.outerRadius,
                    id: c,
                    gradientUnits: "userSpaceOnUse"
                })
            }
            for (var f in h) {
                var j = document.createElementNS(this._svgns, "stop");
                var i = "stop-color:" + a.jqx._adjustColor(e, h[f][1]);
                this.attr(j, {
                    offset: h[f][0] + "%",
                    style: i
                });
                d.appendChild(j)
            }
            this._defs.appendChild(d);
            this._gradients[b] = true;
            return b
        }
    };
    a.jqx.vmlRenderer = function () {};
    a.jqx.vmlRenderer.prototype = {
        init: function (g) {
            var f = "<div class='chartContainer' style=\"position:relative;overflow:hidden;\"><div>";
            g.append(f);
            this.host = g;
            var b = g.find(".chartContainer");
            b[0].style.width = g.width() + "px";
            b[0].style.height = g.height() + "px";
            var d = true;
            try {
                for (var c = 0; c < document.namespaces.length; c++) {
                    if (document.namespaces[c].name == "v" && document.namespaces[c].urn == "urn:schemas-microsoft-com:vml") {
                        d = false;
                        break
                    }
                }
            } catch (h) {
                return false
            }
            if (a.jqx.browser.msie && parseInt(a.jqx.browser.version) < 9 && (document.childNodes && document.childNodes.length > 0 && document.childNodes[0].data && document.childNodes[0].data.indexOf("DOCTYPE") != -1)) {
                if (d) {
                    document.namespaces.add("v", "urn:schemas-microsoft-com:vml")
                }
                this._ie8mode = true
            } else {
                if (d) {
                    document.namespaces.add("v", "urn:schemas-microsoft-com:vml");
                    document.createStyleSheet().cssText = "v\\:* { behavior: url(#default#VML); display: inline-block; }"
                }
            }
            this.canvas = b[0];
            this._width = Math.max(a.jqx._rup(b.width()), 0);
            this._height = Math.max(a.jqx._rup(b.height()), 0);
            b[0].style.width = this._width + 2;
            b[0].style.height = this._height + 2;
            this._id = new Date().getTime();
            this.clear();
            return true
        },
        getRect: function () {
            return {
                x: 0,
                y: 0,
                width: this._width,
                height: this._height
            }
        },
        getContainer: function () {
            var b = this.host.find(".chartContainer");
            return b
        },
        clear: function () {
            while (this.canvas.childElementCount > 0) {
                this.canvas.removeChild(this.canvas.firstElementChild)
            }
            this._gradients = {}
        },
        removeElement: function (b) {
            if (b != null) {
                b.parentNode.removeChild(b)
            }
        },
        _openGroups: [],
        beginGroup: function () {
            var b = this._activeParent();
            var c = document.createElement("v:group");
            c.style.position = "absolute";
            c.coordorigin = "0,0";
            c.coordsize = this._width + "," + this._height;
            c.style.left = 0;
            c.style.top = 0;
            c.style.width = this._width;
            c.style.height = this._height;
            b.appendChild(c);
            this._openGroups.push(c);
            return c
        },
        endGroup: function () {
            if (this._openGroups.length == 0) {
                return
            }
            this._openGroups.pop()
        },
        _activeParent: function () {
            return this._openGroups.length == 0 ? this.canvas : this._openGroups[this._openGroups.length - 1]
        },
        createClipRect: function (b) {
            var c = document.createElement("div");
            c.style.height = b.height + "px";
            c.style.width = b.width + "px";
            c.style.position = "absolute";
            c.style.left = b.x + "px";
            c.style.top = b.y + "px";
            c.style.overflow = "hidden";
            this._clipId = this._clipId || 0;
            c.id = "cl" + this._id + "_" + (++this._clipId).toString();
            this._activeParent().appendChild(c);
            return c
        },
        setClip: function (c, b) {
            b.appendChild(c)
        },
        _clipId: 0,
        addHandler: function (b, d, c) {
            if (a(b).on) {
                a(b).on(d, c)
            } else {
                a(b).bind(d, c)
            }
        },
        measureText: function (o, d, e) {
            var f = document.createElement("v:textbox");
            var m = document.createElement("span");
            m.appendChild(document.createTextNode(o));
            f.appendChild(m);
            if (e["class"]) {
                m.className = e["class"]
            }
            var n = this._activeParent();
            n.appendChild(f);
            var h = a(f);
            var i = a.jqx._rup(h.width());
            var b = a.jqx._rup(h.height());
            n.removeChild(f);
            if (b == 0 && a.jqx.browser.msie && parseInt(a.jqx.browser.version) < 9) {
                var p = h.css("font-size");
                if (p) {
                    b = parseInt(p);
                    if (isNaN(b)) {
                        b = 0
                    }
                }
            }
            if (d == 0) {
                return {
                    width: i,
                    height: b
                }
            }
            var k = d * Math.PI * 2 / 360;
            var c = Math.abs(Math.sin(k));
            var j = Math.abs(Math.cos(k));
            var g = Math.abs(i * c + b * j);
            var l = Math.abs(i * j + b * c);
            return {
                width: a.jqx._rup(l),
                height: a.jqx._rup(g)
            }
        },
        text: function (o, l, k, r, p, A, C, B, n, g) {
            var s = C.stroke || "black";
            var q;
            if (!n) {
                n = "center"
            }
            if (!g) {
                g = "center"
            }
            B = false;
            if (B) {
                q = this.beginGroup();
                var e = this.createClipRect({
                    x: a.jqx._rup(l),
                    y: a.jqx._rup(k),
                    width: a.jqx._rup(r),
                    height: a.jqx._rup(p)
                });
                this.setClip(q, e)
            }
            var b = document.createElement("v:textbox");
            b.style.position = "absolute";
            var t = document.createElement("span");
            t.appendChild(document.createTextNode(o));
            if (C["class"]) {
                t.className = C["class"]
            }
            b.appendChild(t);
            var j = this._activeParent();
            j.appendChild(b);
            var D = a(b).width();
            var i = a(b).height();
            j.removeChild(b);
            var m = r || 0;
            var v = p || 0;
            if (!A || A == 0 || Math.abs(A) != 90) {
                if (n == "center") {
                    l += (m - D) / 2
                } else {
                    if (n == "right") {
                        l += (m - D)
                    }
                } if (g == "center") {
                    k = k + (v - i) / 2
                } else {
                    if (g == "bottom") {
                        k = k + v - i
                    }
                } if (!r) {
                    r = D
                }
                if (!p) {
                    p = i
                }
                if (!q) {
                    b.style.left = a.jqx._rup(l);
                    b.style.top = a.jqx._rup(k);
                    b.style.width = a.jqx._rup(r);
                    b.style.height = a.jqx._rup(p)
                }
                j.appendChild(b);
                if (q) {
                    this.endGroup();
                    return j
                }
                return b
            }
            var f = A * Math.PI * 2 / 360;
            var d = Math.abs(D * Math.sin(f) - i * Math.cos(f));
            var z = Math.abs(D * Math.cos(f) + i * Math.sin(f));
            if (n == "center") {
                l += (m - z) / 2
            } else {
                if (n == "right") {
                    l += (m - z)
                }
            } if (g == "center") {
                k = k + (v - d) / 2
            } else {
                if (g == "bottom") {
                    k = k + v - d
                }
            }
            l = a.jqx._rup(l);
            k = a.jqx._rup(k);
            var u = a.jqx._rup(l + z);
            var c = a.jqx._rup(k + d);
            if (Math.abs(A) == 90) {
                j.appendChild(b);
                b.style.left = a.jqx._rup(l);
                b.style.top = a.jqx._rup(k);
                b.style.filter = "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
                if (q) {
                    this.endGroup();
                    return j
                }
                return b
            }
            return b
        },
        shape: function (b, e) {
            var c = document.createElement(this._createElementMarkup(b));
            if (!c) {
                return undefined
            }
            for (var d in e) {
                c.setAttribute(d, e[d])
            }
            this._activeParent().appendChild(c);
            return c
        },
        line: function (e, g, d, f, h) {
            var b = "M " + e + "," + g + " L " + d + "," + f + " X E";
            var c = this.path(b);
            this.attr(c, h);
            return c
        },
        _createElementMarkup: function (b) {
            var c = "<v:" + b + ' style=""></v:' + b + ">";
            if (this._ie8mode) {
                c = c.replace('style=""', 'style="behavior: url(#default#VML);"')
            }
            return c
        },
        path: function (c, e) {
            var b = document.createElement(this._createElementMarkup("shape"));
            b.style.position = "absolute";
            b.coordsize = this._width + " " + this._height;
            b.coordorigin = "0 0";
            b.style.width = parseInt(this._width);
            b.style.height = parseInt(this._height);
            b.style.left = 0;
            b.style.top = 0;
            var d = document.createElement(this._createElementMarkup("path"));
            d.v = c;
            b.appendChild(d);
            this._activeParent().appendChild(b);
            if (e) {
                this.attr(b, e)
            }
            return b
        },
        rect: function (b, g, c, d, f) {
            b = a.jqx._ptrnd(b);
            g = a.jqx._ptrnd(g);
            c = a.jqx._rup(c);
            d = a.jqx._rup(d);
            var e = this.shape("rect", f);
            e.style.position = "absolute";
            e.style.left = b;
            e.style.top = g;
            e.style.width = c;
            e.style.height = d;
            e.strokeweight = 0;
            return e
        },
        circle: function (b, e, d) {
            var c = this.shape("oval");
            b = a.jqx._ptrnd(b - d);
            e = a.jqx._ptrnd(e - d);
            d = a.jqx._rup(d);
            c.style.position = "absolute";
            c.style.left = b;
            c.style.top = e;
            c.style.width = d * 2;
            c.style.height = d * 2;
            return c
        },
        updateCircle: function (d, b, e, c) {
            if (b == undefined) {
                b = parseFloat(d.style.left) + parseFloat(d.style.width) / 2
            }
            if (e == undefined) {
                e = parseFloat(d.style.top) + parseFloat(d.style.height) / 2
            }
            if (c == undefined) {
                c = parseFloat(d.width) / 2
            }
            b = a.jqx._ptrnd(b - c);
            e = a.jqx._ptrnd(e - c);
            c = a.jqx._rup(c);
            d.style.left = b;
            d.style.top = e;
            d.style.width = c * 2;
            d.style.height = c * 2
        },
        pieSlicePath: function (k, j, h, r, B, C, d) {
            if (!r) {
                r = 1
            }
            var m = Math.abs(B - C);
            var p = m > 180 ? 1 : 0;
            if (m > 360) {
                B = 0;
                C = 360
            }
            var q = B * Math.PI * 2 / 360;
            var i = C * Math.PI * 2 / 360;
            var w = k,
                v = k,
                f = j,
                e = j;
            var n = !isNaN(h) && h > 0;
            if (n) {
                d = 0
            }
            if (d > 0) {
                var l = m / 2 + B;
                var A = l * Math.PI * 2 / 360;
                k += d * Math.cos(A);
                j -= d * Math.sin(A)
            }
            if (n) {
                var u = h;
                w = a.jqx._ptrnd(k + u * Math.cos(q));
                f = a.jqx._ptrnd(j - u * Math.sin(q));
                v = a.jqx._ptrnd(k + u * Math.cos(i));
                e = a.jqx._ptrnd(j - u * Math.sin(i))
            }
            var t = a.jqx._ptrnd(k + r * Math.cos(q));
            var s = a.jqx._ptrnd(k + r * Math.cos(i));
            var c = a.jqx._ptrnd(j - r * Math.sin(q));
            var b = a.jqx._ptrnd(j - r * Math.sin(i));
            r = a.jqx._ptrnd(r);
            h = a.jqx._ptrnd(h);
            k = a.jqx._ptrnd(k);
            j = a.jqx._ptrnd(j);
            var g = Math.round(B * 65535);
            var z = Math.round(C - B) * 65536;
            var o = "";
            if (n) {
                o = "M" + w + " " + f;
                o += " AE " + k + " " + j + " " + h + " " + h + " " + g + " " + z;
                o += " L " + s + " " + b;
                g = Math.round(B - C) * 65535;
                z = Math.round(C) * 65536;
                o += " AE " + k + " " + j + " " + r + " " + r + " " + z + " " + g;
                o += " L " + w + " " + f
            } else {
                o = "M" + k + " " + j;
                o += " AE " + k + " " + j + " " + r + " " + r + " " + g + " " + z
            }
            o += " X E";
            return o
        },
        pieslice: function (k, i, h, e, g, b, j, d) {
            var f = this.pieSlicePath(k, i, h, e, g, b, j);
            var c = this.path(f, d);
            if (d) {
                this.attr(c, d)
            }
            return c
        },
        _keymap: [{
            svg: "fill",
            vml: "fillcolor"
        }, {
            svg: "stroke",
            vml: "strokecolor"
        }, {
            svg: "stroke-width",
            vml: "strokeweight"
        }, {
            svg: "stroke-dasharray",
            vml: "dashstyle"
        }, {
            svg: "fill-opacity",
            vml: "fillopacity"
        }, {
            svg: "opacity",
            vml: "opacity"
        }, {
            svg: "cx",
            vml: "style.left"
        }, {
            svg: "cy",
            vml: "style.top"
        }, {
            svg: "height",
            vml: "style.height"
        }, {
            svg: "width",
            vml: "style.width"
        }, {
            svg: "x",
            vml: "style.left"
        }, {
            svg: "y",
            vml: "style.top"
        }, {
            svg: "d",
            vml: "v"
        }, {
            svg: "display",
            vml: "style.display"
        }],
        _translateParam: function (b) {
            for (var c in this._keymap) {
                if (this._keymap[c].svg == b) {
                    return this._keymap[c].vml
                }
            }
            return b
        },
        attr: function (c, e) {
            if (!c || !e) {
                return
            }
            for (var d in e) {
                var b = this._translateParam(d);
                if (b == "fillcolor" && e[d].indexOf("grd") != -1) {
                    c.type = e[d]
                } else {
                    if (b == "opacity" || b == "fillopacity") {
                        if (c.fill) {
                            c.fill.opacity = e[d]
                        }
                    } else {
                        if (b == "textContent") {
                            c.children[0].innerText = e[d]
                        } else {
                            if (b == "dashstyle") {
                                c.dashstyle = e[d].replace(",", " ")
                            } else {
                                if (b.indexOf("style.") == -1) {
                                    c[b] = e[d]
                                } else {
                                    c.style[b.replace("style.", "")] = e[d]
                                }
                            }
                        }
                    }
                }
            }
        },
        getAttr: function (d, c) {
            var b = this._translateParam(c);
            if (b == "opacity" || b == "fillopacity") {
                if (d.fill) {
                    return d.fill.opacity
                } else {
                    return 1
                }
            }
            if (b.indexOf("style.") == -1) {
                return d[b]
            }
            return d.style[b.replace("style.", "")]
        },
        _gradients: {},
        _toRadialGradient: function (b, d, c) {
            return b
        },
        _toLinearGradient: function (g, i, j) {
            if (this._ie8mode) {
                return g
            }
            var d = "grd" + g.replace("#", "") + (i ? "v" : "h");
            var e = "#" + d + "";
            if (this._gradients[e]) {
                return e
            }
            var f = document.createElement(this._createElementMarkup("fill"));
            f.type = "gradient";
            f.method = "linear";
            f.angle = i ? 0 : 90;
            var c = "";
            for (var h in j) {
                if (h > 0) {
                    c += ", "
                }
                c += j[h][0] + "% " + a.jqx._adjustColor(g, j[h][1])
            }
            f.colors = c;
            var b = document.createElement(this._createElementMarkup("shapetype"));
            b.appendChild(f);
            b.id = d;
            this.canvas.appendChild(b);
            return e
        }
    };
    a.jqx.HTML5Renderer = function () {};
    a.jqx.ptrnd = function (c) {
        if (Math.abs(Math.round(c) - c) == 0.5) {
            return c
        }
        var b = Math.round(c);
        if (b < c) {
            b = b - 1
        }
        return b + 0.5
    };
    a.jqx.HTML5Renderer.prototype = {
        _elements: {},
        init: function (b) {
            try {
                this.host = b;
                this.host.append("<canvas id='__jqxCanvasWrap' style='width:100%; height: 100%;'/>");
                this.canvas = b.find("#__jqxCanvasWrap");
                this.canvas[0].width = b.width();
                this.canvas[0].height = b.height();
                this.ctx = this.canvas[0].getContext("2d")
            } catch (c) {
                return false
            }
            return true
        },
        getContainer: function () {
            if (this.canvas && this.canvas.length == 1) {
                return this.canvas
            }
            return undefined
        },
        getRect: function () {
            return {
                x: 0,
                y: 0,
                width: this.canvas[0].width - 1,
                height: this.canvas[0].height - 1
            }
        },
        beginGroup: function () {},
        endGroup: function () {},
        setClip: function () {},
        createClipRect: function (b) {},
        addHandler: function (b, d, c) {},
        clear: function () {
            this._elements = {};
            this._maxId = 0;
            this._renderers._gradients = {};
            this._gradientId = 0
        },
        removeElement: function (b) {
            if (this._elements[b.id]) {
                delete this._elements[b, id]
            }
        },
        _maxId: 0,
        shape: function (b, e) {
            var c = {
                type: b,
                id: this._maxId++
            };
            for (var d in e) {
                c[d] = e[d]
            }
            this._elements[c.id] = c;
            return c
        },
        attr: function (b, d) {
            for (var c in d) {
                b[c] = d[c]
            }
        },
        rect: function (b, g, c, e, f) {
            if (isNaN(b)) {
                throw 'Invalid value for "x"'
            }
            if (isNaN(g)) {
                throw 'Invalid value for "y"'
            }
            if (isNaN(c)) {
                throw 'Invalid value for "width"'
            }
            if (isNaN(e)) {
                throw 'Invalid value for "height"'
            }
            var d = this.shape("rect", {
                x: b,
                y: g,
                width: c,
                height: e
            });
            if (f) {
                this.attr(d, f)
            }
            return d
        },
        path: function (b, d) {
            var c = this.shape("path", d);
            this.attr(c, {
                d: b
            });
            return c
        },
        line: function (c, e, b, d, f) {
            return this.path("M " + c + "," + e + " L " + b + "," + d, f)
        },
        circle: function (b, f, d, e) {
            var c = this.shape("circle", {
                x: b,
                y: f,
                r: d
            });
            if (e) {
                this.attr(c, e)
            }
            return c
        },
        pieSlicePath: function (c, h, g, e, f, d, b) {
            return a.jqx.commonRenderer.pieSlicePath(c, h, g, e, f, d, b)
        },
        pieslice: function (j, h, g, e, f, b, i, c) {
            var d = this.path(this.pieSlicePath(j, h, g, e, f, b, i), c);
            this.attr(d, {
                x: j,
                y: h,
                innerRadius: g,
                outerRadius: e,
                angleFrom: f,
                angleTo: b
            });
            return d
        },
        _getCSSStyle: function (c) {
            var g = document.styleSheets;
            try {
                for (var d = 0; d < g.length; d++) {
                    for (var b = 0; g[d].cssRules && b < g[d].cssRules.length; b++) {
                        if (g[d].cssRules[b].selectorText.indexOf(c) != -1) {
                            return g[d].cssRules[b].style
                        }
                    }
                }
            } catch (f) {}
            return {}
        },
        measureText: function (o, e, f) {
            var k = "Arial";
            var p = "10pt";
            var m = "";
            if (f["class"]) {
                var b = this._getCSSStyle(f["class"]);
                if (b.fontSize) {
                    p = b.fontSize
                }
                if (b.fontFamily) {
                    k = b.fontFamily
                }
                if (b.fontWeight) {
                    m = b.fontWeight
                }
            }
            this.ctx.font = m + " " + p + " " + k;
            var h = this.ctx.measureText(o).width;
            var n = document.createElement("span");
            n.font = this.ctx.font;
            n.textContent = o;
            document.body.appendChild(n);
            var c = n.offsetHeight * 0.6;
            document.body.removeChild(n);
            if (e == 0) {
                return {
                    width: h,
                    height: c
                }
            }
            var j = e * Math.PI * 2 / 360;
            var d = Math.abs(Math.sin(j));
            var i = Math.abs(Math.cos(j));
            var g = Math.abs(h * d + c * i);
            var l = Math.abs(h * i + c * d);
            return {
                width: a.jqx._rup(l),
                height: a.jqx._rup(g)
            }
        },
        text: function (m, l, j, c, n, f, g, d, h, k, e) {
            var o = this.shape("text", {
                text: m,
                x: l,
                y: j,
                width: c,
                height: n,
                angle: f,
                clip: d,
                halign: h,
                valign: k,
                rotateAround: e
            });
            if (g) {
                this.attr(o, g)
            }
            o.fontFamily = "Arial";
            o.fontSize = "10pt";
            o.fontWeight = "";
            o.color = "#000000";
            if (g["class"]) {
                var b = this._getCSSStyle(g["class"]);
                o.fontFamily = b.fontFamily || o.fontFamily;
                o.fontSize = b.fontSize || o.fontSize;
                o.fontWeight = b.fontWeight || o.fontWeight;
                o.color = b.color || o.color
            }
            var i = this.measureText(m, 0, g);
            o.textWidth = i.width;
            o.textHeight = i.height;
            return o
        },
        _toLinearGradient: function (c, g, f) {
            if (this._renderers._gradients[c]) {
                return c
            }
            var b = [];
            for (var e = 0; e < f.length; e++) {
                b.push({
                    percent: f[e][0] / 100,
                    color: a.jqx._adjustColor(c, f[e][1])
                })
            }
            var d = "gr" + this._gradientId++;
            this.createGradient(d, g ? "vertical" : "horizontal", b);
            return d
        },
        _toRadialGradient: function (c, f) {
            if (this._renderers._gradients[c]) {
                return c
            }
            var b = [];
            for (var e = 0; e < f.length; e++) {
                b.push({
                    percent: f[e][0] / 100,
                    color: a.jqx._adjustColor(c, f[e][1])
                })
            }
            var d = "gr" + this._gradientId++;
            this.createGradient(d, "radial", b);
            return d
        },
        _gradientId: 0,
        createGradient: function (d, c, b) {
            this._renderers.createGradient(d, c, b)
        },
        _renderers: {
            _gradients: {},
            createGradient: function (d, c, b) {
                this._gradients[d] = {
                    orientation: c,
                    colorStops: b
                }
            },
            setStroke: function (b, c) {
                b.strokeStyle = c.stroke || "transparent";
                b.lineWidth = c["stroke-width"] || 1;
                if (b.setLineDash) {
                    if (c["stroke-dasharray"]) {
                        b.setLineDash(c["stroke-dasharray"].split(","))
                    } else {
                        b.setLineDash([])
                    }
                }
            },
            setFillStyle: function (m, e) {
                m.fillStyle = "transparent";
                if (e["fill-opacity"]) {
                    m.globalAlpha = e["fill-opacity"]
                } else {
                    m.globalAlpha = 1
                } if (e.fill && e.fill.indexOf("#") == -1 && this._gradients[e.fill]) {
                    var k = this._gradients[e.fill].orientation != "horizontal";
                    var g = this._gradients[e.fill].orientation == "radial";
                    var c = a.jqx.ptrnd(e.x);
                    var l = a.jqx.ptrnd(e.y);
                    var b = a.jqx.ptrnd(e.x + (k ? 0 : e.width));
                    var h = a.jqx.ptrnd(e.y + (k ? e.height : 0));
                    var j;
                    if ((e.type == "circle" || e.type == "path") && g) {
                        x = a.jqx.ptrnd(e.x);
                        y = a.jqx.ptrnd(e.y);
                        r1 = e.innerRadius || 0;
                        r2 = e.outerRadius || e.r || 0;
                        j = m.createRadialGradient(x, y, r1, x, y, r2)
                    }
                    if (!g) {
                        if (isNaN(c) || isNaN(b) || isNaN(l) || isNaN(h)) {
                            c = 0;
                            l = 0;
                            b = k ? 0 : m.canvas.width;
                            h = k ? m.canvas.height : 0
                        }
                        j = m.createLinearGradient(c, l, b, h)
                    }
                    var d = this._gradients[e.fill].colorStops;
                    for (var f = 0; f < d.length; f++) {
                        j.addColorStop(d[f].percent, d[f].color)
                    }
                    m.fillStyle = j
                } else {
                    if (e.fill) {
                        m.fillStyle = e.fill
                    }
                }
            },
            rect: function (b, c) {
                if (c.width == 0 || c.height == 0) {
                    return
                }
                b.fillRect(a.jqx.ptrnd(c.x), a.jqx.ptrnd(c.y), c.width, c.height);
                b.strokeRect(a.jqx.ptrnd(c.x), a.jqx.ptrnd(c.y), c.width, c.height)
            },
            circle: function (b, c) {
                if (c.r == 0) {
                    return
                }
                b.beginPath();
                b.arc(a.jqx.ptrnd(c.x), a.jqx.ptrnd(c.y), c.r, 0, Math.PI * 2, false);
                b.closePath();
                b.fill();
                b.stroke()
            },
            _parsePoint: function (c) {
                var b = this._parseNumber(c);
                var d = this._parseNumber(c);
                return ({
                    x: b,
                    y: d
                })
            },
            _parseNumber: function (d) {
                var e = false;
                for (var b = this._pos; b < d.length; b++) {
                    if ((d[b] >= "0" && d[b] <= "9") || d[b] == "." || (d[b] == "-" && !e)) {
                        e = true;
                        continue
                    }
                    if (!e && (d[b] == " " || d[b] == ",")) {
                        this._pos++;
                        continue
                    }
                    break
                }
                var c = parseFloat(d.substring(this._pos, b));
                if (isNaN(c)) {
                    return undefined
                }
                this._pos = b;
                return c
            },
            _pos: 0,
            _cmds: "mlcaz",
            _lastCmd: "",
            _isRelativeCmd: function (b) {
                return a.jqx.string.contains(this._cmds, b)
            },
            _parseCmd: function (b) {
                for (var c = this._pos; c < b.length; c++) {
                    if (a.jqx.string.containsIgnoreCase(this._cmds, b[c])) {
                        this._pos = c + 1;
                        this._lastCmd = b[c];
                        return this._lastCmd
                    }
                    if (b[c] == " ") {
                        this._pos++;
                        continue
                    }
                    if (b[c] >= "0" && b[c] <= "9") {
                        this._pos = c;
                        if (this._lastCmd == "") {
                            break
                        } else {
                            return this._lastCmd
                        }
                    }
                }
                return undefined
            },
            _toAbsolutePoint: function (b) {
                return {
                    x: this._currentPoint.x + b.x,
                    y: this._currentPoint.y + b.y
                }
            },
            _currentPoint: {
                x: 0,
                y: 0
            },
            path: function (C, L) {
                var z = L.d;
                this._pos = 0;
                this._lastCmd = "";
                var k = undefined;
                this._currentPoint = {
                    x: 0,
                    y: 0
                };
                C.beginPath();
                var G = 0;
                while (this._pos < z.length) {
                    var F = this._parseCmd(z);
                    if (F == undefined) {
                        break
                    }
                    if (F == "M" || F == "m") {
                        var D = this._parsePoint(z);
                        if (D == undefined) {
                            break
                        }
                        C.moveTo(D.x, D.y);
                        this._currentPoint = D;
                        if (k == undefined) {
                            k = D
                        }
                        continue
                    }
                    if (F == "L" || F == "l") {
                        var D = this._parsePoint(z);
                        if (D == undefined) {
                            break
                        }
                        C.lineTo(D.x, D.y);
                        this._currentPoint = D;
                        continue
                    }
                    if (F == "A" || F == "a") {
                        var g = this._parseNumber(z);
                        var f = this._parseNumber(z);
                        var J = this._parseNumber(z) * (Math.PI / 180);
                        var N = this._parseNumber(z);
                        var e = this._parseNumber(z);
                        var o = this._parsePoint(z);
                        if (this._isRelativeCmd(F)) {
                            o = this._toAbsolutePoint(o)
                        }
                        if (g == 0 || f == 0) {
                            continue
                        }
                        var h = this._currentPoint;
                        var I = {
                            x: Math.cos(J) * (h.x - o.x) / 2 + Math.sin(J) * (h.y - o.y) / 2,
                            y: -Math.sin(J) * (h.x - o.x) / 2 + Math.cos(J) * (h.y - o.y) / 2
                        };
                        var j = Math.pow(I.x, 2) / Math.pow(g, 2) + Math.pow(I.y, 2) / Math.pow(f, 2);
                        if (j > 1) {
                            g *= Math.sqrt(j);
                            f *= Math.sqrt(j)
                        }
                        var p = (N == e ? -1 : 1) * Math.sqrt(((Math.pow(g, 2) * Math.pow(f, 2)) - (Math.pow(g, 2) * Math.pow(I.y, 2)) - (Math.pow(f, 2) * Math.pow(I.x, 2))) / (Math.pow(g, 2) * Math.pow(I.y, 2) + Math.pow(f, 2) * Math.pow(I.x, 2)));
                        if (isNaN(p)) {
                            p = 0
                        }
                        var H = {
                            x: p * g * I.y / f,
                            y: p * -f * I.x / g
                        };
                        var B = {
                            x: (h.x + o.x) / 2 + Math.cos(J) * H.x - Math.sin(J) * H.y,
                            y: (h.y + o.y) / 2 + Math.sin(J) * H.x + Math.cos(J) * H.y
                        };
                        var A = function (i) {
                            return Math.sqrt(Math.pow(i[0], 2) + Math.pow(i[1], 2))
                        };
                        var t = function (m, i) {
                            return (m[0] * i[0] + m[1] * i[1]) / (A(m) * A(i))
                        };
                        var M = function (m, i) {
                            return (m[0] * i[1] < m[1] * i[0] ? -1 : 1) * Math.acos(t(m, i))
                        };
                        var E = M([1, 0], [(I.x - H.x) / g, (I.y - H.y) / f]);
                        var n = [(I.x - H.x) / g, (I.y - H.y) / f];
                        var l = [(-I.x - H.x) / g, (-I.y - H.y) / f];
                        var K = M(n, l);
                        if (t(n, l) <= -1) {
                            K = Math.PI
                        }
                        if (t(n, l) >= 1) {
                            K = 0
                        }
                        if (e == 0 && K > 0) {
                            K = K - 2 * Math.PI
                        }
                        if (e == 1 && K < 0) {
                            K = K + 2 * Math.PI
                        }
                        var t = (g > f) ? g : f;
                        var w = (g > f) ? 1 : g / f;
                        var q = (g > f) ? f / g : 1;
                        C.translate(B.x, B.y);
                        C.rotate(J);
                        C.scale(w, q);
                        C.arc(0, 0, t, E, E + K, 1 - e);
                        C.scale(1 / w, 1 / q);
                        C.rotate(-J);
                        C.translate(-B.x, -B.y);
                        continue
                    }
                    if ((F == "Z" || F == "z") && k != undefined) {
                        C.lineTo(k.x, k.y);
                        this._currentPoint = k;
                        continue
                    }
                    if (F == "C" || F == "c") {
                        var d = this._parsePoint(z);
                        var c = this._parsePoint(z);
                        var b = this._parsePoint(z);
                        C.bezierCurveTo(d.x, d.y, c.x, c.y, b.x, b.y);
                        this._currentPoint = b;
                        continue
                    }
                }
                C.fill();
                C.stroke();
                C.closePath()
            },
            text: function (s, A) {
                var m = a.jqx.ptrnd(A.x);
                var k = a.jqx.ptrnd(A.y);
                var q = a.jqx.ptrnd(A.width);
                var p = a.jqx.ptrnd(A.height);
                var o = A.halign;
                var g = A.valign;
                var v = A.angle;
                var c = A.rotateAround;
                var z = A.clip;
                if (z == undefined) {
                    z = true
                }
                s.save();
                if (!o) {
                    o = "center"
                }
                if (!g) {
                    g = "center"
                }
                if (z) {
                    s.rect(m - 2, k - 2, q + 5, p + 5);
                    s.clip()
                }
                var B = A.textWidth;
                var i = A.textHeight;
                var n = q || 0;
                var t = p || 0;
                s.fillStyle = A.color;
                s.font = A.fontWeight + " " + A.fontSize + " " + A.fontFamily;
                if (!v || v == 0) {
                    if (o == "center") {
                        m += (n - B) / 2
                    } else {
                        if (o == "right") {
                            m += (n - B)
                        }
                    }
                    k += i;
                    if (g == "center") {
                        k += (t - i) / 2
                    } else {
                        if (g == "bottom") {
                            k += t - i
                        }
                    } if (!q) {
                        q = B
                    }
                    if (!p) {
                        p = i
                    }
                    s.fillText(A.text, m, k);
                    s.restore();
                    return
                }
                var e = v * Math.PI * 2 / 360;
                var u = Math.sin(e);
                var f = Math.cos(e);
                var j = B * u;
                var l = B * f;
                var r = k;
                var d = m;
                if (o == "center" || o == "" || o == "undefined") {
                    m = m + q / 2
                } else {
                    if (o == "right") {
                        m = m + q
                    }
                } if (g == "center" || g == "" || g == "undefined") {
                    k = k + p / 2
                } else {
                    if (g == "bottom") {
                        k += p - i / 2
                    } else {
                        if (g == "top") {
                            k += i / 2
                        }
                    }
                }
                c = c || "";
                var C = "middle";
                if (c.indexOf("top") != -1) {
                    C = "top"
                } else {
                    if (c.indexOf("bottom") != -1) {
                        C = "bottom"
                    }
                }
                var b = "center";
                if (c.indexOf("left") != -1) {
                    b = "left"
                } else {
                    if (c.indexOf("right") != -1) {
                        b = "right"
                    }
                } if (b == "center") {
                    m -= l / 2;
                    k -= j / 2
                } else {
                    if (b == "right") {
                        m -= l;
                        k -= j
                    }
                } if (C == "top") {
                    m -= i * u
                } else {
                    if (C == "middle") {
                        m -= i * u / 2
                    }
                }
                m = a.jqx._rup(m);
                k = a.jqx._rup(k);
                s.translate(m, k);
                s.rotate(e);
                s.fillText(A.text, 0, 0);
                s.restore()
            }
        },
        refresh: function () {
            this.ctx.clearRect(0, 0, this.canvas[0].width, this.canvas[0].height);
            for (var b in this._elements) {
                var c = this._elements[b];
                this._renderers.setFillStyle(this.ctx, c);
                this._renderers.setStroke(this.ctx, c);
                this._renderers[this._elements[b].type](this.ctx, c)
            }
        }
    }
})(jQuery);