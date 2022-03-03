// JavaScript Document
var obj = document.getElementById('btnCont');
var l=obj.offsetLeft;
jQuery('#boxMultiSort').css({"left" : (l+30)+"px", top: (l.offsetTop+32) + 'px'});

function showHideIt()
{
	jQuery('#boxMultiSort').animate({height: 'toggle'},100, function(){});
}
$('#optionField').change(function(){
	jQuery('#onSort').hide();
	var flag;
	if(jQuery('#strMultiSort').val()==''||jQuery('#strMultiSort').val()=='[]')
	{
		flag=0;
		//jQuery('#onSort').toggle();
	}
	else
	{
		flag=1;
	}
	var value=(this.value);
	if(value=='-1') return false;
	var className=$("option:selected", this).attr("title");
	if($('li').hasClass(className))
	{
		return false;
	}
	var text=$("option:selected", this).attr("rel");
	var typeSorts=jQuery('#typeSort').html();
	var html='<li rel="'+value+'" id="'+className+'" class="columns '+className+'"><strong>'+text+'</strong><select onchange=\'updateChoise(this.value,"'+className+'")\'>'+typeSorts+'</select><span onclick=\'removeChoise("'+className+'");\'></span></li>';
	jQuery('#fieldsChoise').append(html);
	var data='multiSort=1&actSort=add&value='+value+'&flag='+flag;
	jQuery.ajax({
    type: "POST",
    data: data,
    cache: false,
    success: function (html) {
		jQuery('#strMultiSort').html(html);
		jQuery('#onSort').css('display', 'inline-block');
		}		
    });
});
function removeChoise(className)
{
	jQuery('#onSort').hide();
	var value=jQuery('#'+className).attr("rel");
	jQuery('#'+className).remove();
	
	var data='multiSort=1&actSort=remove&value='+value;
	jQuery.ajax({
    type: "POST",
    data: data,
    cache: false,
    success: function (html) {
			jQuery('#strMultiSort').html(html);
			if(html!=''&&html!='[]')
			{
				jQuery('#onSort').css('display', 'inline-block');
			}
		}
    });
}
function updateChoise(type,className)
{	
	jQuery('#onSort').hide();
	var value=jQuery('#'+className).attr("rel");
	var data='multiSort=1&actSort=update&value='+value+'&type='+type;
	jQuery.ajax({
    type: "POST",
    data: data,
    cache: false,
    success: function (html) {
		jQuery('#strMultiSort').html(html);
		jQuery('#onSort').css('display', 'inline-block');
		}		
    });	
}