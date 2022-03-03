(function ($) {
  'use strict';

  window.scala = window.scale || {};
  window.scala.Togglable = function ($properties)
  {
    var self = this;
    this.ID = this.toggleID;
    this.toggleID += 1;
    this.element = $properties.element;
    this.togglingNodes = $properties.togglingNodes || this.element;
    this.name = $properties.name;
    this.classHolder = ($properties.classHolder && $properties.classHolder.length > 0) ? $properties.classHolder : this.element;
    this.defaultState = $properties.defaultState || 'off';
    this.enablingNodes = $properties.enablingNodes || $();
    if ($properties.enablingNodesInside) { this.enablingNodes = this.enablingNodes.add($properties.enablingNodesInside); }
    this.disablingNodes = $properties.disablingNodes || $();
    if ($properties.disablingNodesInside) { this.disablingNodes = this.disablingNodes.add($properties.disablingNodesInside); }
    this.preventingNodes = $properties.preventingNodes || $();

    this.togglingNodes.click(function (event) {
      console.log($properties.preventingNodes);
      if (!self.shouldPrevent(event.target, this))
      {
        event.preventDefault();
        self.toggle();
      }
    });

    if (this.disablingNodes)
    {
      this.disablingNodes.click(function (event)
      {
        if (!self.shouldPrevent(event.target, this)
          && !self.isInEnablingNodes(event.target)
          && !self.isInTogglingNodes(event.target))
        {
          self.toggle('off');
        }
      });
    }

    if (this.enablingNodes)
    {
      this.enablingNodes.click(function (event)
      {
        if (!self.shouldPrevent(event.target, this) && !self.isInTogglingNodes(event.target))
        {
          self.toggle('on');
        }
      });
    }

    this.toggle(this.defaultState);
  };

  window.scala.Togglable.toggleID = 0;

  window.scala.Togglable.prototype.isInEnablingNodes = function (eventTarget)
  {
    return this.enablingNodes.find(eventTarget).length !== 0 || eventTarget === this.enablingNodes[0];
  };

  window.scala.Togglable.prototype.isInTogglingNodes = function (eventTarget)
  {
    return this.togglingNodes.find(eventTarget).length !== 0 || eventTarget === this.togglingNodes[0];
  };

  window.scala.Togglable.prototype.shouldPrevent = function (eventTarget, initiator)
  {
    // if deepest clicked element is not in a group preventing toggling then don't prevent it
    if (this.preventingNodes.find(eventTarget).length === 0 && this.preventingNodes[0] !== eventTarget)
    {
      return false;
    }
    // if it is in a preventing group, prevent it
    else
    {
      if (this.preventingNodes.find(initiator).length === 0) { return true; }
      //unless the initiator is a children of the preventing node
      else { return false; }
    }
  };

  window.scala.Togglable.prototype.toggle = function (toggle)
  {
    if (toggle === 'on')
    {
      this.classHolder.removeClass(this.name + '--untoggled');
      this.classHolder.addClass(this.name + '--toggled');
    }
    else if (toggle === 'off')
    {
      this.classHolder.removeClass(this.name + '--toggled');
      this.classHolder.addClass(this.name + '--untoggled');
    }
    else
    {
      this.classHolder.toggleClass(this.name + '--toggled');
      this.classHolder.toggleClass(this.name + '--untoggled');
    }
  };

  //parsing page and creating togglable elements from data attributes
  $('[data-tgl]').each(function () {
    var $this = $(this);
    var classHolder = $this.closest($this.attr('data-tgl-class-holder')).first();
    classHolder = classHolder.length > 0 ? classHolder : $this;
    var name = $this.attr('data-tgl');
    var preventingNodes = classHolder.find('[data-tgl-prevent="' + name + '"]');
    var togglingNodes = $this.find($this.attr('data-tgl-togglers'));

    var enablingNodes = $($this.attr('data-tgl-on'));
    var enablingNodesInside = $this.find($this.attr('data-tgl-on-inside'));
    var disablingNodes = $($this.attr('data-tgl-off'));
    var disablingNodesInside = $this.find($this.attr('data-tgl-off-inside'));

    //if there is no value for the togglers and the attribute hasn't been specified,
    // we assume the element itself is supposed to toggle the class
    //if the attribute has been explicitly specified it means there are no togglers
    if (togglingNodes.length <= 0 && $this.attr('data-tgl-togglers') === undefined
    && enablingNodes.length <= 0 && $this.attr('data-tgl-on') === undefined)
    {
      togglingNodes = $this;
    }

    var togglable = new window.scala.Togglable({
      element: $this,
      togglingNodes: togglingNodes,
      name: name,
      classHolder: classHolder,
      defaultState: $this.attr('data-tgl-default'),
      enablingNodes: enablingNodes,
      disablingNodes: disablingNodes,
      enablingNodesInside: enablingNodesInside,
      disablingNodesInside: disablingNodesInside,
      preventingNodes: preventingNodes
    });
  });
})(jQuery);
