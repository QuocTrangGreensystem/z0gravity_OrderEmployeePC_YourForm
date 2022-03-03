<style>
.wd-paged{
	text-align: center;
	color: #242424;
	margin-bottom: 40px;
	font-size: 13px;
	margin-top: 15px;
}
.wd-paged a:not(.wd-prev):not(.wd-next),
.wd-paged .current{
	height: 30px;
    width: 30px;
    line-height: 30px;
    border: 1px solid #E1E6E8;
    background-color: #FFFFFF;
    border-radius: 50%;
    padding: 0;
    box-sizing: border-box;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    color: #666;
    cursor: pointer;
	margin-left: 5px;
	vertical-align: middle;
}
.wd-paged a:not(.wd-prev):not(.wd-next):hover{
	background-color: #247FC3;
    color: #fff;
	box-shadow: 0 0 8px 1px rgba(29,29,27,0.06);
    border-color: #247FC3;
}
.wd-paged .current{
	background-color: #247FC3;
    color: #fff;
    border-color: #247FC3;
}
.wd-paged .page-counter{
	display: inline-block;
    margin-left: 10px;
    line-height: 30px;
    vertical-align: middle;
    position: relative;
    top: 1px;
}
.wd-paged a.page-all:not(.wd-prev):not(.wd-next){
	background: transparent;
    border-color: transparent;
    color: #242424;
    text-decoration: underline;
    border-radius: 0;
}
.wd-paged a.page-all:not(.wd-prev):not(.wd-next):hover{
	background: transparent;
    border-color: transparent;
	color: #247FC3;
	box-shadow: none;
}
.wd-paged a.icon-arrow-right,
.wd-paged a.icon-arrow-left{
	font-size: 12px;
	position: relative;
}
.wd-paged a.icon-arrow-right:after,
.wd-paged a.icon-arrow-left:after{
	content: "\e605";
    font-family: 'simple-line-icons';
    display: inline-block;
    position: absolute;
    left: 5px;
    top: 0;
    font-size: 10px;
}
.wd-paged a.icon-arrow-right:after{
	content: "\e606";
	left: 12px;
}
</style>

<div class="wd-paged">
	<?php 
	echo $this->Paginator->first(__("", true), array('class'=>'icon-arrow-left icon-arrow-left', 'tag' => 'span'));
	echo $this->Paginator->numbers(array("separator" => " "));
	echo $this->Paginator->last(__("", true), array('class' => 'icon-arrow-right icon-arrow-right', 'tag' => 'span'));
	$count = $this->Paginator->counter('{:count}');
	echo $this->Paginator->link(__('All', true), array('limit' => $count), array('class'=>'page-all'));
	echo $this->Paginator->counter('<p class="page-counter">Page {:page} '. __('of', true) .' {:pages} pages</p>');?>
	
</div>