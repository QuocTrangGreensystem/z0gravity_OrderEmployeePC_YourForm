(function ($)
{
  /******************************* task list toggling **************************/

  var recur = function ($item, state) {
    var notState = state === 'toggled' ? 'untoggled' : 'toggled';
    $item.removeClass('task-list__header--' + notState);
    $item.addClass('task-list__header--' + state);
    var $items = $('[data-parent="' + $item.attr('data-id') + '"]');
    $items.each(function ()
    {
      console.log($(this));
      $(this).removeClass('task-list__header-parent--' + notState);
      $(this).addClass('task-list__header-parent--' + state);
      if (state === 'untoggled') {
        recur($(this), state);
      }
    });
  };

  $('.task-list__header').on('click', function () {
    var $this = $(this);
    var state = $this.hasClass('task-list__header--toggled') ? 'untoggled' : 'toggled';
    recur($this, state);
  });
  $('.task-list__header--sub, .task-list__task-row').addClass('task-list__header-parent--untoggled');
}(window.jQuery.noConflict()));
