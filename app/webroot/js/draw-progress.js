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
    // var format = max;
    var format = Math.round(max);
	// update ticket 533 - check >100 thi red
	var _max = max;
	// end update ticket 533
    if(max > 100) max = 100;
    if(max < 0) max = 0;
    // var font = '15pt Verdana';
    var font = '300 40px "Open Sans"';
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
        color = _max > 100 ? arr_cl2 : arr_cl1;
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
            context.arc(cw,ch,square,i*angle+start ,i*angle + start + surplus_angle ,false);
            color_index = 0; 
            context.strokeStyle=color[color_index];
            context.stroke();
        }
        // End tô màu viền
    
        // vẽ hình tròn bên trong      
        // context.beginPath();
        // context.arc(cw,ch,square - 2*border,0,Math.PI*2,false);
        // context.fillStyle = bgr_cl;
        // context.fill();
        
        // vẽ chữ
        context.beginPath();
        context.textAlign='center';
        context.lineWidth = 5;
        context.font = font;
        // context.fillStyle = '#fff';
        context.fillStyle = color[0];
        context.fill();
        context.beginPath();
        context.stroke();
        // context.fillText(format+'%',cw+2,ch+6);
        context.fillText(format+'%',cw,ch+12);
        return color[0];
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
    canvas.setAttribute('data-color', bar);
}
// var prog = draw_progress('myCanvas');
// var prog = draw_progress('myCanvas-2');
function draw_line_progress(value){
    var _html = '';
    var _color_gray = '#E2E6E8'
    var _color_green = ['#6EAF79', '#89BB92', '#AACCB0', '#C1D6C5', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9' ];
    var _color_blue =  ['#6FB0CF', '#87BFDA', '#A3CCE0', '#BBDAE9', '#D6E8F0'];
    var _use_color = value > 50 ? _color_green : _color_blue;
    var _index, _current_color;
    for( _index = 1; _index <= 10; _index++){
        _current_color = _index*10 <= value ? _use_color[(parseInt(value/10) - _index)] : _color_gray;
        _html += '<span class="progress-node" style="background: ' + _current_color + '"></span>';
    }
    return _html;
}