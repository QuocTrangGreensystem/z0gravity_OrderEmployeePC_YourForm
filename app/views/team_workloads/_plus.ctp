<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=viewport content="width=device-width, initial-scale=1, min-scale=1, max-scale=1, shrink-to-fit=no">
    <title>Plan de charge</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style type="text/less">
        .pdc-header-table, .pdc-table {
            .comment-td {
                width: 60px;
            }
            .commitee-td {
                width: 100px;
            }
            .version-td {
                width: 120px;
            }
            .name-td {
                width: 250px;
            }
            .priority-td {
                width: 60px;
            }
            .team-td {
                width: 150px;
            }
            .task-td {
                width: 300px;
            }
        }

        .pdc-header-table {
            position: relative;
            table-layout: fixed;
            margin-bottom: 0;
            thead {
                color: #FDFDFD;
                border: none;
                .sortable {
                    &:hover {
                        cursor: pointer;
                    }
                }
                .sorted {
                    &:after {
                        content: "\e114";
                        font-family: 'Glyphicons Halflings';
                        font-size: 12px;
                        padding-left: 5px;
                        -webkit-font-smoothing: antialiased;
                        color: #21b0e4;
                    }
                    &.sorted-reverse:after {
                        content: "\e113";
                    }
                }
                .sorted.shift-sorted {
                    &:after {
                        color: #ff552b;
                    }
                }
                tr th {
                    &:first-child {
                        border-left: 2px solid #0d3a69;
                    }
                    &.last-month {
                        border-right: 2px solid #0d3a69;
                    }
                    background-color: #0d3a69;
                    width: 60px;
                    height: 50px;
                    padding: 0px 5px 5px 5px;
                    text-align: center;
                    overflow: hidden;
                    border-right: 1px solid white;
                    border-left: none;
                    border-bottom: none;
                    border-top: none;
                    font-size: 13px;
                    font-weight: 400;
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    user-select: none;
                    cursor: default;
                }
            }
            .total-td {
                width: 120px;
            }
            #capacity-row {
                border: none;
                display: none;
                background-color: white;
                margin: 0 !important;
                padding: 0 !important;
                position: absolute;
                top: 51px;
                left: 940px;
                z-index: 100;
                box-shadow: 0px 0px 6px 6px rgba(0, 39, 113, 0.3);

                td {
                    border: none;
                    width: 60px;
                    text-align: center;
                    font-size: 12px;
                    border-bottom: 1px solid #ddd;
                    border-right: 1px solid #ddd;
                }
                .team-capacity-td {
                    width: 200px;
                    vertical-align: middle;
                    background-color: #d1dbed;
                    border-bottom: 2px solid #bbb;
                }
                .capacity-text-td {
                    width: 80px;
                    background-color: lighten(#d1dbed, 8%);
                }
                .workload-td {
                    border-top: none;
                    border-left: none;
                }
                tr:nth-child(3n+0) {
                    border-bottom: 2px solid #ccc;
                }
            }
        }
        .pdc-table {
            table-layout: fixed;
            font-size: 12px;
            font-weight: 400;
            tbody {
                border: 2px solid #0d3a69;
                &:nth-child(even) {
                    background-color: #f8f8f8;
                    .conso-td {
                        background-color: #d9e6fb;
                    }
                }
                .conso-td {
                    background-color: lighten(#d9e6fb, 3%);
                }
                tr td {
                    padding: 0;
                    width: 60px;
                    height: 33px;
                    overflow: hidden;
                    vertical-align: middle;
                    border-top: none;
                    border: 1px solid #ddd;
                }
            }
            .name-td {
                font-weight: 600;
            }
            .comment-td, .workload-td, .team-td, .priority-td, .total-td, .team-total-td {
                text-align: center;
            }
            .comment-td {
                text-align: center;
                font-size: 14px;
                color: #888;
                &:hover {
                    color: #222;
                    cursor: pointer;
                }
            }
            .task-comment-td {
                .comment-td;
            }
            .total-td, .team-total-td {
                width: 60px;
            }
            .conso-td {
                color: darken(#0e4b8a, 10%);
            }
            .conso-back-div {
                position: absolute;
                top: 0;
                left: 0;
            }
            .conso-back-text {
                position: relative;
                line-height: 32px;
            }
            .editable {
                &:hover {
                    background-color: darken(#d9e6fb, 5%);
                    cursor: pointer;
                }
                input {
                    margin: 0 5px;
                    width: 50px;
                }
            }
        }
        .td-tooltip {
            position: absolute;
            background-color: #2d2d2d;
            color: white;
            padding: 5px 8px;
            text-align: center;
            width: 110px;
            height: 50px;
            line-height: 20px;
            font-size: 12px;
            z-index: 1000;
            &.purple-tooltip {
                background-color: #2e1c3f;
            }
        }
        .header-container {
            background-color: white;
            position: fixed;
            padding: 51px 0 0 20px;
            top: 0px;
            z-index: 5;
        }
        .option-bar {
            padding: 10px 0 10px 20px;
            height: 51px;
            position: fixed;
            background-color: white;
            top: 0;
            z-index: 10;
        }
        .pdc-container {
            padding: 100px 0 0 20px;
        }
        .search-container {
            display: inline-block;
            position: relative;
            #search-text {
                display: inline-block;
                width: 300px;
            }
            .close-btn {
                display: none;
                position: absolute;
                right: 5px;
                top: 6px;
                background-color: transparent;
                color: #bbb;
                &:hover {
                    color: #ccc;
                    cursor: pointer;
                }
                padding: 0;
                font-weight: 300;
                font-size: 18px;
            }
        }
        #commentsPopup .modal-body {
            height: 600px;
            overflow-y: scroll;
        }

        #empty-btn, #timesheet-btn, #capacity-btn {
            margin-left: 30px;
            width: 110px;
        }
        #team-select-container {
            display: inline-block;
            margin-left: 30px;
            button {
                width: 110px;
            }
            .dropdown-menu {
                width: 260px;
                font-size: 12px;
                .dropdown-options {
                    padding: 0px 20px 10px 20px;
                    input[type="checkbox"] {
                        margin: 1px 0 0 -20px;
                    }
                }
            }
        }
        #export-container {
            display: inline-block;
            margin-left: 30px;
            button {
                width: 110px;
            }
            .dropdown-menu {
                width: 150px;
                font-size: 12px;
            }
        }
        .dropdown-link {
            cursor: pointer;
            padding: 5px 20px;
            color: #555555;
            white-space: nowrap;
            &:hover {
                color: #262626;
                background-color: #f5f5f5;
            }
        }
        .tooltip {
            .tooltip-inner {
                max-width: 300px;
                background-color: #f5f5f5;
                color: #222;
                border: 1px solid #ccc;
            }
            &.left .tooltip-arrow {
                border-left-color: #ccc;
            }
            &.right .tooltip-arrow {
                border-right-color: #ccc;
            }
            &.in {
                opacity: 1;
            }
        }

        .glyphicon.spinning {
            animation: spin 1.5s infinite linear;
            -webkit-animation: spin2 1.5s infinite linear;
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg); }
            to { transform: scale(1) rotate(360deg); }
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg); }
            to { -webkit-transform: rotate(360deg); }
        }

        #loading-icon {
            padding: 40px 20px;
            text-align: center;
            .glyphicon {
                font-size: 2rem;
                margin-right: 15px;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.7.1/less.min.js"></script>
</head>

<body>
    <div class="option-bar">
        <div class="search-container">
            <input type="text" class="form-control input-sm" id="search-text" placeholder="Recherche" />
            <span class="glyphicon glyphicon-remove-circle close-btn"></span>
        </div>
        <button type="button" class="btn btn-sm btn-default btn-success active" id="timesheet-btn">Consommés</button>
        <div class="dropdown" id="team-select-container">
            <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="team-select" data-toggle="dropdown">
                Equipes <span class="caret"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="team-select">
                <div class="dropdown-link">Effacer sélection</div>
                <div class="dropdown-options"></div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-default" id="capacity-btn">Capacité</button>
        <div class="dropdown" id="export-container">
            <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="export-btn" data-toggle="dropdown">
                Export <span class="caret"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="export-btn">
                <div class="dropdown-link" data-export="workload">Charges</div>
                <div class="dropdown-link" data-export="timesheet">Consos</div>
                <div class="dropdown-link" data-export="capacity">Capacité</div>
            </div>
        </div>
    </div>
    <div class="header-container">
        <table class="table pdc-header-table">
            <thead>
                <tr>
                    <th class="comment-td" data-toggle="tooltip" data-placement="right" title="<strong>Fil de discussion du projet</strong>">Msg.</th>
                    <th class="commitee-td"><span class="sortable" data-sort="commitee">Comité</span></th>
                    <th class="version-td"><span class="sortable sorted" data-sort="version">Version</span></th>
                    <th class="name-td"><span class="sortable" data-sort="name">Projet</span></th>
                    <th class="priority-td"><span class="sortable" data-sort="priority">Prior.</span></th>
                    <th class="total-td" data-toggle="tooltip" data-placement="left" title="<strong>Charges totales du projet</strong> (à gauche) <strong>ou par équipe</strong> (à droite).<br>Tri sur le total projet.">
                        <span class="sortable" data-sort="totalWorkload">Totaux</span></th>
                    <th class="team-td">Equipe</th>
                    <th class="task-td">Tâche</span></th>
                    <th class="workload-td" data-toggle="tooltip" data-placement="left" title="<strong>Charges de référence de la tâche</strong>">Ref.</span></th>
                    <th class="workload-td" data-toggle="tooltip" data-placement="left" title="<strong>Charges ou consos totales de la tâche.</strong><br>Tri sur les charges des équipes affichées.">
                        <span class="sortable shift-sortable" data-sort="teamWorkload">Tot.</span></th>
                    <th class="task-comment-td last-month" data-toggle="tooltip" data-placement="left" title="<strong>Fil de discussion de la tâche</strong>">Msg.</th>
                </tr>
            </thead>
            <tbody id="capacity-row">
            </tbody>
        </table>
    </div>
    <div class="pdc-container">
        <table class="table pdc-table">
        </table>
    </div>

    <div class="modal" id="commentsPopup" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="commentsTitle"></h4>
          </div>
          <div class="modal-body">
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="loadingPopup" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div id="loading-icon">
                <span class="glyphicon glyphicon-refresh spinning"></span>
                <span>Chargement des données...</span>
            </div>
        </div>
      </div>
    </div>
	<?php
	// get capacity
	foreach ($listTeamIds as $id) {
		$args = array(
			'type' => 1,
			'view_by' => 'month',
			'start_date' => $start_date,
			'end_date' => $end_date,
			'summary' => 1,
			'pc' => $id,
		);
		$extra = $this->requestAction(array('controller' => 'new_staffing', 'action' => 'index', '?' => $args));
		$_capacity[$listTeam[$id]] = !empty($extra['summary']['capacity']) ? $extra['summary']['capacity'] : $zeroCapa;
	}
	$listAvartar = array();
	$listIdEm = array_keys($listEmployee);
	foreach ($listIdEm as $_id) {
		$link = $this->UserFile->avatar($_id, "small");
		$listAvartar[$_id] = $link;
	}
	?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.2.8/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php echo $html->script('d3/pdc.js'); ?>

    <!-- <script type="text/javascript" src="./pdc_data.js"></script>
    <script type="text/javascript" src="./pdc.js"></script> -->
	<script type="text/javascript">
		// doan nay custom function cho nhung method con thieu.
		// method fill
		if ( ![].fill)  {
			Array.prototype.fill = function( value ) {
				var O = Object( this );
				var len = parseInt( O.length, 10 );
				var start = arguments[1];
				var relativeStart = parseInt( start, 10 ) || 0;
				var k = relativeStart < 0 ? Math.max( len + relativeStart, 0) : Math.min( relativeStart, len );
				var end = arguments[2];
				var relativeEnd = end === undefined ? len : ( parseInt( end)  || 0) ;
				var final = relativeEnd < 0 ? Math.max( len + relativeEnd, 0 ) : Math.min( relativeEnd, len );
				for (; k < final; k++) {
					O[k] = value;
				}
				return O;
			};
		}
		// method assign
		if (typeof Object.assign != 'function') {
			Object.assign = function(target) {
				'use strict';
				if (target == null) {
				  throw new TypeError('Cannot convert undefined or null to object');
				}

				target = Object(target);
				for (var index = 1; index < arguments.length; index++) {
					var source = arguments[index];
					if (source != null) {
						for (var key in source) {
							if (Object.prototype.hasOwnProperty.call(source, key)) {
								target[key] = source[key];
							}
						}
					}
				}
				return target;
			};
		}
		// method startsWith
		if (!String.prototype.startsWith) {
			String.prototype.startsWith = function(searchString, position){
				position = position || 0;
				return this.substr(position, searchString.length) === searchString;
			};
		}
		// teams
		var _teamsObj = <?php echo json_encode($listTeam); ?>;
		var _teams = [];
		var _teams = $.map(_teamsObj, function(value) {
			return [value];
		});
		// period
		var _periods = <?php echo json_encode($_period) ?>;
		var _period = [];
		var _period = $.map(_periods, function(value) {
			return [value];
		});
		var numberOfMonth = _period.length;
		// capacity
		var capacity = <?php echo json_encode($_capacity); ?>;
		var _capacity = {};
		if(capacity){
			$.each(capacity, function(t, value){
				// var a = [];
				var a = $.map(value, function(val) {
					return [val];
				});
				_capacity[t] = a;
			});
		}
		var rawPjList = <?php echo json_encode($stringPj); ?>;
		var rawData = <?php echo json_encode($rawData); ?>;
		var assignConsume = <?php echo json_encode($assignConsume) ?>;
		var assignWorkloads = <?php echo json_encode($assignWorkloads) ?>;
		// -----------
		var pjList = d3.map();
		var pjCsvData = d3.dsvFormat(";").parseRows(rawPjList);
		for (var i = 0; i < pjCsvData.length; i++) {
			var line = pjCsvData[i];
			var name = line[1];
			pjList.set(name, {
				id: line[0],
				name: name,
				cdp: line[3],
				version: line[7],
				commitee: line[4],
				priority: line[6],
			});
		}

		var readNumber = function(value) {
			value = value.trim();
			if (value.length == 0) {
				return 0.0;
			} else {
				var nr = parseFloat(value);
				return isNaN(nr)?0.0:nr;
			}
		};
		var sum = function(a,b) {
			return a+b;
		};
		var _projects = d3.map();
		var _counter = 0;
		for (var k=0; k<_teams.length; k++) {
			var team = _teams[k];
			var csvData = d3.dsvFormat(";").parseRows(rawData[team]);

			for (var i = 0; i < csvData.length; i++) {
				// Process task
				var line = csvData[i];
				var name = line[1];
				var pj = pjList.get(name);
				if (pj === null || pj === undefined) {
					console.log("Erreur, pj non trouvé : " + name);
					continue;
				}
				var project = null;
				if (_projects.has(pj.id)) {
					project = _projects.get(pj.id);
				} else {
					_projects.set(pj.id, {
						id: pj.id,
						name: name,
						version: pj.version,
						commitee: pj.commitee,
						// priority: pj.priority.substring(0,1),
						priority: pj.priority,
						// List of tasks
						tasks: [],
					});
					project = _projects.get(pj.id);
				}
				var task = {
					id: line[0],
					team: team,
					phase: line[2],
					name: line[3],
					teamId: line[4],
					// Task type : non linear ("NL") or linear ("L")
					type: "NL",
					// Workload reference
					reference: readNumber(line[5]),
					// Task workload by month => can be empty for linear tasks
					workload: line.slice(6, 6+numberOfMonth).map(readNumber),
					// Workload ids (for editing)
					wIds: [],
					// Total task workload
					totalWorkload: 0,
					// Task timesheet by month
					timesheet: line.slice(7+numberOfMonth, 7+numberOfMonth+numberOfMonth).map(readNumber),
					// Task total timesheet
					totalTimesheet: 0,
				};
				task.totalWorkload = task.workload.reduce(sum,0);
				task.totalTimesheet = task.timesheet.reduce(sum,0);
				// fake ids
				task.wIds = new Array(numberOfMonth).fill(0).map(function(v, i) { return _counter++; });
				project.tasks.push(task);
			}
		}
		_projects = _projects.values();
		//$('#loadingPopup').modal('show');
		PDCData.load(_period, _teams, _projects, _capacity);
		$(initTable);
		//$('#loadingPopup').modal('hide');
		// var x = $('.pdc-header-table .task-td').position();
		// var w = $('.pdc-header-table .workload-td:first').width();
		// $('#capacity-row').css('left', x.left-209);
		// $('#capacity-row .capacity-text-td').width(w);
		// $('#capacity-row .workload-td').width(w);
		// var saveCommentPj = function(){
		// 	var text = $('.textarea-ct').val(),
		// 		_id = $('.submit-btn-msg').data('id');
		// 	var d = new Date,
		// 	dformat =   [ d.getFullYear(),
		// 				((d.getMonth()+1) < 10 ? '0'+(d.getMonth()+1) : (d.getMonth()+1)),
		// 				(d.getDate()< 10 ? '0'+d.getDate() : d.getDate())].join('-')+' '+
		// 				[(d.getHours() < 10 ? '0'+d.getHours() : d.getHours()),
		// 				(d.getMinutes() < 10 ? '0'+d.getMinutes() : d.getMinutes()),
		// 				(d.getSeconds() < 10 ? '0'+d.getSeconds() : d.getSeconds())].join(':');
		// 	var link = listAvartar[employee_id];
		// 	var content = text.replace(/\n/g, "<br>");
		// 	if(text != ''){
		// 		$.ajax({
		// 			url: '/zog_msgs/saveComment',
		// 			type: 'POST',
		// 			data: {
		// 				id: _id,
		// 				content: text
		// 			},
		// 			success: function(success){
		// 				$('.modal-body').append('<div class="my-comment"><div class="my-date"><span>' + dformat + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>')
		// 				$('.textarea-ct').val('');
		// 			}
		// 		});
		// 	}
		// };
		// var saveCommentTask = function(){
		// 	var text = $('.textarea-ct').val(),
		// 		_id = $('.submit-btn-msg').data('id');
		// 	if(text != ''){
		// 		$.ajax({
		// 			url: '/team_workloads/saveTxtOfTask',
		// 			type: 'POST',
		// 			data: {
		// 				id: _id,
		// 				content: text
		// 			},
		// 			dataType: 'json',
		// 			success: function(data){
		// 				if(data){
		// 					var time = data.time,
		// 						content = data.content,
		// 						link = listAvartar[data.employee_id];
		// 					$('.modal-body').append('<div class="my-comment"><div class="my-date"><span>' + time + '</span></div><div class="my-content"><span>' + content + '</span></div></div><div class="right-avatar"><img class="avatar" src="'+link+'" alt="photo"></div>')
		// 					$('.textarea-ct').val('');
		// 				}
		// 			}
		// 		});
		// 	}
		// };
		// $('#scrollLeftAbsence').on('scroll', function(e){
		//     var amount = $('#scrollLeftAbsence').scrollTop();
		//     $('#right-scroll').scrollTop(amount);
		// });
		// function setupScroll(){
		//     rightContent = $('.pdc-table');
		//     rightHeaderHeight = $('.pdc-header-table').height();
		//     rightScroll = $('#scrollLeftAbsenceContent');
		//     rightScrollContainer = $('#scrollLeftAbsence');
		//     //fix position for right scroll container
		//     rightScrollContainer.height($('.wd-content-project').height()-75).css('top', rightHeaderHeight + 45);
		//     //right scroll content height
		//     var rightHeight = rightContent.height() + 630;
		//     rightScroll.height(rightHeight);
		// }
		// setupScroll();
		// $('.wd-content-project').mousewheel(function(event) {
		//     var amount = event.deltaY * event.deltaFactor;
		//     scaleY(amount);
		// });
		// $(document).on('mousewheel', '.wd-content-project', function(){
		//     //do check here
		//     return false;
		// });
		// function scaleY(amount){
		//     //down -> negative
		//     $('#scrollLeftAbsence')[0].scrollTop -= amount;
		// }
	</script>

</body>

</html>
