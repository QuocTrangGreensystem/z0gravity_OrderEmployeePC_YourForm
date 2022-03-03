<?php echo $html->script('context/jquery.contextmenu'); ?>
<?php echo $html->script('qtip/jquery.qtip'); ?>
<?php echo $html->css(array('projects')); ?>
<?php echo $html->css('context/jquery.contextmenu'); ?>
<?php echo $html->css('/js/qtip/jquery.qtip'); ?>
<?php echo $html->css('preview/tab-admin'); ?>
<?php echo $html->css('layout_admin_2019'); ?>
<style type="text/css">
.ui-datepicker-multi {
	width: 100% !important;
}
.ui-datepicker-group {
	float: left;
	width: 16%;
	margin-right: 0.66%;
}

.ui-datepicker-row-break {
	clear: both;
	margin-bottom: 0.66%;
}
.ui-datepicker table th {
	padding: 5px 0;
}
.ui-datepicker table td a {
	display: block;
	padding: 5px 0;
	border: 1px solid transparent;
	font-weight: normal;
}
.ui-datepicker table .ui-state-hover {
	border: 1px solid transparent;
}

.date-highlighted a {
	background-color: <?php echo $constraint['holiday']['color']; ?>;
}
.date-highlighted.date-repeat a {
	background-image: url(/img/repeat-indicator2.png);
	background-position: top right;
	background-repeat: no-repeat;
}

.ui-datepicker table td.ui-datepicker-today {
	background: none;
}
.ui-datepicker table td.ui-datepicker-today.ui-datepicker-current-day {
	background: inherit;
}
.ui-datepicker table td.ui-datepicker-today a {
	background: inherit;
	color: inherit;
}

#tooltip-template {
	display: none;
}
.tooltip-content {
	overflow: hidden;
}
.tooltip-content dt {
	float: left;
	display: block;
	width: 100px;
}
.tooltip-content dd {
}
.qtip-title,
.qtip-content {
	font-size: 12px;
}
.qtip-content {
	line-height: 1.25;
}
</style>
<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">

		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-tab">
					<?php echo $this->element("admin_sub_top_menu");?>
					<div class="wd-panel">
						<div class="wd-section" id="wd-fragment-1">
							<?php echo $this->element('administrator_left_menu') ?>
							<div class="wd-content">
								<h2 class="wd-t3"></h2>
								<a href="<?php echo $this->Html->url('/holidays/manage/' . ($year-1)) ?>" id="absence-prev">Prev</a>
								<span class="currentWeek"><?php echo $year ?></span>
								<a href="<?php echo $this->Html->url('/holidays/manage/' . ($year+1)) ?>" id="absence-next">Next</a>
								<div id="message-place">
									
								</div>
								<div id="holiday-container">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="tooltip-template">
	<dl class="tooltip-content">
		<dt><?php __('Repeat') ?></dt>
		<dd id="info-repeat"></dd>
		<dt><?php __('AM') ?></dt>
		<dd id="info-am"></dd>
		<dt><?php __('PM') ?></dt>
		<dd id="info-pm"></dd>
	</dl>
</div>
<script type="text/javascript">
	var year = <?php echo json_encode($year) ?>,
		holidays = <?php echo json_encode($holidays) ?>,
		i18n = <?php echo json_encode(array(
			'yes' => __('Yes', true),
			'no' => __('No', true)
		)) ?>;

	var tooltip = $('#tooltip');
	var container = $('#holiday-container');

	function buildData(){
		var result = {
			days: [],
			repeat: {}
		};
		$.each(holidays, function(date, val){
			result.days.push(date);
			result.repeat[date] = val.repeat;
		});
		return result;
	}

	$('#holiday-container').on('mouseenter', '.date-highlighted', function(ev){
		$(this).qtip({
			overwrite: false,
			show: {
				solo: true,
				event: ev.type, // Use the same event type as above
				ready: true // Show immediately - important!
			},
			content: {
				text: function(e, api){
					var td = api.target,
						key = td.data('time'),
						content = $('#tooltip-template').clone();
					if( holidays[key] ){
						content.find('#info-repeat').html(holidays[key].repeat ? i18n['yes'] : i18n['no']);
						content.find('#info-am').html(holidays[key].am ? i18n['yes'] : i18n['no']);
						content.find('#info-pm').html(holidays[key].pm ? i18n['yes'] : i18n['no']);
					}
					return content.html();
				},
				title: function(e, api){
					var td = api.target;
					return $.datepicker.formatDate('dd-mm-yy', new Date(td.data('time')));
				}
			},
			position: {
				my: 'bottom center',
				at: 'top center'
			},
			style: {
				classes: 'qtip-shadow',
				width: 150,
			}
		});
	});

	$(document).ready(function(){
		$(this).on('click', ':not(#tooltip, #tooltip *, .ui-datepicker-calendar td:not(.ui-state-disabled))', function(e){
			if( $(e.target).prop('id') != 'tooltip' )tooltip.hide();
			e.stopPropagation();
		});
		container.datepicker({
			minDate: new Date(year + '-01-01'),
			maxDate: new Date(year + '-12-31'),
			hideIfNoPrevNext: true,
			highlights: buildData(),
			numberOfMonths: [2, 6],
			dateFormat: 'dd-mm-yy',
			defaultDate: '02-01-' + year,
			//events
			onSelect: function(day, inst){
				var date = new Date(inst.currentYear, inst.currentMonth, inst.currentDay),
					week = $.datepicker.iso8601Week(date),
					year = inst.currentYear;
				if( (week == 52 || week == 53) && inst.currentMonth == 0 ){
					year--;
				} else if( week == 1 && inst.currentMonth == 11 ){
					year++;
				}
				window.open('<?php echo $this->Html->url('/holidays/') ?>?week=' + week + '&year=' + year, '_blank');
			}
		});
		// .on('click', '.ui-datepicker-calendar td:not(.ui-state-disabled)', function(e){
		// 	e.stopPropagation();
		// 	var td = $('.ui-datepicker-current-day');
		// });
	});

</script>
