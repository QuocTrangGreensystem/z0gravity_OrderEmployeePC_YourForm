<?php
    class checkKeyBG{
        /** Enter date expired of Module Projects follow struct:
         * 'day-month-year' (dd-mm-YYYY).
         * Ex: '20-10-2010';
         * Note: Only edit value, not edit variables.
         * @date string
         */
        public $date = '20-06-2020';

        /** Enter domain name of customers
         * Ex: 'demo.projectvisum.com';
         * Note: Only edit value, not edit variables.
         * @domain string
         */
        public $domain = array('z0gravity.local', '192.168.1.20', 'public.yourpmstrategy.com');

        /** Function check Date expired and Domain name of customers
         *
         * @return boolean
         */
        public function validateBG(){
            $result = 'false';
            $_getDate = time();
            $_domain = $_SERVER['SERVER_NAME'];
            $domain = $this->domain;
            if(strtotime($this->date) < $_getDate || (!in_array($_domain, $domain))){
                 $result = 'false';
            } else {
                 $result = 'true';
            }
            return $result;
        }

        /** Function return date range of project         *
         * @return boolean
         */
         public function dateRange(){
            $_total = 0;
            $dateCurrent = time();
            $endDate = strtotime($this->date);
            if ($endDate > $dateCurrent) {
                $_rangeDate = array();
                while ($endDate > $dateCurrent){
                    $_rangeDate[] = $endDate;
                    $endDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate)-1, date("Y", $endDate));
                }
               $_total = count($_rangeDate);
            }
            return $_total;
         }

        /** Function check date expired and message for customers
         *
         * @return boolean
         */
         public function message(){
            $result = 'false';
            $_total = 0;
            $dateCurrent = time();
            $endDate = strtotime($this->date);
            if ($endDate > $dateCurrent) {
                $_rangeDate = array();
                while ($endDate > $dateCurrent){
                    $_rangeDate[] = $endDate;
                    $endDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate)-1, date("Y", $endDate));
                }
               $_total = count($_rangeDate);
            }
            if($_total < 32){
                $result = 'true';
            } else {
                $result = 'false';
            }
            return $result;
         }
    }
?>
