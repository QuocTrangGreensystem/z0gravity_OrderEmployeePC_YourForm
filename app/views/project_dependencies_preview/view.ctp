<?php
echo $this->Html->css('projects');
echo $this->Html->css('vis.min');
echo $this->Html->css('preview/project_dependency.css?ver=1.1');
echo $this->Html->script('vis.min');
?>
<style>
    .row-number{
        float: right;
    }
    .row-center-custom{
        text-align: center;
    }
    .row-date{
        text-align: center;
    }
    .color {
        display: inline-block;
        width: 15px;
        height: 15px;
        border: 1px solid #ddd;
        vertical-align: middle;
    }
    #open-diagram {
        background: url(/img/icon-diagram.png) no-repeat;
        display: inline-block;
        width: 32px;
        height: 32px;
        float: right;
        text-decoration: none;
    }
    #diagram {
        width: 100%;
        height: 650px;
        position: relative;
    }
    .wd-title {
        position: relative;
    }
    #dependency-info {
        position: absolute;
        left: 0;
        bottom: 0;
        min-width: 200px;
        max-width: 300px;
        min-height: 80px;
        background: #fff;
        z-index: 999999;
        border: 1px solid #ddd;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        padding: 10px;
        display: none;
    }
    #dependency-info dl {
        overflow: hidden;
        clear: both;
        margin-bottom: 5px;
    }
    #dependency-info dt {
        display: inline-block;
        width: 30px;
        height: 8px;
        margin-right: 10px;
        vertical-align: middle;
    }
    #dependency-info dd {
        display: inline-block;
        vertical-align: middle;
    }
    #wd-container-footer{
        display: none;
    }
    body{
        overflow: hidden;
    }
</style>
<div id="wd-container-main" class="wd-project-admin">
    <div class="wd-layout">
        <div class="wd-main-content">
            <div class="wd-list-project">
                <div id="diagram">
                    <div class="vis-network" tabindex="900" style="position: relative; overflow: hidden; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%;">
                        <canvas style="position: relative; touch-action: none; -webkit-user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--
<div class="popup-template">
	<div class="popup popup-%PROJECT_ID%" style="display:none;">   
		<div class="project-image">
			<img src="%GLOBAL_VIEW%" alt="%PROJECT_NAME%">
		</div>
		<div class="dropdown-menu">
			<ul>
				<li><a href="javascript: void(0);" onclick="expand(%PROJECT_ID%)"> <?php __('Expand'); ?></a></li>
				<li><a href="javascript: void(0);"> <?php __('Action secondaire'); ?></a></li>
				<li><a href="javascript: void(0);"> <?php __('Action supp.'); ?></a></li>
			</ul>
		</div>
	</div>
</div>
-->
<div class="menu-template">
	<div class="menu-template-inner">
		
	</div>
</div>

<script type="text/javascript">
var wdTable = $('#diagram');
var heightTable = $(window).height() - wdTable.offset().top - 40;
//heightTable = (heightTable < 550) ? 550 : heightTable;
wdTable.css({
    height: heightTable,
});
$(window).resize(function(){
    heightTable = $(window).height() - wdTable.offset().top - 40;
    //heightTable = (heightTable < 550) ? 550 : heightTable;
    wdTable.css({
        height: heightTable,
    });
});
var toggling = false,
    open = '<?php echo $this->Html->url('/img/icon-plus.png') ?>',
    close = '<?php echo $this->Html->url('/img/icon-minus.png') ?>',
    _history = <?php echo empty($history) ? '{}' : json_encode($history) ?>,
    historyData = new $.z0.data(_history),
    data = <?php echo json_encode($data) ?>,
    projects = <?php echo json_encode($projects) ?>,
    globalViews = <?php echo json_encode($globalViews) ?>,
    dependencies = <?php echo json_encode($dependencies) ?>,
    project = <?php echo json_encode($projectName['Project']) ?>,
    colors = <?php echo json_encode($colors) ?>,
    list = <?php echo json_encode(array_unique($list)) ?>,
    cp = '<?php echo $project_id ?>',
    count = <?php echo json_encode($count) ?>,
    nodes,
    links,
    linkTracks = {},
    pTracks = {},
    diagram;
    pTracks[cp] = 1,
	global_default = <?php echo json_encode($this->Html->url('/img/project_preview_default2x.png')) ?>,
	global_src = <?php echo json_encode($this->Html->url(array(
		'controller' => 'project_global_views_preview',
		'action' => 'attachment',
		'%PROJECT_ID%',
		'?' => array('sid' => $api_key)), 
		true)) ?>;
var	popupTemplate = '<div class="popup popup-%PROJECT_ID%" style="display:none;"><div class="project-image"><img src="%GLOBAL_VIEW%" alt="%PROJECT_NAME%"></div><div class="dropdown-menu"><ul><li><a href="javascript: void(0);" onclick="expand(%PROJECT_ID%)"><?php echo __('Expand', true);?></a></li><li><a href="javascript: void(0);"> Action secondaire</a></li><li><a href="javascript: void(0);"> Action supp.</a></li></ul></div></div>';
function globalViewsSrc(id){
    if( globalViews[id] && globalViews[id]['isImage'])
		return global_src.replace('%PROJECT_ID%', id);
	return global_default;
}
function trackProject(id){
    if( typeof pTracks[id] == 'undefined' )pTracks[id] = 1;
}

function hasLink(i, d){
    var id = i + '-' + d;
    return typeof linkTracks[id] != 'undefined';
}

function trackLink(i, d){
    var id = i + '-' + d;
    if( !hasLink(i, d) )linkTracks[id] = 1;
}

function breakByHalf(text){
    if( text !== undefined){
        var arrofwords = text.split(" ");
        var middle = arrofwords.length / 2;
        arrofwords.splice(middle, 0, "\n");
        return arrofwords.join(" ");
    }
    return '';
    return 'Project is deleted';
}

function attachButton(id){
    if( !diagram.findNode('btn-' + id)[0] ){
        nodes.add({
            id: 'btn-' + id,
            group: 'button',
            size: 10
        });
    }
    repositionButton(id);
}

function detachButton(id){
    if( diagram.findNode('btn-' + id)[0] ){
        nodes.update({id: 'btn-' + id, hidden: true});
    }
}

function repositionButton(id){
    //reposition
    try {
        var node = diagram.findNode(id)[0];
        var btnNode = diagram.findNode('btn-' + id)[0];
        if( btnNode ){
            var x = node.x + node.shape.width/2 + 10;
            diagram.moveNode('btn-' + id, x, node.y);
        }
    } catch(ex){}
}

function updateButtons(){
    nodes.forEach(function(node){
        if( node && node.id.indexOf('btn') == -1 ){
            var Node = diagram.findNode(node.id)[0];
            var currentLinks = Node.edges.length, totalLinks = count[node.id];
            if( currentLinks < totalLinks ){
                attachButton(node.id);
            } else {
                detachButton(node.id);
            }
        }
    });
}

function expand(id){
    if( toggling )return;
    jQuery.getJSON('<?php echo $this->Html->url('/project_dependencies_preview/expand/') ?>' + id, function(response){
        //do add node/links
        process(response.data, function(){
            //update links count for additional projects
            for(var i in response.count){
                count[i] = response.count[i];
            }
            diagram.setData({nodes: nodes, edges: links});
            updateButtons();
        });
        toggling = false;
    });
}

function process(rawData, afterRender){
    // var pos = $.z0Cookie.get('dependency_' + cp, {});
    var pos = _history['dependency_' + cp];
    for(var i in rawData){
        var node = rawData[i].ProjectDependency;
        if( typeof pTracks[ node.target_id ] == 'undefined' ){
            var newNode = {
                id: node.target_id,
                label: breakByHalf(projects[node.target_id]),
                group: 'project'
            };
            if( typeof pos != 'undefined' && typeof pos[node.target_id] != 'undefined' ){
                newNode.x = pos[node.target_id].x;
                newNode.y = pos[node.target_id].y;
            }
            nodes.add(newNode);
            trackProject(node.target_id);
        }
        //make links
        var dataLinks = $.parseJSON(node.dependency_ids);
        for(var j in dataLinks){
            var linkId = dataLinks[j];
            var arrow = {};
            if( node.value == 3){ // left and right
                arrow = {
                    to: 0,
                    from: 0
                };
            } else if (node.value == 2){ // right
                arrow = {
                    from: 0
                };
            }else if(node.value == 1){ // left
                arrow = {
                    to: 0
                };
            }
            if( !hasLink(node.grouper, linkId) ){
                links.add({
                    id: node.grouper + linkId,
                    from: node.project_id,
                    to: node.target_id,
                    label: dependencies[linkId],
                    title: dependencies[linkId],
                    color: colors[linkId],
                    arrows: arrow,
                    font: {
                        face: "Open Sans",
                        color: colors[linkId],
                        align: 'horizontal',
                        size: '12'
                    },
                    selectionWidth: 0
                });
                trackLink(node.grouper, linkId);
            }
        }
    }
    if( typeof afterRender == 'function' ){
        afterRender();
    }
}
var curent_node;
function init(){
    //add nodes and links
    nodes = new vis.DataSet();
    links = new vis.DataSet();
    nodes.add({
        id: cp,
        label: breakByHalf(project.project_name),
        group: 'main'
    });
    process(data);
    //finalizing...
	var font_option = {
		face: "Open Sans",
		size: '14',
		color: '#242424',
		// color: '#ff0000',
		bold: {
			color: '#fff',
		}
	};
    var options = {
        interaction: {
            selectConnectedEdges: false
        },
        groups: {
            main: {
                shape: 'box',
                physics: false,
                color: '#242424',
                borderWidth: 1,
                labelHighlightBold: false,
                color: {
                    background: '#ffffff',
                    border: '#E1E6E8',
                    highlight: {
                        background: 'rgb(210, 229, 255)',
                        border: 'transparent',
                    }

                },
                font: font_option,
                shadow: {
                    enabled: true,
                    size: 2,
                    x: 1,
                    y: 1
                }
            },
            project: {
                shape: 'box',
                physics: false,
                value: 3,
                color: '#242424',
                labelHighlightBold: false,
                borderWidth: 1,
                color: {
                    background: '#ffffff',
                    border: '#E1E6E8',
                    highlight: {
                        background: 'rgb(210, 229, 255)',
                        border: 'transparent',
                    }

                },
                font: font_option,
                shadow: {
                    enabled: true,
                    size: 2,
                    x: 1,
                    y: 1
                }
            },
            button: {
                image: open,
                borderWidth: 1,
                shape: 'circularImage',
                size: 16,
                physics: false,
                title: '<?php __('Expand') ?>',
                color: {
                    border: '#006da9',
                    background: '#fff'
                }
            }
        },
        layout: {
            randomSeed: 915113
            // improvedLayout: true
        }
    };
    var dataset = {
        nodes: nodes,
        edges: links
    };
    //console.log( dataset);
    diagram = new vis.Network(document.getElementById('diagram'), dataset, options);
    updateButtons();
    //attach events
    diagram.on('dragEnd', function(params){
        var node = params.nodes[0];
        if( node && node.indexOf('btn-') != -1 ){
            //reposition the btn
            repositionButton(node.replace('btn-', ''));
        }
        savePosition();
    });
    diagram.on('dragging', function(params){
        var node = params.nodes[0];
        if( node && node.indexOf('btn-') == -1 ){
            //reposition the btn
            repositionButton(node);
        }
    });
    diagram.on('click', function(params){
        var node = params.nodes[0];
        if(node != curent_node){
            curent_node = node;
            var p_id = node;
            if( node && node.indexOf('btn-') != -1 ){
                p_id = node.replace('btn-', '');
				expand(p_id);
            }
            var templ = $('.menu-template .menu-template-inner');
            var popups = popupTemplate.replaceArray([/%PROJECT_ID%/g, /%PROJECT_NAME%/g, /%GLOBAL_VIEW%/g], [p_id, projects[p_id], globalViewsSrc(p_id)]);
            if(node){            
                templ.empty().html(popups);
                templ.children().slideDown(); // show
            }else{
                templ.children().slideUp(); // hide
            }
        }

    });
    // diagram.on("afterDrawing", function (ctx) {
    //         // console.log(nodes._data, nodes._data + 1);
    //     var arr = Object.values(nodes._data);
    //         // console.log(typeof arr, arr);
    //     for( var i in arr){ 

    //         //console.log(arr[i]);
    //         var nodeId = arr[i]['id'];

    //         var nodePosition = diagram.getPositions([nodeId]);
    //         ctx.strokeStyle = '#E1E6E8';
    //         ctx.fillStyle = '#fff';
    //         console.log(nodePosition[nodeId]);
    //         var _box = diagram.getBoundingBox(nodeId);
    //         var _height = _box.bottom - _box.top;
    //         var _width  = _box.right - _box.left;
    //         ctx.circle(nodePosition[nodeId].x  - _width/2, nodePosition[nodeId].y + _height/2 ,15);
    //         ctx.fill();
    //         ctx.stroke();
    //     }
        
    // });
    jQuery('#diagram').append('<div id="dependency-info"></div>');
    jQuery('#diagram').append($('.menu-template'));
    jQuery.each(dependencies, function(i, v){
        jQuery('#dependency-info').append('<dl><dt style="background-color: ' + colors[i] + '"></dt><dd>' + v + '</dd></dl>');
    });
}
function savePosition(){
    historyData.set('dependency_' + cp, diagram.getPositions(nodes.getIds()));
    var saveTimer;
    clearTimeout(saveTimer);
    saveTimer = setTimeout(function(){
        $.z0.History.save('dependency_' + cp, historyData);
    }, 1000);
    // $.z0Cookie.set('dependency_' + cp, diagram.getPositions(nodes.getIds()));
    // $.z0Cookie.save();
}
jQuery(document).ready(init);
</script>
