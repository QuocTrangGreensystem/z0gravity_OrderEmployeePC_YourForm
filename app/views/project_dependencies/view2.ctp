<?php echo $this->Html->css('projects'); ?>
<?php echo $this->Html->script('go') ?>
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
		min-height: 500px;
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
	#hide-me {
		width: 200px;
		height: 200px;
		background: #Fff;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 999999;
	}
</style>

<div id="wd-container-main" class="wd-project-admin">
	<?php echo $this->element("project_top_menu") ?>
	<div class="wd-layout">
		<div class="wd-main-content">
			<div class="wd-list-project">
				<div class="wd-title">
					<a href="<?php echo $this->Html->url('/project_dependencies/index/' . $projectName['Project']['id']) ?>" class="wd-add-project"><span><?php __('Back') ?></span></a>
					<h2 class="wd-t1"><?php echo $projectName['Project']['project_name'] ?></h2>
				</div>
				<div id="diagram"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var toggling = false,
	open = '<?php echo $this->Html->url('/img/icon-plus.png') ?>',
	close = '<?php echo $this->Html->url('/img/icon-minus.png') ?>';

var data = <?php echo json_encode($data) ?>,
	projects = <?php echo json_encode($projects) ?>,
	dependencies = <?php echo json_encode($dependencies) ?>,
	project = <?php echo json_encode($projectName['Project']) ?>,
	colors = <?php echo json_encode($colors) ?>,
	list = <?php echo json_encode(array_unique($list)) ?>,
	cp = <?php echo $project_id ?>,
	count = <?php echo json_encode($count) ?>,
	nodes = [{
		key: cp,
		text: project.project_name,
		color: '#09c',
		expanded: true,
		image: close,
		expandable: false
	}],
	links = [],
	linkTracks = {},
	pTracks = {};
	pTracks[cp] = 1;

//expand/collapse by ajax
function toggleNodes(evt, obj){
	var node = obj.part,
		diagram = node.diagram,
		img = evt.targetObject;
	//prevent multi clicks | expand/collapse root (current project)
	if( toggling || node.data.key == cp || node.data.expanded )return;
	//do expand
	jQuery.getJSON('<?php echo $this->Html->url('/project_dependencies/expand/') ?>' + node.data.key, function(response){
		//update links count for additional projects
		for(var i in response.count){
			count[i] = response.count[i];
		}
		//do add node/links
		addData(node, diagram, response.data);
		img.visible = false;
		toggling = false;
	});
	node.data.expanded = !node.data.expanded;
}

function addData(node, diagram, serverData){
	var _nodes = [], _links = [];
	for(var i in serverData){
		//create note (project)
		var x = serverData[i].ProjectDependency;
		if( typeof pTracks[ x.target_id ] == 'undefined' ){
			_nodes.push({
				key: x.target_id,
				text: projects[ x.target_id ],
				color: '#333',
				expanded: false,
				image: open,
				expandable: true
			});
			trackProject(x.target_id);
		}
		//create links
		var list = jQuery.parseJSON(x.dependency_ids);
		for(var j in list){
			var y = list[j];
			if( !hasLink(x.grouper, y) ){
				_links.push({
					from: x.project_id,
					to: x.target_id,
					text: dependencies[ y ],
					color: colors[ y ],
					group: x.grouper,
					id: y
				});
				trackLink(x.grouper, y);
			}
		}
	}
	diagram.startTransaction("toggleNodes");
		for(var i in _nodes){
			diagram.model.addNodeData(_nodes[i]);
		}
		for(var i in _links){
			diagram.model.addLinkData(_links[i]);
		}
		nodes = nodes.concat(_nodes);
		links = links.concat(_links);
		toggleButtons();
	diagram.commitTransaction("toggleNodes");
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
function toggleButtons(){
	var it = myDiagram.nodes,
		c = 0;
	while(it.next()){
		var node = it.value,
			id = node.data.key;
		var currentLinks = node.linksConnected.count, totalLinks = count[id];
		if( currentLinks < totalLinks ){
			node.data.expandable = true;
			node.data.expanded = false;
		} else {
			node.data.expandable = false;
			node.data.expanded = true;
		}
		node.updateTargetBindings();
	}
}

function init() {
	//if (window.goSamples) goSamples();	// init for these samples -- you don't need to call this
	var $ = go.GraphObject.make;	// for conciseness in defining templates
	myDiagram =
		$(go.Diagram, "diagram",	// must name or refer to the DIV HTML element
			{
				initialAutoScale: go.Diagram.Uniform,	// an initial automatic zoom-to-fit
				contentAlignment: go.Spot.Center,	// align document to the center of the viewport
				layout:
					$(go.ForceDirectedLayout,	// automatically spread nodes apart
						{ defaultSpringLength: 50, defaultElectricalCharge: 120 }
					),
				allowDelete: false,
				allowClipboard: false
			}
		);

	var forelayer = myDiagram.findLayer("Foreground");
	myDiagram.addLayerBefore($(go.Layer, { name: "behind" }), forelayer);
	myDiagram.addLayerAfter($(go.Layer, { name: "front" }), forelayer);

	// define each Node's appearance
	myDiagram.nodeTemplate = $(go.Node, "Horizontal",	// the whole node panel
		// define the node's outer shape, which will surround the TextBlock
		$(
			go.Panel, 'Auto',
			$(
				go.Shape, "Rectangle",
				{
					fill: $(
						go.Brush,
						"Linear",
						{
							0: "rgba(255,255,255,1)",
							1: "rgba(246,246,246,1)"
						}
					),
					stroke: "#ccc"
				}
			),
			$(
				go.TextBlock,
				{
					font: "bold 10pt helvetica, bold arial, sans-serif",
					margin: 6
				},
				new go.Binding("text", "text"),
				new go.Binding('stroke', 'color')
			)
		),
		//button to show/hide
		$(
			go.Picture,
			{
				//source: '<?php echo $this->Html->url('/img/icon-plus.png') ?>',
				width: 16,
				height: 16,
				margin: 2,
				cursor: 'pointer',
				alignment: go.Spot.MiddleRight,
				click: toggleNodes,
				toolTip:
				$(
					go.Adornment,
					"Auto",
					$(
						go.Shape, { fill: "#f0f0f0", stroke: '#bbb' }
					),
					$(
						go.TextBlock,
						{
							margin: 4,
							text: '<?php __('Expand') ?>'
						}
					)
				)
			},
			new go.Binding('source', 'image'),
			new go.Binding('visible', 'expandable')
		),
		{
			shadowColor: 'rgba(0, 0, 0, 0.2)',
			isShadowed: true,
			shadowOffset: new go.Point(2, 2)
		}
	);

	// replace the default Link template in the linkTemplateMap
	myDiagram.linkTemplate =
		$(
			go.Link,	// the whole link panel
			{
				layerName: 'behind',
				toolTip:
					$(
						go.Adornment,
						"Auto",
						$(
							go.Shape, { fill: "#fff", stroke: '#bbb' }
						),
						$(
							go.TextBlock, { margin: 4 },
							new go.Binding("text", "text")
						),
						{
							shadowColor: 'rgba(0, 0, 0, 0.2)',
							isShadowed: true,
							shadowOffset: new go.Point(2, 2)
						}
					),
				selectionChanged: function(p) {
		          if (p.isSelected) p.layerName = "Foreground";
		          else p.layerName = 'behind';
		        }
			},
			$(
				go.Shape,	// the link shape
				new go.Binding('stroke', 'color')
			),
			$(
				go.Panel,
				"Auto",
				$(
					go.Shape,	// the link shape
					{
						fill: $(
							go.Brush,
							"Radial",
							{
								0: "rgb(240, 240, 240)",
								0.3: "rgb(240, 240, 240)",
								1: "rgba(240, 240, 240, 0)"
							}
						),
						stroke: null
					}
				)
				,
				$(
					go.TextBlock,	// the label
					{
						textAlign: "center",
						font: "10pt helvetica, arial, sans-serif",
						margin: 4
					},
					new go.Binding("text", "text"),
					new go.Binding('stroke', 'color')
				)
			)
		);

	//create nodes
	for(var i = 0; i < list.length; i++){
		if( list[i] == cp )continue;
		nodes.push({
			key: list[i],
			text: projects[ list[i] ],
			color: '#333',
			expanded: false,
			image: open,
			expandable: true
		});
		trackProject(list[i]);
	}
	//create links
	for(var i = 0; i < data.length; i++){
		var x = data[i].ProjectDependency,
			y = jQuery.parseJSON(x.dependency_ids);
		for(var j = 0; j < y.length; j++){
			links.push({
				from: x.project_id,
				to: x.target_id,
				text: dependencies[ y[j] ],
				color: colors[ y[j] ],
				group: x.grouper,
				id: y[j]
			});
			trackLink(x.grouper, y[j]);
		}
	}
	myDiagram.toolManager.hoverDelay = 200;

	myDiagram.model = new go.GraphLinksModel(nodes, links);
	toggleButtons();

	jQuery('#diagram').append('<div id="dependency-info"></div><div id="hide-me"></div>');
	jQuery.each(dependencies, function(i, v){
		jQuery('#dependency-info').append('<dl><dt style="background-color: ' + colors[i] + '"></dt><dd>' + v + '</dd></dl>');
	});
}
jQuery(document).ready(init);
</script>