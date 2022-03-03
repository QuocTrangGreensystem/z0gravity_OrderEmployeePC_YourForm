(function ($)
{
  'use strict';

  var hideRecursively = function (element)
  {
    element.find('[aria-controls]').each(function (index, item)
    {
      var controlled = $('#' + $(item).attr('aria-controls'));
      if (controlled.length > 0)
      {
        controlled.attr('aria-expanded', 'false');
        hideRecursively(controlled);
      }
    });
  };

  var setExpanded = function (element, state)
  {
    element.attr('aria-expanded', state);

    if (state === 'false')
    {
      hideRecursively(element);
    }
  };

  var showRecursively = function (element, show)
  {
    element.attr('aria-expanded', show);
    var parent = $('[aria-controls="' + element.attr('id') + '"]').closest('.col-menu__col');
    if (parent.length > 0)
    {
      showRecursively(parent, 'true');
    }
  };

  $('.col-menu [aria-controls]').on('click', function (event) {
    var target = $('#' + $(this).attr('aria-controls'));
    var oldState = target.attr('aria-expanded');
    var newState = oldState === 'true' ? 'false' : 'true';

    $('.col-menu__col').attr('aria-expanded', 'false');
    showRecursively(target, newState);

    $('.col-menu__btn').each(function (index, item) {
      if ($('#' + $(this).attr('aria-controls')).attr('aria-expanded') === 'true')
      {
        $(this).addClass('col-menu__btn--toggled');
        $(this).removeClass('col-menu__btn--untoggled');
      }
      else
      {
        $(this).addClass('col-menu__btn--untoggled');
        $(this).removeClass('col-menu__btn--toggled');
      }
    });
  });
}(window.jQuery.noConflict()));
