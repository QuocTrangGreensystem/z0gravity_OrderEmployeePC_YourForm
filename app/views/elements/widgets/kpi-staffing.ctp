<div class="group-content">
    <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Staffing', true);?></span></h3>
    <div class="wd-input wd-input-90" style="margin-top: 3px;">
        <!--label for="organization"><?php //__("Organization") ?></label-->
        <?php
        // echo $this->Form->input('project_amr_organization_id', array('div' => false, 'label' => false,
        // 	'class' => 'selection-plus',
        // 	'name' => 'data[ProjectAmr][project_amr_organization_id]',
        // 	'value' => (!empty($this->data['ProjectAmr']['project_amr_organization_id'])) ? $this->data['ProjectAmr']['project_amr_organization_id'] : "",
        // 	"empty" => __("-- Select --", true),
        // ));
        ?>
        <div style="float: left; line-height: -40px; width:30%">
            <div class="wd-input wd-weather-list-dd">
                <ul style="float: left; display: inline;">
                    <li><input class="input_weather" checked="true" <?php echo !(($canModified && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][organization_weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][organization_weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["organization_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][organization_weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                </ul>
                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
            </div>
        </div>
    </div>
    <div class="demo-gauge" style="float: left; margin-top: 45px; margin-left: -450px;">
        <div id='gaugeProfit'>
            <aside>
             
              <div class="progress-circle progress-circle-yellow">
                <div class="progress-circle-inner">
                    <i class="icon-question" aria-hidden="true"></i>
                    <canvas data-value = "<?php echo $assginProfitCenter; ?>" id="myCanvas" width="165" height="160" style="" class="canvas-circle"></canvas>
                    <div class ="progress-value progress-validated"><p><p><?php echo __('Consumed', true);?></p><span><?php echo $validated; ?></span></div>
                    <div class ="progress-value progress-engaged"><p><?php echo __('Planed', true);?></p><span><?php echo round($assgnPc, 2); ?></span></div>
                  <!--   <div class="progress-circle-text"><?php echo __d(sprintf($_domain, 'KPI'), 'synth??se d??taill??e', true);?></div> -->
                </div>
            </div>
            </aside>
        </div>
        <div>
            <p style="padding-left: 55px;">
            <?php
                $assPc = !empty($assginProfitCenter) ? $assginProfitCenter : 0;
                echo $assPc . __('% Assigned to profit center', true);
            ?>
            </p>
        </div>
    </div>
    <div class="demo-gauge" style="float: left; margin-top: 45px; margin-left: 50px;">
        <div id='gaugeEmployee'>
            <aside>
            
               <div class="progress-circle progress-circle-yellow">
                <div class="progress-circle-inner">
                    <i class="icon-question" aria-hidden="true"></i>
                    <canvas data-value = "<?php echo $assgnEmployee; ?>" id="myCanvas-2" width="165" height="160" style="" class="canvas-circle"></canvas>
                    <div class ="progress-value progress-validated"><p><?php echo __('Consumed', true);?></p><span><?php echo $tWorkload; ?></span></div>
                    <div class ="progress-value progress-engaged"><p><?php echo __('Planed', true);?></p><span><?php echo round($assgnEmploy, 2); ?></span></div>
                  <!--   <div class="progress-circle-text"><?php echo __d(sprintf($_domain, 'KPI'), 'synth??se d??taill??e', true);?></div> -->
                </div>
            </div>
            </aside>
        </div>
        <div>
            <p style="padding-left: 60px;">
            <?php
                $assEm = !empty($assgnEmployee) ? $assgnEmployee : 0;
                echo $assEm . __('% Assigned to employee', true);
            ?>
            </p>
        </div>
    </div>
</div>
<script>
    function draw_progress(myCanvas){
    //var id = id ? id : 'myCanvas';
    var id = myCanvas;
    var canvas = document.getElementById(id);
    var context = canvas.getContext('2d');
    /* Kh???i t???o gi?? tr??? */
    var al=0; // gi?? tr??? kh???i ?????u
    var start= 21; // V??? tr?? kh???i ?????u
    var border = 10; // ????? r???ng c???a v??ng cung
    var bgr_cl = '#d4d4d4'; // Background color
    var total_steps = 12; // chia v??ng tr??n l??m 12 ph???n
    var num_steps = 10; // ch???y 10 ph???n b??? tr???ng 2
    var not_fill = 0.1; // t?? m??u 0.9 (90%), b??? tr???ng 0.1 (10%)
    var max = 0; // gi?? tr??? d???ng 
    if( canvas.getAttribute('data-value') ) max = canvas.getAttribute('data-value');
    var font = '15pt Verdana';
    arr_cl2 = ['#DB414F','#E0636E','#EA848E','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC','#EFA5AC']; // c??i n??y cho n?? tr??n 50%
    arr_cl1 = ['#75AF7E','#8FBB96','#AECDB3','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC','#DBE4DC']; // c??i n??y cho d?????i 50%
    /* End Kh???i t???o gi?? tr??? */
    var width = context.canvas.width;
    var height = context.canvas.height;
    var square = Math.min(width,height); // l???y k??ch th?????c canvas
    square /= 2; // b??n k??nh = 1/2 canvas
    square -= border/2; // tr??? ph???n border 
    var cw=context.canvas.width/2;
    var ch=context.canvas.height/2;
    var diff; // ????? d??i v??ng cung
    var percent;

    /*
    arc( x,y,R, start_angle, e_angle,bool counterclockwise);
    */
    function progressBar(){
        angle = Math.PI*2/total_steps; // vong cung c???a 1 g??c
        diff=(al/10)*Math.PI*2;
        context.clearRect(0,0,400,200);
        
        // V??? v??ng cung m??u nh???t b??n d?????i (placeholder)
        for(i = 0; i< num_steps; i++){
          context.beginPath(); // clear v??? tr?? con tr??? graph
          context.lineWidth = border; // ????? r???ng c???a v??ng cung
          context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
          context.strokeStyle=bgr_cl;
          context.stroke();
        }
        // context.strokeStyle= al <50 ? '#F29D3A' : 'red';
        
        
        // T?? m??u vi???n
        color = max > 90 ? arr_cl2 : arr_cl1;
        //number = parseInt(max/10);
        number = Math.floor(max/10); // l??m tr??n xu???ng
        surplus = parseInt(max%10);
        i=0;
        for(i=0; i < number;i++){
            context.beginPath(); // clear v??? tr?? con tr??? graph
            context.lineWidth = border; // ????? r???ng c???a v??ng cung
            context.arc(cw,ch,square,i*angle+start ,(i+1)*angle + start - angle*not_fill ,false);
            color_index = number - i - 1; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
        if(surplus){
            context.beginPath(); // clear v??? tr?? con tr??? graph
            context.lineWidth = border; // ????? r???ng c???a v??ng cung
            surplus_angle = surplus*angle/10;
            console.log(surplus_angle);
            context.arc(cw,ch,square,i*angle+start ,i*angle + start + surplus_angle ,false);
            color_index = 0; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
            // End t?? m??u vi???n
            
            // v??? h??nh tr??n b??n trong      
            context.beginPath();
            context.arc(cw,ch,square - 2*border,0,Math.PI*2,false);
            context.fillStyle = bgr_cl;
            context.fill();
            
            // v??? ch???
            context.beginPath();
            context.textAlign='center';
            context.lineWidth = 5;
            context.font = font;
            context.fillStyle = '#fff';
            context.fill();
            context.beginPath();
            context.stroke();
            context.fillText(max+'%',cw+2,ch+6);
            
            // Ph???n n??y ????? ???? cho ch??? ch???y v?? thanh progres ch???y
            /*percent = parseInt(al/10) * 10;
            context.fillText(percent+'%',cw+2,ch+6);
            ///al = al == 99 ? 0 : al;  
            if(al>= max){
                  clearTimeout(bar);
              }
              al++;*/
        }

        //var bar=setInterval(progressBar,50); // c??i n??y cho ch???y v???i delay 50
        var bar = progressBar();
    }
    var prog = draw_progress('myCanvas');
    var prog = draw_progress('myCanvas-2');


</script>