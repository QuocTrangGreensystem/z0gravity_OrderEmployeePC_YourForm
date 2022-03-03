<?php
    ob_start();
    for($i=0;$i<10;$i++)
    {
        echo 'printing...<br />';
        ob_get_flush();
        flush();
        usleep(300000);
    }
?>