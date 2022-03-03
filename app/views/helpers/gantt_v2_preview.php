<?php

/**
 * PHP versions 5
 *
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
class GanttV2PreviewHelper extends AppHelper {
    public $helpers = array('UserFile');

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
     * Constructor.
     *
     */
    public function __construct() {
        parent::__construct();
        App::import('Core', 'Validation');
        $this->na = '0.00';
    }

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     *
     * @return integer, a Unix timestamp value
     */
    public function toTime($date) {
        if (empty($date) || !Validation::date($date, 'ymd')) {
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
    protected function buildGroup($syear, $eyear, $type = '3years'){
        $num = $col = '';
        for($i = $syear; $i <= $eyear; $i++){
            //each year has 2 semeters
            if( $type == '10years' ){
                $cls = 29 == $this->daysOfMonth(2, $i) ? 'leap' : 'normal';
            }
            else {
                $cls = 29 == $this->daysOfMonth(2, $i) ? 366 : 365;
            }
            $num .= "<td class=\"gantt-d$cls\"><div>$i</div></td>";
            $col .= "<td class=\"gantt-d$cls\"><div>&nbsp;</div></td>";
        }
        return array($num, $col);
    }
    public function create($type, $start, $end, $stones = array(), $displayList = true, $format = 'd-m-Y', $noPhasename = false) {
        $chart = $list = $staff = $head = $num = '';
        switch ($type) {
            case 'week':
            case 'date':
                $start-= 7 * DAY;
                $end+= 7 * DAY;
                $_t = intval(date('w', $start));
                if ($_t > 1) {
                    $start = $start - (($_t - 1) * DAY);
                } elseif ($_t == 0) {
                    $start = $start - (6 * DAY);
                }
                $_t = intval(date('w', $end));
                if ($_t !== 0) {
                    $end = $end + ((7 - $_t) * DAY);
                }
                $end += DAY;
                break;
            case 'month':
                if( $noPhasename ){
                    $start = strtotime('first day of this month', $start);
                    $end = strtotime('last day of this month', $end);
                } else {
                    $start = strtotime('first day of last month', $start);
                    $end = strtotime('last day of next month', $end);
                }
                break;
            case 'year':
                $start = strtotime(date('Y', $start) . "-1-1");
                $end = strtotime((date('Y', $end) + (intval(date('m', $end)) == 12 ? 1 : 0)) . "-12-31");
                break;
            case '2years':
            case '3years':
            case '4years':
            case '5years':
            case '10years':
                if( intval(date('m', $start)) == 1 ){
                    //lui ve 1 nam
                    $start = strtotime((date('Y', $start)-1) . "-1-1");
                } else {
                    $start = strtotime(date('Y', $start) . "-1-1");
                }
                $end = strtotime((date('Y', $end) + (intval(date('m', $end)) == 12 ? 1 : 0)) . "-12-31");
                $group = intval(substr($type, 0, 1));
                break;
            default :
                $this->cakeError('error404');
        }

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

        $total = ceil(($end - $start) / DAY);

        list($_d, $_m, $_y) = explode('-', date('j-n-Y', $start));


        $_n = date('j-n-Y');
        $_days = $this->daysOfMonth($_m, $_y);
        $col = $line = $stone = '';
        $i = 1;
        $current = 0;

        /* Milestone */
        $duplicate = 0;
        $_index = 0;
		$re_stones = array();
        foreach ($stones as $idx => $_stones) {
			list($k, $desc, $valid) = $_stones;
			if( !empty($k)){
				$re_stones[$idx] = $_stones;
				if(!empty($re_stones[$idx - 1]) && ($re_stones[$idx - 1][0] == $stones[$idx][0])){
					$pre_desc = $re_stones[$idx - 1][1];
					$re_stones[$idx][1] = $_stones[1] .', '. $pre_desc;
					unset($re_stones[$idx - 1]);
				}
			}
		}
		$prev_stone = array();
		$prev_left = -1;
		$pos_stone = 'line-top';
        foreach ($re_stones as $idx => $_stones) {
			list($k, $desc, $valid) = $_stones;
			
			if( !empty($k)){
				$left = max(0, round((floor((($k - $start)) / DAY) / $total) * 100, 2));
			
				if(!empty($prev_stone)){
					$date1 = new DateTime(date('Y-m-d',$_stones[0]));
					$date2 = new DateTime(date('Y-m-d',$prev_stone[0]));
					$interval = $date1->diff($date2);
					$diff_time = $interval->days;
					
					if($diff_time > 0 && $diff_time < 4){
						$pos_stone = $pos_stone == 'line-top' ? 'line-bottom' : 'line-top';
					}else if($diff_time > 3){
						if($pos_stone == 'line-bottom') $pos_stone = 'line-top';
					}
				}
				if(!empty($valid)){
					$stone .= "<div class=\"gantt-msi gantt-msi-green gantt-ms $pos_stone\" style=\"left:$left%;top:0px\" data-index='" . $_index ."' title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span data-index='" . $_index ."'>$desc</span></div>\n";
				} else {
					$currentDate = strtotime(date('d-m-Y', time()));
					$k = strtotime(date('d-m-Y', $k));
					if($currentDate > $k){
						$stone .= "<div class=\"gantt-msi gantt-ms gantt-msi-red  gantt-msi-mi $pos_stone\" data-index='" . $_index ."' style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span data-index='" . $_index ."'>$desc</span></div>\n";
					} elseif($currentDate < $k){
						$stone .= "<div class=\"gantt-msi gantt-msi-blue gantt-ms $pos_stone\" data-index='" . $_index ."' style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span data-index='" . $_index ."'>$desc</span></div>\n";
					} else {
						$stone .= "<div class=\"gantt-msi gantt-msi-orange gantt-ms $pos_stone\" data-index='" . $_index ."' style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span data-index='" . $_index ."'>$desc</span></div>\n";
					}
				}
				$prev_stone = $_stones;
				$prev_left = $left; 
				$_index++;
				
			}
        }
        if ($duplicate && mb_strlen(trim($desc)) >= 20) {
            $duplicate+=1;
        }

        $this->_months = array();
        //quyet here
		$loop_y = 0;
        if( isset($group) ){
            $cyear = date('Y', $start);
            $eyear = date('Y', $end);
            $step = $group;
            while( $cyear <= $eyear ){
                $next = $cyear + $step - 1;
                if( $next > $eyear ){
                    $next = $eyear;
                }
                $header = $cyear . '-' . $next;
                if( $cyear == $next ){
                    $header = $cyear;
                    $group = 1;
                }
                $head .= "<td id=\"year-$cyear-$next\" colspan=\"$group\"><div>$header</div></td>";
                list($_num, $_col) = $this->buildGroup($cyear, $next, $type);
                $num .= $_num;
                $col .= $_col;
                $cyear += $step;
            }
        } else
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
                    $_t += (7 * DAY);
                    list($_d, $_m, $_y) = explode('-', date('j-n-Y', $_t));
                }
                if (($i <= $total && $mod == 0)) {
                    $week = date('W', $_t);
                    $_month = __( date('F', $_t), true);
                    $tran_txt = __('Week', true);
                    if ($type == 'date') {
                        $head.="<td colspan=\"7\"><div>$tran_txt $week . $_month $_y</div></td>";
                    } else {
                        $head.="<td><div>$_month . $_y</div></td>";
                        $num .= "<td id=\"" . Inflector::slug("week $week $_m $_y") . "\"><div>$tran_txt $week</div></td>";
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
					$loop_y++;
					if($_m == 12 || $i >= $total-$step ){
						$head.="<td colspan=\"$loop_y\" class=\"gantt-d$_days\">";
						if($loop_y > 2){
							$head.="<div>$_y</div>";
						}
						$head.="</td>";
						$loop_y = 0;
					}
                    $num.= "<td class=\"gantt-d$_days\" id=\"" . Inflector::slug("month $_m $_y") . "\"><div>{$month[$_m]}</div></td>";
                    $this->_months[] = array($_days, $_m, $_y);
                } elseif ($type == 'year') {
                    $num .= "<td id=\"" . Inflector::slug("month $_m $_y") . "\"><div>{$month[$_m]}</div></td>";
                }
                $_m++;
                if ($_m > 12) {
                    $_m = 1;
                    if ($type == 'year') {
                        $head .= "<td colspan=\"12\"><div>$_y</div></td>";
                    }
                    $_y++;
                }
                if ($type == 'month' || $type == 'year') {
                    $col .= "<td class=\"gantt-d$_days\"><div>&nbsp;</div></td>\n";
                }
                $_days = $this->daysOfMonth($_m, $_y);
            }
            $i+=$step;
        }
        $displayList = intval($displayList);
        if ($displayList) {
            $list .= "<table class=\"gantt-list gantt-list-primary " . ($noPhasename ? 'team-plus' : '') . "\">
                            <tr class=\"gantt-head\">
                                        <td><div>" . ( $noPhasename ? '&nbsp;' : __('Phase name', true) ) . "</div></td>
                                        <td><div>" . ( $noPhasename ? '&nbsp;' : __('Start Date', true) ) . "</div></td>
                                        <td><div>" . ( $noPhasename ? '&nbsp;' : __('End Date', true) ) . "</div></td>
                                        <td><div>" . ( $noPhasename ? '&nbsp;' : __('Start Real Date', true) ) . "</div></td>
                                        <td><div>" . ( $noPhasename ? '&nbsp;' : __('End Real Date', true) ) . "</div></td>
                             </tr>";
        }
        $chart .= "<table class=\"gantt gantt-primary\">
            <tr>
                <td class=\"gantt-node gantt-node-head\">
                        <table>";
        $chart.= "<tr class=\"gantt-head\">$head</tr>";
        $chart.= "<tr class=\"gantt-num\">$num</tr>";
        if ($type == 'year') {
            $chart.= "<tr class=\"gantt-num-date\">$col</tr>";
        }
        $chart.= "</table>
                </td>
            </tr>
        </tr>";
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
    public function draw($_id, $_name, $_psor, $_start, $_end, $_rstart, $_rend, $color, $class = 'parent', $comp = null, $assign = null, $viewPhase = true, $task_status = null) {

        extract($this->_runtime);

        $realDate = (!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0);
        $initDate = (!empty($_end) && $_end > 0) && (!empty($_start) && $_start > 0);


        if ($displayList) {
            $list .= "<tr class=\"gantt-row gantt-$class line-$_id \">";
			if($initDate){
                   $list .= "<td class=\"gantt-name\"><div>" . $_name . "</div></td>
                        <td><div>" . date($format, $_start) . "</div></td>
                        <td><div>" . date($format, $_end) . "</div></td>";
			} else {
                $list .="<td><div>&nbsp;</div></td>
                        <td><div>&nbsp;</div></td>";
            }
            if ($realDate) {
                $list .="
                        <td><div>" . date($format, $_rstart) . "</div></td>
                        <td><div>" . date($format, $_rend) . "</div></td>";
            } else {
                $list .="<td><div>&nbsp;</div></td>
                        <td><div>&nbsp;</div></td>";
            }
            $list .="</tr>";
        }
        if($viewPhase == false){
            $chart.= "<tr class=\"gantt-$class wd-$_id hideGantt\">
                        <td class=\"gantt-node\">
                                <div class=\"gantt-line\">
                                    <table><tr>$col</tr></table>";
			if ($initDate) {
				 $chart.= $this->_draw($_id, $_name, $_psor, 'n', $start, $total, $_start, $_end, 'd/m', $color, $class == 'parent', $comp, $assign);
			}
        } else {
            $chart.= "<tr class=\"gantt-$class wd-$_id\">
                        <td class=\"gantt-node\">
                                <div class=\"gantt-line\">
                                    <table><tr>$col</tr></table>";
			if ($initDate){
				$chart.=$this->_draw($_id, $_name, $_psor, 'n', $start, $total, $_start, $_end, 'd/m', $color, $class == 'parent', $comp, $assign, $viewPhase, $task_status);
			}
        }
        if ($realDate) {
            $chart.=$this->_draw($_id, $_name, $_psor, 's', $start, $total, $_rstart, $_rend, 'd/m', $color, $class == 'parent', $comp, $assign);
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
    public function drawLine($_name, $_start, $_end, $_rstart, $_rend, $color) {
        extract($this->_runtime);
        $line.= $this->_draw($_name, "n", $start, $total, $_start, $_end, $format, $color);
        if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
            $line.=$this->_draw($_name, "s", $start, $total, $_rstart, $_rend, $format, $color);
        }
        $current = $current ? 0 : 1;
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }

    /**
     * End draw a chart line for phase plan.
     *
     * @param string $_name, the project name
     *
     * @return void
     */
    public function drawEnd($_name) {
        extract($this->_runtime);
        $list .= "<tr class=\"gantt-row gantt-child\">
                        <td class=\"gantt-name\"><div>" . $_name . "</div></td>
                 </tr>";
        $chart.= "<tr class=\"gantt-child\">
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

    public function drawMilestones($stones){
        extract($this->_runtime);
        $stoneHtml = '';
        foreach ($stones as $_stones) {
            list($k, $desc, $valid) = $_stones;
            $left = max(0, round((floor((($k - $start)) / DAY) / $total) * 100, 2));
            if(!empty($valid)){
                $stoneHtml .= "<div class=\"gantt-msi gantt-msi-green gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
            } else {
                $currentDate = strtotime(date('d-m-Y', time()));
                $k = strtotime(date('d-m-Y', $k));
                if($currentDate > $k){
                    $stoneHtml .= "<div class=\"gantt-msi gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                } elseif($currentDate < $k){
                    $stoneHtml .= "<div class=\"gantt-msi gantt-msi-blue gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                } else {
                    $stoneHtml .= "<div class=\"gantt-msi gantt-msi-orange gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
                }
            }
        }
        $chart .= "<tr class=\"gantt-ms\">
                            <td>
                              <div class=\"gantt-line\">$stoneHtml</div>
                            </td>
                    </tr>";
        $this->_runtime = compact('type', 'staff', 'current', 'line', 'displayList', 'stone', 'total', 'start', 'col', 'end', 'format', 'chart', 'list');
    }

    /**
     * Draw html for gantt that in runtime configuration.
     *
     * @return void
     */
    public function end() {
        extract($this->_runtime);
        if ($stone) {
            $chart.= "<tr class=\"gantt-ms\">
                            <td>
                              <div class=\"gantt-line\">$stone</div>
                            </td>
                    </tr>";
            if ($displayList) {
                $list.= "<tr class=\"gantt-ms\">
                            <td colspan=\"5\">
                              <div class=\"gantt-line\">&nbsp;</div>
                            </td>
                    </tr>";
            }
        }

        if ($chart) {
            $chart.= '</table>';
        }
        if ($list) {
            $list.= '</table>';
        }

        if ($staff) {
            $list.= '<div class="gantt-content-wrapper"><table class="gantt-list">' . $staff[0] . '</table></div>';
            $chart.= '<div class="gantt-content-wrapper"><table class="gantt">' . $staff[1] . '</table></div>';
        }

        if ($list) {
            $list = "<div id=\"mcs_container\" class=\"gantt-side gantt-side-$displayList\">
                                <div class=\"customScrollBox\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                $list
                                            </div>
                                        </div>
                                </div>
                        </div>";
        }

        echo "<div class=\"gantt-wrapper\">
                        $list
                        <div id=\"mcs1_container\" class=\"gantt-chart gantt-$type gantt-chart-$displayList\">
                                <div class=\"customScrollBox\">
                                        <div class=\"container\">
                                            <div class=\"content\">
                                                <div class=\"gantt-chart-wrapper\">$chart</div>
                                            </div>
                                        </div>
                                </div>
                        </div>
                        <div class=\"gantt-scroll-place\"></div>
             </div>";
        $this->_runtime = array();
    }
    private function draw_line_progress($value){
        $_html = '';
        $_color_gray = '#E2E6E8';
        $_color_green = array('#6EAF79', '#89BB92', '#AACCB0', '#C1D6C5', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9', '#D7E2D9' );
        $_color_blue =  array('#6FB0CF', '#87BFDA', '#A3CCE0', '#BBDAE9', '#D6E8F0');
        $_use_color = $value > 50 ? $_color_green : $_color_blue;
        $_index = 1; $_current_color = '';
        for( $_index = 1; $_index <= 10; $_index++){
            $_current_color = $_index*10 <= $value ? $_use_color[(intval($value/10) - $_index)] : $_color_gray;
            $_html .= '<span class="progress-node" style="background: ' . $_current_color . '"></span>';
        }
        return $_html;
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

    protected function _draw($id, $name, $psor, $pos, $time, $total, $start, $end, $format, $color = '#000', $addSpan = false, $comp = null, $assign = null, $viewPhase = true, $task_status = null) {
        $left = max(0, (((($start - $time)) / DAY) / $total) * 100);
        $width = (((($end - $start) + DAY) / DAY) / $total) * 100;
        if ($left + $width > 100) {
            $width -= ($width + $left) - 100;
        }
        $out = "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"right:" . (100 - $left) . "%\">" . date($format, $start) . "</div>";
        $out.= "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"left:" . ($left + $width) . "%\">" . date($format, $end) . "</div>";
        $assign = !empty($assign) ? $assign : '';
        // $out = '';
        $left_desc = $left;
		$predecessor = '';
		if($psor){
			$psor_task = explode('-', $psor);
			if(!empty( $psor_task[1])) $predecessor = $psor_task[1] . ' - ';
		}
        $out.= "<div class=\"gantt-line-desc gantt-line-$pos gantt-line-tip\" style=\"left:". ($left_desc) ."%\">". h($predecessor) . h($name) . "</div>";
        
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
			$style="width:$width%;left:$left%;border: 2px solid $border;";
		}
		else
		{
			$style="background: none !important; border: 2px dashed $border;background-color: #FFF !important; width:$width%;left:$left%";
		}
		$node_item = '';
        $bg_color = $border;
        if($start && $end && $start == $end){
			$bg_color= '#F05352';
			if($end >= time()){
				$bg_color = '#6EAF79';
			}
            $node_item = "<span style=\"background-color: $bg_color !important; width: 10px; height: 10px; transform: rotate(45deg);display: inline-block; position: relative;top: -3px;left: -5px;\"></span>";
        }
        return $out . "<div id=\"line-$pos-$id\" class=\"gantt-line-$pos hover-tooltip\" title=\""
                . "\" style=\"".$style."\""
                . ( $psor ? " rel=\"$psor\"" : '') . ">". $node_item ."
                <div class=\"arrow-down\" style=\"background-color: $border !important;
                    left: 2px;display:".$_isTask."\"></div>
                <div id=\"hover-data\" style=\"display: none\">
                    <p class=\"hover-data-name\">" . h($name) . "</p>
                    <p class=\"hover-data-start\">" . date($format, $start) . "</p>
                    <p class=\"hover-data-end\">" . date($format, $end) . "</p>
                    <p class=\"hover-data-comp\">" . $comp . "</p>
                    <p class=\"hover-data-assign\">" . h($assign) . "</p>
                </div>
                <em style=\"background-color: $color; position: relative; top: 2px; left: 2px; height: 8px; border-radius: 4px;display: block; width: calc( $comp% - 4px);\"></em>"
                . ( $addSpan ? "<span style=\"background: none !important;\"><span style=\"background: none !important;\"></span></span>" : '')
                . "
                <div class=\"arrow-down\" style=\"background-color: $border !important;right: 2px;display:".$_isTask."\"></div>
                </div>";
		 //END
    }

    //protected function _drawWithSuccess($id, $name, $psor, $pos, $time, $total, $start, $end, $format, $color = '#000', $addSpan = false, $comp = null, $assign = null) {
//        $left = max(0, (((($start - $time)) / DAY) / $total) * 100);
//        $width = (((($end - $start) + DAY) / DAY) / $total) * 100;
//        if ($left + $width > 100) {
//            $width -= ($width + $left) - 100;
//        }
//        $out = "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"right:" . (100 - $left) . "%\">" . date($format, $start) . "</div>";
//        $out.= "<div class=\"gantt-line-$pos gantt-line-tip\" style=\"left:" . ($left + $width) . "%\">" . date($format, $end) . "</div>";
//        if ($pos == 'n') {
//            $assign = !empty($assign) ? '- ('.$assign.')' : '';
//            $out.= "<div class=\"gantt-line-desc\" style=\"left:$left%\">" . h($name) . " - (" . $comp . "%) " .$assign. "</div>";
//        }
//        return $out . "<div id=\"line-$pos-$id\" class=\"gantt-line-$pos\" title=\""
//                . "\" style=\"background-color:$color;width:$width%;left:$left%\""
//                . ( $psor ? " rel=\"$psor\"" : '') . ">" . ( $addSpan ? "<span><span></span></span>" : '') . "</div>";
//    }

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
            if (!empty($data['consumed']) && $data['consumed'] != '0') {
                //$data['forecast'] = floatval($data['consumed'] + $data['remains']);
            } else {
                $data['consumed'] = $this->na;
                //$data['forecast'] = floatval($data['remains']);
            }
        } elseif ($data['consumed'] == 0 && !isset($data['has'])) {
            $data['consumed'] = $this->na;
        }
        $class = '';
        //if ($data['validated'] > $data['forecast']) {
        if (!empty($data['totalWorkload']) && !empty($data['capacity']) && $data['totalWorkload'] > $data['capacity']) {
            $class = 'gantt-invalid';
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
            'name' => __('Total', true),
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
        $md = !empty($this->employee_info['Company']['unit']) ? $this->employee_info['Company']['unit'] : 'M.D';
        $md = __($md, true);
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
				$input['estimation']=$this->formatNumber($input['estimation']);
				$input['validated']=$this->formatNumber($input['validated']);
				$input['consumed']=$this->formatNumber($input['consumed']);
				$input['remains']=$this->formatNumber($input['remains']);
				$input['forecast']=$this->formatNumber($input['forecast']);
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
					$year[$key][$y][$k]=$this-formatNumber($year[$key][$y][$k]);
                    $staffings['summary']['data'][$date][$k] += $input[$k];
					$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k]);
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
				$_year['estimation']=$this->formatNumber($_year['estimation']);
				$_year['validated']=$this->formatNumber($_year['validated']);
				$_year['remains']=$this->formatNumber($_year['remains']);
				$_year['consumed']=$this->formatNumber($_year['consumed']);
				$_year['forecast']=$this->formatNumber($_year['forecast']);
                $estimation[$key].= "<td rel=\"e-$_y\"><div>{$_year['estimation']}</div></td>";
                $validated[$key].= "<td rel=\"v-$_y\"><div>{$_year['validated']}</div></td>";
                $remains[$key].= "<td rel=\"r-$_y\"><div>{$_year['remains']}</div></td>";
                $consumed[$key].= "<td rel=\"c-$_y\"><div>{$_year['consumed']}</div></td>";
                $forecast[$key].= "<td rel=\"f-$_y\" class=\"$class\"><div>{$_year['forecast']}</div></td>";
                foreach ($default as $k => $v) {
                    $total[$k] += $_year[$k];
					$total[$k]=$this->formatNumber($total[$k]);
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
			$total['estimation']=$this->formatNumber($total['estimation']);
			$total['validated']=$this->formatNumber($total['validated']);
			$total['remains']=$this->formatNumber($total['remains']);
			$total['consumed']=$this->formatNumber($total['consumed']);
			$total['forecast']=$this->formatNumber($total['forecast']);
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
                                   <tr><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
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
    public function drawStaffing2($staffings, $employee_info, $budgetMdTeams, $freezeTeams, $displaySummary = true, $showType = false , $displayTeamPlus, $profile = false, $showAllPicture = false, $isPlus = false) {
		$profile = $profile == '' ? false : true ;
        $allWorkload = 0;
        foreach($staffings as $staffing){
            if(!empty($staffing['data'])){
                foreach($staffing['data'] as $values){
                    $allWorkload += !empty($values['validated']) ? $values['validated'] : 0;
                }
            }
        }
        $displayCapacity = false;
        if($showType == false || $showType == 1){
            $displayCapacity = true;
        }
        $this->_runtime['staff'] = array('', '');
        $staffings['summary'] = array(
            'id' => 'summary',
            'name' => __('Total', true),
            'func' => 0,
            'data' => array()
        );
        $resource = $resource_theoretical = $fte = $estimation = $validated = $consumed = $remains = $capacity = $absence = $totalWorkload = $assignEm = $assignPc = array_fill_keys(array_keys($staffings), '');
        $year = array();
        if($displayCapacity){
            if($showType == false){
                $default = array(
                    'validated' => 0,
                    'consumed' => null,
                    'remains' => 0,
                    'capacity' => 0,
					'absence' => 0,
                    'totalWorkload' => 0,
                    'assignEm' => 0
                );
            } else {
                $default = array(
                    'validated' => 0,
                    'consumed' => null,
                    'remains' => 0,
                    'capacity' => 0,
					'absence' => 0,
                    'totalWorkload' => 0,
                    'assignPc' => 0
                );
            }
        } else {
			if($profile == true)
			{
				$default = array(
					//'estimation' => 0,
					'validated' => 0,
					'consumed' => null,
					'capacity' => null,
					'totalWorkload' => 0,
					'resource' => 0,
					'resource_theoretical' => 0,
					'fte' => 0
					//'forecast' => 0
				);
			}
			else
			{
				$default = array(
					//'estimation' => 0,
					'validated' => 0,
					'consumed' => null,
					'remains' => 0
					//'forecast' => 0
				);
			}
            if($showType == 3){
                $default = array(
					'validated' => 0,
					'consumed' => null,
				);
            }
        }

        $titles = '';
        $md = !empty($employee_info['Company']['unit']) ? $employee_info['Company']['unit'] : 'M.D';
        $md = __($md, true);
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
				$input['remains']=$this->formatNumber($input['remains']);
				$input['validated']=$this->formatNumber($input['validated']);
				$input['consumed']=$this->formatNumber($input['consumed']);
				$input['capacity']=$this->formatNumber($input['capacity']);
				$input['absence']=$this->formatNumber($input['absence']);
				$input['totalWorkload']=$this->formatNumber($input['totalWorkload']);
                // sửa chổ này
                $class = $this->parseData($input, $key === 'summary' ? null : true);
                $summary = $key === 'summary' ? '' : 'gantt-input';
                $estimation[$key].= "";
                $validated[$key].= "<td rel=\"v-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">" . (!$isPlus || $input['validated'] > 0 ? $input['validated'] : '&nbsp;' ) . "</div></td>";
                $remains[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['remains']}</div></td>";
                // show red for consume > workdload with team +
                $_class = (($showType == 3) && ($input['consumed'] > $input['validated'])) ? 'gantt-invalid' : '';
                $consumed[$key].= "<td rel=\"c-$y-$m\" class=\"gantt-d$days $_class\"><div class=\"$summary\">" . (!$isPlus || $input['consumed'] > 0 ? $input['consumed'] : '&nbsp;' ) . "</div></td>";
                if($displayCapacity){
                    if($class){
                        $totalWorkload[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days $class\"><div>{$input['totalWorkload']}</div></td>";
                    } else {
                        $totalWorkload[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['totalWorkload']}</div></td>";
                    }

                    $capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['capacity']}</div></td>";
					$absence[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['absence']}</div></td>";
                    if($showType == false){
                        $_assignEm = ($allWorkload == 0) ? 0 : $this->formatNumber(round(($input['validated']/$allWorkload)*100, 2));
                        $assignEm[$key] .= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$_assignEm}</div></td>";
                    } else {
                        $_assignPc = ($allWorkload == 0) ? 0 : $this->formatNumber(round(($input['validated']/$allWorkload)*100, 2));
                        $assignPc[$key] .= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$_assignPc}</div></td>";
                    }
                }
				if($profile == true)
				{
					$capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['capacity']}</div></td>";
					$totalWorkload[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days $class\"><div>{$input['totalWorkload']}</div></td>";
					$resource[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['resource']}</div></td>";
					$resource_theoretical[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['resource_theoretical']}</div></td>";
					$fte[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['fte']}</div></td>";
				}
                //$capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days $class\"><div>{$input['forecast']}</div></td>";
                foreach ($default as $k => $v) {
                    // sửa chổ này
                    if ($k == 'consumed' && $input[$k] != $this->na) {
                        $year[$key][$y]['has'] = true;
                    }
					if($k == 'resource_theoretical')
					   $year[$key][$y][$k] = $year[$key][$y][$k] != 0 ? $year[$key][$y][$k] : $input[$k] ;
					else
					   $year[$key][$y][$k] += $input[$k];
					if($k!='employee' || $k!='resource')
					   $year[$key][$y][$k]=$this->formatNumber($year[$key][$y][$k]);
                    $staffings['summary']['data'][$date][$k] += $input[$k];
					$staffings['summary']['data'][$date][$k] = $this->formatNumber($staffings['summary']['data'][$date][$k]);
                }
            }
        }
        $count = array(3 + count($year['summary']), count($this->_months));

        if($displayCapacity){
            if($showType == false){
                $_title = array(
                    'validated' => __('Workload', true),
                    'consumed' => __('Consumed', true),
                    'remains' => __('Remain', true),
                    'capacity' => __('Capacity', true),
					'absence' => __('Absence', true),
                    'totalWorkload' => __('Total Workload', true),
                    'assignEm' => __('% Assigned to employee', true)
                );
            } else {
                $_title = array(
                    'validated' => __('Workload', true),
                    'consumed' => __('Consumed', true),
                    'remains' => __('Remain', true),
                    'capacity' => __('Capacity', true),
					'absence' => __('Absence', true),
                    'totalWorkload' => __('Total Workload', true),
                    'assignPc' => __('% Assigned to profit center', true)
                );
            }
        } else {
			if($profile == true)
			{
				$_title = array(
					//'estimation' => __('Estimation', true),
					'validated' => __('Workload', true),
					'capacity' => __('Capacity Theoretical', true),
					'totalWorkload' => __('Total Workload', true),
					'resource' => __('Resource', true),
					'resource_theoretical' => __('Resource Theoretical', true),
					'fte' => __('FTE +/-', true)
					//'forecast' => __('Forecast', true)
				);
			}else{
				$_title = array(
					//'estimation' => __('Estimation', true),
					'validated' => __('Workload', true),
					'consumed' => __('Consumed', true),
					'remains' => __('Remain', true)
					//'forecast' => __('Forecast', true)
				);
			}
            if($showType == 3){
                $_title = array(
					'validated' => __('Workload', true),
					'consumed' => __('Consumed', true),
				);
            }
        }

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
            $is_check = isset($staffing['is_check']) ? $staffing['is_check'] : 0;
            if($displayCapacity){
                if($showType == false){
                    $staffGantt = "<tr class=\"gantt-staff\">
                        <td class=\"gantt-node gantt-node-head\">
                          <table rel=\"{$staffing['id']}\" class=\"$summary gantt-right\" check=\"{$is_check}\">
                               $titles
                               <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
                               <tr class=\"gantt-num\">{$consumed[$key]}</tr>
                               <tr class=\"gantt-num gantt-capacity\">{$capacity[$key]}</tr>
                               <tr class=\"gantt-num gantt-capacity\">{$absence[$key]}</tr>
                               <tr class=\"gantt-num\">{$totalWorkload[$key]}</tr>
                               <tr class=\"gantt-num\">{$assignEm[$key]}</tr>
                               <tr class=\"fixedHeightStaffing gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//REMAIN DA XAY DUNG SAN CHI CAN GOI RA O DAY. code : <tr class=\"gantt-num wd-total-remains\">{$remains[$key]}</tr>
                } else {
                    $staffGantt = "<tr class=\"gantt-staff\">
                        <td class=\"gantt-node gantt-node-head\">
                          <table rel=\"{$staffing['id']}\" class=\"$summary gantt-right\" check=\"{$is_check}\">
                               $titles
                               <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
                               <tr class=\"gantt-num\">{$consumed[$key]}</tr>
                               <tr class=\"gantt-num gantt-capacity\">{$capacity[$key]}</tr>
                               <tr class=\"gantt-num gantt-capacity\">{$absence[$key]}</tr>
                               <tr class=\"gantt-num\">{$totalWorkload[$key]}</tr>
                               <tr class=\"gantt-num\">{$assignPc[$key]}</tr>
                               <tr class=\"fixedHeightStaffing gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//REMAIN DA XAY DUNG SAN CHI CAN GOI RA O DAY. code : <tr class=\"gantt-num wd-total-remains\">{$remains[$key]}</tr>
                }
            } else {
				if($profile == true)
				{
					$staffGantt = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-node-head\">
						  <table rel=\"{$staffing['id']}\" class=\"$summary gantt-right\" check=\"{$is_check}\">
							   $titles
							   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
							   <tr class=\"gantt-num\">{$capacity[$key]}</tr>
							   <tr class=\"gantt-num wd-total-remains\">{$totalWorkload[$key]}</tr>
							   <tr class=\"gantt-num\">{$resource[$key]}</tr>
							   <tr class=\"gantt-num\">{$resource_theoretical[$key]}</tr>
							   <tr class=\"gantt-num\">{$fte[$key]}</tr>
							   <tr class=\"fixedHeightStaffing gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
				}
				else
				{
					$staffGantt = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-node-head\">
						  <table rel=\"{$staffing['id']}\" class=\"$summary gantt-right\" check=\"{$is_check}\">
							   $titles
							   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
							   <tr class=\"gantt-num\">{$consumed[$key]}</tr>
							   <tr class=\"gantt-num wd-total-remains\">{$remains[$key]}</tr>
							   <tr class=\"fixedHeightStaffing gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
				}
                if($showType == 3){
                    $staffGantt = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-node-head\">
						  <table rel=\"{$staffing['id']}\" class=\"$summary gantt-right\" check=\"{$is_check}\">
							   $titles
							   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
							   <tr class=\"gantt-num\">{$consumed[$key]}</tr>
							   <tr class=\"fixedHeightStaffing gantt-num\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
                }
            }
            $resource_theoretical[$key] = $resource[$key] = $fte[$key] = $estimation[$key] = $validated[$key] = $consumed[$key] = $remains[$key] = $forecast[$key] = $capacity[$key] = $absence[$key] = $totalWorkload[$key] = $assignEm[$key] = $assignPc[$key] = '';
            $total = $default;

            //$estimation[$key].= "<td rowspan=\"5\" class=\"gantt-name\"><div><a href=\"javascript:void(0)\">$image{$staffing['name']}</a></div></td>";
            //$estimation[$key].= "<td rowspan=\"5\" class=\"gantt-name\"><div rel=\"{$staffing['func']}\"><a href=\"javascript:void(0)\">$image{$staffing['name']}</a></div></td>";
            if($showType == false){
                $_name = "<div style=\"min-width: 250px;\" rel=\"{$staffing['id']}\"><a href=\"javascript:void(0)\">$image<span>{$staffing['name']}</span></a>";
                if( $showAllPicture && $staffing['id'] != 'summary' && $staffing['id'] != '999999999' ){
                    $avatarEmploy = $this->UserFile->avatar($staffing['id']);
                    $_name .= "<img class='wdcircle' style='float:right; width:30px; height:30px;margin-right: 20px; margin-top: -5px;' src='" . $avatarEmploy . "'>";
                }
                $_name .= "</div>";
            } else {
                $children = isset($staffing['children']) ? 'data-children="' . implode($staffing['children'], ',') . '"' : '';
                if($showType == 3){
                    $_nameDisplay = ($staffing['id'] == 'summary') ? __('Total', true) : $staffing['name'];
                } else {
                    $_nameDisplay = $staffing['name'];
                }
                $_name = "<div style=\"min-width: 200px;\" $children data-pc-id=\"{$staffing['id']}\">{$_nameDisplay}";
                if( $showAllPicture && $staffing['id'] != 'summary' && $staffing['id'] != '999999999' && empty($staffing['children']) && $showType != 3 && $showType != 1 ){
                    $avatarEmploy = $this->UserFile->avatar($staffing['id']);
                    $_name .= "<img class='wdcircle' style='float:right; width:30px; height:30px; margin-right: 20px; margin-top: -5px;' src='" . $avatarEmploy . "'>";
                }
                $_name .= "</div>";
            }
            if($displayCapacity){
                $estimation[$key].= "<td rowspan=\"7\" class=\"gantt-name\">$_name</td>";
            } else {
				$colspan = 3;
				if($profile == true)
				{
					$colspan = 7;
				}
                if( $isPlus ){
                    $colspan = 3;
                }
                $estimation[$key].= "<td rowspan=\"$colspan\" class=\"gantt-name\">$_name</td>";
            }
            foreach ($_title as $k => $v) {
                ${$k}[$key].= "<td class=\"gantt-func\"><div style='min-width: 150px;'>{$v}</div></td>";
                if($showType == 3){
                    if($displayTeamPlus){
                        ${$k}[$key] .= "<td><div class='freeze'>". (!empty($freezeTeams[$staffing['id']]) && ($v == __('Workload', true)) ? $freezeTeams[$staffing['id']] : '' ) ."</div></td>";
                    }
                    ${$k}[$key] .= "<td><div class='budgetmd'>". (!empty($budgetMdTeams[$staffing['id']]) && ($v == __('Workload', true)) ? $budgetMdTeams[$staffing['id']] : '' ) ."</div></td>";
                }
            }
            if ($titles) {
                switch ($showType) {
                    case 1: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Profit centers', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            break;
                        }
                    case 3: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Profit centers', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            if($displayTeamPlus){
                                $titles .="<td><div>".__('Freeze', true)."</div></td>";
                            }
                            $titles .="<td><div>".__('Engaged', true)."</div></td>";
                            break;
                        }
                    case 2: {
							$module = 'Skills';
							if($profile == true)
							{
								$module = 'Profile';
							}
                            $titles = "<td class=\"gantt-name\"><div>" . __($module, true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            break;
                        }
                    default: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('Employees', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                        }
                }
            }
            // $titles .="<td><div>".__('Engaged', true)."</div></td>";
            foreach ($year[$key] as $_y => $_year) {
                $class = $this->parseData($_year);
				$_year['remains']=$this->formatNumber($_year['remains']);
				$_year['validated']=$this->formatNumber($_year['validated']);
                $estimation[$key].= "";
                $validated[$key].= "<td class='gantt-num' rel=\"v-$_y\" class=\"wd-work-year\"><div>" . (!$isPlus || $_year['validated'] > 0 ? $_year['validated'] : '&nbsp;') . "</div></td>";
                $consumed[$key].= "<td class='gantt-num' rel=\"c-$_y\"><div>" . (!$isPlus || $_year['consumed'] > 0 ? $_year['consumed'] : '&nbsp;') . "</div></td>";
                $remains[$key].= "<td class='gantt-num' rel=\"r-$_y\" class=\"wd-remains-year\"><div>{$_year['remains']}</div></td>";
				if($profile == true)
				{
					$totalWorkload[$key].= "<td class='gantt-num' rel=\"f-$_y\" class=\"$class\"><div>{$_year['totalWorkload']}</div></td>";
                    $capacity[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_year['capacity']}</div></td>";
					$resource[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>&nbsp;</div></td>";
					$resource_theoretical[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_year['resource_theoretical']}</div></td>";
					$fte[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_year['fte']}</div></td>";
				}
                if($displayCapacity){
                    if($showType == false){
                        $_assignEmYear = ($allWorkload == 0) ? 0 : round(($_year['validated']/$allWorkload)*100, 2);
						$_assignEmYear=$this->formatNumber($_assignEmYear);
                        $assignEm[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_assignEmYear}</div></td>";
                    } else {
                        $_assignPcYear = ($allWorkload == 0) ? 0 : round(($_year['validated']/$allWorkload)*100, 2);
						$_assignPcYear=$this->formatNumber($_assignPcYear);
                        $assignPc[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_assignPcYear}</div></td>";
                    }
					$_year['totalWorkload']=$this->formatNumber($_year['totalWorkload']);
					$_year['capacity']=$this->formatNumber($_year['capacity']);
					$_year['absence']=$this->formatNumber($_year['absence']);
                    $totalWorkload[$key].= "<td class='gantt-num' rel=\"f-$_y\" class=\"$class\"><div>{$_year['totalWorkload']}</div></td>";
                    $capacity[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_year['capacity']}</div></td>";
					$absence[$key].= "<td class='gantt-num' rel=\"f-$_y\"><div>{$_year['absence']}</div></td>";
                }
                foreach ($default as $k => $v) {
                    $total[$k] += $_year[$k];
					if($k!='employee' || $k!='resource')
					$total[$k]=$this->formatNumber($total[$k]);
                }
                if ($titles) {
                    $titles .= "<td><div>$_y</div></td>";
                }
            }
            if ($titles) {
                $titles.= "<td><div>Total</div></td>";
                $titles = "<tr class=\"gantt-title gantt-head gantt-header\">$titles</tr>";
            }

            $class = $this->parseData($total);
			$total['validated']=$this->formatNumber($total['validated']);
			$total['consumed']=$this->formatNumber($total['consumed']);
			$total['remains']=$this->formatNumber($total['remains']);
			$total['totalWorkload']=$this->formatNumber($total['totalWorkload']);
			$total['capacity']=$this->formatNumber($total['capacity']);
			$total['absence']=$this->formatNumber($total['absence']);
            $estimation[$key].= "";
            $validated[$key].= "<td class='gantt-num' rel=\"v-total\"><div>" . (!$isPlus || $total['validated'] > 0 ? $total['validated'] : '&nbsp;') . "</div></td>";
            $consumed[$key].= "<td class='gantt-num' rel=\"c-total\"><div>" . (!$isPlus || $total['consumed'] > 0 ? $total['consumed'] : '&nbsp;') . "</div></td>";
            $remains[$key].= "<td class='gantt-num' rel=\"r-total\"><div>{$total['remains']}</div></td>";
			if($profile == true)
			{
				$totalWorkload[$key].= "<td class='gantt-num' rel=\"f-total\" class=\"$class\"><div>{$total['totalWorkload']}</div></td>";
                $capacity[$key].= "<td class='gantt-num' rel=\"f-total\"><div>{$total['capacity']}</div></td>";
				$resource[$key].= "<td class='gantt-num' rel=\"f-total\" ><div>&nbsp;</div></td>";
				$resource_theoretical[$key].= "<td class='gantt-num' rel=\"f-total\" ><div>{$total['resource_theoretical']}</div></td>";
                $fte[$key].= "<td class='gantt-num' rel=\"f-total\"><div>{$total['fte']}</div></td>";
			}
            if($displayCapacity){
                $totalWorkload[$key].= "<td class='gantt-num' rel=\"f-total\" class=\"$class\"><div>{$total['totalWorkload']}</div></td>";
                $capacity[$key].= "<td class='gantt-num' rel=\"f-total\"><div>{$total['capacity']}</div></td>";
				$absence[$key].= "<td class='gantt-num' rel=\"f-total\"><div>{$total['absence']}</div></td>";
                if($showType == false){
                    $_assignEmTotal = ($allWorkload == 0 ) ? 0 : round(($total['validated']/$allWorkload)*100, 2);
					$_assignEmTotal=$this->formatNumber($_assignEmTotal);
                    $assignEm[$key].= "<td class='gantt-num' rel=\"f-total\"><div>{$_assignEmTotal}</div></td>";
                    $staffList = "<tr class=\"gantt-staff\">
                        <td class=\"gantt-node gantt-child\" colspan=\"5\">
                          <table rel=\"list-{$staffing['id']}\" class=\"$summary\">
                               $titles
                               <tr>{$estimation[$key]}</tr>
                               <tr class='wd-workload'>{$validated[$key]}</tr>
                               <tr>{$consumed[$key]}</tr>
                               <tr class=\"gantt-capacity\">{$capacity[$key]}</tr>
							   <tr class=\"gantt-capacity\">{$absence[$key]}</tr>
                               <tr>{$totalWorkload[$key]}</tr>
                               <tr>{$assignEm[$key]}</tr>
                               <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//REMAIN DA XAY DUNG SAN CHI CAN GOI RA O DAY. code <tr class=wd-remains>{$remains[$key]}</tr>
                } else {
                    $_assignPcTotal = ($allWorkload == 0 ) ? 0 : round(($total['validated']/$allWorkload)*100, 2);
					$_assignPcTotal=$this->formatNumber($_assignPcTotal);
                    $assignPc[$key].= "<td rel=\"f-total\"><div>{$_assignPcTotal}</div></td>";
                    $staffList = "<tr class=\"gantt-staff\">
                        <td class=\"gantt-node gantt-child\" colspan=\"5\">
                          <table rel=\"list-{$staffing['id']}\" class=\"$summary gantt-left\">
                               $titles
                               <tr>{$estimation[$key]}</tr>
                               <tr class=wd-workload>{$validated[$key]}</tr>
                               <tr>{$consumed[$key]}</tr>
                               <tr class=\"gantt-capacity\">{$capacity[$key]}</tr>
							   <tr class=\"gantt-capacity\">{$absence[$key]}</tr>
                               <tr>{$totalWorkload[$key]}</tr>
                               <tr>{$assignPc[$key]}</tr>
                               <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//REMAIN DA XAY DUNG SAN CHI CAN GOI RA O DAY. code <tr class=wd-remains>{$remains[$key]}</tr>
                }
            } else {
				if($profile == true)
				{
					$staffList = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-child\" colspan=\"7\">
						  <table rel=\"list-{$staffing['id']}\" class=\"$summary gantt-left\">
							   $titles
							   <tr>{$estimation[$key]}</tr>
							   <tr class=wd-workload>{$validated[$key]}</tr>
							   <tr>{$capacity[$key]}</tr>
							   <tr class=wd-remains>{$totalWorkload[$key]}</tr>
							   <tr class=wd-remains>{$resource[$key]}</tr>
							   <tr class=wd-remains>{$resource_theoretical[$key]}</tr>
							   <tr class=wd-remains>{$fte[$key]}</tr>
							   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
				}
				else
				{
					$staffList = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-child\" colspan=\"5\">
						  <table rel=\"list-{$staffing['id']}\" class=\"$summary gantt-left\">
							   $titles
							   <tr>{$estimation[$key]}</tr>
							   <tr class=wd-workload>{$validated[$key]}</tr>
							   <tr>{$consumed[$key]}</tr>
							   <tr class=wd-remains>{$remains[$key]}</tr>
							   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
				}
                if($showType == 3){
                    if($displayTeamPlus){
                        $_count = $count[0] +2;
                    } else {
                        $_count = $count[0] +1;
                    }
                    $staffList = "<tr class=\"gantt-staff\">
						<td class=\"gantt-node gantt-child\" colspan=\"5\">
						  <table rel=\"list-{$staffing['id']}\" class=\"$summary gantt-left\">
							   $titles
							   <tr>{$estimation[$key]}</tr>
							   <tr class=wd-workload>{$validated[$key]}</tr>
							   <tr>{$consumed[$key]}</tr>
							   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$_count}\"><div>&nbsp;</div></td></tr>
						  </table>
						</td>
					</tr>";
                }
            }


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
	function formatNumber(&$number=null)
	{
		if(isset($number)&&is_numeric($number))
		{
			$number=number_format($number,2, '.', '');
		}
		else
		{
			$number=number_format(0.00,2, '.', '');
		}
		return $number;
	}
}
