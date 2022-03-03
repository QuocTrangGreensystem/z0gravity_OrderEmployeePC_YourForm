<?php

$datas = array(
    'result' => $result,
    'message' => $this->Session->flash(),
    'data' => $data
);
echo json_encode($datas);
?>
