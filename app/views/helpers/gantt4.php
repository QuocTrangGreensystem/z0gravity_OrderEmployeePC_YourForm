<?php

/**
 * PHP versions 5
 * 
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
App::import('Helper', 'Gantt', false);

class Gantt4Helper extends GanttHelper {

    /**
     * Draw Staffing
     *
     * @param array $data
     * 
     * @return void
     */
    public function drawStaffing($staffings, $displaySummary = true, $showType = false) {
        //$this->_runtime['list'] = '';
        //$this->_runtime['chart'] = '';
        $this->_runtime['staff'] = array('', '');

        $staffings['summary'] = array(
            'id' => 'summary',
            'name' => '',
            'func' => 0,
            'data' => array(
                'summary' => array(
                    'id' => 'summary',
                    'name' => __('Summary', true),
                    'func' => 0,
                    'data' => array()
                )
            )
        );

        $_estimation = $_validated = $_consumed = $_remains = $_forecast = array_fill_keys(array_keys($staffings), '');
        $year = array();
        $yearCount = array();
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
            $yearCount[$y] = 0;
            $titles.= "<td class=\"gantt-d$days\"><div>$m-$y</div></td>";
            $date = strtotime($y . '-' . $m . '-1');
            $staffings['summary']['data']['summary']['data'][$date] = $default;

            foreach ($staffings as $staffingKey => &$staffingData) {
                $staffingData = & $staffingData['data'];

                foreach ($staffingData as $key => &$staffing) {

                    foreach ($default as $k => $v) {
                        $k = '_' . $k;
                        if (!isset(${$k}[$staffingKey][$key])) {
                            ${$k}[$staffingKey][$key] = '';
                        }
                    }

                    if (!isset($year[$staffingKey][$key][$y])) {
                        $year[$staffingKey][$key][$y] = $default;
                    }
                    $input = $default;
                    if (isset($staffing['data'][$date])) {
                        $input = array_merge($input, $staffing['data'][$date]);
                    }
                    //unset($staffing['data']);
                    $class = $this->parseData($input, $key === 'summary' ? null : true);
                    $summary = $key === 'summary' ? '' : 'gantt-input';

                    $_estimation[$staffingKey][$key].= "<td rel=\"e-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['estimation']}</div></td>";
                    $_validated[$staffingKey][$key].= "<td rel=\"v-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['validated']}</div></td>";
                    $_consumed[$staffingKey][$key].= "<td rel=\"c-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['consumed']}</div></td>";
                    $_remains[$staffingKey][$key].= "<td rel=\"r-$y-$m\" class=\"gantt-d$days\"><div class=\"$summary\">{$input['remains']}</div></td>";
                    $_forecast[$staffingKey][$key].= "<td rel=\"f-$y-$m\" class=\"gantt-d$days$class\"><div>{$input['forecast']}</div></td>";
                    foreach ($default as $k => $v) {
                        if ($k == 'consumed' && $input[$k] != $this->na) {
                            $year[$key][$y]['has'] = true;
                        }
                        $year[$staffingKey][$key][$y][$k] += $input[$k];
                        $staffings['summary']['data']['summary']['data'][$date][$k] += $input[$k];
                    }
                }
            }
        }

        $count = array(3 + count($yearCount), count($this->_months));
        $_title = array(
            'estimation' => __('Estimation', true),
            'validated' => __('Validated', true),
            'remains' => __('Postponed', true),
            'consumed' => __('Consumed', true),
            'forecast' => __('Forecast', true)
        );
        $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";

        $image = ''; //'<img src="' . $this->url('/img/front/add.gif') . '" class="gantt-image" />';
        $staffings = array_merge(array('summary' => array()), $staffings);
        if (!$displaySummary) {
            unset($staffings['summary']);
        }
        unset($staffingData, $staffing);

        $this->_runtime['staff'][1].= "<tr class=\"gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table>
                                   $titles
                              </table>
                            </td>
                    </tr>";

        $titles = "<td class=\"gantt-name\"><div class=\"gantt-staff-tp\">" . __('Profit center', true) . "</div></td>";
        if ($showType == 3) {
            $titles = "<td class=\"gantt-name\"><div class=\"gantt-staff-tp\">" . __('Function', true) . "</div></td>";
            $titles .= "<td class=\"gantt-name\"><div>" . __('Project', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
        } elseif ($showType == 4) {
            $titles .= "<td class=\"gantt-name\"><div>" . __('Function', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
        } else {
            $titles .= "<td class=\"gantt-name\"><div>" . __('Project', true) . "</div></td><td class=\"gantt-func\"><div></div></td>";
        }

        foreach ($yearCount as $_y => $_year) {
            $titles .= "<td><div>$_y</div></td>";
        }
        $titles.= "<td><div>Total</div></td>";
        $titles = "<tr class=\"gantt-title gantt-head\">$titles</tr>";



        $this->_runtime['staff'][0].= "<tr class=\"gantt-staff gantt-staff-left\">
                            <td class=\"gantt-node gantt-child\" colspan=\"5\">
                              <table>
                                   $titles
                              </table>
                            </td>
                    </tr>";

        $output = array();
        foreach ($staffings as $staffingKey => $staffingData) {
            foreach ($default as $k => $v) {
                $v = '_' . $k;
                ${$k} = ${$v}[$staffingKey];
            }

            $staffList = "<tr class=\"gantt-staff-wrap\">
                            <td class=\"gantt-node gantt-staff-pt\">
                               <div>{$staffingData['name']}</div>
                            </td>
                            <td class=\"gantt-node gantt-staff-col\"><table>";
            $staffGantt = '';
            foreach ($staffingData['data'] as $key => $staffing) {

                $summary = $key === 'summary' ? 'gantt-summary' : '';
                $staffGantt .= "<tr class=\"gantt-staff\">
                            <td class=\"gantt-node gantt-node-head\">
                              <table rel=\"{$staffing['id']}\" class=\"$summary\">
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

                foreach ($year[$staffingKey][$key] as $_y => $_year) {
                    $class = $this->parseData($_year);
                    $estimation[$key].= "<td rel=\"e-$_y\"><div>{$_year['estimation']}</div></td>";
                    $validated[$key].= "<td rel=\"v-$_y\"><div>{$_year['validated']}</div></td>";
                    $consumed[$key].= "<td rel=\"c-$_y\"><div>{$_year['consumed']}</div></td>";
                    $remains[$key].= "<td rel=\"r-$_y\"><div>{$_year['remains']}</div></td>";
                    $forecast[$key].= "<td rel=\"f-$_y\" class=\"$class\"><div>{$_year['forecast']}</div></td>";
                    foreach ($default as $k => $v) {
                        $total[$k] += $_year[$k];
                    }
                }

                $class = $this->parseData($total);
                $estimation[$key].= "<td rel=\"e-total\"><div>{$total['estimation']}</div></td>";
                $validated[$key].= "<td rel=\"v-total\"><div>{$total['validated']}</div></td>";
                $consumed[$key].= "<td rel=\"c-total\"><div>{$total['consumed']}</div></td>";
                $remains[$key].= "<td rel=\"r-total\"><div>{$total['remains']}</div></td>";
                $forecast[$key].= "<td rel=\"f-total\" class=\"$class\"><div>{$total['forecast']}</div></td>";

                $staffList .= "<tr class=\"gantt-staff\">
                            <td class=\"gantt-node gantt-child\" colspan=\"5\">
                              <table rel=\"list-{$staffing['id']}\" class=\"$summary\">
                                   <tr>{$estimation[$key]}</tr>
                                   <tr>{$validated[$key]}</tr>
                                   <tr>{$remains[$key]}</tr>
                                   <tr>{$consumed[$key]}</tr>
                                   <tr class=\"gantt-forecast\">{$forecast[$key]}</tr>
                                   <tr class='fixedHeightStaffing'><td class=\"gantt-space\" colspan=\"{$count[0]}\"><div>&nbsp;</div></td></tr>
                              </table>
                            </td>
                    </tr>";
            }

            $staffList .= "</table></td></tr>";
            $output[] = array($staffList, $staffGantt);
        }
        return json_encode($output);
    }

}
