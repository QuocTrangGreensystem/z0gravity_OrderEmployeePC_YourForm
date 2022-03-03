<?php  
require_once('vendors/iCalcreator.class.php'); 

class ICalHelper extends AppHelper  
{ 
    var $name = 'ICalHelper'; 
    var $errorCode = null; 
    var $errorMessage = null; 
     
    var $calendar; 
             
    function create($name, $description='', $tz='US/Eastern') 
    { 
        $v = new vcalendar(); 
        $v->setConfig('unique_id', $name.'.'.$_SERVER['SERVER_NAME']); 
        $v->setProperty('method', 'PUBLISH'); 
        $v->setProperty('x-wr-calname', $description.' Calendar'); 
        $v->setProperty("X-WR-CALDESC", $description); 
        $v->setProperty("X-WR-TIMEZONE", $tz); 
        $this->calendar = $v; 
    } 
     
    function addEvent($start, $end=false, $summary, $description='', $extra=false) 
    { 
        $start = strtotime($start); 
         
        $vevent = new vevent(); 
        if(!$end) 
        { 
            $end = $start + 24*60*60; 
            $vevent->setProperty('dtstart', date('Ymd', $start), array('VALUE'=>'DATE')); 
            $vevent->setProperty('dtend', date('Ymd', $end), array('VALUE'=>'DATE')); 
        } 
        else 
        { 
            $end = strtotime($end); 
            $end = getdate($end); 
            $end['sec'] = $end['second']; 
            $end['hour'] = $end['hours']; 
            $end['min'] = $end['minutes']; 
            $end['month'] = $end['mon']; 
             
            $start = getdate($start); 
            $start['sec'] = $start['second']; 
            $start['hour'] = $start['hours']; 
            $start['min'] = $start['minutes']; 
            $start['month'] = $start['mon']; 
             
            $vevent->setProperty('dtstart', $start); 
            $vevent->setProperty('dtend', $end);             
        } 
        $vevent->setProperty('summary', $summary); 
        $vevent->setProperty('description', $description); 
        if(is_array($extra)) 
        { 
            foreach($extra as $key=>$value) 
            { 
                $vevent->setProperty($key, $value); 
            } 
        } 
        $this->calendar->setComponent($vevent); 
    } 
     
    function getCalendar() 
    { 
        return $this->calendar; 
    } 
     
    function render() 
    { 
        $this->calendar->returnCalendar(); 
    } 
} 
?>