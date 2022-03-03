<?php
class str_utility {
    /* function name: convertToSQLDate
     * param-input: date string with format dd-mm-yyyy or dd/mm/yyyy
     * return: date string will using to input into database: yyyy-mm-dd
     */
	
	/* Huynh update 13-06-2019
	 * Support input yyyy-mm-dd, yyyy/mm/dd
	 */

	 /* Huynh update 14-06-2019
	 * Check month date by function checkdate
	 */
    function convertToSQLDate($dateString) {
        $retDateString = "";
		$day = $month = $year = 0;
        if( preg_match('/^[0-9]{2}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{4}$/', $dateString) ){
            list($day, $month, $year) = split('[/.-]', $dateString);
        } elseif( preg_match('/^[0-9]{4}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{2}$/', $dateString) ){
            list($year, $month, $day) = split('[/.-]', $dateString);
        }
		//checkdate(month,day,year);
		if( checkdate($month, $day, $year) ){
            if($day<10)$day = '0' . intval($day);
            if($month<10)$month = '0' . intval($month);
            $retDateString = $year . "-" . $month . "-" . $day;
            // if ($retDateString == "0000-00-00")
                // $retDateString = "";
		}
        return $retDateString;
    }

    /* function name: convertFrToSQLDate
     * param-input: date string with format dd-mm-yyyy or dd/mm/yyyy
     * return: date string will using to input into database: yyyy-mm-dd
     */

    function convertFrToSQLDate($dateString) {
        if (empty($dateString)) {
            $retDateString = "";
        } else {
            list($month, $day, $year) = split('[/.-]', $dateString);
            $retDateString = $year . "-" . $month . "-" . $day;
            if ($retDateString == "0000-00-00")
                $retDateString = "";
        }
        return $retDateString;
    }

    /* function name: convertToFRDate
     * param-input: date string with format yyyy-mm-dd
     * return: date string will using to input into database: mm-dd-yyyy
     */

    function convertToFRDate($dateString) {
        if (empty($dateString)) {
            $retDateString = "";
        } else {
            list($year, $month, $day) = split('[/.-]', $dateString);
            $retDateString = $month . "-" . $day . "-" . $year;
            if ($retDateString == "00-00-0000")
                $retDateString = "";
        }
        return $retDateString;
    }

    /* function name: convertToVNDate
     * param-input: date string with format yyyy-mm-dd or yyyy-mm-dd
     * return: date string with VN date format: dd-mm-yyyy
     */

    function convertToVNDate($dateString) {
        if (empty($dateString)) {
            $retDateString = "";
        } else {
            list($year, $month, $day) = split('[/.-]', $dateString);
            if (strlen($day) > 2) {
                $day = explode(" ", $day);
            }
            if (is_array($day)) {
                $retDateString = $day[1] . "  " . $day[0] . "-" . $month . "-" . $year;
            } else {
                $retDateString = $day . "-" . $month . "-" . $year;
            }
            if ($retDateString == "00-00-0000")
                $retDateString = "";
        }
        return $retDateString;
    }

    /*
     * function name: dateDiff
     * @param-input: $endDate: the date of end
     * @param-input: $beginDate: the date of begin
     * @return:  the first content with number words of $n
     */

    function dateDiff($beginDate, $endDate) {
        $end_date = strtotime($endDate);
        $start_date = strtotime($beginDate);
        return ($start_date - $end_date) / 86400;
    }

    /*
     * function name: checkDeadline
     * @param-input: $endDate: the date of end
     * @param-input: $beginDate: the date of begin
     * @return:  return 0,1,2.
     */

    function checkDeadline($beginDate, $endDate) {
        $kq = $this->dateDiff($beginDate, $endDate);
        round($kq);
        if ($kq <= 0) {
            return 0;
        }
        if ($kq >= 0 && $kq <= 3) {
            return 1;
        }
        if ($kq > 3) {
            return 2;
        }
    }

    /*
     * function name: dateView
     * param-input: $date: date;
     * return:  date (d-m-Y)
     */

    function dateView($date) {
        return(date("d-m-Y", strtotime($date)));
    }

    /*
     * function name: convertDateNull
     * param-input: $date: date;
     * return:  $return_date: date or text
     */

    function convertDateNull($date) {
        if ($date == "0000-00-00" || $date == null)
            $return_date = "";
        else
            $return_date = $this->dateView($date);
        return $return_date;
    }

    /*
     * function name: subString
     * param-input: $st: string input; $n: number for sub content
     * return:  the first content with number words of $n
     */

    function subString($str, $n) {
        $arystr = explode(" ", $str);
        //	$arystr=explode("/[\s\t\n\f\r ]+/", $str);
        $st1 = "";
        for ($i = 0; $i < $n; $i++) {
            @$st1.=" " . $arystr[$i];
        }
        if (count($arystr) > $n) {
            $st1.="...";
        }
        return $st1;
    }

    /*
     * function name: firstContent
     * param-input: $st: content to get; $n: number for sub content
     * return: the first content with number words of $n
     */

    function firstContent($st, $n) {
        $search = array("'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<[\/\!]*?[^<>]*?>'si", // Strip out html tags
            "'([\r\n])[\s]+'", // Strip out white space  khong trip ([\r\n])
            "'&(quot|#34);'i", // Replace html entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'e");  // evaluate as php

        $replace = array("",
            "",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\\1)");

        $st = preg_replace($search, $replace, $st); //loai the html

        $arystr = explode(" ", $st);
        //$st1=ereg_replace("\'","`",$st);
        if ($n >= count($arystr))
            return $this->subString($st, $n);
        else
            return $this->subString($st, $n) . "...";
    }

    function file2string($filename) {
        $search = array("'&(quot|#34);'i");
        $replace = array("\"");
        $fd = fopen($filename, "r");
        if ($filename && filesize($filename) > 0) {
            $contents = fread($fd, filesize($filename));
            $str = preg_replace($search, $replace, $contents);
        }
        return $contents;
    }

    function removeHtml($st) {
        $search = array("'<title[^>]*?>.*?</title>'si",
            "'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<[\/\!]*?[^<>]*?>'si", // Strip out html tags
            "'([\r\n])[\s]+'", // Strip out white space
            "'&(quot|#34);'i", // Replace html entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'e");  // evaluate as php

        $replace = array("",
            "",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\\1)");
        $st1 = preg_replace($search, $replace, $st); //loai the html
        //$ary = preg_split ("/[\t\n\f\r]+/", "$st1");	//loc theo ky tu xuong dong
        return $st1;       //lay cau dau tien ra
    }

    function todayInVNFormat() {
        $str_search = array(
            "Mon",
            "Tue",
            "Wed",
            "Thu",
            "Fri",
            "Sat",
            "Sun",
            "am",
            "pm",
            ":"
        );
        $str_replace = array(
            "Thứ hai",
            "Thứ ba",
            "Thứ tư",
            "Thứ năm",
            "Thứ sáu",
            "Thứ bảy",
            "Chủ nhật",
            " phút, sáng",
            " phút, chiều",
            " giờ "
        );
        //	$timenow  = gmdate("D, d/m/Y - g:i a.", time() + 7*3600);
        $timenow = gmdate("D, d-m-Y");
        $timenow = str_replace($str_search, $str_replace, $timenow);
        return $timenow;
    }

    function removeVNUnicode($text) {
        $UNI = array("á", "à", "ả", "ã", "ạ", "ắ", "ằ", "ẳ", "ẵ", "ặ", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ố", "ồ", "ổ", "ỗ", "ộ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "Á", "À", "Ả", "Ã", "Ạ", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ỏ", "Õ", "Ọ", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "ă", "â", "ê", "ô", "ơ", "ư", "đ", "Ă", "Â", "Ê", "Ô", "Ò", "Ư", "Đ");
        $TXT = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "a", "a", "e", "o", "o", "u", "d", "A", "A", "E", "O", "O", "U", "D");

        for ($i = 0; $i < count($UNI); $i++) {
            $text = str_replace($UNI[$i], $TXT[$i], $text);
        }
        return $text;
    }

    /*
     * function name: checkPermissionACO
     * param-input: $list_acos_permited: string read session ACO ; $url_check: url for link check permission
     * return:  $accept_aco : True or False
     */

    function checkPermissionACO($list_acos_permited, $url_check) {
        $accept_aco = false;
        foreach ($list_acos_permited as $key => $str_value) {
            if ($str_value == $url_check) {
                $accept_aco = true;
                break;
            }
        }
        return $accept_aco;
    }

    function checkMaxPostSize() {

        if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {
            if (empty($_POST) && empty($_FILES)) {

                // Get maximum size and meassurement unit
                $max = ini_get('post_max_size');
                $unit = substr($max, -1);
                if (!is_numeric($unit)) {
                    $max = substr($max, 0, -1);
                }

                // Convert to bytes
                switch (strtoupper($unit)) {
                    case 'G':
                        $max *= 1024;
                    case 'M':
                        $max *= 1024;
                    case 'K':
                        $max *= 1024;
                }

                // Assert the content length is within limits
                $length = $_SERVER['CONTENT_LENGTH'];
                return $length;
                /* if ($max < $length) {
                  //throw new Exception('Maximum content length size (' . $max . ') exceeded');
                  return -$length;
                  }else{
                  return +$length;
                  } */
            }
        }
    }

    /**
     * convert xml string to php array - useful to get a serializable value
     *
     * @param string $xmlstr
     * @return array
     * @author Adrien aka Gaarf
     */
    function xmlstr_to_array($xmlstr) {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        return $this->domnode_to_array($doc->documentElement);
    }

    function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v) {
                        $output = (string) $v;
                    }
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }
    function time2str($ts) {
        if(!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if($diff == 0) {
            return __('now', true);
        } elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 60) return __('just now', true);
                if($diff < 120) return __('1 minute ago', true);
                if($diff < 3600) return sprintf(__('%s minutes ago', true), floor($diff / 60));
                if($diff < 7200) return __('1 hour ago', true);
                if($diff < 86400) return sprintf(__('%s hours ago', true), floor($diff / 3600));
            }
            if($day_diff == 1) { return __('Yesterday', true); }
            if($day_diff < 7) { return sprintf(__('%s days ago', true), $day_diff); }
            if($day_diff < 31) { return sprintf(__('%s weeks ago', true), ceil($day_diff / 7)); }
            if($day_diff < 60) { return __('last month', true); }
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) { return 'in a minute'; }
                if($diff < 3600) { return 'in ' . floor($diff / 60) . ' minutes'; }
                if($diff < 7200) { return 'in an hour'; }
                if($diff < 86400) { return 'in ' . floor($diff / 3600) . ' hours'; }
            }
            if($day_diff == 1) { return 'Tomorrow'; }
            if($day_diff < 4) { return date('l', $ts); }
            if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
            if(ceil($day_diff / 7) < 4) { return 'in ' . ceil($day_diff / 7) . ' weeks'; }
            if(date('n', $ts) == date('n') + 1) { return 'next month'; }
            return date('F Y', $ts);
        }
    }
	
	/* Function convert datetime to year
	* @param string $dateString: dd-mm-yyyy , yyyy-mm-dd, yyyy
	* @return: yyyy
	* @author Huynh Le
	*/
	function convertToYYYY($dateString){
		$retDateString = "";
        if( preg_match('/^[0-9]{2}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{4}$/', $dateString) ){ //dd-mm-yyyy, dd/mm/yyyy
            list($day, $month, $retDateString) = split('[/.-]', $dateString);
			
        }elseif(preg_match('/^[0-9]{4}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{2}$/', $dateString)){ // yyyy-mm-dd			
			list($retDateString, $month, $day) = split('[/.-]', $dateString);
			
		}elseif( preg_match('/^[0-9]{2}(\/|\.|-)[0-9]{4}$/', $dateString) ){
            // mm-yyyy
			list($retDateString, $month) = split('[/.-]', $dateString);
		}elseif(preg_match('/^[0-9]{4}$/', $dateString)){
			//yyyy
			$retDateString = $dateString;
		}
		if( $retDateString == "0000" ) $retDateString = "";
		return $retDateString;
	}
	function convertToMMYY($dateString){
		$retDateString = "";
		$year = $month = 0;
        if( preg_match('/^[0-9]{2}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{4}$/', $dateString) ){ //dd-mm-yyyy, dd/mm/yyyy
            list($day, $month, $year) = split('[/.-]', $dateString);
			
        }elseif(preg_match('/^[0-9]{4}(\/|\.|-)[0-9]{2}(\/|\.|-)[0-9]{2}$/', $dateString)){ // yyyy-mm-dd			
			list($year, $month, $day) = split('[/.-]', $dateString);
			
		}elseif( preg_match('/^[0-9]{2}(\/|\.|-)[0-9]{4}$/', $dateString) ){
            // mm-yyyy
			list($month, $year) = split('[/.-]', $dateString);
        }elseif( preg_match('/^[0-9]{4}(\/|\.|-)[0-9]{2}$/', $dateString) ){
            // yyyy-mm
			list($year, $month) = split('[/.-]', $dateString);
        }
		//checkdate(month,day,year);
		if( checkdate($month, 1, $year)){
			$retDateString = $month . "-" . $year;
			// if ($retDateString == "00-0000") $retDateString = "";
		}
		return $retDateString;
	}
}