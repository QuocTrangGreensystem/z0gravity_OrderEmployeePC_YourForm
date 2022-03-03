////////////////////////////////////////////////////////////////////
///////////////////////// DATA MANAGEMENT //////////////////////////
////////////////////////////////////////////////////////////////////

// Data container
// load function must be called when page is first loaded, passing in data
var PDCData = {
    load: function(period, teams, projects, capacity, show_popup) {
        this.period = period;
        this.projects = projects;
        this.teams = teams.sort();
        this.show_popup = show_popup||0;
        this.capacity = {};
        for (var i=0; i<this.teams.length; i++) {
            var key = this.teams[i];
            var total = capacity[key].reduce(function(a,b) {return a+b;}, 0);
            this.capacity[key] = {resources: capacity[key], totalResources: total};
        }

        // Sort project tasks
        for (var j=0; j<this.projects.length; j++) {
            var project = this.projects[j];
            project.tasks = project.tasks.sort(function (t1, t2) {
                if (t1.team < t2.team) {
                    return -1;
                } else if (t1.team == t2.team) {
                    if (t1.phase < t2.phase) {
                        return -1;
                    } else if (t1.phase == t2.phase) {
                        return 0;
                    }
                }
                return 1;
            });
        }

        this.updateTotals(this.projects);
    },


    // Update totals for specific projects
    updateTotals: function(projects) {
        var pdcData = this;
        var initByTeamData = function() {
            var data = {};
            pdcData.teams.forEach(function(team) {
                data[team] = {total: 0, months: new Array(pdcData.period.length).fill(0)};
            });
            return data;
        };

        for (var i=0; i<this.projects.length; i++) {
            var project = this.projects[i];
            project.totalWorkload = 0;
            project.totalTimesheet = 0;
            project.workloadByTeam = initByTeamData();
            project.timesheetByTeam = initByTeamData();
            for (var j=0; j<project.tasks.length; j++) {
                var t = project.tasks[j];
                project.totalWorkload += t.totalWorkload;
                project.workloadByTeam[t.team].total += t.totalWorkload;
                project.totalTimesheet += t.totalTimesheet;
                project.timesheetByTeam[t.team].total += t.totalTimesheet;
                for (var k=0; k<this.period.length; k++) {
                    project.workloadByTeam[t.team].months[k] += t.workload[k];
                    project.timesheetByTeam[t.team].months[k] += t.timesheet[k];
                }
            }
        }
    },


    // Computes total workload and timesheets by team
    updateCapacity: function() {
        for (var i=0; i<this.teams.length; i++) {
            var team = this.teams[i];
            var teamCap = this.capacity[team];
            teamCap.workload = new Array(this.period.length).fill(0.0);
            teamCap.totalWorkload = 0;
            teamCap.timesheet = new Array(this.period.length).fill(0.0);
            teamCap.totalTimesheet = 0;
        }
        for (var i=0; i<this.projects.length; i++) {
            var project = this.projects[i];
            for (var j=0; j<this.teams.length; j++) {
                team = this.teams[j];
                teamCap = this.capacity[team];
                var workload = project.workloadByTeam[team].months;
                var timesheet = project.timesheetByTeam[team].months;
                for (var k=0; k<workload.length; k++) {
                    teamCap.workload[k] += workload[k];
                    teamCap.totalWorkload += workload[k];
                    teamCap.timesheet[k] += timesheet[k];
                    teamCap.totalTimesheet += timesheet[k];
                }
            }
        }
    },

    // Computes project total workload for selected teams (used for "month" and "total workload" column ordering)
    getProjectWorkloadForSelectedTeams: function(selectedTeams) {
        if (this.projectWorkloadForTeams !== undefined && this.projectWorkloadForTeams !== null)
            return this.projectWorkloadForTeams;

        this.projectWorkloadForTeams = d3.map();
        for (var i=0; i<this.projects.length; i++) {
            var project = this.projects[i];
            var workload = new Array(this.period.length).fill(0);
            var diff = new Array(this.period.length).fill(0);
            var total = 0;
            var totalDiff = 0;
            for (var j=0; j<this.teams.length; j++) {
                var team = this.teams[j];
                if (selectedTeams.has(team)) {
                    var teamWorkload = project.workloadByTeam[team].months;
                    var teamTimesheet = project.timesheetByTeam[team].months;
                    total += project.workloadByTeam[team].total;
                    totalDiff += project.timesheetByTeam[team].total - project.workloadByTeam[team].total;
                    for (var k=0; k<this.period.length; k++) {
                        workload[k] += teamWorkload[k];
                        diff[k] += teamTimesheet[k] - teamWorkload[k];
                    }
                }
            }
            this.projectWorkloadForTeams.set(project.id, {total: total, months: workload, diff: diff, totalDiff: totalDiff});
        }
        return this.projectWorkloadForTeams;
    },

    teamSelectionChanged: function() {
        this.projectWorkloadForTeams = null;
    },

    // Saves cell data
    saveWorkload: function(data, newValue, project) {
        var oldValue = data.task.workload[data.index];
        data.task.workload[data.index] = newValue;
        data.task.totalWorkload += (newValue-oldValue);
        this.updateTotals([project]);
        this.teamSelectionChanged();
        updateProjectRows([project]);
    },
	
    // Generate Excel files
    exportExcelXLSX: function(type) {
	/* EMAIL: Z0G 28/2/2020 Blocking point prod5 : impossible to export */ 
		var hrd = {};
		var content = [];
		switch(type){
			case 'capacity':
				
				break;
			
			case 'timesheet':
			
				break;
				
			default:
			case 'workload':
				console.log( 'workload' );
				hrd = {
					commitee: i18n.committee,
					version: i18n.version,
					id: i18n.project_id,
					name: i18n.project_name,
					priority: i18n.priority,
					task_team: i18n.task_team,
					task_name: i18n.task_name,
					task_reference: i18n.task_reference,
					task_totalWorkload: i18n.totalWorkload
				}
				$.each(this.period, function(ind, month) { 
					hrd[ind] = month; 
				});
				console.log( hrd);
				var projects = this.projects.sort(function(p1,p2) {return p1.id.localeCompare(p2.id);});
				projects.forEach(function(pj, index) {
					var p = {
						commitee : pj.commitee,
						version : pj.version,
						id : pj.id,
						name : pj.name,
						priority : pj.priority
					};
					pj.tasks.forEach(function(task) {
                        var r = p; // Lỗi! chỗ này, tạm dừng theo yêu cầu
						r.task_team = task.team;
						r.task_name = task.name;
						r.task_reference = task.reference.toFixed(2);
						r.task_totalWorkload = task.totalWorkload.toFixed(2);
						
                        for (var i=0; i<task.workload.length; i++) {
                            r[i] = task.workload[i].toFixed(2);
                        }
						console.log( r);
						content.push(r);
                    });
				}, this );
				break;
			
		}
	},
	
    exportExcel: function(type) {
        var xlsContent = 'data:application/vnd.ms-excel,<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40"><x:ExcelWorkbook ><x:WindowHeight>15000</x:WindowHeight><x:WindowWidth>20000</x:WindowWidth><x:WindowTopX>100</x:WindowTopX><x:WindowTopY>100</x:WindowTopY></x:ExcelWorkbook><Worksheet ss:Name="Plan de charge"><Table>';
        var wrap = function(type, content) { return '<Cell><Data ss:Type="' + type + '">' + content + '</Data></Cell>'; };
        var wrapString = function(content) { return wrap("String", content); };
        var wrapNumber = function(content) { return wrap("Number", content); };
        var filename = "pdc_workload";

        if (type == "workload" || type == "timesheet") {
            var projects = this.projects.sort(function(p1,p2) {return p1.id.localeCompare(p2.id);});
            xlsContent += '<ss:Column ss:Width="100"/><ss:Column ss:Width="200"/><ss:Column ss:Width="100"/><ss:Column ss:Width="320"/>' +
                '<ss:Column ss:Width="50"/><ss:Column ss:Width="200"/>' +
                // ((type == "workload")?'<ss:Column ss:Width="320"/><ss:Column ss:Width="100"/>':'') +
                ((type == "workload")?'<ss:Column ss:Width="320"/><ss:Column ss:Width="100"/>':'') +
                '<ss:Column ss:Width="100"/>';

            xlsContent += '<Row>' + wrapString("Comité") + wrapString("Version") + wrapString("ID projet") + wrapString("Nom projet") +
                wrapString("Priorité") + wrapString("Equipe") +
                // ((type == "workload")?(wrapString("Tâche") + wrapString("Référence") + wrapString("Charge totale")):(wrapString("Conso totale"))) +
                ((type == "workload") ? (wrapString("Tâche") + wrapString("Référence") + wrapString("Charge total")) : (wrapString("Consume total"))) +
                this.period.reduce(function(sum, month) { return sum + wrapString(month); }, '') + '</Row>';
            projects.forEach(function(pj, index) {
                var rowString = '<Row>';
                rowString += wrapString(pj.commitee);
                rowString += wrapString(pj.version);
                rowString += wrapString(pj.id);
                rowString += wrapString(pj.name);
                rowString += wrapString(pj.priority);
                if (type == "workload") {
                    pj.tasks.forEach(function(task) {
                        var taskString = wrapString(task.team);
                        taskString += wrapString(task.name);
                        taskString += wrapNumber(task.reference.toFixed(2));
                        taskString += wrapNumber(task.totalWorkload.toFixed(2));
                        for (var i=0; i<task.workload.length; i++) {
                            taskString += wrapNumber(task.workload[i].toFixed(2));
                        }
                        xlsContent += rowString + taskString + '</Row>\n';
                    });
                } else {
                    var _listTeams = [];
                    pj.tasks.forEach(function(task) {
                        _listTeams[task.team] = task.team;
                    });
                    for (var key in _listTeams){
                        var _csAll = (_totalConsumeOfPj[key] !== undefined && _totalConsumeOfPj[key][pj.id] !== undefined ) ? _totalConsumeOfPj[key][pj.id] : undefined;
                        var taskString = wrapString(key);
                        var val = _csAll !== undefined ? _csAll['total'] : 0.00;
                        taskString += wrapNumber(val.toFixed(2));
                        for (var i=0; i < numberOfMonth; i++) {
                            if(_csAll !== undefined && _csAll[i] !== undefined)
                                taskString += wrapNumber(_csAll[i].toFixed(2));
                            else
                                taskString += wrapNumber(0.00);
                        }
                        xlsContent += rowString + taskString + '</Row>\n';
                    }
                }
            }, this);
            if (type == "timesheet")
                filename = "pdc_consume";

        } else if (type == "capacity") {
            this.updateCapacity();
            xlsContent += '<ss:Column ss:Width="200"/><ss:Column ss:Width="120"/><ss:Column ss:Width="100"/>';
            xlsContent += '<Row>' + wrapString("Equipe") + wrapString("Donnée") + wrapString("Total") +
                this.period.reduce(function(sum, month) { return sum + wrapString(month); }, '') + '</Row>';
            this.teams.forEach(function(team) {
                var teamCap = this.capacity[team];
                xlsContent += '<Row>' + wrapString(team) + wrapString("Capacité") + wrapNumber(teamCap.totalResources) +
                    teamCap.resources.reduce(function(sum, cap) { return sum + wrapNumber(cap); }, '') + '</Row>\n';
                xlsContent += '<Row>' + wrapString(team) + wrapString("Charge") + wrapNumber(teamCap.totalWorkload) +
                    teamCap.workload.reduce(function(sum, cap) { return sum + wrapNumber(cap); }, '') + '</Row>\n';
                xlsContent += '<Row>' + wrapString(team) + wrapString("Ecart") + wrapNumber(teamCap.totalWorkload - teamCap.totalResources) +
                    teamCap.resources.reduce(function(sum, cap, i) { return sum + wrapNumber(teamCap.workload[i]-cap); }, '') + '</Row>\n';
            }, this);
            filename = "pdc_capacity";
        }
        xlsContent += '</Table></Worksheet></Workbook>';
        var blob = new Blob([xlsContent], { type: 'data:application/vnd.ms-excel;' });
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, filename + ".xls");
        } else {
            var encodedUri = encodeURI(xlsContent);
            var link = document.createElement("a");
            link.style.display = 'none';
            // link.setAttribute("href", encodedUri);
			var _url = window.URL.createObjectURL(blob);
			link.setAttribute("href", _url);
            document.body.appendChild(link);
            link.setAttribute("download", filename + ".xls");
            link.click();
			window.URL.revokeObjectURL(_url);
            link.remove();
        }
    }

};
////////////////////////////////////////////////////////////////////
///////////////////////// UI MANAGEMENT ////////////////////////////
////////////////////////////////////////////////////////////////////
var state = {
    withTimesheet: true,
    sort: 'version',
    reversed: false,
    shifted: false,
    selectedTeams: []
};

var getSelectedTeams = function(options) {
    if (options.selectedTeams.length > 0) {
        var selectedTeams = d3.set();
        options.selectedTeams.forEach(function(v) {
            selectedTeams.add(PDCData.teams[v]);
        });
        return selectedTeams;
    } else {
        return d3.set(PDCData.teams);
    }
};


////////////////////////////////////////////////////////////////////
/////////////////////////// UI CALLBACKS ///////////////////////////

var sortCallback = function(event) {
    var target = $(this);
    var sort = target.attr("data-sort");
    var sorted = target.hasClass("sorted");
    var reversed = target.hasClass("sorted-reverse");
    var shifted = (target.hasClass("shift-sortable") && event.shiftKey);
    if (sorted === false) {
        $(".pdc-header-table .sortable").toggleClass("sorted sorted-reverse shift-sorted", false);
        target.toggleClass("sorted", true);
        target.toggleClass("shift-sorted", shifted);
        reversed = false;
    } else {
        target.toggleClass("sorted-reverse", !reversed);
        target.toggleClass("shift-sorted", shifted);
        reversed = !reversed;
    }
    updateTable({
        sort: sort,
        reversed: reversed,
        shifted: shifted
    });
};
var searchCallback = function() {
    if (this.value.length > 0) {
        $(".search-container .close-btn").show();
    } else {
        $(".search-container .close-btn").hide();
    }
    var txt = this.value.trim();
    updateTable({
        search: txt
    });
};
var emptySearchCallback = function() {
    $(this).hide();
    var input = $("#search-text").val("").focus();
    input.trigger("input");
};
var commentCallback = function(id) {
    var pj = $('.project-'+id).closest("tbody")[0].__data__;
    $("#commentsTitle").text('Projet: '+pj.name);
    var popup = $("#commentsPopup");
    var body = popup.find(".modal-body");
    popup.find(".comment-btn")
        .html('<button data-id="'+pj.id+'" onclick="saveCommentPj()" class="submit-btn-msg" type="button"><img src="'+_urlImg+'" alt="" /></button><textarea class="textarea-ct" name="name" rows="2" cols="40"></textarea>');
    body.html('');
    var _html = '';
    $.ajax({
        url: '/zog_msgs/getComment',
        type: 'POST',
        data: {
            id: pj.id
        },
        dataType: 'json',
        success: function(data) {
            if (data['comment']) {
                $.each(data['comment'], function(ind, _data) {
                    var idEm = _data['employee_id']['id'],
                        name = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                        content = _data['content'].replace(/\n/g, "<br>"),
                        date = _data['created'];
                    var link = listAvartar[idEm];
                    _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+link+'"></div><div class="content-comment"><div class="name"><h5>'+ name +'</h5><em>'+ date +'</em></div><div class="comment">'+ content +'</div></div></div>';
                });
            } else {
                _html += '';
            }
            body.html(_html);
        }
    });
    body.find('img').load(function() {
        body.scrollTop(body[0].scrollHeight);
    });
    popup.modal('show');
};
var liveCommentCallback = function(id) {
    var _html = '';

    var profile = $('.class_'+id+'').data('profile');
    var popup = $('#template_comment');
    popup.find(".add-comment")
        .html('<button data-id="'+id+'" onclick="addCommentCallback() '+profile+'" class="submit-btn-msg" type="button"><img src="/img/ui/blank-plus.png"></button><textarea '+profile+' class="textarea-ct" name="name" rows="2" cols="40"></textarea>');
    $.ajax({
        url: '/project_livrables/getComment',
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json',
        success: function(data) {
            html = '<div id="content-comment-id">';
            if (data['comment']) {
                $.each(data['comment'], function(ind, _data) {
                        var idEm = _data['employee_id']['id'],
                        nameEmloyee = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                        comment = _data['comment'] ? _data['comment'].replace(/\n/g, "<br>") : '',
                        created = _data['created'];
                        var avartarImage = listAvartar[idEm];
                        _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+ avartarImage +'"></div><div class="content-comment"><div class="name"><h5>'+ nameEmloyee +'</h5><em>'+ created +'</em></div><div class="comment">'+ comment +'</div></div></div>';                        
                    });
            } else {
                _html += '';
            }
            _html += '</div>';
            $('#content_comment').html(_html);
            
            var createDialog2 = function(){
                $('#template_comment').dialog({
                    position    :'center',
                    autoOpen    : false,
                    autoHeight  : true,
                    modal       : true,
                    width       : 500,
                    minHeight   : 50,
                    open : function(e){
                        var $dialog = $(e.target);
                        $dialog.dialog({open: $.noop});
                    }
                });
                createDialog2 = $.noop;
            }
            createDialog2();
            $("#template_comment").dialog('option',{title:''}).dialog('open');
            
        }
    });
   
};
var addCommentCallback = function(){
    var text = $('.textarea-ct').val(),
        _id = $('.submit-btn-msg').data('id');
    if(text != ''){
        var _html = '';
        $.ajax({
            url: '/project_livrables/update_text',
            type: 'POST',
            data: {
                data:{
                    id: _id,
                    text_1: text
                }
            },
            dataType: 'json',
            success: function(data){
                if(data){
                    var idEm =  data['_idEm'],
                    avartarImage = listAvartar[idEm],
                    nameEmloyee = data['text_updater'],
                    comment = data['comment'],
                    created = data['text_time'];
                    _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+ avartarImage +'"></div><div class="content-comment"><div class="name"><h5>'+ nameEmloyee +'</h5><em>'+ created +'</em></div><div class="comment">'+ comment +'</div></div></div>';

                    $('#content_comment').append(_html);
                    $('.textarea-ct').val("");
                }
            }
        });
    }
};
var taskCommentCallback = function(id) {
    var task = $('.task-id-'+id).closest("tr")[0].__data__;
    $("#commentsTitle").text('Projet Task: '+task.name);
    var popup = $("#commentsPopup");
    var body = popup.find(".modal-body");
    // Todo : popup with task comments
    // ajax call with task.id
    popup.find(".comment-btn")
        .html('<button data-id="'+task.id+'" onclick="saveCommentTask()" class="submit-btn-msg" type="button"><img src="'+_urlImg+'" alt="" /></button><textarea class="textarea-ct" name="name" rows="2" cols="40"></textarea>');
    body.html('');
    var _html = '';
    $.ajax({
        url: '/team_workloads/getTxtOfTask',
        type: 'POST',
        data: {
            id: task.id
        },
        dataType: 'json',
        success: function(data) {
            if (data) {
                $.each(data, function(ind, _data) {
                    var idEm = _data['employee_id']['id'],
                        name = _data['employee_id']['first_name'] + ' ' + _data['employee_id']['last_name'],
                        content = _data['comment'],
                        date = _data['created'];
                    var link = listAvartar[idEm];
                    _html += '<div class="content"><div class= "avartar-image" style="width: 60px"><img class="avartar-image-img" src="'+link+'"></div><div class="content-comment"><div class="name"><h5>'+ name +'</h5><em>'+ date +'</em></div><div class="comment">'+ content +'</div></div></div>';

                });
            } else {
                _html += '';
            }
            body.html(_html);
        }
    });
    body.find('img').load(function() {
        body.scrollTop(body[0].scrollHeight);
    });
    popup.modal('show');
};
var capacity_timeout = 0; 
var capacityCallback = function() {
    var btn = $(this);
    var active = btn.hasClass("active");
    btn.toggleClass("btn-success active", !active);
    if (!active)
        updateCapacityTable();
    $("#capacity-row").closest('.table-container').slideToggle(300);
	clearTimeout( capacity_timeout);
	capacity_timeout = setTimeout(function(){ $(window).trigger('resize'); },400);
};
var timesheetCallback = function() {
    var btn = $(this);
    var active = btn.hasClass("active");
    btn.toggleClass("btn-success active", !active);
    updateTable({
        withTimesheet: !active
    });
};
var teamCallback = function(event) {
    event.stopPropagation();
    var target = $(event.target);
    if (target.is("input")) {
        var selectedTeams = target.parents(".dropdown-options").find("input:checked")
            .map(function() {
                return $(this).val();
            }).get();
        PDCData.teamSelectionChanged();
        updateTable({
            selectedTeams: selectedTeams
        });
    } else if (target.is(".dropdown-link")) {
        $("#team-select-container .checkbox input").attr("checked", false);
        PDCData.teamSelectionChanged();
        updateTable({
            selectedTeams: []
        });
        $("#team-select-container").removeClass("open");
    }
	updateCapacityTable();
};

var exportCallback = function() {
    var type = $(this).data("export");
    PDCData.exportExcel(type);
};


////////////////////////////////////////////////////////////////////
///////////////////////// EDITING CALLBACKS ////////////////////////

var editCellCallback = function(ev) {
    var cell = $(this);
    var input = cell.find("input");
    // xu ly qtip va save workload.
    var task_id = cell.data('task-id');
    var team_id = cell.data('team-id');
    var month = cell.data('month');
    var date = month.split('-');
    var tbody = cell.parent().closest("tbody");
    var prId = tbody[0].__data__.id;
	var a = true;
	var show_popup = PDCData.show_popup;
	//Ticket #1461. Multi resource luon luon open popup.
	var count_emp = 0;
	if (assignWorkloads[team_id][task_id][month] !== undefined) {
		$.each(assignWorkloads[team_id][task_id][month], function(assignID, value) {
			count_emp ++;
		});
	}
    // if (assignWorkloads[team_id][task_id][month] !== undefined && Object.keys(assignWorkloads[team_id][task_id][month]).length > 1) {
    if (assignWorkloads[team_id][task_id][month] !== undefined && (show_popup == 1 || count_emp > 1) ) {
        cell.qtip({
            overwrite: false,
            show: {
                solo: true,
                event: 'click', // Use the same event type as above
                ready: true // Show immediately - important!
            },
            hide: 'unfocus',
            content: {
                text: function(e, api) {
                    //lay du lieu tu assigns
                    var data = assignWorkloads[team_id][task_id][month];
                    var template = $('#tooltip-template').clone().removeClass('hidden').prop('id', ''),
                        item = template.find('.tooltip-workload').clone(),
                        list = template.find('.tooltip-assign');
                    //fill hidden input value
                    template.find('.tooltip-id').val(task_id);
                    template.find('.tooltip-type').val('p');
                    template.find('.tooltip-month').val(date[1] + '-' + date[0]);
                    //empty list
                    list.html('');
                    //append list
                    $.each(data, function(assignID, value) {
                        resourceId = assignID.split('-');
                        var newItem = item.clone();
                        if ( resourceId[0] == '1' ) {
                            newItem.find('.tooltip-name').text(listTeam[resourceId[1]]);
                        } else {
                            newItem.find('.tooltip-name').text(listEmployee[resourceId[1]]);
                        }
                        //workload
                        theInput = newItem.find('.tooltip-input');
                        theInput.prop({
                            name: 'data[workload][' + assignID + ']'
                        }).val(value);
                        //consume
                        try {
                            xconsume = assignConsume[team_id][task_id][month][resourceId[1]];
                        } catch(ex) {
                            xconsume = 0;
                        } finally {
                            xconsume = parseFloat(xconsume);
                        }
                        if( isNaN(xconsume) )xconsume = 0.00;
                        xconsume = xconsume.toFixed(2);
                        newItem.find('.tooltip-consume').text(xconsume);
                        //append to list
                        list.append(newItem);
                    });
                    template.find('.tooltip-input').click(function() {
                        $(this).select();
                    })
                    .off('keydown').on('keydown', function(e) {
                        if ( e.keyCode == 13 || e.keyCode == 9 ) {
                            // find next input
                            $(this).closest('div').next().find('input').focus().select();
                            // if last input, save and find the cell in table.
                            if ( !$(this).closest('div').next().hasClass('tooltip-workload') ) {
                                template.find('.tooltip-form').find('.tooltip-ok').click();
                                cell.closest('td').next().click();
                            }
                            e.preventDefault();
                        }
                    });
                    //click vo cancel thi tat tooltip
                    template.find('.tooltip-cancel').on('click', function() {
                        api.hide();
                    });

                    //ajax save workload here
                    template.find('.tooltip-form')
                        .submit(function(){
                        template.find('button').prop('disabled', true);
                        $(this).ajaxSubmit({
                            dataType:  'json',
                            success: function(xdata) {
                                //update assign
                                assignWorkloads[team_id][task_id][month] = xdata.assign;
                                //funny animation
                                cell.find('span').addClass('updated');
                                cell.animate({
                                    'background-color': '#FFFFA3',
                                }, 400, function(){
                                    $(this).animate({
                                        'background-color': '#fff',
                                    }, 600);
                                });
                                template.find('button').prop('disabled', false);
                                //
                                cell.find("span").text(xdata.total.toFixed(2)).css('color', '#449d44');
                                saveAfterEdit(cell, xdata.total);
                                //run staffing
                                staffing(prId, 'p');
                                //end update
                                api.hide();
                            },
                            error: function(){
                                cell.find('span').css({
                                    color: '#f00'
                                });
                                api.hide();
                            }
                        });
                        return false;
                    });
                    return template;
                },
                title: function(e, api) {
                    var monthAbbr = ["Janv", "Févr", "Mars", "Avr", "Mai", "Juin", "Juill", "Août", "Sept", "Oct", "Nov", "Déc"];
                    var year = date[0];
                    return monthAbbr[date[1]-1] + '-' + year;
                }
            },
            position: {
                my: 'bottom center',
                at: 'top center'
            },
            style: {
                classes: 'qtip-shadow qtip-blue'
                // width: 150,
            }
        });
    } else {
        var txt = cell.find(".edit-text").hide();
        // check k cho update truong hop workload = 0;
        var data = cell.find('.edit-text').text();
        var check = cell.hasClass('can-edit');
        if (data == '' && check === false) return;
        if (input.size() === 0) {
            input = $('<input type="text">');
            cell.append(input);
            input.on("focusout", hideEdit);
            input.on("keydown", editKeystroke);
        } else {
            if (input.is(":visible"))
                return;
            input.show();
        }
        var _val = parseFloat(txt.text());
        if ( isNaN(_val) ) _val = 0;
        input.val(_val);
        input.focus().select();
        if ( input.data('shown') ) {
            return;
        }
        cell.find('span').hide();
        input.data({
                'old-workload': _val,
                'shown': true
            })
            .focus()
            .off('change blur')
            .change(function() {
                //lay du lieu tu assigns
                var data = assignWorkloads[team_id][task_id][month];
                var date = month.split('-');
                input.data('shown', false).prop('disabled', true);
                cell.find('span.edit-text').addClass('pch_loading loading_input');
                var newWorkload = parseFloat(input.val()),
                    oldWorkload = parseFloat(input.data('old-workload'));
                if ( newWorkload != oldWorkload && !isNaN(newWorkload) ) {
                    //ajax
                    wl = {};
                    $.each(data, function(resource, val) {
                        wl[resource] = newWorkload;
                    });
                    $.ajax({
                        url: $('.tooltip-form:first').prop('action'),
                        type: 'post',
                        dataType: 'json',
                        data: {
                            data: {
                                id: task_id,
                                type: 'p',
                                month: date[1] + '-' + date[0],
                                workload: wl
                            }
                        },
                        success: function(xdata) {
                            input.prop('disabled', false).addClass('input-hidden');
                            cell.find('span').show();
                            //todo: update lai workload
                            assignWorkloads[team_id][task_id][month] = xdata.assign;
                            //funny animation
                            setTimeout(function() {
                                cell.find('span.edit-text').removeClass('pch_loading loading_input');
                            }, 300);
                            cell.find('span.edit-text').addClass('updated');
                            cell.animate({
                                'background-color': '#FFFFA3',
                            }, 400, function() {
                                $(this).animate({
                                    'background-color': '#fff',
                                }, 600);
                            });
                            cell.find("span").text(xdata.total.toFixed(2)).css('color', '#449d44');
                            saveAfterEdit(input, xdata.total);
                            //
                            //run staffing
                            staffing(prId, 'p');
                            //end update
                        }
                    });
                } else {
                    cell.find('span').removeClass('pch_loading loading_input');
                    cell.find('span').show();
                }
            })
            .blur(function() {
                if ( input.data('shown') ) {
                    input.data('shown', false);
                    cell.find('span').show();
                }
           });
        ;
    }
};
//todo: run staffing
function staffing(id, type) {
    //call ajax, type = a | p
    var controller = type == 'p' ? 'project_tasks' : 'activity_tasks';
    $.ajax({
        url: '/' + controller + '/staffingWhenUpdateTask/' + id,
        method: 'GET'
    });
}
var hideEdit = function() {
    var input = $(this);
    input.hide();
    var txt = input.parent().find(".edit-text");
    txt.show();
};
var editKeystroke = function(event) {
    var input = $(this);
    if (event.keyCode === 13) {
        // Return key
        saveAfterEdit(input, false);
        input.focusout();
    } else if (event.keyCode === 27) {
        // Esc key
        input.focusout();
    } else if (event.keyCode === 9) {
        // Tab key
        event.preventDefault();
        // Save
        saveAfterEdit(input, false);
        var td = input.closest("td");
        if (event.shiftKey === false) {
            td.next().click();
        } else {
            td.prev().click();
        }
    }
};
var saveAfterEdit = function(input, type) {
    var td = input.closest("td");
    var newVal = parseFloat(input.val());
    if ( type ) {
        newVal = type;
    }
    if (isNaN(newVal) || newVal < 0)
        newVal = 0;
    // var txt = input.parent().find(".edit-text");
    // txt.text(newTxt);
    var newTxt = (newVal !== 0) ? newVal.toFixed(2) : "";
    var tbody = input.parent().closest("tbody");
    // __data__ is the data associated with node by d3.js
    PDCData.saveWorkload(td['0'].__data__, newVal, tbody['0'].__data__);
};
// Update project row after updating totals
var updateProjectRows = function(projects) {
    // Select tbody elements
    var pjIds = projects.map(function(v) {return v.id;});
    var selection = d3.selectAll(".pdc-table tbody").filter(function(pj) { return pjIds.indexOf(pj.id) >= 0; });
    selection.each(function(pj) {
        d3.select(this).selectAll("tr").each(function(task) {
            updateTaskLineTotals(d3.select(this), pj, task);
        });
    });
};
////////////////////////////////////////////////////////////////////
///////////////////// TABLE CREATION AND UPDATE ////////////////////

var createTooltip = function(td, txt, size) {
    td.on("mouseenter", function() {
        var height = (size === undefined || size == "large")?50:28;
        var jtd = $(this);
        var previousTooltip = jtd.data("tooltip");
        if (previousTooltip !== undefined && previousTooltip !== null)
            previousTooltip.remove(); // security
        var offset = jtd.offset();
        var tooltip = $('<div class="td-tooltip" style="height: ' + height + 'px; ' +
                'top: ' + (offset.top - height) + 'px; left: ' + (offset.left - 25) + 'px; ' +
                '">' + txt + '</div>');
        if (size === undefined || size == "large")
            tooltip.addClass("purple-tooltip");
        $("body").append(tooltip);
        jtd.data("tooltip", tooltip);
        td.on("mouseleave", function() {
            tooltip.remove();
            jtd.data("tooltip", null);
        });
    });
};

// Timesheet lines coloring
// Color scale when timesheet < workload
var greenScale = d3.scaleLinear().clamp(true)
    .domain([0, 5])
    .range([d3.hsl(138, 1.0, 0.65, 0.2), d3.hsl(138, 1.0, 0.65, 1.0)]);
// Color scale when workload < timesheet
var redScale = d3.scaleLinear().clamp(true)
    .domain([0, 5])
    .range([d3.hsl(0, 1.0, 0.65, 0.2), d3.hsl(0, 1.0, 0.65, 1.0)]);

var createProgressTd = function(td, timesheet, workload, precision, consumedAll) {
    td.select("div").remove();
    var width = 0.0;
    var color = "white";
    if (workload > 0) {
        if (consumedAll <= workload) {
            width = 1.0 - consumedAll / workload;
            color = greenScale(workload - consumedAll);
        } else {
            width = (consumedAll < 2 * workload) ? (consumedAll / workload - 1.0) : 1.0;
            color = redScale(consumedAll - workload);
        }
    } else {
        if(consumedAll > 0){
            width = 1.0;
            color = redScale(consumedAll);
        }
    }
    var backDiv = '<div class="conso-back-div" style="width: ' + (width * 100).toFixed(0) +
        '%; height: 100%; background-color: ' + color + ';"></div>';
    var textDiv = '<span class="conso-back-text">' + consumedAll.toFixed(precision) + '</span>';
    td.html('<div style="position: relative;">' + backDiv + textDiv + "</div>");
    var tooltipTxt = "Charge : " + workload.toFixed(precision) + '<br/>' + "Ecart : " +
        (consumedAll - workload).toFixed(precision);
    createTooltip(td, tooltipTxt);
};

// Create task line
var createTaskLine = function(dtr, pj, task) {
    // dtr.append("td").html('<span data-id="'+pj.id+'" class="glyphicon glyphicon-list"></span>')
    //     .classed("comment-td common", true).on("click", commentCallback);
    dtr.append("td").text(pj.commitee).classed("commitee-td common " + ( state.show_program ? '' : 'd3-hidden' ), true);
    dtr.append("td").text(pj.version).classed("version-td common " + ( state.show_sub_program ? '' : 'd3-hidden' ), true);
	var project_task_screen_link = '/project_tasks/index/' + pj.id;
    var ht = '<span onclick=\'commentCallback('+pj.id+');\' style="margin-left:5px; margin-right: 5px" data-id="'+pj.id+'" class="glyphicon glyphicon-list"></span><a href="' + project_task_screen_link + '" target="_blank"><span>' + pj.name + '</span></a>';
    dtr.append("td").html(ht).classed("name-td common", true);
    dtr.append("td").text(pj.priority).classed("priority-td common " + ( state.show_priority ? '' : 'd3-hidden' ), true);
    dtr.append("td").classed("total-td totals-td common " + ( state.show_total ? '' : 'd3-hidden' ), true);
    dtr.append("td").classed("team-total-td " + ( state.show_total ? '' : 'd3-hidden' ), true);
    dtr.append("td").text(task.team).classed("team-td", true);

    if (task.type == "TT") {
        // timesheet total line
        dtr.append("td").text("TOTAL CONSOMMES").classed("task-td conso-td", true);
        dtr.append("td").text("").classed("workload-td conso-td ref-td " + ( state.show_ref ? '' : 'd3-hidden' ), true);
        dtr.insert("td").classed("workload-td conso-td total-conso-td", true);
        for (var k = 0; k < task.timesheet.length; k++) {
            dtr.insert("td").classed("workload-td conso-td month-conso-td", true);
        }
        dtr.append("td").classed("task-comment-td conso-td", true);

    } else {
        // Standard task line
		var project_task_link = project_task_screen_link + '?id=' + task.id;
        // var ht = '<span onclick=\'taskCommentCallback('+task.id+');\' style="margin-left:5px; margin-right: 5px" class="glyphicon glyphicon-comment"></span><a href="' + project_task_link + '" class="task-link-popup" target="_blank">' + task.phase + " - " + task.name + '</a>';
        var ht = '<span onclick=\'taskCommentCallback('+task.id+');\' style="margin-left:5px; margin-right: 5px" class="glyphicon glyphicon-comment"></span><a href="javascript:void(0);" class="task-link-popup" onclick="openNCTask(' + task.id + ', ' + pj.id + ')">' + task.phase + " - " + task.name + '</a>';
        var tCls = 'task-td task-id-' + task.id;
        dtr.append("td").html(ht).classed(tCls, true);
        dtr.append("td").text(task.reference.toFixed(2)).classed("workload-td task-reference " + ( state.show_ref ? '' : 'd3-hidden' ), true);
        var totalTd = dtr.append("td").classed("workload-td task-total-td", true);
        createTooltip(totalTd, i18n.Consume + " : " + task.totalTimesheet.toFixed(2), "small");
        var workload = task.workload,
            timesheet = task.timesheet;
        for (var k = 0; k < workload.length; k++) {
            var td = dtr.append("td").classed("workload-td", true).attr("data-task-id", task.id).attr("data-month", _period[k]).attr("data-team-id", task.teamId);
            // cho phep sua nhung cell nam trong start date end date cua task.
            var sDate = task.startDate;
            var eDate = task.endDate;
            sDate = sDate.replace(/-/g, '');
            eDate = eDate.replace(/-/g, '');
            var dDate = _period[k].replace(/-/g, '') + '01';
            if((dDate <=  eDate) && (dDate >= sDate)){
                td.classed("can-edit", true);
            }
            td.append("span").classed("edit-text", true)
                .text((workload[k] !== 0.0) ? workload[k].toFixed(2) : "");
            if (timesheet[k] > 0)
                createTooltip(td, i18n.Consume + " : " + timesheet[k].toFixed(2), "small");
            var id = task.wIds[k];
            if (id !== undefined & id !== null) {
                td.classed("editable", true).datum({task: task, index: k, id: id});
            }
        }
        dtr.append("td").classed("task-comment-td", true).append("span")
            .classed("glyphicon glyphicon-comment", true).on("click", taskCommentCallback);
    }

    updateTaskLineTotals(dtr, pj, task);
    dtr.selectAll(".editable").on("click", editCellCallback);
};

// Update task line totals : called to update project after user editing
var updateTaskLineTotals = function(dtr, pj, task) {
    dtr.select(".total-td").text(pj.totalWorkload.toFixed(2));
    dtr.select(".team-total-td").text(pj.workloadByTeam[task.team].total.toFixed(2));
    if (task.type == "TT") {
        var totalTd = dtr.select(".total-conso-td");
        var _totalC = _totalConsumeOfPj[task.team] !== undefined && _totalConsumeOfPj[task.team][pj.id] !== undefined && _totalConsumeOfPj[task.team][pj.id]['total'] !== undefined ? _totalConsumeOfPj[task.team][pj.id]['total']  : 0;
        createProgressTd(totalTd, task.totalTimesheet, pj.workloadByTeam[task.team].total, 2, _totalC);
        var teamWorkload = pj.workloadByTeam[task.team];
        dtr.selectAll(".month-conso-td").each(function(_,k) {
            var _aConsume = _totalConsumeOfPj[task.team] !== undefined && _totalConsumeOfPj[task.team][pj.id] !== undefined && _totalConsumeOfPj[task.team][pj.id][k] !== undefined ? _totalConsumeOfPj[task.team][pj.id][k] : 0;
            createProgressTd(d3.select(this), task.timesheet[k], teamWorkload.months[k], 2,  _aConsume);
        });
    } else {
        totalTd = dtr.select(".task-total-td").text(task.totalWorkload.toFixed(2));
        // totalTd = dtr.select(".task-reference").text(task.totalWorkload.toFixed(2));
    }
};


var updateTable = function(options) {
    options = Object.assign({}, state, options);
    var numShowLine = 0;
    // selected teams
    var selectedTeams = getSelectedTeams(options);

    var projectList = PDCData.projects;
	
    // Filter project list by search criteria
    if (options.search !== undefined && options.search.length > 0) {
        var search = removeDiacritics(options.search).toLowerCase();
        search = search.split(/\s+/);
        search = search.filter(function(s) {
            return s.length >= 3;
        });
        projectList = projectList.filter(function(pj) {
            if (pj.haystack === undefined) {
                pj.haystack = removeDiacritics(pj.commitee + " " + pj.version + " " +
                    pj.id + " " + pj.name).toLowerCase();
            }
            return search.every(function(needle) {
                return pj.haystack.indexOf(needle) >= 0;
            });
        });
    }
	
    // Sort project list
    var field = options.sort;
    var sign = options.reversed ? -1 : 1;
    var selectedMonth = null;
    var workloadForTeams = null;
    var teamWorkload = false;
    if (field.startsWith("M")) {
        selectedMonth = parseInt(field.substring(1));
        sign = -sign;
        workloadForTeams = PDCData.getProjectWorkloadForSelectedTeams(selectedTeams);
    } else if (field == "teamWorkload") {
        teamWorkload = true;
        sign = -sign;
        workloadForTeams = PDCData.getProjectWorkloadForSelectedTeams(selectedTeams);
    } else if (field == "totalWorkload") {
        sign = -sign;
    }
    projectList = projectList.sort(function(p1, p2) {
        var f1, f2, diff;
        if (selectedMonth !== null) {
            f1 = (options.shifted === false)?(workloadForTeams.get(p1.id).months[selectedMonth]):Math.abs(workloadForTeams.get(p1.id).diff[selectedMonth]);
            f2 = (options.shifted === false)?(workloadForTeams.get(p2.id).months[selectedMonth]):Math.abs(workloadForTeams.get(p2.id).diff[selectedMonth]);
            diff = f1 - f2;
        } else if (teamWorkload === true) {
            f1 = (options.shifted === false)?(workloadForTeams.get(p1.id).total):Math.abs(workloadForTeams.get(p1.id).totalDiff);
            f2 = (options.shifted === false)?(workloadForTeams.get(p2.id).total):Math.abs(workloadForTeams.get(p2.id).totalDiff);
            diff = f1 - f2;
        } else {
            f1 = p1[field];
            f2 = p2[field];
            diff = (typeof(f1) === "string")?f1.localeCompare(f2):(f1-f2);
        }

        if (diff < 0 || diff > 0) {
            return sign*diff;
        }

        if (diff === 0) {
            return p1.name.localeCompare(p2.name);
        }
    });
	
	
    // Create a set of lines for a single project
    var createProjectRow = function(d) {
        var parent = document.createElement("tbody");
        parent.className += "project-row project-"+d.id;

        // Filter tasks according to selected teams
        var tasks = d.tasks;
        var nLines = tasks.length;
        if (nLines === 0) {
            var tr = document.createElement("tr");
            parent.appendChild(tr);
            return parent;
        }
        for (var i = 0; i < nLines; i++) {
            var task = tasks[i];
            var tr = document.createElement("tr");
            var dtr = d3.select(tr);
            dtr.datum(task);
            createTaskLine(dtr, d, task);
            parent.appendChild(tr);
            // Insert total line
            if (i == nLines - 1 || tasks[i + 1].team != task.team) {
                var teamTotal = {
                    team: task.team,
                    type: "TT",
                    reference: 0.0,
                    totalTimesheet: d.timesheetByTeam[task.team].total,
                    timesheet: d.timesheetByTeam[task.team].months,
                };
                tr = document.createElement("tr");
                dtr = d3.select(tr);
                dtr.datum(teamTotal);
                createTaskLine(dtr, d, teamTotal);
                parent.appendChild(tr);
            }
        }
        return parent;
    };
	
    // Method to show or hide task lines, and adjust rowspan
    var styleProject = function(d) {
        var row = d3.select(this);
        var lines = row.selectAll("tr");

        var showLine = function(data) {
            if (selectedTeams.has(data.team) === false)
                return false;
            if (data.type == "TT" && options.withTimesheet === false)
                return false;
            return true;
        };

        // First loop : hide lines according to options, and calculate team grouping
        var groups = {
            start: -1,
            nLines: 0
        };
        lines.each(function(t, i) {
            var line = d3.select(this);
            if (showLine(t)) {
                line.style("display", "table-row");
                if (groups.start < 0)
                    groups.start = i;
                groups.nLines++;
                if (groups[t.team] === undefined)
                    groups[t.team] = {
                        start: i,
                        nLines: 0
                    };
                groups[t.team].nLines++;
            numShowLine ++;
            } else {
                line.style("display", "none");
            }
        });

        if (groups.nLines === 0) {
            // If no line to show, remove tbody
            row.remove();
        }

        // Second loop : group rows by team (adjust rowspan)
        lines.each(function(t, i) {
            var line = d3.select(this);
            var thisGroup = groups[t.team];
            if (showLine(t)) {
                if (i === groups.start) {
                    line.selectAll("td.common").attr("rowspan", groups.nLines)
                        .style("display", "table-cell");
                } else {
                    line.selectAll("td.common").style("display", "none");
                }
                if (thisGroup !== undefined && i === thisGroup.start) {
                    line.selectAll(".team-td,.team-total-td").attr("rowspan", thisGroup.nLines)
                        .style("display", "table-cell");
                } else {
                    line.selectAll(".team-td,.team-total-td").style("display", "none");
                }
            }
        });
    };
	
    var displayLines = function(projectList, nLines) {
        // Select existing project rows
        var selection = d3.select(".pdc-table").selectAll("tbody.project-row")
            .data(projectList.slice(0, nLines), function(d) { return d.id; });
        // Remove extra rows
        selection.exit().remove();
        // Add missing rows, then update existing
        selection.enter().append(createProjectRow)
            .merge(selection).order().each(styleProject);
        setupScroll();
		update_check_point();
    };
	
	// Display following lines on scrolling
    var $window = $("#scrollLeftAbsence");
	var check_point = 0;
    var nDisplayedLines = 10;	
	function update_check_point(){
		var max_scroll = $window.children().first().height() - $window.height() - 40; // scroll bar tren 20 duoi 20
		check_point = parseInt( Math.max( max_scroll * 0.7 , max_scroll - 300));
	}
	update_check_point();
	
    // Start by displaying only first 10 lines
    displayLines(projectList, nDisplayedLines);
    state = options;
    var shouldDisplayMoreLines = function() {
		return ((nDisplayedLines < projectList.length) && ( $('#scrollLeftAbsence').scrollTop() >= check_point));
    };
	
    $window.unbind("scroll").bind("scroll", function() {
        if (shouldDisplayMoreLines()) {
            nDisplayedLines += 10;
            displayLines(projectList, nDisplayedLines);
        }
        if ((nDisplayedLines <= 10) && (nDisplayedLines < projectList.length)) {
            nDisplayedLines += 10;
            displayLines(projectList, nDisplayedLines);
        }
        $('#scrollLeftAbsence').on('scroll', function(e) {
            var amount = $('#scrollLeftAbsence').scrollTop();
            $('#right-scroll').scrollTop(amount);
        });
        scrollHeader();
    });
	$(window).ready(function(){
		while (shouldDisplayMoreLines() && (nDisplayedLines < projectList.length)) {
			// Just in case 10 lines don't fill the screen (scroll is not emitted in this case)
			$window.trigger("scroll");
		}
	});
	
    while (numShowLine < 40 && nDisplayedLines < projectList.length){
        nDisplayedLines += 10;
        displayLines(projectList, nDisplayedLines);
    }
};


var updateCapacityTable = function() {
    var tbody = d3.select("#capacity-row");
    tbody.selectAll("tr").remove();

    PDCData.updateCapacity();
    var selectedTeams = getSelectedTeams(state);

    for (var i = 0; i < PDCData.teams.length; i++) {
        var name = PDCData.teams[i];
        if (selectedTeams.has(name)) {
            var teamCap = PDCData.capacity[name];
            var cap = teamCap.resources;
            var workload = teamCap.workload;
            var csOfCapa = consumeCapa[name];
            // Capacity line
            var tr1 = tbody.append("tr");
            tr1.append("td").classed("team-capacity-td", true).attr("rowspan", 3).text(name);
            tr1.append("td").classed("capacity-text-td", true).text("Capacité");
            tr1.append("td").classed("workload-td", true).datum(teamCap.totalResources);
            // Workload line
            var tr2 = tbody.append("tr");
            tr2.append("td").classed("capacity-text-td", true).text("Charge");
            var td2 = tr2.append("td").classed("workload-td", true).datum(teamCap.totalWorkload);
            createTooltip(td2, i18n.Consume + " : " + csOfCapa['total'].toFixed(2), "small");
            // Difference line
            var tr3 = tbody.append("tr");
            tr3.append("td").classed("capacity-text-td", true).text("Ecart");
            tr3.append("td").classed("workload-td diff-td total-diff-td", true)
                .datum(teamCap.totalWorkload - teamCap.totalResources);
            for (var j = 0; j < workload.length; j++) {
                tr1.append("td").classed("workload-td", true).datum(cap[j]);
                td2 = tr2.append("td").classed("workload-td", true).datum(workload[j]);
                createTooltip(td2, i18n.Consume + " : " + csOfCapa[j].toFixed(2), "small");
                tr3.append("td").classed("workload-td diff-td", true).datum(workload[j] - cap[j]);
            }
        }
    }

    // Print data and style cells with background color
    tbody.selectAll(".workload-td").text(function(d) {
        if( d != undefined)
			return d.toFixed(2);
		return '';
    });
    tbody.selectAll(".diff-td").style("background-color", function(d) {
        var cell = d3.select(this);
        var max = cell.classed("total-diff-td") ? 60 : 20;
        var hue = (d < 0) ? 138 : 0;
        var ad = (d > 0) ? d : -d;
        var lum = (ad > max) ? 0.65 : (1.0 - ad * (1.0 - 0.65) / max);
        if (hue === 0 && lum < 0.7)
            d3.select(this).style("color", "white");
        return d3.hsl(hue, 1.0, lum);
    });
};

initTable = function() {
    // Generate list of teams in menu bar
	
	state = Object.assign({}, state, _show_in_config);
    var teams = PDCData.teams.map(function(t, i) {
        return $('<div class="checkbox"><label><input type="checkbox" value="' + i + '">' +
            t + '</label></div>').on("click", teamCallback);
    });
    $("#team-select-container .dropdown-options").append(teams);
    $("#team-select-container .dropdown-link").on("click", teamCallback);

    // Generate month columns
    var monthAbbr = ["Janv", "Févr", "Mars", "Avr", "Mai", "Juin", "Juill", "Août", "Sept", "Oct", "Nov", "Déc"];
    var months = PDCData.period.map(function(m, i) {
        var year = m.substring(0,4);
        var month = parseInt(m.substring(5,7));
        var th = $('<th class="workload-td month-td" data-toggle="tooltip" data-placement="left"><span class="sortable" data-sort="M' + i +'">' +
            monthAbbr[month-1] + '<br/>' + year + '</span></th>');
        return th;
    });
    $(".pdc-header-table th.task-comment-td, .capacity-table th.task-comment-td").before(months);
    // Set tables and table containers to fixed width (works best with margins and scrolling)
    var width = $('.pdc-header-table').width();
    $('.pdc-header-table').css("width", width + "px");
    $('.pdc-container').css("width", (width) + "px");
    $('.pdc-table').css("width", width + "px");

    // Set callbacks
    // $(".pdc-table .sortable").on("click", sortCallback);
    $(".pdc-header-table .sortable").on("click", sortCallback);
   
    $("#search-text").on("input", searchCallback);
    $(".search-container .close-btn").on("click", emptySearchCallback);
    $("#timesheet-btn").on("click", timesheetCallback);
    $("#capacity-btn").on("click", capacityCallback);
    $("#export-container .dropdown-link").on("click", exportCallback);

    $('[data-toggle="tooltip"]').tooltip({delay: {'show': 700, 'hide': 0}, html: true});

    updateTable();
};

var scrollHeader = function() {
    var left = $(window).scrollLeft();
    $('.header-container').css("left", (-left) + "px");
};

/**
 * Remove diacritics (accents) from a string
 * @param {string} str The input string from which we will remove strings with diacritics
 * @returns {string}
 * @see http://goo.gl/zCBxkM
 */
function removeDiacritics(str) {
    var diacriticsMap = {
        A: /[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g,
        AA: /[\uA732]/g,
        AE: /[\u00C6\u01FC\u01E2]/g,
        AO: /[\uA734]/g,
        AU: /[\uA736]/g,
        AV: /[\uA738\uA73A]/g,
        AY: /[\uA73C]/g,
        B: /[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g,
        C: /[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g,
        D: /[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g,
        DZ: /[\u01F1\u01C4]/g,
        Dz: /[\u01F2\u01C5]/g,
        E: /[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g,
        F: /[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g,
        G: /[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g,
        H: /[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g,
        I: /[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g,
        J: /[\u004A\u24BF\uFF2A\u0134\u0248]/g,
        K: /[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g,
        L: /[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g,
        LJ: /[\u01C7]/g,
        Lj: /[\u01C8]/g,
        M: /[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g,
        N: /[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g,
        NJ: /[\u01CA]/g,
        Nj: /[\u01CB]/g,
        O: /[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g,
        OI: /[\u01A2]/g,
        OO: /[\uA74E]/g,
        OU: /[\u0222]/g,
        P: /[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g,
        Q: /[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g,
        R: /[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g,
        S: /[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g,
        T: /[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g,
        TZ: /[\uA728]/g,
        U: /[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g,
        V: /[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g,
        VY: /[\uA760]/g,
        W: /[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g,
        X: /[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g,
        Y: /[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g,
        Z: /[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g,
        a: /[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g,
        aa: /[\uA733]/g,
        ae: /[\u00E6\u01FD\u01E3]/g,
        ao: /[\uA735]/g,
        au: /[\uA737]/g,
        av: /[\uA739\uA73B]/g,
        ay: /[\uA73D]/g,
        b: /[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g,
        c: /[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g,
        d: /[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g,
        dz: /[\u01F3\u01C6]/g,
        e: /[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g,
        f: /[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g,
        g: /[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g,
        h: /[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g,
        hv: /[\u0195]/g,
        i: /[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g,
        j: /[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g,
        k: /[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g,
        l: /[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g,
        lj: /[\u01C9]/g,
        m: /[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g,
        n: /[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g,
        nj: /[\u01CC]/g,
        o: /[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g,
        oi: /[\u01A3]/g,
        ou: /[\u0223]/g,
        oo: /[\uA74F]/g,
        p: /[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g,
        q: /[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g,
        r: /[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g,
        s: /[\u0073\u24E2\uFF53\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g,
        ss: /[\u00DF]/g,
        t: /[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g,
        tz: /[\uA729]/g,
        u: /[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g,
        v: /[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g,
        vy: /[\uA761]/g,
        w: /[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g,
        x: /[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g,
        y: /[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g,
        z: /[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g
    };
    for (var x in diacriticsMap) {
        // Iterate through each keys in the above object and perform a replace
        str = str.replace(diacriticsMap[x], x);
    }
    return str;
}