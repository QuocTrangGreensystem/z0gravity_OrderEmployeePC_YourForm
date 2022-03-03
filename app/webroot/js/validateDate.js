// DD_MM_YYYY
	var dtCh= "-";
	var minYear=1900;
	var maxYear=2100;
	
	function ValidateForm(kaka){
		var date = $("#"+kaka).val();
		//var dt=document.frmSample.txtDate
		if (isDate(kaka)==false){
			//dt.focus()
			return false;
		}
		return true
	 }
	 function isDate(tmp){
		var dtStr = $("#"+tmp).val();
		var daysInMonth = DaysArray(12);
		var pos1=dtStr.indexOf(dtCh);
		var pos2=dtStr.indexOf(dtCh,pos1+1);
		var strDay=dtStr.substring(0,pos1);
		var strMonth=dtStr.substring(pos1+1,pos2);
		var strYear=dtStr.substring(pos2+1);
		strYr=strYear;
		if((strMonth.length > 2) || (strDay.length > 2)||(strYear.length > 4)) return false;
		if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
		if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
		for (var i = 1; i <= 3; i++) {
			if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
		}
		month=parseInt(strMonth);
		day=parseInt(strDay);
		year=parseInt(strYr);
		if(dtStr=="") return true;
		if (pos1==-1 || pos2==-1){
			return false;
		}
		if (strMonth.length<1 || month<1 || month>12){
			return false;
		}
		if ((strDay.length<1) || (day<1) || (day>31) || ((month==2) && (day>daysInFebruary(year))) || (day>daysInMonth[month])){
			return false;
		}
		if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
			return false;
		}
		if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
			return false;
		}
	return true;
	}
	function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
	function stripCharsInBag(s, bag){
		var i;
		var returnString = "";
		// Search through string's characters one by one.
		// If character is not in bag, append to returnString.
		for (i = 0; i < s.length; i++){   
			var c = s.charAt(i);
			if (bag.indexOf(c) == -1) returnString += c;
		}
		return returnString;
	}

	function daysInFebruary (year){
		// February has 29 days in any year evenly divisible by four,
		// EXCEPT for centurial years which are not also divisible by 400.
		return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
	}
	function DaysArray(n) {
		var result = [];
		for (var i = 1; i <= n; i++) {
			result[i] = 31;
			if (i==4 || i==6 || i==9 || i==11) {result[i] = 30;}
			if (i==2) {result[i] = 29;}
	   } 
	   return result;
	}
	function getDay(tmp){
		if(isDate(tmp)){
			var dtStr = $("#"+tmp).val();
			var daysInMonth = DaysArray(12);
			var pos1=dtStr.indexOf(dtCh);
			var pos2=dtStr.indexOf(dtCh,pos1+1);
			var strMonth=dtStr.substring(0,pos1);
			if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1);
			//alert(parseInt(strMonth));
			return parseInt(strMonth);
		}
		else return "NaN";
	}
	function getMonth(tmp){
		if(isDate(tmp)){
			var dtStr = $("#"+tmp).val();
			var daysInMonth = DaysArray(12);
			var pos1=dtStr.indexOf(dtCh);
			var pos2=dtStr.indexOf(dtCh,pos1+1);
			var strDay=dtStr.substring(pos1+1,pos2);
			if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
			//alert(parseInt(strDay));
			return parseInt(strDay);
		}
		else return "NaN";
	}
	function getYear(tmp){
		if(isDate(tmp)){
			var dtStr = $("#"+tmp).val();
			var daysInMonth = DaysArray(12);
			var pos1=dtStr.indexOf(dtCh);
			var pos2=dtStr.indexOf(dtCh,pos1+1);
			var strYear=dtStr.substring(pos2+1);
			strYr=strYear;
			for (var i = 1; i <= 3; i++) {
				if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
			}
			//alert(parseInt(strYr));
			return parseInt(strYr);
		}
		else return "NaN";
	}
	function compareDate(tmp1,tmp2){
		if(isDate(tmp1)&&isDate(tmp2)){
			var y1=getYear(tmp1),y2=getYear(tmp2);
			if(y1>y2) return 1;
			else if(y1<y2) return -1;
			else{
				var m1=getMonth(tmp1),m2=getMonth(tmp2);
				if(m1>m2) return 1;
				else if(m1<m2) return -1;
				else{
					var d1=getDay(tmp1),d2=getDay(tmp2);
					if(d1>d2) return 1;
					else if(d1<d2) return -1;
					else{
						return 0;
					}
				}
			}
		}
		else return -2;
	}