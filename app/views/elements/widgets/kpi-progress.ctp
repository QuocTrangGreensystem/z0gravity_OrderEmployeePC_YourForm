	<div class="group-content" style="clear:both;">
		<h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Progress', true);?></span></h3>

	<div class="budget-progress">
		 <div class="progress-label">
            <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span><?php echo __('Consumed', true);?></span></div>
            <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><?php echo __('Planed', true);?></span></div>
        </div>
		<div id = "budget-inner" class="budget-inner">
			<div class="wd-table" id="budget_db" style="height:280px;"></div>	
		</div>
		<span id ="left" class="scroll-progress scroll-left"></span>
        <span id ="right" class="scroll-progress scroll-right"></span>
	</div>
    <aside class="budget-progress-circle" style="overflow:visible; margin-top: 32px">

        <div class="progress-circle progress-circle-yellow">
            <div class="progress-circle-inner">
                <i class="icon-question" aria-hidden="true"></i>
                <canvas data-value = "<?php echo $progression; ?>" id="myCanvas" width="165" height="160" style="" class="canvas-circle"></canvas>
                <div class ="progress-value progress-validated"><p><?php echo __('Consumed', true);?></p><span><?php echo $engaged; ?></span></div>
                <div class ="progress-value progress-engaged"><p><?php echo __('Planed', true);?></p><span><?php echo round($validated, 2); ?></span></div>
              <!--   <div class="progress-circle-text"><?php echo __d(sprintf($_domain, 'KPI'), 'synthèse détaillée', true);?></div> -->
            </div>
        </div>
    </aside>
	<br clear="all"  />
	<?php
	
	foreach($dataExternals as $_external=> $_dataExternal){
		$count = !empty($_dataExternal['dataSetsExternal']) ? count($_dataExternal['dataSetsExternal']) : 0;

	?>
	<div class="demo-gauge">
		<div id='gauge_<?php echo $_external; ?>'>
		</div>
		<br clear="all"  />
		<div class="num-progress">
			<?php
				$pros = !empty($_dataExternal['progressExternal']) ? $_dataExternal['progressExternal'] : 0;
				echo __($pros . '% Progression', true);
			?>
		</div>
	</div>
	<div class="budget-progress budget-external">
		<div class="progress-label">
	        <div class=""><span class="progress-label-color" style="background-color: #538FFA"></span><span><?php echo __('Consumed', true);?></span></div>
	        <div class=""><span class="progress-label-color" style="background-color: #E44353"></span><span><?php echo __('Planed', true);?></span></div>
	    </div>
		<div id = "budget-inner" class="budget-inner">
			<div class="wd-table" id="budget_external_<?php echo $_external; ?>" style="width: <?php echo $count * 50 ?>px; height:280px;"></div>
		</div>
		<span id ="left" class="scroll-progress scroll-left"></span>
        <span id ="right" class="scroll-progress scroll-right"></span>
	</div>
	<br clear="all"  />
	<?php } ?>
</div>

<script>
    function draw_progress(myCanvas){
    //var id = id ? id : 'myCanvas';
    var id = myCanvas;
    var canvas = document.getElementById(id);
    var context = canvas.getContext('2d');
    /* Khởi tạo giá trị */
    var al=0; // giá trị khởi đầu
    var start= 21; // Vị trí khởi đầu
    var border = 10; // độ rộng của vòng cung
    var bgr_cl = '#d4d4d4'; // Background color
    var total_steps = 12; // chia vòng tròn làm 12 phần
    var num_steps = 10; // chạy 10 phần bỏ trống 2
    var not_fill = 0.1; // tô màu 0.9 (90%), bỏ trống 0.1 (10%)
    var max = 0; // giá trị dừng 
    if( canvas.getAttribute('data-value') ) max = canvas.getAttribute('data-value');
    var font = '15pt Verdana';
    arr_cl2 = ['#DB414F','#E0636E','#EA848E','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC']; // cái này cho nó trên 50%
    arr_cl1 = ['#75AF7E','#8FBB96','#AECDB3','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC']; // cái này cho dưới 50%
    /* End Khởi tạo giá trị */
    var width = context.canvas.width;
    var height = context.canvas.height;
    var square = Math.min(width,height); // lấy kích thước canvas
    square /= 2; // bán kính = 1/2 canvas
    square -= border/2; // trừ phần border 
    var cw=context.canvas.width/2;
    var ch=context.canvas.height/2;
    var diff; // độ dài vòng cung
    var percent;

    /*
    arc( x,y,R, start_angle, e_angle,bool counterclockwise);
    */
    function progressBar(){
        angle = Math.PI*2/total_steps; // vong cung của 1 góc
        diff=(al/10)*Math.PI*2;
        context.clearRect(0,0,400,200);
        
        // Vẽ vòng cung màu nhạt bên dưới (placeholder)
        for(i = 0; i< num_steps; i++){
          context.beginPath(); // clear vị trí con trỏ graph
          context.lineWidth = border; // độ rộng của vòng cung
          context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
          context.strokeStyle=bgr_cl;
          context.stroke();
        }
        // context.strokeStyle= al <50 ? '#F29D3A' : 'red';
        
        
        // Tô màu viền
        color = max > 90 ? arr_cl2 : arr_cl1;
        //number = parseInt(max/10);
        number = Math.floor(max/10); // làm tròn xuống
        surplus = parseInt(max%10);
        i=0;
        for(i=0; i < number;i++){
            context.beginPath(); // clear vị trí con trỏ graph
            context.lineWidth = border; // độ rộng của vòng cung
            context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
            color_index = number - i - 1; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
        if(surplus){
            context.beginPath(); // clear vị trí con trỏ graph
            context.lineWidth = border; // độ rộng của vòng cung
            surplus_angle = surplus*angle/10;
            console.log(surplus_angle);
            context.arc(cw,ch,square,i*angle+start ,i*angle + start + surplus_angle ,false);
            color_index = 0; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
            // End tô màu viền
            
            // vẽ hình tròn bên trong      
            context.beginPath();
            context.arc(cw,ch,square - 2*border,0,Math.PI*2,false);
            context.fillStyle = bgr_cl;
            context.fill();
            
            // vẽ chữ
            context.beginPath();
            context.textAlign='center';
            context.lineWidth = 5;
            context.font = font;
            context.fillStyle = '#fff';
            context.fill();
            context.beginPath();
            context.stroke();
            context.fillText(max+'%',cw+2,ch+6);
            
            // Phần này để đó cho chữ chạy và thanh progres chạy
            /*percent = parseInt(al/10) * 10;
            context.fillText(percent+'%',cw+2,ch+6);
            ///al = al == 99 ? 0 : al;  
            if(al>= max){
                  clearTimeout(bar);
              }
              al++;*/
        }

        //var bar=setInterval(progressBar,50); // cái này cho chạy với delay 50
        var bar = progressBar();
    }
    var prog = draw_progress('myCanvas');
    var prog = draw_progress('myCanvas-2');


</script>
