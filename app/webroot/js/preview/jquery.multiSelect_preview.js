/*
// jQuery multiSelect
//
// Version 1.2.2 beta
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 09 September 2009
//
// Visit http://abeautifulsite.net/notebook/62 for more information
//
// (Amended by Andy Richmond, Letters & Science Deans' Office, University of California, Davis)
//
// Usage: $('#control_id').multiSelect( options, callback )
//
// Options:  selectAll          - whether or not to display the Select All option; true/false, default = true
//           selectAllText      - text to display for selecting/unselecting all options simultaneously
//           noneSelected       - text to display when there are no selected items in the list
//           oneOrMoreSelected  - text to display when there are one or more selected items in the list
//                                (note: you can use % as a placeholder for the number of items selected).
//                                Use * to show a comma separated list of all selected; default = '% selected'
//           optGroupSelectable - whether or not optgroups are selectable if you use them; true/false, default = false
//           listHeight         - the max height of the droptdown options
//
// Dependencies:  jQuery 1.2.6 or higher (http://jquery.com/)
//
// Change Log:
//
//		1.0.1	- Updated to work with jQuery 1.2.6+ (no longer requires the dimensions plugin)
//				- Changed $(this).offset() to $(this).position(), per James' and Jono's suggestions
//
//		1.0.2	- Fixed issue where dropdown doesn't scroll up/down with keyboard shortcuts
//				- Changed '$' in setTimeout to use 'jQuery' to support jQuery.noConflict
//				- Renamed from jqueryMultiSelect.* to jquery.multiSelect.* per the standard recommended at
//				  http://docs.jquery.com/Plugins/Authoring (does not affect API methods)
//
//		1.0.3	- Now uses the bgiframe plugin (if it exists) to fix the IE6 layering bug.
//              - Forces IE6 to use a min-height of 200px (this needs to be added to the options)
//
//		1.1.0	- Added the ability to update the options dynamically via javascript: multiSelectOptionsUpdate(JSON)
//              - Added a title that displays the whole comma delimited list when using oneOrMoreSelected = *
//              - Moved some of the functions to be closured to make them private
//              - Changed the way the keyboard navigation worked to more closely match how a standard dropdown works
//              - ** by Andy Richmond **
//
//		1.2.0	- Added support for optgroups
//              - Added the ability for selectable optgroups (i.e. select all for an optgroup)
//              - ** by Andy Richmond **
//
//		1.2.1	- Fixed bug where input text overlapped dropdown arrow in IE (i.e. when using oneOrMoreSelected = *)
//              - Added option "listHeight" for min-height of the dropdown
//              - Fixed bug where bgiframe was causing a horizontal scrollbar and on short lists extra whitespace below the options
//              - ** by Andy Richmond **
//
//		1.2.2	- Fixed bug where the keypress stopped showing the dropdown because in jQuery 1.3.2 they changed the way ':visible' works
//              - Fixed some other bugs in the way the keyboard interface worked
//              - Changed the main textbox to an <a> tag (with 'display: inline-block') to prevent the display text from being selected/highlighted
//              - Added the ability to jump to an option by typing the first character of that option (simular to a normal drop down)
//              - ** by Andy Richmond **
//				- Added [] to make each control submit an HTML array so $.serialize() works properly
//
// Licensing & Terms of Use
//
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC.
//
*/
if(jQuery) (function($){
    
    var ava_employee;
    // render the html for a single option
    function renderOption(id, option)
    {
		console.log( option);
        var html = '<label><input type="checkbox"  name="' + id + '[]" value="' + option.value + '" id="check-' + option.value + '"';
        if( option.selected ){
            html += ' checked="checked"';
        }
        var avatar = '';
        resource_id = option.value.split('-');
        if(resource_id[1] == 1){
            avatar = '<span class="circle-name" title ="'+ option.text +'"><i class="icon-user"></i></span>';
        }else{
            if(ava_employee[resource_id[0]]){
                avatar = '<span class="circle-name" title ="'+ option.text +'"><img src="'+ ava_employee[resource_id[0]] +'" /></span>';
            }else{
                name = option.text.split(' ');
                avatar = '<span class="circle-name" title ="'+ option.text +'">'+ name[0].substr(0,1) + name[1].substr(0,1) +'</span>';
            }
        }
        html += ' />' + avatar + option.text + '</label>';

        return html;
    }

    // render the html for the options/optgroups
    function renderOptions(id, options, o)
    {

        var html = "";
        html += '<div class="option-content">';
        for(var i = 0; i < options.length; i++) {
            if(options[i].optgroup) {
                html += '<label class="optGroup">';

                if(o.optGroupSelectable) {
                    html += '<input type="checkbox" class="optGroup" />' + options[i].optgroup;
                }
                else {
                    html += options[i].optgroup;
                }

                html += '</label><div class="optGroupContainer">';

                html += renderOptions(id, options[i].options, o);

                html += '</div>';
            }
            else {
                html += renderOption(id, options[i]);
            }
        }
		html += '</div>';
        return html;
    }

    // Building the actual options
    function buildOptions(options)
    {
        var multiSelect = $(this);
        var multiSelectOptions = multiSelect.data("multiSelectOptions").css({
            height:'',
            width:''
        });
        var o = multiSelect.data("config");
        var callback = multiSelect.data("callback");

        // clear the existing options
        multiSelectOptions.html("");
        var html = "";

        // if we should have a select all option then add it
        if( o.selectAll ) {
            html += '<label class="selectAll"><input type="checkbox" class="selectAll" />' + o.selectAllText + '</label>';
        }

        // generate the html for the new options
        html += renderOptions(multiSelect.attr('id'), options, o);

        multiSelectOptions.html(html);

        // variables needed to account for width changes due to a scrollbar
        var initialWidth = multiSelectOptions.width();
        var hasScrollbar = false;

        // set the height of the dropdown options
        if(multiSelectOptions.height() > o.listHeight) {
            multiSelectOptions.css("height", o.listHeight + 'px');
            hasScrollbar = true;
        } else {
            multiSelectOptions.css("height", '');
        }

        // if the there is a scrollbar and the browser did not already handle adjusting the width (i.e. Firefox) then we will need to manaually add the scrollbar width
        var scrollbarWidth = hasScrollbar && (initialWidth == multiSelectOptions.width()) ? 17 : 0;

        // set the width of the dropdown options
        if((multiSelectOptions.width() + scrollbarWidth) < multiSelect.outerWidth()) {
            multiSelectOptions.css("width", multiSelect.outerWidth() - 2/*border*/ + 'px');
        } else {
            multiSelectOptions.css("width", (multiSelectOptions.width() + scrollbarWidth) + 'px');
        }

        // Apply bgiframe if available on IE6
        if( $.fn.bgiframe ) multiSelect.data("multiSelectOptions").bgiframe( {
            width: multiSelectOptions.width(),
            height: multiSelectOptions.height()
        });

        // Handle selectAll oncheck
        if(o.selectAll) {
            multiSelectOptions.find('INPUT.selectAll').click( function() {
                // update all the child checkboxes
                multiSelectOptions.find('INPUT:checkbox').attr('checked', $(this).attr('checked')).parent("LABEL").toggleClass('checked', $(this).attr('checked'));
            });
        }

        // Handle OptGroup oncheck
        if(o.optGroupSelectable) {
            multiSelectOptions.addClass('optGroupHasCheckboxes');

            multiSelectOptions.find('INPUT.optGroup').click( function() {
                // update all the child checkboxes
                $(this).parent().next().find('INPUT:checkbox').attr('checked', $(this).attr('checked')).parent("LABEL").toggleClass('checked', $(this).attr('checked'));
            });
        }

        // Handle all checkboxes
        multiSelectOptions.find('INPUT:checkbox').click( function() {
            // set the label checked class
            $(this).parent("LABEL").toggleClass('checked', $(this).prop('checked') == true);

            updateSelected.call(multiSelect,'click');
            multiSelect.focus();
            if($(this).parent().parent().hasClass('optGroupContainer')) {
                updateOptGroup.call(multiSelect, $(this).parent().parent().prev());
            }
            if( callback ) {
                callback.call(multiSelect,$(this));
            }
        }).change(function(){
            updateSelected.call(multiSelect,'change');
            if($(this).parent().parent().hasClass('optGroupContainer')) {
                updateOptGroup.call(multiSelect, $(this).parent().parent().prev());
            }
            $(this).parent("LABEL").toggleClass('checked', $(this).prop('checked') == true);
            if( callback ) {
                callback.call(multiSelect,$(this));
            }
        });

        // Initial display
        multiSelectOptions.each( function() {
            $(this).find('INPUT:checked').parent().addClass('checked');
        });

        // Initialize selected and select all
        updateSelected.call(multiSelect);

        // Initialize optgroups
        if(o.optGroupSelectable) {
            multiSelectOptions.find('LABEL.optGroup').each( function() {
                updateOptGroup.call(multiSelect, $(this));
            });
        }

        // Handle hovers
        multiSelectOptions.find('LABEL:has(INPUT)').hover( function() {
            $(this).parent().find('LABEL').removeClass('hover');
            $(this).addClass('hover');
        }, function() {
            $(this).parent().find('LABEL').removeClass('hover');
        });

        // Keyboard
        multiSelect.keydown( function(e) {

            var multiSelectOptions = $(this).data("multiSelectOptions");

            // Is dropdown visible?
            if( multiSelectOptions.css('visibility') != 'hidden' ) {
                // Dropdown is visible
                // Tab
                if( e.keyCode == 9 ) {
                    $(this).addClass('focus').trigger('click'); // esc, left, right - hide
                    $(this).focus().next(':input').focus();
                    return true;
                }

                // ESC, Left, Right
                if( e.keyCode == 27 || e.keyCode == 37 || e.keyCode == 39 ) {
                    // Hide dropdown
                    $(this).addClass('focus').trigger('click');
                }
                // Down || Up
                if( e.keyCode == 40 || e.keyCode == 38) {
                    var allOptions = multiSelectOptions.find('LABEL');
                    var oldHoverIndex = allOptions.index(allOptions.filter('.hover'));
                    var newHoverIndex = -1;

                    // if there is no current highlighted item then highlight the first item
                    if(oldHoverIndex < 0) {
                        // Default to first item
                        multiSelectOptions.find('LABEL:first').addClass('hover');
                    }
                    // else if we are moving down and there is a next item then move
                    else if(e.keyCode == 40 && oldHoverIndex < allOptions.length - 1)
                    {
                        newHoverIndex = oldHoverIndex + 1;
                    }
                    // else if we are moving up and there is a prev item then move
                    else if(e.keyCode == 38 && oldHoverIndex > 0)
                    {
                        newHoverIndex = oldHoverIndex - 1;
                    }

                    if(newHoverIndex >= 0) {
                        $(allOptions.get(oldHoverIndex)).removeClass('hover'); // remove the current highlight
                        $(allOptions.get(newHoverIndex)).addClass('hover'); // add the new highlight

                        // Adjust the viewport if necessary
                        adjustViewPort(multiSelectOptions);
                    }

                    return false;
                }

                // Enter, Space
                if( e.keyCode == 13 || e.keyCode == 32 ) {
                    var selectedCheckbox = multiSelectOptions.find('LABEL.hover INPUT:checkbox');

                    // Set the checkbox (and label class)
                    selectedCheckbox.attr('checked', !selectedCheckbox.attr('checked')).parent("LABEL").toggleClass('checked', selectedCheckbox.attr('checked'));

                    // if the checkbox was the select all then set all the checkboxes
                    if(selectedCheckbox.hasClass("selectAll")) {
                        multiSelectOptions.find('INPUT:checkbox').attr('checked', selectedCheckbox.attr('checked')).parent("LABEL").addClass('checked').toggleClass('checked', selectedCheckbox.attr('checked'));
                    }

                    updateSelected.call(multiSelect,'keydown');

                    if( callback ) callback($(this));
                    return false;
                }

                // Any other standard keyboard character (try and match the first character of an option)
                if( e.keyCode >= 33 && e.keyCode <= 126 ) {
                    // find the next matching item after the current hovered item
                    var match = multiSelectOptions.find('LABEL:startsWith(' + String.fromCharCode(e.keyCode) + ')');

                    var currentHoverIndex = match.index(match.filter('LABEL.hover'));

                    // filter the set to any items after the current hovered item
                    var afterHoverMatch = match.filter(function (index) {
                        return index > currentHoverIndex;
                    });

                    // if there were no item after the current hovered item then try using the full search results (filtered to the first one)
                    match = (afterHoverMatch.length >= 1 ? afterHoverMatch : match).filter("LABEL:first");

                    if(match.length == 1) {
                        // if we found a match then move the hover
                        multiSelectOptions.find('LABEL.hover').removeClass('hover');
                        match.addClass('hover');

                        adjustViewPort(multiSelectOptions);
                    }
                }
            } else {
                // Dropdown is not visible
                if( e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13 || e.keyCode == 32 ) { //up, down, enter, space - show
                    // Show dropdown
                    $(this).removeClass('focus').trigger('click');
                    multiSelectOptions.find('LABEL:first').addClass('hover');
                    return false;
                }
                //  Tab key
                if( e.keyCode == 9 ) {
                    // Shift focus to next INPUT element on page
                    multiSelectOptions.next(':input').focus();
                    return true;
                }
            }
            // Prevent enter key from submitting form
            if( e.keyCode == 13 ) return false;
        });

        // add filter
        multiSelectOptions.prepend($('<div class="multiSelect-filter"><span><input type="text" rel="no-history" /></span></div>'));
        var timeoutID = null, searchHandler = function(){
            var val = $(this).val();
            multiSelectOptions.find('LABEL').each(function(){
                var $label = $(this);
                if(!val.length || $label.text().toLowerCase().indexOf(val.toLowerCase()) != -1){
                    $label.removeClass('notmatch');
                }else{
                    $label.addClass('notmatch');
                }
            });
            if(multiSelectOptions.height() > o.listHeight) {
                multiSelectOptions.css("height", o.listHeight + 'px');
                hasScrollbar = true;
            } else {
                multiSelectOptions.css("height", '');
            }
        };
        multiSelectOptions.find('.multiSelect-filter input').keyup(function(){
            var self = this;
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function(){
                searchHandler.call(self);
            } , 200);
        });

    }

    // Adjust the viewport if necessary
    function adjustViewPort(multiSelectOptions)
    {
        // check for and move down
        var selectionBottom = multiSelectOptions.find('LABEL.hover').position().top + multiSelectOptions.find('LABEL.hover').outerHeight();

        if(selectionBottom > multiSelectOptions.innerHeight()){
            multiSelectOptions.scrollTop(multiSelectOptions.scrollTop() + selectionBottom - multiSelectOptions.innerHeight());
        }

        // check for and move up
        if(multiSelectOptions.find('LABEL.hover').position().top < 0){
            multiSelectOptions.scrollTop(multiSelectOptions.scrollTop() + multiSelectOptions.find('LABEL.hover').position().top);
        }
    }

    // Update the optgroup checked status
    function updateOptGroup(optGroup)
    {
        var multiSelect = $(this);
        var o = multiSelect.data("config");

        // Determine if the optgroup should be checked
        if(o.optGroupSelectable) {
            var optGroupSelected = true;
            $(optGroup).next().find('INPUT:checkbox').each( function() {
                if( !$(this).attr('checked') ) {
                    optGroupSelected = false;
                    return false;
                }
            });

            $(optGroup).find('INPUT.optGroup').attr('checked', optGroupSelected).parent("LABEL").toggleClass('checked', optGroupSelected);
        }
    }

    // Update the textbox with the total number of selected items, and determine select all
    var stackTimeoutId = {};
    function updateSelected(eventKey) {
        var multiSelect = $(this);
        var multiSelectOptions = multiSelect.data("multiSelectOptions");
        var o = multiSelect.data("config");

        var i = 0;
        var selectAll = true;
        var display = '';
        multiSelectOptions.find('INPUT:checkbox').not('.selectAll, .optGroup').each( function() {
            if( $(this).attr('checked') ) {
                i++;
                // employee_name = $(this).parent().find('.circle-name').html();
                // console.log(employee_name);
                display += $(this).parent().find('.circle-name')[0].outerHTML;
            }
            else selectAll = false;
        });

        // trim any end comma and surounding whitespace
        display = display.replace(/\s*\,\s*$/,'');

        // console.log(display);

        if( i == 0 ) {
            multiSelect.html( o.noneSelected );
        } else {
            if( o.oneOrMoreSelected == '*' ) {
                multiSelect.html( display );
            } else {
                multiSelect.html( o.oneOrMoreSelected.replace('%', i) );
            }
        }

        // Determine if Select All should be checked
        if(o.selectAll) {
            multiSelectOptions.find('INPUT.selectAll').attr('checked', selectAll).parent("LABEL").toggleClass('checked', selectAll);
        }

        var id = multiSelect.attr('id');
        clearTimeout(stackTimeoutId[id]);
        stackTimeoutId[id] = setTimeout(function(){
            ajaxLoader(eventKey,multiSelect,multiSelectOptions,o);
        },200);
    }
    // Building the ajax loader
    function  ajaxLoader(eventKey,instance,list, option){
        if(!eventKey || !option.url){
            return;
        }
        $.ajax({
            cache : true,
            url:  option.url,
            data : {
                data : list.find('input:checked').map(function(){
                    return $(this).val();
                }).get()
            },
            beforeSend: function() {
                var $element = $(instance);
                if($(option.update).length){
                    $element = $(option.update);
                }
                if($element.data('config')){
                    buildOptions.call($element, []);
                }
                $element.html("<span>"+ (option.loadingText || 'Loading...') + "</span>").addClass(option.loadingClass || 'multiselect-loading');
            },
            success: function(result) { //MODIFY BY VINGUYEN 03/09/2014
                var $update = $(option.update);
				//ADD CODE BY VINGUYEN 03/09/2014
				if(option.update=='#ActivityActivityEmployee' || option.update=='#export-list-employees' )
				{
                    var $parent = option.parent ? $(option.parent) : $('#filter_profitCenter');
					if(result)
					{
						var $result=JSON.parse(result);
						var data=$result.html;
						var pc=$result.pc;
						var display = '';
						//$('#ActivityActivityProfitCenter').find('span').text('<?php __("-- Any --"); ?>');
						$.each(pc, function($index, $pc)
						{
							/*if($('#filter_profitCenter div label').find('input[id=check-'+$pc+']').is(':checked'))
							{
								$('#filter_profitCenter div label').find('input[id=check-'+$pc+']').parent().removeClass('checked');
								$('#filter_profitCenter div label').find('input[id=check-'+$pc+']').removeAttr('checked');
								//display += $('#filter_profitCenter div label').find('input[id=check-'+$pc+']').parent().text() + ', ';
							}
							else
							{
								$('#filter_profitCenter div label').find('input[id=check-'+$pc+']').parent().addClass('checked');
								$('#filter_profitCenter div label').find('input[id=check-'+$pc+']').attr('checked','checked');
								display += $('#filter_profitCenter div label').find('input[id=check-'+$pc+']').parent().text() + ', ';
							}*/
							$parent.find('div label').find('input[id=check-'+$pc+']').parent().addClass('checked');
							$parent.find('div label').find('input[id=check-'+$pc+']').attr('checked','checked');
							display += $parent.find('div label').find('input[id=check-'+$pc+']').parent().text() + ',';
						});
						$(instance).find('span').text('-- Any --');
						$(instance).find('span').text(display);
					}
					else
					{
						var data='';
					}
				}
				else if(option.update=='#export-list-employees123123123' )
				{
 					var data='';
					if(result)
					{
						var $result=JSON.parse(result);
						var data=$result.html;
					}
				}
				else
				{
					var data=result;
				}
				//END
                $('#filter_profitCenter').find('.multiSelectOptions input[type="checkbox"]').each(function(){
                    if(typeof $(this).attr('checked') == 'undefined'){
                        $('#ActivitySelectPCAll').val('false');
                        return false;
                    }
                    $('#ActivitySelectPCAll').val('true');
                });
                if($update.length && !$update.data('config')){
                    $update.removeClass(option.loadingClass || 'multiselect-loading');
                    $update.empty().append(data || '');
                    return;
                }

                var options = [];
                $('<select></select>').append(data || "").children().each( function() {
                    if(this.tagName.toUpperCase() == 'OPTGROUP')
                    {
                        var suboptions = [];
                        options.push({
                            optgroup: $(this).attr('label'),
                            options: suboptions
                        });

                        $(this).children('OPTION').each( function() {
                            if( $(this).val() != '' ) {
                                suboptions.push({
                                    text: $(this).html(),
                                    value: $(this).val()
                                });
                            }
                        });
                    }
                    else if(this.tagName.toUpperCase() == 'OPTION')
                    {
                        if( $(this).val() != '' ) {
                            options.push({
                                text: $(this).html(),
                                value: $(this).val()
                            });
                        }
                    }
                });
                // Build the dropdown options
                if($update.length){
                    $update.removeClass(option.loadingClass || 'multiselect-loading');
                    buildOptions.call($update, options);
                }else{
                    $(instance).removeClass(option.loadingClass || 'multiselect-loading');
                    buildOptions.call(instance, options);
                }
            }
        });
    }
    $.extend($.fn, {
        multiSelect: function(o, callback) {
            // Default options
            if( !o ) o = {};
            if( o.selectAll == undefined ) o.selectAll = true;
            if( o.selectAllText == undefined ) o.selectAllText = "Select All";
            if( o.noneSelected == undefined ) o.noneSelected = 'Select options';
            if( o.oneOrMoreSelected == undefined ) o.oneOrMoreSelected = '% selected';
            if( o.optGroupSelectable == undefined ) o.optGroupSelectable = false;
            if( o.listHeight == undefined ) o.listHeight = 150;
            if( o.appendTo == undefined ) o.appendTo = null;

            // Initialize each multiSelect
            $(this).each( function() {
                var select = $(this);

                var multiSelect = $('<a href="javascript:;" class="multiSelect"><span></span></a>');
                var multiSelectOptions = $('<div class="multiSelectOptions" style="position: absolute; z-index: 99999; visibility: hidden;"></div>');
                // if the select object had a width defined then match the new multilsect to it
                multiSelect.find("span").css("width", $(select).width() + 'px');
                $(select).after(multiSelect);

                if(o.appendTo){
                    $(multiSelectOptions).appendTo(o.appendTo);
                }else{
                    multiSelect.after(multiSelectOptions);
                }

                // Attach the config options to the multiselect
                multiSelect.data("config", o);
                multiSelect.data("multiSelectOptions", multiSelectOptions);

                // Attach the callback to the multiselect
                multiSelect.data("callback", callback);

                // Serialize the select options into json options
                var options = [];
                $(select).children().each( function() {
                    if(this.tagName.toUpperCase() == 'OPTGROUP')
                    {
                        var suboptions = [];
                        options.push({
                            optgroup: $(this).attr('label'),
                            options: suboptions
                        });

                        $(this).children('OPTION').each( function() {
                            if( $(this).val() != '' ) {
                                console.log($(this).html());
                                suboptions.push({
                                    text: $(this).html(),
                                    value: $(this).val(),
                                    selected: $(this).attr('selected')
                                });
                            }
                        });
                    }
                    else if(this.tagName.toUpperCase() == 'OPTION')
                    {
                        if( $(this).val() != '' ) {
                            options.push({
                                text: $(this).html(),
                                value: $(this).val(),
                                selected: $(this).attr('selected')
                            });
                        }
                    }
                });

                // Eliminate the original form element
                $(select).remove();

                // Add the id that was on the original select element to the new input
                multiSelect.attr("id", $(select).attr("id"));

                // Build the dropdown options
                buildOptions.call(multiSelect, options);

                // Events
                multiSelect.hover( function() {
                    $(this).addClass('hover');
                }, function() {
                    $(this).removeClass('hover');
                }).click( function() {
                    // Show/hide on click
                    if( $(this).hasClass('active') ) {
                        $(this).multiSelectOptionsHide();
                    } else {
                        $(this).multiSelectOptionsShow();
                    }
                    return false;
                }).focus( function() {
                    // So it can be styled with CSS
                    $(this).addClass('focus');
                }).blur( function() {
                    // So it can be styled with CSS
                    $(this).removeClass('focus');
                });

                // Add an event listener to the window to close the multiselect if the user clicks off
                $(document).click( function(event) {
                    // If somewhere outside of the multiselect was clicked then hide the multiselect
                    if(!($(event.target).parents().andSelf().is('.multiSelectOptions'))){
                        multiSelect.multiSelectOptionsHide();
                    }
                });
            });
        },
        // Update the dropdown options
        multiSelectDestroy: function(options) {
            var multiSelect = $(this);
            $(multiSelect.data("multiSelectOptions")).remove();
            multiSelect.remove();
        },
        // Update the dropdown options
        multiSelectOptionsUpdate: function(options) {
            var opts = [];
            $.each(options,function(k,v){
                opts.push({
                    value : k,
                    text : v
                });
            });
            buildOptions.call($(this), opts);
        },

        // Hide the dropdown
        multiSelectOptionsHide: function() {
            $($(this).removeClass('active').removeClass('hover').data("multiSelectOptions")).css({
                'visibility' : 'hidden',
                'top' : -9999
            });
        },

        // Show the dropdown
        multiSelectOptionsShow: function() {
            var multiSelect = $(this);
            var multiSelectOptions = multiSelect.data("multiSelectOptions");
            var o = multiSelect.data("config");

            // Hide any open option boxes
            $('.multiSelect').multiSelectOptionsHide();
            multiSelectOptions.find('LABEL').removeClass('hover');
            multiSelect.addClass('active').data("multiSelectOptions").css('visibility', 'visible');
            multiSelect.focus();

            // reset the scroll to the top
            multiSelect.data("multiSelectOptions").scrollTop(0);

            // Position it
            var offset = o.appendTo ? multiSelect.offset() :  multiSelect.position();
            var css = {
                top:  offset.top + $(this).outerHeight() + 'px',
                left: offset.left + 'px'
            };
            if(o.appendTo){
                //css.width = multiSelect.width();
            }
            multiSelect.data("multiSelectOptions").css(css);
        },

        // get a coma-delimited list of selected values
        selectedValuesString: function() {
            var selectedValues = "";
            $(this).data("multiSelectOptions").find('INPUT:checkbox:checked').not('.optGroup, .selectAll').each(function() {
                selectedValues += $(this).attr('value') + ",";
            });
            // trim any end comma and surounding whitespace
            return selectedValues.replace(/\s*\,\s*$/,'');
        }
    });

    // add a new ":startsWith" search filter
    $.expr[":"].startsWith = function(el, i, m) {
        var search = m[3];
        if (!search) return false;
        return eval("/^[/s]*" + search + "/i").test($(el).text());
    };

})(jQuery);
