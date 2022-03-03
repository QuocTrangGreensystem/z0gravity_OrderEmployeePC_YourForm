<style>
	.wd-table{
		height: calc( 100vh - 80px);
		min-height: 400px;
		max-width: 100%;
		overflow: auto;
		border: 1px solid #e0e0e0;
	}
	.project-item{
		display: block;
		border-bottom: 1px solid #e0e0e0;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.project-item p{
		display: inline-block;
		line-height: 28px;
		padding: 0 30px;
		margin: 0;
	}
	.project-item.finish p{
		color: green;
	}
	.project-item.error p{
		color: red;
	}
	.project-item.loading p{
		background: url(../../img/ajax-loader.gif) no-repeat right 2px center;
	}
	#progress{
		color: #111;
		padding: 0 30px;
		line-height: 40px;
		font-size: 120%;
	}
	#layout{
		height: 100%;
	}
</style>
<div class="wd-table">
	<?php 
	$i = 0;
	foreach($listProject as $id => $name){
		echo "<div class='project-item' id='project-{$id}' data-id='{$id}' data-index='{$i}'><p>{$name}</p></div>";
		$i++;
	}?>
</div>
<div><p id="progress"><?php __('Please wait...');?></p> </div>
<script>
var i = 0;
var listProject = <?php echo json_encode($listProject);?>;
var allItem = $('.project-item');
var length = allItem.length;
var _time = '';
// length = 5;
var each_run = 30;
$( document ).ready(function() {
	function run(){
		list = allItem.slice(i, i+each_run);
		var listProjectID = [];
		elm = 0;
		$.each(list, function(i, item){
			if( elm == 0) elm = item;
			$(item).addClass('loading');
			listProjectID.push( $(item).data('id'));
		});
		$.ajax({
			url : 'updateProjectBudgetSyn/run',
			type: 'POST',
			dataType: 'json',
			// async: false,
			data: {
				data: listProjectID
			}, 
			beforeSend: function(){
				var top = $(elm).data('index') * ($(elm).height()) - 40;
				$('.wd-table').animate({
					scrollTop: top
				});
			},
			success: function(res){
				if( res.result == 'success'){
					$.each(res.data, function( id, data){
						$('#project-' + id).removeClass('loading').addClass('finish');
					});
					i += each_run;
					$('#progress').html('Updated ' + Math.min(i,length) + '/' + length + ' projects');
					if( length >= i){
						setTimeout( function(){
							run();
						}, 150);
					}else{
						var now = new Date();
						var ptime = parseInt((now  - _time )/1000);
						$('#progress').html('Update Finish '+ length + ' projects in ' + ptime + 's.');
					}
				}else{
					allItem.removeClass('loading').addClass('error');
				}
			},
			complete: function(){
				
			},
			error: function(){
				allItem.removeClass('loading').addClass('error');
			},			
		});
	}
	setTimeout( function(){
		_time = new Date();
		run();
	}, 150);
	/* while( length >=i ){
		
		var each_run = 30;
		// var each_run = 3;
		list = allItem.slice(i, i+each_run);
		// console.log( list);
		var listProjectID = [];
		$.each(list, function(i, item){
			$(item).addClass('loading');
			listProjectID.push( $(item).data('id'));
		});
		console.log( listProjectID);
		
		setTimeout( function(){
			$.ajax({
				url : 'updateProjectBudgetSyn/run',
				type: 'POST',
				dataType: 'json',
				async: false,
				data: {
					data: listProjectID
				}, 
				success: function(res){
					console.log( res.result, res);
					if( res.result == 'success'){
						$.each(res.data, function( id, data){
							$('#project-' + id).removeClass('loading').css('color', 'green');
						});
					}else{
						allItem.removeClass('loading').css('color', 'red');
					}
				},
				complete: function(){
					
				},
				error: function(){
					allItem.css('color', 'red');
				},
			
				
			});
		}, 50);		
		i += each_run;
		// console.log(i);
	}*/
});
</script>
