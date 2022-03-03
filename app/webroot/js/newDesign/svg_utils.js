var z0g = window.z0g || {};

z0g.SVGUtils = {
  getParentSVG: function (element)
  {
    while (element.parentNode)
    {
      if (/svg/i.test(element.parentNode.tagName))
      {
        return element.parentNode;
      }
      element = element.parentNode;
    }
    return null;
  },

  getSVGRelativeWidthFraction: function (SVG)
  {
    var attr = SVG.getAttribute('width');
    var parentSVG = this.getParentSVG(SVG);
    if (attr)
    {
      var regexResult = /([\d\.]*)(.*)/.exec(attr);
      var attrValue = Number(regexResult[1]);
      var attrUnit = regexResult[2];

      if (parentSVG)
      {
        if (attrUnit === '%')
        {
          return attrValue / 100;
        }
        else
        {
          return attrValue / this.getSVGViewboxWidth(parentSVG);
        }
      }
      else
      {
        return 1;
      }
    }
    else
    {
      return 1;
    }
  },

  getSVGViewboxWidth: function (SVG)
  {
    var VB = this.getSVGViewbox(SVG);
    return VB[2] - VB[0];
  },

  getSVGViewbox: function (SVG)
  {
    var viewboxSTR = SVG.getAttribute('viewBox') ? SVG.getAttribute('viewBox') : SVG.getAttribute('viewbox');
    if (viewboxSTR)
    {
      var viewbox = viewboxSTR.replace(/ /g, ',').split(',');
      viewbox.forEach(function (item, index) { viewbox[index] = Number(item); });
      return viewbox;
    }
    else
    {
      var parent = this.getParentSVG(SVG);
      if (parent)
      {
        return this.getSVGViewbox(parent);
      }
      return null;
    }
  },

  getSVGScale: function (SVG)
  {
    var parent = this.getParentSVG(SVG);
    if (parent)
    {
      return this.getSVGRelativeWidthFraction(SVG) * this.getSVGScale(parent);
    }
    else
    {
      return 1;
    }
  },

  computeElementScale: function (element)
  {
    var parent = this.getParentSVG(element);
    return 1 / this.getSVGViewboxWidth(parent) * this.getSVGScale(parent);

    return scale;
  },

  getRootSVG: function (element)
  {
    var parent;
    while (element)
    {
      parent = this.getParentSVG(element);
      if (parent)
      {
        element = parent;
      }
      else
      {
        break;
      }
    }
    return element;
  },

  getSVGSize: function (SVG) {
    var width = Number(/\d*/.exec(window.getComputedStyle(SVG).width)[0]);
    var height = Number(/\d*/.exec(window.getComputedStyle(SVG).height)[0]);
    return Math.min(width, height);
  },

  getMatrix: function ($attribute)
  {
    if (!$attribute) { return null; }

    var TFType = $attribute.match(/([a-z]+)/igm)[0];
    var values = $attribute.match(/(-?[\d.]+)/igm);

    var matrices = [];
    var tX;
    var tY;
    var angle;

    if (TFType === 'matrix')
    {
      return [Number(values[0]), Number(values[2]), this.getCoord(values[4]), Number(values[1]), Number(values[3]), this.getCoord(values[5]), 0, 0, 1];
    }
    else if (TFType === 'rotate')
    {
      angle = Number(values[0]) * (Math.PI / 180);
      tX = this.getCoord(Number(values[1] || 0));
      tY = this.getCoord(Number(values[2] || 0));
      var m1 = [1, 0, tX, 0, 1, tY, 0, 0, 1];
      var m2 = [Math.cos(angle), -Math.sin(angle), 0, Math.sin(angle), Math.cos(angle), 0, 0, 0, 1];
      var m3 = [1, 0, -tX, 0, 1, -tY, 0, 0, 1];

      matrices.push(m1, m2, m3);
      var p = m1;

      for (var i = 1, matricesLength = matrices.length; i < matricesLength; i += 1)
      {
        var currMat = matrices[i];
        var newP = [0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (var k = 0; k < 9; k += 1)
        {
          var row = Math.floor(k / 3);
          var col = k % 3;
          //var mVal = p[row * col - 1];
          for (var pos = 0; pos < 3; pos += 1)
          {
            newP[k] = newP[k] + p[row * 3 + pos] * currMat[pos * 3 + col];
          }
        }
        p = newP;
      }
      return p;
    }
    else if (TFType === 'translate')
    {
      tX = this.getCoord(Number(values[0] || 0));
      tY = this.getCoord(Number(values[1] || 0));
      return [1, 0, tX, 0, 1, tY, 0, 0, 1];
    }
    else if (TFType === 'scale')
    {
      var sX = this.getCoord(Number(values[0] || 0));
      var sY = this.getCoord(Number(values[1] || 0));
      return [sX, 0, 0, 0, sY, 0];
    }
    else if (TFType === 'skewX')
    {
      angle = Number(values[0]) * (Math.PI / 180);
      return [1, Math.tan(angle), 0, 0, 1, 0, 0, 0, 1];
    }
    else if (TFType === 'skewY')
    {
      angle = Number(values[0]) * (Math.PI / 180);
      return [1, 0, 0, Math.tan(angle), 1, 0, 0, 0, 1];
    }
  }
};
