<?php

/**
 * PHP versions 5
 *
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
class OrganizationHelper extends AppHelper {
    public $helpers = array('UserFile');
    /**
     * The runtime config for create a gantt chart
     *
     * @var array
     */
    //protected $_runtime = array();

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     *
     * @return integer, a Unix timestamp value
     */
    function getOrganization($array, $companyConfigs) {
        $node_tmp = 0;
        $chart = "";
        $showImageManager = !empty($companyConfigs['display_picture_manager']) ? $companyConfigs['display_picture_manager'] : 0;
        $showImageAll = !empty($companyConfigs['display_picture_all_resource']) ? $companyConfigs['display_picture_all_resource'] : 0;
        foreach ($array as $key => $item) {

            $node_level = substr_count($item, '--');
            $item = str_replace('--', '', $item);
            $items = explode("|", $item);
            $manager = explode('->', $items[1]);
            $avatarManager = $this->UserFile->avatar($manager[0]);
            $listEmploy = explode('=>', $items[2]);
            if ($node_level == $node_tmp) {
                if (!empty($chart))
                    $chart .= "</li>";
                $chart .= "<li>";
                $chart .= "<strong class='pc-name' onclick='myFunction(this)'>" . $items[0] . "</strong>";
                $chart .= "<br /><div style='font-size:11px; color: white;text-align:left;margin-top: 10px;'><span><strong>" . $manager[1] . "</strong>";
                if( $showImageManager && ($manager[1] != ' ') ){
                    $chart .= "<img class='avatar-origan' src='" . $avatarManager . "'></span></div>";
                } else {
                    $chart .= "</span></div>";
                }

                if(!empty($listEmploy)){
					$i = 0;
                    foreach ($listEmploy as $key => $_employee) {
                        if(!empty($_employee)){
                            $employee = explode('->', $_employee);
                            $avatarEmploy = $this->UserFile->avatar( str_replace('<br/>' , '', $employee[0]));
                            $chart .= "<div style='font-size:11px; color: #CCC;text-align:left;margin-top: 10px;'><span>" . ( !empty($employee[1]) ? $employee[1] : '') . "</span>";
                            if( !empty($employee[1]) && $showImageManager ){
                                $chart .= "<img class='" . $employee[0] . " 2 avatar-origan employee-avatar hidden-avatar' src='" . $avatarEmploy . "'></span></div>";
                            } else {
                                $chart .= "</div>";
                            }
                        }
                    }
                }
            } else if ($node_level > $node_tmp) {
                $node_tmp = $node_level;
                $chart .= "<ul>";
                $chart .= "<li>";
                $chart .= "<strong class='pc-name' onclick='myFunction(this)'>" . $items[0] . "</strong>";
                $chart .= "<br /><div style='font-size:11px; color: white;text-align:left;margin-top: 10px;'><span><strong>" . $manager[1] . "</strong>";
                if( $showImageManager && ($manager[1] != ' ') ){
                    $chart .= "<img class='avatar-origan' src='" . $avatarManager . "'></span></div>";
                } else {
                    $chart .= "</span></div>";
                }

                if(!empty($listEmploy)){
                    foreach ($listEmploy as $key => $_employee) {
                        if(!empty($_employee)){
                            $employee = explode('->', $_employee);
                            $avatarEmploy = $this->UserFile->avatar($employee[0]);
                            $chart .= "<div style='font-size:11px; color: #CCC;text-align:left;margin-top: 10px;'><span>" . $employee[1] . "</span>";
                            if( !empty($employee[1]) && $showImageManager ){
                                $chart .= "<img class='avatar-origan employee-avatar hidden-avatar' src='" . $avatarEmploy . "'></span></div>";
                            } else {
                                $chart .= "</div>";
                            }
                        }
                    }
                }
            } else if ($node_level < $node_tmp) {
                $max = $node_tmp - $node_level;
                for ($i = 0; $i < $max; $i++) {
                    $chart .= "</li>";
                    $chart .= "</ul>";
                }
                $chart .= "<li>";
                $chart .= "<strong class='pc-name' onclick='myFunction(this)'>" . $items[0] . "</strong>";
                $chart .= "<br /><div style='font-size:11px; color: white;text-align:left;margin-top: 10px;'><span><strong>" . $manager[1] . "</strong>";
                if( $showImageManager && ($manager[1] != ' ') ){
                    $chart .= "<img class='avatar-origan' src='" . $avatarManager . "'></span></div>";
                } else {
                    $chart .= "</span></div>";
                }

                if(!empty($listEmploy)){
                    foreach ($listEmploy as $key => $_employee) {
                        if(!empty($_employee)){
                            $employee = explode('->', $_employee);
                            $avatarEmploy = $this->UserFile->avatar($employee[0]);
                            $chart .= "<div style='font-size:11px; color: #CCC;text-align:left;margin-top: 10px;'><span>" . $employee[1] . "</span>";
                            if( !empty($employee[1]) && $showImageManager ){
                                $chart .= "<img class='avatar-origan employee-avatar hidden-avatar' src='" . $avatarEmploy . "'></span></div>";
                            } else {
                                $chart .= "</div>";
                            }
                        }
                    }
                }
                $node_tmp = $node_level;
            }
        }
        return $chart;
    }

}
