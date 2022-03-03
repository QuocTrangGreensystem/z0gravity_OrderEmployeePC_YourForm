<?php

$data = array(
    'result' => $result,
    'message' => $this->Session->flash(),
    'data' => $this->data,
    'dataSync' => $dataSync
);
echo json_encode($data);
?>