<?php

$data = array(
    'result' => $result,
    'message' => $this->Session->read('Message.flash.message'),
    'filename' => $attachment
);
$this->Session->delete('Message.flash');
echo '<script type="text/javascript">parent.window.IuploadComplete(' . json_encode($data) . ');</script>';
?>
