<?php 
    $iCal->create('My_diary', 'My Diary '.$employeeName['fullname'], 'US/Eastern'); 
    //Holiday
    if($holidays!=array()){
       foreach($holidays as $key=>$value)
        {
            $time = date('Y-m-d',$key); 
            $subName = 'Holiday';
            $iCal->addEvent($time, false, $subName, $subName, array('UID'=>$key,'CATEGORIES'=>'Yellow Category', 'attach'=>$urlExport, 'organizer'=>$subName, 'location'=>$subName)); 
        }
    }
    //Absence full
    $full = array();
    if($absenFull!=array()){
       foreach($absenFull as $keyF=>$valueF)
        {   
            array_push($full,$valueF['AbsenceRequest']['date']);
            $time = date('Y-m-d',$valueF['AbsenceRequest']['date']); 
            $subName = ($valueF['AbsenceAm']['name']!=$valueF['AbsencePm']['name'])?$valueF['AbsenceAm']['name'].' (0.5) '.$valueF['AbsencePm']['name'].' (0.5)':$valueF['AbsencePm']['name'].' (1.0)';
            $iCal->addEvent($time, false, $subName, $subName, array('UID'=>$valueF['AbsenceRequest']['date'],'CATEGORIES'=>'Green Category', 'attach'=>$urlExport, 'organizer'=>$subName, 'location'=>$subName)); 
            unset($time);
            unset($subName);
        }
    }
    //Absence haft
    if($absenHaft!=array()){
       foreach($absenHaft as $keyH=>$valueH)
        {   
            $time = date('Y-m-d',$valueH['AbsenceRequest']['date']); 
            $subName = ($valueH['AbsenceAm']['name']!='')?$valueH['AbsenceAm']['name'].' (0.5) ':$valueH['AbsencePm']['name'].' (0.5)';
            $iCal->addEvent($time, false, $subName, $subName, array('UID'=>$valueH['AbsenceRequest']['date'],'CATEGORIES'=>'Green Category', 'attach'=>$urlExport, 'organizer'=>$subName, 'location'=>$subName)); 
            unset($time);
            unset($subName);
        }
    }
    //Workday
    foreach($workloads[$employeeName['id']] as $workload) 
    { 
        foreach($workload as $val){
            if(!in_array($val['date'], $full)){
                $time = date('Y-m-d',$val['date']);
                $name = (isset($val['namePr'])?$val['namePr']:$val['nameAc']);
                $iCal->addEvent($time, false, $name, $val['nameTask'].'('.$val['workload'].')', array('UID'=>$val['date'].$val['task_id'], 'attach'=>$urlExport, 'organizer'=>$val['nameTask'], 'location'=>$val['nameTask'])); 
                unset($time);
                unset($name);
            }
        }
    } 
    $iCal->render();
?>