<?php

/**
 * PHP versions 5
 *
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
class GanttVsPreviewHelper extends AppHelper {

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
        if (empty($date) || !preg_match($this->_pattern, $date)) {
            return 0;
        }
        return intval(strtotime($date));
    }

    public function addElementToArrayFollowKey($datas = array(), $key = null, $newKey = null, $newVal = null){
        $results = array();
        if(!empty($datas)){
            foreach($datas as $k => $vl){
                $results[$k] = $vl;
                if($key == $k){
                    $results[$newKey] = $newVal;
                }
            }
        }
        return $results;
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
    public function create($type, $start, $end, $stones = array(), $displayList = true, $format = 'd-m-Y') {
        $staff = $head = $num = '';
        $_dayDiv = 0;
        $dateTypes = array();
		if(is_array($type))
		{
		$dateTypes = isset($type['dateTypes']) && !empty($type['dateTypes']) ? $type['dateTypes'] : array();
        $type = isset($type['type']) && !empty($type['type']) ? $type['type'] : 'month';
		}

		$type = $type == 'day' ? 'date' : $type ;
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
        } else {
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
            list($k, $desc) = $_stones;
            $left = max(0, round((floor((($k - $start)) / 86400) / $total) * 100, 2));
            $stone .= "<div class=\"gantt-msi gantt-ms\" style=\"left:$left%;top:0px\" title=\"" . h($desc) . ' : ' . date($format, $k) . "\"><i></i><span>$desc</span></div>\n";
        }

        if ($duplicate && mb_strlen(trim($desc)) >= 20) {
            $duplicate+=1;
        }

        $this->_months = array();
        $cols = 0;
        if($type == 'week'){
            foreach($dateTypes as $date){
                $week = date('W', $date);
                $_d = date('d', $date);
                $_m = date('m', $date);
                $_y = date('Y', $date);
                $this->_months[] = array($_d, $_m, $_y);
                $head.="<td class=\"gantt-day\"><div>$_m/$_y</div></td>";
                $num .= "<td class=\"gantt-day\"><div>w$week</div></td>";
            }
        } else {
            $_m_of_y = 1;
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
                    $firstWeek = mktime(0, 0, 0, date("m", $_t), date("d", $_t)-7, date("Y", $_t));
                    if (($i <= $total && $mod == 0)) {
                        $week = date('W', $firstWeek);
                        if ($type != 'date') {
                            $head.="<td ><div>$_m/$_y</div></td>";
                            $num .= "<td><div>w$week</div></td>";
                        }
                    }
                    if($type == 'week'){
                        $this->_months[] = array(date('d', $firstWeek), date('m', $firstWeek), date('Y', $firstWeek));
                    }
                    $_t_d = cal_days_in_month(CAL_GREGORIAN, $_m, $_y);
                    $col.= "<td class=\"$class\"><div>&nbsp;</div></td>\n";
                    if ($type == 'date') {
                        if(in_array($_t, $dateTypes)){
    						$this->_months[] = array($_d, $_m, $_y);
                            $num.= "<td class=\"gantt-day\"><div>$_d</div></td>\n";
                            $cols++;
                        }
                        if ($i <= $total && $_d == $_t_d && $cols != 0) {
                            $head.="<td colspan=\"$cols\"><div>$_m/$_y</div></td>";
                        }
                        if($_d == $_t_d){
                            $cols = 0;
                        }
                        $_d++;
                    }
                } else {
                    $step = $_days;
                    $_d+= $_days;
                }
                if ($_d > $_days) {
                    $_d = 1;
                    if ($type == 'month') {
                        if($_m == 12 || $i >= $total-$step){
							if($_m_of_y < 3){
								$head.='<td colspan="'. $_m_of_y .'" class="gantt-d' . $_days. '"><div class="wd-absolute wd-year-header">' . '</div></td>';
								$_m_of_y = 1;
							}else{
								$head.='<td colspan="'. $_m_of_y .'" class="gantt-d' . $_days. '"><div class="wd-absolute wd-year-header">' . $_y . '</div></td>';
								$_m_of_y = 1;
							}
                        }else{
                            $_m_of_y++;
                        }
                        $num.= "<td class=\"gantt-day gantt-d$_days\"><div>{$month[$_m]}</div></td>";
                        $this->_months[] = array($_days, $_m, $_y);
                    } elseif ($type == 'year') {
                        $num .= "<td><div>{$month[$_m]}</div></td>";
                    }
                    $_m++;
                    if ($_m > 12) {
                        $_m = 1;
                        if ($type == 'year') {
                            $head.="<td colspan=\"12\"><div>$_y</div></td>";
                        }
                        $_y++;
                    }
                    if ($type == 'month' || $type == 'year') {
                        $col.= "<td class=\"gantt-d$_days\"><div>&nbsp;</div></td>\n";
                    }
                    $_days = $this->daysOfMonth($_m, $_y);
                }
                $i+=$step;
            } //exit;
        }
        $displayList = intval($displayList);
        if($this->params['controller'] == 'activity_tasks_preview'){
            $showType = !empty($this->params['url']['type']) ? $this->params['url']['type'] : 0;
            if($showType == 0){
                $list = "<table class=\"gantt-list gantt-list-primary\">
                    <tr class=\"gantt-head\">
                            <td><div class=\"wd-absolute\">" . ($displayList ? __('Phase name', true) : __('Activity', true)) . "</div></td>";
            } elseif($showType == 1){
                $list = "<table class=\"gantt-list gantt-list-primary\">
                    <tr class=\"gantt-head\">
                            <td><div class=\"wd-absolute\">" . ($displayList ? __('Phase name', true) : __('Profit Centers', true)) . "</div></td>";
            }elseif($showType == 2){
                $list = "<table class=\"gantt-list gantt-list-primary\">
                    <tr class=\"gantt-head\">
                            <td><div class=\"wd-absolute\">" . ($displayList ? __('Phase name', true) : __('Project', true)) . "</div></td>";
            } else {
                $list = "<table class=\"gantt-list gantt-list-primary\">
                    <tr class=\"gantt-head\">
                            <td><div class=\"wd-absolute\">" . ($displayList ? __('Phase name', true) : __('Family', true)) . "</div></td>";
            }

        } else {
            $list = "<table class=\"gantt-list gantt-list-primary\">
                <tr class=\"gantt-head\">
                        <td><div class=\"wd-absolute\">" . ($displayList ? __('Phase name', true) : __('Project name', true)) . "</div></td>";
        }

        if ($displayList) {
            $list .= "<td><div>" . __('Start Date', true) . "</div></td>
                                <td><div>" . __('End Date', true) . "</div></td>
                                <td><div>" . __('Start Real Date', true) . "</div></td>
                                <td><div>" . __('End Real Date', true) . "</div></td>";
        }
        $list.="</tr>";
        $chart = "<table class=\"gantt\">
            <tr>
                <td class=\"gantt-node gantt-node-head\">
                        <table id='export-header'>";

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
    public function draw($_name, $_start, $_end, $_rstart, $_rend, $color, $class = 'parent') {
        extract($this->_runtime);
        $list .= "<tr class=\"gantt-row gantt-$class\">
                        <td class=\"gantt-name\"><div>" . $_name . "</div></td>";
        if ($displayList) {
            $list .="<td><div>" . date($format, $_start) . "</div></td>
                        <td><div>" . date($format, $_end) . "</div></td>";
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
                                    <table><tr>$col</tr></table>
                                    {$this->_draw($_name, 'n', $start, $total, $_start, $_end, $format, $color)}";
        if ((!empty($_rend) && $_rend > 0) && (!empty($_rstart) && $_rstart > 0)) {
            $chart.=$this->_draw($_name, 's', $start, $total, $_rstart, $_rend, $format, $color);
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
            $list.= "<tr class=\"gantt-ms\">
                            <td colspan=\"5\">
                              <div class=\"gantt-line\">&nbsp;</div>
                            </td>
                    </tr>";
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
	public function endAjax() {
        extract($this->_runtime);
        if ($stone) {
            $chart.= "<tr class=\"gantt-ms\">
                            <td>
                              <div class=\"gantt-line\">$stone</div>
                            </td>
                    </tr>";
            $list.= "<tr class=\"gantt-ms\">
                            <td colspan=\"5\">
                              <div class=\"gantt-line\">&nbsp;</div>
                            </td>
                    </tr>";
        }

        if (!empty($chart)) {
            $chart.= '</table>';
        }
        if (!empty($list)) {
            $list.= '</table>';
        }

        if ($staff) {
            $list.= $staff[0];
            $chart.= $staff[1];
        }
		echo $list;
		echo $chart;
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
    protected function _draw($name, $pos, $time, $total, $start, $end, $format, $color = '#000') {
        $left = max(0, round((floor((($start - $time)) / 86400) / $total) * 100, 2));
        $width = round((floor((($end - $start) + 86400) / 86400) / $total) * 100, 2);
        if ($left + $width > 100) {
            $width -= ($width + $left) - 100;
        }
        return "<div class=\"gantt-line-$pos\" title=\"" . sprintf(__('Project: %s from %s to %s', true), $name, date($format, $start), date($format, $end)) . "\" style=\"background-color:$color;width:$width%;left:$left%\"></div>";
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
        if(empty($data['capacity']) || !isset($data['capacity'])){
            $data['capacity'] = 0;
        }
        //if ($data['validated'] > $data['forecast']) {
        if($data['validated'] > $data['capacity']){
            $class = 'gantt-invalid';
        }

        return $class;
    }

    /**
     * Parse input data
     *
     * @param array $data
     * @param boolean $strict
     *
     * @return string,html class name for input
     */
    public function parseDataCapacity($data, $capacity) {
        $class = '';
        if(isset($data) && $data > $capacity){
            $class = 'gantt-invalid';
        }
        return $class;
    }
	public function parseDataTheoreticalCapacity($data, $capacity) {
        $class = '';
        if(isset($data) && $data > $capacity){
            $class = 'gantt-invalid';
        }
		else{
			$class = 'gantt-green';
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
    public function drawStaffing($staffings, $md, $displaySummary = 0, $showType = false, $isCheck = false, $newDataStaffings = array(),$activityType=0,$isAjax=null,$classParent='',$classPParent='') {
		$showType = $showType == 2 ? 0 : $showType;
		$isMultiYear = $staffings['isMultiYear'];
        ini_set('memory_limit', '512M');
		$dateType = 'month';
        $displayCapacity = false;
        if($showType == 1 || (($showType == 0 || $showType == 5) && ($isCheck == 2 || $isCheck == 0)) || (($showType == 0 || $showType == 5) && ($isCheck == 1 || $isCheck == 0))){
            $displayCapacity = true;
        }
		$allowDisplayCapacities = $displayRealCapacity = $displayTheoreticalCapacity = $displayAbsence = $displayWorkingDay = $displayTheoreticalFte = $displayRealFte = false;

		if(($showType == 1 && $isCheck == false) || ($showType == 0 && $isCheck == 1) || ($showType == 0 && $isCheck == 2) || ($showType == 5 && $isCheck == 2) || ($showType == 5 && $isCheck == 1))
		{
			$allowDisplayCapacities = true;
		}
		if(isset($staffings['companyConfigs']))
		{
            if($showType == 0 && $isCheck == 0){
                $companyConfigs=$staffings['companyConfigs'];
                $displayAbsence = isset($companyConfigs['staffing_by_pc_display_absence']) ? $companyConfigs['staffing_by_pc_display_absence'] : false;
                $displayWorkingDay = isset($companyConfigs['staffing_by_pc_display_working_day']) ? $companyConfigs['staffing_by_pc_display_working_day'] : false;
            } else {
                $companyConfigs=$staffings['companyConfigs'];
    			$displayAbsence = isset($companyConfigs['staffing_by_pc_display_absence']) ? $companyConfigs['staffing_by_pc_display_absence'] : false;
    			$displayWorkingDay = isset($companyConfigs['staffing_by_pc_display_working_day']) ? $companyConfigs['staffing_by_pc_display_working_day'] : false;
    			$displayRealCapacity = isset($companyConfigs['staffing_by_pc_display_real_capacity']) ? $companyConfigs['staffing_by_pc_display_real_capacity'] : false;
    			$displayRealFte = isset($companyConfigs['staffing_by_pc_display_real_fte']) ? $companyConfigs['staffing_by_pc_display_real_fte'] : false;
    			$displayTheoreticalCapacity = isset($companyConfigs['staffing_by_pc_display_theoretical_capacity']) ? $companyConfigs['staffing_by_pc_display_theoretical_capacity'] : false;
    			$displayTheoreticalFte = isset($companyConfigs['staffing_by_pc_display_theoretical_fte']) ? $companyConfigs['staffing_by_pc_display_theoretical_fte'] : false;
            }
		}
		$displayDayNotValidated = isset($companyConfigs['staffing_show_days_not_validates']) ? $companyConfigs['staffing_show_days_not_validates'] : 1;
		$displayResources = isset($companyConfigs['staffing_show_resources']) ? $companyConfigs['staffing_show_resources'] : 1;
		$displayConsumed = isset($companyConfigs['staffing_show_consumed']) ? $companyConfigs['staffing_show_consumed'] : 1;
        $budgetTeam = isset($staffings['budgetTeam']) ? $staffings['budgetTeam'] : array();
		if(isset($staffings['data']))
		{
			$dateType=$staffings['dateType'];
			$staffings=$staffings['data'];
		}
		//Hide theoretical when filter by day/week
		if($dateType == 'day' || $dateType == 'week')
		{
			$displayTheoreticalCapacity = false;
			$displayTheoreticalFte = false;
		}
		//end
		$dateType = $dateType == 'day' ? 'date' : $dateType;
        $this->_runtime['staff'] = array('', '');
        $staffings['summary'] = array(
            'id' => 'summary',
			'isSummary' => 1,
            'name' => __('Summary', true),
            'func' => 0,
            'data' => array()
        );
        $budget = $resource_theoretical = $resource =  $totalWorkload = $estimation = $validated = $notValidated = $consumed = $employee = $capacity = $working = $absence = $employeeAsterisk = $capacityAsterisk = $workingDay = $consumedSplitWk = $consumedSplitCp = $consumedSplitEm = array_fill_keys(array_keys($staffings), '');
		//WORKING 2015/03/24
		$capacityTheoretical = $fte = $fte_real = array_fill_keys(array_keys($staffings), '');
		//END
        $year = array();

        // sửa chổ này
        if($displayCapacity){
            $default = array(
                //'estimation' => 0,
                'validated' => 0, // la workload
                'consumed' => null,
				'notValidated' => 0,
                'employee' => 0,
                'capacity' => 0,
				'working' => 0,
				'absence' => 0
            );
            if($dateType == 'month' && $showType == 0 && $budgetTeam){
                $default = array(
                    'budget' => 0,
                    'validated' => 0, // la workload
                    'consumed' => null,
    				'notValidated' => 0,
                    'employee' => 0,
                    'capacity' => 0,
    				'working' => 0,
    				'absence' => 0
                );
            }
			//WORKING 2015/03/24;
			if($allowDisplayCapacities && (($showType == 0 && $isCheck == 2) || $showType == 1))
			{
				$default_capacity = array(
					'capacityTheoretical' => 0,
					'capacity_theoretical' => 0,
					'fte' => 0,
					'fte_real' => 0
				);
				$default = array_merge($default,$default_capacity);
			}
            if($dateType == 'month' && $allowDisplayCapacities && ($showType == 0 && $isCheck == 1)){
                $default_capacity = array(
					'capacity_theoretical' => 0
				);
				$default = array_merge($default,$default_capacity);
            }
			//END
        } else {
            if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
				$default = array(
                    'validated' => 0,
					'consumed' => null,
					'capacity' => null,
					'totalWorkload' => 0,
					'resource' => 0,
					'resource_theoretical' => 0,
					'fte' => 0
                );
            } else {
				$default = array(
                    //'estimation' => 0,
                    'validated' => 0,
                    'consumed' => null
                );
                if($dateType == 'month' && $showType == 0 && $budgetTeam){
                    $default = array(
                        'budget' => 0,
                        'validated' => 0,
                        'consumed' => null
                    );
                }
            }
        }

        $titles = '';
        $md = __($md, true);
		if(isset($_SESSION['arrMonth']))
		{
			unset($_SESSION['arrMonth']);
		}
		foreach($this->_months as $_mVal)
		{
			list($days, $m, $y) = $_mVal;
			//DAY
			if($dateType == 'date' || $dateType == 'week')
			{
				$_date = strtotime($y . '-' . $m . '-' .$days);
				$_date = date('d-m-Y',$_date);
			}
			else
			{
				$_date = strtotime($y . '-' . $m . '-1');
				$_date = date('m-Y',$_date);
			}
		}
		if(isset($_sessionVal['summaryVal'])) unset($_sessionVal['summaryVal']);
        foreach ($this->_months as $data) {
            list($days, $m, $y) = $data;
            $titles.= "<td class=\"gantt-cell gantt-d$days\"><div>$md</div></td>";
			if($dateType == 'date' || $dateType == 'week')
			{
				$date = strtotime($y . '-' . $m . '-'.$days);
				$_date = date('d-M-y',$date);
			}
			else
			{
				$date = strtotime($y . '-' . $m . '-1');
				$_date = date('M-y',$date);
			}
			//SESSION DATE
			$_arrMonth[]=$_date;
			//END

            $staffings['summary']['data'][$date] = $default;
            reset($staffings);
            $parentList = array();
            while (list($key, $staffing) = each($staffings)) {
                $parentList[$staffing['id']] = 1;
                if (!isset($year[$key][$y])) {
                    $year[$key][$y] = $default;
                }
                $input = $default;
                if (isset($staffing['data'][$date])) {
                    $input = array_merge($input, $staffing['data'][$date]);
                }

				//SET ROWS FOR PROFILE
				if($showType == 5)
				{
					$fullColumns = false ;
					if((isset($staffing['isEmployee'])&&$staffing['isEmployee']) || (isset($staffing['isSummary'])&&$staffing['isSummary']))
					{
						$fullColumns = true;
					}
				}
				//END


				if($showType==0 || $showType==2)
				{
					if($activityType==1)
					{
						$_idFamilyVi=null;
						$_idEmp=0;
						//SET DATA MD CHART USE SESSION
						/*if(isset($staffing['isEmployee'])&&$staffing['isEmployee'])
						{
							$_idEmp=$staffing['employee_id'];
							if(isset($_SESSION['graphMonth'][$_idEmp])&&!empty($_SESSION['graphMonth'][$_idEmp]))
							{
								unset($_SESSION['graphMonth'][$_idEmp]);
							}
						}*/
						if(isset($staffing['isFamily'])&&$staffing['isFamily'])
						{
							$_idFamilyVi=$staffing['id'];
							$_idEmp=$staffing['employee_id'];
							//echo "xxx"; echo $date; echo "<br />";
							if(!isset($_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['name']))
							{
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['name']=$staffing['name'];
							}
							if($date!=null)
							{
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['dataConsumed'][$date]=$input['consumed']===null?0:$input['consumed'];
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['dataWorkload'][$date]=$input['validated']===null?0:$input['validated'];
							}
						}
						//END

                        // loop 2 time, just 1 time for sum workload and comsumed
                        // isEmployee and isFamily this is 2 time, removed isEmployee or isFamily 
						if(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)
						{
							// foreach ($default as $k => $v) {
								// $staffings['summary']['data'][$date][$k]=$this->parseNumber($staffings['summary']['data'][$date][$k]);
								
                                //fix in 22/01/2018 ticket #228
                                // $staffings['summary']['data'][$date][$k] += $input[$k];
								// $staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
                            // }
						} else if(isset($staffing['isFamily']) && $staffing['isFamily']){
                            foreach ($default as $k => $v) {
								// $_sessionVal['summaryVal'][$k] = isset($_sessionVal['summaryVal'][$k])?$_sessionVal['summaryVal'][$k]:0;
								// $_sessionVal['summaryVal'][$k] += $input[$k];
								$staffings['summary']['data'][$date][$k] += $input[$k];
								$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
							}
                        }
					}
					else
					{
						$_idFamilyVi=null;
						$_idEmp=0;
						//SET DATA MD CHART USE SESSION
						/*if(isset($staffing['isEmployee'])&&$staffing['isEmployee'])
						{
							$_idEmp=$staffing['employee_id'];
							if(isset($_SESSION['graphMonth'][$_idEmp])&&!empty($_SESSION['graphMonth'][$_idEmp]))
							{
								unset($_SESSION['graphMonth'][$_idEmp]);
							}
						}*/
						if(isset($staffing['isFamily'])&&$staffing['isFamily'])
						{
							$_idFamilyVi=$staffing['id'];
							$_idEmp=$staffing['employee_id'];
							if(!isset($_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['name']))
							{
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['name']=$staffing['name'];
							}
							if($date!=null)
							{
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['dataConsumed'][$date]=$input['consumed']===null?0:$input['consumed'];
								$_SESSION['graphMonth'][$_idEmp][$_idFamilyVi]['dataWorkload'][$date]=$input['validated']===null?0:$input['validated'];
							}
						}
						if(isset($staffing['isFamily']) && $staffing['isFamily'])
						{
							foreach ($default as $k => $v) {
								$_sessionVal['summaryVal'][$k] = isset($_sessionVal['summaryVal'][$k])?$_sessionVal['summaryVal'][$k]:0;
								$_sessionVal['summaryVal'][$k] += $input[$k];
								$staffings['summary']['data'][$date][$k] += $input[$k];
								$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
							}
						}
                        if(isset($staffing['isSubfamily']) && $staffing['isSubfamily'] == 1 && $dateType == 'month' && $showType == 0 && $budgetTeam){
                            //if(!isset($staffings['summary']['data'][$date]['budget'])){
//                                $staffings['summary']['data'][$date]['budget'] = 0;
//                            }
//                            $staffings['summary']['data'][$date]['budget'] += isset($input['budget']) && !empty($input['budget']) ? $input['budget'] : 0;
//                            $staffings['summary']['data'][$date]['budget'] = $this->formatNumber($staffings['summary']['data'][$date]['budget'], 2);
                        }
					}
				}
				elseif($showType==1)
				{
					if((isset($staffing['isFirst'])&&$staffing['isFirst'])||(isset($staffing['level'])&&$staffing['level']==0))
					{
						foreach ($default as $k => $v) {

							$staffings['summary']['data'][$date][$k] += $input[$k];
							if($k=='working')
							{
								$staffings['summary']['data'][$date][$k] = $input[$k];
							}
							if($k!='employee')
							$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
						}
					}
				}
				else
				{
					if(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)
					{
						foreach ($default as $k => $v) {
							$staffings['summary']['data'][$date][$k] += $input[$k];
							if($k!='employee')
							$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
						}
					}
				}

				foreach ($default as $k => $v) {
					if ($k == 'consumed' && $input[$k] != $this->na) {
						$year[$key][$y]['has'] = true;
					}
					$year[$key][$y][$k] += $input[$k];
					if($k!='employee')
					$year[$key][$y][$k] = $this->formatNumber($year[$key][$y][$k],2);
				}
				//$staffings['summary']['data'][$date][$k]=$this->formatNumber($staffings['summary']['data'][$date][$k],2);
				//$year[$key][$y][$k] = $this->formatNumber($year[$key][$y][$k],2);
                $input['budget']=$this->formatNumber($input['budget'],2);
				$input['validated']=$this->formatNumber($input['validated'],2);
				$input['consumed']=$this->formatNumber($input['consumed'],2);

                $class = $this->parseData($input, $key === 'summary' ? null : true);
                $summary = $key === 'summary' ? '' : 'gantt-input';
                //$estimation[$key].= "<td rel=\"e-$y-$m\" class=\"gantt-cell gantt-d$days\"><div class=\"$summary gantt-estimated\">{$input['estimation']}</div></td>";
                $estimation[$key].= "";
                if($dateType == 'month' && $showType == 0 && $budgetTeam){
                    if(isset($staffing['isActivity']) && $staffing['isActivity'] == 1){
                        //do nothing
                    } else {
                        $budget[$key].= "<td rel=\"b-$y-$m\" class=\"gantt-cell gantt-d$days\"><div class=\"$summary\">{$input['budget']}</div></td>";
                    }
                }
                $validated[$key].= "<td rel=\"v-$y-$m\" class=\"gantt-cell gantt-d$days cell-workload-$date\"><div class=\"$summary\">{$input['validated']}</div></td>";
                //$remains[$key].= "";
                $consumed[$key].= "<td rel=\"c-$y-$m\" class=\"gantt-cell gantt-d$days cell-consumed-$date\"><div class=\"$summary\">{$input['consumed']}</div></td>";

                if($displayCapacity){
					if($dateType == 'date' || $dateType == 'week'){
						$newDataStaffingTime=isset($newDataStaffings[strtotime($days.'-'.$m.'-'.$y)])?$newDataStaffings[strtotime($days.'-'.$m.'-'.$y)]:array();
					} else {
						$newDataStaffingTime=isset($newDataStaffings[strtotime('01-'.$m.'-'.$y)])?$newDataStaffings[strtotime('01-'.$m.'-'.$y)]:array();
					}
                    if(($showType == 0 || $showType == 5) && ($isCheck == 2 || $isCheck == 0)){
						//ADD NOT VALIDATED IS HERE
						//$_notValidated=isset($newDataStaffingTime['capacity']) ? $newDataStaffingTime['capacity']-$input['consumed'] : -$input['consumed'];
						//$_notValidated=$input['notValidated'];
						$_notValidated=isset($newDataStaffingTime['notValidated']) ? $newDataStaffingTime['notValidated'] : 0 ;
						$_notValidated = $_notValidated > 0 ? $_notValidated : 0;
						$_notValidated=$this->formatNumber($_notValidated,2);
						$notValidated[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-capacity gantt-cell gantt-d$days cell-notValidated-$date\"><div class=\"$summary\">{$_notValidated}</div></td>";
						//END
                        $_emp = isset($newDataStaffingTime['employee']) ? $newDataStaffingTime['employee'] : 0;
                        $_cap = $this->formatNumber($newDataStaffingTime['capacity'],2);
						$_woking = $this->formatNumber($newDataStaffingTime['working'],2);
						$_absence = $this->formatNumber($newDataStaffingTime['absence'],2);
                        $_class = $this->parseDataCapacity($input['validated'], $_cap);
                        $employee[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days cell-employee-$date\"><div class=\"$summary\">{$_emp}</div></td>";
                        $capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-capacity-$date\"><div class=\"$summary\">{$_cap}</div></td>";
						//WORKING 2015/03/24
						$_fte = 0;
						$_capacityTheoretical = isset($newDataStaffingTime['capacity_theoretical']) ? $newDataStaffingTime['capacity_theoretical'] : 0;
						$_fte = $_woking==0?0:($input['validated'] - $_capacityTheoretical)/$_woking;
						$_fte=$this->formatNumber($_fte,2);
						$_class = $this->parseDataTheoreticalCapacity($_fte, 0);
						$capacityTheoretical[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-capacity_theoretical-$date\"><div class=\"$summary\">{$_capacityTheoretical}</div></td>";
						$fte[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-fte-$date\"><div class=\"$summary\">{$_fte}</div></td>";
						$_fteReal = $_woking==0?0:($input['validated'] - $_cap)/$_woking;
						$_fteReal=$this->formatNumber($_fteReal,2);
						$_class = $this->parseDataTheoreticalCapacity($_fteReal, 0);
						$fte_real[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-realfte-$date\"><div class=\"$summary\">{$_fteReal}</div></td>";
						//END
						//$working[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days \"><div class=\"$summary\">{$_woking}</div></td>";
						//$absence[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days \"><div class=\"$summary\">{$_absence}</div></td>";
                    } elseif(($showType == 0 || $showType == 5) && $isCheck == 1){
						if(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)
						{
							$_woking = $this->formatNumber($input['working'],2);
							$_absence = $this->formatNumber($input['absence'],2);
							$_cap = $this->formatNumber($input['capacity'],2);
						}
						else
						{
							$_woking = $this->formatNumber($newDataStaffingTime['working'],2);
							$_absence = $this->formatNumber($newDataStaffingTime['absence'],2);
							$_cap = $this->formatNumber($newDataStaffingTime['capacity'],2);
						}
						//ADD NOT VALIDATED IS HERE
						//$_notValidated=$_cap-$input['consumed'];
						$_notValidated=$input['notValidated'];
						$_notValidated = $_notValidated > 0 ? $_notValidated : 0;
						$_notValidated=$this->formatNumber($_notValidated,2);
						$notValidated[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days cell-notValidated-$date\"><div class=\"$summary\">{$_notValidated}</div></td>";
						//END
                        $_class = $this->parseDataCapacity($input['validated'], $_cap);
                        $capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-capacity-$date\"><div class=\"$summary\">{$_cap}</div></td>";
						//WORKING 2015/03/24
						$_fte = 0;
						if($showType == 5)
						{
							$_capacityTheoretical = isset($newDataStaffingTime['capacity_theoretical']) ? $newDataStaffingTime['capacity_theoretical'] : 0;
						}
						else
						{
							$_capacityTheoretical = isset($input['capacity_theoretical']) ? $input['capacity_theoretical'] : 0;
						}
						$_fte = $_woking==0?0:($input['validated'] - $_capacityTheoretical)/$_woking;
						$_fte=$this->formatNumber($_fte,2);
						$_class = $this->parseDataTheoreticalCapacity($_fte, 0);
						$capacityTheoretical[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-capacity_theoretical-$date\"><div class=\"$summary\">{$_capacityTheoretical}</div></td>";
						$fte[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-fte-$date\"><div class=\"$summary\">{$_fte}</div></td>";
						$_fteReal = $_woking==0?0:($input['validated'] - $_cap)/$_woking;
						$_fteReal=$this->formatNumber($_fteReal,2);
						$_class = $this->parseDataTheoreticalCapacity($_fteReal, 0);
						$fte_real[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-realfte-$date\"><div class=\"$summary\">{$_fteReal}</div></td>";
						//END
						// $working[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days \"><div class=\"$summary\">{$_woking}</div></td>";
						// $absence[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days \"><div class=\"$summary\">{$_absence}</div></td>";
                    } else {
						//ADD NOT VALIDATED IS HERE
						//$_notValidated=$input['capacity']-$input['consumed'];
						$_notValidated=$input['notValidated'];
						$_notValidated = $_notValidated > 0 ? $_notValidated : 0;
						$_notValidated=$this->formatNumber($_notValidated,2);
						$notValidated[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days cell-notValidated-$date\"><div class=\"$summary\">{$_notValidated}</div></td>";
						//END

						$input['capacity']=$this->formatNumber($input['capacity'],2);
						$_woking=$this->formatNumber($input['working'],2);
						$_absence=$this->formatNumber($input['absence'],2);
                        $employee[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days cell-employee-$date\"><div class=\"$summary\">{$input['employee']}</div></td>";
                        if($class == true){
                            $capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days $class cell-capacity-$date\"><div>{$input['capacity']}</div></td>";
                        } else {
                            $capacity[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days  cell-capacity-$date\"><div class=\"$summary\">{$input['capacity']}</div></td>";
                        }
						//WORKING 2015/03/24
						$_fte = 0;
						$_capacityTheoretical = isset($input['capacity_theoretical']) ? $input['capacity_theoretical'] : 0;
						$_fte = $_woking==0?0:($input['validated'] - $_capacityTheoretical)/$_woking;
						$_fte=$this->formatNumber($_fte,2);
						$_class = $this->parseDataTheoreticalCapacity($_fte, 0);
						$capacityTheoretical[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-capacity_theoretical-$date\"><div class=\"$summary\">{$_capacityTheoretical}</div></td>";
						$fte[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-fte-$date\"><div class=\"$summary\">{$_fte}</div></td>";
						$_fteReal = $_woking==0?0:($input['validated'] - $input['capacity'])/$_woking;
						$_fteReal=$this->formatNumber($_fteReal,2);
						$_class = $this->parseDataTheoreticalCapacity($_fteReal, 0);
						$fte_real[$key].= "<td rel=\"r-$y-$m\" class=\"gantt-cell gantt-d$days $_class cell-realfte-$date\"><div class=\"$summary\">{$_fteReal}</div></td>";
						//END
                    }
					$working[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days cell-working-$date\"><div class=\"$summary\">{$_woking}</div></td>";
					$absence[$key].= "<td rel=\"f-$y-$m\" class=\"gantt-cell gantt-d$days cell-absence-$date\"><div class=\"$summary\">{$_absence}</div></td>";
                } else {
                    if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
						if($fullColumns == true)
						{
							//$cls = "gantt-d".$days
							$cls = "gantt-cell";
							$_date = strtotime('01-'.$m.'-'.$y);
							$capacity[$key].= "<td rel=\"f-$y-$m\" class=\"$cls cell-capacity-$date\"><div class=\"$summary\">{$input['capacity']}</div></td>";
							$totalWorkload[$key].= "<td rel=\"f-$y-$m\" class=\"$cls cell-workload-$date\"><div>{$input['totalWorkload']}</div></td>";
							$resource[$key].= "<td rel=\"f-$y-$m\" class=\"$cls cell-employee-$date\"><div class=\"$summary\">{$input['resource']}</div></td>";
							$resource_theoretical[$key].= "<td rel=\"f-$y-$m\" class=\"$cls\"><div class=\"$summary\">{$input['resource_theoretical']}</div></td>";
							$fte[$key].= "<td rel=\"f-$y-$m\" class=\"$cls cell-fte-$date\"><div class=\"$summary\">{$input['fte']}</div></td>";
						}
                    }
                }
            }
        }
		$_SESSION['arrMonth']=$_arrMonth;
        $count = array(3 + count($year['summary']), count($this->_months));

        // sửa chổ này
        if($displayCapacity){
			//WORKING 2015/03/24
			$_titleEhancement = array();
			$txtCapacity = 'Capacity';
			if($allowDisplayCapacities && (($showType == 0 && $isCheck == 2) || $showType == 1)){
				$_titleEhancement = array(
					'capacityTheoretical' => __('Theoretical capacity', true),
					'fte' => __('FTE +/- Theoretical', true),
					'fte_real' => __('FTE +/- Real', true)
				);
				$txtCapacity = 'Real capacity';
			}
			//END
            $_title = array(
                //'estimation' => __('Estimation', true),
                'validated' => __('Workload', true),
                'consumed' => __('Consumed', true),
				'notValidated' => __('Days not validated', true),
                'employee' => __('Employee', true),
                'capacity' => __($txtCapacity, true),
				'working' => __('Working days', true),
				'absence' => __('Absence', true)
            );
			//WORKING 2015/03/24
			$_title = array_merge($_title,$_titleEhancement);
            if($dateType == 'month' && $allowDisplayCapacities && ($showType == 0 && $isCheck == 1)){
                $_title['capacityTheoretical'] = __('Theoretical capacity', true);
            }
            ///
        } else {
            if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
				$_title = array(
					'validated' => __('Workload', true),
					'consumed' => __('Consumed', true),
					'capacity' => __('Capacity Theoretical', true),
					'totalWorkload' => __('Total Workload', true),
					'resource' => __('Resource', true),
					'resource_theoretical' => __('Resource Theoretical', true),
					'fte' => __('FTE +/-', true)
				);
            } else {
                $_title = array(
                    //'estimation' => __('Estimation', true),
                    'validated' => __('Workload', true),
                    'consumed' => __('Consumed', true),
                    //'remains' => __('Remain', true)
                );
                if($dateType == 'month' && $showType == 0 && $budgetTeam){
                    $_title = array(
                        'budget' => __('Budget', true),
                        'validated' => __('Workload', true),
                        'consumed' => __('Consumed', true)
                    );
                }
            }
        }
        $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";

        $image = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"></rect><path class="b" d="M-3193-1748a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Zm0-5a1,1,0,0,1-1-1,1,1,0,0,1,1-1h14a1,1,0,0,1,1,1,1,1,0,0,1-1,1Z" transform="translate(3666 1994)"></path></g></svg><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><defs></defs><g transform="translate(-464 -224)"><rect class="a" width="32" height="32" transform="translate(464 224)"></rect><rect class="a" width="32" height="32" transform="translate(464 224)"></rect><path class="b" d="M-3181.758-1748.343l-4.242-4.243-4.243,4.243a1,1,0,0,1-1.414,0,1,1,0,0,1,0-1.415l4.242-4.242-4.242-4.243a1,1,0,0,1-.293-.707,1,1,0,0,1,.293-.707,1,1,0,0,1,1.414,0l4.243,4.242,4.242-4.242a1,1,0,0,1,1.414,0,1,1,0,0,1,0,1.414l-4.242,4.243,4.243,4.243a1,1,0,0,1,.292.707,1,1,0,0,1-.292.707,1,1,0,0,1-.707.292A1,1,0,0,1-3181.758-1748.343Z" transform="translate(3666 1994)"></path></g></svg><span class="wd-loading"></span>';
        $staffings = array_merge(array('summary' => array()), $staffings);
        if ($displaySummary == 0) {
            unset($staffings['summary']);
        } elseif($displaySummary == 99){
			$staffingsTmp = $staffings;
			$staffings = array();
			$staffings['summary'] = $staffingsTmp['summary'];
            /*foreach ($staffings as $key => $staffing) {
                if( $key === 'summary' ){
                    //do nothing
                } else {
                    unset($staffings[0]);
                    unset($staffings[$key]);
                }
            }*/
        } else {
            //do nothing
        }
        $output = array();
		$space='&nbsp;';
        // sửa vị trí
        foreach ($staffings as $key => $staffing) {
			$_classTr='';
			$_idTr='';
			$_idTrR='';
			$_function='';
			$_id=$staffing['id'];
			$_employee=isset($staffing['employee_id'])?$staffing['employee_id']:'';
			$_family=isset($staffing['family_id'])?$staffing['family_id']:'';
			$_subfamily=isset($staffing['subfamily_id'])?$staffing['subfamily_id']:'';
			$_pc_id = isset($staffing['profit_center_id']) ? $staffing['profit_center_id']:'';
			if($showType === 0)
			{
				//echo $activityType;
				
				//if($isAjax) $classNoParent = $classParent;
				if($activityType==1)
				{
					if(isset($staffing['isEmployee'])&&$staffing['isEmployee'])
					{
						$_classTr=' trEmployee '.$classPParent.' acti';

						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleEmployee('$_id')\"";
						//$_function="onclick=\"ajaxShowPC('$_id')\"";
					}
					elseif(isset($staffing['isActivity'])&&$staffing['isActivity'])
					{
						$_classTr=' trActivity employee-'.$_employee.' family-'.$_employee.'-'.$_family.' subfamily-'.$_employee.'-'.$_family.'-'.$_subfamily.' '.$classParent;
					}
					elseif(isset($staffing['isSubfamily'])&&$staffing['isSubfamily'])
					{
						$_classTr=' trSubFamily  employee-'.$_employee.' family-'.$_employee.'-'.$_family.' '.$classParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleActivity('$_id')\"";
						if( isset($staffing['subfamily_id'])&& $staffing['subfamily_id']== -1){
							/* Ticket #487 Hide "No Sub-family Name" if have no sub-family*/
							if( preg_match('/-$/',$staffing['id'])){
								$_classTr .= ' f-hidden';
							}
						}
					}
					elseif(isset($staffing['isFamily'])&&$staffing['isFamily'])
					{
						$_classTr=' trFamily employee-'.$_employee.' '.$classParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleSub('$_id')\"";
					}
				}
				else
				{
					if(isset($staffing['isActivity'])&&$staffing['isActivity'])
					{
						$_idTr='tr-activity-'.$_id;
						$_idTrR='r-tr-activity-'.$_id;
						$_classTr='trActivity family-'.$_family.' subfamily-'.$_family.'-'.$_subfamily.' '.$classParent.' onload acti';
						$_function="onclick=\"ajaxShowResource('$_idTr','$_id')\"";
					}
					elseif(isset($staffing['isSubfamily'])&&$staffing['isSubfamily'])
					{
						$_classTr='trSubFamily family-'.$_family.' '.$classParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleActivity('$_id')\"";
						if( isset($staffing['subfamily_id'])&& $staffing['subfamily_id']== -1){
							/* Ticket #487 Hide "No Sub-family Name" if have no sub-family*/
							if( preg_match('/-$/',$staffing['id'])){
								$_classTr .= ' f-hidden';
							}
						}
					}
					elseif(isset($staffing['isFamily'])&&$staffing['isFamily'])
					{
						$_classTr='trFamily '.$classPParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleSub('$_id')\"";
					}elseif(isset($staffing['isProfitCenter'])&&$staffing['isProfitCenter']){
						$_classTr='trProfitCenter acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"togglePCEmployee('$_id')\"";
					}elseif(isset($staffing['isEmployee'])&&$staffing['isEmployee']){
						$_classTr='trEmployee trPCEmployee profit_center-'.$_pc_id;
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						// $_function="onclick=\"toggleEmployee('$_id')\"";
					}
				}
			}
			elseif($showType === 1)
			{
				$_strClass='';
				if(isset($staffing['isSummary'])&&$staffing['isSummary'])
				{
					//do nothing
				}
				else
				{
					if(!isset($staffing['arrParent']))
					{
						$staffing['arrParent']=array();
					}
					foreach($staffing['arrParent'] as $_index=>$_parent)
					{
						$_strClass.=' p-tr-'.$_parent;
					}
					if(!isset($staffing['parent']))
					{
						$staffing['parent']=0;
						$staffing['level']=0;
					}
                    $parent = 'pc-' . $staffing['parent'];
                    $_id = str_replace('pc-', '', $_id);
					$_levelParent=$staffing['level']-1;
					$_classTr = 'trPC trPC-'.$staffing['level'].' '.$_strClass.' pp-tr-'.$staffing['parent'].' onload acti';
                    if( isset($parentList[$parent]) )$_classTr .= ' is-child';
					$_idTr = 'tr-'.$_id;
					$_idTrR = 'r-tr-'.$_id;
					$_function = "onclick=\"ajaxShowPC('$_idTr', $_id)\"";
                    $_function .= sprintf(' data-pc-id="%s" data-pc-parent="%s"', $_id, $staffing['parent']);
				}
			}
			if($showType === 5)
			{
				$fullColumns = false;
				if(isset($staffing['isSummary'])&&$staffing['isSummary'])
				{
					$fullColumns = true;
				}
				elseif(isset($staffing['isEmployee'])&&$staffing['isEmployee'])
					{
						$fullColumns = true;
						$_classTr=' trEmployee '.$classPParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleEmployee('$_id')\"";
						//$_function="onclick=\"ajaxShowPC('$_id')\"";
					}
					elseif(isset($staffing['isActivity'])&&$staffing['isActivity'])
					{
						$_classTr=' trActivity employee-'.$_employee.' family-'.$_employee.'-'.$_family.' subfamily-'.$_employee.'-'.$_family.'-'.$_subfamily.' '.$classParent;
					}
					elseif(isset($staffing['isSubfamily'])&&$staffing['isSubfamily'])
					{
						$_classTr=' trSubFamily  employee-'.$_employee.' family-'.$_employee.'-'.$_family.' '.$classParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleActivity('$_id')\"";
						if( isset($staffing['subfamily_id'])&& $staffing['subfamily_id']== -1){
							/* Ticket #487 Hide "No Sub-family Name" if have no sub-family*/
							if( preg_match('/-$/',$staffing['id'])){ // ID kết thúc bằng dấu '-'
								$_classTr .= ' f-hidden';
							}
						}
					}
					elseif(isset($staffing['isFamily'])&&$staffing['isFamily'])
					{
						$_classTr=' trFamily employee-'.$_employee.' '.$classParent.' acti';
						$_idTr='tr-'.$_id;
						$_idTrR='r-tr-'.$_id;
						$_function="onclick=\"toggleSub('$_id')\"";
					}
			}
			if( empty( $_function)) $_classTr.= ' no-expand';
            $summary = $key === 'summary' ? 'gantt-summary' : '';
            $addClass = 'export-table export-table-'.$key;
            $addClassLeft = 'export-left export-left-'.$key;
            $is_check = isset($staffing['is_check']) ? $staffing['is_check'] : 0;
			//GANTT RIGHT
			
            if($displayCapacity){
				//WORKING 2015/03/24
				//CHECK DISPLAY BY CONFIG
				// pr($key);
				$dataAbsence = $displayAbsence ? "<tr class=\"gantt-num gantt-capacity _absence\">{$absence[$key]}</tr>" : "";
				$dataRealCapacity = $displayRealCapacity ? "<tr class=\"text-bold gantt-num gantt-capacity _capacity\">{$capacity[$key]}</tr>" : "";
				$dateRealFte = $displayRealFte ? "<tr class=\"gantt-num gantt-capacity _fte_real\">{$fte_real[$key]}</tr>" : "";
				$dataTheoreticalCapacity = $displayTheoreticalCapacity ? "<tr class=\"gantt-num gantt-capacity _theoreticalcapacity\">{$capacityTheoretical[$key]}</tr>" : "";
				$dataTheoreticalFte = $displayTheoreticalFte ? "<tr class=\"gantt-num gantt-capacity _theoreticalfte\">{$fte[$key]}</tr>" : "";
				$dataWorking = $displayWorkingDay ? "<tr class=\"gantt-num gantt-capacity _workingday\">{$working[$key]}</tr>" : "";
				$dataEstimation = "<tr class=\"gantt-num\">{$estimation[$key]}</tr>";
				$dataWorkload = "<tr class=\"text-bold gantt-num wd-total-workload _workload\">{$validated[$key]}</tr>";
				$dataConsumed = $displayConsumed ? "<tr class=\"text-bold gantt-num _consumed\">{$consumed[$key]}</tr>" : '';
				$dataNotValidated = $displayDayNotValidated ? "<tr class=\"gantt-num gantt-capacity _dataNotValidated\">{$notValidated[$key]}</tr>" :"";
				if( $showType == 5) $dataConsumed = '';
				//END
                if(($showType == 0 || $showType == 5) && $isCheck == 1){
					$_classRow=(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)?'gantt-employee':'';
                    $staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr gantt-staff\">
                        <td class=\"gantt-node $_classRow gantt-node-head\">
                          <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
								{$dataEstimation}
							    {$dataWorking}
								{$dataAbsence}
								{$dataRealCapacity}
								{$dataWorkload}
								{$dataConsumed}
								{$dataNotValidated}
								{$dataTheoreticalCapacity}
                               <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//WORKING DAY DA XAY DUNG O DAY. CHI CAN GOI RA DE SU DUNG. CODE: <tr class=\"gantt-num gantt-capacity\">{$working[$key]}</tr>
                } else {
					$dataEmployee = $displayResources ? "<tr class=\"gantt-num gantt-employee\">{$employee[$key]}</tr>" : "";
                    if($dateType == 'month' && $showType == 0 && $budgetTeam){
						$dataBudget = "<tr class=\"gantt-num wd-total-budget\">{$budget[$key]}</tr>";
                        $staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
									{$dataEstimation}
									{$dataWorking}
									{$dataEmployee}
                                    {$dataAbsence}
    							    {$dataRealCapacity}
									{$dataWorkload}
    							    {$dateRealFte}
                                    {$dataConsumed}
    							    {$dataNotValidated}
    								{$dataTheoreticalCapacity}
    								{$dataTheoreticalFte}
									{$dataBudget}
                                   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
                    } else {
                        $staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
                                    {$dataEstimation}
                                    {$dataWorking}
								    {$dataEmployee}
                                    {$dataAbsence}
    							    {$dataRealCapacity}
                                    {$dataWorkload}
    							    {$dateRealFte}
                                    {$dataConsumed}
    							    {$dataNotValidated}
									{$dataTheoreticalCapacity}
    								{$dataTheoreticalFte}
                                   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
                    }

					//WORKING DAY DA XAY DUNG O DAY. CHI CAN GOI RA DE SU DUNG. CODE: <tr class=\"gantt-num gantt-capacity\">{$working[$key]}</tr>
                }
            } else {
                if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
					
						$_classRow='';
						if($fullColumns == true)
						{
							$_classRow = 'gantt-employee';
							$staffTmp = "<tr class=\"gantt-num\">{$capacity[$key]}</tr>
									   <tr class=\"gantt-num\">{$resource[$key]}</tr>
									   <tr class=\"gantt-num\">{$resource_theoretical[$key]}</tr>
									   <tr class=\"gantt-num\">{$fte[$key]}</tr>";
						}
						else
						{
							$staffTmp = "<tr class=\"gantt-num\">{$consumed[$key]}</tr>";
						}
						$staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr class=\"gantt-staff\">
							<td class=\"gantt-node $_classRow  gantt-node-head\">
							  <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
								   <tr class=\"gantt-num\">{$estimation[$key]}</tr>
								   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
								   {$staffTmp}
								   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
							  </table>
							</td>
						</tr>";
                } else {
                    if($dateType == 'month' && $showType == 0 && $budgetTeam){
                        $staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
                                   <tr class=\"gantt-num\">{$estimation[$key]}</tr>
                                   <tr class=\"gantt-num wd-total-budget\">{$budget[$key]}</tr>
                                   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
                                   <tr class=\"gantt-num\">{$consumed[$key]}</tr>
                                   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
                    } else {
                        $staffGantt = "<tr $_function id=\"$_idTrR\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary $addClass\" check=\"{$is_check}\">
                                   <tr class=\"gantt-num\">{$estimation[$key]}</tr>
                                   <tr class=\"gantt-num wd-total-workload\">{$validated[$key]}</tr>
                                   <tr class=\"gantt-num\">{$consumed[$key]}</tr>
                                   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$count[1]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
                    }

                }
            }
			$staffGanttTitle = "<tr class=\"gantt-staff\">
					<td class=\"gantt-node gantt-node-head\">
					  <table>
						   $titles
					  </table>
					</td>
				</tr>";
            $budget[$key] = $resource_theoretical[$key] = $resource[$key] =  $totalWorkload[$key] = $estimation[$key] = $notValidated[$key] = $validated[$key] = $consumed[$key] = $employee[$key] = $capacity[$key] = $working[$key] = $absence[$key] = $employeeAsterisk[$key] = $capacityAsterisk[$key] = $workingDay[$key] = $consumedSplitWk[$key] = $consumedSplitCp[$key] = $consumedSplitEm[$key] = '';
			//WORKING 2015/03/24
			$capacityTheoretical[$key] = '';
			$fte[$key] = $fte_real[$key] = '';
			//END
            $total = $totalTemp[$key] = $default;
			
            if($displayCapacity){
				//WORKING 2015/03/24
				//$colCapacityByYears = 7;
				$colCapacityByYears = ($showType == 0 && $isCheck == 1) ? 1 : 2;
				$colCapacityByYears = $displayAbsence ? $colCapacityByYears+1 : $colCapacityByYears+0;
				$colCapacityByYears = $displayWorkingDay ? $colCapacityByYears+1 : $colCapacityByYears+0;
				$colCapacityByYears = $displayRealCapacity ? $colCapacityByYears+1 : $colCapacityByYears+0;
				$colCapacityByYears = $displayRealFte ? $colCapacityByYears+1 : $colCapacityByYears+0;
				$colCapacityByYears = $displayTheoreticalCapacity ? $colCapacityByYears+1 : $colCapacityByYears+0;
				$colCapacityByYears = $displayTheoreticalFte ? $colCapacityByYears+1 : $colCapacityByYears+0;
                if($dateType == 'month' && $showType == 0 && $budgetTeam){
                    $colCapacityByYears += 1;
                }
				if( $displayDayNotValidated) $colCapacityByYears++;
				if( $displayResources) $colCapacityByYears++;
				if( $displayConsumed) $colCapacityByYears++;
				$estimation[$key].= "<td rowspan=\"$colCapacityByYears\" class=\"gantt-name check-name multi-row\"><span class=\"name\">{$staffing['name']}</span>{$image}</td>";
            } else {
				$colCapacityByYears = 0 ;
				if( $displayDayNotValidated) $colCapacityByYears++;
				if( $displayResources) $colCapacityByYears++;
				if( $displayConsumed) $colCapacityByYears++;

                if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
					if($fullColumns == true)
					{
						$colCapacityByYears = 6 ;
					}
					//$colCapacityByYears = 6 ;
                    $estimation[$key].= "<td rowspan=\"$colCapacityByYears\" class=\"gantt-name check-name\"><span class=\"name\">{$staffing['name']}</span>{$image}</td>";
                } else {
                    if($dateType == 'month' && $showType == 0 && $budgetTeam){
                        $colCapacityByYears += 1;
                    }
                    $estimation[$key].= "<td rowspan=\"$colCapacityByYears\" class=\"gantt-name check-name\"><span class=\"name\">{$staffing['name']}</span>{$image}</td>";
                }
            }
            if($dateType == 'month' && $showType == 0 && $budgetTeam){
                if(isset($staffing['isActivity']) && $staffing['isActivity'] == 1){
                    if(in_array('budget', array_keys($_title))){
                        unset($_title['budget']);
                    }
                } else {
                    if(!in_array('budget', array_keys($_title))){
                        $_title = Set::pushDiff(array('budget' => __('Budget', true)), $_title);
                    }
                }
            }
            foreach ($_title as $k => $v) {
                if($showType == 0 || $showType == 1 || ($showType == 5 && ($isCheck ==  2 || $isCheck == 1))){
					$cls = '';
					if($v == 'Resource Theoretical'){
						$cls = 'resource_theoretical';
					}
                    ${$k}[$key].= "<td class=\"gantt-func $cls\"><div >{$v}</div></td>";
                } else {
                    ${$k}[$key].= "<td class=\"gantt-func\"><div style='min-width: 150px;'>{$v}</div></td>";
                }
            }
            if ($titles) {
                switch ($showType) {
                    case 1: {
                            $titles = "<td class=\"gantt-name\"><div>" . __('', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            break;
                        }
                    case 0: {
                            if($this->params['controller'] == 'activity_tasks'){
                                $titles = "<td class=\"gantt-name\"><div>" . __('', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            } else {
                                $titles = "<td class=\"gantt-name\"><div>" . __('', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            }
                            break;
                        }
                    default: {
                            if($this->params['controller'] == 'activity_tasks'){
                                $titles = "<td class=\"gantt-name\"><div>" . __('', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            } else {
                                $titles = "<td class=\"gantt-name\"><div>" . __('', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
                            }

                        }
                }
            }
            //$titles .="<td><div>Previous Tasks</div></td>";
			//VALUE OF YEAR
            foreach ($year[$key] as $_y => $_year) {
                $class = $this->parseData($_year);
                $estimation[$key].= "";
                $_year['budget']=$this->formatNumber($_year['budget']);
				$_year['validated']=$this->formatNumber($_year['validated']);
				$_year['consumed']=$this->formatNumber($_year['consumed']);
                //$estimation[$key].= "<td rel=\"total-$_y\"><div>{$_year['estimation']}</div></td>";

                if($dateType == 'month' && $showType == 0 && $budgetTeam){
                    if(isset($staffing['isActivity']) && $staffing['isActivity'] == 1){
                        //do nothing
                    } else {
                        $budget[$key].= "<td rel=\"total-$_y\" class=\"wd-bug-year\"><div>{$_year['budget']}</div></td>";
                    }
                }
                $validated[$key].= "<td rel=\"total-$_y\" class=\"wd-work-year\"><div>{$_year['validated']}</div></td>";
                $consumed[$key].= "<td class=\"wd-work-year\" rel=\"total-$_y\"><div>{$_year['consumed']}</div></td>";
                //$remains[$key].= "";
                if($displayCapacity){
                    if(($showType == 0 || $showType == 5) && ($isCheck == 2 || $isCheck == 0)){
                        $totalEmployeeYear = $totalCapacityYear = array();
						$totalWorkingYear = array();
						$totalAbsenceYear = array();
                        if(!empty($newDataStaffings)){
                            foreach($newDataStaffings as $time => $newDataStaffing){
                                $_time = date('Y', $time);
                                if(!isset($totalEmployeeYear[$_time])){
                                    $totalEmployeeYear[$_time] = 0;
                                }
                                $totalEmployeeYear[$_time] += !empty($newDataStaffing['employee']) ? $newDataStaffing['employee'] : 0;

                                if(!isset($totalCapacityYear[$_time])){
                                    $totalCapacityYear[$_time] = 0;
                                }
                                $totalCapacityYear[$_time] += !empty($newDataStaffing['capacity']) ? $newDataStaffing['capacity'] : 0;

								if(!isset($totalWorkingYear[$_time])){
                                    $totalWorkingYear[$_time] = 0;
                                }
                                $totalWorkingYear[$_time] += !empty($newDataStaffing['working']) ? $newDataStaffing['working'] : 0;

								if(!isset($totalAbsenceYear[$_time])){
                                    $totalAbsenceYear[$_time] = 0;
                                }
                                $totalAbsenceYear[$_time] += !empty($newDataStaffing['absence']) ? $newDataStaffing['absence'] : 0;

								if(!isset($_totalNotValidatedYear[$_time])){
                                    $_totalNotValidatedYear[$_time] = 0;
                                }
                                $_totalNotValidatedYear[$_time] += !empty($newDataStaffing['notValidated']) ? $newDataStaffing['notValidated'] : 0;

								if(!isset($_totalCapacityTheoretical[$_time])){
                                    $_totalCapacityTheoretical[$_time] = 0;
                                }
                                $_totalCapacityTheoretical[$_time] += !empty($newDataStaffing['capacity_theoretical']) ? $newDataStaffing['capacity_theoretical'] : 0;
                             }
                        }
						//ADD NOT VALIDATED IS HERE
						//$_totalNotValidatedYear[$_y]=$totalCapacityYear[$_y]-$_year['consumed'];
						$_totalNotValidatedYear[$_y]= isset($_totalNotValidatedYear[$_y]) ? $_totalNotValidatedYear[$_y] : $_year['notValidated'];
						$_totalNotValidatedYear = $_totalNotValidatedYear > 0 ? $_totalNotValidatedYear : 0;
						$_totalNotValidatedYear[$_y]=$this->formatNumber($_totalNotValidatedYear[$_y],2);
						$notValidated[$key].= "<td rel=\"total-$_y\" class=\"gantt-capacity wd-work-year\"><div>{$_totalNotValidatedYear[$_y]}</div></td>";
						//END
						$totalCapacityYear[$_y]= $this->formatNumber($totalCapacityYear[$_y], 2);
						$totalWorkingYear[$_y]= $this->formatNumber($totalWorkingYear[$_y], 2);
						$totalAbsenceYear[$_y]= $this->formatNumber($totalAbsenceYear[$_y], 2);
                        $_class = $this->parseDataCapacity($_year['validated'], $totalCapacityYear[$_y]);
                        //$employee[$key].= "<td rel=\"total-$_y\" class=\"wd-remains-year gantt-num\"><div>{$totalEmployeeYear[$_y]}</div></td>"; : khong cong employee cho nam
						$space='&nbsp;';
						$employee[$key].= "<td rel=\"total-$_y\" class=\"wd-remains-year gantt-num\"><div>{$space}</div></td>";
                        $capacity[$key].= "<td rel=\"total-$_y\" class=\"$_class gantt-num\"><div>{$totalCapacityYear[$_y]}</div></td>";
						$working[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$totalWorkingYear[$_y]}</div></td>";
						$absence[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$totalAbsenceYear[$_y]}</div></td>";

						if($allowDisplayCapacities && $showType == 0 && $isCheck == 2)
						{
							//WORKING 2015/03/24
							$_totalCapacityTheoretical[$_y]= isset($_totalCapacityTheoretical[$_y]) ? $_totalCapacityTheoretical[$_y] : $_year['capacity_theoretical'];
							$_year['fte'] = $totalWorkingYear[$_y]==0?0:($_year['validated'] - $_totalCapacityTheoretical[$_y])/$totalWorkingYear[$_y];
							$_year['fte']=$this->formatNumber($_year['fte'],2);
							$_year['fte_real'] = $totalWorkingYear[$_y]==0?0:($_year['validated'] - $totalCapacityYear[$_y])/$totalWorkingYear[$_y];
							$_year['fte_real']=$this->formatNumber($_year['fte_real'],2);
							$_class = $this->parseDataTheoreticalCapacity($_year['fte'], 0);
							$capacityTheoretical[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_totalCapacityTheoretical[$_y]}</div></td>";
							$fte[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte']}</div></td>";
							$_class = $this->parseDataTheoreticalCapacity($_year['fte_real'], 0);
							$fte_real[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte_real']}</div></td>";
							//END
						}

                    } elseif(($showType == 0 || $showType == 5) && ($isCheck == 1 || $isCheck == 0)){
                        $totalCapacityYear = array();
						$totalWorkingYear = array();
						$totalAbsenceYear = array();
						$totalTheoreticalCapacity = array();
                        if(!empty($newDataStaffings)){
                            foreach($newDataStaffings as $time => $newDataStaffing){
                                $_time = date('Y', $time);
                                if(!isset($totalCapacityYear[$_time])){
                                    $totalCapacityYear[$_time] = 0;
                                }
								if(!isset($totalWorkingYear[$_time])){
                                    $totalWorkingYear[$_time] = 0;
                                }
								if(!isset($totalAbsenceYear[$_time])){
                                    $totalAbsenceYear[$_time] = 0;
                                }
								if(!isset($_totalNotValidatedYear[$_time])){
                                    $_totalNotValidatedYear[$_time] = 0;
                                }

								if(!isset($totalTheoreticalCapacity[$_time])){
                                    $totalTheoreticalCapacity[$_time] = 0;
                                }
								if(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)
								{
									$totalCapacityYear[$_time] += !empty($staffing['data'][$time]['capacity']) ? $staffing['data'][$time]['capacity'] : 0;
									$totalWorkingYear[$_time] += !empty($staffing['data'][$time]['working']) ? $staffing['data'][$time]['working'] : 0;
									$totalAbsenceYear[$_time] += !empty($staffing['data'][$time]['absence']) ? $staffing['data'][$time]['absence'] : 0;
								}
								else
								{
									$totalCapacityYear[$_time] += !empty($newDataStaffing['capacity']) ? $newDataStaffing['capacity'] : 0;
									$totalWorkingYear[$_time] += !empty($newDataStaffing['working']) ? $newDataStaffing['working'] : 0;
									$totalAbsenceYear[$_time] += !empty($newDataStaffing['absence']) ? $newDataStaffing['absence'] : 0;
									$totalTheoreticalCapacity[$_time] += !empty($newDataStaffing['capacity_theoretical']) ? $newDataStaffing['capacity_theoretical'] : 0;
									$_totalNotValidatedYear[$_time] += !empty($newDataStaffing['notValidated']) ? $newDataStaffing['notValidated'] : 0;
								}
                            }
                        }
						//ADD NOT VALIDATED IS HERE
						$_year['consumed']=isset($_year['consumed'])?$_year['consumed']:0;
						$totalCapacityYear[$_y]=isset($totalCapacityYear[$_y])?$totalCapacityYear[$_y]:0;
						//$_totalNotValidatedYear[$_y]=$totalCapacityYear[$_y]-$_year['consumed'];
						//$_totalNotValidatedYear[$_y]=$_year['notValidated'];
						if($showType == 5)
							$_totalNotValidatedYear[$_y]= isset($_totalNotValidatedYear[$_y]) ? $_totalNotValidatedYear[$_y] : 0;
						else
							$_totalNotValidatedYear[$_y]= $_year['notValidated'];
						$_totalNotValidatedYear = $_totalNotValidatedYear > 0 ? $_totalNotValidatedYear : 0;					
						$_totalNotValidatedYear[$_y]=$this->formatNumber($_totalNotValidatedYear[$_y],2);
						$notValidated[$key].= "<td rel=\"total-$_y\" class=\"wd-work-year\"><div>{$_totalNotValidatedYear[$_y]}</div></td>";
						//END
                        $_class = $this->parseDataCapacity($_year['validated'], $totalCapacityYear[$_y]);
						$totalCapacityYear[$_y]=$this->formatNumber($totalCapacityYear[$_y]);
						$totalWorkingYear[$_y]=$this->formatNumber($totalWorkingYear[$_y],2);
						$totalAbsenceYear[$_y]=$this->formatNumber($totalAbsenceYear[$_y],2);
                        $capacity[$key].= "<td rel=\"total-$_y\" class=\"$_class gantt-num\"><div>{$totalCapacityYear[$_y]}</div></td>";
						$working[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$totalWorkingYear[$_y]}</div></td>";
						$absence[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$totalAbsenceYear[$_y]}</div></td>";
						if($allowDisplayCapacities && $showType == 0 && $isCheck == 2)
						{
							//WORKING 2015/03/24 //CHO NAY NHE
							if($showType == 5)
							$totalTheoreticalCapacity[$_y] = $totalTheoreticalCapacity[$_y];
							else
							$totalTheoreticalCapacity[$_y] = $_year['capacity_theoretical'];
							$_year['fte'] = $totalWorkingYear[$_y]==0?0:($_year['validated'] - $totalTheoreticalCapacity[$_y])/$totalWorkingYear[$_y];
							$_year['fte']=$this->formatNumber($_year['fte'],2);
							$_year['fte_real'] = $totalWorkingYear[$_y]==0?0:($_year['validated'] - $totalCapacityYear[$_y])/$totalWorkingYear[$_y];
							$_year['fte_real']=$this->formatNumber($_year['fte_real'],2);
							$_class = $this->parseDataTheoreticalCapacity($_year['fte'], 0);
							$capacityTheoretical[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$totalTheoreticalCapacity[$_y]}</div></td>";
							$fte[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte']}</div></td>";
							$_class = $this->parseDataTheoreticalCapacity($_year['fte_real'], 0);
							$fte_real[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte_real']}</div></td>";
							//END
						}
                        if($dateType == 'month' && $allowDisplayCapacities && ($showType == 0 && $isCheck == 1)){
                            if($showType == 5)
							     $totalTheoreticalCapacity[$_y] = $totalTheoreticalCapacity[$_y];
							else
							     $totalTheoreticalCapacity[$_y] = $_year['capacity_theoretical'];
							$capacityTheoretical[$key].= "<td rel=\"total-$_y\" class=\"wd-work-year\"><div>{$totalTheoreticalCapacity[$_y]}</div></td>";
                        }
                    } else {
						//ADD NOT VALIDATED IS HERE
						$_year['consumed']=isset($_year['consumed'])?$_year['consumed']:0;
						//$_year['notValidated']=$_year['capacity']-$_year['consumed'];
						$_year['notValidated'] = $_year['notValidated'] > 0 ? $_year['notValidated'] : 0;
						$_year['notValidated']=$this->formatNumber($_year['notValidated'],2);
						$notValidated[$key].= "<td rel=\"total-$_y\" class=\"wd-work-year\"><div>{$_year['notValidated']}</div></td>";
						//END
						//WORKING 2015/03/24
						$_year['fte'] = $_year['working']==0?0:($_year['validated'] - $_year['capacity_theoretical'])/$_year['working'];
						$_year['fte']=$this->formatNumber($_year['fte'],2);
						$_class = $this->parseDataTheoreticalCapacity($_year['fte'], 0);
						$capacityTheoretical[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['capacity_theoretical']}</div></td>";
						$fte[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte']}</div></td>";
						$_year['fte_real'] = $_year['working']==0?0:($_year['validated'] - $_year['capacity'])/$_year['working'];
						$_year['fte_real']=$this->formatNumber($_year['fte_real'],2);
						$_class = $this->parseDataTheoreticalCapacity($_year['fte_real'], 0);
						$fte_real[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_year['fte_real']}</div></td>";
						//END
						$_year['capacity']=$this->formatNumber($_year['capacity'],2);
						$_year['working']=$this->formatNumber($_year['working'],2);
						$_year['absence']=$this->formatNumber($_year['absence'],2);
						$space='&nbsp;';
                        //$employee[$key].= "<td rel=\"total-$_y\" class=\"wd-remains-year gantt-num\"><div>{$_year['employee']}</div></td>"; : khong cong employee cho nam
						$employee[$key].= "<td rel=\"total-$_y\" class=\"wd-remains-year gantt-num\"><div>{$space}</div></td>";
                        $capacity[$key].= "<td rel=\"total-$_y\" class=\"$class gantt-num\"><div>{$_year['capacity']}</div></td>";
						$working[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$_year['working']}</div></td>";
						$absence[$key].= "<td rel=\"total-$_y\" class=\"gantt-num\"><div>{$_year['absence']}</div></td>";
                    }
                } else {
                    $totalWorkingDayYear = $totalEmployeeAsteriskYear = $totalCapacityAsteriskYear = array();
                    if(!empty($newDataStaffings)){
                        foreach($newDataStaffings as $time => $newDataStaffing){
                            $_time = date('Y', $time);

                            if(!isset($totalWorkingDayYear[$_time])){
                                $totalWorkingDayYear[$_time] = 0;
                            }
                            $totalWorkingDayYear[$_time] += !empty($newDataStaffing['workingDay']) ? $newDataStaffing['workingDay'] : 0;

                            if(!isset($totalEmployeeAsteriskYear[$_time])){
                                $totalEmployeeAsteriskYear[$_time] = !empty($newDataStaffing['employeeAsterisk']) ? $newDataStaffing['employeeAsterisk'] : 0;
                            } else {
                                if(isset($newDataStaffing['employeeAsterisk']) && $totalEmployeeAsteriskYear[$_time] <= $newDataStaffing['employeeAsterisk']){
                                    $totalEmployeeAsteriskYear[$_time] = $newDataStaffing['employeeAsterisk'];
                                }
                            }
                            //$totalEmployeeAsteriskYear[$_time] += !empty($newDataStaffing['employeeAsterisk']) ? $newDataStaffing['employeeAsterisk'] : 0;

                            if(!isset($totalCapacityAsteriskYear[$_time])){
                                $totalCapacityAsteriskYear[$_time] = 0;
                            }
                            $totalCapacityAsteriskYear[$_time] += !empty($newDataStaffing['capacityAsterisk']['capacity']) ? $newDataStaffing['capacityAsterisk']['capacity'] : 0;
                        }
                    }


                    if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
						
						$CAPACITY = $RESOURCE = $THEORETICAL = $FTE = $TOTALWORKLOAD = array() ;
						foreach($newDataStaffings as $time => $newDataStaffing){
							$_time = date('Y', $time);
							if(!isset($CAPACITY[$_time])){
                                $CAPACITY[$_time] = 0;
                            }
							$CAPACITY[$_time] += $newDataStaffing['capacity'];

							if(!isset($RESOURCE[$_time])){
                                $RESOURCE[$_time] = 0;
                            }
							$RESOURCE[$_time] += $newDataStaffing['resource'];

							if(!isset($TOTALWORKLOAD[$_time])){
                                $TOTALWORKLOAD[$_time] = 0;
                            }
							$TOTALWORKLOAD[$_time] += $newDataStaffing['totalWorkload'];

							if(!isset($THEORETICAL[$_time])){
                                $THEORETICAL[$_time] = 0;
                            }
							$THEORETICAL[$_time] += $newDataStaffing['resource_theoretical'];

							if(!isset($FTE[$_time])){
                                $FTE[$_time] = 0;
                            }
							$FTE[$_time] = $newDataStaffing['fte'];
						}
						$CAPACITY = $_year['capacity'];
						$TOTALWORKLOAD = $_year['totalWorkload'];
						$RESOURCE = $_year['resource'];
						$THEORETICAL = $_year['resource_theoretical'];
						$FTE = $_year['fte'];
						$capacity[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$CAPACITY}</div></td>" :"";
						$totalWorkload[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div>{$TOTALWORKLOAD}</div></td>" :"";
						$resource[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >&nbsp;</div></td>" :"";
						$resource_theoretical[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$THEORETICAL}</div></td>" :"";
						$fte[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$FTE}</div></td>" :"";
                    }
                }
                foreach ($default as $k => $v) {
                    $total[$k] += $_year[$k];

					if($k!='employee')
					$total[$k]=$this->formatNumber($total[$k]);

					$totalTemp[$key][$k]=$this->formatNumber($total[$k]);
                }
                if ($titles) {
                    $titles .= "<td><div>$_y</div></td>";
                }
            }
			
            if ($titles) {
                $titles.= $isMultiYear ? "<td><div>Total</div></td>" : "";
				$titles.= "<td class=\"percent\"><div>%</div></td>"; //ADD PERCENT
                $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";
            }
			
            $class = $this->parseData($total);
            $total['budget']=$this->formatNumber($total['budget'],2);
			$total['validated']=$this->formatNumber($total['validated'],2);
			$total['consumed']=$this->formatNumber($total['consumed'],2);
            $estimation[$key].= "";
			$_percentValidateVal=$_percentConsumedVal=$space;
			//SET DATA MD CHART USE SESSION
			$_idEmp=null;
			$_idFamilyVi=0;
			if(isset($staffing['isEmployee'])&&$staffing['isEmployee'])
			{
				if(isset($_SESSION['graph'][$_idEmp])&&!empty($_SESSION['graph'][$_idEmp]))
				{
					unset($_SESSION['graph'][$_idEmp]);
				}
				$_idEmp=$staffing['employee_id'];
				$_percentValidateVal=$_percentConsumedVal='100%';
			}
			elseif((isset($staffing['isFamily'])&&$staffing['isFamily']) || (isset($staffing['isSubfamily'])&&$staffing['isSubfamily'])|| (isset($staffing['isActivity'])&&$staffing['isActivity']))
			{
				if($activityType==1&&$showType==0){
					$_idFamilyVi=$staffing['id'];
					$_idEmp=$staffing['employee_id'];
					/* Huynh - #606 */
					$_totalValidateOfEmployee=isset($totalTemp['employee-'.$staffing['employee_id']]['validated'])?$totalTemp['employee-'.$staffing['employee_id']]['validated']:0;
                    // $_totalValidateOfEmployee=isset($totalTemp['summary']['validated'])?$totalTemp['summary']['validated']:0;
					$_percentValidateVal=$_totalValidateOfEmployee==0?0:$this->formatNumber(round(($total['validated']*100)/$_totalValidateOfEmployee, 2),2);
					if((isset($staffing['isFamily'])&&$staffing['isFamily'])){
						if(isset($_SESSION['graph'][$_idEmp][$_idFamilyVi])&&!empty($_SESSION['graph'][$_idEmp][$_idFamilyVi]))
						{
							unset($_SESSION['graph'][$_idEmp]);
						}
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['name']=$staffing['name'];
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['workloadVal']=$_percentValidateVal;
					}
					$_percentValidateVal.='%';
					
					/* Huynh - #606 */
					$_totalConsumedOfEmployee=isset($totalTemp['employee-'.$staffing['employee_id']]['consumed'])?$totalTemp['employee-'.$staffing['employee_id']]['consumed']:0;
                    // $_totalConsumedOfEmployee=isset($totalTemp['summary']['consumed'])?$totalTemp['summary']['consumed']:0;
					$_percentConsumedVal=$_totalConsumedOfEmployee==0?0:$this->formatNumber(round(($total['consumed']*100)/$_totalConsumedOfEmployee, 2),2);
					if((isset($staffing['isFamily'])&&$staffing['isFamily'])){
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['consumedVal']=$_percentConsumedVal;
					}
					$_percentConsumedVal.='%';
				}
				elseif(($showType == 0 && $isCheck == 2)||($activityType==0&&$showType==0))
				{
					$_idFamilyVi=$staffing['id'];
					$_idEmp=$staffing['employee_id'];
					$_totalValidateOfEmployee=isset($_sessionVal['summaryVal']['validated'])?$_sessionVal['summaryVal']['validated']:0;
					$_percentValidateVal=$_totalValidateOfEmployee==0?0:$this->formatNumber(round(($total['validated']*100)/$_totalValidateOfEmployee, 2),2);
					if((isset($staffing['isFamily'])&&$staffing['isFamily'])){
						if(isset($_SESSION['graph'][$_idEmp][$_idFamilyVi])&&!empty($_SESSION['graph'][$_idEmp][$_idFamilyVi]))
						{
							unset($_SESSION['graph'][$_idEmp]);
						}
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['name']=$staffing['name'];
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['workloadVal']=$_percentValidateVal;
					}
					$_percentValidateVal.='%';

					$_totalConsumedOfEmployee=isset($_sessionVal['summaryVal']['consumed'])?$_sessionVal['summaryVal']['consumed']:0;
					$_percentConsumedVal=$_totalConsumedOfEmployee==0?0:$this->formatNumber(round(($total['consumed']*100)/$_totalConsumedOfEmployee, 2),2);
					if((isset($staffing['isFamily'])&&$staffing['isFamily'])){
						$_SESSION['graph'][$_idEmp][$_idFamilyVi]['consumedVal']=$_percentConsumedVal;
					}
					$_percentConsumedVal.='%';
				}
			}
			//END
			if($_idEmp===null || (isset($staffing['isSubfamily'])&&$staffing['isSubfamily']) || (isset($staffing['isActivity'])&&$staffing['isActivity']))
			{
				$_funcA="";
				$_funcB="";
			}
			else
			{
				$_funcA="href=\"javascript:;\" onclick=\"showGraph($_idEmp,'".$_idFamilyVi."','workload')\"";
				$_funcB="href=\"javascript:;\" onclick=\"showGraph($_idEmp,'".$_idFamilyVi."','consumed')\"";
			}
			//END
            $_percentBudgetHtml="<td class=\"percent wd-work-year\" rel=\"all-total\"><div></div></td>";
			$_percentValidateHtml="<td class=\"percent wd-work-year\" rel=\"all-total\"><div><a {$_funcA} >{$_percentValidateVal}</a></div></td>";
			$_percentConsumedHtml="<td class=\"percent wd-work-year\" rel=\"all-total\"><div><a {$_funcB} >{$_percentConsumedVal}</a></div></td>";
            if($dateType == 'month' && $showType == 0 && $budgetTeam){
                if(isset($staffing['isActivity']) && $staffing['isActivity'] == 1){
                    //do nothing
                } else {
                    $budget[$key].= $isMultiYear ? "<td class=\"wd-work-year\" rel=\"all-total\"><div>{$total['budget']}</div></td>" : "";
                    $budget[$key].= $_percentBudgetHtml;
                }
            }
			
			$validated[$key].= $isMultiYear ? "<td class=\"wd-work-year\" rel=\"all-total\"><div>{$total['validated']}</div></td>" : "";
			$validated[$key].= $_percentValidateHtml;
			$consumed[$key].= $isMultiYear ? "<td class=\"wd-work-year\" rel=\"all-total\"><div>{$total['consumed']}</div></td>" : "";			
			$consumed[$key].= $_percentConsumedHtml;
            //$remains[$key].= "";
            if($displayCapacity){
                if(($showType == 0 || $showType == 5) && ($isCheck == 2 || $isCheck == 0)){
                    $totalEmployee = $totalCapacity = $totalWorking = $totalAbsence = $totalNotValidated = $totalCapacityTheoretical = 0;
                    if(!empty($newDataStaffings)){
                        foreach($newDataStaffings as $time => $newDataStaffing){
                            $totalEmployee += $newDataStaffing['employee'];
                            $totalCapacity += $newDataStaffing['capacity'];
							$totalWorking += !empty($newDataStaffing['working']) ? $newDataStaffing['working'] : 0;
							$totalAbsence += !empty($newDataStaffing['absence']) ? $newDataStaffing['absence'] : 0;
							$totalNotValidated += !empty($newDataStaffing['notValidated']) ? $newDataStaffing['notValidated'] : 0;
							$totalCapacityTheoretical += !empty($newDataStaffing['capacity_theoretical']) ? $newDataStaffing['capacity_theoretical'] : 0;
                        }
                    }
					//ADD NOT VALIDATED IS HERE
					//$_totalNotValidated=$totalCapacity-$total['consumed'];
					$_totalNotValidated= $totalNotValidated > 0 ? $totalNotValidated : $total['notValidated'];
					$_totalNotValidated = $_totalNotValidated > 0 ? $_totalNotValidated : 0;
					$_totalNotValidated=$this->formatNumber($_totalNotValidated,2);
					$notValidated[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"wd-work-year\"><div>{$_totalNotValidated}</div></td>" : "";
					$notValidated[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					//END
                    $_class = $this->parseDataCapacity($total['validated'], $totalCapacity);
					$totalCapacity=$this->formatNumber($totalCapacity,2);
					$totalWorking=$this->formatNumber($totalWorking);
					$totalAbsence=$this->formatNumber($totalAbsence,2);
                    //$employee[$key].= "<td class=\"gantt-num\" rel=\"all-total\"><div>{$totalEmployee}</div></td>"; : khong cong employee cho nam
					//ADD PERCENT
					$space='&nbsp;';
					$employee[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div>{$space}</div></td>" : "";
					$employee[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
                    $capacity[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class gantt-num\"><div>{$totalCapacity}</div></td>" : "";
					$capacity[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$working[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"gantt-num\"><div>{$totalWorking}</div></td>" : "";
					$working[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$absence[$key].= "<td rel=\"all-total\" class=\"gantt-num\"><div>{$totalAbsence}</div></td>";
					$absence[$key].= $isMultiYear ? "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>" : "";
                    if($allowDisplayCapacities && $showType == 0 && $isCheck == 2)
					{
						//WORKING 2015/03/24
						$_class = $this->parseDataTheoreticalCapacity($total['fte'], 0);
						//$_totalFte = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity_theoretical'])/$total['working'];
						//$_totalFte = $this->formatNumber($_totalFte,2);
						$_totalFte = $total['fte'];
						$capacityTheoretical[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$totalCapacityTheoretical}</div></td>" : "";
						$capacityTheoretical[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						$fte[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFte}</div></td>" : "";
						$fte[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						$_class = $this->parseDataTheoreticalCapacity($total['fte_real'], 0);
						//$_totalFteReal = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity'])/$total['working'];
						//$_totalFteReal = $this->formatNumber($_totalFteReal,2);
						$_totalFteReal = $total['fte_real'];
						$fte_real[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFteReal}</div></td>" : "";
						$fte_real[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						//END
					}
                } elseif(($showType == 0 || $showType == 5) && ($isCheck == 1 || $isCheck == 0)){
                    $totalCapacity = $totalWorking= $totalAbsence = $totalNotValidated = $totalCapacityTheoretical = 0;
                    if(!empty($newDataStaffings)){
                        foreach($newDataStaffings as $time => $newDataStaffing){
							if(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)
							{
									$totalCapacity += !empty($staffing['data'][$time]['capacity']) ? $staffing['data'][$time]['capacity'] : 0;
									$totalWorking += !empty($staffing['data'][$time]['working']) ? $staffing['data'][$time]['working'] : 0;
									$totalAbsence += !empty($staffing['data'][$time]['absence']) ? $staffing['data'][$time]['absence'] : 0;
							}
							else
							{
								$totalCapacity += !empty($newDataStaffing['capacity']) ? $newDataStaffing['capacity'] : 0;
								$totalWorking += !empty($newDataStaffing['working']) ? $newDataStaffing['working'] : 0;
								$totalAbsence += !empty($newDataStaffing['absence']) ? $newDataStaffing['absence'] : 0;
								$totalNotValidated += !empty($newDataStaffing['notValidated']) ? $newDataStaffing['notValidated'] : 0;
								$totalCapacityTheoretical += !empty($newDataStaffing['capacity_theoretical']) ? $newDataStaffing['capacity_theoretical'] : 0;
							}
                        }
                    }
					//ADD PERCENT
					//ADD NOT VALIDATED
					//$_totalNotValidated=$totalCapacity-$total['consumed'];
					if($showType == 5)
						$_totalNotValidated=$totalNotValidated;
					else
						$_totalNotValidated=$total['notValidated'];
					$_totalNotValidated = $_totalNotValidated > 0 ? $_totalNotValidated : 0;
					$_totalNotValidated=$this->formatNumber($_totalNotValidated,2);
					$notValidated[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"wd-work-year\"><div>{$_totalNotValidated}</div></td>" : "";
					$notValidated[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					//END
                    $_class = $this->parseDataCapacity($total['validated'], $totalCapacity);
					$totalCapacity=$this->formatNumber($totalCapacity,2);
					$totalWorking=$this->formatNumber(floatval($totalWorking));
					$totalAbsence=$this->formatNumber($totalAbsence,2);
                    $totalCapacityTheoretical=$this->formatNumber($totalCapacityTheoretical,2);

                    $capacity[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class gantt-num\"><div>{$totalCapacity}</div></td>" : "";
                    $capacity[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$working[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"gantt-num\"><div>{$totalWorking}</div></td>" : "";
					$working[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$absence[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"gantt-num\"><div>{$totalAbsence}</div></td>" : "";
					$absence[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
                    if($allowDisplayCapacities && $showType == 0 && $isCheck == 2){
						if($showType == 5){
							$totalCapacityTheoretical = $totalCapacityTheoretical;
						} else {
							$totalCapacityTheoretical = $total['capacity_theoretical'];
						}
						//WORKING 2015/03/24
						$_class = $this->parseDataTheoreticalCapacity($total['fte'], 0);
						//$_totalFte = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity_theoretical'])/$total['working'];
						//$_totalFte = $this->formatNumber($_totalFte,2);
						$_totalFte = $total['fte'];
						$capacityTheoretical[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$totalCapacityTheoretical}</div></td>" : "";
						$capacityTheoretical[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						$fte[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFte}</div></td>" : "";
						$fte[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						$_class = $this->parseDataTheoreticalCapacity($total['fte_real'], 0);
						//$_totalFteReal = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity'])/$total['working'];
						//$_totalFteReal = $this->formatNumber($_totalFteReal,2);
						$_totalFteReal = $total['fte_real'];
						$fte_real[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFteReal}</div></td>" : "";
						$fte_real[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						//END
					}
                    if($dateType == 'month' && $allowDisplayCapacities && ($showType == 0 && $isCheck == 1)){
						if($showType == 5){
							$totalCapacityTheoretical = $totalCapacityTheoretical;
						} else {
							$totalCapacityTheoretical = $total['capacity_theoretical'];
						}
						//WORKING 2015/03/24
						//$_class = $this->parseDataTheoreticalCapacity($total['fte'], 0);
						//$_totalFte = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity_theoretical'])/$total['working'];
						//$_totalFte = $this->formatNumber($_totalFte,2);
						//$_totalFte = $total['fte'];
						$capacityTheoretical[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"wd-work-year\"><div>{$totalCapacityTheoretical}</div></td>" : "";
						$capacityTheoretical[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						//$fte[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_totalFte}</div></td><td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						//$_class = $this->parseDataTheoreticalCapacity($total['fte_real'], 0);
						//$_totalFteReal = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity'])/$total['working'];
						//$_totalFteReal = $this->formatNumber($_totalFteReal,2);
						//$_totalFteReal = $total['fte_real'];
						//$fte_real[$key].= "<td rel=\"total-$_y\" class=\"$_class wd-work-year\"><div>{$_totalFteReal}</div></td><td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
						//END
					}
                } else {
                    //$employee[$key].= "<td  class=\"gantt-num\" rel=\"all-total\"><div>{$total['employee']}</div></td>";  : khong cong employee cho nam
					$space='&nbsp;';
					$employee[$key].= $isMultiYear ? "<td  class=\"gantt-num\" rel=\"all-total\"><div>{$space}</div></td>" : "";
					$employee[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$total['capacity']=$this->formatNumber($total['capacity'],2);
					$total['working']=$this->formatNumber($total['working'],2);
					$total['absence']=$this->formatNumber($total['absence'],2);
					//ADD PERCENT
					//ADD NOT VALIDATED IS HERE
					$total['notValidated'] = $total['notValidated'] > 0 ? $total['notValidated'] : 0;
					$total['notValidated']=$this->formatNumber($total['notValidated'],2);
					$notValidated[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"wd-work-year\"><div>{$total['notValidated']}</div></td>" : "";
					$notValidated[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					//END

                    $capacity[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$class gantt-num\"><div>{$total['capacity']}</div></td>" : "";
                    $capacity[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$working[$key].= $isMultiYear ? "<td class=\"total-working-days gantt-num\" rel=\"all-total\"><div>{$space}</div></td>" : "";
					$working[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$absence[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"gantt-num\"><div>{$total['absence']}</div></td>" : "";
					$absence[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					//WORKING 2015/03/24
					$_class = $this->parseDataTheoreticalCapacity($total['fte'], 0);
					//$_totalFte = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity_theoretical'])/$total['working'];
					//$_totalFte = $this->formatNumber($_totalFte,2);
					$_totalFte = $total['fte'];
					$capacityTheoretical[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$total['capacity_theoretical']}</div></td>" : "";
					$capacityTheoretical[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$fte[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFte}</div></td>" : "";
					$fte[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$_class = $this->parseDataTheoreticalCapacity($total['fte_real'], 0);
					//$_totalFteReal = $total['working'] == 0 ? 0 : ($total['validated'] - $total['capacity'])/$total['working'];
					//$_totalFteReal = $this->formatNumber($_totalFteReal,2);
					$_totalFteReal = $total['fte_real'];
					$fte_real[$key].= $isMultiYear ? "<td rel=\"all-total\" class=\"$_class wd-work-year\"><div>{$_totalFteReal}</div></td>" : "";
					$fte_real[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					//END
                }
            } else {
                $totalWorkingDay = $totalCapacityAsterisk = $totalEmployeeAsterisk = 0;
                if(!empty($newDataStaffings)){
                    foreach($newDataStaffings as $time => $newDataStaffing){
                        $totalWorkingDay += !empty($newDataStaffing['workingDay']) ? $newDataStaffing['workingDay'] : 0;
                        $totalCapacityAsterisk += !empty($newDataStaffing['capacityAsterisk']['capacity']) ? $newDataStaffing['capacityAsterisk']['capacity'] : 0;
                        if(isset($newDataStaffing['employeeAsterisk']) && $totalEmployeeAsterisk <= $newDataStaffing['employeeAsterisk']){
                            $totalEmployeeAsterisk = $newDataStaffing['employeeAsterisk'];
                        }
                        //$totalEmployeeAsterisk += !empty($newDataStaffing['employeeAsterisk']) ? $newDataStaffing['employeeAsterisk'] : 0;
                    }
                }
				$totalWorkingDay=$this->formatNumber($totalWorkingDay);
				$totalCapacityAsterisk=$this->formatNumber($totalCapacityAsterisk);
                if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
					$CAPACITY = $RESOURCE = $THEORETICAL = $FTE = $TOTALWORKLOAD = 0 ;
					$CAPACITY = $total['capacity'];
					$RESOURCE = $total['resource'];
					$TOTALWORKLOAD = $total['totalWorkload'];
					$THEORETICAL = $total['resource_theoretical'];
					$FTE = $total['fte'];
					$capacity[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$CAPACITY}</div></td>" : "";
					$capacity[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$totalWorkload[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div>{$TOTALWORKLOAD}</div></td>" : "";
					$totalWorkload[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$resource[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$space}</div></td>" : "";
					$resource[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$resource_theoretical[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$THEORETICAL}</div></td>" : "";
					$resource_theoretical[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
					$fte[$key].= $isMultiYear ? "<td class=\"gantt-num\" rel=\"all-total\"><div >{$FTE}</div></td>" : "";
					$fte[$key].= "<td class=\"percent gantt-num\" rel=\"all-total\"><div>{$space}</div></td>";
                }
            }			
			$_colspan=$count[0]+1;
			// pr($displayCapacity);
			// pr($displayCapacity);
            if($displayCapacity){
				//WORKING 2015/03/24
				//CHECK DISPLAY BY CONFIG
				$dataAbsence = $displayAbsence ? "<tr class=\"gantt-num gantt-capacity total-absence\">{$absence[$key]}</tr>" : "";
				$dataRealCapacity = $displayRealCapacity ? "<tr class=\"text-bold gantt-num gantt-capacity total-capacity\">{$capacity[$key]}</tr>" : "";
				$dateRealFte = $displayRealFte ? "<tr class=\"gantt-num gantt-capacity total-realfte\">{$fte_real[$key]}</tr>" : "";
				$dataTheoreticalCapacity = $displayTheoreticalCapacity ? "<tr class=\"gantt-num gantt-capacity total-capacity_theoretical\">{$capacityTheoretical[$key]}</tr>" : "";
				$dataTheoreticalFte = $displayTheoreticalFte ? "<tr class=\"gantt-num gantt-capacity total-fte\">{$fte[$key]}</tr>" : "";
				$dataWorking = $displayWorkingDay ? "<tr class=\"gantt-num gantt-capacity total-working\">{$working[$key]}</tr>" : "";
				$dataEstimation = "<tr>{$estimation[$key]}</tr>";
				$dataWorkload = "<tr class=\"text-bold wd-workload total-workload\">{$validated[$key]}</tr>";
				$dataConsumed = $displayConsumed ? "<tr class=\"text-bold total-consumed\">{$consumed[$key]}</tr>" : "";
				$dataNotValidated = $displayDayNotValidated ? "<tr class=\"gantt-capacity total-notValidated\">{$notValidated[$key]}</tr>" : "";
				if( $showType == 5) $dataConsumed = '';
                //END
				//GANTT LEFT
				
                if(($showType == 0 || $showType == 5) && $isCheck == 1){
					//$_classRow=(isset($staffing['isEmployee'])&&$staffing['isEmployee']==1)?'gantt-summary':'';
                    $staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
                        <td class=\"gantt-node $_classRow gantt-child\" colspan=\"5\">
                          <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\"w>
								{$dataEstimation}
								{$dataWorking}
								{$dataAbsence}
								{$dataRealCapacity}
								{$dataWorkload}
								{$dateRealFte}
								{$dataConsumed}
								{$dataNotValidated}
								{$dataTheoreticalCapacity}
								{$dataTheoreticalFte}
                               <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
                          </table>
                        </td>
                    </tr>";
					//WORKING DAY DA XAY DUNG O DAY. CHI CAN GOI RA DE SU DUNG. CODE: <tr class=\"gantt-capacity\">{$working[$key]}</tr>
                } else {
					$dataEmployee = $displayResources ? "<tr class=\"gantt-num gantt-employee\">{$employee[$key]}</tr>" : "";
					if($dateType == 'month' && $showType == 0 && $budgetTeam){
						$dataBudget = "<tr>{$budget[$key]}</tr>";
					   $staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-child\" colspan=\"5\">
                              <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\">
									{$dataEstimation}
    								{$dataWorking}
									{$dataEmployee}
                                    {$dataAbsence}
    							    {$dataRealCapacity}
									{$dataWorkload}
    							    {$dateRealFte}
                                    {$dataConsumed}
    							    {$dataNotValidated}
    								{$dataTheoreticalCapacity}
    								{$dataTheoreticalFte}
									{$dataBudget}
                                   <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
						
                    } else {
                        $staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
                            <td class=\"gantt-node gantt-child\" colspan=\"5\">
                              <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\">
                                    {$dataEstimation}
    								{$dataWorking}
								    {$dataEmployee}
                                    {$dataAbsence}
    							    {$dataRealCapacity}
                                    {$dataWorkload}
    							    {$dateRealFte}
                                    {$dataConsumed}
    							    {$dataNotValidated}
									{$dataTheoreticalCapacity}
    								{$dataTheoreticalFte}
                                   <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                        </tr>";
                    }
					//WORKING DAY DA XAY DUNG O DAY. CHI CAN GOI RA DE SU DUNG. CODE: <tr class=\"gantt-capacity\">{$working[$key]}</tr>
                }
            } else {
                if($showType == 5 && $isCheck == false && $this->params['controller'] == 'activity_tasks_preview'){
                    
					$_classRow = '';
					if($fullColumns == true)
					{
						$_classRow = 'gantt-employee';
						$staffTmp = "<tr>{$capacity[$key]}</tr>
								   <tr>{$resource[$key]}</tr>
								   <tr>{$resource_theoretical[$key]}</tr>
								   <tr>{$fte[$key]}</tr>";
					}
					else
					{
						$staffTmp = "<tr>{$consumed[$key]}</tr>";
					}
					$staffTmp =
					$staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
							<td class=\"gantt-node $_classRow gantt-child\" colspan=\"5\">
							  <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\">
								   <tr>{$estimation[$key]}</tr>
								   <tr class=\"wd-workload total-workload\">{$validated[$key]}</tr>
								   {$staffTmp}
								   <tr class=\"fixedHeightStaffing\"><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
							  </table>
							</td>
						</tr>";
                } else {
                    if($dateType == 'month' && $showType == 0 && $budgetTeam){
                        $staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
                                <td class=\"gantt-node gantt-child\" colspan=\"5\">
                                  <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\">
                                       <tr>{$estimation[$key]}</tr>
                                       <tr>{$budget[$key]}</tr>
                                       <tr class=\"wd-workload total-workload\">{$validated[$key]}</tr>
                                       <tr class=\"total-consumed\">{$consumed[$key]}</tr>
                                       <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
                                  </table>
                                </td>
                        </tr>";
                    } else {
                        $staffList = "<tr $_function id=\"$_idTr\" class=\"$_classTr gantt-staff\">
                                <td class=\"gantt-node gantt-child\" colspan=\"5\">
                                  <table rel=\"list-{$staffing['id']}\" class=\"$summary $addClassLeft\">
                                       <tr>{$estimation[$key]}</tr>
                                       <tr class=\"wd-workload total-workload\">{$validated[$key]}</tr>
                                       <tr class=\"total-consumed\">{$consumed[$key]}</tr>
                                       <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$_colspan}\"><div>&nbsp;</div></td></tr>
                                  </table>
                                </td>
                        </tr>";
                    }
                }
            }
            $staffTitle = "";
			if ($titles) {
				$staffTitle = "<tr class=\"gantt-staff\">
					<td class=\"gantt-node gantt-child\" colspan=\"5\">
					  <table>
						   $titles
					  </table>
					</td>
				</tr>";
                $this->_runtime['staff'][0].= $staffTitle.''.$staffList;
                $this->_runtime['staff'][1].= $staffGanttTitle.''.$staffGantt;
            } else {
				if($isAjax)
				{
					if($activityType==1)
					{
						if(isset($staffing['isManager'])&&$staffing['isManager'])
						{
							$output[] = array('leftManager'=>$staffList, 'rightManager'=>$staffGantt);
						}
						else
						{
							$output[] = array('left'=>$staffList, 'right'=>$staffGantt);
						}
					}
					else
					{
						$output[] = array('left'=>$staffList, 'right'=>$staffGantt);
					}
				}
				else
				{
					$output[] = array($staffList, $staffGantt);
				}
            }
            $titles = '';
        }
		//if(($activityType==0&&$showType==0)||$showType==1) unset($_SESSION['graph']);
		if($isAjax&&$activityType==1&&$showType==0)
		{
			$output['session']=array('graph'=>isset($_SESSION['graph'])?$_SESSION['graph']:null, 'graphMonth'=>isset($_SESSION['graphMonth'])?$_SESSION['graphMonth']:null);
            return json_encode($output);
		}
		else
		{
			return json_encode($output);
		}
    }

    /**
     * Get month Staffing
     *
     * @return list month
     */
    function getMonths() {
        return $this->_months;
    }
	function formatNumber(&$number,$format=2)
	{
		if(isset($number)&&is_numeric($number) && $number > 0)
		{
			$tmp_number1 = $number * 100;
			$tmp_number2 = round($number, 2) * 100;
			if($tmp_number2 != $tmp_number1){
				$number=number_format($number,3, '.', '');
			}else{
				$number=number_format($number,2, '.', '');
			}
			
		}
		else
		{
			$number=number_format(0.00,2, '.', '');
		}
		return $number;
	}
}
