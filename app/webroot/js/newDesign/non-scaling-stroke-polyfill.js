var vectorEffectSupported = false;
var elm = document.createElementNS('http://www.w3.org/2000/svg', 'g');
if (elm.style.vectorEffect != undefined)
{
  vectorEffectSupported = true;
}

var adjustStrokeScale = function (element)
{
  if (!vectorEffectSupported)
  {
    var strokeWidth;
    if (element.style.strokeWidth || element.getAttribute('stroke-width'))
    {
      strokeWidth = Number(/\d*/.exec(window.getComputedStyle(element).strokeWidth)[0]);
    }
    else
    {
      strokeWidth = 1;
    }

    var rootSVG = z0g.SVGUtils.getRootSVG(element);
    var rootSVGWidth = Number(/\d*/.exec(window.getComputedStyle(rootSVG).width)[0]);
    var result = z0g.SVGUtils.computeElementScale(element);
    var output = strokeWidth / (rootSVGWidth * result);
    element.style.strokeWidth = output + 'px';
  }
};

Polyfill({ declarations: ['vector-effect:*'] }).doMatched(function (rules)
{
  rules.each(function (rule)
  {
    if (rule.getDeclaration()['vector-effect'] === 'non-scaling-stroke')
    {
      var selectors = document.querySelectorAll(rule.getSelectors());
      for (var i = 0, length = selectors.length; i < length; i += 1)
      {
        var curr = selectors[i];
        adjustStrokeScale(curr);
      }
    }
  });
});

var viaAttributes = document.querySelectorAll('[vector-effect="non-scaling-stroke"]');
for (var i = 0, length = viaAttributes.length; i < length; i += 1)
{
  var curr = viaAttributes[i];
  adjustStrokeScale(curr);
}

