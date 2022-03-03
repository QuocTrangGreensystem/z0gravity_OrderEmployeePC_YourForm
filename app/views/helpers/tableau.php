<?php

/**
 * PHP versions 5
 * 
 * Your Project Management Strategy (yourpmstrategy.com)
 * Copyright 2011-2013, GLOBAL SI (http://globalsi.fr) - GREEN SYSTEM SOLUTONS (http://greensystem.vn)
 *
 */
class TableauHelper extends AppHelper {
    /**
     * The runtime config for create a gantt chart
     *
     * @var array
     */
    public function get_trusted_ticket($wgserver, $user, $remote_addr) {
        $params = array(
            'username' => $user
        );
        return http_parse_message(http_post_fields("https://$wgserver/trusted", $params))->body;
    }

    /**
     * Parse datetime string Y-m-d format into a Unix timestamp.
     *
     * @param string $date the datetime Y-m-d format
     * 
     * @return integer, a Unix timestamp value
     */
    public function get_trusted_url($user, $server, $view_url){
        $params = ':embed=yes&:toolbar=yes';
        $ticket = $this->get_trusted_ticket($server, $user, $_SERVER['SERVER_ADDR']);
        if (strcmp($ticket, "-1") != 0) {
            return "https://$server/trusted/$ticket/$view_url?$params";
        } else {
            return 0;
        }
    } 
}
