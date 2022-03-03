<?php
/** 
 * z0 Gravity™
 * Copyright 2011 -2016 by Global SI (http://globalsi.fr) 
 * and Green System Solutions (http://greensystem.vn)  
 */
class LibComponent extends Object {
	public function caculateGlobal($total, $number, $exactlyAlgorithm = true){
        if($total == $number){
            $integer = 1;
            $excess = 0;
        } else {
            if($number == 0){
                $integer = 0;
                $excess = 0;
            } else {
				$integer = $exactlyAlgorithm ? round(($total/$number),2) : $total/$number;
                if(strpos($integer, '.') != false){
                    $nguyen = strstr($integer, '.', true);
                    $du = substr(strstr($integer, '.'), 0, 3);
                    $integer = (float) $nguyen.$du;
                    $afterDivision = $integer*$number;
                    $excess = round($total - $afterDivision, 2);
                } else {
                    $integer = $integer;
                    $excess = 0;
                }
            }
        }
        $result['original'] = $integer;
		if($exactlyAlgorithm)
		{
			if($excess<0)
			{
				$result['remainder'] = -0.01;
				$result['number'] = $excess/$result['remainder'];
			}
			elseif($excess==0)
			{
				$result['remainder'] = 0.00;
				$result['number'] = 0;
			}
			else
			{
				$result['remainder'] = 0.01;
				$result['number'] = $excess/$result['remainder'];
			}
			
		}
		else
		{
			$result['remainder'] = $excess;
			$result['number'] = 1;
		}
        return $result;
    }
}
?>