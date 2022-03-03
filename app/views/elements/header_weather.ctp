<?php
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/* List of ID:
* Refer: https://openweathermap.org/weather-conditions
* 200: Thunderstorm weather_7.svg
* 3xx: Drizzle      weather_4.svg
* 5xx: Rain         weather_5.svg
* 6xx: Snow         weather_6.svg
* 7xx: Atmosphere   weather_3.svg
* 800: Clear        weather_1.svg
* 80x: Clouds       weather_2.svg
*/
function weather_get_status( $weather_id){

    $status = array();
    $status['id'] = $weather_id;
    if( !$weather_id) return false;
    $id = intval($weather_id/100);
    if( ( $id < 2) || ($id>8)) return false;
    switch($id){
        case 2:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="35" height="40" viewBox="0 0 35 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="35" height="40" xlink:href="data:img/png;base64,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"/></svg>';
            break;
        case 3:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="40" viewBox="0 0 40 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="40" xlink:href="data:img/png;base64,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"/></svg>';
            break;
        case 5:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="40" viewBox="0 0 40 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="40" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAMAAAC7IEhfAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACglBMVEUAAADGzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM9ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM7GzM9ksM4AAADyudOHAAAA03RSTlMAA1Gk3/nuyIUlSN3+ohWE6KB0aYK9/eIsfvEGIbAgOSoBvhNnu7KIyTz2riOrt9uzwPdPk9Q7EHFqntcPpXxL1ekSR3rgCp2BHbYL9G8t69COAu0eDBlUj3iK3NGo+/yqqfrajX8uDuaSczLwxcb4gL/WNz8DOhIGPQsMBZTVs7bOmMlaq3mNB9b9Ner1HyT35g9+kZ1yvFMn+eMNP80EXrIBwU7Z/DMU6/MdY62CjqFuFu7xGirhQ8umacNLCNwwSMYCZqmGivss7PAYEd4KdltCjcOTbAAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfhDBoIGwE7upLJAAACRElEQVQ4y52T+V9MURjGDxpNmTSJUaZospNlIkW2kKyh7ESy70IyCsm+79tg7CH7vmdn7Mvz/EHu59yJmTt3Znw8v9zve+733Pu+536uEDqpU7demAH1w40RImgiG8ATU1TDIF60YphjGsU2bmIBmsYF9OKBZrFWiQmJypbmAbwWFpiTagubEUi2aZWWraJat2nbDmjvtag8M1IzaXwHzwQdvZdTTOjk41k7K0qXrvZUoJvPjXCYferuQFoP5Zqe0dO3p16A1auMMCGzt+5wfXzFvkA//VPojyzvV8T47vubAUrrmQNrq7hB2cBgXdGWo4yHIZKHDpOnkiT0MzxjBDBSgdxRgGW0fUyeCJTcfBjGinFhQM54ETQTgIliEjBZhIgtDFPEVGRZQ4miANPEdBSG9MQMFImifxBt2Zgp0mAO+WplmFliNmAM4c3JR2qCmDsPmJ8eRJMHvkCBhYsAw+LEQFmyVPlqy+SWlGIEz/I/P8OK6MJiu36SC4wr88R/pWRVqYdWO9Z4qMxR7i+u5ToPrWeFCuUbWOnnOciNKm0iN6u0hdyq9bZt546dkkp3cfceSXv3cf8BrXiQPKTSYfKISkfJY1rvuJMnTko6RbpOSzpDntV6586TF9QWLrLqkqTLV1h9VSteI6+rdIO8qdIt8rbWu3OX9+5LeuDkwzJJj8jHJVrxCflUbeEZ+VxSzQtWvdR6r8jXbyS9Jd+pa+/JD35n7aL7o0qf+PmLhK9ufvvuJ1a6f3iowvlThRpX9S+N9RsR8gY/Mr+X1gAAAABJRU5ErkJggg=="/></svg>';
            break;
        case 6:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="34" height="40" viewBox="0 0 34 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="34" height="40" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAACIAAAAoCAMAAACsAtiWAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAC2VBMVEUAAADGzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM9ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM5ksM7GzM9ksM4AAAApr5skAAAA8HRSTlMAEHfF9PHDdFzw7Vd68pJRPlKU83JQuRUYv/5GCCATBuTBB8jcBED76fwkK06HtsCXcccjOJjeSYa9FvWoA8ppnLKZsdp8BQ3SrIwMYe6Q97qgSm8xuMKupdVk6uhoWp23vDL6bU0CO+eBcLsvzyk0k8TQxp5PF4GsjSRQoKVcAxqFrYogH+f1OqkBJ+ryNKfznOXQLr+zSe7pxG1FDGrkyYntYwTUoQV8+Vr2KhzmeeCZdf6fD+z4IYKTlrQNOeJNCh5EKEAJJeveBnDXuTIY6KPwZZcm+qheqvtOMMeIUoMR/JtL3KaxlTHC/QuEzUHYRoFqAAAAAWJLR0QAiAUdSAAAAAlwSFlzAAALEgAACxIB0t1+/AAAAAd0SU1FB+EMGggbEFEKsjsAAAJZSURBVDjLjZH3X41hGMbv1KmMJKOoFCFCtuwQkb33Xtl7NDkdomRVyIrIOMaxk5mRlU3I3iv7uv8Dz3tOH+ctzzkf1y/3eL7P897X/RIVlU0xWzto7B1syJIci8OkEiUtEKUAp9LOZVzKlgPKS4kKgKubMatYCe4eRY89K3t5V0FV74LSpxqq1ygE+LrWBGrBr/bfTh131FUT/gVD1lP16qOBqmoINHJu3KRpQDNVszlamIuWrdC6jcRdoLloC41kU+3Q3lwEoYNkAx2hCe5kSjs7hqCLBOkqPNp3U7LuPYSTQAlCXj1hvOsA9Ordp6/8f/QDPKi/Hwa4kUUNxCAajCE+lgkaimE0HCOsEOQLEOBrDXGBHTlhpDVkFGxpNMZYIcaOw3iaAPiHWiImTkKAJwlLCJk8RabgqYBmmiC9g2BZ02eYnps5a/YcmebOmx9K/68FYeERkUoSFb1wkVbEGN3iJbFqYukyZo6LJ4oQkZcn0IqVIq5arULWcGJSMq+ldczrU5g3UApvTNrEm1VIMm+hVOat2zhNS9t5B6XzTopi3mVGdvMe/V7ep9/PBwwHD/FhOsJH9TpOVL1yjPk4cwadyFSSk6fotDITn1Eh2rOCOJdFdP4Cc/pFIn2GIMIvFXKdffmKMerjr14zJrGp1//dTWR4zo2wm0QJt25n3rkr2969+8rnHxDplBhnkCBhnPvwEfPjrDx+8jSNoyXIM+Eo4Tm/eMkcI/hXEuQ157x5y2x4954/fPzEnyVIdq4ywxeifCXmSef9+u17Yv4P4ftnOv/6rT75A92STELMCdHLAAAAAElFTkSuQmCC"/></svg>';
            break;
        case 7:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="27" viewBox="0 0 40 27"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="27" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAbCAMAAAA5zj1cAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAABuVBMVEUAAADGzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM/GzM8AAAB22unkAAAAkXRSTlMACWu/8/TDbQo94eJJ+vCLRC5CifFGHckaGMgbDTo+n9wP252g+f5RUOkq5e0DAXNS5lMFNVtqIxBlrpYEu4hg2TjecZqCqPf2OWS+/C+hHs7gGcxesevkxe9mz/iUTkyMQLpPq7MGVdTLgbW0yvtFvVaTaDuKSuMWLaYUwqL17hI0JR8zWiIwF5faXSApYueZQYTGxwAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfhDBoIGh02oP/HAAABpUlEQVQ4y4WTZVcCYRCFBxUbLFRQAUFFMQA7sDvAROzE7u7u1vuPZWHVrYP3w87OPc+7MzuzSySQLCg4BPLQsHAKrIhI+BUVEZCLVkAZExsXn6CCIjEAl6RCstp3p0mBMlUaStPq1HqkG9jUYESGFKbJzPI2ZkL2r5MDswSXmwco5EB+wa9lsdrEnMyIwqJiKtGX/jOUMqjKxW5Fpb7KbqvmWjWoFXN1St9A6xs4XiPE/TQB5uaW1ja0d7BOdWeXA3VCzulAN7PGnl70yXxO/wBTwMWFdINu9xCG/RPQjGCUicVjGJ+Y1Fo4nNW/7Sk2ncYgE8L+emA1A8x6PHPzC2y+iCUmLGNF0N0q1rjPJxfWmbABpwB0Y5OXT0Bu9Z50IJdne7ZMmOE5297V7hClY5frTjOvsccvIdsHDigBh0d/ng1ojT4WDf8Es3RqRuPiz0dIZzgnCelwQaS9ZP8S5F3ROK6lwBvAe73NlPvBNjVl4U4KvMehLy48PDJ6IkrGswRneIFdYL0C9pI3vt5dRiiThIczICVVvKiK5WPMIcQ+v3qIvgF1bZKCUVepzAAAAABJRU5ErkJggg=="/></svg>';
            break;
        case 8:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="40" viewBox="0 0 40 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="40" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAMAAAC7IEhfAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAA6lBMVEUAAADdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCsAAAChALkYAAAATHRSTlMAFN9AFXkulOgO6WmJBNb6JNdHsLGzRQFW1Fu28lrVJmwFiotICqypclfntMPxxQ2I+YzNHocjD68iQ9O18+BYVXMLRs6yJQyqWURqyy4BnAAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfhDBoIGTK2XJFdAAAB4klEQVQ4y41V6VriQBCcgBhYwAQPjuVyxUgUkeVQVyFy6HrW+z/PdjJhNpm0mv6Vr6aY7umuaoRIhJHJGCJNZIHsV+c7uV2duJvbYYgm8oU4sfADJkMsAqU4sQQUGWJ5D5YdJdoWKmWuyH3gIEo8BPb55xwBVSFqdaBeE6IKHH3ybsrV+NlsgaLVbHdgdbW+mMe/5NcJYtELiy+asks5oHLqfzhn/l199/zc7fv3njlB6RUgJ9t6QeCgK5xLYOg68hrHHQJXjugO6PAiHMXo9xiwJnTfdPa/otmU7pxYwPh6pMCbg6Co6W209ttpAP65i73ofk55ZzFIzCj7/F7vTRtwdcwF2okmLuA5OuZ4aOpYrYV+cgR9tGrbbyMbRJ3JHOSuy3NDPKhBLJPEpTp8EJl0xMw29QpYs6lX29SpHxNpz0bHNh4WiR+nbXi1wY+w8xiDQlE8caI4tBUyKvky65HMnnSZ9UhmVkn6XQr373Mo3BcJvvjCvSThkuOQl8IlK3QCXwZW8F7Xy+X61WOsYJjF0Oe9uLkmElXmUvFsYf62GPqc4eKtkbCrCvLRO82IprmSC2DA8/SVQi075XjlCsZ2lGiP+SX1wa29D4ZIi3QUJxby7CJNvZpVfLfsVfB/H/8AWjhwhQatqrMAAAAASUVORK5CYII="/></svg>';
            if( $weather_id > 800) $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="32" viewBox="0 0 40 32"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="32" xlink:href="data:img/png;base64,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"/></svg>';
            break;
        default:
            $status['image'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40" height="40" viewBox="0 0 40 40"> <metadata><?xpacket begin="&#65279;" id="W5M0MpCehiHzreSzNTczkc9d"?><x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c142 79.160924, 2017/07/13-01:06:39 "> <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"> <rdf:Description rdf:about=""/> </rdf:RDF></x:xmpmeta> <?xpacket end="w"?></metadata><image id="Objet_dynamique_vectoriel" data-name="Objet dynamique vectoriel" width="40" height="40" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAMAAAC7IEhfAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAA6lBMVEUAAADdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCvdlCsAAAChALkYAAAATHRSTlMAFN9AFXkulOgO6WmJBNb6JNdHsLGzRQFW1Fu28lrVJmwFiotICqypclfntMPxxQ2I+YzNHocjD68iQ9O18+BYVXMLRs6yJQyqWURqyy4BnAAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfhDBoIGTK2XJFdAAAB4klEQVQ4y41V6VriQBCcgBhYwAQPjuVyxUgUkeVQVyFy6HrW+z/PdjJhNpm0mv6Vr6aY7umuaoRIhJHJGCJNZIHsV+c7uV2duJvbYYgm8oU4sfADJkMsAqU4sQQUGWJ5D5YdJdoWKmWuyH3gIEo8BPb55xwBVSFqdaBeE6IKHH3ybsrV+NlsgaLVbHdgdbW+mMe/5NcJYtELiy+asks5oHLqfzhn/l199/zc7fv3njlB6RUgJ9t6QeCgK5xLYOg68hrHHQJXjugO6PAiHMXo9xiwJnTfdPa/otmU7pxYwPh6pMCbg6Co6W209ttpAP65i73ofk55ZzFIzCj7/F7vTRtwdcwF2okmLuA5OuZ4aOpYrYV+cgR9tGrbbyMbRJ3JHOSuy3NDPKhBLJPEpTp8EJl0xMw29QpYs6lX29SpHxNpz0bHNh4WiR+nbXi1wY+w8xiDQlE8caI4tBUyKvky65HMnnSZ9UhmVkn6XQr373Mo3BcJvvjCvSThkuOQl8IlK3QCXwZW8F7Xy+X61WOsYJjF0Oe9uLkmElXmUvFsYf62GPqc4eKtkbCrCvLRO82IprmSC2DA8/SVQi075XjlCsZ2lGiP+SX1wa29D4ZIi3QUJxby7CJNvZpVfLfsVfB/H/8AWjhwhQatqrMAAAAASUVORK5CYII="/></svg>';

    }
    return $status;
}

function get_weather_by_ip($weather_info = null){
    if(isset($weather_info['by_ip']) && $weather_info['by_ip']){
        // we save  info for 10 minute = 600s
        if( (time() - $weather_info['by_ip']) > 600){
            $weather_info['by_ip'] = 0;
            $weather_info['geo'] = 0;
        }
    }
    if(empty( $weather_info['geo'])){
        $user_ip = get_client_ip();
        try{
            $content = file_get_contents("http://www.geoplugin.net/php.gp?ip=".$user_ip);
        }catch (Exception $e){
            echo '<p class="wd-hide" > Error when Get IP </p>';
            return false;
        }
        if(!$content) return $weather_info;
        $weather_info['geo'] = unserialize($content);
    }
    $city = str_replace(' ','',$weather_info['geo']["geoplugin_city"]);
    $country = $weather_info['geo']["geoplugin_countryName"];
    $country_code = $weather_info['geo']["geoplugin_countryCode"];
    $weather_ip = ''; 
    try{
        if(isset($weather_info['geo']['city_id'])){
            $weather_ip = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=". $weather_info['geo']['city_id'] ."&appid=".$weather_info['weather_api_key'].'&units=metric'));
        }elseif($city){
           $weather_ip =file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=". $city.",". $country_code ."&appid=".$weather_info['weather_api_key'].'&units=metric');
           $weather_ip = json_decode($weather_ip);
        }
    }catch (Exception $e){
        echo 'Error on get weather info';
        return false;
    }
    if( $weather_ip ){
        $weather_id =  $weather_ip->weather['0']->id; 
        $status = weather_get_status( $weather_id);
        $status['text'] = $weather_ip->weather['0']->main;
        $status['temp'] =  $weather_ip->main->temp;
        $weather_info['geo']['city_id'] = $weather_ip->id;
        $weather_info['geo']['city_name'] = $weather_ip->name;
        $weather_info['status'] = $status;


    }

    if( !isset($weather_info['by_ip']) || !$weather_info['by_ip'])  {
        $weather_info['by_ip'] = time();
        $weather_info['by_ip_time']  = date("Y-m-d H:i:s",$weather_info['by_ip']);
    }
    return $weather_info;
}
function get_weather_by_location($weather_info = null){
    $weather_info['by_location'] = time();
    if(isset($weather_info['by_location']) && $weather_info['by_location']){
        if( (time() - $weather_info['by_location']) > 600){
            $weather_info['by_location'] = 0;
            $weather_info['geo'] = 0;
        }
    }
    $weather_loc = '';
    if(isset($weather_info['by_location']) && $weather_info['by_location'] ){
        try{

            $weather_loc = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=". $weather_info['geo']['coords']['lat'] .'&lon=' . $weather_info['geo']['coords']['lon'] ."&appid=".$weather_info['weather_api_key'].'&units=metric'));
        }catch (Exception $e){
            echo '<p class="error" style="display: none;">Error on get weather info </p>';
            return get_weather_by_ip($weather_info);
        }
    }
    if( $weather_loc ){
        $weather_id =  $weather_loc->weather['0']->id; 
        $status = weather_get_status( $weather_id);
        $status['text'] = $weather_loc->weather['0']->main;
        $status['temp'] =  $weather_loc->main->temp;
        $weather_info['geo']['city_id'] = $weather_loc->id;
        $weather_info['geo']['city_name'] = $weather_loc->name;
        $weather_info['status'] = $status;


    }

    if( !isset($weather_info['by_location']) || !$weather_info['by_location'])  {
        $weather_info['by_location'] = time();
        $weather_info['by_location_time']  = date("Y-m-d H:i:s",$weather_info['by_ip']);
    }
    return $weather_info;
}
function display_waether($weather_info = null){
    if( isset($weather_info['status']) && $weather_info['status'] ){
        $status = $weather_info['status'];
        ?>
        <div id="header-weather">
            <div class="header-weather-inner">
                <div class="weather">
                    <div class="weather-image">
                        <?php echo $status['image']; ?>
                    </div>
                    <div class="location-temp">
                        <p class="location"><?php echo $weather_info['geo']['city_name']; ?></p>
                        <p class="temp"><?php echo $status['temp'];?>° </p>
                    </div>
                </div>
                <div class="weather-control">
                    <a href="javascript:void(0);" title="" class="reload">
                        <img src="/img/new-icon/map-light.png" alt='Get Location'>
                    </a>
                    </a>
                    <a href="https://openweathermap.org/city/<?php echo $weather_info['geo']['city_id'];?>">
                        <img src="/img/new-icon/weather.png" title="Power by openweathermap.org" alt='openweathermap.org'>
                    </a>
                </div>    
            </div>
        </div>
        <?php 
    }
    else{
        ?>
        <div id="header-weather" class='wd-hide'>
            <div class="header-weather-inner">
                <div class="weather">
                    
                </div>
                <div class="weather-control">
                    <a href="javascript:void(0);" title="" class="reload">
                        <img src="/img/new-icon/map-light.png" alt='Get Location'>
                    </a>
                    </a>
                    <a href="https://openweathermap.org/city/<?php echo isset($weather_info['geo']['city_id']) ? $weather_info['geo']['city_id'] : '';?>">
                        <img src="/img/new-icon/weather.png" title="Power by openweathermap.org" alt='openweathermap.org'>
                    </a>
                </div>    
            </div>
        </div>
        <?php
    }
}

/* 
-----------------------------------------------------------------------
*/
$weather_info = isset($_SESSION['weather_info']) ? $_SESSION['weather_info'] : array();
$weather_api_key = 'fe2734201eb96e738c81314292687ad9';  //openweathermap.org/appid
$weather_info['weather_api_key'] = $weather_api_key;
// check time and status
if( (isset($weather_info['geo']['coords']) && $weather_info ['geo']['coords'])){
    $weather_info = get_weather_by_location($weather_info);
}else{
    $weather_info = get_weather_by_ip($weather_info);
}
display_waether($weather_info);
?>
<script>
    // $('body').css('overflow', 'auto');
    // $('.header-bottom-image').css('height', 'inherit');
    (function($) {
        "use strict";
        $(document).ready(function(){  
            function weatherReload(){
                $('.weather-control .reload').addClass('loading');
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                }
            } 
            /* List of ID:
            * Refer: https://openweathermap.org/weather-conditions
            * 200: Thunderstorm weather_7.svg
            * 3xx: Drizzle      weather_4.svg
            * 5xx: Rain         weather_5.svg
            * 6xx: Snow         weather_6.svg
            * 7xx: Atmosphere   weather_3.svg
            * 800: Clear        weather_1.svg
            * 80x: Clouds       weather_2.svg
            */
            function get_weather_status( weather_id){
                var status = [];
                if( !weather_id) return false;
                status.id = weather_id;
                var id = parseInt(weather_id/100);
                if( ( id < 2) || (id > 8)) return false;
                switch(id){
                    case 2:
                     status.url = '/img/new-icon/weather_7.png"';
                     break;
                    case 3:
                     status.url = '/img/new-icon/weather_4.png"';
                     break;
                    case 5:
                     status.url = '/img/new-icon/weather_5.png"';
                     break;
                    case 6:
                     status.url = '/img/new-icon/weather_6.png"';
                     break;
                    case 7:
                     status.url = '/img/new-icon/weather_3.png"';
                     break;
                    case 8:
                     status.url = '/img/new-icon/weather_1.png"';
                     if( weather_id > 800) status.url = '/img/new-icon/weather_2.png"';
                     break;
                    default:
                     status.url = '/img/new-icon/weather_1.png"';
                     break;
                }
                return status;
            }
            function get_weather_html(status){    
                var _html ='';
                _html += '<div class="weather-image">';
                _html +=    '<img src="'+ status.url +'" alt="' + status.text + '" >';
                _html += '</div>';
                _html += '<div class="location-temp">';
                _html +=    '<p class="location">' + status.city_name + '</p>';
                _html +=    '<p class="temp">' + status.temp  + '°</p>';
                _html += '</div>';                    
                return _html;
            }
            function showPosition(position) {
                $.ajax({
                    type: 'GET',
                    url : 'https://api.openweathermap.org/data/2.5/weather?lat=' + position.coords.latitude + '&lon=' + position.coords.longitude  + '&appid=<?php echo $weather_info['weather_api_key'];?>&units=metric',
                    beforeSend: function() {
                        $("#header-weather").addClass('loading');
                    },
                    success : function(responseContent){

                        var weather_id =  responseContent.weather['0'].id;
                        var weather = [];
                        weather = get_weather_status( weather_id);
                        weather.text = responseContent.weather['0'].main;
                        weather.temp =  parseInt(responseContent.main.temp);
                        weather.city_name =  responseContent.name;
                        var _html = get_weather_html(weather);
                        $('#header-weather .weather').html(_html);
                        $('#header-weather').removeClass('loading').show(300);
                        $('.weather-control .reload').removeClass('loading');
                    }
                });


            }     
            weatherReload();
            $('#header-weather').on('click', '.reload', weatherReload);
        });
    })(window.jQuery)
</script>