/*
var $liv_icon = [
	'Access.svg',
	'Android.svg',
	'Apple.svg',
	'Behance.svg',
	'dropbox.svg',
	'Excel.svg',
	'facebook.svg',
	'Instagram.svg',
	'Linkedin.svg',
	'Messenger.svg',
	'microsoft-teams.svg',
	'Microsoft.svg',
	'onedrive.svg',
	'onenote.svg',
	'Pinterest.svg',
	'Powerpoint.svg',
	'Publisher.svg',
	'skype.svg',
	'slack.svg',
	'trello.svg',
	'twitter.svg',
	'Word.svg',
	'Youtube.svg',
	'z0g.svg'
];
var $list_liv_icon = {};
$.each( $liv_icon, function( i, val){
	var imgURL = '/img/new-icon/project_document/'+ val;
	// console.log( val);
	jQuery.get(imgURL, function(data) {
		var $svg = jQuery(data).find('svg');

		// Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');

        // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
        if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
        }
		// $list_liv_icon[i] = $svg;
		$list_liv_icon[val] = $svg;

	}, 'xml');
});
*/ // Sau khi lấy dc icon thì update lại vào code ( IE, Firefox) 

var liv_icons = {
	'Android.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Android" class="cls-1" d="M3919.75,147.223a2.29,2.29,0,0,1-2.25-2.323v-9.937a2.251,2.251,0,1,1,4.5,0V144.9A2.29,2.29,0,0,1,3919.75,147.223Zm-6.13,4.9h-1.71a2.278,2.278,0,0,1,.09.644v4.9a2.251,2.251,0,1,1-4.5,0v-4.9a2.278,2.278,0,0,1,.09-0.644h-4.18a2.278,2.278,0,0,1,.09.644v4.9a2.251,2.251,0,1,1-4.5,0v-4.9a2.278,2.278,0,0,1,.09-0.644h-1.71a2.422,2.422,0,0,1-2.38-2.454V132.64h21v17.034A2.422,2.422,0,0,1,3913.62,152.128Zm-13.28-28.786-0.43-.654-0.41-.645-0.94-1.449a0.393,0.393,0,0,1,.11-0.536,0.369,0.369,0,0,1,.52.107l1,1.553,0.42,0.653,0.43,0.662a12.166,12.166,0,0,1,8.92,0l0.43-.662,0.42-.653,1-1.553a0.368,0.368,0,0,1,.52-0.107,0.393,0.393,0,0,1,.11.536l-0.94,1.449-0.41.645-0.43.654a8.785,8.785,0,0,1,5.34,7.749h-21A8.785,8.785,0,0,1,3900.34,123.342Zm9.66,4.652a1.162,1.162,0,1,0-1.12-1.162A1.141,1.141,0,0,0,3910,127.994Zm-9,0a1.162,1.162,0,1,0-1.12-1.162A1.149,1.149,0,0,0,3901,127.994Zm-9.75,19.229A2.29,2.29,0,0,1,3889,144.9v-9.937a2.251,2.251,0,1,1,4.5,0V144.9A2.29,2.29,0,0,1,3891.25,147.223Z" transform="translate(-3885.5 -120)"/> ',  
	'Apple.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Apple" class="cls-1" d="M3837.23,141.25c0.05,6.051,5.7,8.063,5.76,8.091a21.378,21.378,0,0,1-2.97,5.69c-1.8,2.438-3.65,4.863-6.58,4.912-2.88.051-3.8-1.588-7.09-1.588s-4.32,1.539-7.04,1.639c-2.83.1-4.98-2.631-6.79-5.06-3.69-4.967-6.51-14.037-2.72-20.157a10.65,10.65,0,0,1,8.89-5.017c2.77-.046,5.4,1.742,7.09,1.742s4.88-2.15,8.23-1.834a10.32,10.32,0,0,1,7.86,3.962C3841.66,133.753,3837.17,136.182,3837.23,141.25Zm-11.79-12.038a8.064,8.064,0,0,1,2.28-6.19,9.883,9.883,0,0,1,6.33-3.029,8.447,8.447,0,0,1-2.23,6.388A8.237,8.237,0,0,1,3825.44,129.212Z" transform="translate(-3805.5 -120)"/> ' ,
	'Access.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Access" class="cls-1" d="M3285.99,128.8c0.15-1.934-1.58-3.2-3.16-3.753a28.158,28.158,0,0,0-13.2-1V120h-2.72c-6.96,1.27-13.94,2.484-20.91,3.73v32.549c6.93,1.247,13.87,2.428,20.8,3.721h2.83v-5.009a28.261,28.261,0,0,0,13.14-.968c1.6-.548,3.38-1.8,3.22-3.763Q3286.005,139.531,3285.99,128.8Zm-26.2,18.6c-0.28-1.165-.57-2.326-0.88-3.484-1.39-.008-2.78-0.048-4.18-0.121-0.27,1.1-.54,2.2-0.82,3.3-0.88-.07-1.77-0.135-2.66-0.2,1.33-4.9,2.71-9.776,4.03-14.674,1.06-.079,2.11-0.154,3.18-0.237,1.52,5.209,2.96,10.446,4.47,15.66C3261.88,147.581,3260.83,147.5,3259.79,147.405Zm9.84-21.935a27.972,27.972,0,0,1,12.23.679c1.09,0.414,2.54.911,2.79,2.237-0.18,1.084-1.29,1.572-2.16,1.963a26.908,26.908,0,0,1-12.86.9V125.47Zm0,14.7a35.444,35.444,0,0,0,9.34-.023h0a11.3,11.3,0,0,0,5.67-2.069c-0.09,2,.15,4.009-0.12,5.99a6.118,6.118,0,0,1-3.25,1.689,31.914,31.914,0,0,1-11.64.446v-6.033Zm14.88,11.35c-1.29,1.432-3.33,1.7-5.1,2.065a32.653,32.653,0,0,1-9.8.037c0.03-2,.02-4,0.02-6.009a35.378,35.378,0,0,0,9.32-.024h0a11.32,11.32,0,0,0,5.69-2.069C3284.55,147.512,3284.79,149.535,3284.51,151.517Zm0-14.88a6.241,6.241,0,0,1-3.27,1.684,31.844,31.844,0,0,1-11.61.432v-6.027a35.69,35.69,0,0,0,9.31-.019,11.361,11.361,0,0,0,5.7-2.074A60.036,60.036,0,0,1,3284.51,136.637Zm-29.21,4.652c1,0.008,2,.013,3.01.018-0.52-2.177-1.15-4.325-1.56-6.53C3256.44,136.986,3255.76,139.111,3255.3,141.289Z" transform="translate(-3246 -120)"/> ' ,
	'Behance.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Behance" class="cls-1" d="M3672.24,130.428h9.96v2.485h-9.96v-2.485Zm13.73,14.716h-12.96c0.14,3.073,1.67,4.2,4.41,4.2,1.99,0,3.59-.618,3.9-1.712h3.95c-1.39,4.165-3.95,5.363-8.02,5.363-5.66,0-9.17-3.822-9.17-9.281a8.925,8.925,0,0,1,9.17-9.319C3683.37,134.4,3686.32,139.718,3685.97,145.144Zm-12.96-3.608h8.03c-0.45-2.456-1.53-3.483-3.93-3.483C3673.98,138.053,3673.08,140.173,3673.01,141.536Zm-6.23,3.919c0,4.95-4.28,7.068-8.76,7.068H3646V128h11.68c4.72,0,7.92,1.655,7.92,6.193a5.352,5.352,0,0,1-3.37,5.088A5.961,5.961,0,0,1,3666.78,145.455Zm-10.68-13.33h-5.11v5.7h5.52c1.91,0,3.34-.853,3.34-2.9C3659.85,132.6,3658.04,132.125,3656.1,132.125Zm0.83,9.35h-5.94v6.69h5.83c2.16,0,4.03-.681,4.03-3.208C3660.85,142.466,3659.26,141.475,3656.93,141.475Z" transform="translate(-3646 -120.5)"/> ' ,
	'dropbox.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="dropbox" class="cls-1" d="M2556,128.833l8.23-6.833,11.77,7.637-8.14,6.482Zm20,13.765-11.77,7.64-8.23-6.832,11.86-7.287Zm-28.23,7.64L2536,142.6l8.14-6.479,11.86,7.287Zm-11.77-20.6L2547.77,122l8.23,6.833-11.86,7.286Zm20.02,15.238,8.26,6.815,3.54-2.3v2.575L2556.02,159l-11.79-7.032v-2.575l3.54,2.3Z" transform="translate(-2536 -120.5)"/> ' ,
	'Excel.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Excel" class="cls-1" d="M3121.42,133.023h-6.36v3.256h6.36v-3.256Zm0-5.116h-6.36v3.256h6.36v-3.256Zm4.55,22.814q-0.015-12.1.01-24.214a4.327,4.327,0,0,0-.45-2.354,4.1,4.1,0,0,0-2.28-.446c-4.54.023-9.09,0.014-13.64,0.014V120h-2.7c-6.96,1.261-13.94,2.484-20.91,3.726v32.553c6.93,1.242,13.87,2.432,20.79,3.721h2.82v-4.186c4.71-.009,9.42.014,14.13,0,0.76-.033,1.89-0.056,2.07-1.023A21.277,21.277,0,0,0,3125.97,150.721Zm-26.59-3.442a55.308,55.308,0,0,1-2.27-5.842c-0.62,1.888-1.5,3.67-2.21,5.521-0.99-.014-1.99-0.056-2.98-0.1,1.17-2.339,2.29-4.7,3.5-7.023-1.03-2.4-2.15-4.744-3.2-7.126q1.5-.09,3-0.176c0.67,1.818,1.41,3.613,1.97,5.479,0.6-1.977,1.5-3.837,2.26-5.749,1.03-.074,2.06-0.14,3.09-0.191q-1.815,3.82-3.66,7.623c1.25,2.6,2.52,5.191,3.78,7.8C3101.56,147.429,3100.47,147.357,3099.38,147.279Zm25.22,7.14h-14.99v-2.791h3.64v-3.256h-3.64v-1.86h3.64v-3.256h-3.64V141.4h3.64V138.14h-3.64v-1.861h3.64v-3.256h-3.64v-1.86h3.64v-3.256h-3.64v-2.791h14.99v29.3Zm-3.18-6.047h-6.36v3.256h6.36v-3.256Zm0-5.116h-6.36v3.256h6.36v-3.256Zm0-5.116h-6.36V141.4h6.36V138.14Z" transform="translate(-3086 -120)"/> ' ,
	'Linkedin.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Linkedin" class="cls-1" d="M3997.09,159V145.175c0-2.834-.82-5.673-4.17-5.673s-4.74,2.839-4.74,5.742V159h-8.94V133.686h8.94v3.408c2.35-2.978,4.39-4.206,8.09-4.206s9.73,1.724,9.73,11.721V159h-8.91Zm-26.1-29.067a4.494,4.494,0,1,1,4.99-4.464A4.746,4.746,0,0,1,3970.99,129.933ZM3975.45,159h-8.95V133.686h8.95V159Z" transform="translate(-3966 -120)"/> ' ,
	'facebook.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="facebook" class="cls-1" d="M3435.96,120.007v6.436s-4.81-.473-6.01,1.346c-0.66.994-.27,3.9-0.33,5.993H3436c-0.54,2.443-.92,4.1-1.32,6.214h-5.09v20h-8.83V140.075H3417v-6.293h3.72c0.19-4.6.26-9.159,2.57-11.48C3425.9,119.693,3428.38,120.007,3435.96,120.007Z" transform="translate(-3406.5 -120)"/> ' ,
	'Instagram.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Instagram" class="cls-1" d="M4162.47,156.474a11.939,11.939,0,0,1-8.51,3.526h-15.92A12.045,12.045,0,0,1,4126,147.961V132.039A12.045,12.045,0,0,1,4138.04,120h15.92A12.045,12.045,0,0,1,4166,132.039v15.922A11.939,11.939,0,0,1,4162.47,156.474Zm0.23-24.435a8.749,8.749,0,0,0-8.74-8.738h-15.92a8.749,8.749,0,0,0-8.74,8.738v15.922a8.749,8.749,0,0,0,8.74,8.738h15.92a8.749,8.749,0,0,0,8.74-8.738V132.039Zm-6.18-.595a1.96,1.96,0,1,1,1.96-1.96A1.955,1.955,0,0,1,4156.52,131.444ZM4146,150.206A10.206,10.206,0,1,1,4156.21,140,10.219,10.219,0,0,1,4146,150.206Zm0-17.111a6.905,6.905,0,1,0,6.91,6.905A6.916,6.916,0,0,0,4146,133.1Z" transform="translate(-4126 -120)"/> ',
	'Microsoft.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Microsoft" class="cls-1" d="M4286,154.207l16.14,2.337V141.1H4286v13.1Zm0-15.31h16.14V123.455L4286,125.793v13.1Zm18.34,17.966L4326,160V141.1h-21.66v15.76Zm0-33.728V138.9H4326V120Z" transform="translate(-4286 -120)"/> ' ,
	'Messenger.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Messenger" class="cls-1" d="M4240.17,125.811a19.876,19.876,0,0,0-31.34,23.917L4206,160l10.56-2.757a19.979,19.979,0,0,0,9.52,2.413h0A19.9,19.9,0,0,0,4246,139.834,19.667,19.667,0,0,0,4240.17,125.811Zm-14.09,30.5h0a16.6,16.6,0,0,1-8.43-2.3l-0.6-.357-6.27,1.635,1.68-6.079-0.4-.623A16.571,16.571,0,1,1,4226.08,156.308Zm9.08-12.339c-0.49-.248-2.94-1.446-3.4-1.611a0.781,0.781,0,0,0-1.12.248c-0.33.5-1.28,1.611-1.57,1.942a0.766,0.766,0,0,1-1.08.124,13.573,13.573,0,0,1-4-2.458,14.966,14.966,0,0,1-2.77-3.431,0.72,0.72,0,0,1,.22-1.011c0.22-.222.5-0.579,0.75-0.868a3.444,3.444,0,0,0,.49-0.826,0.884,0.884,0,0,0-.04-0.868c-0.12-.248-1.12-2.686-1.53-3.677a1,1,0,0,0-1.12-.851c-0.29-.014-0.62-0.017-0.96-0.017a1.812,1.812,0,0,0-1.32.62,5.53,5.53,0,0,0-1.74,4.132,9.606,9.606,0,0,0,2.03,5.123c0.25,0.331,3.51,5.333,8.5,7.478a28.058,28.058,0,0,0,2.83,1.044,6.872,6.872,0,0,0,3.14.2c0.95-.142,2.94-1.2,3.36-2.355a4.208,4.208,0,0,0,.29-2.355A2.581,2.581,0,0,0,4235.16,143.969Z" transform="translate(-4206 -120)"/> ' ,
	'microsoft-teams.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="microsoft_teams" data-name="microsoft teams" class="cls-1" d="M2926,155.909L2949.37,160V120L2926,124.091v31.818Zm6.29-22.972,10.79-.664V135l-4.05.182v11.933l-2.69-.16V135.288l-4.05.165v-2.516Zm20.69,1.126a3.423,3.423,0,0,0,1.39-.283,3.639,3.639,0,0,0,0-6.68,3.408,3.408,0,0,0-1.39-.284,3.328,3.328,0,0,0-1.38.284c-0.33.141-.43,0.324-0.88,0.545v5.589c0.45,0.223.55,0.405,0.88,0.546A3.344,3.344,0,0,0,2952.98,134.063ZM2966,145.786v-7.149h-7.19v10.908h1.8a10.147,10.147,0,0,0,1.84-.177,5.712,5.712,0,0,0,1.74-.615,4.033,4.033,0,0,0,1.3-1.158A3.121,3.121,0,0,0,2966,145.786Zm-6.14-10.51a3.465,3.465,0,0,0,1.14.778,3.549,3.549,0,0,0,2.78,0,3.64,3.64,0,0,0,0-6.68,3.558,3.558,0,0,0-2.78,0A3.641,3.641,0,0,0,2959.86,135.276Zm-2.4,1.088h-6.74V152.43h0c0.45,0.1.61,0.175,0.93,0.219a7.571,7.571,0,0,0,1.05.078,7.922,7.922,0,0,0,1.63-.177,4.71,4.71,0,0,0,1.53-.606,3.53,3.53,0,0,0,1.15-1.15,3.361,3.361,0,0,0,.45-1.808V136.364Z" transform="translate(-2926 -120)"/> ' ,
	'onedrive.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="onedrive" class="cls-1" d="M2698.97,150.859c-2.42-.588-3.77-2.458-3.78-5.233a4.379,4.379,0,0,1,.3-1.883,5.167,5.167,0,0,1,3.98-2.842c0.97-.193,1.27-0.4,1.27-0.881a4.468,4.468,0,0,1,.25-1,7,7,0,0,1,3.09-3.931,6.631,6.631,0,0,1,3.6-.782,5.95,5.95,0,0,1,5,2.113l0.87,0.891,0.78-.263c3.79-1.274,7.57.894,7.87,4.518l0.08,0.991,0.75,0.26a4.089,4.089,0,0,1,2.95,4.588,3.9,3.9,0,0,1-1.98,3.286l-0.54.279-11.92.022c-9.16.018-12.07-.014-12.57-0.136h0Zm-8.94-1.6a5.9,5.9,0,0,1-3.61-2.926,3.609,3.609,0,0,1-.42-2.295,3.829,3.829,0,0,1,.35-2.194,5.251,5.251,0,0,1,3.56-2.781,1.8,1.8,0,0,0,.71-0.294,3.786,3.786,0,0,0,.12-0.956,6.553,6.553,0,0,1,4.6-6,7.543,7.543,0,0,1,4.72.278c0.49,0.189.43,0.23,1.46-1.083a9.375,9.375,0,0,1,2.85-2.234,7.81,7.81,0,0,1,3.57-.772,8.616,8.616,0,0,1,8.24,5.814c0.39,1.121.37,1.435-.09,1.444a8.506,8.506,0,0,0-1.26.235l-0.9.229-0.82-.8a8.311,8.311,0,0,0-9.32-1.2,7.637,7.637,0,0,0-3.1,2.651,9.406,9.406,0,0,0-1.26,2.734c0,0.27-.23.405-1.19,0.71-2.97.948-4.71,3.136-4.7,5.936a7.332,7.332,0,0,0,.64,2.98,1.038,1.038,0,0,1,.18.569,27.579,27.579,0,0,1-4.33-.045h0Z" transform="translate(-2686 -128)"/> ' ,
	'Pinterest.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Pinterest" class="cls-1" d="M3583.48,145.976c-0.32,1.3-.65,2.661-0.98,3.991a30.857,30.857,0,0,1-1.1,3.85,19.572,19.572,0,0,1-3.13,5.49c-0.88,1.039-.69.623-0.96,0.568-0.2-.043-0.22-0.244-0.26-0.537a30.222,30.222,0,0,1,.02-5.206,28.5,28.5,0,0,1,.9-5.411c0.74-3.216,1.45-6.37,2.19-9.608a2.08,2.08,0,0,0,.14-0.694,10.254,10.254,0,0,1-.66-2.65,8.6,8.6,0,0,1,.22-3.029,4.981,4.981,0,0,1,2.9-3.645,3.263,3.263,0,0,1,2.65.206,2.906,2.906,0,0,1,1.33,1.94,7.084,7.084,0,0,1-.12,2.95c-0.41,1.9-.95,3.325-1.43,5.159a5.631,5.631,0,0,0-.29,2.824,3.243,3.243,0,0,0,1.29,1.988,3.745,3.745,0,0,0,2.49.71,5.418,5.418,0,0,0,4.06-2.446,14.665,14.665,0,0,0,2.57-7.3,18.925,18.925,0,0,0,.1-3.14,8.654,8.654,0,0,0-1.72-4.859,8.113,8.113,0,0,0-3.85-2.666,12.565,12.565,0,0,0-5.74-.363,10.159,10.159,0,0,0-7.52,5.506,11.11,11.11,0,0,0-1.29,5.317,7,7,0,0,0,1.04,3.9,10.665,10.665,0,0,1,.7.931,2.693,2.693,0,0,1-.18,1.815c-0.17.663-.18,1.578-0.94,1.593a2.281,2.281,0,0,1-.98-0.379,7.986,7.986,0,0,1-3.71-5.821,14.268,14.268,0,0,1,0-5.048,14.551,14.551,0,0,1,1.43-4.023,14.338,14.338,0,0,1,5.3-5.554,15.876,15.876,0,0,1,5.98-2.1c0.78-.115,1.59-0.163,2.46-0.2a14.532,14.532,0,0,1,6.63,1.262,13.651,13.651,0,0,1,4.56,3.345,12.727,12.727,0,0,1,2.81,5.159,12.56,12.56,0,0,1,.61,3.565,22.136,22.136,0,0,1-.47,3.691c-0.73,4.409-2.79,8.014-5.88,10.066a10.089,10.089,0,0,1-2.69,1.262,9.6,9.6,0,0,1-3.43.441,6.952,6.952,0,0,1-3.04-.9A5.023,5.023,0,0,1,3583.48,145.976Z" transform="translate(-3566 -120.031)"/> ' ,
	'onenote.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="onenote" class="cls-1" d="M2612.3,127.747h3.7a0.384,0.384,0,0,0,.38-0.388s-0.04-3.219-.04-4.113v-0.011a4.226,4.226,0,0,1,.42-1.913l0.12-.241a0.1,0.1,0,0,0-.04.024l-7.19,7.233a0.05,0.05,0,0,0-.03.043c0.15-.075.35-0.177,0.38-0.19a5.5,5.5,0,0,1,2.3-.444h0Zm28.8-.787a3.31,3.31,0,0,0-2.08-2.693,27.059,27.059,0,0,0-5.09-.959,39.03,39.03,0,0,0-5.39-.164,3.251,3.251,0,0,0-1.79-2.47,13.642,13.642,0,0,0-6.71-.449,3.585,3.585,0,0,0-1.96,1.267,3.013,3.013,0,0,0-.5,1.752c0,0.436.02,1.463,0.03,2.376s0.02,1.734.02,1.739a1.467,1.467,0,0,1-1.46,1.479h-3.7a4.382,4.382,0,0,0-1.85.347,2.556,2.556,0,0,0-1.04.838,3.807,3.807,0,0,0-.58,2.352s0.01,0.692.17,2.034c0.14,1.037,1.25,8.285,2.3,10.489a2.675,2.675,0,0,0,1.49,1.593,39.324,39.324,0,0,0,7.78,1.889c1.92,0.248,3.12.769,3.83-.751,0,0,.14-0.378.34-0.929a15.246,15.246,0,0,0,.7-4.823,0.09,0.09,0,1,1,.18,0c0,0.864-.16,3.924,2.12,4.744a27.024,27.024,0,0,0,4.66.838c1.72,0.2,2.96.884,2.96,5.346,0,2.714-.56,3.086-3.5,3.086-2.38,0-3.29.062-3.29-1.856,0-1.552,1.51-1.389,2.64-1.389,0.5,0,.13-0.378.13-1.335s0.59-1.5.03-1.517c-3.87-.109-6.15-0.005-6.15,4.913,0,4.464,1.68,5.293,7.18,5.293,4.32,0,5.84-.143,7.62-5.749a52.254,52.254,0,0,0,1.72-10.154,112.864,112.864,0,0,0-.81-17.137h0Zm-7.52,11.96a7.781,7.781,0,0,0-1.52.094c0.13-1.1.58-2.455,2.17-2.4,1.76,0.061,2,1.747,2.01,2.889a7.154,7.154,0,0,0-2.66-.585h0Z" transform="translate(-2605.5 -120)"/> ',
	'Powerpoint.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Powerpoint" class="cls-1" d="M3198.71,134.87a6.45,6.45,0,0,0-5.96-6.093q-0.015,3.048-.01,6.107C3194.73,134.884,3196.72,134.893,3198.71,134.87Zm7.15-8.861a1.587,1.587,0,0,0-1.75-1.8c-4.84-.074-9.69,0-14.54-0.023V120H3187c-7.01,1.223-14,2.489-21,3.73v32.549c6.92,1.247,13.84,2.428,20.75,3.721h2.82v-4.651c4.54,0,9.07-.01,13.61.014a3.979,3.979,0,0,0,2.29-.466,5.6,5.6,0,0,0,.44-2.813C3205.85,143.39,3205.94,134.7,3205.86,126.009Zm-26.56,15.536a6.545,6.545,0,0,1-3.33.427c0,1.819-.01,3.638,0,5.456q-1.35-.12-2.7-0.232c-0.04-4.968-.05-9.94,0-14.908h0c2.47,0.121,5.45-1,7.49.894C3182.71,135.614,3182.2,140.088,3179.3,141.545Zm25.23,12.409h-14.96v-3.721h10.88v-1.861h-10.88v-2.325h10.88v-1.861h-10.88c0-.911,0-1.823-0.01-2.735a6.233,6.233,0,0,0,5.45-.549,6.388,6.388,0,0,0,2.79-5.079c-1.99-.014-3.98-0.009-5.96-0.009-0.01-2.024.02-4.047-.04-6.065-0.75.148-1.49,0.307-2.23,0.469v-4.637h14.96v28.373Zm-28.56-19.149h0c-0.02,1.562-.03,3.121.07,4.679,0.9-.112,2.07-0.019,2.59-0.977a3.444,3.444,0,0,0-.06-3.051C3177.97,134.6,3176.86,134.763,3175.97,134.805Z" transform="translate(-3166 -120)"/> ',
	'skype.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="skype" class="cls-1" d="M4073.71,147.005a6.823,6.823,0,0,1-3.04,2.217,12.6,12.6,0,0,1-4.67.79,11.794,11.794,0,0,1-5.35-1.081,6.942,6.942,0,0,1-2.46-2.1,4.5,4.5,0,0,1-.95-2.6,1.783,1.783,0,0,1,.62-1.357,2.256,2.256,0,0,1,1.56-.559,2.048,2.048,0,0,1,1.32.44,3.24,3.24,0,0,1,.87,1.233,7.605,7.605,0,0,0,.86,1.464,3.233,3.233,0,0,0,1.25.933,5.339,5.339,0,0,0,2.17.373,5.4,5.4,0,0,0,3.01-.755,2.089,2.089,0,0,0,1.11-1.795,1.734,1.734,0,0,0-.58-1.364,4.18,4.18,0,0,0-1.59-.847c-0.68-.2-1.6-0.424-2.74-0.654a22.719,22.719,0,0,1-3.92-1.127,6.531,6.531,0,0,1-2.57-1.809,4.383,4.383,0,0,1-.96-2.883,4.569,4.569,0,0,1,1.01-2.909,6.335,6.335,0,0,1,2.9-1.938,13.151,13.151,0,0,1,4.39-.666,12.263,12.263,0,0,1,3.48.442,7.737,7.737,0,0,1,2.47,1.184,5.2,5.2,0,0,1,1.45,1.572,3.362,3.362,0,0,1,.47,1.64,1.951,1.951,0,0,1-.61,1.389,2.105,2.105,0,0,1-1.54.617,1.975,1.975,0,0,1-1.29-.386,4.639,4.639,0,0,1-.89-1.143,4.739,4.739,0,0,0-1.34-1.6,4.319,4.319,0,0,0-2.49-.553,4.829,4.829,0,0,0-2.59.615,1.65,1.65,0,0,0-.93,1.378,1.239,1.239,0,0,0,.3.837,2.63,2.63,0,0,0,.89.652,6.745,6.745,0,0,0,1.2.448c0.42,0.112,1.12.276,2.07,0.488,1.21,0.251,2.32.53,3.3,0.834a10.718,10.718,0,0,1,2.56,1.124,4.69,4.69,0,0,1,2.29,4.244A5.331,5.331,0,0,1,4073.71,147.005Zm12.29,3a9.995,9.995,0,0,1-16.92,7.212,17.419,17.419,0,0,1-20.29-20.3,9.993,9.993,0,0,1,14.13-14.128,17.419,17.419,0,0,1,20.29,20.3A9.929,9.929,0,0,1,4086,150Z" transform="translate(-4046 -120)"/> ',
	'twitter.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="twitter" class="cls-1" d="M3513.21,124.991a8.47,8.47,0,0,1,6.48,2.539,13.407,13.407,0,0,0,4.21-1.4l1.02-.551a8.091,8.091,0,0,1-2.66,3.8,4.145,4.145,0,0,1-.9.63v0.02c1.78-.018,3.24-0.811,4.64-1.24v0.019a13.029,13.029,0,0,1-2.78,3.15c-0.43.334-.86,0.669-1.28,1a23.193,23.193,0,0,1-.38,5.2c-2.05,9.083-7.48,15.25-16.08,17.891a24.9,24.9,0,0,1-11.61.472,30.091,30.091,0,0,1-4.83-1.555,19.693,19.693,0,0,1-2.32-1.18l-0.72-.433a13.642,13.642,0,0,0,2.62.1c0.81-.128,1.6-0.095,2.34-0.256a16.907,16.907,0,0,0,4.93-1.752,9.132,9.132,0,0,0,2.22-1.436,7.041,7.041,0,0,1-2.44-.433,8.193,8.193,0,0,1-5.21-5.177c0.81,0.087,3.13.294,3.67-.157a6.016,6.016,0,0,1-2.68-1.063,7.767,7.767,0,0,1-3.87-6.928l0.84,0.394a9.219,9.219,0,0,0,1.72.472,2.514,2.514,0,0,0,1.13.1h-0.04c-0.42-.474-1.09-0.79-1.51-1.3a8.194,8.194,0,0,1-1.84-7.342,9.411,9.411,0,0,1,.88-2.106,0.147,0.147,0,0,1,.04.02,5.232,5.232,0,0,0,.74.846,14.988,14.988,0,0,0,2.45,2.3,24.238,24.238,0,0,0,10.03,4.783,15.935,15.935,0,0,0,3.68.472,7.41,7.41,0,0,1,.04-3.8,8.024,8.024,0,0,1,4.64-5.432,10.181,10.181,0,0,1,1.83-.551C3512.54,125.07,3512.87,125.03,3513.21,124.991Z" transform="translate(-3486 -121)"/> ' ,
	'Youtube.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Youtube" class="cls-1" d="M3759.85,126.012L3746.79,126l-13.58.012c-3.4,0-7.21,2.225-7.21,5.489v16.247c0,3.262,3.81,5.252,7.21,5.252h26.64c3.4,0,6.15-1.99,6.15-5.252V131.5C3766,128.237,3763.25,126.012,3759.85,126.012Zm-16.44,19.744-0.07-12.372,8.77,6.24Z" transform="translate(-3726 -119.5)"/> ' ,
	'z0g.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="z0g" class="cls-1" d="M4386,160a19.956,19.956,0,0,1-14.14-5.848l1.82-1.817a17.334,17.334,0,0,0,29.65-12.169q0-.739-0.06-1.464h2.68c0.03,0.44.05,0.884,0.05,1.331A19.98,19.98,0,0,1,4386,160Zm0-5.391a14.667,14.667,0,0,1-14.61-13.311h-5.34c-0.03-.44-0.05-0.884-0.05-1.331a20.005,20.005,0,0,1,34.14-14.119l-1.88,1.883a17.342,17.342,0,0,0-29.54,10.905h2.67a14.676,14.676,0,0,1,24.98-9.023l-1.88,1.883a11.976,11.976,0,1,0,3.44,9.8H4386v-2.662h14.61q0.06,0.657.06,1.331A14.658,14.658,0,0,1,4386,154.609Z" transform="translate(-4366 -120)"/> ' ,
	'Word.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Word" class="cls-1" d="M3045.86,125.526a1.577,1.577,0,0,0-1.75-1.777c-4.84-.079-9.69,0-14.54-0.028V120h-2.71c-6.95,1.27-13.91,2.484-20.86,3.73v32.549c6.92,1.242,13.84,2.428,20.74,3.721h2.83v-3.721c4.54,0,9.07-.009,13.61.013a4.032,4.032,0,0,0,2.29-.46,5.6,5.6,0,0,0,.44-2.814C3045.85,143.856,3045.94,134.688,3045.86,125.526Zm-24.73,20.8c-0.76.405-1.89-.019-2.79,0.046-0.61-3.078-1.31-6.139-1.85-9.232-0.53,3-1.22,5.981-1.82,8.968-0.87-.047-1.75-0.1-2.63-0.163-0.75-4.093-1.63-8.158-2.33-12.261,0.77-.037,1.55-0.07,2.33-0.1,0.46,2.963.99,5.912,1.4,8.879,0.64-3.041,1.29-6.083,1.93-9.125h0c0.86-.051,1.72-0.088,2.58-0.135,0.61,3.14,1.22,6.274,1.88,9.4,0.51-3.228,1.07-6.447,1.62-9.67,0.91-.032,1.82-0.083,2.72-0.139C3023.14,137.3,3022.25,141.842,3021.13,146.321Zm23.4,8.563h-14.96v-3.721h11.79V149.3h-11.79v-2.325h11.79v-1.861h-11.79v-2.325h11.79V140.93h-11.79V138.6h11.79v-1.861h-11.79v-2.325h11.79v-1.861h-11.79v-2.325h11.79v-1.861h-11.79v-3.256h14.96v29.768Z" transform="translate(-3006 -120)"/> ',
	'Publisher.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="Publisher" class="cls-1" d="M3366,126.042a7.363,7.363,0,0,0-.24-2.1c-0.84-.981-2.26-0.6-3.37-0.7-2.81.121-5.65-.186-8.44,0.153-2.02.679-1.3,3.317-1.43,4.977h-3.16V120h-2.67c-6.89,1.261-13.79,2.484-20.69,3.726v32.553c6.86,1.247,13.73,2.432,20.58,3.721h2.78v-4.651c3.15-.009,6.3.014,9.45,0,0.91-.028,2.16.024,2.54-1.065a52.139,52.139,0,0,0,.14-6.377,14.293,14.293,0,0,0,4.23-.274,8.277,8.277,0,0,0,.28-2.53Q3365.955,135.576,3366,126.042Zm-26.82,15.5a6.336,6.336,0,0,1-3.3.423v5.475c-0.91-.093-1.82-0.186-2.72-0.289,0.05-4.948.01-9.892,0.02-14.842h0c2.45,0.079,5.43-1.023,7.46.88C3342.57,135.632,3342.05,140.088,3339.18,141.544Zm20.52,12.409h-10.34v-2.79H3357q-0.015-.93,0-1.861h-7.64v-2.325H3357q-0.015-.93,0-1.861h-7.64v-2.325H3357q-0.015-.93,0-1.861h-7.64V138.6H3357v-6.512h-7.64v-2.326h10.34v24.186Zm4.94-7.906h-3.15c-0.01-5.591.02-11.177-.01-16.768-2.11-1.73-4.94-2.451-6.78-4.488,3.3-.3,6.63-0.065,9.94-0.14v21.4Zm-28.76-11.238c0.89-.051,2-0.218,2.6.67a3.54,3.54,0,0,1,.04,3.033c-0.52.953-1.67,0.86-2.57,0.967-0.09-1.553-.08-3.112-0.07-4.67h0Z" transform="translate(-3326 -120)"/> ' ,
	'slack.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="slack" class="cls-1" d="M2791.94,122.463a3.564,3.564,0,0,0-6.78,2.2l9.22,28.348a3.561,3.561,0,0,0,6.77-2.2l-9.21-28.349m-14.28,4.639a3.56,3.56,0,1,0-6.77,2.2l9.21,28.348a3.557,3.557,0,0,0,4.33,2.208,3.52,3.52,0,0,0,2.44-4.41l-9.21-28.348m25.88,18.839a3.565,3.565,0,0,0-2.21-6.778l-28.34,9.214a3.563,3.563,0,0,0,2.2,6.774l28.35-9.21m-24.64,8.006,6.77-2.2-2.2-6.776-6.78,2.2,2.21,6.775m14.27-4.639c2.56-.832,4.94-1.6,6.78-2.2l-2.2-6.777-6.78,2.2,2.2,6.776m5.73-17.644a3.563,3.563,0,1,0-2.2-6.777l-28.35,9.214a3.563,3.563,0,0,0,2.2,6.774l28.35-9.211m-24.64,8.007c1.84-.6,4.22-1.373,6.77-2.2-0.83-2.561-1.6-4.942-2.2-6.775l-6.78,2.2,2.21,6.775m14.27-4.638,6.78-2.2q-1.11-3.388-2.2-6.778l-6.78,2.2,2.2,6.777" transform="translate(-2766 -120)"/> ' ,
	'trello.svg'  : '   <defs xmlns="http://www.w3.org/2000/svg">    <style>      .cls-1 {        fill: #666;        fill-rule: evenodd;      }    </style>  </defs>  <path xmlns="http://www.w3.org/2000/svg" id="trello" class="cls-1" d="M2882.09,120h-32.18a3.908,3.908,0,0,0-3.91,3.906v32.188a3.908,3.908,0,0,0,3.91,3.906h32.18a3.908,3.908,0,0,0,3.91-3.906V123.906A3.908,3.908,0,0,0,2882.09,120Zm-18.69,30.825a1.874,1.874,0,0,1-1.87,1.875h-8.46a1.874,1.874,0,0,1-1.87-1.875v-23.75a1.874,1.874,0,0,1,1.87-1.875h8.46a1.874,1.874,0,0,1,1.87,1.875v23.75Zm17.4-10a1.876,1.876,0,0,1-1.88,1.875h-8.45a1.874,1.874,0,0,1-1.87-1.875v-13.75a1.874,1.874,0,0,1,1.87-1.875h8.45a1.876,1.876,0,0,1,1.88,1.875v13.75Z" transform="translate(-2846 -120)"/> '
};