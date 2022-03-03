<?php

/**
 * PHP versions 5
 * 
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
class GanttHelper extends AppHelper {

    /**
     * The runtime config for create a gantt chart
     *
     * @var array
     */
    protected $_runtime = array();

    /**
     * The runtime month for create a gantt chart
     *
     * @var array
     */
    protected $_months = array();

    /**
     * N/A message
     *
     * @var string
     */
    public $na = null;

    /**
     * Preg pattern for valid date format.
     *
     * @var string
     */
    protected $_pattern = '%^(?:(?:(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\\/|-|\\.|\\x20)(?:0?2\\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\\d)?\\d{2})(\\/|-|\\.|\\x20)(?:(?:(?:0?[13578]|1[02])\\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\\2(?:0?[1-9]|1\\d|2[0-8]))))$%';

    /**
     * Constructor.
     *
     */
    public function __construct() {
        parent::__construct();
        $this->na = __('N/A', true);
    }

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     * 
     * @return integer, a Unix timestamp value
     */
    public function toTime($date) {
        if (empty($date) || !preg_match($this->_pattern, $date)) {
            return 0;
        }
        return intval(strtotime($date));
    }

    /**
     * Parse value to runtime config.
     *
     * @param string $type, the view type of gantt chart (date, week, month or year)
     * @param integer $start, a Unix timestamp value of mininum date start
     * @param integer $end, a Unix timestamp value of maxinum date end
     * @param array $stones, an list milestones
     * @param boolean $displayList, set true if you want show list date detail or false for hide it
     * @param string $format, the date format display in chart
     * 
     * @return void
     */
    public function create($type, $start, $end, $stones = array(), $displayList = true, $isName = true, $format = 'd-m-Y', $pList = false, $twoTable = false) {
        $staff = $head = $num = '';
        $_dayDiv = 0;
        if ($type == 'week' || $type == 'date') {
            $_t = intval(date('w', $start));
            if ($_t > 1) {
                $start = $start - (($_t - 1) * 86400);
            } elseif ($_t == 0) {
                $start = $start - (6 * 86400);
            }
            $_t = intval(date('w', $end));
            if ($_t !== 0) {
                $end = $end + ((7 - $_t) * 86400);
            }
            $end += 86400;
            $_dayDiv = 7;
        } elseif ($type == 'month') {
            $start = strtotime(date('Y-n', $start) . '-1');
            list($_m, $_y) = explode('-', date('n-Y', $end));
            $end = strtotime("$_y-$_m-" . $this->daysOfMonth($_m, $_y));
            $_dayDiv = 30;
        } elseif ($type == 'year') {
            $start = strtotime(date('Y', $start) . "-1-1");
            $end = strtotime(date('Y', $end) . "-12-31");
            $_dayDiv = 360;
        } elseif ($type == 'monthyear') {
            $start = strtotime(date('Y-n', $start) . '-1');
            list($_m, $_y) = explode('-', date('n-Y', $end));
            $end = strtotime("$_y-$_m-" . $this->daysOfMonth($_m, $_y));
            $_dayDiv = 30;
        }else{
            $this->cakeError('error404');
        }

        $_dayDiv = $_dayDiv * 86400;

        $month = array(
            1 => __('Jan', true),
            2 => __('Feb', true),
            3 => __('Mar', true),
            4 => __('Apr', true),
            5 => __('May', true),
            6 => __('Jun', true),
            7 => __('Jul', true),
            8 => __('Aug', true),
            9 => __('Sep', true),
            10 => __('Oct', true),
            11 => __('Nov', true),
            12 => __('Dec', true)
        );

        $total = ceil(($end - $start) / 86400);

        list($_d, $_m, $_y) = explode('-', date('j-n-Y', $start));


        $_n = date('j-n-Y');
        $_days = $this->daysOfMonth($_m, $_y);
        $col = $line = $stone = '';
        $i = 1;
        $current = 0;

        /* Milestone */
        $duplicate = 0;
        foreach ($stones as $_stones) {
            list($k, $desc, $valid) = $_stones;
            $left = max(0, round((floor((($k - $start)) / DAY) / $total) * 100, 2));
            if(!empty($valid)){
                $stone .= "<div class=\"gantt-msi gantt-msi-green gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
            } else {
                $currentDate = strtotime(date('d-m-Y', time()));
                $k = strtotime(date('d-m-Y', $k));
                if($currentDate > $k){
                    $stone .= "<div class=\"gantt-msi gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                } elseif($currentDate < $k){
                    $stone .= "<div class=\"gantt-msi gantt-msi-blue gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                } else {
                    $stone .= "<div class=\"gantt-msi gantt-msi-orange gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                }
            }
        }

        if ($duplicate && mb_strlen(trim($desc)) >= 20) {
            $duplicate+=1;
        }

        $this->_months = array();

        while ($i <= $total) {
            $step = 1;
            $class = '';

            if ($_n == $_d . '-' . $_m . '-' . $_y) {
                $class = 'gantt-now';
            }

            if ($type == 'week' || $type == 'date') {
                $mod = $i % 7;
                if (!$class) {
                    if ($i != 1 && ($mod == 6 || $mod == 0)) {
                        if ($mod == 6) {
                            $class = 'gantt-sat';
                        } elseif ($mod == 0) {
                            $class = 'gantt-sun';
                        }
                    }
                }
                $_t = strtotime($_y . '-' . $_m . '-' . $_d);
                if ($type == 'week') {
                    $step = 7;
                    $mod = 0;
                    $_t += (7 * 86400);
                    list($_d, $_m, $_y) = explode('-', date('j-n-Y', $_t));
                }
                if (($i <= $total && $mod == 0)) {
                    $week = date('W', $_t);
                    if ($type == 'date') {
                        $head.="<td colspan=\"7\"><div>w$week - $_m/$_y</div></td>";
                    } else {
                        $head.="<td><div>$_m/$_y</div></td>";
                        $num .= "<td id=\"" . Inflector::slug("week $week $_m $_y") . "\"><div>w$week</div></td>";
                    }
                }
                $col.= "<td class=\"$class\"><div>&nbsp;</div></td>\n";
                if ($type == 'date') {
                    $num.= "<td class=\"$class\" id=\"" . Inflector::slug("date $_d $_m $_y") . "\"><div>$_d</div></td>\n";
                    $_d++;
                }
            } else {
                $step = $_days;
                $_d+= $_days;
            }
            if ($_d > $_days) {
                $_d = 1;
                if ($type == 'month') {
                    $head.="<td class=\"gantt-d$_days\"><div>$_y</div></td>";
                    $num.= "<td class=\"gantt-d$_days\" id=\"" . Inflector::slug("month $_m $_y") . "\"><div>{$month[$_m]}</div></td>";
                    $this->_months[] = array($_days, $_m, $_y);
                } elseif ($type == 'year') {
                    $num .= "<td class=\"" . Inflector::slug("year $_y") . "\"><div>{$month[$_m]}</div></td>";
                } elseif($type == 'monthyear'){
                    $head.="<td class=\"gantt-d$_days\"><div>{$month[$_m]} - $_y</div></td>";
                    $num.= "<td class=\"gantt-d$_days\" id=\"" . Inflector::slug("month $_m $_y") . "\"><div></div></td>";
                    $this->_months[] = array($_days, $_m, $_y);
                }
                $_m++;
                if ($_m > 12) {
                    $_m = 1;
                    if ($type == 'year') {
                        $padding = ($pList == true) ? '0 4px' : '11px';
                        $head.="<td colspan=\"12\" style=\"padding: " . $padding . ";\"><div>$_y</div></td>";
                    }
                    $_y++;
                }
                if ($type == 'month' || $type == 'year') {
                    $col.= "<td class=\"gantt-d$_days\" id=\"" . Inflector::slug("month $_m $_y") . "\"><div>&nbsp;</div></td>\n";
                }
                if ($type == 'monthyear'){
                    $col.= "<td class=\"gantt-d$_days\" id=\"" . Inflector::slug("month $_m $_y") . "\"><div>&nbsp;</div></td>\n";
                }
                
                $_days = $this->daysOfMonth($_m, $_y);
            }
            $i+=$step;
        }
        $displayList = intval($displayList);
		$_status='';
		$list = "<table class=\"gantt-list gantt-list-primary\"><tr class=\"gantt-head\">";
		if($this->params['action']=='projects_vision'&&$this->params['controller']=='projects'){
			$list .= "<td><div>" .  __('Program', true) . "</div></td>";
			$_status = "<td><div>" .  __('Status', true) . "</div></td>";
		}
		
        if($isName == true){
            $list .= "<td><div>" . ($displayList ? __('Phase name', true) : __('Project name', true)) . "</div></td>";
        } else {
            $list .= "<tr class=\"gantt-head\">";
        }
		$list .=$_status;
        if ($displayList) {
            $list .= "<td><div>" . __('Start Date', true) . "</div></td>
                                <td><div>" . __('End Date', true) . "</div></td>
                                <td><div>" . __('Start Real Date', true) . "</div></td>
                                <td><div>" . __('End Real Date', true) . "</div></td>";
        }
        $list.="</tr>";
        if($pList == true){
            $chart = "<table class=\"gantt\">
                <tr>
                    <td class=\"gantt-node gantt-node-head\">
                            <table>";
            if($twoTable == false){
                $chart.= "<tr class=\"gantt-head\" id=\"checkGt\">$head</tr>";
                if($type != 'year'){
                    $chart.= "<tr class=\"gantt-num\">$num</tr>";
                }                                                                                                                                                                                                                                                                                                                                                                                                                   
            }
            $chart.= "</table>
                    </td>
                </tr>
            </tr>";
        } else {
            $chart = "<table class=\"gantt\">
                <tr>
                    <td class=\"gantt-node gantt-node-head\">
                            <table>";
            if ($type == 'month'){
                $chart.= "<tr class=\"gantt-head\">$head</tr>";
                $chart.= "<tr class=\"gantt-num\">$num</tr>";
            }elseif ($type == 'year') {
                $chart.= "<tr class=\"gantt-head\">$head</tr>";
                $chart.= "<tr class=\"gantt-num-date\">$col</tr>";
            }
            else{
                $chart.= "<tr class=\"gantt-head\">$head</tr>";
                $chart.= "<tr class=\"gantt-num\">$num</tr>";    
            }
            
            $chart.= "</table>
                    </td>
                </tr>
            </tr>";
        }
        
        $this->_runtime = compact('type', 'staff', 'line', 'current', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }

    /**
     * Draw a chart line for project.
     *
     * @param string $_name, the project name
     * @param integer $start, a Unix timestamp value of date start
     * @param integer $end, a Unix timestamp value of date end
     * @param integer $_rstart, a Unix timestamp value of read date start
     * @param integer $_rend, a Unix timestamp value of read date end
     * @param string $color, the html syntax color
     * @param string $class, the parant or chidrent html class
     * 
     * @return void
     */
    public function draw($_name, $_start, $_end, $_rstart, $_rend, $color, $class = 'parent') {
        extract($this->_runtime);
        $list .= "<tr class=\"gantt-row gantt-$class\">
                        <td class=\"gantt-name\"><div>" . $_name . "</div></td>";
        if ($displayList) {
            if ((!empty($_end) && $_end > 0) && (!empty($_start) && $_start > 0)) {
                $list .="<td><div>" . date($format, $_start) . "</div></td>
                        <td><div>" . date($format, $_end) . "</div></td>";
            }else{
                 $list .="<td><div>&nbsp;</div></td>
                        <td><div>&nbsp;</div></td>";
            }
            if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
                $list .="<td><div>" . date($format, $_rstart) . "</div></td>
                        <td><div>" . date($format, $_rend) . "</div></td>";
            } else {
                $list .="<td><div>&nbsp;</div></td>
                        <td><div>&nbsp;</div></td>";
            }
        }
        $list .="</tr>";
      
            $chart.= "<tr class=\"gantt-$class\">
                            <td class=\"gantt-node\">
                                    <div class=\"gantt-line\">
                                        <table><tr>$col</tr></table>";
        if ((!empty($_end) && $_end > 0) && (!empty($_start) && $_start > 0)) {
            $chart.= $this->_draw($_name, 'n', $start, $total, $_start, $_end, $format, $color);
        }
        if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
            $chart.= $this->_draw($_name, 's', $start, $total, $_rstart, $_rend, $format, $color);
        }
        $chart.= "</div>
                        </td>
                </tr>";
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }

    /**
     * Start draw a chart line for phase plan.
     *
     * @param string $_name, the phase name
     * @param integer $start, a Unix timestamp value of date start
     * @param integer $end, a Unix timestamp value of date end
     * @param integer $_rstart, a Unix timestamp value of read date start
     * @param integer $_rend, a Unix timestamp value of read date end
     * @param string $color, the html syntax color
     * 
     * @return void
     */
    public function drawLine($_name, $_start, $_end, $_rstart, $_rend, $color, $isPhase = false, $isID = null) {
        extract($this->_runtime);
        if ((!empty($_end) && $_end > 0) && (!empty($_start) && $_start > 0)) {
            $line.= $this->_draw($_name, "n", $start, $total, $_start, $_end, $format, $color, $isPhase, $isID);    
        }
        if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
            $line.=$this->_draw($_name, "s", $start, $total, $_rstart, $_rend, $format, $color, $isPhase, $isID);
        }
        $current = $current ? 0 : 1;
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }
    
    /**
     * Start draw a chart line for phase plan.
     *
     * @param string $_name, the phase name
     * @param integer $start, a Unix timestamp value of date start
     * @param integer $end, a Unix timestamp value of date end
     * @param integer $_rstart, a Unix timestamp value of read date start
     * @param integer $_rend, a Unix timestamp value of read date end
     * @param string $color, the html syntax color
     * 
     * @return void
     */
    public function drawLineProject($id, $_name, $_start, $_end, $_rstart, $_rend, $color, $comp = null, $stones = array()) {
        extract($this->_runtime);
        if ((!empty($_end) && $_end > 0) && (!empty($_start) && $_start > 0)) {
            $line.= $this->_drawProject($id, $_name, '', "n", $start, $total, $_start, $_end, 'd/m/Y', $color, 'parent', $comp);    
        }
        if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
            $line.=$this->_drawProject($id, $_name, '', "s", $start, $total, $_rstart, $_rend, 'd/m/Y', $color, 'parent', $comp);
        }
        $current = $current ? 0 : 1;
        $stone = '';
        $stoneDet = '';
        if(!empty($stones)){
            foreach ($stones as $_stones) {
                list($k, $desc, $valid) = $_stones;
                $left = max(0, round((floor((($k - $start)) / DAY) / $total) * 100, 2));
                if(!empty($valid)){
                    $stone .= "<div class=\"gantt-msi gantt-msi-green gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span></span></div>\n";
                    $stoneDet .= "<div class=\"gantt-msi gantt-msi-green gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                } else {
                    $currentDate = strtotime(date('d-m-Y', time()));
                    $k = strtotime(date('d-m-Y', $k));
                    if($currentDate > $k){
                        $stone .= "<div class=\"gantt-msi gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span></span></div>\n";
                        $stoneDet .= "<div class=\"gantt-msi gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                    } elseif($currentDate < $k){
                        $stone .= "<div class=\"gantt-msi gantt-msi-blue gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span></span></div>\n";
                        $stoneDet .= "<div class=\"gantt-msi gantt-msi-blue gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                    } else {
                        $stone .= "<div class=\"gantt-msi gantt-msi-orange gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span></span></div>\n";
                        $stoneDet .= "<div class=\"gantt-msi gantt-msi-orange gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                    }
                }
            }
        }
        $stoneDetails = "<div class=\"st-detail-$id stone-detail-hide\">" . $stoneDet . "</div>";
        $stoneIndex = "<div class=\"st-index-$id\">" . $stone . "</div>";
        $line .= $stoneIndex . $stoneDetails;
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }
    
    /**
     * Draw html for a chart line.
     *
     * @param string $name, the line display name
     * @param integer $start, a Unix timestamp value of date start
     * @param integer $end, a Unix timestamp value of date end
     * @param string $format, the date format display in chart
     * @param string $color, the html syntax color
     * 
     * @return string output
     */
    protected function _drawProject($id, $name, $psor, $pos, $time, $total, $start, $end, $format = 'd/m', $color = '#000', $addSpan = 'parent', $comp = null, $assign = null) {
        $left = max(0, (((($start - $time)) / DAY) / $total) * 100);
        $width = (((($end - $start) + DAY) / DAY) / $total) * 100;
        if ($left + $width > 100) {
            $width -= ($width + $left) - 100;
        }
        // debug($id);
        //$out = "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"right:" . (100 - $left) . "%\">" . date($format, $start) . "</div>";
        //$out.= "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"left:" . ($left + $width) . "%\">" . date($format, $end) . "</div>";
        $assign = !empty($assign) ? $assign : '';
        $out = '';
        if ($pos == 'n') {
            //$out.= "<div class=\"gantt-line-desc gantt-line-$pos gantt-line-tip\" style=\"margin-top: -3px; left:". ($left) ."%\">" . h($name) . "</div>";
        }
        $_task = explode('-', $id);
        $_isTask = 'block';
        if (!empty($_task)) {
            if ($_task[0] == 'task') {
                $_isTask = 'none';
            }
        }
        if($comp > 100){
            $comp = 100;
        }
        if($color == '#fff'){
            $border = '#004380';
        }else{
            $border = $color; 
        }
		//MODIFY BY VINGUYEN 09/05/2014
		if($pos=='n')
		{
			$style=" background-color: $border !important; width:$width%;left:$left%;border: 1px solid $border;";
		}
		else
		{
			$style="background: none !important; border: 1px solid $border;background-color: #FFF !important; width:$width%;left:$left%";
		}
        $_title = sprintf(__(' Project: %s from %s to %s: %s', true), $name, date('d-m-Y', $start), date('d-m-Y', $end), $comp.'%');   
        return $out . "<div id=\"line-$pos-$id\" onclick='showPhaseDetail(event, $id)' class=\"gantt-line-$pos hover-tooltip\" title=\"$_title"
                . "\" style=\"".$style."\""
                . ( $psor ? " rel=\"$psor\"" : '') . ">
                <div id=\"hover-data\" style=\"display: none\">
                    <p class=\"hover-data-name\">" . h($name) . "</p>
                    <p class=\"hover-data-start\">" . date($format, $start) . "</p>
                    <p class=\"hover-data-end\">" . date($format, $end) . "</p>
                    <p class=\"hover-data-comp\">" . $comp . "</p>
                    <p class=\"hover-data-assign\">" . h($assign) . "</p>
                </div>
                <em style=\"background-color: $color; height: 4px; display: block; width:$comp%;left:$left%\"></em>" 
                . ( $addSpan ? "<span style=\"background: none !important;\"><span style=\"background: none !important;\"></span></span>" : '') 
                . "
                </div>";
        //return $out . "<div id=\"line-$pos-$id\" class=\"gantt-line-$pos hover-tooltip\" title=\""
//                . "\" style=\"".$style."\""
//                . ( $psor ? " rel=\"$psor\"" : '') . ">
//                <div class=\"arrow-down\" style=\"background-color: $border !important;
//                    left: -2px;display:".$_isTask."\"></div>
//                <div id=\"hover-data\" style=\"display: none\">
//                    <p class=\"hover-data-name\">" . h($name) . "</p>
//                    <p class=\"hover-data-start\">" . date($format, $start) . "</p>
//                    <p class=\"hover-data-end\">" . date($format, $end) . "</p>
//                    <p class=\"hover-data-comp\">" . $comp . "</p>
//                    <p class=\"hover-data-assign\">" . h($assign) . "</p>
//                </div>
//                <em style=\"background-color: $color; height: 4px; display: block; width:$comp%;left:$left%\"></em>" 
//                . ( $addSpan ? "<span style=\"background: none !important;\"><span style=\"background: none !important;\"></span></span>" : '') 
//                . "
//                <div class=\"arrow-down\" style=\"background-color: $border !important;right: -2px;display:".$_isTask."\"></div>
//                </div>";
		 //END
    }

    /**
     * End draw a chart line for phase plan.
     *
     * @param string $_name, the project name
     * 
     * @return void
     */
    public function drawEnd($_name, $isName = true,$program=null,$status=null, $idElement = 0) {
        extract($this->_runtime);
        //@Huu Change
        if($isName == true){
			if($this->params['action']=='projects_vision'&&$this->params['controller']=='projects')
			{
				$program="<td class=\"gantt-name\"><div>" . $program . "</div></td>";
				$status="<td class=\"gantt-name\"><div>" . $status . "</div></td>";
			}
            $list .= "<tr class=\"gantt-row gantt-child\">
						".$program."
                        <td class=\"gantt-name\"><div>" . $_name . "</div></td>
						".$status."
                 </tr>";
        } else {
            $list .= "<tr class=\"gantt-row gantt-child\"></tr>";
        }
        $chart.= "<tr class=\"gantt-child pr-" . $idElement . "\" onclick=\"showStonesDetails('pr-" . $idElement . "', " . $idElement . ")\">
                        <td class=\"gantt-node\">
                                <div class=\"gantt-line\">
                                    <table><tr>$col</tr></table>
                                    $line
                                </div>
                        </td>
                </tr>";
        $line = '';
        $current = 0;
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }

    /**
     * Draw html for gantt that in runtime configuration.
     * 
     * @return void
     */
    public function end($check = true, $pLists = false, $twoTable = false, $viewStone = true) {
        extract($this->_runtime);
        if ($stone && $viewStone) {
            $chart.= "<tr class=\"gantt-ms\">
                            <td>
                              <div class=\"gantt-line\">$stone</div>
                            </td>
                    </tr>";
            if($check == true){
                $list.= "<tr class=\"gantt-ms\">
                            <td colspan=\"5\">
                              <div class=\"gantt-line\">&nbsp;</div>
                            </td>
                    </tr>";
            }
        }

        if (!empty($chart)) {
            $chart.= '</table>';
        }
        if (!empty($list)) {
            $list.= '</table>';
        }

        if ($staff) {
            $list.= '<div class="gantt-content-wrapper"><table class="gantt-list">' . $staff[0] . '</table></div>';
            $chart.= '<div class="gantt-content-wrapper"><table class="gantt">' . $staff[1] . '</table></div>';
        }

        if($pLists == true){
             $setClass = ($twoTable == true) ? 'twoTable' : '';
             echo "<div class=\"gantt-wrapper\">
                        <div id=\"mcs_container\" class=\"gantt-side gantt-side-$displayList\" style=\"display: none;\">
                                <div class=\"customScrollBox\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                
                                            </div>
                                        </div>
                                </div>
                        </div>
                        <div id=\"mcs1_container\" class=\"gantt-chart gantt-$type gantt-chart-$displayList " .$setClass. "\">
                                <div class=\"customScrollBox\" id=\"x-scroll\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                <div class=\"gantt-chart-wrapper\">$chart</div>
                                            </div>
                                        </div>
                                </div>
                        </div>
                        <div class=\"gantt-scroll-place\"></div>
             </div>";
        } else {
            echo "<div class=\"gantt-wrapper\">
                        <div id=\"mcs_container\" class=\"gantt-side gantt-side-$displayList\">
                                <div class=\"customScrollBox\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                $list
                                            </div>
                                        </div>
                                </div>
                        </div>
                        <div id=\"mcs1_container\" class=\"gantt-chart gantt-$type gantt-chart-$displayList\">
                                <div class=\"customScrollBox\" id=\"x-scroll\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                <div class=\"gantt-chart-wrapper\">$chart</div>
                                            </div>
                                        </div>
                                </div>
                        </div>
                        <div class=\"gantt-scroll-place\"></div>
             </div>";
        }
        
        $this->_runtime = array();
    }
    
    /**
     * Draw html for a chart line.
     *
     * @param string $name, the line display name
     * @param integer $start, a Unix timestamp value of date start
     * @param integer $end, a Unix timestamp value of date end
     * @param string $format, the date format display in chart
     * @param string $color, the html syntax color
     * 
     * @return string output
     */
    protected function _draw($name, $pos, $time, $total, $start, $end, $format, $color = '#000', $isPhase = false, $isID) {
        $left = max(0, round((floor((($start - $time)) / 86400) / $total) * 100, 2));
        $width = round((floor((($end - $start) + 86400) / 86400) / $total) * 100, 2);
        if ($left + $width > 100) {
            $width -= ($width + $left) - 100;
        }
        $start = ($start =='') ? '' : date($format, $start);
        $end = ($end =='') ? '' : date($format, $end);
        //debug($name);
        if($isPhase == true){
            if($pos === 'n'){
                return "<div class=\"gantt-line-$pos\" title=\"" . sprintf(__(' Phase: %s from %s to %s', true), $name, $start, $end) . "\" style=\"border-top: 1px dashed #FFFFFF; border-bottom: 1px dashed #FFFFFF; background-color:$color; width:$width%;left:$left%\"></div>";
            } else {
                return "<div class=\"gantt-line-$pos\" title=\"" . sprintf(__(' Phase: %s from %s to %s', true), $name, $start, $end) . "\" style=\"background-color:$color;width:$width%;left:$left%\"></div>";
            }
        } else {
			if($isID === null)
			$isID = -1;
            return "<div class=\"gantt-line-$pos\" onclick=\"showPhaseDetail($isID);\" title=\"" . sprintf(__(' Project: %s from %s to %s', true), $name, $start, $end) . "\" style=\"background-color:$color;width:$width%;left:$left%\"></div>";
        }
    }

    /**
     * Get days of month
     *
     * @param integer $_m, the number of month
     * @param integer $_y, the number of year
     * 
     * @return integer
     */
    public function daysOfMonth($_m, $_y) {
        $timestamp = mktime(0, 0, 0, $_m, 1, $_y);
        return date("t", $timestamp);
    }

    /**
     * Parse input data
     *
     * @param array $data
     * @param boolean $strict
     * 
     * @return string,html class name for input
     */
    public function parseData(&$data, $strict = false) {
        if ($strict === true) {
            if (empty($data['consumed']) && $data['consumed'] != '0') {
                $data['consumed'] = $this->na;
                $data['forecast'] = floatval($data['validated'] + $data['remains']);
            } else {
                $data['forecast'] = floatval($data['consumed']);
            }
        } elseif ($data['consumed'] == 0 && !isset($data['has'])) {
            $data['consumed'] = $this->na;
        }
        $class = '';
        if ($data['validated'] < $data['forecast']) {
            $class = ' gantt-invalid';
        }
        return $class;
    }

    /**
     * Draw Staffing
     *
     * @param array $data
     * 
     * @return void
     */
    public function drawStaffing($staffings, $displaySummary = true, $showType = false) {
        $this->_runtime['staff'] = array('', '');

        $staffings['summary'] = array(
            'id' => 'summary',
            'name' => __('Summary', true),
            'func' => 0,
            'data' => array()
        );

        $estimation = $validated = $consumed = $remains = $forecast = array_fill_keys(array_keys($staffings), '');
        $year = array();
        
        // sửa chổ này
        $default = array(
            'estimation' => 0,
            'validated' => 0,
            'remains' => 0,
            'consumed' => null,
            'forecast' => 0
        );
        $titles = '';
        $md = __('M.D', true);
        foreach ($this->_months as $data) {
            list($days, $m, $y) = $data;
            $titles.= "<td class=\"gantt-d$days\"><div>$md</div></td>";
            $date = strtotime($y . '-' . $m . '-1');
            $staffings['summary']['data'][$date] = $default;
            reset($staffings);
            while (list($key, $staffing) = each($staffings)) {
                if (!isset($year[$key][$y])) {
                    $year[$key][$y] = $default;
                }
                $input = $default;
                if (isset($staffing['data'][$date])) {
                    $input = array_merge($input, $staffing['data'][$date]);
                }
                // sửa chổ này
                $class = $this->parseData($input, $key === 'summary' ? null : true);
                $summary = $key === 'summary' ? '' : 'gantt-input';
                $estimation[$key].= "<td rel=\"e-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['estimation']}</div></td>";
                $validated[$key].= "<td rel=\"v-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['validated']}</div></td>";
                $consumed[$key].= "<td rel=\"c-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['consumed']}</div></td>";
                $remains[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['remains']}</div></td>";
                $forecast[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days$class\"><div>{$input['forecast']}</div></td>";
                foreach ($default as $k => $v) {
                    // sửa chổ này
                    if ($k == 'consumed' && $input[$k] != $this->na) {
                        $year[$key][$y]['has'] = true;
                    }
                    $year[$key][$y][$k] += $input[$k];
                    $staffings['summary']['data'][$date][$k] += $input[$k];
                }
            }
        }

        //pr($staffings);
        //exit();

        $count = array(3 + count($year['summary']), count($this->_months));
        
        // sửa chổ này
        $_title = array(
            'estimation' => __('Estimation', true),
            'validated' => __('Validated', true),
            'remains' => __('Postponed', true),
            'consumed' => __('Consumed', true),
            'forecast' => __('Forecast', true)
        );
        $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";

        $image = '<img src="' . $this->url('/img/front/add.gif') . '" class="gantt-image" />';
        $staffings = array_merge(array('summary' => array()), $staffings);
        if (!$displaySummary) {
            unset($staffings['summary']);
        }
        $output = array();
        
        // sửa vị trí
        foreach ($staffings as $key => $staffing) {

            $summary = $key === 'summary' ? 'gantt-summary' : '';
            $staffGantt = "<tr class=\"gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary\">
                                   $titles
                                   <tr class=\"gantt-num\">{$estimation[$key]}</tr>
                                   <tr class=\"gantt-num\">{$validated[$key]}</tr>
                                   <tr class=\"gantt-num\">{$remains[$key]}</tr>
                                   <tr class=\"gantt-num\">{$consumed[$key]}</tr>
                                   <tr class=\"gantt-num gantt-forecast\">{$forecast[$key]}</tr>
                                   <tr class=\"gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                    </tr>";
            $estimation[$key] = $validated[$key] = $consumed[$key] = $remains[$key] = $forecast[$key] = '';
            $total = $default;

            $estimation[$key].= "<td rowspan=\"5\" class=\"gantt-name\"><div rel=\"{$staffing['func']}\"><a href=\"javascript:void(0)\">$image{$staffing['name']}</a></div></td>";
            foreach ($_title as $k => $v) {
                ${$k}[$key].= "<td class=\"gantt-func\"><div>{$v}</div></td>";
            }
            if ($titles) {
                switch ($showType) {
                    case 1: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Profit center', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            break;
                        }
                    case 5: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Project', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            break;
                        }
                    default: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Function', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                        }
                }
            }
            foreach ($year[$key] as $_y => $_year) {
                $class = $this->parseData($_year);

                $estimation[$key].= "<td rel=\"e-$_y\"><div>{$_year['estimation']}</div></td>";
                $validated[$key].= "<td rel=\"v-$_y\"><div>{$_year['validated']}</div></td>";
                $remains[$key].= "<td rel=\"r-$_y\"><div>{$_year['remains']}</div></td>";
                $consumed[$key].= "<td rel=\"c-$_y\"><div>{$_year['consumed']}</div></td>";
                $forecast[$key].= "<td rel=\"f-$_y\" class=\"$class\"><div>{$_year['forecast']}</div></td>";
                foreach ($default as $k => $v) {
                    $total[$k] += $_year[$k];
                }
                if ($titles) {
                    $titles .= "<td><div>$_y</div></td>";
                }
            }
            if ($titles) {
                $titles.= "<td><div>Total</div></td>";
                $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";
            }
            $class = $this->parseData($total);

            $estimation[$key].= "<td rel=\"e-total\"><div>{$total['estimation']}</div></td>";
            $validated[$key].= "<td rel=\"v-total\"><div>{$total['validated']}</div></td>";
            $remains[$key].= "<td rel=\"r-total\"><div>{$total['remains']}</div></td>";
            $consumed[$key].= "<td rel=\"c-total\"><div>{$total['consumed']}</div></td>";
            $forecast[$key].= "<td rel=\"f-total\" class=\"$class\"><div>{$total['forecast']}</div></td>";

            $staffList = "<tr class=\"gantt-staff\">
                            <td class=\"gantt-node gantt-child\" colspan=\"5\">
                              <table rel=\"list-{$staffing['id']}\" class=\"$summary\">
                                   $titles
                                   <tr>{$estimation[$key]}</tr>
                                   <tr>{$validated[$key]}</tr>
                                   <tr>{$remains[$key]}</tr>
                                   <tr>{$consumed[$key]}</tr>
                                   <tr class=\"gantt-forecast\">{$forecast[$key]}</tr>
                                   <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                    </tr>";

            if ($titles) {
                $this->_runtime['staff'][0].= $staffList;
                $this->_runtime['staff'][1].= $staffGantt;
            } else {
                $output[] = array($staffList, $staffGantt);
            }
            $titles = '';
        }
        return json_encode($output);
    }

    /**
     * Get month Staffing
     * 
     * @return list month
     */
    function getMonths() {
        return $this->_months;
    }

}
