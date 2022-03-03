<?php if($typeSelect == 'month'){?>
<style type="text/css">#absence{width:4000px !important;} #absence-scroll{overflow-x: scroll;} .validate-for-validate-top{margin-left: 321px;} .validate-month{margin-left: 7px !important;} .reject-month{margin-left: 4px !important;}</style>    
<?php } elseif($typeSelect == 'year') {?>
<style type="text/css">#absence{width:35000px !important;} #absence-scroll{overflow-x: scroll;} .validate-for-validate-top{margin-left: 321px;} .validate-month{margin-left: 7px !important;} .reject-month{margin-left: 4px !important;}</style>
<?php }?>
<?php 
    $query = '';
    if(isset($profit['id'])){
        $query = isset($profit) ? '&profit=' . $profit['id'] : '';
    } 
    if (!empty($isManage)) {
        $query = '&id=' . $this->params['url']['id'] . '&profit=' . $this->params['url']['profit'];
    }
    if($this->params['controller'] == 'absence_requests' && isset($this->params['url']['st'])){
        $query .= '&st=' . $this->params['url']['st'];
    }
	if(isset($getDataByPath) && $getDataByPath && $this->params['controller'] == 'absence_requests' && $this->params['action'] == 'index'){
		$query .= '&get_path=' . $getDataByPath;
	}
    $linkedPre = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $preWeek = mktime(0, 0, 0, date("m", $_start), date("d", $_start)-7, date("Y", $_start));
    $week = date('W', $preWeek);
    $year = date('Y', $preWeek);
    if ($year < date('Y', mktime(0, 0, 0, date("m", $_start)+1, date("d", $_start), date("Y", $_start))) && $week == 1 && $typeSelect == 'week') {
        $year++;
    }
    if($typeSelect=='week'){
        $linkedPre = $this->Html->here .'?week=' . $week . '&year=' . $year . $query;
        echo '<a id="absence-prev" href="' . $this->Html->url($linkedPre) . '"><span>Prev</span></a>';
    } else {
        $month = date('m', mktime(0, 0, 0, date("m", $_start)-1, date("d", $_start), date("Y", $_start)));
        echo '<a id="absence-prev" href="' . $this->Html->here . '?month=' . $month . '&year=' . $year . $query . ' "><span>Prev</span></a>';
    }
    if(($this->params['controller']=='activity_forecasts' && ($this->params['action']=='request' || $this->params['action']=='response' || $this->params['action']=='manages') || ($this->params['controller']=='absence_requests'&& ($this->params['action']=='index' || $this->params['action']=='mobile-index')) || ($this->params['controller']=='absence_requests'&& ($this->params['action']=='manage' || $this->params['action']=='mobile-manage' )))){
        if($typeSelect=='week'){
            $msg = __(date('d ', $_start), true) . __(date('M', $_start), true) . __(' to ', true) . __(date('d ', $_end), true) . __(date('M', $_end), true);
            echo '<span class="currentWeek">' . $msg . '</span>';
        } elseif($typeSelect=='month'){
            echo '<span class="currentWeek">' . __(date('M', $_start), true) . '</span>';
        }else{
            echo '<span class="currentWeek">' . __(date('Y', $_start), true) . '</span>';
        }
    }
    $nextWeek = mktime(0, 0, 0, date("m", $_start), date("d", $_start)+7, date("Y", $_start));
    $week = date('W', $nextWeek);
    $year = date('Y', $nextWeek);
    if ($year < date('Y', mktime(0, 0, 0, date("m", $_end)+1, date("d", $_end), date("Y", $_end))) && $week == 1 && $typeSelect == 'week') {
        $year++;
    }
    $linkedNext = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if($typeSelect == 'week'){
        $linkedNext = $this->Html->here .'?week=' . $week . '&year=' . $year . $query;
        echo '<a id="absence-next" href="' . $this->Html->url($linkedNext) . '"><span>Next</span></a>';
    } elseif($typeSelect == 'month'){
        $nextDate = mktime(0, 0, 0, date("m", $_start)+1, date("d", $_start), date("Y", $_start));
        $month = date('m', $nextDate);
        $year = date('Y', $nextDate);
        $linkedNext = $this->Html->here .'?month=' . $month . '&year=' . $year . $query;
        echo '<a id="absence-next" href="' . $this->Html->url($linkedNext) . '"><span>Next</span></a>';
    }else{
        $month = 1;
        $year = mktime(0, 0, 0, date("m", $_start), date("d", $_start), date("Y", $_start)+1);
        $year = date('Y', $year);
        echo '<a id="absence-next" href="'. $this->Html->here .'?month=' . $month . '&year=' . $year . $query . '"><span>Next</span></a>';
    }
?>